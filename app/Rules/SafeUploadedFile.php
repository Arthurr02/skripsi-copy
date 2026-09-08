<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Validates an uploaded file from its contents, not only the browser-provided name.
 *
 * This rule deliberately permits only the document and image formats supported by
 * the application. Files that merely rename an executable or script to an allowed
 * extension fail the signature checks below.
 */
class SafeUploadedFile implements ValidationRule
{
    /** @param array<int, string> $allowedExtensions */
    public function __construct(
        private readonly array $allowedExtensions,
        private readonly int $maximumKilobytes = 5120,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('Berkas yang diunggah tidak valid.');

            return;
        }

        if (($value->getSize() ?? 0) > $this->maximumKilobytes * 1024) {
            $fail('Ukuran berkas maksimal '.($this->maximumKilobytes / 1024).' MB.');

            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $allowedExtensions = array_map('strtolower', $this->allowedExtensions);
        if (! in_array($extension, $allowedExtensions, true)) {
            $fail('Format berkas tidak diizinkan.');

            return;
        }

        $path = $value->getRealPath();
        if (! $path || ! is_file($path)) {
            $fail('Berkas tidak dapat diperiksa dengan aman.');

            return;
        }

        $valid = match ($extension) {
            'jpg', 'jpeg', 'png' => $this->isExpectedImage($path, $extension),
            'pdf' => $this->startsWith($path, '%PDF-') && $this->hasMime($path, ['application/pdf', 'application/x-pdf']),
            'doc', 'xls' => $this->startsWith($path, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"),
            'docx' => $this->isOfficeOpenXml($path, 'word/document.xml'),
            'xlsx' => $this->isOfficeOpenXml($path, 'xl/workbook.xml'),
            default => false,
        };

        if (! $valid) {
            $fail('Isi berkas tidak sesuai dengan format yang diizinkan.');
        }
    }

    private function isExpectedImage(string $path, string $extension): bool
    {
        $image = @getimagesize($path);
        $expectedMime = in_array($extension, ['jpg', 'jpeg'], true) ? 'image/jpeg' : 'image/png';

        return is_array($image) && ($image['mime'] ?? null) === $expectedMime;
    }

    /** @param array<int, string> $allowedMimes */
    private function hasMime(string $path, array $allowedMimes): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $path) : false;
        if ($finfo) {
            finfo_close($finfo);
        }

        return is_string($mime) && in_array($mime, $allowedMimes, true);
    }

    private function startsWith(string $path, string $signature): bool
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        $contents = fread($handle, strlen($signature));
        fclose($handle);

        return $contents === $signature;
    }

    private function isOfficeOpenXml(string $path, string $requiredEntry): bool
    {
        if (! $this->startsWith($path, "PK\x03\x04") || ! class_exists(\ZipArchive::class)) {
            return false;
        }

        $archive = new \ZipArchive;
        if ($archive->open($path) !== true) {
            return false;
        }

        $isValid = $archive->locateName('[Content_Types].xml') !== false
            && $archive->locateName($requiredEntry) !== false;
        $archive->close();

        return $isValid;
    }
}

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
            'zip' => $this->isSafeZipArchive($path),
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

    /**
     * ZIP is accepted only as an archive, never based on its filename alone.
     * The archive is not extracted by the application, but these limits also
     * prevent a later consumer from receiving a ZIP-slip or decompression bomb.
     */
    private function isSafeZipArchive(string $path): bool
    {
        if (! $this->hasZipSignature($path) || ! class_exists(\ZipArchive::class)) {
            return false;
        }

        $archive = new \ZipArchive;
        if ($archive->open($path) !== true) {
            return false;
        }

        $isValid = $archive->numFiles > 0 && $archive->numFiles <= 1000;
        $totalUncompressedSize = 0;

        for ($index = 0; $isValid && $index < $archive->numFiles; $index++) {
            $stat = $archive->statIndex($index);
            $entryName = $stat['name'] ?? '';
            $size = (int) ($stat['size'] ?? -1);
            $compressedSize = (int) ($stat['comp_size'] ?? -1);

            $pathParts = explode('/', $entryName);
            $hasUnsafePath = ! is_string($entryName)
                || $entryName === ''
                || str_contains($entryName, "\0")
                || str_starts_with($entryName, '/')
                || str_contains($entryName, '\\')
                || preg_match('/^[A-Za-z]:/', $entryName) === 1
                || in_array('..', $pathParts, true);

            $totalUncompressedSize += max(0, $size);
            $compressionRatio = $compressedSize > 0 ? $size / $compressedSize : ($size > 0 ? INF : 1);

            $isValid = ! $hasUnsafePath
                && $size >= 0
                && $compressedSize >= 0
                && $size <= 25 * 1024 * 1024
                && $totalUncompressedSize <= 50 * 1024 * 1024
                && $compressionRatio <= 100;
        }

        $archive->close();

        return $isValid;
    }

    private function hasZipSignature(string $path): bool
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        $signature = fread($handle, 4);
        fclose($handle);

        return in_array($signature, ["PK\x03\x04", "PK\x05\x06", "PK\x07\x08"], true);
    }
}

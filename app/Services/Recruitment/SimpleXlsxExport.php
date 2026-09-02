<?php

namespace App\Services\Recruitment;

use Carbon\CarbonInterface;
use RuntimeException;
use ZipArchive;

/**
 * Membuat workbook XLSX sederhana tanpa ketergantungan paket eksternal.
 * Semua nilai teks ditulis sebagai inline string agar jawaban peserta tidak
 * dieksekusi Excel sebagai formula.
 */
class SimpleXlsxExport
{
    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     * @param  array<int, int>  $dateColumns
     */
    public function buat(array $headers, array $rows, array $dateColumns = []): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi ZIP PHP tidak tersedia untuk membuat berkas Excel.');
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'seleksi-xlsx-');
        if ($temporaryPath === false) {
            throw new RuntimeException('Berkas Excel sementara tidak dapat dibuat.');
        }

        $zip = new ZipArchive;
        if ($zip->open($temporaryPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($temporaryPath);
            throw new RuntimeException('Berkas Excel tidak dapat disiapkan.');
        }

        try {
            $zip->addFromString('[Content_Types].xml', $this->contentTypes());
            $zip->addFromString('_rels/.rels', $this->rootRelationships());
            $zip->addFromString('docProps/app.xml', $this->appProperties());
            $zip->addFromString('docProps/core.xml', $this->coreProperties());
            $zip->addFromString('xl/workbook.xml', $this->workbook());
            $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelationships());
            $zip->addFromString('xl/styles.xml', $this->styles());
            $zip->addFromString('xl/worksheets/sheet1.xml', $this->worksheet($headers, $rows, $dateColumns));
        } finally {
            $zip->close();
        }

        $contents = file_get_contents($temporaryPath);
        @unlink($temporaryPath);

        if ($contents === false) {
            throw new RuntimeException('Berkas Excel gagal dibaca setelah dibuat.');
        }

        return $contents;
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     * @param  array<int, int>  $dateColumns
     */
    private function worksheet(array $headers, array $rows, array $dateColumns): string
    {
        $xmlRows = [$this->rowXml(1, $headers, true, $dateColumns)];

        foreach ($rows as $index => $row) {
            $xmlRows[] = $this->rowXml($index + 2, $row, false, $dateColumns);
        }

        $lastColumn = $this->columnName(max(1, count($headers)) - 1);
        $lastRow = max(1, count($rows) + 1);
        $columnWidths = [];

        foreach ($headers as $index => $header) {
            $width = min(42, max(14, mb_strlen((string) $header) + 4));
            $columnWidths[] = sprintf(
                '<col min="%1$d" max="%1$d" width="%2$.2f" customWidth="1"/>',
                $index + 1,
                $width,
            );
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            .'<cols>'.implode('', $columnWidths).'</cols>'
            .'<sheetData>'.implode('', $xmlRows).'</sheetData>'
            .'<autoFilter ref="A1:'.$lastColumn.$lastRow.'"/>'
            .'</worksheet>';
    }

    /**
     * @param  array<int, mixed>  $values
     * @param  array<int, int>  $dateColumns
     */
    private function rowXml(int $rowNumber, array $values, bool $header, array $dateColumns): string
    {
        $cells = [];

        foreach (array_values($values) as $columnIndex => $value) {
            $reference = $this->columnName($columnIndex).$rowNumber;
            $isDate = ! $header && in_array($columnIndex, $dateColumns, true) && $value instanceof CarbonInterface;

            if ($isDate) {
                $cells[] = sprintf(
                    '<c r="%s" s="2"><v>%s</v></c>',
                    $reference,
                    $this->excelDate($value),
                );

                continue;
            }

            $style = $header ? ' s="1"' : '';
            $cells[] = sprintf(
                '<c r="%s"%s t="inlineStr"><is><t xml:space="preserve">%s</t></is></c>',
                $reference,
                $style,
                $this->escape($this->stringValue($value)),
            );
        }

        return sprintf('<row r="%d"%s>%s</row>', $rowNumber, $header ? ' ht="24" customHeight="1"' : '', implode('', $cells));
    }

    private function excelDate(CarbonInterface $date): string
    {
        $base = $date->copy()->setTimezone('UTC')->setDate(1899, 12, 30)->startOfDay();
        $seconds = $base->diffInSeconds($date->copy()->setTimezone('UTC'), false);

        return number_format($seconds / 86400, 10, '.', '');
    }

    private function stringValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        if (is_array($value)) {
            return implode(', ', array_map(fn ($item) => $this->stringValue($item), $value));
        }

        if ($value instanceof CarbonInterface) {
            return $value->format('Y-m-d H:i');
        }

        return (string) $value;
    }

    private function escape(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value) ?? '';

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function columnName(int $index): string
    {
        $name = '';

        do {
            $name = chr(65 + ($index % 26)).$name;
            $index = intdiv($index, 26) - 1;
        } while ($index >= 0);

        return $name;
    }

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            .'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            .'</Types>';
    }

    private function rootRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            .'</Relationships>';
    }

    private function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Hasil Seleksi" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private function workbookRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="1"><numFmt numFmtId="164" formatCode="yyyy-mm-dd hh:mm"/></numFmts>'
            .'<fonts count="2"><font><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font></fonts>'
            .'<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF2563EB"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFD1D5DB"/></left><right style="thin"><color rgb="FFD1D5DB"/></right><top style="thin"><color rgb="FFD1D5DB"/></top><bottom style="thin"><color rgb="FFD1D5DB"/></bottom><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/><xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/><xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/></cellXfs>'
            .'</styleSheet>';
    }

    private function appProperties(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Sistem Rekrutmen</Application></Properties>';
    }

    private function coreProperties(): string
    {
        $now = now()->utc()->format('Y-m-d\\TH:i:s\\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            .'<dc:creator>Sistem Rekrutmen</dc:creator><dc:title>Hasil Seleksi</dc:title>'
            .'<dcterms:created xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:created>'
            .'<dcterms:modified xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:modified>'
            .'</cp:coreProperties>';
    }
}

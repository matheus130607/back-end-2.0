<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$docsDir = $root . DIRECTORY_SEPARATOR . 'docs';
$output = $docsDir . DIRECTORY_SEPARATOR . 'documentacao-completa.docx';

$files = [
    'documentacao-tecnica.md',
    'dados-teste.md',
    'mailpit.md',
];

function xmlText(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
}

function slugTitle(string $filename): string
{
    return match ($filename) {
        'documentacao-tecnica.md' => 'Documentacao Tecnica',
        'dados-teste.md' => 'Dados de Teste',
        'mailpit.md' => 'Mailpit',
        default => pathinfo($filename, PATHINFO_FILENAME),
    };
}

function inlineRuns(string $text): string
{
    $parts = preg_split('/(`[^`]+`|\*\*[^*]+\*\*)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $xml = '';

    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }

        if (str_starts_with($part, '`') && str_ends_with($part, '`')) {
            $xml .= '<w:r><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas"/><w:shd w:fill="F3F4F6"/></w:rPr><w:t xml:space="preserve">' . xmlText(trim($part, '`')) . '</w:t></w:r>';
            continue;
        }

        if (str_starts_with($part, '**') && str_ends_with($part, '**')) {
            $xml .= '<w:r><w:rPr><w:b/></w:rPr><w:t xml:space="preserve">' . xmlText(trim($part, '*')) . '</w:t></w:r>';
            continue;
        }

        $xml .= '<w:r><w:t xml:space="preserve">' . xmlText($part) . '</w:t></w:r>';
    }

    return $xml;
}

function paragraph(string $text = '', ?string $style = null, bool $bullet = false, bool $code = false): string
{
    $pPr = '';

    if ($style) {
        $pPr .= '<w:pStyle w:val="' . xmlText($style) . '"/>';
    }

    if ($bullet) {
        $pPr .= '<w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr>';
    }

    if ($code) {
        $pPr .= '<w:shd w:fill="F3F4F6"/><w:spacing w:before="80" w:after="80"/>';
    }

    $runs = $code
        ? '<w:r><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas"/></w:rPr><w:t xml:space="preserve">' . xmlText($text) . '</w:t></w:r>'
        : inlineRuns($text);

    return '<w:p>' . ($pPr ? '<w:pPr>' . $pPr . '</w:pPr>' : '') . $runs . '</w:p>';
}

function tableXml(array $rows): string
{
    if ($rows === []) {
        return '';
    }

    $xml = '<w:tbl><w:tblPr><w:tblStyle w:val="TableGrid"/><w:tblW w:w="0" w:type="auto"/><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:left w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:right w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/></w:tblBorders></w:tblPr>';

    foreach ($rows as $index => $row) {
        $xml .= '<w:tr>';

        foreach ($row as $cell) {
            $cellStyle = $index === 0 ? '<w:shd w:fill="F3F4F6"/>' : '';
            $xml .= '<w:tc><w:tcPr><w:tcW w:w="2400" w:type="dxa"/>' . $cellStyle . '</w:tcPr>' . paragraph(trim($cell), $index === 0 ? null : null) . '</w:tc>';
        }

        $xml .= '</w:tr>';
    }

    return $xml . '</w:tbl>' . paragraph();
}

function isTableSeparator(string $line): bool
{
    return (bool) preg_match('/^\s*\|?\s*:?-{3,}:?\s*(\|\s*:?-{3,}:?\s*)+\|?\s*$/', $line);
}

function parseMarkdown(string $markdown): string
{
    $lines = preg_split('/\R/', $markdown) ?: [];
    $xml = '';
    $inCode = false;
    $codeLines = [];
    $tableRows = [];

    $flushCode = function () use (&$xml, &$codeLines): void {
        if ($codeLines === []) {
            return;
        }

        foreach ($codeLines as $codeLine) {
            $xml .= paragraph($codeLine, null, false, true);
        }

        $codeLines = [];
    };

    $flushTable = function () use (&$xml, &$tableRows): void {
        if ($tableRows === []) {
            return;
        }

        $xml .= tableXml($tableRows);
        $tableRows = [];
    };

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if (str_starts_with($trimmed, '```')) {
            if ($inCode) {
                $flushCode();
                $inCode = false;
            } else {
                $flushTable();
                $inCode = true;
            }

            continue;
        }

        if ($inCode) {
            $codeLines[] = $line;
            continue;
        }

        if ($trimmed === '') {
            $flushTable();
            $xml .= paragraph();
            continue;
        }

        if (str_contains($trimmed, '|') && str_starts_with($trimmed, '|')) {
            if (isTableSeparator($trimmed)) {
                continue;
            }

            $cells = array_map('trim', explode('|', trim($trimmed, '|')));
            $tableRows[] = $cells;
            continue;
        }

        $flushTable();

        if (preg_match('/^(#{1,6})\s+(.+)$/', $trimmed, $matches)) {
            $level = min(strlen($matches[1]), 3);
            $xml .= paragraph($matches[2], 'Heading' . $level);
            continue;
        }

        if (preg_match('/^[-*]\s+(.+)$/', $trimmed, $matches)) {
            $xml .= paragraph($matches[1], null, true);
            continue;
        }

        if (preg_match('/^\d+\.\s+(.+)$/', $trimmed, $matches)) {
            $xml .= paragraph($matches[1], null, true);
            continue;
        }

        $xml .= paragraph($trimmed);
    }

    $flushCode();
    $flushTable();

    return $xml;
}

function dosTime(): array
{
    $time = getdate();

    return [
        (($time['hours'] << 11) | ($time['minutes'] << 5) | (int) floor($time['seconds'] / 2)),
        ((($time['year'] - 1980) << 9) | ($time['mon'] << 5) | $time['mday']),
    ];
}

function zipFile(array $files): string
{
    [$modTime, $modDate] = dosTime();
    $local = '';
    $central = '';
    $offset = 0;

    foreach ($files as $name => $content) {
        $name = str_replace('\\', '/', (string) $name);
        $content = (string) $content;
        $crc = crc32($content);
        $size = strlen($content);
        $nameLength = strlen($name);

        $localHeader = pack(
            'VvvvvvVVVvv',
            0x04034b50,
            20,
            0,
            0,
            $modTime,
            $modDate,
            $crc,
            $size,
            $size,
            $nameLength,
            0,
        ) . $name;

        $centralHeader = pack(
            'VvvvvvvVVVvvvvvVV',
            0x02014b50,
            20,
            20,
            0,
            0,
            $modTime,
            $modDate,
            $crc,
            $size,
            $size,
            $nameLength,
            0,
            0,
            0,
            0,
            0,
            $offset,
        ) . $name;

        $local .= $localHeader . $content;
        $central .= $centralHeader;
        $offset += strlen($localHeader) + $size;
    }

    $centralOffset = strlen($local);
    $centralSize = strlen($central);
    $fileCount = count($files);

    $end = pack(
        'VvvvvVVv',
        0x06054b50,
        0,
        0,
        $fileCount,
        $fileCount,
        $centralSize,
        $centralOffset,
        0,
    );

    return $local . $central . $end;
}

$body = paragraph('Documentacao Completa do Sistema', 'Title');
$body .= paragraph('Sistema de Vendas e Pedidos - Laravel e Filament');
$body .= paragraph('Gerado em ' . date('d/m/Y H:i'));
$body .= '<w:p><w:r><w:br w:type="page"/></w:r></w:p>';

foreach ($files as $index => $file) {
    $path = $docsDir . DIRECTORY_SEPARATOR . $file;

    if (! is_file($path)) {
        continue;
    }

    if ($index > 0) {
        $body .= '<w:p><w:r><w:br w:type="page"/></w:r></w:p>';
    }

    $body .= paragraph(slugTitle($file), 'Heading1');
    $body .= parseMarkdown((string) file_get_contents($path));
}

$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml" xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup" xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk" xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml" xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape" mc:Ignorable="w14 wp14">'
    . '<w:body>' . $body . '<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1440" w:right="1134" w:bottom="1440" w:left="1134" w:header="708" w:footer="708" w:gutter="0"/></w:sectPr></w:body></w:document>';

$stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
    . '<w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="22"/></w:rPr><w:pPr><w:spacing w:after="120"/></w:pPr></w:style>'
    . '<w:style w:type="paragraph" w:styleId="Title"><w:name w:val="Title"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:b/><w:sz w:val="40"/></w:rPr><w:pPr><w:spacing w:after="240"/></w:pPr></w:style>'
    . '<w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:uiPriority w:val="9"/><w:qFormat/><w:rPr><w:b/><w:color w:val="4C1D95"/><w:sz w:val="32"/></w:rPr><w:pPr><w:spacing w:before="360" w:after="160"/></w:pPr></w:style>'
    . '<w:style w:type="paragraph" w:styleId="Heading2"><w:name w:val="heading 2"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:uiPriority w:val="9"/><w:qFormat/><w:rPr><w:b/><w:color w:val="111827"/><w:sz w:val="28"/></w:rPr><w:pPr><w:spacing w:before="300" w:after="120"/></w:pPr></w:style>'
    . '<w:style w:type="paragraph" w:styleId="Heading3"><w:name w:val="heading 3"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:uiPriority w:val="9"/><w:qFormat/><w:rPr><w:b/><w:color w:val="374151"/><w:sz w:val="24"/></w:rPr><w:pPr><w:spacing w:before="240" w:after="100"/></w:pPr></w:style>'
    . '<w:style w:type="table" w:styleId="TableGrid"><w:name w:val="Table Grid"/><w:tblPr><w:tblBorders><w:top w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:left w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:right w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:insideH w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/><w:insideV w:val="single" w:sz="4" w:space="0" w:color="D1D5DB"/></w:tblBorders></w:tblPr></w:style>'
    . '</w:styles>';

$numberingXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:abstractNum w:abstractNumId="0"><w:lvl w:ilvl="0"><w:start w:val="1"/><w:numFmt w:val="bullet"/><w:lvlText w:val="•"/><w:lvlJc w:val="left"/><w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr></w:lvl></w:abstractNum><w:num w:numId="1"><w:abstractNumId w:val="0"/></w:num></w:numbering>';

$contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/><Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/><Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/></Types>';

$relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>';

$docRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/></Relationships>';

$docx = zipFile([
    '[Content_Types].xml' => $contentTypesXml,
    '_rels/.rels' => $relsXml,
    'word/_rels/document.xml.rels' => $docRelsXml,
    'word/document.xml' => $documentXml,
    'word/styles.xml' => $stylesXml,
    'word/numbering.xml' => $numberingXml,
]);

if (file_put_contents($output, $docx) === false) {
    fwrite(STDERR, "Nao foi possivel criar {$output}\n");
    exit(1);
}

echo $output . PHP_EOL;

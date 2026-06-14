<?php

// Generates simple PWA icons (solid background + a "J" glyph) as raw PNGs
// without relying on the GD/Imagick extensions.

function crc32Table(): array
{
    $table = [];
    for ($n = 0; $n < 256; $n++) {
        $c = $n;
        for ($k = 0; $k < 8; $k++) {
            $c = ($c & 1) ? (0xEDB88320 ^ ($c >> 1)) : ($c >> 1);
        }
        $table[$n] = $c;
    }

    return $table;
}

function crc32Bytes(string $data): int
{
    static $table = null;
    $table ??= crc32Table();

    $crc = 0xFFFFFFFF;
    foreach (str_split($data) as $byte) {
        $crc = $table[($crc ^ ord($byte)) & 0xFF] ^ ($crc >> 8);
    }

    return $crc ^ 0xFFFFFFFF;
}

function chunk(string $type, string $data): string
{
    $length = pack('N', strlen($data));
    $crc = pack('N', crc32Bytes($type.$data));

    return $length.$type.$data.$crc;
}

/**
 * @param array<int, array<int, array{int,int,int}>> $pixels RGB grid
 */
function encodePng(array $pixels): string
{
    $height = count($pixels);
    $width = count($pixels[0]);

    $raw = '';
    foreach ($pixels as $row) {
        $raw .= "\x00"; // filter type 0 (none)
        foreach ($row as [$r, $g, $b]) {
            $raw .= chr($r).chr($g).chr($b);
        }
    }

    $signature = "\x89PNG\x0D\x0A\x1A\x0A";
    $ihdr = pack('NNCCCCC', $width, $height, 8, 2, 0, 0, 0);
    $idat = gzcompress($raw, 9);

    return $signature.chunk('IHDR', $ihdr).chunk('IDAT', $idat).chunk('IEND', '');
}

/**
 * 16x16 bitmap of a "J" glyph (1 = foreground, 0 = background).
 */
function jGlyph(): array
{
    $rows = [
        '0000000011000000',
        '0000000011000000',
        '0000000011000000',
        '0000000011000000',
        '0000000011000000',
        '0000000011000000',
        '0000000011000000',
        '0000000011000000',
        '0000000011000000',
        '1100000011000000',
        '1100000011000000',
        '1101111110000000',
        '0111111100000000',
        '0001111000000000',
        '0000000000000000',
        '0000000000000000',
    ];

    return array_map(fn ($row) => array_map('intval', str_split($row)), $rows);
}

function buildIcon(int $size): string
{
    $background = [79, 70, 229]; // indigo-600
    $foreground = [255, 255, 255];

    $glyph = jGlyph();
    $glyphSize = count($glyph);
    $scale = intdiv($size, $glyphSize);
    $offset = intdiv($size - $scale * $glyphSize, 2);

    $pixels = array_fill(0, $size, array_fill(0, $size, $background));

    foreach ($glyph as $gy => $row) {
        foreach ($row as $gx => $value) {
            if (! $value) {
                continue;
            }

            for ($dy = 0; $dy < $scale; $dy++) {
                for ($dx = 0; $dx < $scale; $dx++) {
                    $y = $offset + $gy * $scale + $dy;
                    $x = $offset + $gx * $scale + $dx;

                    if ($y >= 0 && $y < $size && $x >= 0 && $x < $size) {
                        $pixels[$y][$x] = $foreground;
                    }
                }
            }
        }
    }

    return encodePng($pixels);
}

$outputDir = __DIR__.'/../public/icons';

foreach ([192, 512] as $size) {
    file_put_contents("{$outputDir}/icon-{$size}.png", buildIcon($size));
    echo "Generated icon-{$size}.png\n";
}

<?php

declare(strict_types=1);

namespace App\Utility;

class FileWriterUtility
{
    public static function updateLocked(string $filename, callable $update): void
    {
        $handle = fopen($filename,'r+');

        if (!flock($handle, LOCK_EX)) {
            throw new \UnexpectedValueException('Couldn\'t lock result file', 221220222329);
        }

        $size = filesize($filename);
        $content = $update($size === 0 ? '' : fread($handle, $size));
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, $content);
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    public static function ensureDir(string $dir): string
    {
        if (!is_dir($dir)) {
            mkdir($dir, recursive: true);
        }
        return $dir;
    }
}

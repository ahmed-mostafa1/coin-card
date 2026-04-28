<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class SquareImageOptimizer
{
    public function store(UploadedFile $file, string $directory, string $disk = 'public', int $size = 480): string
    {
        $source = $this->createImage($file);
        $source = $this->applyOrientation($source, $file);

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $cropSize = min($sourceWidth, $sourceHeight);
        $cropX = (int) floor(($sourceWidth - $cropSize) / 2);
        $cropY = (int) floor(($sourceHeight - $cropSize) / 2);

        $extension = function_exists('imagewebp') ? 'webp' : 'jpg';
        $target = imagecreatetruecolor($size, $size);

        if ($extension === 'webp') {
            imagealphablending($target, false);
            imagesavealpha($target, true);
            imagefill($target, 0, 0, imagecolorallocatealpha($target, 0, 0, 0, 127));
        } else {
            imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255));
        }

        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            $cropX,
            $cropY,
            $size,
            $size,
            $cropSize,
            $cropSize
        );

        $contents = $this->encode($target, $extension);
        imagedestroy($source);
        imagedestroy($target);

        $path = trim($directory, '/') . '/' . Str::uuid() . '.' . $extension;
        Storage::disk($disk)->put($path, $contents);

        return $path;
    }

    private function createImage(UploadedFile $file): \GdImage
    {
        $contents = file_get_contents($file->getRealPath());
        $image = $contents === false ? false : @imagecreatefromstring($contents);

        if (! $image instanceof \GdImage) {
            throw new RuntimeException('Unable to process uploaded image.');
        }

        imagepalettetotruecolor($image);

        return $image;
    }

    private function applyOrientation(\GdImage $image, UploadedFile $file): \GdImage
    {
        if (! function_exists('exif_read_data') || ! in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)) {
            return $image;
        }

        $exif = @exif_read_data($file->getRealPath());
        $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => false,
        };

        if (! $rotated instanceof \GdImage) {
            return $image;
        }

        imagedestroy($image);
        imagepalettetotruecolor($rotated);

        return $rotated;
    }

    private function encode(\GdImage $image, string $extension): string
    {
        ob_start();

        $success = $extension === 'webp'
            ? imagewebp($image, null, 82)
            : imagejpeg($image, null, 82);

        $contents = ob_get_clean();

        if (! $success || $contents === false) {
            throw new RuntimeException('Unable to encode optimized image.');
        }

        return $contents;
    }
}

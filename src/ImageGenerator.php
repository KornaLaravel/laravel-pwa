<?php

namespace Ladumor\LaravelPwa;

trait ImageGenerator
{
    /**
     * Generate icons and splash screens from a source image.
     *
     * @param string $sourcePath
     * @param string $outputDir
     * @param array $sizes
     * @return void
     */
    public function generateIcons($sourcePath, $outputDir, $sizes)
    {
        if (!file_exists($sourcePath)) {
            return;
        }

        $sourceImage = $this->createImageFromSource($sourcePath);
        if (!$sourceImage) {
            return;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        foreach ($sizes as $size) {
            $dimensions = explode('x', $size);
            $width = (int)$dimensions[0];
            $height = (int)$dimensions[1];

            $targetImage = imagecreatetruecolor($width, $height);

            // Preserve transparency
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $width, $height, $transparent);

            imagecopyresampled(
                $targetImage,
                $sourceImage,
                0,
                0,
                0,
                0,
                $width,
                $height,
                $sourceWidth,
                $sourceHeight
            );

            imagepng($targetImage, $outputDir . DIRECTORY_SEPARATOR . "icon-{$size}.png");
            imagedestroy($targetImage);
        }

        imagedestroy($sourceImage);
    }

    /**
     * Create a GD image resource from the source path.
     *
     * @param string $path
     * @return resource|false
     */
    private function createImageFromSource($path)
    {
        $info = getimagesize($path);
        if (!$info) {
            return false;
        }

        switch ($info[2]) {
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            default:
                return false;
        }
    }
}

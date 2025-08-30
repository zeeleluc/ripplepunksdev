<?php

namespace App\Services\Image;

abstract class AbstractImageProcessor
{
    protected \Imagick $image;

    /**
     * Initialize the image property. Child classes can call this to set up the Imagick object.
     */
    protected function initializeImage(int $width, int $height, string $backgroundColor = '#FFFFFF'): void
    {
        $this->image = new \Imagick();
        $this->image->newImage($width, $height, new \ImagickPixel($backgroundColor));
        $this->image->setImageFormat('png');
    }

    /**
     * Allow child classes to pass a pre-configured Imagick object
     */
    protected function setImage(\Imagick $image): void
    {
        $this->image = $image;
    }

    /**
     * Save the processed image
     */
    public function save(string $filename): bool
    {
        try {
            return $this->image->writeImage($filename);
        } catch (\ImagickException $e) {
            dd($e);
            return false;
        }
    }

    /**
     * Child classes must implement their processing logic
     */
    abstract public function process(): void;

    public function __destruct()
    {
        if (isset($this->image)) {
            $this->image->clear();
            $this->image->destroy();
        }
    }
}

<?php

namespace IOLabs\Handler;

class ImageHandler
{
    public function resizeImage($file, $w = 500, $h = 0)
    {
        $image = new \Imagick($file);
        $image->scaleImage($w, $h);

        return $image;
    }
}
<?php


namespace Image;


use Imagick;
use ImagickException;

class ImagickCompress implements CompressInterface
{
    
    private $imagick;
    private $options;
    private $width;
    private $height;

    public function resize(): void
    {

        $ratio = $this->width / $this->height;
        if ($ratio > 1) {
            $newWidth = $this->options->getOption('width');
            $newHeight = round($newWidth / $ratio);
        } else {
            $newHeight = $this->options->getOption('height');
            $newWidth = round($newHeight * $ratio);
        }
        $this->imagick->resizeImage($newWidth,$newHeight, imagick::FILTER_LANCZOS, 0.9, true);
        $this->width = $newWidth;
        $this->height = $newHeight;
    }

    public function compress(string $image): bool
    {

	    $this->imagick        = new Imagick();
        $this->options        = new \Options();

	    $rawImage = file_get_contents($image);

	    $this->imagick->readImageBlob($rawImage);
	    $this->imagick->stripImage();

	    // Define image
	    $this->width      = $this->imagick->getImageWidth();
        $this->height     = $this->imagick->getImageHeight();

	    if ($this->width > $this->options->getOption('width') || $this->height > $this->options->getOption('height')) {
	        $this->resize();
        }

	    // Compress image
	    $this->imagick->setImageCompressionQuality(85);

	    $image_types = getimagesize($image);

	    // Get thumbnail image
	    $this->imagick->thumbnailImage($this->width, $this->height);

	    // Set image as based its own type
	    if ($image_types[2] === IMAGETYPE_JPEG)
	    {
		    $this->imagick->setImageFormat('jpeg');

		    $this->imagick->setSamplingFactors(array('2x2', '1x1', '1x1'));

		    $profiles = $this->imagick->getImageProfiles("icc", true);

		    $this->imagick->stripImage();

		    if(!empty($profiles)) {
			    $this->imagick->profileImage('icc', $profiles['icc']);
		    }

		    $this->imagick->setInterlaceScheme(Imagick::INTERLACE_JPEG);
		    $this->imagick->setColorspace(Imagick::COLORSPACE_SRGB);
	    }
	    else if ($image_types[2] === IMAGETYPE_PNG)
	    {
		    $this->imagick->setImageFormat('png');
	    }
	    else if ($image_types[2] === IMAGETYPE_GIF)
	    {
		    $this->imagick->setImageFormat('gif');
	    }

	    // Get image raw data
	    $rawData = $this->imagick->getImageBlob();

	    // Destroy image from memory
	    $this->imagick->destroy();
		if (file_put_contents($image, $rawData, LOCK_EX)) {
			return true;
		} else {
			return false;
		}

//	    return $rawData;

    }
}
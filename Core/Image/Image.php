<?php


namespace Image;

use Helper;

abstract class Image
{

    public $imageName;
    public $attachmentId;
    public $imageSize = 0;
    public $newSize = 0;
	public $errorMassage = '';


    protected $path;
    protected $imageMime;

    protected $imageWidth;
    protected $imageHeight;

    protected $imageInfo;


    public function __construct($path)
    {

        $this->path = $path;
        $pathArray = explode('/', $path);
        $this->imageName = end($pathArray);

    }

    protected function necessarilyCompressImage(string $path)
    {
        $options = new \Options();
        if ( $options->getOption( 'optimize' ) === 'on' ) {
            $compress = new ImagickCompress();
            $compress->compress($path);

            clearstatcache($path);
            return filesize($path);
        } else {
            return 'Ужатие выключено';
        }
    }

    protected function optionalCompressImage(string $path)
    {

        $options = new \Options();
        if ( $options->getOption( 'optimize' ) === 'on' ) {
            if ($this->imageWidth > $options->getOption('width') || $this->imageHeight > $options->getOption('height')) {

                $compress = new ImagickCompress();
                if ($this->imageSize > $options->getOption('maxSize')) {
                    $compress->compress($path);
                }

            }
            clearstatcache($path);
            return filesize($path);
        } else {
            return 'Ужатие выключено';
        }


    }


    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getImageMime(): string
    {
        return $this->imageMime;
    }

    /**
     * @return string
     */
    public function getAttachmentId(): string
    {
        return $this->attachmentId;
    }

    /**
     * @return int
     */
    public function getImageWidth(): int
    {
        return $this->imageWidth;
    }

    /**
     * @param int $imageWidth
     */
    public function setImageWidth(int $imageWidth): void
    {
        $this->imageWidth = $imageWidth;
    }

    /**
     * @return int
     */
    public function getImageHeight(): int
    {
        return $this->imageHeight;
    }

    /**
     * @param int $imageHeight
     */
    public function setImageHeight(int $imageHeight): void
    {
        $this->imageHeight = $imageHeight;
    }

    /**
     * @return int
     */
    public function getImageSize(): int
    {
        return $this->imageSize;
    }

    /**
     * @param int $imageSize
     */
    public function setImageSize(int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }


    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMassage;
    }

}
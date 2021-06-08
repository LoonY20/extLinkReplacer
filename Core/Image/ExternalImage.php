<?php


namespace Image;


use ErrorException;
use Exception;

class ExternalImage extends Image
{

    private $imageCode;
    private $oldPath;

    private $id;

    private $wp_get_upload_dir;

    private $download = false;


    public function __construct($path, $id, $title)
    {

        parent::__construct($path);
        $this->wp_get_upload_dir = wp_get_upload_dir();
        $this->init($id, $title);


    }

    private function init($id, $title)
    {
        $result = $this->download();



        if (!$result) return;
        if (!isset($this->imageMime)) $this->getMimeType();
        $path = $this->createImage($id, $title);

		if ($this->imageMime !== 'svg') {
			$imageInfo = getimagesize(WP_ROOT . $this->path);
			$this->imageWidth = $imageInfo[0];
			$this->imageHeight = $imageInfo[1];
			$this->imageSize = filesize(WP_ROOT . $this->path);
			$this->necessarilyCompressImage($path);
		}

    }

    private function download(): bool
    {

        set_error_handler(
            function ($severity, $message, $file, $line) {
                throw new ErrorException($message, $severity, $severity, $file, $line);
            }
        );

        try {
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $this->imageCode = file_get_contents($this->path, false, stream_context_create($arrContextOptions));

        } catch (Exception $e) {
            restore_error_handler();
            $this->download = false;
            $this->errorMassage = $e->getMessage();
            return false;
        }
        restore_error_handler();

        $result = $this->isImage($this->imageCode);
        if (!$result) return false;

        $this->download = true;
        return true;
    }

    private function createImage($id, $title): string
    {

        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $imageName = 'image-' . substr(str_shuffle($permitted_chars), 0, 16) . '.' . $this->imageMime;
        $path = $this->wp_get_upload_dir['path'] . DS . $imageName;
        $uri = parse_url($this->wp_get_upload_dir['url']);
        $this->oldPath = $this->path;
        $this->path = $uri['path'] . '/' . $imageName;
        $this->download = true;

        fopen($path, "w");
        file_put_contents($path, $this->imageCode, LOCK_EX);

        if ($this->imageMime !== 'svg') {
	        $this->insertAttachments($imageName, $id, $title);
        }

        $this->imageName = $imageName;
        return $path;

    }

    private function getMimeType(): void
    {

        switch (exif_imagetype($this->path)) {
            case '1':
                $this->imageMime = 'gif';
                break;
            case '2':
                $this->imageMime = 'jpg';
                break;
            default:
                $this->imageMime = 'png';
                break;
        }

    }

    private function insertAttachments($imageName, $id, $title): void
    {

        $url = $this->wp_get_upload_dir['url'] . '/' . $imageName;
        $pathTo = $this->wp_get_upload_dir['path'] . '/' . $imageName;

        $attachment = array(
            'guid' => $url,
            'post_mime_type' => 'image/' . $this->imageMime,
            'post_title' => preg_replace('/\.[^.]+$/', '', $imageName),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        preg_match(
            '/uploads\/([^^]*)/',
            $url,
            $match
        );

        $attach_id = wp_insert_attachment($attachment, $pathTo, $id);

        require_once WP_ROOT . '/wp-admin/includes/image.php';

        $attach_data = wp_generate_attachment_metadata($attach_id, $pathTo);
        wp_update_attachment_metadata($attach_id, $attach_data);
        update_post_meta($attach_id, '_wp_attachment_image_alt', $title);
        $this->id = $attach_id;




    }

    private function isImage($image): bool
    {
        $result = strpos($image, '</html>');

        if ($result) {
            $this->download = false;
            $this->errorMassage = 'Это не картинки';
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isDownload(): bool
    {
        return $this->download;
    }


}
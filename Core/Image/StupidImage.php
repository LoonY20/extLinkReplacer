<?php


namespace Image;


class StupidImage extends Image
{

    private $imageCode;
    private $oldSrc;
    private $wp_get_upload_dir;
    private $download = false;

    public function __construct($src, $id, $title)
    {
        parent::__construct($src);
        $this->init($src, $id, $title);

    }

    private function init($src, $id, $title) {
        $this->wp_get_upload_dir = wp_get_upload_dir();
        if ($this->decodeSrc($src)) return false;

        $this->path = $this->createImage($id, $title);
        if (!$this->path) {
            return false;
        }

        if ($this->imageMime !== 'svg') {
            $imageInfo = getimagesize(WP_ROOT . $this->path);
            $this->imageWidth = $imageInfo[0];
            $this->imageHeight = $imageInfo[1];
            $this->imageSize = filesize(WP_ROOT . $this->path);
            $this->necessarilyCompressImage($this->path);
        }
    }

    private function decodeSrc($src): bool {

        $image = explode(',', $src, 2);
        $type = explode('/', $image[0]);
        $code = explode(';', $type[1]);
        $type = explode(':', $type[0])[1];
        $mime = $code[0];
        $code = $code[1];
        if (strpos($mime, '+')) {
            $array = explode('+', $mime);
            $this->imageMime = $array[0];
        } else {
            $this->imageMime = $mime;
        }

        if ($type !== 'image') {
            $this->errorMassage = 'Это не картинка';
            return false;
        }

        if ($code === 'base64') {
            $this->imageCode = base64_decode($image[1]);
            return true;
        } elseif ($code === 'utf8') {
            $this->imageCode = html_entity_decode($image[1]);
            return true;
        } else {
            $this->errorMassage = 'Это не картинка';
            return false;
        }

    }

    private function createImage($id, $title) {

        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $imageName = 'image-' . substr(str_shuffle($permitted_chars), 0, 16) . '.' . $this->imageMime;
        $path = $this->wp_get_upload_dir['path'] . DS . $imageName;
        $uri = parse_url($this->wp_get_upload_dir['url']);
        $this->oldSrc = $this->imageCode;
        $this->path = $uri['path'] . '/' . $imageName;
        $this->download = true;

        fopen($path, "w");
        file_put_contents($path, $this->imageCode, LOCK_EX);

        if (class_exists('Imagick')) {
            if (!$this->checkImage($path)) {
                unlink($path);
                $this->download = false;
                $this->errorMassage = 'Это не картинка';
                return false;
            }
        }

        if ($this->imageMime !== 'svg') {
            $this->insertAttachments($imageName, $id, $title);
        }

        $this->imageName = $imageName;
        return $path;

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

    private function checkImage($path): bool {

        $imagick = new \Imagick();
        $rawImage = file_get_contents($path);
        try {
            $imagick->readImageBlob($rawImage);
        } catch (\ImagickException $e) {
            return false;
        }
        return true;

    }

    public function isDownload(): bool
    {
        return $this->download;
    }

}
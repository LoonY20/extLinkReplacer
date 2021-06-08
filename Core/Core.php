<?php

use Post\Post;

class Core
{

    private $response;

    public function optimizePosts($id): array
    {

        $response['postId'] = $id;
        $response['downloadImage'] = 0;
        $response['setAlt'] = 0;
        $response['editSrc'] = 0;
        $response['delete'] = 0;
        $response['postUpdate'] = false;
        $response['massage'] = '';
        $response['error'] = [];
        $post = new Post($id);
        $result = $post->findImageTag();

        if (!$result) {
            $response['massage'] = 'Image not found';

            return $response;
        }

        $response = $this->imageIterator($post, $response);

        if ($response['postUpdate']) {
            $post->updateBody();
            $response['massage'] = 'Замена произведена';
        } else {
            $response['massage'] = 'Не обновили пост';
        }

        return $response;

    }

    public function optimizeImages($image)
    {

        $image = new \Image\InternalImage($image, true);

        if ($image->getImageMime() !== 'svg' && $image->getImageMime()) {
            $image->compress();
        }

        $response['imageName'] = $image->imageName;
        $response['imageSize'] = intdiv($image->imageSize, 1000);
        if (gettype($image->newSize) === 'integer') {
            $response['newSize'] = intdiv($image->newSize, 1000);
        } else {
            $response['newSize'] = $image->newSize;
        }

        $response['errorMassage'] = $image->errorMassage;

        return $response;

    }

    public function deleteImage()
    {

        $image = new \Image\InternalImage($_POST['path'], true);

        $result = $image->deleteImage($image->imageName);

        $result = $result ? 'remove' : 'nothing';

        return ['path' => $_POST['path'], 'action' => $result];

    }

    public function saveImage($image)
    {

        $helper = new Helper();

        return $helper->saveImageInDataBase($image);

    }

    public function saveImageFromPost($postId): int
    {

        $post = new Post($postId);
        $post->findImageTag();
        $count = 0;
        if ($post->getImagesTag()) {
            foreach ($post->getImagesTag() as $imageTag) {
                $image = new \Image\ImageTag($imageTag[0], $post);
                if ($image->saveImage()) {
                    $count++;
                }
            }
        }

        return $count;

    }

    public function addFilter($data, $postarr)
    {
        require_once 'const.php';
        require_once 'autoloader.php';

        $post = new Post($postarr['ID']);
        $body = wp_unslash($data['post_content']);
        $response = [];
        $response['postUpdate'] = false;

        $post->setBody($body);

        $result = $post->findImageTag();

        if (!$result) {
            add_filter('redirect_post_location', function ($location) {
                return add_query_arg('extLinkReplacerMessage', 1, $location);
            }, 10);
            return $data;
        }

        $response = $this->imageIterator($post, $response);

        if ($response['postUpdate']) {
            $data['post_content'] = wp_slash($post->getBody());
            add_filter('redirect_post_location', function ($location) {
                return add_query_arg('extLinkReplacerMessage', 2, $location);
            }, 10);
            return $data;

        } else {
            add_filter('redirect_post_location', function ($location) {
                if (!strstr($location, 'extLinkReplacerMessage')) {
                    return add_query_arg('extLinkReplacerMessage', 1, $location);
                } else {
                    return $location;
                }
            }, 10);
            return $data;
        }
    }

    private function imageIterator($post, $response): array
    {

        foreach ($post->getImagesTag() as $imageTag) {

            $image = new \Image\ImageTag($imageTag[0], $post);
            $image->init();

            if ($image->getErrorMessage()) {
                $response['error'][] = $image->getSrc() . ' - ' . $image->getErrorMessage();
            }

            if ($image->downloaded) {
                $response['downloadImage']++;
            }
            if ($image->setAlt) {
                $response['setAlt']++;
            }
            if ($image->editSrc) {
                $response['editSrc']++;
            }
            if ($image->delete) {
                $response['delete']++;
            }

            if ($image->checkEdit()) {
                $response['postUpdate'] = true;
            }

        }

        return $response;

    }

}
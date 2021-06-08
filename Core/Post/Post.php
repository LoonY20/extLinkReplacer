<?php

namespace Post;

class Post
{

    private $id;
    private $body;
    private $title;
    private $imagesTag;

    public function __construct($id)
    {

        $this->id = $id;
        $this->body = get_the_content(null, null, $id);
        $this->title = get_the_title($id);

    }

    public function findImageTag(): bool
    {
        $result = preg_match_all(
            '/<img[^^]*?>/',
            $this->body,
            $imageTags,
            PREG_SET_ORDER
        );

        if ($result) {
            $this->imagesTag = $imageTags;
            return true;
        } else {
            return false;
        }
    }

    public function replaceImageTag(string $oldTag, string $newTag) {

        $this->body = str_replace($oldTag, $newTag, $this->body, $count);

        return $count;

    }

    public function updateBody()
    {
        $my_post = array();
        $my_post['ID'] = $this->id;
        $my_post['post_content'] = $this->body;

        if (!defined('WP_POST_REVISIONS')) {
            define('WP_POST_REVISIONS', true);
        }

        return wp_update_post(wp_slash($my_post));
    }

    public function deleteTag($tag): int
    {
        $regex = '/(<figure.*?>[^<]*?)?(' . addcslashes($tag, '/.?') . '){1}[^^]*?(<\/figure>)?/';

        $count = 0;
        $this->body = str_replace($tag, '', $this->body, $count);
        if ($count > 0) {
            $this->body = preg_replace('/<figure[^>]*>[\s\n]*?<\/figure>/', '', $this->body);
            $this->body = preg_replace('/<!-- wp:image {.*?} -->[\s\n]*?<!-- \/wp:image -->/', '', $this->body);
        }
        return $count;

    }



    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }



    /**
     * @return array
     */
    public function getImagesTag(): array
    {

        if (isset($this->imagesTag))return $this->imagesTag;
        return [];
    }

    /**
     * @param array $imagesTag
     */
    public function setImages(array $imagesTag): void
    {
        $this->imagesTag = $imagesTag;
    }



}
<?php


namespace Image;

use Post\Post;

class ImageTag {

	private $src;
	private $alt;
	private $class;
	private $id;
	private $oldTag;
	private $errorMessage = '';

	public $downloaded = false;
	public $setAlt = false;
	public $setWidth = false;
	public $setHeight = false;
	public $editSrc = false;
	public $delete = false;
	public $createImage = false;

	public $editCounter = 0;

	private $imageObject;
	private $post;

	private $attributes = [
		'src',
		'alt',
		'width',
		'height',
	];

	public function __construct( $imageTag, $post ) {
		$this->oldTag = $imageTag;
		$this->post   = $post;
		$this->explodeTag( $imageTag );
		$this->createImage = $this->createImageObject();
	}

	public function init() {
		if ( ! $this->createImage ) {
			return;
		}
		if ( $this->checkEdit() ) {
			$this->editCounter = $this->replaceOldTag();

		}
	}

	public function saveImage() {

		return $this->imageObject->saveImageInDataBase();

	}

	public function replaceOldTag() {

		$newTag = $this->createNewTag();

		return $this->post->replaceImageTag( $this->oldTag, $newTag );

	}

	public function checkEdit(): bool {

		return $this->editSrc || $this->downloaded || $this->setAlt || $this->setWidth|| $this->setHeight || $this->delete;

	}

	private function delete(): int {
		return $this->post->deleteTag( $this->oldTag );
	}

	private function createNewTag(): string {

		$tag = '<img';
		foreach ( $this->attributes as $attribute ) {
			switch ( $attribute ) {
				case 'width':
					if ( $this->imageObject->getImageWidth() && $this->imageObject->getImageMime() !== 'svg' ) {
						$tagAttributes[ $attribute ] = $attribute . '="' . $this->imageObject->getImageWidth() . '"';
					}

					break;
				case 'height':
					if ( $this->imageObject->getImageHeight() && $this->imageObject->getImageMime() !== 'svg' ) {
						$tagAttributes[ $attribute ] = $attribute . '="' . $this->imageObject->getImageHeight() . '"';
					}
					break;
				default:
					if ( ! empty( $this->$attribute ) ) {
						$tagAttributes[ $attribute ] = $attribute . '="' . $this->$attribute . '"';
					}
					break;
			}

		}

		foreach ( $tagAttributes as $tagAttribute ) {
			$tag .= ' ' . $tagAttribute;
		}
		$tag .= '>';

		return $tag;

	}

	private function explodeTag( $imageTag ) {

		foreach ( $this->attributes as $attribute ) {
			preg_match(
				'/' . $attribute . '="(.*?)"/',
				$imageTag,
				$option
			);

			if ( empty( $option[1] ) ) {
				switch ( $attribute ) {
					case 'alt':
						$this->alt    = $this->post->getTitle();
						$this->setAlt = true;
						break;
					case 'width':
						$this->setWidth = true;
						break;
					case 'height':
						$this->setHeight = true;
						break;
				}
			}

			if ( ! empty( $option[1] ) ) {
				$this->$attribute = $option[1];
			}
		}

	}

	private function createImageObject(): bool {

		if ( $this->src[0] === '/' ) {

			$this->imageObject = new InternalImage( $this->src );
			$this->imageObject->init();

			return true;

		} else if ( strstr( $this->src, $_SERVER['HTTP_HOST'] ) ) {

			$this->editSrc     = true;
			$this->src         = parse_url( $this->src )['path'];
			$this->imageObject = new InternalImage( $this->src );
			$this->imageObject->init();

			return true;

		} else {

            if (!strstr($this->src, 'data:image')) {
                $this->imageObject = new ExternalImage( $this->src, $this->post->getId(), $this->post->getTitle() );
            } else {
                $this->imageObject = new ExternalImage( $this->src, $this->post->getId(), $this->post->getTitle() );
            }



			if ( $this->imageObject->isDownload() ) {
				$this->src        = $this->imageObject->getPath();
				$this->downloaded = true;
				return true;
			} else {
				$this->setAlt       = false;
				$this->errorMessage = $this->imageObject->getErrorMessage();

				$options = new \Options();
				if ( $options->getOption( 'delete' ) ) {
					$deleteCount = $this->delete();
					if ( $deleteCount > 0 ) {
						$this->delete = true;
					} else {
						$this->errorMessage .= ' - не удалилось по какой то причине';
					}
				}


				return false;
			}

		}
	}

	private function checkTag(): bool {

		return isset( $this->src );

	}

	/**
	 * @return string
	 */
	public function getErrorMessage(): string {
		return $this->errorMessage;
	}

	/**
	 * @return string
	 */
	public function getSrc(): string {
		return $this->src;
	}


}
<?php


namespace Image;


class InternalImage extends Image {

	public function __construct( string $path, bool $absolute = false ) {

		$path = $absolute ? $path : WP_ROOT . $path;
		parent::__construct( $path );

		$this->imageMime = \Helper::checkMimeType($path);

		if ( $this->imageMime !== 'svg' && $this->imageMime ) {
			$imageInfo         = getimagesize( $path );

			if ($imageInfo) {
				$this->imageWidth  = $imageInfo[0];
				$this->imageHeight = $imageInfo[1];
				$this->imageSize   = filesize( $path );
			}
		}
	}

	public function init() {
		if ( $this->imageMime !== 'svg' && $this->imageMime ) {
			$this->attachmentId = $this->findImageInDataBase( $this->imageName, $this->imageMime );
			$this->newSize      = $this->optionalCompressImage( $this->path );
		}

	}

	public function compress() {

		$this->newSize = $this->necessarilyCompressImage( $this->path );

	}

	private function findImageInDataBase( string $name, string $mime ): int {

		$name = str_replace( '.' . $mime, '', $name );
		$args = array(
			'posts_per_page' => 1,
			'post_type'      => 'attachment',
			'fields'         => 'ids',
			'name'           => $name,
		);

		$get_attachment = new \WP_Query( $args );

		if ( ! $get_attachment || ! isset( $get_attachment->posts, $get_attachment->posts[0] ) ) {
			return 0;
		}


		return $get_attachment->posts[0];

	}

	public function deleteImage( $image ): bool {

		$helper  = new \Helper();
		$options = new \Options();

		if ( ! $helper->getSavedImages( $image ) ) {
			return unlink( $this->path );
		}

		return false;
	}

	public function saveImageInDataBase() {

		global $wpdb;
		$table_name = $wpdb->prefix . "extLinkReplacerClear";
		$sql        = "INSERT INTO {$table_name} (name) VALUES ('{$this->imageName}') ON DUPLICATE KEY UPDATE name=name;";

		return $wpdb->query( $sql );

	}

}
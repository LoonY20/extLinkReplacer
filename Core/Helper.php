<?php

class Helper {

	private $options;

	public function __construct() {

		$this->options = new Options();

	}

	public function getAllPosts() {
		set_error_handler(
			function ( $severity, $message, $file, $line ) {
				throw new ErrorException( $message, $severity, $severity, $file, $line );
			}
		);

		$options = $this->options->getOption( 'postType' );

		try {
			$results     = new WP_Query( array(
				'post_type'      => $options,
				'fields'         => 'ids',
				'posts_per_page' => - 1
			) );
			$jsonResults = $results->posts;
		} catch ( Throwable  $e ) {
			$jsonResults = $e->getMessage();
		}
		restore_error_handler();

		return $jsonResults;
	}

	public function getAllImages( $echo = true, $month = false ): array {

		require_once ABSPATH . 'wp-admin/includes/file.php';

		if ( $month ) {
			$path = wp_get_upload_dir()['basedir'] . DS . $this->options->getOption( 'cleanYear' ) . DS . $this->options->getOption( 'cleanMonth' );
		} else {
			$path = $this->options->getOption( 'dirPath' );
		}
		$files      = list_files( $path );
		$imagesList = [];

		foreach ( $files as $file ) {
			$type = explode( '/', mime_content_type( $file ) );

			if ( $type[0] === 'image' ) {
				array_push( $imagesList, $file );
			}
		}

		if ( $echo ) {
			echo json_encode( $imagesList );
			exit();
		} else {
			return $imagesList;
		}
	}

	public function getSavedImages( $name ): array {
		global $wpdb;
		$table_name = $wpdb->prefix . "extLinkReplacerClear";
		$sql        = "SELECT name FROM {$table_name} WHERE name='{$name}'";

		return $wpdb->get_results( $sql );

	}

	public function getAllImagesFromDatabase(): array {
		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => 'attachment',
			'post_status'    => 'any'
		);

		$get_attachment = new \WP_Query( $args );

		foreach ( $get_attachment->posts as $image ) {
			$nameArray = explode( '/', $image->guid );
			$images[]  = end( $nameArray );

		}

		return $images;
	}

	public function getAllSavedImages(): array {

		global $wpdb;
		$table_name = $wpdb->prefix . "extLinkReplacerClear";
		$sql        = "SELECT name FROM {$table_name}";

		$result = $wpdb->get_results( $sql );

		foreach ( $result as $image ) {
			$images[] = $image->name;
		}

		return $images;
	}

	public function saveImageInDataBase( $name ) {

		global $wpdb;
		$table_name = $wpdb->prefix . "extLinkReplacerClear";
		$sql        = "INSERT INTO {$table_name} (name) VALUES ('{$name}') ON DUPLICATE KEY UPDATE name=name;";

		return $wpdb->query( $sql );

	}

	static function checkMimeType( $image ): string {

		if (file_exists($image)) {
			$array = explode( '/', mime_content_type( $image ) );
			$mime  = end( $array );

			switch ( $mime ) {
				case 'svg+xml':
					return 'svg';
				default:
					return $mime;

			}
		} else {
			return '';
		}


	}
}
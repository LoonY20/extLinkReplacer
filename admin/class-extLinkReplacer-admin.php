<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    extLinkReplacer
 * @subpackage extLinkReplacer/admin
 * @author     Erik Zalialutdinov <erikza@wizardsdev.com>
 */
class extLinkReplacer_Admin {

	private $extLinkReplacer;
	private $version;


	public function __construct( $extLinkReplacer, $version ) {
		add_action( 'admin_enqueue_scripts', function () {
			$this->enqueue_styles();
			$this->enqueue_scripts();

		} );
		$this->extLinkReplacer = $extLinkReplacer;
		$this->version         = $version;

		add_action( 'admin_menu', array( $this, 'addMenu' ) );

	}

	function addMenu() {
		add_menu_page(
			'extLinkReplacer',
			'extLinkReplacer',
			'manage_options',
			'extLinkReplacer',
			array( $this, 'render' ),
			'dashicons-images-alt2',
		);
		add_submenu_page(
			'extLinkReplacer',
			'optomizemyimage',
			'Optomize',
			'manage_options',
			'extLinkReplacerOptomize',
			array( $this, 'renderOptomizeMyImage' ),
			2 );
		add_submenu_page(
			'extLinkReplacer',
			'clean',
			'Clean(Alpha)',
			'manage_options',
			'extLinkReplacerClean',
			array( $this, 'renderCleanUploads' ),
			3 );
		add_submenu_page(
			'extLinkReplacer',
			'options',
			'Options',
			'manage_options',
			'extLinkReplacerOptions',
			array( $this, 'renderOptions' ),
			4 );


	}

	public function render() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/extLinkReplacer-admin-display.php';
	}

	public function renderOptions() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/extLinkReplacerOptions-admin-display.php';
	}

	public function renderCleanUploads() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/extLinkReplacerRenderCleaner-admin-display.php';
	}

	public function renderOptomizeMyImage() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/extLinkReplacerrenderOptomizeMyImage-admin-display.php';
	}

	public function enqueue_styles() {

		wp_enqueue_style( 'main', plugin_dir_url( __FILE__ ) . 'css/extLinkReplacer-admin.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {

		global $plugin_page;
		require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Options.php';
		$option = new Options();
		$hook   = $option->getOption( 'hookEnable' );

		if ( $hook === 'on' && ( get_option( 'classic-editor-replace' ) !== 'classic' || !get_option( 'classic-editor-replace') )) {
			wp_enqueue_script( $this->extLinkReplacer . '-postEditor', plugin_dir_url( __FILE__ ) . 'js/extLinkReplacer-postEditor.js', array(
				'wp-i18n',
				'wp-blocks',
				'wp-editor',
				'wp-components'
			), $this->version, false );
		}
		if ( $plugin_page ) {
			wp_enqueue_script( $this->extLinkReplacer, plugin_dir_url( __FILE__ ) . 'js/extLinkReplacer-admin.js', array(), $this->version, false );
		}


		wp_localize_script( $this->extLinkReplacer, 'wpApiSettings', array(
			'root' => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		) );

	}

}

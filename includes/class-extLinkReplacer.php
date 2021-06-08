<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      5.0.1
 *
 * @package    extLinkReplacer
 * @subpackage extLinkReplacer/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      5.0.1
 * @package    extLinkReplacer
 * @subpackage extLinkReplacer/includes
 * @author     Erik Zalialutdinov <erikza@wizardsdev.com>
 */
class extLinkReplacer {

	protected $version;

	public function __construct() {

		if ( defined( 'extLinkReplacer_VERSION' ) ) {
			$this->version = extLinkReplacer_VERSION;
		} else {
			$this->version = '5.0.1';
		}
		$this->extLinkReplacer = 'extLinkReplacer';

		$this->load_dependencies();
		$this->define_admin_hooks();
		require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Options.php';
		$option = new Options();

		if ( $option->getOption( 'hookEnable' ) ) {
			if ( array_key_exists('extLinkReplacerMessage', $_GET) ) {
				if ( $_GET['extLinkReplacerMessage'] === '1' ) {
					add_action( 'admin_notices', function () {
						echo '<div class="notice notice-info is-dismissible">';
						echo '<p>Плагин ExtLinkReplacer ничего не заменил</p>';
						echo '</div>';
					} );
				} elseif ( $_GET['extLinkReplacerMessage'] === '2' ) {
					add_action( 'admin_notices', function () {
						echo '<div class="notice notice-success is-dismissible">';
						echo '<p>Плагин ExtLinkregister_activation_hookReplacer произвел действия над постом</p>';
						echo '</div>';
					} );
				}
			}

			require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Core.php';
			$core = new Core();

			if (get_option( 'classic-editor-replace' ) === 'classic') {
                add_filter( 'wp_insert_post_data', [ $core, 'addFilter' ], 10, 2 );
            }

		}

	}


	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-extLinkReplacer-admin.php';

	}

	private function define_admin_hooks() {
		new extLinkReplacer_Admin( $this->get_extLinkReplacer(), $this->get_version() );
	}


	public function get_extLinkReplacer() {
		return $this->extLinkReplacer;
	}

	public function get_version() {
		return $this->version;
	}

}
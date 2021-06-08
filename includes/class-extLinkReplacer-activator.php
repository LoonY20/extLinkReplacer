<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      5.0.1
 *
 * @package    extLinkReplacer
 * @subpackage extLinkReplacer/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      5.0.1
 * @package    extLinkReplacer
 * @subpackage extLinkReplacer/includes
 * @author     Erik Zalialutdinov <erikza@wizardsdev.com>
 */
class extLinkReplacer_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    5.0.1
     */
    public static function activate()
    {
        self::safe();
    }

    public static function activateForMultisite()
    {

        foreach (get_sites() as $site) {
            switch_to_blog($site->blog_id);

            self::safe();

            restore_current_blog();
        }
    }

    public static function safe()
    {

        $dir = wp_upload_dir();

        $options = [
            'postType' => ['post'],
            'download' => '',
            'delete' => '',
            'width' => '1024',
            'height' => '924',
            'quality' => '75',
            'maxSize' => '150000',
            'dirPath' => $dir['basedir'],
            'timeDelay' => '500',
            'hookEnable' => '',
            'cleanMonth' => '01',
            'cleanYear' => '2021',
        ];

        add_option('extLinkReplacerOption', $options);

//        global $wpdb;
//        $table_name = $wpdb->prefix . "extLinkReplacerClear";
//        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
//            $sql = "CREATE TABLE " . $table_name . " (
//					  name varchar(100) NOT NULL,
//					  PRIMARY KEY  (name)
//					) {$charset_collate};";
//            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//            $result = dbDelta($sql);
//        }

    }

}

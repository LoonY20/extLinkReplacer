<?php

/**
 * Rest Api extLinkReplacer.
 *
 * This class defines Rest Api for this plugin.
 *
 * @since      5.0.1
 * @package    extLinkReplacer
 * @subpackage extLinkReplacer/includes
 * @author     Erik Zalialutdinov <erikza@wizardsdev.com>
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . '../Core/const.php';
require_once ROOT_CORE . DS . 'autoloader.php';

class RestController extends WP_REST_Controller
{
    public $helper;
    public $core;
    public $options;
    public $post;

    function __construct()
    {
        $this->core = new Core();
        $this->helper = new Helper();
        $this->options = new Options();
        $this->namespace = 'ext-link-replacer/v1';

    }

    function register_routes()
    {

        // GET

        register_rest_route($this->namespace, "/getallposts", [
            [
                'methods' => 'GET',
                'callback' => [$this->helper, 'getAllPosts'],
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/getallimage", [
            [
                'methods' => 'GET',
                'callback' => [$this->helper, 'getallimages'],
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/getallimagebymonth", [
            [
                'methods' => 'GET',
                'callback' => function () {
                    return $this->helper->getAllImages(false, true);
                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/getalloptions", [
            [
                'methods' => 'GET',
                'callback' => function () {
                    return $this->options->getOptions();
                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/getallimagesfromdatabase", [
            [
                'methods' => 'GET',
                'callback' => function () {
                    return $this->helper->getAllImagesFromDatabase();

                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/getallsavedimages", [
            [
                'methods' => 'GET',
                'callback' => function () {
                    return $this->helper->getAllSavedImages();

                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);

//        //POST

        register_rest_route($this->namespace, "/optimizeposts", [
            [
                'methods' => 'POST',
                'callback' => function () {
                    return $this->core->optimizePosts($_POST['id']);
                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/optimizeimages", [
            [
                'methods' => 'POST',
                'callback' => function () {
                    return $this->core->optimizeImages($_POST['image']);
                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/cleartrashimages", [
            [
                'methods' => 'POST',
                'callback' => function () {
                    return $this->core->deleteImage();
                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/updateoptions", [
            [
                'methods' => 'POST',
                'callback' => function () {
                    $this->options->updateOptions();
                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/saveimage", [
            [
                'methods' => 'POST',
                'callback' => function () {
                    return $this->core->saveImage($_POST['path']);
                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);
        register_rest_route($this->namespace, "/saveimagefromPost", [
            [
                'methods' => 'POST',
                'callback' => function () {
                    return $this->core->saveImageFromPost($_POST['id']);
                },
//                'permission_callback' => [$this, 'get_items_permissions_check'],
            ]
        ]);



    }

    function get_items_permissions_check($request)
    {
        if (!current_user_can('manage_options'))
            return new WP_Error('rest_forbidden', esc_html__('You cannot view the post resource.'), ['status' => $this->error_status_code()]);

        return true;
    }

    function error_status_code()
    {
        return is_user_logged_in() ? 403 : 401;
    }

}
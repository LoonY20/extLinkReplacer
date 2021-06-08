=== extLinkReplacer ===
Tags: images, external link
Requires at least: 1.0
Tested up to: 5.0
Stable tag: 5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

This plugin scan all of your posts for external image links, download images to folder
`/wp-content/uploads/'year'/'months'/` and replace old links with `/wp-content/uploads/'year'/'months'/'image-name'`.
After activating the plugin, a table with logs (name) will be created. When going to the settings page, the plugin
automatically sends a query to the database for all id posts. After clicking the start button, a separate request
is sent for each id to get the content of the post, which searches for images from external resources and copying
them to our site using standard wordpress methods. As a result, the plugin logs will be saved to the name table and
displayed on the screen. After deleting the plugin, the table with logs will be deleted.


== Future updates ==

In the next version the plugin will be able to recognize temporary google image links!!

== Installation ==

1. Upload `extLinkReplacer.zip` to the wp-admin->Plugin->Add New->Upload Plugin
    or
1. Upload `extLinkReplacer` folder to the `/wp-content/plugins/` directory with FTP manager

2. Activate plugin in wp-admin -> Plugins
3. To start scanning your posts, get on the link '/wp-admin/admin.php?page=menu-template' and put 'START' button.


== Changelog ==

= 5.0 =
* Stable version

`<?php code(); // goes in backticks ?>`
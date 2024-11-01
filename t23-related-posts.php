<?php
/*
Plugin Name: T23 Related Posts
Plugin URI: http://www.itlessons.info/

Description: A simple related posts plugin that using Sphinx server to search related content

Author: itlessons
Author URI: http://www.itlessons.info

Version: 0.0.1

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

if(is_admin()){
    require_once __DIR__.'/inc/admin.php';
    new T23RelatedPostsAdmin();
} else{
    require_once __DIR__.'/inc/frontend.php';
    new T23RelatedPostsFrontend();
}
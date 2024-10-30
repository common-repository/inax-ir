<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");
/*
Plugin Name: inax-ir
Plugin URI:  https://inax.ir/wordpress-plugin
Description: توسط این پلاگین میتوانید امکان خرید شارژ، بسته اینترنت و پرداخت قبض را به وب سایت خود اضافه کرده و کسب درآمد کنید
Version:     3.4
Author:      نمایندگی فروش شارژ آینکس
Author URI:  https://inax.ir
Text Domain: inax
Domain Path: /languages
*/

# Copyright 2005-2020  Wordpress inax-ir  (email : info@inax.ir)
# 
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
# 
# 
# Contributors:
#
# Since 1.0:
#       Mohamamd Moradpour
#

/*
 * define plugin dir
 */
defined('INAX_DIR') or define('INAX_DIR',  dirname(__FILE__).DIRECTORY_SEPARATOR);
defined('INAX_DIR2') or define('INAX_DIR2',  dirname(__FILE__));
defined('INAX_Main_File_Path') or define('INAX_Main_File_Path',  __FILE__ );//inc/db.php
//echo $plugins_url 		= plugins_url('/inc', __FILE__);

//for use css & js import
if ( ! defined( 'INAX_PLUGIN_FILE' ) ) {
	define( 'INAX_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'INAX_language_path' ) ) {
	//used in load_plugin_textdomain
	define( 'INAX_language_path', basename(dirname(__FILE__)).'/languages' );
}

//defined('inax_img_url') or define('inax_img_url',  plugins_url('/inc/templates/images', __FILE__ ));

/**
 * include structor
 */
include INAX_DIR.'inc/inax-functions.php';

/**
 * include libs
 */
include INAX_DIR.'inc'.DIRECTORY_SEPARATOR.'db.php';
//require_once( dirname( __FILE__ ) .'/db.php' );


/**
 * initialize...
 */
inax_init();
register_activation_hook(__FILE__, 'inax_installer');
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'inax_add_settings_link' );

/* Additional links on the plugin page */
if( ! function_exists( 'inax_register_plugin_links' ) ){
	function inax_register_plugin_links( $links, $file ){
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ){
			if ( ! is_network_admin() ){
				//$links[]	=	'<a href="admin.php?page=captcha.php">' . __( 'Settings', 'captcha' ) . '</a>';
			}
			$links[] = '<a href="https://inax.ir/panel/login.php" target="_blank">ورود</a>';
			$links[] = '<a href="https://inax.ir/panel/register.php" target="_blank">ثبت نام</a>';
			$links[] = '<a href="https://inax.ir/wordpress-plugin" target="_blank">پشتیبانی</a>';
		}
		return $links;
	}
}
add_filter( 'plugin_row_meta', 'inax_register_plugin_links', 10, 2 );


/**
 * include admin stuff
 */
include INAX_DIR.'inc'.DIRECTORY_SEPARATOR.'inax-admin.php';


/*
//check for update
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker('https://inax.ir/plugin.json',
	__FILE__, //Full path to the main plugin file or functions.php.
	'inax'
);*/


//https://wordpress.stackexchange.com/questions/17385/custom-post-type-templates-from-plugin-folder
/*add_filter( 'template_include', 'index_page_template', 99 );
function index_page_template( $template ){
	global $inax_option;
	$inax_theme             = isset($inax_option['theme']) ? $inax_option['theme'] : 'default';

	if ( is_page( 'main' ) ){
		$file_name = 'page-index.php';
		if ( locate_template( $file_name ) ){//check if exist in theme folder
			$template = locate_template( $file_name );
		} else {
			// Template not found in theme's folder, use plugin's template as a fallback
			$template = dirname( __FILE__ ) . '/' . $file_name;
		}
	}
	elseif ( is_page( 'verify' ) ){
		$template = dirname( __FILE__ ) . '/page-verify.php';
	}
	return $template;
}*/
?>
<?php
if (!defined("ABSPATH"))	die("This file cannot be accessed directly");

/**
 * register admin menu
 * @see http://codex.wordpress.org/Administration_Menus
 */
add_action('admin_menu', 'inax_reg_admin_meun_fn');

function inax_reg_admin_meun_fn(){
    global $inaxir;
    $inaxir = add_menu_page(
            'تنظیمات آینکس', // page title 
            'تنظیمات آینکس', // menu title
            'manage_options', // user access capability
            'inaxir', // menu slug
            'inaxir_fn', //menu content function
            plugins_url('/assets/images/plugin-icon.png', INAX_PLUGIN_FILE ), // menu icon
            82 // menu position
    );
	add_submenu_page('inaxir', 'صفحات خرید آینکس', 'صفحات خرید' , 'manage_options', 'inax_page', 'inax_page_fn');
	add_submenu_page('inaxir', 'شارژ و استعلام موجودی', 'موجودی آینکس' , 'manage_options', 'inax_credit', 'inax_credit_fn');
	add_submenu_page('inaxir', 'تراکنش های آینکس', 'تراکنش ها' , 'manage_options', 'inax_trans', 'inax_trans_fn');
	add_submenu_page('inaxir', 'درگاه های پرداخت آینکس', 'درگاه های بانکی' , 'manage_options', 'inax_gateway', 'inax_gateways');
	add_submenu_page('inaxir', 'جزئیات سیستم', 'جزئیات سیستم' , 'manage_options', 'inax_system', 'inax_system_fn1');
    add_submenu_page('inaxir', 'درباره آینکس', 'درباره آینکس' , 'manage_options', 'inax_about', 'inax_about_fn1');
	
    //add_action('load-' . $inaxir, 'inax_admin_save_option_page_fn');
}

function inaxir_fn(){
    //include INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'inax-admin-option.php';
	include INAX_DIR .  DIRECTORY_SEPARATOR . 'inax-admin-option.php';
}

function inax_system_fn1(){
	include INAX_DIR . DIRECTORY_SEPARATOR . 'inax-system.php';
}

function inax_about_fn1(){
//    wp_enqueue_style( 'wp-pointer' );
//    wp_enqueue_script( 'wp-pointer' );
    //include INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'inax-about.php';
	include INAX_DIR . DIRECTORY_SEPARATOR . 'inax-about.php';
}

function inax_gateways(){
	include INAX_DIR . DIRECTORY_SEPARATOR . 'gateways.php';
}

function inax_trans_fn(){
	global $wpdb;
    //include INAX_DIR . DIRECTORY_SEPARATOR . 'inax-trans.php';
    include INAX_DIR . DIRECTORY_SEPARATOR . 'inax-trans.php';
}

function inax_credit_fn(){
	global $inax_option;
    //include INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'inax-credit.php';
	include INAX_DIR . DIRECTORY_SEPARATOR . 'inax-credit.php';
}

function inax_page_fn(){
	include INAX_DIR . DIRECTORY_SEPARATOR . 'inax-pages.php';
}

function inax_admin_save_option_page_fn(){
	//include INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'inax-admin-option.php';
	include INAX_DIR . DIRECTORY_SEPARATOR . 'inax-admin-option.php';
}

/**
 * after install actions
 */
add_action('admin_init', 'inax_after_install_actions');
function inax_after_install_actions(){
    $active = get_option('inax_do_activation');
    if ($active){
    	//display message in all wordpress admin page
        add_action('admin_notices', 'inax_admin_message');
    }
}

function inax_admin_message(){
	global $inax_option;
	//print_r($inax_option);
	$username = isset($inax_option['username']) ? $inax_option['username'] : '';
	$password = isset($inax_option['password']) ? $inax_option['password'] : '';
	//$parts           = parse_url( admin_url() );
	//$url             = "{$parts['scheme']}://{$parts['host']}" . add_query_arg( NULL, NULL );
	$screen = get_current_screen();
	if( is_object($screen) && $screen->id!=='toplevel_page_inaxir' && ($username=='' || $password=='' ) ){
		//$Message=  sprintf( __('inax successful installed. please check %soptions%s','inax') ,'<a href="'.menu_page_url('inaxir',FALSE).'">', '</a>' );
		$Message=  sprintf( "افزونه آینکس با موفقیت نصب شد. لطفا برای تنظیم نام کاربری و پسورد وب سرویس بر روی لینک %sتنظیمات%s کلیک نمائید." ,'<a href="'.menu_page_url('inaxir',FALSE).'">', '</a>' );
		echo '<div class="updated"><p>' . $Message . '</p></div>';
	}
}
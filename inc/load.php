<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");
require_once( dirname( __FILE__ ) .'/validation.php' );
require_once( dirname( __FILE__ ) .'/smarty-4.3.0/libs/Smarty.class.php' );

$test_mode = false;

global $wpdb;
$inax_bill_db   = $wpdb->prefix . 'inax_bill';//used in db.php
$inax_charge_db = $wpdb->prefix . 'inax_charge';

$validate = new SimaNet_Validate;

$smarty = new Smarty;

$msc = microtime(true);

/*ini_set("display_errors", 1);
error_reporting(E_ALL); 
$smarty->debugging = false;*/

$smarty->setTemplateDir( realpath(__DIR__ . '/../') . '/templates' );
$smarty->setCompileDir( dirname( __FILE__ ) . '/templates_c');

global $inax_option;
$display_error          = (isset($inax_option['display_error']) && $inax_option['display_error'] ) ? true : false;
$inax_theme             = isset($inax_option['theme']) ? $inax_option['theme'] : 'default';//تنظیم به عنوان قالب پیش فرض
$inax_time_limitation   = isset($inax_option['time_limitation']) ? $inax_option['time_limitation'] : '';//محدودیت زمانی خرید
$inax_amount_limitation = isset($inax_option['amount_limitation']) ? $inax_option['amount_limitation'] : 0;//محدودیت مبلغ خرید
//$payment_gateway        = isset($inax_option['payment_gateway']) ? $inax_option['payment_gateway'] : '';//درگاه پرداخت انلاین
$smarty->assign('inax_theme', $inax_theme );
$smarty->assign('inax_amount_limitation', $inax_amount_limitation );

//defined('inax_img_url') or define('inax_img_url',  plugins_url("../templates/client/$inax_theme/images", __FILE__ ));
defined('inax_img_url') or define('inax_img_url',  plugins_url("assets/images", INAX_PLUGIN_FILE ));
//$inax_img_url 	= plugins_url('/templates/images', __FILE__);
$smarty->assign('inax_img_url', inax_img_url );


defined('inax_admin_img_url') or define('inax_admin_img_url',  plugins_url("assets/images", INAX_PLUGIN_FILE ));
$smarty->assign('inax_admin_img_url', inax_admin_img_url );

$gateways_dir = realpath(__DIR__ . '/../') . '/gateways';//must globaled in post__shortcode function

//$wordpress_csrf = wp_nonce_field( 'name_of_my_action', 'Token',true,true );
$wordpress_csrf = wp_nonce_field( 'name_of_my_action', 'Token' ,true,false);
$smarty->assign('wordpress_csrf',$wordpress_csrf);

$wpdb->query( "SET time_zone='+4:30'; ");

$timezone = get_option('timezone_string');
date_default_timezone_set($timezone);

//$display_error = false;
if($display_error){
	ini_set("display_errors", 1);
	error_reporting(E_ALL);
	$smarty->debugging = false;
	//$smarty->clearAllCache();//https://www.smarty.net/docs/en/caching.tpl

}

//افزونه فارسی ساز نصب نشده باشد یا فعال نباشد
/*if( !function_exists('jdate') &&
	!function_exists('jstrftime') &&
	!function_exists('jmktime') &&
	!function_exists('jgetdate') &&
	!function_exists('jcheckdate') &&
	!function_exists('tr_num') &&
	!function_exists('jdate_words') &&
	!function_exists('gregorian_to_jalali') &&
	!function_exists('jalali_to_gregorian') &&
	!in_array('wp-jalali/wp-jalali.php', apply_filters('active_plugins', get_option('active_plugins'))) ){
    //
}*/
//echo $language_code;exit;
/*if( is_admin() ) { 
	echo 'it
	 is admin';
}*/

/*
function inax_get_current_screen() {
	global $current_screen;

	if ( ! isset( $current_screen ) ) {
		return null;
	}

	return $current_screen;
}
echo '<pre>'. print_r( inax_get_current_screen() ,true) . '</pre>';
var_dump( is_user_admin() );*/


$language_code = inax_get_site_lang();
//$language_code = 'en';
//echo "language_code: $language_code ";

if($language_code=='en' || $language_code=='tr' || $language_code=='ar' ){
	/**
	 * Setup language text domain
	 */
	//https://stackoverflow.com/questions/25503480/how-to-use-poedit-with-smarty-templates
	//load_plugin_textdomain('inax', false, basename(dirname(__FILE__)).'/languages');

	load_plugin_textdomain("inax", false, INAX_language_path); //load inax-fa_IR.po && inax-fa_IR.mo
}

//دریافت لینک صفحه اصلی و صفحه تراکنش ها از دیتابیس
$main_link = $trans_link = '';
//['main','trans']
$inax_get_pages = inax_get_pages('', $language_code);//$inax_get_pages used in main.php & inax-pages.php too
//echo '<pre>'. print_r($inax_get_pages,true) . '</pre>';exit;
foreach( $inax_get_pages as $page_lang => $page_rows ){
	foreach($page_rows as $page_row){
		if( isset($page_row['url']) ){
			$page_name = $page_row['page'];
			$page_link = $page_row['url'];
			$page_lang = $page_row['lang'];
	
			//echo "<br/>page_lang: $page_lang - language_code: $language_code - page_name: $page_name<br/>";

			if($page_lang == $language_code){
				if($page_name=='main'){
					$main_link = $page_link;
					$smarty->assign('main_link',$main_link);
				}
				elseif($page_name=='trans'){
					$trans_link = $page_link;
				}
			}
		}
	}
}
//echo "main_link: $main_link<br/>";
//echo "trans_link: $trans_link<br/>";

//ایجاد صفحه تراکنش ها در صورت عدم وجود
if($trans_link==''){
	global $current_user;
	exit('come here to insert trans page');

	$post_title = __("لیست تراکنش ها", "inax" );
	$page_list = array(
		'post_title'  => $post_title,
		'post_status' => 'publish',
		//'post_author' =>  wp_get_current_user()->ID ,
		'post_author' =>  $current_user->ID ,
		'post_type'   => 'page',
		'post_name'   => 'trans',
		'post_content'=> '[inax page="trans"]',
	);

	$post_id = wp_insert_post( $page_list );
}
$smarty->assign('trans_link',$trans_link);

require_once( dirname( __FILE__ ) .'/jdf.php' );

//حالت تست درگاه
if( isset($inax_option['payment_gateway']) && $inax_option['payment_gateway']!='' ){
	$active_gateway = $inax_option['payment_gateway'];
	$inax_payment_gateway 	= get_option('inax_payment_gateway');
	if($inax_payment_gateway!=false){
		$inax_payment_option 	= json_decode($inax_payment_gateway, TRUE);
		if(isset($inax_payment_option[$active_gateway]['test_mode']) && $inax_payment_option[$active_gateway]['test_mode']==1 ){
			$smarty->assign('gateway_test_mode', "حالت تست درگاه {$active_gateway} فعال می باشد" );
		}
	}
}

$disabled_page = isset($inax_option['disabled_page']) ? $inax_option['disabled_page'] : [];//صفحات غیرفعال دیتابیس
$smarty->assign('disabled_page',$disabled_page);

$smarty->assign('test_mode', $test_mode );

/*
require_once( realpath(__DIR__ . '/../../../../') . '/wp-load.php' );
global $current_user;
echo $current_user->ID;*/

//check have wallet and selected by admin for display pay by credit button
$client_id = get_current_user_id();
if( $client_id!=0 && !empty($inax_option['wallet']) && in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')) ) 
	&& ( in_array('woo-wallet/woo-wallet.php', apply_filters('active_plugins', get_option('active_plugins')) ) || in_array('yith-woocommerce-account-funds-premium/init.php', apply_filters('active_plugins', get_option('active_plugins')) ) ) ){
	$smarty->assign('credit_payment', true );

	$user_credit = inax_check_user_credit();
	//در فانکشن بالا به تومان تبدیل می شود نیاز به لاین پایین نیست
	/*if( get_option('woocommerce_currency') == 'IRR' ){
		$user_credit = $user_credit/10;
	}*/
	$smarty->assign('user_credit', $user_credit );
}

$smarty->registerPlugin("modifier","inax_jdate_format", "inax_jdate_format");
$smarty->registerPlugin("modifier","esc_attr", "esc_attr");
$smarty->registerPlugin("modifier","inax_bill_type_fa", "inax_bill_type_fa");

$smarty->registerPlugin("modifier","operator_fa", "operator_fa");
$smarty->registerPlugin("modifier","sprintf", "sprintf");
$smarty->registerPlugin("modifier","inax_internet_type_fa", "inax_internet_type_fa");

//echo "get_locale : ". get_locale();
?>
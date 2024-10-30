<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

wp_enqueue_style('inax_admin_style');//UNSET SOME WPRDPRESS CSS
wp_enqueue_style('inax_bootstrap');
wp_enqueue_style('inax_bootstrap_rewrite');
wp_enqueue_style('inax_font_awesome');
wp_enqueue_style('inax_font_awesome2');
wp_enqueue_script('inax_bootstrap_js');
wp_enqueue_script('inax_js');//number_to_letter()

$language_code 	= inax_get_site_lang();
if($language_code=='fa' || $language_code=='ar'){
	wp_enqueue_style('inax_style_fa');
}

require_once INAX_DIR.'inc'.DIRECTORY_SEPARATOR.'load.php';
global $inax_option,$inaxir;

$screen = get_current_screen();
if($screen->id != $inaxir)
	return;

//Get all classes that extent IX_Payment_Gateway
$active_gateways = array();
foreach( get_declared_classes() as $class ){
	if( is_subclass_of( $class, 'IX_Payment_Gateway' ) ){
		$active_gateways[] = $class;

		$result = call_user_func( array(new $class, 'config') );

		$gt_name = str_replace("INAX_","", $class);
		$gt_elements[$gt_name] = $result;
	}
}
//echo '<pre>'. print_r($gt_elements,true) . '</pre>';exit;

if(isset($gt_elements)){
	//مقادیر دیتابیس
	$inax_payment_gateway 	= get_option('inax_payment_gateway');
	if($inax_payment_gateway!=false){
		$inax_payment_option 	= json_decode($inax_payment_gateway, TRUE);
		//echo '<pre>'. print_r($inax_payment_option,true) . '</pre>';exit;
	}

	foreach ($gt_elements as $gateway => $elements){
		$status     = (isset($inax_payment_option[$gateway]['status']) && $inax_payment_option[$gateway]['status']==1 ) ? 1 : 0;
		$gateway_fa = isset($elements['label']['value']) ? $elements['label']['value'] : 'نامشخص';

		if($status==1){
			$html_elements2['gateway']       = $gateway;
			$html_elements2['gateway_fa']    = $gateway_fa;
			$smarty->append('html_elements', $html_elements2);
		}
	}
}

if(isset($_POST['save_inax_options'])){
	global $inax_option;

	//remove admin notices in first check options
	delete_option('inax_do_activation');
	remove_action('admin_notices', 'inax_admin_message');

	//check_admin_referer('inax_save_options');
	check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

	$wallet 					= (isset($_POST['inax_wallet']) && $_POST['inax_wallet']!='' ) ? sanitize_text_field(trim($_POST['inax_wallet'])) : '';
	$get_inax_amount_limitation = (isset($_POST['inax_amount_limitation']) && $_POST['inax_amount_limitation']!='' ) ? sanitize_text_field(trim($_POST['inax_amount_limitation'])) : 0;
	$get_inax_amount_limitation = str_replace(',', '', $get_inax_amount_limitation);

	if( $wallet!='' && !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')) ) ){
		$error_msg = "افزونه ووکامرس نصب نیست و یا غیرفعال است. برای نصب افزونه بر روی لینک روبرو کلیک کنید <a href='https://wordpress.org/plugins/woocommerce/' target='_blank'>دانلود افزونه ووکامرس</a>";
	}
	elseif( $wallet=='TeraWallet' && !in_array('woo-wallet/woo-wallet.php', apply_filters('active_plugins', get_option('active_plugins')) ) ){
		$error_msg = "افزونه کیف پول TeraWallet ووکامرس نصب نیست و یا غیرفعال است. برای نصب افزونه بر روی لینک روبرو کلیک کنید <a href='https://wordpress.org/plugins/woo-wallet/' target='_blank'>دانلود افزونه کیف پول TeraWallet ووکامرس</a>";
	}
	elseif( $wallet=='yith' && !in_array('yith-woocommerce-account-funds-premium/init.php', apply_filters('active_plugins', get_option('active_plugins')) ) ){
		$error_msg = "افزونه کیف پول yith ووکامرس نصب نیست و یا غیرفعال است.";
	}
	elseif( !empty($get_inax_amount_limitation) && !($get_inax_amount_limitation>500 && $get_inax_amount_limitation<200000)){
		$error_msg = "مبلغ محدودیت خرید باید مابین 500 الی 200,000 تومان باشد";
	}
	else{
		$inax_option = array(
			'username'          => (isset($_POST['inax_username']) && $_POST['inax_username']!='' ) ? sanitize_text_field(trim($_POST['inax_username'])) : '',
			'password'          => (isset($_POST['inax_password']) && $_POST['inax_password']!='' ) ? sanitize_text_field(trim($_POST['inax_password'])) : '',
			'display_error'     => (isset($_POST['inax_display_error']) && $_POST['inax_display_error']!='' ) ? sanitize_text_field(trim($_POST['inax_display_error'])) : '',
			'ajaxless'          => (isset($_POST['inax_ajaxless']) && $_POST['inax_ajaxless']!='' ) ? sanitize_text_field(trim($_POST['inax_ajaxless'])) : '',
			'newtopup'          => (isset($_POST['inax_newtopup']) && $_POST['inax_newtopup']!='' ) ? sanitize_text_field(trim($_POST['inax_newtopup'])) : '',
			'newinternet'  		=> (isset($_POST['inax_newinternet']) && $_POST['inax_newinternet']!='' ) ? sanitize_text_field(trim($_POST['inax_newinternet'])) : '',
			'time_limitation'   => (isset($_POST['inax_time_limitation']) && $_POST['inax_time_limitation']!='' ) ? sanitize_text_field(trim($_POST['inax_time_limitation'])) : '',
			'amount_limitation' => $get_inax_amount_limitation,
			'wallet'            => $wallet,
			'theme'             => (isset($_POST['inax_theme']) && $_POST['inax_theme']!='' ) ? sanitize_text_field(trim($_POST['inax_theme'])) : 'default',
			'payment_gateway'   => isset($_POST['inax_payment']) ? sanitize_text_field(trim($_POST['inax_payment'])) : '',
		);

		//print_r($inax_option);
		update_option('inax_options', json_encode($inax_option)) OR add_option('inax_options', json_encode($inax_option));

		/*if( isset($_POST['field']) ){
			$field = $_POST['field'];
			foreach($_POST['field'] as $gt => $values){
				//print_r($values);
				$field[$gt]['status'] = isset($values['status']) ? 1 : 0;
			}

			update_option('inax_payment_gateway', json_encode($field)) OR add_option('inax_options', json_encode($field));
		}*/

		//$payment_option = array();
		//update_option('inax_payment_gateway', json_encode($payment_option)) OR add_option('inax_options', json_encode($payment_option));
		$success_msg = "با موفقیت ذخیره گردید.";
	}
}

$inax_option['inax_plugin_version'] = inax_get_plugin_version();
$inax_option['theme']               = $inax_theme;//from load.php
$inax_option['time_limitation']     = $inax_time_limitation;
$inax_option['amount_limitation']   = $inax_amount_limitation;
$smarty->assign('inax_option', $inax_option);

if( isset($error_msg) ){
	$smarty->assign('error_msg', $error_msg);
}
if( isset($success_msg) ){
	$smarty->assign('success_msg', $success_msg);
}
$smarty->display(dirname(__FILE__) . '/templates/admin/main.tpl');
?>
<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

//load_plugin_textdomain('inax', false, basename(dirname(__FILE__)).'/languages');
//echo __( 'Please Select Operator', "inax" );

//add css and js to this file
wp_enqueue_style('inax_bootstrap');
wp_enqueue_style('inax_bootstrap_rewrite');
wp_enqueue_style('inax_bootstrap_themes');
wp_enqueue_style('inax_style');
wp_enqueue_style('inax_font_awesome');
wp_enqueue_style('inax_font_awesome2');
wp_enqueue_script('inax_js');
if($language_code=='fa' || $language_code=='ar'){
	wp_enqueue_style('inax_style_fa');
}

$res = $wpdb->query("SHOW TABLES LIKE '$inax_charge_db' ");
if(!$res){
	inax_install();//install database table again
	$head_error = "مشکلی در اتصال به پایگاه داده وجود دارد - تیبل مورد نظر یافت نشد. لطفا صفحه را رفرش نمائید در صورتی که مشکل برطرف نشد یک تیکت به <a href='https://inax.ir/panel/submit_ticket.php' target='_blank'>پشتیبانی آینکس</a> ارسال نمائید";
}else{

	$smarty->assign('buy_pin',true);
	$smarty->assign('title', __( 'خرید شارژ پین', "inax" ) );
	if(isset($_GET['MTN'])){
		$operator = 'MTN';
		$smarty->assign('mtn_active',true);
	}
	elseif(isset($_GET['MCI'])){
		$operator = 'MCI';
		$smarty->assign('mci_active',true);
	}
	elseif(isset($_GET['RTL'])){
		$operator = 'RTL';
		$smarty->assign('rtl_active',true);
	}

	if( ( isset($_POST['submit']) || isset($_POST['submit_credit']) ) && ( isset($_GET['MTN']) || isset($_GET['MCI']) || isset($_GET['RTL']) ) ){
		if( !isset($_POST['Token']) || !wp_verify_nonce($_POST['Token'], 'name_of_my_action') ){
			print 'Sorry, your nonce did not verify.';
			exit;
		}

		check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

		$mobile         = (isset($_POST['mobile']) && $_POST['mobile'] != '') ? sanitize_text_field(trim($_POST['mobile'])) : '';
		$amount         = (isset($_POST['amount']) && $_POST['amount'] != '') ? sanitize_text_field(trim($_POST['amount'])) : '';

		$mobile = inax_convert_fa_to_en($mobile);
		$mobile = preg_replace('/\s+/', '', $mobile);//remove all space - 0902 111 22

		$valid_amount = array(1000,2000,5000,10000,20000);

		if($mobile == ""){
			$error_msg = 'شماره موبایل را وارد کنید !';
		}
		elseif($amount == ""){
			$error_msg = 'مبلغ خالی است !';
		}
		elseif(!$validate->Mobile($mobile)){
			$error_msg = 'شماره موبایل صحیح نیست !';
		}
		elseif(!$validate->Number($amount)){
			$error_msg = 'مبلغ ارسالی صحیح نیست !';
		}
		elseif( !in_array($amount, $valid_amount)){
			$error_msg = 'مبلغ شارژ صحیح نیست !';
		}
		elseif( isset($_POST['submit_credit']) && $user_credit<$amount ){
			$error_msg = "اعتبار شما ({$user_credit}) تومان جهت پرداخت مبلغ {$amount} کافی نیست";
		}
		else{
			$date = date('Y-m-d H:i:s');

			$count = 1;

			$mode='';
			if($test_mode){
				$mode='test_mode';
			}
			$mode    = !empty($mode) ? "'$mode'" : "NULL";

			$payment_type = 'online';
			if( isset($_POST['submit_credit']) && $user_credit>=$amount ){
				$payment_type = 'credit';
			}

			$sql = $wpdb->query("INSERT INTO $inax_charge_db (client_id,type, mobile, operator, amount, payment_type, date,mode) VALUES ('$client_id', 'pin', '$mobile', '$operator', '$amount', '$payment_type', '$date', $mode) " );
			if( !$sql ){
				$error_msg = 'خطا در ذخیره اطلاعات در دیتابیس ...<br/>' . $wpdb->last_error;
			}
			else{
				$tr_id          		= $wpdb->insert_id;
				//$_SESSION['pin_id'] 	= $tr_id;
				$_GET['id']     		= $tr_id;
				require_once INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'buy.php';
			}
		}
	} //-->submit
}

if(isset($head_error)){$smarty->assign('head_error',$head_error);}
if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . "/templates/client/$inax_theme/pin.tpl");
?>
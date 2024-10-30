<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

wp_enqueue_style('inax_admin_style');//UNSET SOME WPRDPRESS CSS
wp_enqueue_style('inax_bootstrap');
wp_enqueue_style('inax_bootstrap_rewrite');
wp_enqueue_style('inax_font_awesome');
wp_enqueue_style('inax_font_awesome2');
$language_code 	= inax_get_site_lang();
if($language_code=='fa' || $language_code=='ar'){
	wp_enqueue_style('inax_style_fa');
}

require_once INAX_DIR.'inc'.DIRECTORY_SEPARATOR.'load.php';

if( $inax_option['username']=='' || $inax_option['password']=='' ){
	$have_err = "لطفا ابتدا نام کاربری و پسورد وب سرویس آینکس را از بخش <a href='admin.php?page=inaxir'>تنظیمات</a> وارد نمائید.";
	$smarty->assign('have_err',$have_err);
}else{

	$result = inax_request_json('credit',[]);
	if( !$result['status'] ){
		$have_err = "خطا - {$result['msg']}";
		$smarty->assign('have_err',$have_err);
	}
	else{
		$data = $result['data'];
		if( $data['code']!=1 ){
			$have_err = $data['msg'];
			$smarty->assign('have_err',$have_err);
		}else{
			$credit = $data['credit'];
			$smarty->assign('credit',$credit);
		}
	}

	if(isset($_POST['submit'])){
		check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

		$amount = sanitize_text_field(trim($_POST['amount']));
		$amount = str_replace(",","",$amount);
		if($amount=='' || $amount<100 || $amount>10000000 || !validate_amount($amount)){
			$error_msg 	= "مبلغ صحیح نیست. لطفا مبلغ را به صورت عددی و بزرگتر از 100 وارد نمائید";
		}else{
			$param = array(
				'amount'    => $amount,
				'callback'  => admin_url( 'admin.php?page=inax_credit', 'admin' ),
			);

			$result = inax_request_json('addfund',$param);
			if( !$result['status'] ){
				$error_msg = "خطا - {$result['msg']}";
			}
			else{
				$data = $result['data'];
				if( $data['code'] != 1 ){
					$error_msg = $data['msg'];
				}
				else{
					$trans_id   = $data['trans_id'];
					$url        = "https://inax.ir/pay.php?tid={$trans_id}";
					inax_header($url);
					exit;
				}
			}
		}
	}
}
if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . '/templates/admin/credit.tpl');
?>
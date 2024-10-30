<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

//add css and js to this file
wp_enqueue_style('inax_bootstrap');
wp_enqueue_style('inax_bootstrap_rewrite');
//wp_enqueue_style('inax_bootstrap_themes');
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

	//شمارهه های ذخیره شده کاربر
	if( !empty($client_id) ){
		$saved_mobile_rows = $wpdb->get_results("SELECT mobile FROM $inax_charge_db where client_id='$client_id' and save_mobile='1' and mobile!='' and mobile is not null group by mobile ORDER BY id DESC ", ARRAY_A);
		foreach($saved_mobile_rows as $saved_mobile){
			$smarty->append('saved_mobile_rows', $saved_mobile);
		}
	}

	$smarty->assign('buy_charge', true);
	$smarty->assign('title', __('خرید شارژ مستقیم', "inax" ) );

	$operator ='';
	if( isset($_GET['MTN']) ){
		$operator = 'MTN';
		$smarty->assign('mtn_active', true);
	}
	elseif( isset($_GET['MCI']) ){
		$operator = 'MCI';
		$smarty->assign('mci_active', true);
	}
	elseif( isset($_GET['RTL']) ){
		$operator = 'RTL';
		$smarty->assign('rtl_active', true);
	}
	elseif( isset($_GET['SHT']) ){
		$operator = 'SHT';
		$smarty->assign('sht_active', true);
	}
	$smarty->assign('operator',$operator);

	if( ( isset($_POST['submit']) || isset($_POST['submit_credit']) ) && (isset($_GET['MTN']) || isset($_GET['MCI']) || isset($_GET['RTL']) || isset($_GET['SHT'])) ){
		if( !isset($_POST['Token']) || !wp_verify_nonce($_POST['Token'], 'name_of_my_action') ){
			print 'Sorry, your nonce did not verify.';
			exit;
		}

		check_admin_referer('name_of_my_action', 'Token');//wordpress

		$mobile         = (isset($_POST['mobile']) && $_POST['mobile'] != '') ? sanitize_text_field(trim($_POST['mobile'])) : '';
		$amount         = (isset($_POST['amount']) && $_POST['amount'] != '') ? sanitize_text_field(trim($_POST['amount'])) : '';
		$charge_type    = (isset($_POST['charge_type']) && $_POST['charge_type'] != '') ? sanitize_text_field($_POST['charge_type']) : '';
		$save_mobile    = (isset($_POST['save_mobile']) && $_POST['save_mobile']==1) ? "'1'" : "NULL";

		$mnp        = (isset($_POST['mnp']) && $_POST['mnp']==1) ? 1 : '';

		$mobile = inax_convert_fa_to_en($mobile);
		$mobile = preg_replace('/\s+/', '', $mobile);//remove all space - 0902 111 22

		if( $amount == 'custom_amount' ){
			$amount = sanitize_text_field($_POST['custom_amount']);
			$amount = str_replace(',', '', $amount);
			$custom_amount = true;
			$smarty->assign('display_custom_amount_field', true);
		}

		$valid_amount = array(500, 1000, 2000, 5000, 10000, 20000, 30000, 40000, 50000);

		if($operator=='SHT'){
			//افزودن 9 درصد مالیات به مبلغ شارژ
			$amount = $amount + (($amount * 9) /100);
			foreach($valid_amount as $key => $amnt){//افزودن مالیات به ارایه مبالغ مجاز
				$valid_amount[$key] = $amnt + (($amnt * 9) /100);
			}
		}

		if( $mobile == "" ){
			$error_msg = 'شماره موبایل را وارد کنید !';
		}
		elseif( $amount == "" ){
			$error_msg = 'مبلغ خالی است !';
		}
		elseif( !$validate->Mobile($mobile) ){
			$error_msg = 'شماره موبایل صحیح نیست !';
		}
		elseif( !$validate->Number($amount) ){
			$error_msg = 'مبلغ ارسالی صحیح نیست !';
		}
		elseif( !isset($custom_amount) && !in_array($amount, $valid_amount) ){
			$error_msg = 'مبلغ شارژ صحیح نیست !';
		}
		elseif( isset($custom_amount) && ($amount < 500 || $amount > 200000) ){//custom amount
			$error_msg = 'مبلغ شارژ باید مابین 500 الی 200,000 تومان باشد';
		}
		elseif( $charge_type == "" ){
			$error_msg = 'لطفا نوع شارژ را انتخاب کنید';
		}
		elseif( $charge_type != "normal" && $charge_type != "amazing" && $charge_type != "permanent" ){
			$error_msg = 'نوع شارژ صحیح نیست';
		}
		elseif( op_number_check($operator,$mobile,$mnp)['result']==false ){
			$error_msg = "خطا : " . op_number_check($operator,$mobile,$mnp)['msg'];
		}
		elseif( isset($_POST['submit_credit']) && $user_credit<$amount ){
			$error_msg = "اعتبار شما ({$user_credit}) تومان جهت پرداخت مبلغ {$amount} کافی نیست";
		}
		elseif( $inax_amount_limitation!=0 && ($amount>$inax_amount_limitation || (isset($custom_amount) && $custom_amount>$inax_amount_limitation ) ) ){
			$error_msg = "مبلغ خرید (" . number_format($amount) . " تومان)، بیشتر از مبلغ قابل قبول خرید (" . number_format($inax_amount_limitation) . " تومان) است. لطفا با مبلغ کمتر امتحان نمائید";
		}
		else{
			//بررسی فعال بودن محدودیت خرید
			$can_buy=true;
			if( !empty($inax_time_limitation) ){
				$res1 = $wpdb->get_row("SELECT id,date FROM $inax_charge_db where client_id='$client_id' and mobile='$mobile' and status='paid' order by id desc limit 1", ARRAY_A);
				if( !empty($res1['date']) ){
					$date = $res1['date'];
					$dead_date = date('H:i:s', strtotime($date. " +$inax_time_limitation minutes"));
					if( strtotime($date) > strtotime("-$inax_time_limitation minutes") ){
						$can_buy = false;
						$error_msg = "به علت فعال بودن محدودیت زمانی، شما هر $inax_time_limitation دقیقه می توانید یک تراکنش خرید ایجاد نمائید. (زمان رفع محدودیت : $dead_date)";
					}
				}
			}

			if($can_buy){
				$date = date('Y-m-d H:i:s');

				//$mnp='';
				$sim_type   = 'credit';
				if($charge_type=='permanent'){
					$sim_type='permanent';
				}
				/*elseif($charge_type=='mnp'){
					$mnp=1;
				}*/
	
				$mnp    = !empty($mnp) ? "'$mnp'" : "NULL";
	
				$mode='';
				if($test_mode){
					$mode='test_mode';
				}
				$mode    = !empty($mode) ? "'$mode'" : "NULL";
	
				$payment_type = 'online';
				if( isset($_POST['submit_credit']) && $user_credit>=$amount ){
					$payment_type = 'credit';
				}
	
				$sql = $wpdb->query("INSERT INTO $inax_charge_db (client_id, type, mobile, mnp, operator, sim_type, charge_type, amount, payment_type, save_mobile, date ,mode) VALUES ('$client_id', 'topup', '$mobile', $mnp, '$operator', '$sim_type', '$charge_type', '$amount', '$payment_type', $save_mobile, '$date', $mode) ");
				if( !$sql ){
					$error_msg = 'خطا در ذخیره اطلاعات در دیتابیس ...<br/>' . $wpdb->last_error;
				}
				else{
					$tr_id          		= $wpdb->insert_id;
					//$_SESSION['topup_id'] 	= $tr_id;
					$_GET['id']     		= $tr_id;
					require_once INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'buy.php';
				}
			}
		}
	} //-->submit

}

if(isset($head_error)){$smarty->assign('head_error',$head_error);}
if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . "/templates/client/$inax_theme/topup.tpl");
?>
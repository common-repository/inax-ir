<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");
$ajaxless = isset($inax_option['ajaxless']) ? $inax_option['ajaxless'] : '';
$smarty->assign('ajaxless',$ajaxless);

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

if(!$ajaxless){
	//use ajax
	//wp_enqueue_script('inax_jquery');
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('inax_bootstrap_js');
	wp_enqueue_script( 'my_check_bill_ajax' );
}

$res = $wpdb->query("SHOW TABLES LIKE '$inax_charge_db' ");
if(!$res){
	inax_install();//install database table again
	$head_error = "مشکلی در اتصال به پایگاه داده وجود دارد - تیبل مورد نظر یافت نشد. لطفا صفحه را رفرش نمائید در صورتی که مشکل برطرف نشد یک تیکت به <a href='https://inax.ir/panel/submit_ticket.php' target='_blank'>پشتیبانی آینکس</a> ارسال نمائید";
}else{

	$smarty->assign('pay_bill',true);
	$smarty->assign('title', __('پرداخت قبض', "inax" ));

	//get params sent from inquiry_bill.php
	if( isset($_GET['bill_id']) && $_GET['bill_id']!='' && isset($_GET['pay_id']) && $_GET['pay_id']!='' ){
		$bill_id    = sanitize_textarea_field($_GET['bill_id']);
		$pay_id     = sanitize_textarea_field($_GET['pay_id']);
		$smarty->assign('bill_id',$bill_id);
		$smarty->assign('pay_id',$pay_id);
	}

	//check if the headers have not been sent yet. Then you know it's safe to modify them.
	/*if(!headers_sent()){
		foreach (headers_list() as $header)
			header_remove($header);
	}*/

	//ajaxless
	if(isset($_POST['check_bill']) ){
		if( !isset($_POST['Token']) || !wp_verify_nonce($_POST['Token'], 'name_of_my_action') ){
			$error_msg = "سشن منقضی شده است. لطفا صفحه را رفرش نمائید";
		}
		check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

		$bill_id    = (isset($_POST['bill_id']) && $_POST['bill_id']!='' ) ? sanitize_text_field(trim($_POST['bill_id'])) : '';
		$pay_id     = (isset($_POST['pay_id']) && $_POST['pay_id']!='' ) ? sanitize_text_field(trim($_POST['pay_id'])) : '';
		$mobile     = (isset($_POST['mobile']) && $_POST['mobile']!='' ) ? sanitize_text_field(trim($_POST['mobile'])) : '';

		$bill_id    = inax_convert_fa_to_en($bill_id);
		$bill_id    = preg_replace('/\s+/', '', $bill_id);

		$pay_id     = inax_convert_fa_to_en($pay_id);
		$pay_id     = preg_replace('/\s+/', '', $pay_id);

		$mobile     = inax_convert_fa_to_en($mobile);
		$mobile     = preg_replace('/\s+/', '', $mobile);//remove all space - 0902 111 22

		$bill_id    =  ltrim($bill_id,"0");//remove first zero
		$pay_id     =  ltrim($pay_id,"0");//remove first zero

		if($bill_id == ""){
			$error_msg = 'لطفا شناسه قبض را وارد کنید !';
		}
		elseif($pay_id == ""){
			$error_msg = 'لطفا شناسه پرداخت را وارد کنید !';
		}
		elseif($mobile == ""){
			$error_msg = 'شماره موبایل را وارد کنید !';
		}
		elseif(!$validate->Number($bill_id)){
			$error_msg = 'شناسه قبض صحیح نیست !';
		}
		elseif(!$validate->Number($pay_id)){
			$error_msg = 'شناسه پرداخت صحیح نیست !';
		}
		elseif(!$validate->Mobile($mobile)){
			$error_msg = 'شماره موبایل صحیح نیست !';
		}
		else{
			$param = array(
				'bill_id'		=> $bill_id,
				'pay_id'		=> $pay_id,
			);

			$result = inax_request_json('check_bill',$param,'inax');
			//echo '<pre>'. print_r($result,true) . '</pre>';exit;
			if( !$result['status'] ){
				$error_msg = "خطا - {$result['msg']}";
			}
			else{
				$data = $result['data'];
				if( $data['code'] != 1 ){
					$error_msg = $data['msg'];
				}
				else{
					$type_en 		= $data['type_en'];
					$amount 		= $data['amount'];
					$payment_type 	= $data['pay_type'];//تشخیص نوع پرداخت از پارامتر آینکس
					$amount_rial 	= $amount*10; // تبدیل به ریال
					$bill_type_name = inax_bill_type_fa($type_en);

					//echo $payment_type;exit;

					$pay_type_fa = '';
					if( $payment_type=='credit' ){//اگر نوع پرداخت از سمت آینکس اعتباری برگشت داده شود نماینده اعتبار کافی جهت پرداخت دارد
						$pay_type_fa = 'پرداخت اعتباری';
						//بررسی لاگین بودن و داشتن اعتبار خریدار برای اجازه پرداخت اعتباری
						if( get_current_user_id()==0){
							$payment_type = 'online';
							$pay_type_fa = 'پرداخت آنلاین(کاربر مهمان)';
						}else{//بررسی اعتبار خریدار
							$credit = inax_check_user_credit();//return as toman
							if( $credit < $amount ){//تغییر روش پرداخت به آنلاین به علت کافی نبودن اعتبار
								$payment_type='online';
								$pay_type_fa = 'پرداخت آنلاین(به علت کافی نبودن موجودی کیف پول خریدار)';
							}
						}
					}
					//تشخیص نوع پرداخت برجسب موجودی کاربر
					/*if( get_current_user_id()==0){
						$payment_type = 'online';
						$pay_type_fa = 'پرداخت آنلاین(کاربر مهمان)';
					}else{
						$credit = inax_check_user_credit();//return as toman
						if( $credit < $amount ){
							$payment_type='online';
							$pay_type_fa = 'پرداخت آنلاین(به علت کافی نبودن موجودی)';
						}else{
							$payment_type='credit';
							$pay_type_fa = 'پرداخت از اعتبار';
						}
					}*/
					//$payment_type = '';//payment_type define in buy.php

					$check_bill_result = json_encode($result,JSON_UNESCAPED_UNICODE);
					//$check_bill_result='';

					$date 	= date('Y-m-d H:i:s');
					$client_id = get_current_user_id();

					$mode='';
					if($test_mode){
						$mode='test_mode';
					}

					$mode           = !empty($mode) ? "'$mode'" : "NULL";
					$payment_type   = !empty($payment_type) ? "'$payment_type'" : "NULL";

					$db = "INSERT INTO $inax_charge_db (type,client_id, bill_id, pay_id,bill_type, amount,mobile,check_bill_result,payment_type, date ,mode) VALUES ('bill', '$client_id', '$bill_id', '$pay_id', '$type_en', '$amount', '$mobile', '$check_bill_result', $payment_type, '$date' , $mode)";
					//echo $db;
					$sql = $wpdb->query($db);
					if(!$sql){
						$error_msg = 'خطا در ذخیره اطلاعات در دیتابیس ...<br/>' . $wpdb->last_error;
					}else{
						$db_id 					= $wpdb->insert_id;
						//$_SESSION['bill_id'] 	= $db_id;
						//echo $db_id;exit;

						$bill_details['bill_type'] 		= $type_en;
						$bill_details['bill_type_name'] = $bill_type_name;
						$bill_details['pay_type_fa']    = $pay_type_fa;
						$bill_details['amount']         = $amount_rial;
						$bill_details['db_id']          = $db_id;

						$smarty->append('bill_details',$bill_details);
					}
				}
			}
		}
	}

	if(isset($_POST['pay_submit']) ){
		if( !isset($_POST['Token']) || !wp_verify_nonce($_POST['Token'], 'name_of_my_action') ){
			print 'Sorry, your nonce did not verify.';
			exit;
		}

		check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

		if( !isset($_POST['bill_dbid']) || empty($_POST['bill_dbid']) || $_POST['bill_dbid']=='' ){
			$error_msg = 'شناسه پرداخت موجود نیست';
		}
		else{
			$tr_id 		= sanitize_text_field(intval($_POST['bill_dbid']));

			$bill_row = $wpdb->get_row("SELECT * FROM $inax_charge_db WHERE id='$tr_id' and client_id='$client_id' ", ARRAY_A);
			if($bill_row==null){
				$error_msg = 'چنین صورتحسابی یافت نشد';
			}
			else{
				$bill_id 	    = $bill_row['bill_id'];
				$pay_id 	    = $bill_row['pay_id'];
				$amount 	    = $bill_row['amount'];//toman
				$mobile 	    = $bill_row['mobile'];
				//$pay_type 	= $bill_row['payment_type'];

				$_GET['id']     = $tr_id;
				require_once INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'buy.php';
			}
		}
	}
}

if(isset($head_error)){$smarty->assign('head_error',$head_error);}
if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . "/templates/client/$inax_theme/bill.tpl");
?>		
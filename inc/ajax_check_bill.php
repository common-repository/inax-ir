<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

if( isset($_POST['action']) && $_POST['action']=='check_bill' ){
	//اگر پروتکل سایت https است باید از همین روش برای ارسال داده ارسال شود در غیراینصورت خطای سشن میدهد
	//echo '<pre>'. print_r($_POST,true) . '</pre>';exit;
	if( !isset($_POST['Token']) || !wp_verify_nonce($_POST['Token'], 'name_of_my_action') ){
		echo $res = json_encode( array( "error_msg"=>"سشن منقضی شده است... لطفا صفحه را رفرش نمائید" ) );
		exit;
	}
	check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

	require_once( dirname( __FILE__ ) .'/validation.php' );
	require_once INAX_DIR.'inc/inax-functions.php';
	$validate = new SimaNet_Validate;

	$inax_charge_db   = $wpdb->prefix . 'inax_charge';//wpdb globaled in inax_check_ajax_function

	$bill_id    = (isset($_POST['bill_id']) && $_POST['bill_id']!='' ) ? sanitize_text_field(trim($_POST['bill_id'])) : '';
	$pay_id     = (isset($_POST['pay_id']) && $_POST['pay_id']!='' ) ? sanitize_text_field(trim($_POST['pay_id'])) : '';
	$mobile     = (isset($_POST['mobile']) && $_POST['mobile']!='' ) ? sanitize_text_field(trim($_POST['mobile'])) : '';
	$test_mode  = (isset($_POST['mode']) && $_POST['mode']=='test_mode') ? true : false;

	$bill_id    = inax_convert_fa_to_en($bill_id);
	$bill_id    = preg_replace('/\s+/', '', $bill_id);

	$pay_id     = inax_convert_fa_to_en($pay_id);
	$pay_id     = preg_replace('/\s+/', '', $pay_id);

	$mobile     = inax_convert_fa_to_en($mobile);
	$mobile     = preg_replace('/\s+/', '', $mobile);//remove all space - 0902 111 22

	$bill_id    =  ltrim($bill_id,"0");
	$pay_id     =  ltrim($pay_id,"0");

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
		$result = inax_request_json('check_bill',$param);
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

				//error_log( print_r($data, true) );

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
				//$pay_type_fa = '';
				//$payment_type = '';//payment_type define in buy.php

				/*
				 تشخیص نوع پرداخت از پارامتر آینکس
				if($payment_type=='online'){
					$pay_type_fa = 'پرداخت آنلاین';
				}elseif($payment_type=='credit'){
					if( get_current_user_id()==0){
						$payment_type = 'online';
						$pay_type_fa = 'پرداخت آنلاین(کاربر مهمان)';
					}else{
						$pay_type_fa = 'پرداخت از اعتبار';
						$credit = inax_check_user_credit();
						if( $credit < $amount ){
							$payment_type = 'online';
							$pay_type_fa = 'پرداخت آنلاین(به علت کافی نبودن موجودی)';
						}
					}
				}*/

				//$check_bill_result['code'] 	= $result['code'];
				//$check_bill_result['msg'] 	= $result['msg'];

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

				$sql = $wpdb->query( "INSERT INTO $inax_charge_db (type, client_id, bill_id, pay_id,bill_type, amount,mobile,check_bill_result,payment_type, date, mode ) VALUES ('bill', '$client_id', '$bill_id', '$pay_id', '$type_en', '$amount', '$mobile', '$check_bill_result', $payment_type, '$date', $mode )");
				if( !$sql ){
					$error_msg = 'خطا در ذخیره اطلاعات در دیتابیس ...<br/>' . $wpdb->last_error;
				}else{
					$db_id 					= $wpdb->insert_id;
					//$_SESSION['bill_id'] 	= $db_id;

					$error_msg = 'no';
				}
			}
		}
	}
	
	if($error_msg =='no'){
		if($language_code!='fa'){
			$bill_type_name = $type_en;
		}

		$res = json_encode( array( 
			"error_msg"		=> $error_msg, 
			"bill_dbid"		=> $db_id, 
			"type"			=> $bill_type_name, 
			"pay_type_fa"	=> $pay_type_fa, 
			"amount" 		=> number_format($amount_rial) 
		) );
	}else{
		$res = json_encode( array( "error_msg"=>$error_msg ) );
	}
	echo $res;
	
}
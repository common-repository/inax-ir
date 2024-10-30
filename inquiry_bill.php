<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

//add css and js to this file
wp_enqueue_style('inax_bootstrap');
wp_enqueue_style('inax_bootstrap_rewrite');
wp_enqueue_style('inax_bootstrap_themes');
wp_enqueue_style('inax_style');
wp_enqueue_style('inax_font_awesome');
wp_enqueue_style('inax_font_awesome2');

//wp_enqueue_script('inax_jquery');
//wp_enqueue_script( 'jquery' );
//wp_enqueue_script('inax_bootstrap_js');

$res = $wpdb->query("SHOW TABLES LIKE '$inax_charge_db' ");
if(!$res){
	inax_install();//install database table again
	$head_error = "مشکلی در اتصال به پایگاه داده وجود دارد - تیبل مورد نظر یافت نشد. لطفا صفحه را رفرش نمائید در صورتی که مشکل برطرف نشد یک تیکت به <a href='https://inax.ir/panel/submit_ticket.php' target='_blank'>پشتیبانی آینکس</a> ارسال نمائید";
}else{
	$inax_get_pages = inax_get_pages('bill', $language_code);
	//echo '<pre>'. print_r($inax_get_pages,true) . '</pre>';exit;
	if( !isset($inax_get_pages['url']) || $inax_get_pages['status']=='disable' ){
		$head_error = "صفحه مربوط به پرداخت قبض ساخته نشده یا فعال نیست";
	}
	else{
		$bill_page = $inax_get_pages['url'];

		$query 			= parse_url($trans_link, PHP_URL_QUERY);//بررسی وجود کوئری
		if($query){
			$bill_page .= "&";
		}else {
			$bill_page .= "?";
		}

		//$bill_page 	= rtrim($bill_page,"/");
		$smarty->assign('bill_page',$bill_page);

		$smarty->assign('check_inquiry_bill',true);
		$smarty->assign('title', __('استعلام بدهی قبوض', "inax" ) );

		if(isset($_POST['submit_inquiry']) ){//استعلام قبض
			if( !isset($_POST['Token']) || !wp_verify_nonce($_POST['Token'], 'name_of_my_action') ){
				$error_msg = "سشن منقضی شده است ! لطفا صفحه را رفرش نمائید";
			}
			check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

			$bill_type 	    = isset($_POST['bill_type']) ? sanitize_text_field($_POST['bill_type']) : '';
			$input_value    = (isset($_POST['input_value']) && $_POST['input_value']!='' ) ? sanitize_text_field($_POST['input_value']) : '';
			$operator       = (isset($_POST['operator']) && $_POST['operator']!='' ) ? sanitize_text_field(trim($_POST['operator'])) : '';
			$period         = (isset($_POST['period']) && $_POST['period']!='' ) ? sanitize_text_field(trim($_POST['period'])) : '';
			$mobile         = (isset($_POST['mobile']) && $_POST['mobile']!='' ) ? sanitize_text_field(trim($_POST['mobile'])) : '';

			/*$credit = 0;
			if( get_current_user_id()==0){
				$payment_type = 'online';
				//$pay_type_fa = 'پرداخت آنلاین(کاربر مهمان)';
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

			if( $bill_type=="" ){
				$error_msg = "لطفا نوع قبض را انتخاب نمائید";
			}
			elseif( $bill_type!='mobile' && $input_value=="" ){
				$error_msg = "لطفا مقدار فیلد مربوطه را تکمیل نمائید";
			}
			elseif( $bill_type!='mobile' && $bill_type!='elec' && $bill_type!='gas' && $bill_type!='water' && $bill_type!='phone'){
				$error_msg = "نوع قبض صحیح نیست";
			}
			elseif( $bill_type!='mobile' & !$validate->Number($input_value)){
				$error_msg = 'فرمت فیلد مربوطه صحیح نیست';
			}
			elseif( $bill_type!='mobile' && strlen($input_value)<4 || strlen($input_value)>30 ){
				$error_msg = "طول فیلد معتبر نیست";
			}
			elseif( !$validate->Mobile($mobile) ){
				$error_msg = "شماره موبایل صحیح نیست";
			}
			elseif( $bill_type=='mobile' && $operator!='MTN' && $operator!='MCI' && $operator!='RTL' ){
				$error_msg = "اپراتور صحیح نیست";
			}
			elseif( $bill_type=='mobile' && $period!='mid' && $period!='final' ){
				$error_msg = "دوره زمانی قبض صحیح نیست";
			}
			/*elseif( $credit < $bill_inquiry_fee ){
				$error_msg = "کمبود موجودی - هزینه استعلام قبض جدید $bill_inquiry_fee تومان است";
			}*/
			else{
				$param['bill_type']    = $bill_type;
				$param['test_mode']    = $test_mode;

				if( $bill_type=='mobile'){
					//$mobile             = $input_value;
					$param['mobile']    = $mobile;
					$param['operator']  = $operator;
					$param['period']    = $period;
				}
				elseif( $bill_type=='phone'){
					//$phone = $input_value;
					$param['phone']    = $input_value;
				}
				elseif( $bill_type=='elec' || $bill_type=='water' ){
					//$bill_id = $input_value;
					$param['bill_id']    = $input_value;
				}
				elseif( $bill_type=='gas' ){
					//$participate_code = $input_value;
					$param['participate_code']    = $input_value;
				}
				//echo '<pre>'. print_r($param,true) . '</pre>';exit;

				//inquiry_bill
				$result = inax_request_json('inquiry_bill',$param,'inax');
				//echo '<pre>inquiry_bill '. print_r($result,true) . '</pre>';exit;
				if( !$result['status'] ){
					$error_msg = "خطا - {$result['msg']}";
				}
				else{
					$data = $result['data'];
					if( $data['code'] != 1 ){
						$error_msg = $data['msg'];
					}
					else{
						$bill_id 		= $data['bill_id'];
						$pay_id 		= $data['pay_id'];
						$amount 		= $data['amount'];

						$bill_id    =  ltrim($bill_id,"0");//remove first zero
						$pay_id     =  ltrim($pay_id,"0");//remove first zero

						$bill_type_name = inax_bill_type_fa($bill_type);

						//تشخیص نوع پرداخت برجسب موجودی کاربر
						if( get_current_user_id()==0 ){
							$payment_type = 'online';
							$pay_type_fa = 'پرداخت آنلاین(کاربر مهمان)';
						}else{
							$credit = inax_check_user_credit();//return as toman
							if( $credit > $amount ){
								$payment_type   = 'credit';
								$pay_type_fa    = 'پرداخت از اعتبار';
							}
							else{
								$payment_type   = 'online';
								$pay_type_fa    = 'پرداخت آنلاین(به علت کافی نبودن موجودی)';
							}
						}

						$check_bill_result = json_encode($result,JSON_UNESCAPED_UNICODE);

						$date 	    = date('Y-m-d H:i:s');
						$client_id  = get_current_user_id();

						$sql = $wpdb->query("INSERT INTO $inax_charge_db (type, client_id, bill_id, pay_id,bill_type, amount,mobile,check_bill_result,payment_type, date ) VALUES ('bill', '$client_id', '$bill_id', '$pay_id', '$bill_type', '$amount', '$mobile', '$check_bill_result','$payment_type', '$date' )");
						if(!$sql){
							$error_msg = 'خطا در ذخیره اطلاعات در دیتابیس ...<br/>' . $wpdb->last_error;
						}else{
							$db_id = $wpdb->insert_id;

							$bill_details['bill_type']      = $bill_type;
							$bill_details['bill_id']        = $bill_id;
							$bill_details['pay_id']         = $pay_id;
							$bill_details['amount']         = $amount;
							$bill_details['payment_type']   = $payment_type;
							$bill_details['db_id']          = $db_id;
							$smarty->append('bill_details',$bill_details);
						}
					}
				}
			}
		}
	}
}

if(isset($head_error)){$smarty->assign('head_error',$head_error);}
if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . "/templates/client/$inax_theme/inquiry_bill.tpl");
?>
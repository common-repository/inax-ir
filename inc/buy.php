<?php
//if( !isset($_GET['id']) || $_GET['id']=='' || !preg_match("/^([0-9 -])+$/",$_GET['id']) ){//only integer and comma
if( !isset($_GET['id']) || $_GET['id']=='' || !preg_match("/^([0-9])+$/",$_GET['id']) ){//only integer
	$error_msg = 'آیدی تراکنش تنظیم نشده است';
}
else{
	$tr_id          = sanitize_textarea_field($_GET['id']);

	$sql_table = $wpdb->prefix . 'inax_charge';

	/*if(strpos($tr_id, "-") !== false){//bulk bill trans
		$exp = explode("-",$tr_id);

		$imp = implode("','", $exp );
		$cond = "id in ('$imp')";
	}else{
		$cond = "id='$tr_id' limit 1";
	}
	
	$bill_row = $wpdb->get_results("SELECT * FROM $sql_table WHERE $cond ", ARRAY_A);
	if( empty($bill_row) ){
		$error_msg = "آیدی تراکنش صحیح نمی باشد";
		inax_header(get_site_url());
	}
	else{

	}*/
	//echo $tr_id;exit;
	if( isset($bill_data) ){//come from bulk_bill.php
		$row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $sql_table WHERE order_id='%s' LIMIT 1", $order_id ) );
	}else{
		$row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $sql_table WHERE id='%s' LIMIT 1", $tr_id ) );
	}
	
	if($row==null){
		$error_msg = "آیدی تراکنش صحیح نمی باشد";
		inax_header(get_site_url());
	}
	elseif ( is_object( $row ) ){
		$tr_row = (array)$row;//convert to array

		$status         = $tr_row['status'];
		$amount         = $tr_row['amount'];
		$product        = $tr_row['type'];
		$mobile         = $tr_row['mobile'];
		$operator       = $tr_row['operator'];
		$charge_type    = $tr_row['charge_type'];
		$internet_type  = $tr_row['internet_type'];
		$product_id     = $tr_row['product_id'];
		$sim_type       = $tr_row['sim_type'];
		$mnp            = $tr_row['mnp'];
		$bill_id        = $tr_row['bill_id'];
		$pay_id         = $tr_row['pay_id'];
		$mode           = $tr_row['mode'];
		$payment_type   = $tr_row['payment_type'];//if payment_type==online -> returned from online gateway
		$status         = $tr_row['status'];
		$gateway        = $tr_row['gateway'];
		$count          = 1;//تعداد شارژ پین

		$test_mode = false;
		if($mode=='test_mode'){
			$test_mode=true;
		}

		$buy_level = 0 ;
		//test_mode save to db
		//mnp null sent to inax

		$product_fa = inax_product_fa($product);

		$final_status	= '';
		//$error_msg		= 'خطای نامعلوم';
		//$callback		= "{$trans_link}?id=$tr_id";//incorrect query ?page_id=11?id=15&nok
		//$callback		= "{$trans_link}&id=$tr_id";

		$trans_link   	= htmlspecialchars_decode($trans_link);//convert &amp; to & and ...
		$query 			= parse_url($trans_link, PHP_URL_QUERY);//بررسی وجود کوئری
		$build_query 	= http_build_query( array('id'=>$tr_id) );

		if( isset($bill_data) ){//come from bulk_bill.php
			$build_query 	= http_build_query( array('oid'=>$tr_id) );
		}
		else{
			$build_query 	= http_build_query( array('id'=>$tr_id) );
		}

		if($query){
			$callback = "{$trans_link}&{$build_query}";
		}else {
			$callback = "{$trans_link}?{$build_query}";
		}
		//echo $callback;exit;

		$date = $url = null;

		$ref_code = $res_code = $result = $trans_id = '';

		unset($_SESSION['topup_id'], $_SESSION['pin_id'], $_SESSION['internet_id'], $_SESSION['bill_id']);
		$_SESSION["{$product}_id"] = $tr_id;
		//print_r($_SESSION);exit;

		if( $status=='refund' || $status=='refund_to_card' ){
			$error_msg = 'مبلغ تراکنش عودت داده شده است';
		}
		/*elseif( $payment_type == "online" && $status != 'paid' ){ // اگر نوع پرداخت آنلاین باشد و وضعیت پرداخت نشده باشد
			$error_msg = 'تراکنش پرداخت نشده است';
		}
		elseif( $payment_type == "credit"  && $status != 'paid' ){//در پرداخت اعتباری payment_type==null به این فایل ارسال می شود و پس از داشتن اعتبار روش پرداخت آن تنظیم می شود
			$error_msg = 'تراکنش پرداخت نشده است !';
		}*/
		else{
			if( $buy_level == 1 ){
				$error_msg = "درخواست خرید این {$product_fa} قبلا ارسال شده است.";
			}
			else{
				if( $product=='topup' ){
					$param = array(
						'amount'		=> $amount,
						'mobile'		=> $mobile,
						'operator'      => $operator,
						'charge_type'   => $charge_type,
						'mnp'   		=> $mnp,
						'order_id'		=> $tr_id . rand(100, 999),
						'callback'		=> $callback,
						'company'		=> 'inax',
						'test_mode'		=> $test_mode,
						'tr_id'         => $tr_id,//for gateway
					);
				}
				elseif( $product=='pin' ){
					$param = array(
						'amount'		=> $amount,
						'mobile'		=> $mobile,
						'count'			=> $count,
						'operator'		=> $operator,
						'order_id'		=> $tr_id . rand(100, 999),
						'callback'		=> $callback,
						'test_mode'		=> $test_mode,
						'tr_id'         => $tr_id,//for gateway
					);
				}
				elseif( $product=='internet' ){
					$param = array(
						'amount'		=> $amount,
						'internet_type'	=> $internet_type,
						'sim_type'		=> $sim_type,
						'product_id'	=> $product_id,
						'mobile'		=> $mobile,
						'mnp'		    => $mnp,
						'operator'		=> $operator,
						'order_id'		=> $tr_id . rand(100, 999),
						'callback'		=> $callback,
						'test_mode'		=> $test_mode,
						'tr_id'         => $tr_id,//for gateway
					);
				}
				elseif( $product=='bill' ){
					if($mode=='bulk'){
						$product='bulk_bill';
						//پرداخت قبض گروهی
						$param = array(
							'bill_data'		=> $bill_data,
							'mobile'		=> $mobile,
							//'order_id'		=> $tr_id . rand(100, 999),
							'callback'		=> $callback,
							'test_mode'		=> $test_mode,
							'tr_id'         => $tr_id,//for gateway
							'amount'        => $total_amount,//for gateway
						);
						//echo '<pre>'. print_r($param,true) . '</pre>';exit;
					}else{
						$param = array(
							'bill_id'		=> $bill_id,
							'pay_id'		=> $pay_id,
							'mobile'		=> $mobile,
							'order_id'		=> $tr_id . rand(100, 999),
							'callback'		=> $callback,
							'test_mode'		=> $test_mode,
							'tr_id'         => $tr_id,//for gateway
							'amount'        => $amount,//for gateway
						);
					}
				}

				//echo '<pre>'. print_r($param,true) . '</pre>';exit;

				//save order_id
				//if( !($product=='bill' && $mode=='bulk') ){//dont update order_id in bulk_bill
				if( $product!='bulk_bill' ){//dont update order_id in bulk_bill
				//if( isset($param['order_id']) ){
					$order_id = $param['order_id'];
					$wpdb->query("update $sql_table set order_id='$order_id' where id='$tr_id' ");
				}

				$user_payment_type = null;//نحوه پرداخت تراکنش توسط کاربر

				//echo $payment_type;exit;

				//اگر نحوه پرداخت انلاین باشد و وضعیت آن پرداخت شده باشد به معنی دریافت وجه از کاربر توسط درگاه پرداخت بوده و اجازه خرید تراکنش از اینکس برایش فراهم می شود
				if($payment_type=='online' && $status=='paid'){//ورود به این بخش بعد از بازگشت از درگاه پرداخت و پرداخت شدن مبلغ تراکنش
					$user_payment_type  = 'online';
					$payment_type       = 'credit';
					$param['pay_type']  = $payment_type;
					$result             = inax_request_json($product, $param);

					if( !$result['status'] ){
						$error_msg = "خطا - {$result['msg']}";
					}
					else{
						$data       = $result['data'];
						$msg        = $result['msg'];
						$res_code   = $data['code'];

						if( $res_code != 1 ){
							//اگر خطای -33 هم بدهد چون کاربر وجه را پرداخت کرده دیگر نباید مجددا به درگاه پرداخت انتقال داده شود - اطلاع به مدیر
							$error_msg = $data['msg'];
						}
						else{
							$trans_id		= $data['trans_id'];
							$ref_code		= $data['ref_code'];
							$status			= 'paid';
							$final_status	= 'success';
							$date			= date('Y-m-d H:i:s');
							$success_msg = ".عملیات خرید با موفقیت انجام پذیرفت";
							//از کیف پول کاربر نیز نباید مبلغی کسر شود
						}
					}
				}
				else{
					if( $payment_type=='online' ){
						//خرید از درگاه به علت کمبود موجودی کیف پول کاربر
						$user_payment_type  = 'online';
						$payment_type		= 'online';
						$error_msg			= 'اعتبار ناحیه کاربری شما برای پرداخت این تراکنش کافی نمی باشد.';

						//dont need
						//$wpdb->query("update $sql_table set payment_type='$user_payment_type' where id='$tr_id' ");

						$param['pay_type']  = $payment_type;
						$result             = inax_request_json($product, $param);
						$error_msg          = inax_check_response($product, $param, $result);//redirect to gateway or get error message
					}
					elseif( $payment_type=='credit' ){
						$credit = inax_check_user_credit();
						if( $credit >= $amount ){
							//خرید از اعتبار کیف پول کاربر
							$user_payment_type = 'credit';

							$payment_type       = 'credit';
							$param['pay_type']  = $payment_type;

							//remove client credit
							$amount_minus = $amount;
							if( get_option('woocommerce_currency') == 'IRR' ){
								$amount_minus = $amount*10;
							}
							$res = inax_change_credit($amount_minus, 'remove',"{$product_fa} به مبلغ {$amount} تومان - شماره {$mobile} با آیدی <a href='admin.php?page=inax_trans&id=$tr_id' target='_blank' >$tr_id</a>", $test_mode);
							//remove client credit

							$result = inax_request_json($product, $param);
							if( !$result['status'] ){
								$error_msg = "خطا - {$result['msg']}";
							}
							else{
								$data       = $result['data'];
								$msg        = $result['msg'];
								$res_code   = $data['code'];

								//$res_code = 3;//
								if( $res_code != 1 ){

									//refund client credit
									$res = inax_change_credit($amount_minus, 'add',"عودت {$product_fa} به مبلغ {$amount} تومان - شماره {$mobile} با آیدی <a href='admin.php?page=inax_trans&id=$tr_id' target='_blank' >$tr_id</a>", $test_mode);

									//if $status='paid کاربر وجه را پرداخت کرده و مجددا نباید به درگاه منتقل شود
									//خطای -33 به معنای کمبود اعتبار نمایندگی اینکس می باشد.
									// در حالت فعال بودن درگاه شخصی نباید به درگاه شخصی منتقل شود زیرا اعتبار نماینده کم است و در صورت پرداخت مبلع توسط کاربر. تراکنش دچار مشکل می شود
									$payment_gateway        = isset($inax_option['payment_gateway']) ? $inax_option['payment_gateway'] : '';//درگاه پرداخت انلاین

									if( $res_code == -33 && $status!='paid' && $payment_gateway=='' ){
										//if( $product=='bill' && $mode=='bulk' ){//در پرداخت گروهی قبوض مجددا خودکار درخواست نزند
										if( $product=='bulk_bill'  ){//در پرداخت گروهی قبوض مجددا خودکار درخواست نزند
											$error_msg          = 'اعتبار ناحیه کاربری نمایندگی شما برای پرداخت قبوض انتخاب شده کافی نمی باشد.';
										}
										else{
											//اعتبار پنل آینکس نماینده کافی نیست - پرداخت آنلاین
											$user_payment_type  = 'online';
											$payment_type       = 'online';
											$error_msg          = 'اعتبار ناحیه کاربری شما برای خرید شارژ انتخاب شده کافی نمی باشد.';

											//$wpdb->query("update $sql_table set payment_type='online' where id='$tr_id' ");

											$param['order_id']  = $tr_id . rand(100, 999);//جلوگیری از خطای شماره سفارش تکراری - پس از تکرار تراکنش عدم موفقیت در پرداخت اعتباری
											$param['pay_type']	= $payment_type;
											$result				= inax_request_json($product, $param);
											$error_msg			= inax_check_response($product, $param, $result);//redirect to gateway or get error message
										}
									}
									else{
										$error_msg = $data['msg'];
									}
								}
								else{
									//تراکنش موفق آینکس
									$trans_id		= $data['trans_id'];
									$ref_code		= $data['ref_code'];
									$url 			= isset($data['url']) ? $data['url'] : null;//online
									$status			= 'paid';
									$final_status	= 'success';
									$date			= date('Y-m-d H:i:s');

									//old method
									/*$res = inax_change_credit($amount_minus, 'remove',"{$product_fa} به مبلغ {$amount} تومان - شماره {$mobile} با آیدی <a href='admin.php?page=inax_trans&id=$tr_id' target='_blank' >$tr_id</a>", $test_mode);
									if($res!==false){
										$success_msg = "عملیات خرید با موفقیت انجام پذیرفت";
									}*/
									$success_msg = "عملیات خرید با موفقیت انجام پذیرفت";
								}
							}
						}
					}
				}

				$data           = isset($result['data']) ? $result['data'] : '';
				// save $result if $data is empty
				if($data!=''){
					$pay_result		= json_encode($data,JSON_UNESCAPED_UNICODE);
				}
				else{
					$pay_result		= json_encode($result,JSON_UNESCAPED_UNICODE);
				}

				$final_status       = !empty($final_status) ? "'$final_status'" : "NULL";
				$res_code           = !empty($res_code) ? "'$res_code'" : "NULL";
				$ref_code           = !empty($ref_code) ? "'$ref_code'" : "NULL";
				$trans_id           = !empty($trans_id) ? "'$trans_id'" : "NULL";
				$date               = !empty($date) ? "'$date'" : "NULL";
				$url                = !empty($url) ? "'$url'" : "NULL";
				$user_payment_type  = !empty($user_payment_type) ? "'$user_payment_type'" : "NULL";

				$wpdb->query("update $sql_table set 
				status='$status', 
				final_status=$final_status, 
				res_code=$res_code, 
				ref_code=$ref_code, 
				trans_id=$trans_id, 
				pay_date=$date, 
				url=$url, 
				payment_type=$user_payment_type, 
				pay_result='$pay_result' 
				WHERE id='$tr_id' ");

				$res_code = str_replace("'","",$res_code);
				if( $payment_type=='credit' && $res_code==1 ){
					inax_header("$callback&ok");
				}
				/*elseif($payment_type=='online'){
					inax_header($url);
				}*/
			}
		}
	}
}
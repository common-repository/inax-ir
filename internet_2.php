<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

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

//wp_enqueue_script('inax_jquery');
//wp_enqueue_script( 'jquery' );
wp_enqueue_script('inax_bootstrap_js');

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

	$smarty->assign('buy_internet', true);
	$smarty->assign('title', __('خرید بسته اینترنت', "inax" ));
	
	//کلیک روی دکمه برگشت از لیست بسته های اینترنت
	if( empty($_POST) ){
		unset($_SESSION['internet']);
	}

	//دریافت شماره موبایل - اپراتور و نوع سیم کارت
	if( isset($_POST['continue']) ){
		if( !isset($_POST['Token']) || !wp_verify_nonce($_POST['Token'], 'name_of_my_action') ){
			print 'Sorry, your nonce did not verify.';
			exit;
		}
		check_admin_referer('name_of_my_action', 'Token');//wordpress

		$valid_num = array(
			'MTN' 	=> array('0901','0902','0903','0904','0905','0930','0933','0935','0936','0937','0938','0939','0941','0900'),
			'MCI' 	=> array('0910','0911','0912','0913','0914','0915','0916','0917','0918','0919','0990','0991','0992','0993','0994','0995','0996'),
			'RTL' 	=> array('0920','0921','0922','0923'),
			'SHT' 	=> array('0998'),
		);

		$mobile			= !empty($_POST['mobile']) ? sanitize_text_field(trim($_POST['mobile'])) : '';
		$operator   	= !empty($_POST['operator']) ? sanitize_text_field(trim($_POST['operator'])) : '';
		$save_mobile   	= !empty($_POST['save_mobile']) ? 1 : "";
		$sim_type   	= !empty($_POST['sim']) ? sanitize_text_field(trim($_POST['sim'])) : '';

		$mobile = inax_convert_fa_to_en($mobile);
		$mobile = preg_replace('/\s+/', '', $mobile);//remove all space - 0902 111 22

		$valid_op = array_keys($valid_num);

		if( $mobile == "" ){
			$error_msg = 'شماره موبایل را وارد کنید !';
		}
		elseif( !$validate->Mobile($mobile) ){
			$error_msg = 'شماره موبایل صحیح نیست !';
		}
		elseif( !in_array($operator, $valid_op) ){
			$error_msg = 'اپراتور انتخاب نشده یا صحیح نیست !';
		}
		if( !($sim_type=='credit' || $sim_type=='permanent' || $sim_type=='TDLTE_credit' || $sim_type=='TDLTE_permanent' || $sim_type=='data' ) ){
			$error_msg = 'نوع سیم کارت صحیح نیست !';
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
				$four_digit = substr($mobile, 0, 4);

				$mnp = 0;
				if( !in_array($four_digit, $valid_num[$operator]) ){
					$mnp = 1;//شماره با اپراتور همخوانی ندارد لذا ترابرد شده است
				}

				$_SESSION['internet']['step'] 		= 1;
				$_SESSION['internet']['mnp'] 		= $mnp;
				$_SESSION['internet']['mobile'] 	= $mobile;
				$_SESSION['internet']['operator'] 	= $operator;
				$_SESSION['internet']['sim_type'] 	= $sim_type;
				$_SESSION['internet']['save_mobile']= $save_mobile;
				
				$smarty->assign( strtolower($operator) . '_active', true);//mtn_active ,...
			}
		}
	}

	$product_find = false;

	if( isset($_SESSION['internet']) ){
		//echo '<pre>internet '. print_r($_SESSION['internet'],true) . '</pre>';
	}
	
	//submit form step_1
	if( isset($_SESSION['internet']['step']) && $_SESSION['internet']['step']==1 && isset($_SESSION['internet']['operator']) && isset($_SESSION['internet']['mobile']) && isset($_SESSION['internet']['sim_type']) 
	&& !(isset($_POST['submit']) || isset($_POST['submit_credit']))
	){
		$operator = $_SESSION['internet']['operator'];
		$sim_type = $_SESSION['internet']['sim_type'];

		if( $operator=='MTN' ){
			$smarty->assign('mtn_active',true);
		}
		elseif( $operator=='MCI' ){
			$smarty->assign('mci_active',true);
		}
		elseif( $operator=='RTL' ){
			$smarty->assign('rtl_active',true);
		}
		elseif( $operator=='SHT' ){
			$smarty->assign('sht_active',true);
		}
		$smarty->assign('operator',$operator);

		$smarty->assign('package_list',true);

		unset($_SESSION['internet_packages']);

		$operator_fa 	= operator_fa($operator);

		/*$name = "2 ساعته- 4 گیگ";
		if($language_code!='fa'){
			$name2 = __($name,'inax');
			echo "$name2<br/>";
		}
		exit;*/

		$result = inax_request_json('products', []);
		if( !$result['status'] ){
			$error_msg = "خطا - {$result['msg']}";
		}
		else{
			$data = $result['data'];
			if( $data['code'] != 1 ){
				$error_msg = $data['msg'];
			}
			else{
				$pro = $data['products'];
				//$json = json_decode($pro,true);
				if( !isset($pro['internet']) ){
					$error_msg = "خطا در دریافت بسته های اینترنتی... لطفا دوباره تلاش کنید.";
				}else{
					$internet_package = $pro['internet'];
					//echo '<pre>internet_package '. print_r($internet_package,true) . '</pre>';exit;

					$package = [];
					foreach( $internet_package as $key => $values ){
						$id             = $values['id'];
						$name           = $values['name'];
						$amount         = $values['amount'];
						$internet_type  = $values['internet_type'];
						$operator1      = $values['operator'];
						$sim_type1      = $values['sim_type'];

						if($language_code!='fa'){
							//$name = __($name,'inax');
						}

						if($operator==$operator1 && $sim_type==$sim_type1){
							$package[$internet_type][] = array(
								'id'		=> $id,
								'amount'	=> $amount, 
								'name'		=> $name
							);

							$_SESSION['internet_packages'][$operator][$sim_type][$id]= array('id'=>$id,'amount'=>$amount, 'name'=>$name);
						}
					}

					foreach( $package as $type => $values ){
						$have_package['type_en'] = $type;
						$have_package['type_fa'] = inax_internet_type_fa($type);
						$have_package['lists2'] = $values;

						//echo '<pre>products '. print_r($have_package,true) . '</pre>';exit;
						$smarty->append('have_package',$have_package);
					}
				}
			}

			$_SESSION['internet']['internet_type'] = $internet_type;
		}
	}

	//دریافت نام بسته و اینسرت تراکنش
	if( (isset($_POST['submit']) || isset($_POST['submit_credit'])) && isset($_POST['pid']) && $_POST['pid']!='' ){
	//if( ( isset($_POST['submit']) || isset($_POST['submit_credit']) ) &&  ( isset($_GET['MTN']) || isset($_GET['MCI']) || isset($_GET['RTL']) || isset($_GET['SHT']) ) &&  isset($_GET['pid']) && $_GET['pid']!='' && isset($_GET['sim']) && ($_GET['sim']=='credit' || $_GET['sim']=='permanent' || $_GET['sim']=='TDLTE_credit' || $_GET['sim']=='TDLTE_permanent' || $_GET['sim']=='data' ) && isset($_GET['i']) && ($_GET['i']=='hourly' || $_GET['i']=='daily' || $_GET['i']=='weekly' || $_GET['i']=='monthly' || $_GET['i']=='yearly' || $_GET['i']=='amazing' || $_GET['i']=='TDLTE' ) ){
		if( !isset($_POST['Token']) || !wp_verify_nonce($_POST['Token'], 'name_of_my_action') ){
			print 'Sorry, your nonce did not verify.';
			exit;
		}

		check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

		$get_pid 	= sanitize_text_field($_POST['pid']);
		$exp 		= explode("-",$get_pid);

		if(isset($exp[1])){
			$in_type 	= $exp[0];
			$pid 		= $exp[1];//this check by ($_SESSION['internet_packages'] so cant changeed by user posted params

			$int_types_list = array('hourly','daily','weekly','monthly','yearly','amazing','TDLTE');
			if( !in_array($in_type, $int_types_list) ){
				$error_msg = "نوع بسته اینترنت صحیح نیست !";
			}
		}

		if( empty($error_msg) ){
			$operator 		= $_SESSION['internet']['operator'];
			$mobile 		= $_SESSION['internet']['mobile'];
			$mnp     		= (isset($_SESSION['internet']['mnp']) && $_SESSION['internet']['mnp']==1) ? 1 : '';
			$save_mobile    = (isset($_SESSION['internet']['save_mobile']) && $_SESSION['internet']['save_mobile']==1) ? "'1'" : "NULL";
			$sim_type 		= $_SESSION['internet']['sim_type'];

			//echo $save_mobile;exit;
	
			$operator_fa 	= operator_fa($operator);
	
			$mobile = inax_convert_fa_to_en($mobile);
			$mobile = preg_replace('/\s+/', '', $mobile);//remove all space - 0902 111 22
	
			if($mobile == ""){
				$error_msg = 'شماره موبایل را وارد کنید !';
			}
			elseif(!$validate->Mobile($mobile)){
				$error_msg = 'شماره موبایل صحیح نیست !';
			}
			elseif( op_number_check($operator,$mobile,$mnp)['result']==false ){
				$error_msg = "خطا : " . op_number_check($operator,$mobile,$mnp)['msg'];
			}
			else{
				if( !isset($_SESSION['internet_packages']) ){
					$error_msg = "بسته اینترنتی انتخاب نشده است.";
				}else{
					if( !isset($_SESSION['internet_packages'][$operator][$sim_type][$pid]) ){
						$error_msg = "آیدی بسته به درستی تنظیم نشده است";
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
							$package    = $_SESSION['internet_packages'][$operator][$sim_type][$pid];
							$name       = $package['name'];
							$amount     = $package['amount'];
	
							if( isset($_POST['submit_credit']) && $user_credit<$amount ){
								$error_msg = "اعتبار شما ({$user_credit}) تومان جهت پرداخت مبلغ {$amount} کافی نیست";
							}
							else{
								$date = date('Y-m-d H:i:s');
	
								$mode='';
								if($test_mode){
									$mode='test_mode';
								}
	
								$payment_type = 'online';
								if( isset($_POST['submit_credit']) && $user_credit>=$amount ){
									$payment_type = 'credit';
								}
	
								$mnp 	= !empty($mnp) ? "'$mnp'" : "NULL";
								$mode   = !empty($mode) ? "'$mode'" : "NULL";
	
								$que = "INSERT INTO $inax_charge_db (client_id, type, mobile, mnp, operator, internet_type, sim_type, product_id, product_name, amount, payment_type, save_mobile, date, mode) VALUES ('$client_id', 'internet', '$mobile', $mnp, '$operator', '$in_type', '$sim_type', '$pid', '$name' , '$amount', '$payment_type', $save_mobile, '$date', $mode) ";
								//echo $que;exit;
								$sql = $wpdb->query($que);
								if( !$sql ){
									$error_msg = 'خطا در ذخیره اطلاعات در دیتابیس ...<br/>' . $wpdb->last_error;
								}
								else{
									$tr_id          			= $wpdb->insert_id;
									//$_SESSION['internet_id'] 	= $tr_id;
									$_GET['id']     			= $tr_id;
									require_once INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'buy.php';
								}
							}
						}
					}
					
				}
			}
		}
		
		
	} //-->submit
}

if(isset($head_error)){$smarty->assign('head_error',$head_error);}
if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . "/templates/client/$inax_theme/internet_2.tpl");
?>
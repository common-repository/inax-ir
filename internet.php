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
wp_enqueue_script( 'jquery' );
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

	$smarty->assign('title', __('خرید بسته اینترنت', "inax" ));
	

	$product_find = false;

	if(isset($_GET['MTN']) || isset($_GET['MCI']) || isset($_GET['RTL']) || isset($_GET['SHT']) ){

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
		elseif(isset($_GET['SHT'])){
			$operator = 'SHT';
			$smarty->assign('sht_active',true);
		}
		$smarty->assign('operator',$operator);
		//$smarty->assign('total_count',10);

		if( (isset($_GET['MTN']) || isset($_GET['MCI']) || isset($_GET['RTL']) || isset($_GET['SHT']) ) && !isset($_GET['sim']) ){//درخواست نوع سیم کارت
			$smarty->assign('request_sim_type',true);
		}
		elseif( ( isset($_GET['MTN']) || isset($_GET['MCI']) || isset($_GET['RTL']) || isset($_GET['SHT']) ) && isset($_GET['sim']) && ($_GET['sim']=='credit' || $_GET['sim']=='permanent' || $_GET['sim']=='TDLTE_credit' || $_GET['sim']=='TDLTE_permanent' || $_GET['sim']=='data' ) ){
			$smarty->assign('package_list',true);

			unset($_SESSION['internet_packages']);

			$sim_type = sanitize_text_field($_GET['sim']);
			$smarty->assign('sim_type',$sim_type);
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

						/*if( !isset($internet_package[$operator]) ){
							$error_msg = " برای اپراتور {$operator_fa} بسته های اینترنتی یافت نشد .";
						}
						else{
							if(!isset($internet_package[$operator][$sim_type])){
								$sim_type_fa 	= sim_type_fa($sim_type);
								$error_msg = "برای سیم کارت {$sim_type_fa} {$operator_fa} بسته اینترنتی یافت نشد";
							}else{
								$package = $internet_package[$operator][$sim_type];

								//$int_types_list = array('hourly','daily','weekly','monthly','yearly','amazing','TDLTE');
								$int_types_list = array('hourly'=>'ساعتی','daily'=>'روزانه','weekly'=>'هفتگی','monthly'=>'ماهیانه','yearly'=>'سالیانه','amazing'=>'شگفت انگیز','TDLTE'=>'اینترنت ثابت TD-LTE');

								foreach ($int_types_list as $type_en => $type_fa){
									if( isset($package[$type_en]) ){
										$have_package['type_en'] = $type_en;
										$have_package['type_fa'] = $type_fa;

										$have_package['lists2']=array();//جلوگیری از تکرار بسته

										foreach ($package[$type_en] as $pid => $pack){
											$id 		= $pack['id'];
											$name 		= $pack['name'];
											$amount 	= $pack['amount'];

											$pack_list2 = array('id'=>$id,'amount'=>$amount, 'name'=>$name);
											$have_package['lists2'][] = $pack_list2;
										}
										$smarty->append('have_package',$have_package);
									}
								}
								$smarty->append('internet_package',$package);
							}
						}*/
					}
				}
			}
		}

		//مشاهده جزئیات محصول انتخاب شده
		if( isset($_GET['pid']) && $_GET['pid']!='' && ( isset($_GET['MTN']) || isset($_GET['MCI']) || isset($_GET['RTL']) || isset($_GET['SHT']) ) && isset($_GET['sim']) && ($_GET['sim']=='credit' || $_GET['sim']=='permanent' || $_GET['sim']=='TDLTE_credit' || $_GET['sim']=='TDLTE_permanent' || $_GET['sim']=='data' ) && isset($_GET['i']) && ($_GET['i']=='hourly' || $_GET['i']=='daily' || $_GET['i']=='weekly' || $_GET['i']=='monthly' || $_GET['i']=='yearly' || $_GET['i']=='amazing' || $_GET['i']=='TDLTE' ) ){
			$pid		= sanitize_text_field($_GET['pid']);
			$sim_type 	= sanitize_text_field($_GET['sim']);
			$in_type 	= sanitize_text_field($_GET['i']);

			if(isset($_GET['MTN']) ){
				$operator = 'MTN';
			}elseif(isset($_GET['MCI'])){
				$operator = 'MCI';
			}elseif(isset($_GET['RTL'])){
				$operator = 'RTL';
			}elseif(isset($_GET['SHT'])){
				$operator = 'SHT';
			}

			$smarty->assign('package_list',false);
			$smarty->assign('enter_mobile',true);

			//echo '<pre>'. print_r($_SESSION['internet_packages'],true) . '</pre>';exit;

			if( !isset($_SESSION['internet_packages']) ){
				$error_msg = "بسته اینترنتی انتخاب نشده است.";
			}else{
				if( !isset($_SESSION['internet_packages'][$operator][$sim_type][$pid]) ){
					$error_msg = "آیدی بسته به درستی تنظیم نشده است";
				}else{
					$smarty->assign('product_find',true);
					$smarty->assign('buy_internet',true);

					$package = $_SESSION['internet_packages'][$operator][$sim_type][$pid];

					$smarty->assign('product_id', $pid );
					$smarty->assign('product_name', $package['name']);
					$smarty->assign('product_amount', $package['amount']);

					$smarty->assign('internet_type',$in_type);
					$smarty->assign('sim_type',$sim_type);
				}
			}
		}
	}
	else{
		$smarty->assign('select_operator',true);
	}


	//ارسال فرم خرید
	if( ( isset($_POST['submit']) || isset($_POST['submit_credit']) ) &&  ( isset($_GET['MTN']) || isset($_GET['MCI']) || isset($_GET['RTL']) || isset($_GET['SHT']) ) &&  isset($_GET['pid']) && $_GET['pid']!='' && isset($_GET['sim']) && ($_GET['sim']=='credit' || $_GET['sim']=='permanent' || $_GET['sim']=='TDLTE_credit' || $_GET['sim']=='TDLTE_permanent' || $_GET['sim']=='data' ) && isset($_GET['i']) && ($_GET['i']=='hourly' || $_GET['i']=='daily' || $_GET['i']=='weekly' || $_GET['i']=='monthly' || $_GET['i']=='yearly' || $_GET['i']=='amazing' || $_GET['i']=='TDLTE' ) ){
		if( !isset($_POST['Token']) || !wp_verify_nonce($_POST['Token'], 'name_of_my_action') ){
			print 'Sorry, your nonce did not verify.';
			exit;
		}

		check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

		$pid 		= sanitize_text_field($_GET['pid']);
		$sim_type 	= sanitize_text_field($_GET['sim']);
		$in_type 	= sanitize_text_field($_GET['i']);

		if(isset($_GET['MTN']) ){
			$operator = 'MTN';
		}elseif(isset($_GET['MCI'])){
			$operator = 'MCI';
		}elseif(isset($_GET['RTL'])){
			$operator = 'RTL';
		}elseif(isset($_GET['SHT'])){
			$operator = 'SHT';
		}

		$mobile     	= (isset($_POST['mobile']) && $_POST['mobile'] != '') ? sanitize_text_field(trim($_POST['mobile'])) : '';
		$mnp        	= (isset($_POST['mnp']) && $_POST['mnp']==1) ? 1 : '';
		$save_mobile    = (isset($_POST['save_mobile']) && $_POST['save_mobile']==1) ? "'1'" : "NULL";

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
	
								$mnp = !empty($mnp) ? "'$mnp'" : "NULL";
								$mode    = !empty($mode) ? "'$mode'" : "NULL";
	
								$sql = $wpdb->query("INSERT INTO $inax_charge_db (client_id, type, mobile, mnp, operator, internet_type, sim_type, product_id, product_name, amount, payment_type, save_mobile, date, mode) VALUES ('$client_id', 'internet', '$mobile', $mnp, '$operator', '$in_type', '$sim_type', '$pid', '$name' , '$amount', '$payment_type', $save_mobile, '$date', $mode) " );
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
$smarty->display( dirname( __FILE__ ) . "/templates/client/$inax_theme/internet.tpl");
?>
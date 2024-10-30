<?php
if (!defined("ABSPATH"))	die("This file cannot be accessed directly");

wp_enqueue_style('inax_bootstrap');
wp_enqueue_style('inax_bootstrap_rewrite');
wp_enqueue_style('inax_bootstrap_themes');
wp_enqueue_style('inax_style');
wp_enqueue_style('inax_font_awesome');
wp_enqueue_style('inax_font_awesome2');
//wp_enqueue_script('inax_js');
if($language_code=='fa' || $language_code=='ar'){
	wp_enqueue_style('inax_style_fa');
}

//modal
wp_enqueue_script( 'jquery' );
wp_enqueue_script('inax_bootstrap_js');

$client_id = get_current_user_id();

$res = $wpdb->query("SHOW TABLES LIKE '$inax_charge_db' ");
if(!$res){
	inax_install();//install database table again
	$head_error = "مشکلی در اتصال به پایگاه داده وجود دارد - تیبل مورد نظر یافت نشد. لطفا صفحه را رفرش نمائید در صورتی که مشکل برطرف نشد یک تیکت به <a href='https://inax.ir/panel/submit_ticket.php' target='_blank'>پشتیبانی آینکس</a> ارسال نمائید";
}
else{
	$smarty->assign('tr_list', true);
	$smarty->assign('title', __( 'لیست تراکنش ها', "inax" ) );

	//echo '<pre>post : ' . print_r($_POST,true) . '</pre>';exit;

	if( isset($_GET['g']) && $_GET['g']!='' ){//خرید از درگاه اختصاصی فروشگاه
		$gateway = $_GET['g'];
		$class = "INAX_$gateway";
		$result = call_user_func( array( new $class, 'callback' ) );
		$error_msg = $result['error_msg'];
	}
	elseif( isset($_POST['hash']) ){//برگشت از پرداخت آنلاین آینکس
		$trans_id   = sanitize_textarea_field($_POST['id']);
		$order_id   = sanitize_textarea_field($_POST['order_id']);
		$amount     = sanitize_textarea_field($_POST['amount']);
		$ref_code   = sanitize_textarea_field($_POST['ref_code']);
		$status     = sanitize_textarea_field($_POST['status']);
		$hash 		= sanitize_textarea_field($_POST['hash']);

		if( md5("$trans_id:{$inax_option['username']}:{$inax_option['password']}") != $hash ){
			$error_msg = "اعتبارسنجی اطلاعات دریافتی صحیح نیست...";
		}
		else{
			$tr_id = substr($order_id, 0, -3);

			if( isset($_POST['buy_info']) ){//pin
				$buy_info = stripslashes($_POST['buy_info']);// convert \" to " - add_shortcode() function addslashes for $_POST parameters
				
				$res = inax_is_json($buy_info,true);
				if( $res['status']==false ){
					$error_msg = "خطا در جیسون دریافتی ... " . $res['error'];
				}else{
					$buy_info_arr		= json_decode($buy_info,true);
					$_POST['buy_info'] 	= $buy_info_arr;
				}

				//echo '<pre>res : ' . print_r($res,true) . '</pre>';
				//echo '<pre>buy_info_arr : ' . print_r($buy_info_arr,true) . '</pre>';
			}
			//echo '<pre>_POST : ' . print_r($_POST,true) . '</pre>';exit;

			$date = date('Y-m-d H:i:s');
			$pay_result = json_encode($_POST,JSON_UNESCAPED_UNICODE);
			//echo '<pre>pay_result : ' . print_r($pay_result,true) . '</pre>';exit;

			if( $status == 'paid' ){
				$wpdb->query("update $inax_charge_db set status='$status',final_status='success',ref_code='$ref_code',pay_date='$date',pay_result='$pay_result' where id='$tr_id' and status='unpaid' ");
			}
			else{
				$wpdb->query("update $inax_charge_db set status='$status',pay_result='$pay_result' where id='$tr_id' ");
			}

			$affected_rows_count = $wpdb->rows_affected;
			if( $affected_rows_count == 1 ){
				
				if( $status == 'paid' ){
					$build_query 	= http_build_query( array('id'=>$tr_id, 'ok'=>'') );
				}
				else{
					echo "<div class='alert alert-primary'>اطلاعات با موفقیت ذخیره شد</div>";
					$build_query 	= http_build_query( array('id'=>$tr_id, 'nok'=>'') );
				}

				$trans_link   	= htmlspecialchars_decode($trans_link);//convert &amp; to & and ...
				$query 			= parse_url($trans_link, PHP_URL_QUERY);//بررسی وجود کوئری
				if($query){
					inax_header("{$trans_link}&{$build_query}");
				}
				else {
					inax_header("{$trans_link}?{$build_query}");
				}
				exit;
			}
			else{
				echo "<div class='alert alert-danger'>خطا در ذخیره سازی داده ها - ممکن است قبلا انجام شده باشد</div>";
			}
		}
	}
	//else

	//if( (isset($_GET['id']) && $_GET['id']!='' || $client_id!=0 ) && ( ($client_id==0 && isset($valid_order_id) && $valid_order_id==$_GET['id'] && isset($_SESSION['topup_id']) && $_SESSION['topup_id']==$_GET['id']) || ($client_id!=0) ) ){
	//if( display_invoice('topup',$client_id) ){

	//echo '<pre>'. print_r($_SESSION,true) . '</pre>';exit;

	// نمایش تراکنش های کاربر
	$tr_ok = false;
	if( isset($_GET['id']) ){
		if( isset($_SESSION['topup_id']) && $_SESSION['topup_id']==$_GET['id'] ){
			$tr_ok = true;
		}
		if( isset($_SESSION['pin_id']) && $_SESSION['pin_id']==$_GET['id'] ){
			$tr_ok = true;
		}
		if( isset($_SESSION['internet_id']) && $_SESSION['internet_id']==$_GET['id'] ){
			$tr_ok = true;
		}
		if( isset($_SESSION['bill_id']) && $_SESSION['bill_id']==$_GET['id'] ){
			$tr_ok = true;
		}
	}

	if( $client_id!=0 || ( isset($_GET['id']) && $_GET['id']!='' && (is_numeric($_GET['id']) || (is_numeric($_GET['id']) && (strpos($_GET['id'], "-") !== false) ) ) && $client_id==0 && $tr_ok ) ){
		$cond = '';
		$tr_id = (isset($_GET['id']) && $_GET['id']!="") ? sanitize_text_field(intval($_GET['id'])) : '';
		//echo $tr_id;exit;

		if( $tr_id!='' ){
			$cond = " and id='$tr_id' ";
		}

		$type = (isset($_GET['type']) && $_GET['type']!="" && ($_GET['type']=='topup' || $_GET['type']=='pin' || $_GET['type']=='bill' || $_GET['type']=='internet') ) ? sanitize_text_field($_GET['type']) : '';
		if( $type!='' ){
			$cond = " and type='$type' ";
		}

		$sql = "SELECT * FROM $inax_charge_db where client_id='$client_id' $cond ORDER BY id DESC ";
		//echo $sql;exit;

		$trans_rows = $wpdb->get_results($sql, ARRAY_A);
		foreach($trans_rows as $row){
			$type = $row['type'];

			if($type=='pin'){
				$pay_result = $row['pay_result'];
				$operator   = $row['operator'];

				$buyed_output ='';
				$call_charge = '';
				if( !empty($pay_result) ){
					$code_ussd='';

					switch ($operator){
						case 'MTN':	        $code_ussd="*141*"; break;
						case 'MCI':	        $code_ussd="*140*#"; break;
						case 'RTL':		    $code_ussd="*141*";break;
						case 'TAL':	        $code_ussd="*140*"; break;
						default :           $code_ussd="-"; break;
					}

					$buyed_products 		= json_decode($pay_result,true);

					if(isset($buyed_products['buy_info']) && is_array($buyed_products['buy_info'])){
						foreach($buyed_products['buy_info'] as $key => $byued){// به ازای هر تعداد محصول
							$number=$key+1;//begin from 1
							$pin 	= $byued['pin'];
							$serial = $byued['serial'];

							//$call_charge .= $code_ussd.$pin."#<br/>\r\n";
							//{$number}
							$buyed_output .= "کد شارژ :  {$pin}<br/>";
							$buyed_output .= "سریال :  {$serial}<br/><hr/>";
							//$buyed_output .= "call: {$call_charge}<br/>";
						}
						$call_charge .= "{$code_ussd}code#<br/>";
						$buyed_output .= "نحوه شارژ با شماره گیری: <span style='direction:ltr;display:inline-block;'>{$call_charge}</span><br/>";
					}
				}
				$row['buyed_output']= $buyed_output;
			}
			$smarty->append('trans_rows', $row);
		}
	}
}
$smarty->assign('client_id',$client_id);

if(isset($head_error)){$smarty->assign('head_error',$head_error);}
if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . "/templates/client/$inax_theme/trans.tpl");


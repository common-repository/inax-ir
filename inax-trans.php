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

//wp_enqueue_script('inax_bootstrap_js');
wp_enqueue_script( 'jquery' );
wp_enqueue_script('inax_bootstrap_js');
add_thickbox();//add modal to wordpress - https://developer.wordpress.org/reference/functions/add_thickbox/

require_once INAX_DIR.'inc'.DIRECTORY_SEPARATOR.'load.php';

$res = $wpdb->query("SHOW TABLES LIKE '$inax_charge_db' ");
if(!$res){
	inax_install();//install database table again
	$head_error = "مشکلی در اتصال به پایگاه داده وجود دارد - تیبل مورد نظر یافت نشد. لطفا صفحه را رفرش نمائید در صورتی که مشکل برطرف نشد یک تیکت به <a href='https://inax.ir/panel/submit_ticket.php' target='_blank'>پشتیبانی آینکس</a> ارسال نمائید";
}
else{
	if( isset($_GET['do_action']) ){
		$do_action = $_GET['do_action'];

		$tr_id = '';
		if( isset($_POST['trans_id']) ){
			$tr_id = $_POST['trans_id'];
		}
		elseif( isset($_GET['trans_id']) ){
			$tr_id = $_GET['trans_id'];
		}

		$db_name = $wpdb->prefix . 'inax_charge';

		if( $tr_id == '' ){
			echo "شناسه تراکنش خالی است";
		}elseif( $db_name == '' ){
			echo "نام دیتابیس خالی است";
		}else{
			$tr_rows = $wpdb->get_row("select * from $db_name where id='$tr_id' ", ARRAY_A);
			if( $tr_rows!==null){
				$amount         = $tr_rows['amount'];
				$status         = $tr_rows['status'];
				$final_status   = $tr_rows['final_status'];
				$ref_code       = $tr_rows['ref_code'];
				$mnp            = $tr_rows['mnp'];
				$operator       = $tr_rows['operator'];
				$sim_type       = $tr_rows['sim_type'];

				if( $do_action == 'change_status' ){ // تغییر وضعیت پرداخت تراکنش
					$new_status = $_POST['new_status'];
					$text = "وضعیت قبلی پرداخت تراکنش {$status} بود به {$new_status} تغییر یافت.";

					if( $new_status == 'paid' ){
						$date = date('Y-m-d H:i:s');
						$wpdb->query("update $db_name set status='$new_status',pay_date='$date',description=CONCAT(description,' $text') where id='$tr_id' ");
					}else{
						$wpdb->query("update $db_name set status='$new_status',description=CONCAT(description,' $text') where id='$tr_id' ");
					}
					$success_msg = $text;
				}
				elseif( $do_action == 'change_ref_code' ){ // تغییر وضعیت پرداخت تراکنش
					$new_ref_code = $_POST['new_ref_code'];
					$text = "رسید قبلی {$ref_code} بود به {$new_ref_code} تغییر یافت.";

					$wpdb->query("update $db_name set ref_code='$new_ref_code', description=CONCAT(description,' $text') where id='$tr_id' ");

					$success_msg = $text;
				}
				elseif( $do_action == 'change_pr_status' ){ // تغییر وضعیت خرید
					$new_pr_status = $_POST['new_pr_status'];
					$text = "وضعیت قبلی خرید محصول {$pr_status} بود به {$new_pr_status} تغییر یافت.";

					$wpdb->query("update $db_name set final_status='$new_pr_status',description=CONCAT(description,' $text') where id='$tr_id' ");

					$success_msg = $text;
				}
				elseif( $do_action == 'change_mnp' ){
					$new_mnp = $_POST['new_mnp'];
					$text = "وضعیت ترابرد پذیری قبلی {$mnp} بود به {$new_mnp} تغییر یافت.";

					$wpdb->query("update $db_name set mnp='$new_mnp', description=CONCAT(description,' $text') where id='$tr_id' ");

					$success_msg = $text;
				}
				elseif( $do_action == 'change_operator' ){
					$new_operator = $_POST['new_operator'];
					$text = "وضعیت قبلی اپراتور {$operator} بود به {$new_operator} تغییر یافت.";

					$wpdb->query("update $db_name set operator='$new_operator', description=CONCAT(description,' $text') where id='$tr_id' ");

					$success_msg = $text;
				}
				elseif( $do_action == 'change_sim_type' ){
					$new_sim_type = $_POST['new_sim_type'];
					$text = "نوع سیم کارت قبلی {$sim_type} بود به {$new_sim_type} تغییر یافت.";

					$wpdb->query("update $db_name set sim_type='$new_sim_type', description=CONCAT(description,' $text') where id='$tr_id' ");

					$success_msg = $text;
				}
				elseif( $do_action == 'buy' ){
					$url = dirname(set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']));//http://examle.com/wp-admin
					$callback = "$url/admin.php?page=inax_trans&id=$tr_id";

					$_GET['id'] = $tr_id;
					require_once INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'buy.php';
				}

				$st = '';
				$msg = 'بدون پیام';
				if( isset($success_msg) ){
					$st = 'success';
					$msg = $success_msg;
				}elseif( isset($error_msg) ){
					$st = 'danger';
					$msg = $error_msg;
				}

				inax_header("admin.php?page=inax_trans&id={$tr_id}&{$st}={$msg}");
			}
		}
	}

	$display_perpage = 20;
	$page = 1;
	if( (isset($_GET['P']) && $_GET['P'] >= 0) ){
		$page = sanitize_text_field(intval($_GET['P']));
	}
	$start = ($page - 1) * $display_perpage;

	$cond = "";

	$wpdb->get_results("SELECT id FROM $inax_charge_db ORDER BY id DESC ", ARRAY_A);
	$tr_count = $wpdb->num_rows;
	$smarty->assign('tr_count', $tr_count);

	$cond = " where 1=1";
	if( isset($_GET['id']) ){
		$id = sanitize_text_field(intval($_GET['id']));
		$cond .= " and id='$id' ";
	}

	$rows_charge = $wpdb->get_results("SELECT * FROM $inax_charge_db $cond ORDER BY id DESC LIMIT $start,$display_perpage", ARRAY_A);
	foreach($rows_charge as $row){
		$get_userdata   = get_userdata( $row['client_id'] );
		$pay_result   	= $row['pay_result'];

		$pay_result_string = '';
		if( !empty($pay_result) ){
			$ex = json_decode($pay_result, true);
			$pay_result_string = inax_array_to_string($ex, "|");

			//$pay_result_string = htmlentities($pay_result_string,ENT_NOQUOTES,'UTF-8',false);
			$pay_result_string = htmlentities($pay_result_string);

			$pay_result_string = str_replace ("|","<br/>",$pay_result_string);

			//$pay_result_string = htmlentities($pay_result_string);
			
		}
		//echo "pay_result_string : $pay_result_string<br/>";
		$row['pay_result_string'] = $pay_result_string;

		$first_name     = isset($get_userdata->first_name) ? $get_userdata->first_name : '';
		$last_name      = isset($get_userdata->first_name) ? $get_userdata->last_name : '';
		$nickname       = isset($get_userdata->first_name) ? $get_userdata->nickname : '';

		$row['client_name'] = "$first_name $last_name ($nickname)";

		$smarty->append('charge_rows', $row);
	}

	$showpages = inax_show_list_pages("admin.php?page=inax_trans&",$tr_count,$display_perpage,$page);
	$smarty->assign('showpages',$showpages);
}

if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . '/templates/admin/trans.tpl');
?>
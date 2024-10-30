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
//بعد از ایجاد صفحه جدید باید تغییرات در فانکشن های post__shortcode و inax_get_pages نیز اضافه شود
if( isset($_GET['insert']) && $_GET['insert']!='' ){
	///check that the current user is allowed to activate plugins
	$pages = array(
		'main'          => 'صفحه اصلی فروشگاه',
		'topup'         => 'خرید شارژ مستقیم',
		'pin'           => 'خرید شارژ پین',
		'internet'      => 'خرید بسته اینترنت',
		'inquiry_bill'  => 'استعلام بدهی قبوض',
		'bill'          => 'پرداخت قبض',
		//'bulk_bill'     => 'پرداخت گروهی قبض',
		'trans'         => 'تراکنش ها',
	);
	// add page info to 
	// inax_get_pages() function in inax-functions.php 
	// inax_installer() functions in inax-functions.php 
	// post__shortcode() functions in inax-functions.php 

	$flipped_pages = array_flip($pages);

	if( ! current_user_can( 'activate_plugins' ) ){
		$error_msg = "شما دسترسی لازم برای ساخت صفحه جدید را ندارید !";
	}
	elseif( !in_array($_GET['insert'], $flipped_pages)){
		$error_msg = "نام صفحه صحیح نیست !";
	}
	else{
		$page_name = $_GET['insert'];
		
		if( ($check_result = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}posts WHERE post_name = '$page_name'  limit 1 "))!==NULL ){//check if not exist
			$error_msg = "نامک صفحه $page_name از قبل وجود دارد <a target='_blank' href='post.php?post=$check_result&action=edit'>(ویرایش)</a>";
		}
		elseif( ($check_result2 = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}posts WHERE post_content LIKE '%[inax page=\"$page_name\"%' limit 1 "))!==NULL ){
			$error_msg = "صفحه با محتوای <span style='direction:ltr'>[inax page=\"$page_name\"]</span> وجود دارد از قبل وجود دارد <a target='_blank' href='post.php?post=$check_result2&action=edit'>(ویرایش)</a>";
		}
		else{
			$current_user = wp_get_current_user();

			$page_fa = $pages[$page_name];
			$page = array(
				'post_title'  => $page_fa,
				'post_status' => 'publish',
				'post_author' => $current_user->ID,
				'post_type'   => 'page',
				'post_name'   => $page_name,
				'post_content'=> '[inax page="'.$page_name.'"]',
			);
			//print_r($page);exit;

			$post_id = wp_insert_post( $page );
			//print_r($post_id);exit;
			if($post_id>0){
				inax_header("admin.php?page=inax_page");
			}else{
				$error_msg = "خطا در ایجاد صفحه $post_id";
			}
		}
	}
}

if( (isset($_GET['disable']) || isset($_GET['enable'])) && isset($_GET['p']) && $_GET['p']!='' ){
	$page = $_GET['p'];

	//$disabled_page = array('main','pin');
	$inax_option2 = $inax_option;

	if( isset($_GET['disable']) ){
		if(!in_array($page, $disabled_page)){//اگر قبلا در صفحات غیرفعال دیتابیس موجود نباشد
			//افزودن صفحه به آرایه صفحات غیرفعال
			$disabled_page[] = $page;
			$inax_option2['disabled_page'] = $disabled_page;
			update_option('inax_options', json_encode($inax_option2)) OR add_option('inax_options', json_encode($inax_option2));
		}
	}
	elseif( isset($_GET['enable']) ){
		if(($key = array_search($page, $disabled_page)) !== false){//اگر صفحه مورد نظر در صفحات غیرفعال دیتابیس موجود باشد
			unset($disabled_page[$key]);

			$inax_option2['disabled_page'] = $disabled_page;
			update_option('inax_options', json_encode($inax_option2)) OR add_option('inax_options', json_encode($inax_option2));
		}
		//echo '<pre>'. print_r($disabled_page,true) . '</pre>';exit;
	}
}

//ساخت آرایه db_rows با کلید های مورد نیاز
foreach($inax_get_pages as $lang => $res){
	//echo '<pre>'. print_r($res,true) . '</pre>';exit;
	foreach($res as $page => $res2){
		$id       = isset($res2['id']) ? $res2['id'] : '';
		$title    = isset($res2['title']) ? $res2['title'] : '';
		$url      = isset($res2['url']) ? $res2['url'] : '';
		$page     = isset($res2['page']) ? $res2['page'] : '';
		$page_fa  = isset($res2['page_fa']) ? $res2['page_fa'] : '';

		$inax_pages['id']       = $id;
		$inax_pages['title']    = $title;
		$inax_pages['url']      = $url;
		$inax_pages['page']     = $page;
		$inax_pages['page_fa']  = $page_fa;
		$inax_pages['lang']  	= $lang;

		$db_rows[] = $inax_pages;
	}
}
//echo '<pre>'. print_r($db_rows,true) . '</pre>';exit;

//echo '<pre>'. print_r($db_rows,true) . '</pre>';exit;
foreach($db_rows as $page => $db_row){
	unset($inax_pages2);
	$page = $db_row['page'];

	$note='';
	if($page=='inquiry_bill'){
		$note = "استفاده از این سرویس رایگان نیست. استعلام هر قبض شامل هزینه است";
	}
	$db_row['note']    = $note;

	$db_row['page_status'] = 'enable';
	if(in_array($page, $disabled_page)){
		$db_row['page_status'] = 'disable';
	}
	//echo '<pre>'. print_r($inax_pages2,true) . '</pre>';

	$smarty->append("inax_pages", $db_row);
}

if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . '/templates/admin/pages.tpl');
?>
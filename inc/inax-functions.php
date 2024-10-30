<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

/**
 * plugin installer
 */
function inax_installer(){
	global $wpdb;
    //$default_options = include INAX_DIR . 'inc/inax-config.php';
    $default_options = array('username' => '','password' => '');
    add_option('inax_options', json_encode($default_options));
    
    $current_version = inax_get_plugin_version();
    add_option('inax_version',$current_version ) OR update_option('inax_version', $current_version );
    add_option('inax_do_activation', true) OR update_option('inax_do_activation', true );

    //insert pages if not exist
	if( ! current_user_can( 'activate_plugins' ) ) return;////check that the current user is allowed to activate plugins

	$pages = array(
		'main'			=>'صفحه اصلی فروشگاه',
		'topup'			=>'خرید شارژ مستقیم',
		'pin'			=>'خرید شارژ پین',
		'internet'		=>'خرید بسته اینترنت',
		'inquiry_bill'	=>'استعلام بدهی قبوض',
		'bill'			=>'پرداخت قبض',
		//'bulk_bill'     => 'پرداخت گروهی قبض',
		'trans'         => 'تراکنش ها',
	);
	
	foreach($pages as $page => $page_fa){
		if( null === $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = '$page' ") ){//check if not exist
			$current_user = wp_get_current_user();
			// create post object
			$page = array(
				'post_title'  => $page_fa,
				'post_status' => 'publish',
				'post_author' => $current_user->ID,
				'post_type'   => 'page',
				'post_name'   => $page,
				'post_content'=> '[inax page="'.$page.'"]',
			);

			$post_id = wp_insert_post( $page );
		}else{
			//echo 'exist';
		}
	}

	$get_sql_mode = $wpdb->get_row("SELECT @@GLOBAL.sql_mode as global_sql_mode, @@SESSION.sql_mode as session_sql_mode");
	if( $get_sql_mode->session_sql_mode!='' || $get_sql_mode->global_sql_mode!='' ){
		$wpdb->query("SET sql_mode = ''; ");
		$wpdb->query("SET GLOBAL sql_mode = ''; ");
	}
}

/**
 * plugin update
 */
add_action('upgrader_process_complete','inax_updater');
function inax_updater(){
    $current_ver = inax_get_plugin_version();
    if($current_ver != get_option('inax_version')){
        inax_installer();
    }
}

/**
 * init function
 * @global type $inax_option
 */
function inax_init(){
    global $inax_option;
    $db_options 	= get_option('inax_options');

    if($db_options==false){//install again if not exist this options
	    inax_installer();
	    $db_options 	= get_option('inax_options');
	    $inax_option 	= json_decode($db_options, TRUE);
    }
    else{
	    $inax_option 	= json_decode($db_options, TRUE);
    }

	//check if TeraWallet plugin is active , else empty wallet
	//echo '<pre> '. print_r($inax_option,true) . '</pre>';exit;
	if( isset($inax_option['wallet']) && $inax_option['wallet']=='TeraWallet' && !( in_array('woo-wallet/woo-wallet.php', apply_filters('active_plugins', get_option('active_plugins')) ) && in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')) ) ) ){
		$inax_option2 = $inax_option;
		$inax_option2['wallet'] = '';
		update_option('inax_options', json_encode($inax_option2)) OR add_option('inax_options', json_encode($inax_option2));
	}

	//check if yith plugin is active , else empty wallet
	if( isset($inax_option['wallet']) && $inax_option['wallet']=='yith' && !( in_array('yith-woocommerce-account-funds-premium/init.php', apply_filters('active_plugins', get_option('active_plugins')) ) && in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')) ) ) ){
		$inax_option2 = $inax_option;
		$inax_option2['wallet'] = '';
		update_option('inax_options', json_encode($inax_option2)) OR add_option('inax_options', json_encode($inax_option2));
	}
}

/**
 * Setup plugin page option link
 */
function inax_add_settings_link( $links ){
    $settings_link = '<a href="'.menu_page_url('inaxir',FALSE).'">'.__('تنظیمات','inax').'</a>';
    Array_unshift( $links, $settings_link );
    return $links;
}

add_action('admin_enqueue_scripts', 'inax_reg_admin_css_and_js');
function inax_reg_admin_css_and_js(){

	$language_code 	= inax_get_site_lang();
	$version 		= inax_get_plugin_version();
	
	wp_register_style( 'inax_admin_style', plugins_url('assets/css/inax.admin.css', INAX_PLUGIN_FILE), [], $version );
	if($language_code=='fa' || $language_code=='ar'){
		wp_register_style( 'inax_bootstrap', plugins_url('assets/bootstrap-5.2.2/css/bootstrap.rtl.min.css', INAX_PLUGIN_FILE), [], $version );
		wp_register_style( 'inax_style_fa', plugins_url('assets/css/inax-fa.css', INAX_PLUGIN_FILE ), [], $version );
	}else{
		wp_register_style( 'inax_bootstrap', plugins_url('assets/bootstrap-5.2.2/css/bootstrap.min.css', INAX_PLUGIN_FILE), [], $version );
	}

	//wp_register_style( 'inax_font_awesome',  plugins_url('assets/font-awesome-4.7.0/css/font-awesome.min.css', INAX_PLUGIN_FILE), [], $version );
	//https://fontawesome.com/docs/web/setup/host-yourself/webfonts
	wp_register_style( 'inax_font_awesome',  plugins_url('assets/fontawesome-6.2.1-web/css/fontawesome.min.css', INAX_PLUGIN_FILE ), [], $version );
	wp_register_style( 'inax_font_awesome2',  plugins_url('assets/fontawesome-6.2.1-web/css/solid.min.css', INAX_PLUGIN_FILE ), [], $version );
	//wp_register_style( 'inax_font_awesome',  plugins_url('assets/fontawesome-6.2.1-web/css/brands.min.css', INAX_PLUGIN_FILE ), [], $version );

	wp_register_script( 'inax_bootstrap_js', plugins_url('assets/bootstrap-5.2.2/js/bootstrap.bundle.min.js', INAX_PLUGIN_FILE), [], $version );
	
	//number_format for inax_amount_limitation field
	wp_register_script( 'inax_js', plugins_url('assets/js/inax.js', INAX_PLUGIN_FILE ), [], $version, true );//
}

//include css & js to wordpress header (client side)
add_action('wp_enqueue_scripts', 'inax_reg_front_css_and_js');
function inax_reg_front_css_and_js( ){
	global $inax_option;

	$version 		= inax_get_plugin_version();
	$language_code 	= inax_get_site_lang();
	$inax_theme 	= isset($inax_option['theme']) ? $inax_option['theme'] : 'default';

	if($inax_theme=='default'){
		if($language_code=='fa' || $language_code=='ar'){
			wp_register_style( 'inax_bootstrap', plugins_url('assets/bootstrap-5.2.2/css/bootstrap.rtl.min.css', INAX_PLUGIN_FILE ), [], $version );
			wp_register_style( 'inax_style_fa', plugins_url('assets/css/inax-fa.css', INAX_PLUGIN_FILE ), [], $version );
		}else{
			wp_register_style( 'inax_bootstrap', plugins_url('assets/bootstrap-5.2.2/css/bootstrap.min.css', INAX_PLUGIN_FILE), [], $version );
		}
		
		wp_register_style( 'inax_style', plugins_url('assets/css/inax-style.css', INAX_PLUGIN_FILE ), [], $version );

		//wp_register_style( 'inax_font_awesome',  plugins_url('assets/font-awesome-4.7.0/css/font-awesome.min.css', INAX_PLUGIN_FILE ), [], $version );
		//https://fontawesome.com/docs/web/setup/host-yourself/webfonts
		wp_register_style( 'inax_font_awesome',  plugins_url('assets/fontawesome-6.2.1-web/css/fontawesome.min.css', INAX_PLUGIN_FILE ), [], $version );
		wp_register_style( 'inax_font_awesome2',  plugins_url('assets/fontawesome-6.2.1-web/css/solid.min.css', INAX_PLUGIN_FILE ), [], $version );
		//wp_register_style( 'inax_font_awesome',  plugins_url('assets/fontawesome-6.2.1-web/css/brands.min.css', INAX_PLUGIN_FILE ), [], $version );

		wp_register_script( 'inax_bootstrap_js', plugins_url('assets/bootstrap-5.2.2/js/bootstrap.bundle.min.js', INAX_PLUGIN_FILE ), [], $version, true );
		wp_register_script( 'inax_js', plugins_url('assets/js/inax.js', INAX_PLUGIN_FILE ), [], $version, true );

		//wp_register_script( 'inax_jquery', plugins_url('assets/js/jquery-3.6.3.min.js', INAX_PLUGIN_FILE ), [], $version, true );
		//wp_register_script( 'inax_jquery', plugins_url('assets/js/jquery-1.12.3.min.js', INAX_PLUGIN_FILE ), [], $version, true );
	}
	elseif($inax_theme=='perple'){
		/*wp_register_style( 'inax_css_1', plugins_url('../templates/client/perple/css/default.css', __FILE__ ) );
		wp_register_style( 'inax_css_2', plugins_url('../templates/client/perple/css/ion.rangeSlider.min.css', __FILE__ ) );

		wp_register_script( 'inax_js_1', plugins_url('../templates/client/perple/js/jquery-3.2.1.min.js', __FILE__ ), array(), false, true );
		wp_register_script( 'inax_js_2', plugins_url('../templates/client/perple/js/ion.rangeSlider.min.js', __FILE__ ), array(), false, true );
		wp_register_script( 'inax_js_3', plugins_url('../templates/client/perple/js/sweetalert.min.js', __FILE__ ), array(), false, true );
		wp_register_script( 'inax_js_4', plugins_url('../templates/client/perple/js/script.min.js', __FILE__ ), array(), false, true );*/
	}
}

function inax_get_site_lang(){
	$language_code = 'fa';

	//---------- check if WPML Multilingual CMS installed -------------
	if( in_array('sitepress-multilingual-cms/sitepress.php', apply_filters('active_plugins', get_option('active_plugins')) ) ){
		//global $post;////only work in post__shortcode()
		//only client side
		//if( (is_page() || is_single()) && !empty($post->ID) ){//only work in post__shortcode()
			//$post_id = $post->ID;
			//https://wpml.org/faq/how-to-get-current-language-with-wpml/
			//$my_post_language_details = apply_filters( 'wpml_post_language_details', NULL, $post_id ) ;//return NULL if WPML Multilingual CMS not installed
			//$language_code = isset($my_post_language_details['language_code']) ? $my_post_language_details['language_code'] : 'fa';//for wpml_post_language_details
			
			$language_details = apply_filters( 'wpml_current_language', NULL );//return NULL if WPML Multilingual CMS not installed
			//echo '<pre>language_details '. print_r($language_details,true) . '</pre>';exit;
			$language_code = isset($language_details) ? $language_details : 'fa';// for wpml_current_language
			
			//the default language of the admin pages (wp-admin) is determined by the language you select on your profile, so you can have the site in chinese on the front-end and always see the back-end in English if you want.
		//}
	}
	//---------- check if WPML Multilingual CMS installed -------------
	
	//echo get_locale();
	//echo get_bloginfo("language");;
	if (get_locale() == 'en_US') {//get wordpress admin template
		$language_code = 'en';
	}
	elseif (get_locale() == 'tr_TR') {//get wordpress admin template
		$language_code = 'tr';
	}
	elseif (get_locale() == 'ar') {//get wordpress admin template
		$language_code = 'ar';
	}
	return $language_code;
}

//ajax js file
add_action( 'wp_enqueue_scripts', 'inax_ajax_scripts' );
function inax_ajax_scripts(){
	//wp_enqueue_script( 'jquery' );
	wp_register_script( 'my_check_bill_ajax', plugins_url('assets/js/inax.check_bill_ajax.js', INAX_PLUGIN_FILE ), [], false, true );
	wp_localize_script( 'my_check_bill_ajax', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

//string check_bill at the end of wp_ajax_check_bill & wp_ajax_nopriv_check_bill must be some as in check_bill_ajax.js action parameter value
add_action( "wp_ajax_check_bill", "inax_check_ajax_function" );
add_action( "wp_ajax_nopriv_check_bill", "inax_check_ajax_function" );
function inax_check_ajax_function(){
	global $wpdb;
	require_once INAX_DIR.'inc'.DIRECTORY_SEPARATOR.'ajax_check_bill.php';
	//echo json_encode( array( "error_msg"=>'ss' ) );
	wp_die(); // ajax call must die to avoid trailing 0 in your response
}
//ajax


// [inax page="topup" ]
// [inax page="pin" id="2"]
//https://codex.wordpress.org/Shortcode_API
add_shortcode( 'inax', 'post__shortcode' );
function post__shortcode( $atts ){
	// '<pre>post : ' . print_r($_POST,true) . '</pre>';exit;
	if( is_page() || is_single() ){//only in client side , not admin
		global $wpdb,$gateways_dir,$inax_charge_db,$permalink,$test_mode,$trans_link,$inax_option,$post,$language_code;//wpdb inax_charge_db used in inax_request_json
		//global $wpdb, $inax_option,$wp_query,$post;
		//echo get_permalink($post,false);

		//print_r($atts);// Array ( [page] => topup )
		
		//$post_id = $post->ID; //6
		//echo '<pre>'. print_r($post,true) . '</pre>';exit;

		//print_r($inax_option);
		$newtopup 		= (isset($inax_option['newtopup']) && $inax_option['newtopup']==1) ? 1 : 0;
		$newinternet 	= (isset($inax_option['newinternet']) && $inax_option['newinternet']==1) ? 1 : 0;

		$p_url 	= esc_url( get_permalink() );
		$a 		= shortcode_atts( array('page'=>"content"), $atts );
		
		$page = $a['page'];
		//echo '<pre>'. print_r($page,true) . '</pre>';exit;

		 //'bulk_bill',
		$valid_pages = array('main', 'topup', 'pin', 'internet', 'inquiry_bill', 'bill', 'trans');

		if( in_array($page, $valid_pages)){
			require_once INAX_DIR . 'inc' . DIRECTORY_SEPARATOR . 'load.php';
			
			$url    = htmlspecialchars_decode($p_url);//convert &amp; to & and ...
			$query 	= parse_url($url, PHP_URL_QUERY);//بررسی وجود کیئوری در آدرس
			if($query){
				$permalink = "{$p_url}&";
			} else {
				$permalink = "{$p_url}?";
			}

			//echo '<pre>'. print_r($page,true) . '</pre>';exit;

			$is_user_logged_in = is_user_logged_in();
			$smarty->assign('is_user_logged_in',$is_user_logged_in);

			$smarty->assign('permalink',$permalink);
			$smarty->assign('p_url',$p_url);

			$smarty->assign('language_code',$language_code);
			//wpml
		
			/*$awaiting_mod = 10;
			echo sprintf(
				_n( '%s Comment in moderation', '%s Comments in moderation', $awaiting_mod ),number_format_i18n( $awaiting_mod )
			);

			echo '<br/>' . sprintf( __( 'Howdy, %s' ), '<span class="display-name">' . 'John' . '</span>' );*/

			/*$number = 9;
			$str = "Beijing";
			$txt = sprintf("There are %u million bicycles in %s.", $number ,$str);
			echo $txt;*/
			
			//echo '<br/>' . sprintf( __( 'select %s number' ), 10 );

			if( $page=='topup' && $newtopup==1 ){//new topup
				$page='topup2';
			}
			elseif( $page=='internet' && $newinternet==1 ){//new internet
				$page='internet_2';
			}

			$client_id = get_current_user_id();

			//echo $page;

			require_once( realpath(__DIR__ . '/../') . "/{$page}.php" );
		}
	}
	// nothing to see here
	return '';
}

class IX_Payment_Gateway{
	public $tr_id;
	public $sql_table;

	public function __construct($tr_id, $callback, $user_payment_type){
		$this->tr_id                = $tr_id;
		$this->callback             = $callback;
		$this->user_payment_type    = $user_payment_type;
		//$this->trans_link           = $trans_link;
		//exit("IX_Payment_Gateway trans_link:$trans_link");
	}

	/*public function config(){
		//
	}*/

	/*public function pay($params,$gateway_params,$gateway_callback){
		//
	}*/

	/*public function callback(){
		//
	}*/
}

/*add_action( 'init', 'my_url_handler' );
function my_url_handler(){
	//http://localhost/?gateway_callback

	//نتیجه تراکنش برگشتی از درگاه اختصاصی سایت
	if( isset( $_GET['gateway_callback'] ) ){
		//require_once( realpath(__DIR__ . '/../') . "/callback.php" );
	}
}*/


/*
# http://localhost/?inax
add_action('parse_request', 'inax_custom_url_handler');
function inax_custom_url_handler(){
	echo '<pre> '. print_r($_SERVER,true) . '</pre>';exit;
	//&& $_SERVER["REQUEST_URI"] == '/custom_url'
	if( isset($_GET['inax']) ){
		echo "<h1>TEST</h1>";
		exit('ffff');
	}
}*/


/*
add_action( 'init', function(){
	add_rewrite_endpoint( 'changelog', EP_PERMALINK );
} );
# list query defined in wordpress
add_filter( 'query_vars', 'wpse26388_query_vars' );
function wpse26388_query_vars( $query_vars ){
	//echo '<pre> ' . print_r($query_vars, true) . '</pre>';
	//exit;
}*/


# http://localhost/wordpress/inax/test
add_action('init', function(){
	add_rewrite_endpoint('inax', EP_PERMALINK);
	//add_rewrite_endpoint('actors', EP_PERMALINK);
});
/*add_filter('request', function($vars){
	echo '<pre>ffff ' . print_r($vars, true) . '</pre>';
	/*
	 Array(
	    [page] =>
	    [pagename] => inax/test
	)
	* /
	return $vars;
});*/


add_filter('template_include', function($template){
	# http://localhost/wordpress/?inax=gateway
	/*if( get_query_var('inax') ){
		echo 'hello';
	}*/

	$query_value = get_query_var( 'inax' ) ;
	if( $query_value=='gateway' ){
		require_once( realpath(__DIR__ . '/../') . "/callback.php" );
		die;
	}
	elseif( $query_value=='callback' ){
		//نتیجه تراکنش برگشتی از درگاه اختصاصی سایت

	}

	return $template;
});

/*
add_action('admin_enqueue_scripts', 'ffff',9999);
function ffff(){
	global $wp_styles;

	echo '<pre>ffff ' . print_r($wp_styles, true) . '</pre>';exit;
}*/


function inax_get_plugin_version(){
    if(!function_exists('get_plugin_data')){
        include(ABSPATH . "wp-admin/includes/plugin.php"); 
    }

    //$plugin_data = get_plugin_data(dirname(__FILE__).DIRECTORY_SEPARATOR.'inax-ir.php', FALSE, FALSE );
    $plugin_data = get_plugin_data( realpath(__DIR__ . '/../') . '/inax-ir.php' , FALSE, FALSE );
    return $plugin_data['Version'];
}

function inax_array_to_string($array, $separator='<br/>'){
	$string ='';
	if( is_array($array) && !empty($array) ){
		foreach($array as $key => $value){
			//&& !empty($value) - اگر آرایه خالی باشد وارد الس میشه و خطای ارایه تو استرینگ میده
			if( is_array($value) ){
				foreach($value as $key1 => $value1){
					//&& !empty($value1)
					if( is_array($value1) ){
						//$string .= print_r($value1,true);//چون بالا استرینگ هست اینجا ارایه پرینت میکنم خطا میگیره
						$string .= json_encode($value1,JSON_PRETTY_PRINT);
					}else{
						$string .= "$key1: $value1{$separator}";
					}
				}
			}else{
				$string .= "$key: $value{$separator}";
			}
		}
	}
	return $string;
}

function operator_fa($operator, $language_code='fa'){
	switch ($operator){
		case 'MTN':		$operator_fa = 'ایرانسل';		$operator_en = 'Irancell'; break;
		case 'MCI':		$operator_fa = 'همراه اول'; 	$operator_en = 'Hamrahe Aval';break;
		case 'RTL':		$operator_fa = 'رایتل';			$operator_en = 'Rightel';break;
		case 'TAL':		$operator_fa = 'تالیا';			$operator_en = 'Taliya';break;
		case 'SHT':		$operator_fa = 'شاتل موبایل';	$operator_en = 'Shatel mobile';break;
		default   : 	$operator_fa = "";				 $operator_en = '';break;
	}
	if($language_code=='fa'){
		return $operator_fa;
	}else{
		return $operator_en;
	}
}

//چک پیش شماره های معتبر هر اپراتور
//چک امکان استفاده از گزینه ترابرد پذیری
function op_number_check($operator,$mobile,$charge_type){
	$result = false;
	$msg = "";
	if( ($operator=='MTN' || $operator=='MCI' || $operator=='RTL' || $operator=='SHT') && validate_Mobile($mobile) ){
		$valid_num = array(
			'MTN' 	=> array('0901','0902','0903','0904','0905','0930','0933','0935','0936','0937','0938','0939','0941','0900'),
			'MCI' 	=> array('0910','0911','0912','0913','0914','0915','0916','0917','0918','0919','0990','0991','0992','0993','0994','0995','0996'),
			'RTL' 	=> array('0920','0921','0922','0923'),
			'SHT' 	=> array('0998'),
			'TAL' 	=> array('09329'),
			'SPADAN'=> array('0931','09324'),
			'KISH' 	=> array('0934'),
			'UPTEL' => array('0999'),
			//'VOIP' 	=> array('0941'),//تلفن تصویری مشهد
		);
		$four_digit = substr($mobile, 0, 4);

		if($charge_type=='mnp' || $charge_type==1){//$mnp=1
			if( in_array($four_digit, $valid_num[$operator]) ){
				$operator_fa = operator_fa($operator);
				$msg = "شماره {$mobile} مختص اپراتور {$operator_fa} است. انتخاب ترابرد پذیری ممکن نیست.";
			}else{
				$result = true;
			}
		}else{
			if( in_array($four_digit, $valid_num[$operator]) ){//اگر شماره مورد نظر به اپراتور انتخاب شده تعلق داشته باشد
				$result = true;
			}else{
				$operator_fa = operator_fa($operator);
				$msg = "شماره موبایل {$mobile} با اپراتور {$operator_fa} تناسب ندارد . لطفا شماره موبایل یا اپراتور را اصلاح نمائید....";
			}
		}
	}else{
		$msg = "اپراتور یا شماره موبایل صحیح نیست.";
	}
	$out = array(
		'result'	=> $result,
		'msg'		=> $msg,
	);
	return $out;
}

function inax_bill_type_fa($bill_type, $language_code='fa'){
	switch ($bill_type){
		case 'water':	        $bill_type_fa="قبض آب"; break;
		case 'elec':	        $bill_type_fa="قبض برق"; break;
		case 'gas':		        $bill_type_fa="قبض گاز";break;
		case 'phone':	        $bill_type_fa="تلفن ثابت"; break;
		case 'mobile':	        $bill_type_fa="تلفن همراه"; break;
		case 'city':	        $bill_type_fa="عوارض شهرداری"; break;
		case 'tax':	            $bill_type_fa = 'سازمان مالیات';break;
		case 'traffic_fines':	$bill_type_fa = 'جریمه راهنمایی و رانندگی';break;
		default :               $bill_type_fa=""; break;
	}
	if($language_code=='fa'){
		return $bill_type_fa;
	}else{
		return $bill_type;
	}
}

function inax_internet_type_fa($internet_type, $language_code='fa'){
	switch($internet_type){
		case 'hourly'   ; $in_type_fa='ساعتی'; break;
		case 'daily'    ; $in_type_fa='روزانه'; break;
		case 'weekly'   ; $in_type_fa='هفتگی'; break;
		case 'monthly'  ; $in_type_fa='ماهیانه'; break;
		case 'yearly'   ; $in_type_fa='سالیانه'; break;
		case 'amazing'  ; $in_type_fa='شگفت انگیز'; break;
		case 'TDLTE'    ; $in_type_fa='اینترنت ثابت'; break;
		default         : $in_type_fa="نامعلوم"; break;
	}
	return $in_type_fa;
}

function inax_sim_type_fa($sim_type){
	switch( $sim_type ){
		case 'credit'           : $sim_type_fa = 'اعتباری';break;
		case 'permanent'        : $sim_type_fa = 'دایمی';break;
		case 'TDLTE_credit'     : $sim_type_fa = 'سیم کارت TD-LTE اعتباری';break;
		case 'TDLTE_permanent'  : $sim_type_fa = 'سیم کارت TD-LTE دائمی';break;
		case 'data'             : $sim_type_fa = 'دیتا';break;
		default                 : $sim_type_fa = "نامعلوم";break;
	}
	return $sim_type_fa;
}

function inax_convert_fa_to_en($string){
	$persian_num 	= array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'); 
	$persian_num2 	= array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');//2 va 8 ba in format nasashtam
	$latin_num 		= range(0, 9);
	$string 		= str_replace($persian_num, $latin_num, $string);
	$string 		= str_replace($persian_num2, $latin_num, $string);
	return $string;
}

function inax_product_fa($product){
	switch($product){
		case 'topup'		: $product_fa = 'شارژ مستقیم';break;
		case 'pin'			: $product_fa = 'شارژ پین';break;
		case 'internet'		: $product_fa = 'بسته اینترنت';break;
		case 'bill'			: $product_fa = 'پرداخت قبض';break;
		default				: $product_fa = $product ;break;
	}
	return $product_fa;
}

function inax_register_session(){
	if( !session_id() || session_status()== PHP_SESSION_NONE ){
		session_start();
	}
}
add_action('init','inax_register_session');

function inax_url_decrypt($string){
	$counter = 0;
	$data = str_replace(array('-','_','.'),array('+','/','='),$string);
	$mod4 = strlen($data) % 4;
	if($mod4){
		$data .= substr('====', $mod4);
	}
	$decrypted = base64_decode($data);

	$check = array('id','order_id','amount','ref_code','status','buy_info');
	foreach($check as $str){
		if( strpos($decrypted, $str)!==false ){
			$counter++;
		}
	}

	if( $counter==5 || $counter==6){
		return array('data'=>$decrypted , 'status'=>true);
	}else{
		return array('data'=>'' , 'status'=>false);
	}
}

function inax_jdate_format($string, $format=null){
    if($format === null){
        $format = 'Y/m/d H:i:s';
    }
	require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php'); // برای تبدیل تایم استامپ لازم است
    $timestamp = smarty_make_timestamp($string);
	return jdate($format, $timestamp);
}

function inax_request_json($method,$param){
	global $inax_option,$permalink,$wpdb;

	$status = false;
	$msg = $data = '';

	$sql_table = $wpdb->prefix . 'inax_charge';

	//درگاه پیش فرض انتخاب شده از تنظیمات آبنکس
	$payment_gateway        = isset($inax_option['payment_gateway']) ? $inax_option['payment_gateway'] : '';//درگاه پرداخت انلاین

	//check if an active gateway exist
	$inax_payment_gateway 	= get_option('inax_payment_gateway');
	if($inax_payment_gateway!=false){
		$inax_payment_option 	= json_decode($inax_payment_gateway, TRUE);
		//echo '<pre>'. print_r($inax_payment_option,true) . '</pre>';exit;
		if(is_array($inax_payment_option)){
			foreach($inax_payment_option as $gateway => $values){
				$status1 = isset($values['status']) ? $values['status'] : '';
				if($status1==1 && $payment_gateway==$gateway){
					$active_gateway_exist=true;
					break;
				}
			}
		}
	}

	//use site owners gateway for pay online transaction
	if( isset($param['pay_type']) && $param['pay_type']=='online' && $payment_gateway!='' && isset($active_gateway_exist) ){
		$tr_id      = $param['tr_id'];
		$order_id   = $param['order_id'];
		//echo '<pre>'. print_r($param,true) . '</pre>';exit;

		//بررسی اعتبار آینکس نماینده پیش از انتقال کاربر به درگاه پرداخت شخصی
		$credit_result = inax_request_json('credit',[]);
		if( !$credit_result['status'] ){
			$msg = "خطا - {$credit_result['msg']}";
		}
		else{
			$data = $credit_result['data'];
			if( $data['code']!=1 ){
				$msg = $data['msg'];
			}else{
				$reseller_credit = $data['credit'];
				if( $reseller_credit < $param['amount'] ){
					$reseller_credit2 = number_format($reseller_credit);
					$amnt = number_format($param['amount']);
					$msg = "اعتبار پنل نمایندگی فروش شارژ ($reseller_credit2 تومان) برای پرداخت مبلغ این تراکنش ($amnt تومان) کافی نیست.";
				}
				else{
					$reseller_credit_enought = true;
				}
			}
		}

		if( isset($reseller_credit_enought) ){
			$wpdb->query("update $sql_table set gateway='$payment_gateway',gateway_order_id='$order_id' where id='$tr_id' ;");

			//get gateway info
			$gateway_params = $inax_payment_option[$payment_gateway];

			$gateway_callback = $param['callback']."&g=$payment_gateway";

			//check if class and method exist and have valid variables type (string,array ,...)
			$class = "INAX_$gateway";
			if( !is_callable( array( new $class, 'pay' ) , false, $callable_name) ){
				$msg = "خطا در فراخوانی کلاس $callable_name";
			}else{
				//echo $callable_name;  //  someClass::someMethod
				$result     = call_user_func( array(new $class, 'pay'), $param, $gateway_params, $gateway_callback );
				$status     = true;
				$data       = $result;
				$msg        = 'موفق';
			}
		}
	}
	else{
		if(!is_string($method)){
			$msg = "Method name must be a string";
		}
		elseif(!is_array($param)){
			$msg = "Parameters must be an array";
		}
		else{
			$parameters['username'] = isset($inax_option['username']) ? $inax_option['username'] : '';
			$parameters['password'] = isset($inax_option['password']) ? $inax_option['password'] : '';
			$parameters['method']   = $method;
			$parameters['version']  = inax_get_plugin_version();

			//add extra param
			if( !empty($param) ){
				foreach( $param as $key => $value)
					$parameters[$key] = $value;
			}
			//echo '<pre>parameters: '. print_r($parameters,true) . '</pre>';exit;

			$url 	    = "https://inax.ir/webservice.php";
			$result 	= inax_post($url , $parameters, array('Content-Type' => 'application/json; charset=utf-8') );
			//echo '<pre>'. print_r($result,true) . '</pre>';exit;

			if( $result['status'] ){
				$status     = true;
				$data       = $result['data'];
				$msg        = $result['msg'];
			}else{
				$msg = "خطا در برقراری ارتباط با وب سرویس " . $result['msg'];
			}
		}
	}

	$res['status'] 		= $status;
	$res['msg'] 		= $msg;
	$res['data'] 		= $data;
	return $res;
}

function inax_post($url, $parameters, $headers, $http_method='POST'){
	ini_set('default_socket_timeout', 80);

	$http_code = $msg = $response = '';
	$status = false;

	//$headers['Content-Type'] = 'application/json; charset=utf-8';
	$headers['Content-Type'] = 'application/json';

	$arr = array(
		'headers'       => $headers,
		'body'          => json_encode($parameters),
		'method'        => $http_method,
		'timeout'       => 60,
		'sslverify'     => false,
		'data_format'   => 'body',
	);
	//echo '<pre>arr '. print_r($arr,true) . '</pre>';exit;

	$result = wp_remote_post($url, $arr);
	//echo '<pre>wp_remote_post '. print_r($result,true) . '</pre>';exit;

	if( is_wp_error($result) ){
		$msg = $result->get_error_message();
	}
	else{
		$http_code  = $result['response']['code'];
		$body       = $result['body'];

		$response = json_decode($body, true);

		if( $http_code!=200 ){
			$msg = "Request has failed with error";
		}
		else{
			$msg    = 'موفق';
			$status = true;

			/*$inax_is_json = inax_is_json($body,true);
			//echo "<pre>response ". print_r($inax_is_json,true) . '</pre>';
			if( !$inax_is_json['status'] ){
				$msg    = "error_in_response: ". $inax_is_json['error']. "<br/>$body";
			}*/
		}
	}

	$res['http_code'] 	= $http_code;
	$res['status'] 		= $status;
	$res['msg'] 		= $msg;
	$res['data'] 		= $response;

	return $res;
}

function inax_get_pages($get_page='', $language_code='fa'){
	global $wpdb,$inax_option;
	$inax_pages = [];
	$out = [];

	//echo "language_code : $language_code ";
	//وجود صفحات تراکنش با زبان های مختلف
	/*$ex_sql = '';
	//$multi_lang_pages = false;
	$slang = 'lang="' . $language_code . '"';//lang="en"
	if( in_array('sitepress-multilingual-cms/sitepress.php', apply_filters('active_plugins', get_option('active_plugins')) ) ){
		//$multi_lang_pages = true;
		
    	$ex_sql = "and post_content LIKE '%$slang%' ";
    	
		if( $language_code=='fa' ){
		    $slang2 = 'lang="' . 'fa' . '"';
			$ex_sql = "and (post_content not LIKE '%lang=%' or post_content LIKE '%$slang2%' ) ";
		}
	}*/
	/*
		
	}*/

	$disabled_page = isset($inax_option['disabled_page']) ? $inax_option['disabled_page'] : [];//صفحات غیرفعال دیتابیس
	//print_r($disabled_page);

	//order by desc to rewrite new transaction by first one - if new trans page inserted...
	$sql_e = "SELECT ID, post_title, guid,post_content,post_name,post_date FROM ".$wpdb->posts." WHERE post_content LIKE '%[inax page=%' AND post_status='publish' order by id desc ";//$ex_sql 
	$results = $wpdb->get_results($sql_e, ARRAY_A);
	//echo '<pre>'. print_r($results,true) . '</pre>';exit;
	foreach($results as $res){
		$post_id        = $res['ID'];
		$post_content   = $res['post_content'];
		$guid           = $res['guid'];
		$post_title     = $res['post_title'];
		$post_name     	= $res['post_name'];

		$permalink = get_permalink( $post_id );
		//echo "post_id: $post_id - $permalink<br/>"; 

		// '/[^a-z\d]/i' should also work.
		/*if (!preg_match('/[^A-Za-z0-9]/', $post_title)){
			// string contains only english letters & digits
		}*/

		//check if post_content contain lang="en" for example [inax page="trans" lang="en"]
		//$language_code = 'fa';
		/*if($multi_lang_pages){
			$slang = 'lang="' . $language_code . '"';//lang="en"
			if(strpos($post_content, $slang ) !== false){
				// yes exist language code in post_content
				//echo $post_id;exit;
				//echo $post_id. '<br/>';
			}
		}*/
		/*$db_lang_code = 'fa';
		if( ($begin = strpos($post_content, 'lang="' )) !== false ){
			//echo "$get_page<br/>";
			$db_lang_code = substr($post_content, $begin+6, 2);//[inax page="trans" lang="en"]
			
			//echo $db_lang_code. '<br/>';
			//$language_code = 'fa';
		}*/

		$page=$page_fa='';
		if(strpos($post_content, 'main') !== false){
			$page_fa    = 'صفحه اصلی فروشگاه';
			$page       = 'main';
		}
		if(strpos($post_content, 'topup') !== false){
			$page_fa    = 'صفحه خرید شارژ مستقیم';
			$page       = 'topup';
		}
		if(strpos($post_content, 'pin') !== false){
			$page_fa    = 'صفحه خرید شارژ پین';
			$page       = 'pin';
		}
		if(strpos($post_content, 'internet') !== false){
			$page_fa    = 'صفحه خرید بسته اینترنت';
			$page       = 'internet';
		}
		if(strpos($post_content, 'bill')!==false && strpos($post_content, 'bulk_bill')===false && strpos($post_content, 'inquiry_bill')===false){//dont get bulk_bill & inquiry_bill as bill
			$page_fa    = 'صفحه پرداخت قبض';
			$page       = 'bill';
		}
		if(strpos($post_content, 'inquiry_bill') !== false){
			$page_fa    = 'صفحه استعلام بدهی قبوض';
			$page       = 'inquiry_bill';
		}
		/*if(strpos($post_content, 'bulk_bill') !== false){
			$page_fa    = 'صفحه پرداخت گروهی قبض';
			$page       = 'bulk_bill';
		}*/
		if(strpos($post_content, 'trans') !== false){
			$page_fa    = 'صفحه تراکنش ها';
			$page       = 'trans';
		}

		$status = 'enable';
		if(in_array($page, $disabled_page)){
			$status = 'disable';
		}

		//echo "language_code : $language_code";exit;

		$my_post_language_details = apply_filters( 'wpml_post_language_details', NULL, $post_id ) ;//return NULL if WPML Multilingual CMS not installed
		//var_dump($my_post_language_details);
		//echo "<pre>my_post_language_details: $post_id ". print_r($my_post_language_details,true) . '</pre>';
		$db_lang_code = isset($my_post_language_details['language_code']) ? $my_post_language_details['language_code'] : 'fa';//for wpml_post_language_details
		//echo "post_id: $post_id -$page - $db_lang_code<br/>";

		//تغییر db_lang_code متناسب با زبان سایت برای صفحه تراکنش ها
		//if language_code=en and page=trans and wpml not installed 
		if( $language_code!='fa' && $page=='trans' && !in_array('sitepress-multilingual-cms/sitepress.php', apply_filters('active_plugins', get_option('active_plugins')) ) ){
			//$db_lang_code = 'en';//استفاده از صفحه تراکنش های فارسی برای این حالت
			//تنظیم زبان صفحه تراکنش ها متناسب با زبان صفحه
			$db_lang_code = $language_code;//استفاده از صفحه تراکنش های فارسی برای این حالت

		}

		//ساخت آرایه صفحات
		if($page!=''){
			$inax_pages[$db_lang_code][$page] = array(
				'id'        => $post_id,
				'title'     => $post_title,
				//'url'       => $guid,
				'url'       => $permalink,
				'page'      => $page,
				'page_fa'   => $page_fa,
				'status'    => $status,
				'lang'    	=> $db_lang_code,
			);
		}
		//echo '<pre>inax_pages: '. print_r($inax_pages,true) . '</pre>';exit;

		if( !empty($get_page) ){
			//گرفتن برگه به خصوص متناسب با زبان سایت
			if( is_array($get_page) ){
				foreach($get_page as $gt_pg){
					if( isset($inax_pages[$language_code][$gt_pg]) ){//بررسی وجود نام برگه در دیتابیس
						$out[$gt_pg] = $inax_pages[$language_code][$gt_pg];
					}
					//echo "$gt_pg<br/>";
					//$out = '';
				}

			}else{
				if( isset($inax_pages[$language_code][$get_page]) ){//بررسی وجود نام برگه در دیتابیس
					$out = $inax_pages[$language_code][$get_page];
				}
			}
		}
		else{
			//ارایه ای از تمام برگه ها
			$out = $inax_pages;
		}
	}

	//echo '<pre>out: '. print_r($out,true) . '</pre>';exit;
	return $out;
}

function inax_is_json($string,$return_error=false){
	json_decode($string);
	if($return_error){
		switch( json_last_error() ){
			case JSON_ERROR_NONE:           $error = ''; break;
			case JSON_ERROR_DEPTH:	        $error = 'Maximum stack depth exceeded'; break;
			case JSON_ERROR_STATE_MISMATCH: $error = 'Underflow or the modes mismatch';	break;
			case JSON_ERROR_CTRL_CHAR:	    $error = 'Unexpected control character found'; break;
			case JSON_ERROR_SYNTAX:	        $error = 'Syntax error, malformed JSON'; break;
			case JSON_ERROR_UTF8:           $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';	break;
			default:                        $error = 'Unknown error ' . json_last_error(); break;
		}

		return array(
			'status'    => (json_last_error() == JSON_ERROR_NONE),
			'error'     => $error
		);
	}else{
		return (json_last_error() == JSON_ERROR_NONE);
	}
}

//redirect to inax gateway or get error message - only in -33 error
function inax_check_response($product, $param, $result){
	global $error_msg,$wpdb;

	$sql_table = $wpdb->prefix . 'inax_charge';
	$tr_id = $param['tr_id'];

	//echo '<pre>inax_check_response '. print_r($result,true) . '</pre>';exit;
	//error_log('inax_check_response '. print_r($result,true));
	if( !$result['status'] ){
		$error_msg = "خطا - {$result['msg']}";
	}
	else{
		$data = $result['data'];
		if( isset($data['code']) ){
			//inax response
			if( $data['code'] != 1 ){
				$error_msg = isset($data['msg']) ? $data['msg'] : 'پاسخ نامشخص...';
			}
			else{
				$trans_id   = isset($data['trans_id']) ? $data['trans_id'] : '';
				$url        = $data['url'];
				//$url        = "https://inax.ir/pay.php?tid={$url}";

				$wpdb->query("update $sql_table set trans_id=$trans_id where id='$tr_id' ");
				inax_header($url);
			}
		}
		else{
			//return gateway response
			$error_msg = $data;
		}
	}

	return $error_msg;
}

function inax_show_list_pages($part,$total_count,$item_perpage,$page){
	$output = "";
	if($total_count - $item_perpage > 0){
		$output = '<div style="text-align:center;margin-top:15px;">';
		$paged_total = ceil($total_count / $item_perpage);
		$paged_last = $paged_total;
		$paged_middle = $page + 2;
		$paged_start = $paged_middle - 2;

		if($page > 1){
			$output .= '<div class="paged-link"><a href="'.$part.'P=1" title="صفحه اول">اولین</a></div>'."\n";
		}
		else{
			$output .= '<div class="paged-link-off">اولین</div>'."\n";
		}

		if($page > 1){
			$paged_perv = $page - 1;
			$output .= '<div class="paged-link"><a href="'.$part.'P='.$paged_perv.'" title="صفحه قبلی">قبلی</a></div>'."\n";
		}
		else{
			$output .= '<div class="paged-link-off">قبلی</div>'."\n";
		}

		for ($i=$paged_start-2; $i<=$paged_middle; $i++){
			if($i > 0 && $i <= $paged_last){
				if($i == $page){
					$output .= '<div class="paged-link-selected"><a href="'.$part.'P='.$i.'" title="صفحه '.$i.'">'.$i.'</a></div>'."\n";
				}
				else{
					$output .= '<div class="paged-link"><a href="'.$part.'P='.$i.'">'.$i.'</a></div>'."\n";
				}
			}
		}

		if($page <= $paged_last - 1){
			$paged_next = $page + 1;
			$output .= '<div class="paged-link"><a href="'.$part.'P='.$paged_next.'" title="صفحه بعدی">بعدی</a></div>'."\n";
		}
		else{
			$output .= '<div class="paged-link-off">بعدی</div>'."\n";
		}

		if($page <= $paged_last - 1){
			$output .= '<div class="paged-link"><a href="'.$part.'P='.$paged_last.'" title="صفحه آخر">آخرین</a></div>'."\n";
		}
		else{
			$output .= '<div class="paged-link-off">آخرین</div>'."\n";
		}

		$output .= '<div class="paged-link-info">صفحه : '.$page.' از '.$paged_total.'</div>'."\n";
		$output .= "</div>";
	}
	return $output;
}

function inax_check_user_credit(){//return credit as toman
	global $inax_option;
	$credit = 0;
	if(isset($inax_option['wallet'])){
		$wallet = $inax_option['wallet'];
		if( $wallet =='TeraWallet' ){
			if( in_array('woo-wallet/woo-wallet.php', apply_filters('active_plugins', get_option('active_plugins')) ) && in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')) ) ){//بررسی فعال بودن افزونه های مورد نیاز
				//$credit = woo_wallet()->wallet->get_wallet_balance( get_current_user_id() );//10,000 تومان
				$credit = get_user_meta( get_current_user_id(), '_current_woo_wallet_balance', true);
				if( get_option('woocommerce_currency') == 'IRR' ){
					$credit = $credit/10;
				}
			}
		}
		elseif( $wallet =='yith' ){
			if( in_array('yith-woocommerce-account-funds-premium/init.php', apply_filters('active_plugins', get_option('active_plugins')) ) && in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')) ) ){//بررسی فعال بودن افزونه های مورد نیاز
				//echo [yith_ywf_show_user_fund]
				//have html tag
				$customer 	= new YITH_YWF_Customer( get_current_user_id() );
				$credit 	= apply_filters( 'yith_show_available_funds', $customer->get_funds() );
				//$credit 	= $customer->get_funds();

				/*$credit = do_shortcode( '[yith_ywf_show_user_fund]' );//اعتبار موجود: تومان1.500

				$credit = strip_tags($credit);//remove html tags

				//$credit = preg_replace('/[^0-9]/', '', $credit);//only numbers

				$credit = preg_replace('/\s+/', '', $credit);//remove all space
				$credit = trim($credit);
				
				$credit = preg_replace('/[^\00-\255]+/u', '', $credit);//Remove Non English Characters PHP
				$credit = str_replace(array(":","تومان",".","&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;","&#xfdfc;"),"", $credit);
				*/

				//echo $credit;exit;

				if( get_option('woocommerce_currency') == 'IRR' ){
					$credit = $credit/10;
				}
			}
		}
	}
	return $credit;
}

function inax_change_credit($amount, $action, $description, $test_mode){
	//$test_mode = false;
	global $inax_option;
	$result = false;
	//error_log("inax_change_credit - test_mode $test_mode");
	if($test_mode==false){
		if(isset($inax_option['wallet'])){
			$wallet = $inax_option['wallet'];
			
			if( $wallet == 'TeraWallet' ){
				if($action=='remove'){
					$transaction_id = woo_wallet()->wallet->debit( get_current_user_id(), $amount, $description);
					$result 		= $transaction_id;
				}
				elseif($action=='add'){
					$transaction_id = woo_wallet()->wallet->credit( get_current_user_id(), $amount, $description);
					$result 		= $transaction_id;
				}
			}
			elseif( $wallet =='yith' ){
				$user_id 	= get_current_user_id();
				$customer 	= new YITH_YWF_Customer( $user_id );
				$credit 	= floatval( $customer->get_funds() );

				if( get_option('woocommerce_currency') == 'IRR' ){
					$credit = $credit/10;
				}

				if($action=='remove'){
					if( $credit < $amount ){
						error_log("user credit in yith waller ($credit) is less than amount : $amount");
					}
					else{
						$new_funds = $credit - $amount;
						$customer->set_funds( $new_funds );
		
						$diff_funds = $new_funds - $credit;
	
						$log_args = array(
							'user_id'        => $user_id,
							'editor_id'      => $user_id,
							'type_operation' => 'admin_op',
							'fund_user'      => $diff_funds,
							'description'    => $description,
						);
		
						//add log in ywf_user_fund_log table in wordpress db
						YWF_Log()->add_log( $log_args );
	
						$result = true;
					}
				}
				elseif($action=='add'){
					$new_funds = $credit + $amount;
					$customer->set_funds( $new_funds );
	
					$diff_funds = $new_funds - $credit;

					$log_args = array(
						'user_id'        => $user_id,
						'editor_id'      => $user_id,
						'type_operation' => 'admin_op',
						'fund_user'      => $diff_funds,
						'description'    => $description,
					);
					//print_r($diff_funds);exit;
	
					//add log in ywf_user_fund_log table in wordpress db
					YWF_Log()->add_log( $log_args );

					$result = true;
				}
			}
		}
	}else{
		$result = true;
		error_log("wallet did not changed, beacause test_mode is on");
	}

	return $result;
}

function inax_header($url){
	if($url!=''){
		//error_log($url);
		if( headers_sent() ){
			//echo('<script>window.location.assign("'.$url.'")</script>');
			//console.log($url);return false;
			echo "<script>window.location.href = '$url'; </script>";
			//die("<script>window.location.href = '$url'; </script>");
		}else{
			//exit("header: $url");
			header("Location: $url");
		}
	}
}
?>
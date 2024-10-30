<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

wp_enqueue_style('inax_admin_style');//UNSET SOME WPRDPRESS CSS
wp_enqueue_style('inax_bootstrap');
wp_enqueue_style('inax_bootstrap_rewrite');
wp_enqueue_style('inax_font_awesome');
wp_enqueue_style('inax_font_awesome2');
wp_enqueue_script('inax_bootstrap_js');
$language_code 	= inax_get_site_lang();
if($language_code=='fa' || $language_code=='ar'){
	wp_enqueue_style('inax_style_fa');
}

require_once INAX_DIR.'inc'.DIRECTORY_SEPARATOR.'load.php';

$payment_gateway = (isset($inax_option['payment_gateway']) && $inax_option['payment_gateway']!='' ) ? $inax_option['payment_gateway'] : '';//درگاه انتخاب شده به صورت پیش فرض

if(isset($_POST['save_inax_options'])){
	global $inax_option;
	check_admin_referer( 'name_of_my_action', 'Token' );//wordpress

	if( isset($_POST['field']) ){
		$field = $_POST['field'];
		foreach($_POST['field'] as $gt => $values){
			//print_r($values);

			$gateway_status = isset($values['status']) ? 1 : 0;
			$field[$gt]['status'] = $gateway_status;

			//تغییر درگاه پیش فرض انتخاب شده به درگاه آینکس در صورت غیرفعال کردن درگاه
			if($gt==$payment_gateway && $gateway_status==0){
				$inax_option2 = $inax_option;
				$inax_option2['payment_gateway'] = '';
				//print_r($inax_option2);
				update_option('inax_options', json_encode($inax_option2)) OR add_option('inax_options', json_encode($inax_option2));
			}
		}

		update_option('inax_payment_gateway', json_encode($field)) OR add_option('inax_options', json_encode($field));
		$success_msg = "با موفقیت ذخیره گردید.";
	}
}

//Get all classes that extent IX_Payment_Gateway
$active_gateways = array();
foreach( get_declared_classes() as $class ){
	if( is_subclass_of( $class, 'IX_Payment_Gateway' ) ){
		$active_gateways[] = $class;

		$result = call_user_func( array(new $class, 'config') );

		$gt_name = str_replace("INAX_","", $class);
		$gt_elements[$gt_name] = $result;
	}
}
//echo '<pre>'. print_r($gt_elements,true) . '</pre>';exit;

if(isset($gt_elements)){
	//مقادیر دیتابیس
	$inax_payment_gateway 	= get_option('inax_payment_gateway');
	if($inax_payment_gateway!=false){
		$inax_payment_option 	= json_decode($inax_payment_gateway, TRUE);
	}

	foreach ($gt_elements as $gateway => $elements){
		$status = (isset($inax_payment_option[$gateway]['status']) && $inax_payment_option[$gateway]['status']==1 ) ? 1 : 0;

		$gateway_fa = isset($elements['label']['value']) ? $elements['label']['value'] : 'نامشخص';
		$logo       = isset($elements['label']['logo']) ? $elements['label']['logo'] :  plugins_url("assets/images/nopic.png", INAX_PLUGIN_FILE );

		//echo '<pre>'. print_r($elements,true) . '</pre>';

		foreach ($elements as $key => $element){

			//echo '<pre>'. print_r($element,true) . '</pre>';
			$label          = isset($element['label']) ? $element['label'] : '';
			$type           = isset($element['type']) ? $element['type'] : '';
			$size           = isset($element['size']) ? $element['size'] : '';
			$value          = isset($element['value']) ? $element['value'] : '';
			$options        = isset($element['options']) ? $element['options'] : '';
			$description    = isset($element['description']) ? $element['description'] : '';

			if(isset($inax_payment_option[$gateway][$key])){//مقدار هر کلید در دیتابیس
				$html_elements['value']         = $inax_payment_option[$gateway][$key];
			}else{
				$html_elements['value']         = $value;
			}

			$html_elements['gateway']           = $gateway;
			$html_elements['label']             = $label;
			$html_elements['name']              = $key;
			$html_elements['type']              = $type;
			$html_elements['size']              = $size;
			$html_elements['options']           = $options;
			$html_elements['status']            = $status;
			$html_elements['gateway_fa']        = $gateway_fa;
			$html_elements['logo']              = $logo;
			$html_elements['description']       = $description;
			$html_elements['elements_count']    = count($elements);
			$html_elements['is_default']        = $payment_gateway==$gateway ;

			$smarty->append('html_elements', $html_elements);
		}
		//echo '<pre>'. print_r($elements,true) . '</pre>';
	}
}

if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . '/templates/admin/gateways.tpl');
?>
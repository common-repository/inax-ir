<?php if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

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
global $inax_option,$inaxir;

$inax_plugin_version = inax_get_plugin_version();
$smarty->assign('inax_plugin_version',$inax_plugin_version);

if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . '/templates/admin/about.tpl');
?>
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

$smarty->assign('inax_db_version', get_option( 'inax_db_version' ) );

$smarty->assign('default_socket_timeout', ini_get('default_socket_timeout') );

$smarty->assign('execute_time',round( microtime(true)-$msc , 2));

$smarty->assign('timestamp',time());

$get_db_time = $wpdb->get_row ("SELECT NOW() as now", ARRAY_A);
$smarty->assign('phpmyadmin_time', $get_db_time['now'] );

$get_sql_mode = $wpdb->get_row ("SELECT @@GLOBAL.sql_mode as global_sql_mode, @@SESSION.sql_mode as session_sql_mode", ARRAY_A );
if( $get_sql_mode['session_sql_mode'] !='' ||  $get_sql_mode['global_sql_mode'] !=''){
	$smarty->assign('session_sql_mode', $get_sql_mode['session_sql_mode']);
	$smarty->assign('global_sql_mode', $get_sql_mode['global_sql_mode']);
	$smarty->assign('change_sql_mode', true);
}

global $inax_db_version;
$smarty->assign('inax_plugin_db_version', $inax_db_version );

if(isset($error_msg)){$smarty->assign('error_msg',$error_msg);}
if(isset($success_msg)){$smarty->assign('success_msg',$success_msg);}
$smarty->display( dirname( __FILE__ ) . '/templates/admin/system.tpl');
?>
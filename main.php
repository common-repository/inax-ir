<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");

//add css and js to this file
wp_enqueue_style('inax_bootstrap');
wp_enqueue_style('inax_bootstrap_rewrite');
wp_enqueue_style('inax_style');
wp_enqueue_style('inax_font_awesome');
wp_enqueue_style('inax_font_awesome2');
if($language_code=='fa' || $language_code=='ar'){
	wp_enqueue_style('inax_style_fa');
}

/* old
$inax_get_pages = inax_get_pages();
foreach($inax_get_pages as $res){
	$page   = isset($res['page']) ? $res['page'] : '';
	$url    = isset($res['url']) ? $res['url'] : '';
	$smarty->assign("{$page}_link", $url );
}*/

//by language code
//$inax_get_pages = inax_get_pages();
//echo '<pre>'. print_r($inax_get_pages,true) . '</pre>';exit;
//$inax_get_pages from load.php
foreach($inax_get_pages as $lang => $res){
	foreach($res as $res2){
		$page   = isset($res2['page']) ? $res2['page'] : '';
		$url    = isset($res2['url']) ? $res2['url'] : '';
		$smarty->assign("{$page}_link", $url );
	}
}

/*
function wpa_inspect_styles(){//client side
	global $wp_styles;
	echo '<pre>ffff ' . print_r($wp_styles, true) . '</pre>';
	exit;
}
add_action( 'wp_enqueue_scripts', 'wpa_inspect_styles', 9999 );*/

$smarty->display( dirname( __FILE__ ) . "/templates/client/$inax_theme/main.tpl");
?>
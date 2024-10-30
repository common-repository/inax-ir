<?php
if(!defined("ABSPATH"))	die("This file cannot be accessed directly");
# https://codex.wordpress.org/Creating_Tables_with_Plugins
global $inax_db_version;
$inax_db_version = 1.9;

function inax_install(){
	global $wpdb;
	global $inax_db_version;

	$inax_charge_db = $wpdb->prefix . 'inax_charge';

	//$charset_collate = $wpdb->get_charset_collate();//DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
	$charset_collate = 'DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci';
	if($wpdb->get_var( "show tables like '$inax_charge_db'" ) != $inax_charge_db){
		//product_type varchar(10) COLLATE utf8_persian_ci NOT NULL DEFAULT '',
		//in some mysql version first element is used for default for enum type's... but in some mysql version dont work) - so it is better to set
		//status default value must bee unpaid so dont set to NULL
		$sql = "CREATE TABLE IF NOT EXISTS $inax_charge_db (
			id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			client_id int(10) UNSIGNED NOT NULL DEFAULT '0',
			type enum('','topup','pin','internet','bill') COLLATE utf8_persian_ci NULL DEFAULT NULL,
			mobile varchar(11) COLLATE utf8_persian_ci NULL DEFAULT NULL,
     		mnp enum('', '1') COLLATE utf8_persian_ci NULL DEFAULT NULL,
   			operator varchar(10) COLLATE utf8_persian_ci NULL DEFAULT NULL,
   			charge_type varchar(20) COLLATE utf8_persian_ci NULL DEFAULT NULL,
    		internet_type varchar(50) COLLATE utf8_persian_ci NULL DEFAULT NULL,
		    sim_type enum('', 'credit', 'permanent', 'TDLTE_credit', 'TDLTE_permanent', 'data') COLLATE utf8_persian_ci NULL DEFAULT NULL,
		    product_id varchar(50) COLLATE utf8_persian_ci NULL DEFAULT NULL,
		    product_name varchar(150) COLLATE utf8_persian_ci NULL DEFAULT NULL,
   			amount int(10) UNSIGNED NOT NULL DEFAULT '0',
			order_id varchar(100) COLLATE utf8_persian_ci NULL DEFAULT NULL COMMENT 'شماره سفارش آینکس',  
			check_charge varchar(10) COLLATE utf8_persian_ci NULL DEFAULT NULL,
			payment_type enum('','online','credit') COLLATE utf8_persian_ci NULL DEFAULT NULL,
    		gateway varchar(50) COLLATE utf8_persian_ci NULL DEFAULT NULL,
    		gateway_order_id varchar(100) COLLATE utf8_persian_ci NULL DEFAULT NULL COMMENT 'شماره سفارش درگاه',
    		ref_code varchar(100) COLLATE utf8_persian_ci NULL DEFAULT NULL COMMENT 'رسید آینکس',
    		trans_id varchar(100) COLLATE utf8_persian_ci NULL DEFAULT NULL,
       		gateway_ref_code varchar(100) COLLATE utf8_persian_ci NULL DEFAULT NULL COMMENT 'رسید درگاه',
			res_code varchar(100) COLLATE utf8_persian_ci NULL DEFAULT NULL,
			status enum('unpaid','paid') COLLATE utf8_persian_ci NOT NULL DEFAULT 'unpaid' COMMENT 'وضعیت پرداخت تراکنش',
    		final_status enum('','success') COLLATE utf8_persian_ci NULL DEFAULT NULL COMMENT 'وضعیت نهایی خرید',
    
    		bill_id varchar(100) COLLATE utf8_persian_ci NULL DEFAULT NULL,
			pay_id varchar(100) COLLATE utf8_persian_ci NULL DEFAULT NULL,
			bill_type varchar(100) COLLATE utf8_persian_ci NULL DEFAULT NULL,
    		check_bill_result text COLLATE utf8_persian_ci NULL DEFAULT NULL,
			url text COLLATE utf8_persian_ci NULL DEFAULT NULL,
			save_mobile enum('','1') COLLATE utf8_persian_ci NULL DEFAULT NULL,
			date datetime NULL DEFAULT NULL,
			pay_date datetime NULL DEFAULT NULL,
			pay_result text COLLATE utf8_persian_ci NULL DEFAULT NULL COMMENT 'پاسخ درخواست خرید',
    		description text COLLATE utf8_persian_ci NULL DEFAULT NULL,
    		mode enum('','test_mode','bulk') COLLATE utf8_persian_ci NULL DEFAULT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	add_option( 'inax_db_version', $inax_db_version );
}
register_activation_hook( INAX_Main_File_Path , 'inax_install' );

//delete databse whene delete plugins
function inax_remove_database(){
	global $wpdb;
	$inax_charge_db = $wpdb->prefix . 'inax_charge';
	$wpdb->query( "DROP TABLE IF EXISTS $inax_charge_db" );

	$inax_bill_db = $wpdb->prefix . 'inax_bill';
	$wpdb->query( "DROP TABLE IF EXISTS $inax_bill_db" );

	delete_option("inax_options");
	delete_option("inax_version");
	delete_option("inax_db_version");
	delete_option("inax_payment_gateway");
	delete_option("inax_do_activation");
}
//register_deactivation_hook( INAX_Main_File_Path , 'inax_remove_database' );
register_uninstall_hook(INAX_Main_File_Path , 'inax_remove_database');

//update databse
function inax_plugin_update(){
	global $wpdb, $inax_db_version,$inax_bill_db;
	$manually = false;

	//add_option('inax_test','salam' ) OR update_option('inax_test', 'salam2' );

	if( (get_option( 'inax_db_version' ) != $inax_db_version) || $manually==true ){
		$inax_charge_db = $wpdb->prefix . 'inax_charge';
		$inax_bill_db   = $wpdb->prefix . 'inax_bill';//dont delete yet

		//repaired in version 1.4 (1.3 and earlier versions don't have default value)
		if( ( get_option( 'inax_db_version' )<1.1 ) || $manually==true ){////repaired in version 1.4 (1.3 and earlier versions don't have default value)

			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'") ){//if $inax_charge_db exist
				$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE date date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';");
				$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE pay_result pay_result TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT '';");
			}

			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'") ){
				$wpdb->query( "ALTER TABLE $inax_bill_db CHANGE url url TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT '';");

				if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'pay_bill_result';" ) ){
					$wpdb->query("ALTER TABLE $inax_bill_db CHANGE pay_bill_result pay_bill_result TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT '';");
				}

				$wpdb->query( "ALTER TABLE $inax_bill_db CHANGE check_bill_result check_bill_result TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT '';");
			}
		}

		if( ( get_option( 'inax_db_version' )<1.2 ) || $manually==true ){//inax version 1.5
			//if not exist "payment_type" add it
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'payment_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db ADD payment_type ENUM('', 'online', 'credit') NOT NULL DEFAULT '' AFTER status;");
			}

			//if exist "refcode" change to "ref_code"
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'refcode';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db CHANGE refcode ref_code VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT '';");
			}

			//if exist "pay_bill_result" change to "pay_result"
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'pay_bill_result';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db CHANGE pay_bill_result pay_result TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT ''; ");
			}
		}

		if( ( get_option( 'inax_db_version' )<1.3 ) || $manually==true ){//changed in plugin version 1.8
			//if exist "pay_type" change to "payment_type"
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'pay_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db CHANGE pay_type payment_type TEXT CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT ''; ");
			}
		}

		if( ( get_option( 'inax_db_version' )<1.4 ) || $manually==true ){//changed in plugin version 2.1

			//add gateway if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'gateway';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD gateway varchar(50) NOT NULL DEFAULT '' AFTER payment_type;");
			}
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'gateway';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db ADD gateway varchar(50) NOT NULL DEFAULT '' AFTER payment_type;");
			}

			//add charge_status if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'charge_status';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD charge_status enum('','success') NOT NULL DEFAULT '' AFTER status;");
			}

			//add bill_status if not exist
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'bill_status';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db ADD bill_status enum('','success') NOT NULL DEFAULT '' AFTER status;");
			}

			//add gateway_ref_code for inax_charge_db if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'gateway_ref_code';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD gateway_ref_code varchar(100) NULL DEFAULT NULL AFTER ref_code;");
			}
			//add gateway_ref_code for inax_bill_db if not exist
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'gateway_ref_code';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db ADD gateway_ref_code varchar(100) NULL DEFAULT NULL AFTER ref_code;");
			}

			//add charge_type if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'charge_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD charge_type varchar(20) NOT NULL DEFAULT '' AFTER mobile;");
			}
			// if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'charge_type';" ) ){
			//$wpdb->query( "ALTER TABLE $inax_bill_db ADD charge_type varchar(20) NOT NULL DEFAULT '' AFTER mobile;");
			// }

			//add payment_type if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'payment_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD payment_type enum('','online','credit') NOT NULL DEFAULT '' AFTER payment_type;");
			}
			//add payment_type if not exist
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'payment_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db ADD payment_type enum('','online','credit') NOT NULL DEFAULT '' AFTER payment_type;");
			}

			//add operator if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'operator';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD operator varchar(10) NOT NULL DEFAULT '' AFTER mobile;");
			}

			//add internet_type if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'internet_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD internet_type varchar(10) NOT NULL DEFAULT '' AFTER charge_type ;");
			}

			//add sim_type if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'sim_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD sim_type enum('', 'credit', 'permanent', 'TDLTE_credit', 'TDLTE_permanent', 'data') NOT NULL DEFAULT '' AFTER internet_type ;");
			}

			//add product_id if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'product_id';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD product_id varchar(10) NOT NULL DEFAULT '' AFTER sim_type ;");
			}

			//update charge_status=success
			$wpdb->query( "update $inax_charge_db set charge_status='success' where status='paid' ");
			$wpdb->query( "update $inax_bill_db set bill_status='success' where status='paid' ");

			//set payment_type equal by payment_type for old transaction
			//$wpdb->query( "update $inax_charge_db set charge_payment_type=payment_type ");
			//$wpdb->query( "update $inax_bill_db set bill_payment_type=payment_type  ");

			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'product_type';" ) ){
				$wpdb->query( "update $inax_charge_db set operator=product_type ");
				$wpdb->query(" ALTER TABLE $inax_charge_db DROP product_type ");
			}
		}

		if( ( get_option( 'inax_db_version' )<1.5 ) || $manually==true ){//added in plugin version 2.2
			//add mnp if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'mnp';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD mnp enum('', '1') NOT NULL DEFAULT '' AFTER mobile ;");
			}
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'description';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD description text COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER pay_result ;");
			}
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'description';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db ADD description text COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER gateway ;");
			}
		}

		if( ( get_option( 'inax_db_version' )<1.6 ) || $manually==true ){//added in plugin version 2.3
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'trans_id';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD trans_id varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER ref_code ;");
			}

			$wpdb->query( "ALTER TABLE wp_inax_charge CHANGE sim_type sim_type enum('', 'credit', 'permanent', 'TDLTE_credit', 'TDLTE_permanent', 'data') ");

			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'trans_id';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db ADD trans_id varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER ref_code ;");
			}
		}

		if( ( get_option( 'inax_db_version' )<1.7 ) || $manually==true ){//added in plugin version 2.7
			//remove email
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'email';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db DROP email;");
			}
			//remove platform
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'platform';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db DROP platform;");
			}

			//create payment_type if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'payment_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD payment_type enum('','online','credit') COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER check_charge ;");
			}

			//add gateway_order_id if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'gateway_order_id';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD gateway_order_id varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' COMMENT 'شماره سفارش درگاه' AFTER gateway ;");
			}

			//copy charge_payment_type to payment_type and drop charge_payment_type
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'charge_payment_type';" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'payment_type';" ) ){
				$wpdb->query( "update $inax_charge_db set payment_type = charge_payment_type where payment_type='' ");
				$wpdb->query("ALTER TABLE $inax_charge_db DROP charge_payment_type ");
			}

			//create payment_type if not exist for bill table
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'payment_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db ADD payment_type enum('','online','credit') COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER check_charge ;");
			}

			//copy data from bill_payment_type  to payment_type and delete bill_payment_type if exist
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'bill_payment_type';" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'payment_type';" ) ){
				$wpdb->query( "update $inax_bill_db set payment_type = bill_payment_type where payment_type='' ");
				$wpdb->query("ALTER TABLE $inax_bill_db DROP bill_payment_type ");
			}

			//rename charge_status to final_status for $inax_charge_db
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'charge_status';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE charge_status final_status ENUM('','success') CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT '' COMMENT 'وضعیت نهایی خرید'; ");
			}

			//rename bill_status to final_status for $inax_bill_db
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) && $wpdb->get_var( "SHOW COLUMNS FROM `{$inax_bill_db}` LIKE 'bill_status';" ) ){
				$wpdb->query( "ALTER TABLE $inax_bill_db CHANGE bill_status final_status ENUM('','success') CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT '' COMMENT 'وضعیت نهایی خرید'; ");
			}

			//add final_status if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'final_status';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD final_status enum('','success') COLLATE utf8_persian_ci NOT NULL DEFAULT '' COMMENT 'وضعیت نهایی خرید' AFTER status ;");
			}

			//add bill_id to charge table
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'bill_id';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD bill_id varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER final_status ;");
			}

			//add pay_id to charge table
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'pay_id';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD pay_id varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER bill_id ;");
			}

			//add bill_type to charge table
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'bill_type';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD bill_type varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER pay_id ;");
			}

			//add check_bill_result to charge table
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'check_bill_result';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD check_bill_result text COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER bill_type ;");
			}

			//add url to charge table
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'url';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD url text COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER check_bill_result ;");
			}

			//add ref_code to charge table
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'ref_code';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD ref_code varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER gateway ;");
			}

			//add trans_id to charge table
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'trans_id';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD trans_id varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' AFTER ref_code ;");
			}

			//add mode
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'mode';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD mode enum('','test_mode') COLLATE utf8_persian_ci NULL DEFAULT NULL;");
			}

			//add 'bill' to type column of inax_charge_db
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE type type ENUM('','topup','pin','internet','bill') CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT '' ;");

			//copy bill table to charge table
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) ){
				$wpdb->query( "insert into $inax_charge_db (client_id,type,bill_id,pay_id,mobile,bill_type,url,amount,date,pay_date,ref_code,trans_id,gateway_ref_code,check_bill_result,pay_result,status,final_status,payment_type,gateway,description) select client_id,'bill',bill_id,pay_id,mobile,bill_type,url,amount,date,pay_date,ref_code,trans_id,gateway_ref_code,check_bill_result,pay_result,status,final_status,payment_type,gateway,description from $inax_bill_db;");
			}

			//drop bil table
			if( $inax_bill_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_bill_db'" ) ){
				//$wpdb->query( "DROP TABLE $inax_bill_db");
			}

			//change order_id from int to varchar AND add comment
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE order_id order_id varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' COMMENT 'شماره سفارش آینکس' ");

			//add comment
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE ref_code ref_code varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' COMMENT 'رسید آینکس' ");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE gateway_ref_code gateway_ref_code varchar(100) COLLATE utf8_persian_ci NOT NULL DEFAULT '' COMMENT 'رسید درگاه' ");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE status enum('unpaid','paid') COLLATE utf8_persian_ci NOT NULL DEFAULT 'unpaid' COMMENT 'وضعیت پرداخت تراکنش' ");

			//add default null value for following columns
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE type type enum('','topup','pin','internet','bill') NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE mobile mobile varchar(11) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE mnp mnp enum('', '1') NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE operator operator varchar(10) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE charge_type charge_type varchar(20) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE internet_type internet_type varchar(50) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE sim_type sim_type enum('', 'credit', 'permanent', 'TDLTE_credit', 'TDLTE_permanent', 'data') NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE product_id product_id varchar(50) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE order_id order_id varchar(100) NULL DEFAULT NULL COMMENT 'شماره سفارش آینکس';");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE check_charge check_charge varchar(10) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE payment_type payment_type enum('','online','credit') NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE gateway gateway varchar(50) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE gateway_order_id gateway_order_id varchar(100) NULL DEFAULT NULL COMMENT 'شماره سفارش درگاه';");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE ref_code ref_code varchar(100) NULL DEFAULT NULL COMMENT 'رسید آینکس';");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE trans_id trans_id varchar(100) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE gateway_ref_code gateway_ref_code varchar(100) NULL DEFAULT NULL COMMENT 'رسید درگاه';");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE res_code res_code varchar(100) NULL DEFAULT NULL;");
			//$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE status status enum('unpaid','paid') NULL DEFAULT NULL COMMENT 'وضعیت پرداخت تراکنش';");//default must be unpaid
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE final_status final_status enum('','success') NULL DEFAULT NULL COMMENT 'وضعیت نهایی خرید';");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE bill_id bill_id varchar(100) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE pay_id pay_id varchar(100) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE bill_type bill_type varchar(100) NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE check_bill_result check_bill_result text NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE url url text NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE date date datetime NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE pay_date pay_date datetime NULL DEFAULT NULL;");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE pay_result pay_result text NULL DEFAULT NULL COMMENT 'پاسخ درخواست خرید';");
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE description description text NULL DEFAULT NULL;");
		}

		if( ( get_option( 'inax_db_version' )<1.8 ) || $manually==true ){//added in plugin version 3.1
			//add bulk mode
			$wpdb->query( "ALTER TABLE $inax_charge_db CHANGE mode mode ENUM('','test_mode','bulk') CHARACTER SET utf8 COLLATE utf8_persian_ci NULL DEFAULT NULL; ");

			//add product_name if not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'product_name';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD product_name varchar(150) COLLATE utf8_persian_ci NULL DEFAULT null AFTER product_id ;");
			}
		}

		if( ( get_option( 'inax_db_version' )<1.9 ) || $manually==true ){//added in plugin version 3.2

			//if save_mobile not exist
			if( $inax_charge_db === $wpdb->get_var( "SHOW TABLES LIKE '$inax_charge_db'" ) && !$wpdb->get_var( "SHOW COLUMNS FROM `{$inax_charge_db}` LIKE 'save_mobile';" ) ){
				$wpdb->query( "ALTER TABLE $inax_charge_db ADD save_mobile enum('','1') COLLATE utf8_persian_ci NULL DEFAULT NULL AFTER url ;");
			}
		}

		update_option( "inax_db_version", $inax_db_version );
	}
}
add_action( 'plugins_loaded', 'inax_plugin_update' );

// change the global $inax_db_version variable
/*function inax_update_db_check(){
    global $inax_db_version;
    if( get_site_option( 'inax_db_version' ) != $inax_db_version ){
        inax_install();
    }
}
add_action( 'plugins_loaded', 'inax_update_db_check' );*/
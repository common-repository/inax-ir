function check_bill(){
	jQuery(function ($) {//check if jquery exist
		const bill_id 	= jQuery('#bill_id').val();
		const pay_id	= jQuery('#pay_id').val();
		const mobile	= jQuery('#mobile').val();
		const Token		= jQuery('#Token').val();
		const mode		= jQuery('#mode').val();

		$('#myModal').modal('show');
		$('#modal_result').fadeOut(1);
		$('#modal_loading').fadeIn(200);
		
		$.ajax({
		url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
		type: 'POST',
		data:{
			action: 'check_bill', // this is the function in your functions.php that will be triggered
			bill_id: bill_id,
			pay_id: pay_id,
			mobile: mobile,
			Token: Token,
			mode: mode
		},
		success: function( response ){
			//console.log( response );
			const obj = JSON.parse(response);
			//const obj = JSON.parse(JSON.stringify(response));
			//console.log( "salam" );
			//console.log( obj );
			if( obj.error_msg =='no'){
				$('.display_bill_type').html( obj.type );
				$('.display_bill_amount').html( obj.amount + ' ریال' );

				//check if not empty
				if( obj.pay_type_fa ) {
					$('.display_pay_type').html( obj.pay_type_fa );
					$("#tr5").show();
				}

				$('.bill_dbid').val( obj.bill_dbid );
				$('.display_error_msg').html( '' );
				$("#tr1,#tr2,#tr3").show();
				$("#error").hide();
			}else{
				$('.display_bill_type,.display_bill_amount,.display_pay_type').html( 'تعریف نشده' );
				$('.display_error_msg').html( obj.error_msg );
				$("#error").show();
				$("#tr1,#tr2,#tr3,#tr5").hide();
			}
			
			$('#modal_loading').fadeOut(300);
			$('#modal_result').delay(300).fadeIn(500);
		}
	  });
	});	
}
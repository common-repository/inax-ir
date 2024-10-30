function number_to_letter(input_id) {
    let get_number;
    if( input_id==null ){
        get_number = document.getElementById("amount_field");
    }else{
        get_number = document.getElementById(input_id);
    }

    let numbers = persian_to_English_Numbers(get_number.value);
    numbers = numbers.replace(/[^0-9.]/g, ''); //only keep numbers
    const en_num = numbers.replace(",", "");  // remove comma

    //add comma to txtbox
    const add_comma = Comma(en_num);
    get_number.value	= add_comma;
}

/*function disable_submit_btn(){

}*/

function inax_check_numbers(input_id){
    let get_mobile  = document.getElementById(input_id);
    let numbers     = persian_to_English_Numbers(get_mobile.value);
    numbers         = numbers.replace(/[^0-9.]/g, ''); //only keep numbers

    /*if( numbers.length>2 && numbers.substring(0, 2) != '09') {
        numbers = '';
    }*/

    //empty use_saved_mobile to empty value if exist
    if( document.getElementById("use_saved_mobile") ){
        const $select = document.querySelector('#use_saved_mobile');
        $select.value = ''
    }

    get_mobile.value	= numbers;
}

function inax_check_numbers2(input_id,page){
    let get_mobile  = document.getElementById(input_id);
    let number     = persian_to_English_Numbers(get_mobile.value);
    number         = number.replace(/[^0-9.]/g, ''); //only keep numbers
   //console.log('number ' + number.length);

    get_mobile.value	= number;

    var evt = window.event || arguments.callee.caller.arguments[0];
    //console.log(evt.type);

    let obj = {};
    obj['MTN'] = ['0901','0902','0903','0904','0905','0930','0933','0935','0936','0937','0938','0939','0941','0900'];
    obj['MCI'] = ['0910','0911','0912','0913','0914','0915','0916','0917','0918','0919','0990','0991','0992','0993','0994','0995','0996'];
    obj['RTL'] = ['0920','0921','0922','0923'];
    obj['SHT'] = ['0998'];

    if( number.length==0 || number.length==1 || number.length==2 || number.length==3 ){
        //disable operators
        Object.entries(obj).forEach(entry => {
            const [op, op_numbers] = entry;
            //console.log(op, op_numbers);
            
            var img = document.querySelector(".operator_" + op);//operator_MTN - operator_MCI ...

            //add .gray_img class
            img.classList.add("gray_img");

            //remove border
            document.querySelector(".card_" + op).classList.remove("border-primary");
            document.querySelector(".card_" + op).classList.remove("border-warning");
            document.querySelector(".card_" + op).classList.remove("border-info");
            document.querySelector(".card_" + op).classList.remove("border-perple");
        });
    }
    else if( (number.length==4 || number.length==11) && number.substring(0, 2) == '09') {
        //enable operator by mobile number
        //console.log('select operator');

        if(number.length==11){
            number = number.substring(0, 4)
        }
        //console.log('select operator four ' + number);

        //do foreach operator array
        Object.entries(obj).forEach(entry => {
            const [op, op_numbers] = entry;
            //console.log(op, op_numbers);
            
            var img = document.querySelector(".operator_" + op);//operator_MTN - operator_MCI ...

            //add border color to operator card
            let border_class = "secondary";
            if(op=='MTN'){
                border_class = "border-warning";
            }else if( op=='MCI'){
                border_class = "border-info";
            }else if( op=='RTL'){
                border_class = "border-perple";
            }else if( op=='SHT'){
                border_class = "border-primary";
            }

            //check if four digit number exist in op_numbers array enable that operator
            //on mouseout dont change operator if pre checked operator
            var any_op_checked = false;
            if( evt.type=='mouseout' ){
                Object.entries(obj).forEach(entry => {
                    const [op2, op_numbers41] = entry;
                    console.log( op2 + " " + document.getElementById("operator_" + op2).checked )
                    if(document.getElementById("operator_" + op2).checked==true){
                        any_op_checked = true;
                        return false // "break"
                    }
                });
            }
            
            if( any_op_checked==false ){
                if(op_numbers.indexOf(number) !== -1){
                    //console.log("Yes " + op);
                    img.classList.remove("gray_img");
    
                    //add border
                    document.querySelector(".card_" + op).classList.add(border_class);
    
                    //set checkbox as checked
                    document.getElementById("operator_" + op).checked = true;
    
                    //display sim_type radio btns
                    if(page=='internet'){
                        sim_type_auto(op,event);
                    }
                }else{
                    img.classList.add("gray_img");
    
                    //remove border
                    document.querySelector(".card_" + op).classList.remove(border_class);
                }
            }
        });
    }   
}

function persian_to_English_Numbers(a) {
    if (typeof a === "undefined") {
        return
    }
    a = a.toString();
    a = a.replace(/۰/g, "0");
    a = a.replace(/۱/g, "1");
    a = a.replace(/۲/g, "2");
    a = a.replace(/۳/g, "3");
    a = a.replace(/۴/g, "4");
    a = a.replace(/۵/g, "5");
    a = a.replace(/۶/g, "6");
    a = a.replace(/۷/g, "7");
    a = a.replace(/۸/g, "8");
    a = a.replace(/۹/g, "9");
    return a
}

function Comma(Num) {
    Num += '';
    Num = Num.replace(',', ''); Num = Num.replace(',', ''); Num = Num.replace(',', '');
    Num = Num.replace(',', ''); Num = Num.replace(',', ''); Num = Num.replace(',', '');
    let x = Num.split('.');
    let x1 = x[0];
    let x2 = x.length > 1 ? '.' + x[1] : '';
    const rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1))
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    return x1 + x2;
}

function opeartor_handleClick(Radio) {
    if( Radio.value=='custom_amount' ){
        document.getElementById("custom").style.display = '';
        document.getElementById("amount_field").setAttribute('required', '');
    }else{
        document.getElementById("custom").style.display = 'none';
        document.getElementById("amount_field").removeAttribute('required');
    }
}

function handle_saved_mobile(page){
    const selectfrom = document.getElementById("use_saved_mobile");
    const selectedValue = selectfrom.options[selectfrom.selectedIndex].value;

    //set mobile number to mobile textbox
    document.getElementById('inlineFormInputGroup').value = selectedValue;

    //change operator by selected mobile number
    inax_check_numbers2('inlineFormInputGroup',page);
}
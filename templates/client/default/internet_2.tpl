{include file="client/$inax_theme/header.tpl"}

<div class="row">
    <div class="col-lg-12">

    {if isset($buy_internet) && !isset($operator) }
        <div class="card shadow mb-4 p-2 border-default">
            <div class="card-header mb-2 p-2 text-right">
                <i class="fa fa-shopping-cart fa-fw"></i>
                {$title} {if $is_user_logged_in eq 1}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$trans_link}"><i class="fa fa-database fa-fw"></i> {__('لیست تراکنش ها',"inax")}</a>{/if}
                {if isset($main_link)}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none mx-1 inax-font" href="{$main_link}"><i class="fa fa-home fa-fw"></i> {__('صفحه اصلی فروشگاه',"inax")}</a>{/if}
            </div>
            <div class="card-body mt-2 text-right p-0">
                <div class="alert alert-primary">{__('لطفا شماره موبایل، اپراتور و نوع سیم کارت تلفن همراه خود را انتخاب نمائید',"inax")}</div>

                {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
                {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

                <form action="{$permalink}" method="POST">
                    {$wordpress_csrf}

                    <div class="row row-cols-1 row-cols-sm-12 row-cols-md-12 g-12">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon2"><i class="fa fa-phone fa-fw fa-1x"></i></span>
                            <input type="tel" class="form-control text-center" onmouseout="inax_check_numbers2('inlineFormInputGroup','internet');" onkeyup="inax_check_numbers2('inlineFormInputGroup','internet');" id="inlineFormInputGroup" maxlength="11" aria-label="{__('شماره تلفن همراه',"inax")}" aria-describedby="basic-addon1" placeholder="{__('شماره تلفن همراه',"inax")}" dir="ltr" name="mobile" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile}{/if}" tabindex="1" required>
                        </div>
                    </div>

                    {if $is_user_logged_in eq 1}
                    <div class="row row-cols-1 row-cols-sm-12 row-cols-md-12 g-12">
                        <div class="mb-3 text-center">
                            <input type="checkbox" name="save_mobile" value="1" {if isset($smarty.post.save_mobile)}checked="checked"{/if} tabindex="2" id="save_mobile" >
                            <label for="save_mobile" > {__('ذخیره شماره برای استفاده های بعدی',"inax")}</label>
                            {if isset($saved_mobile_rows)}
                                <select name="use_saved_mobile" id="use_saved_mobile" onchange="handle_saved_mobile('internet')" class="form-control" style="display: unset;width:unset;"> 
                                    <option value="">{__('استفاده از شماره های ذخیره شده',"inax")}</option>
                                    {foreach from=$saved_mobile_rows key=key item=link}
                                    <option value="{$link.mobile}">{$link.mobile}</option>
                                    {/foreach}
                                </select>
                            {/if}
                        </div>
                    </div>
                    {/if}

                    <div class="album py-2 bg-light" >
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                            <div class="col text-center">
                                <div class="card shadow-sm border-2 card_MTN {if isset($smarty.post.operator) && $smarty.post.operator eq 'MTN' }border-warning{/if}"><!-- border-warning -->
                                    <input {if isset($smarty.post.operator) && $smarty.post.operator eq 'MTN' }checked="checked"{/if}  id="operator_MTN" type="radio" name="operator" class="hide-checkbox-circle" value="MTN" tabindex="3" />
                                    <label class="" for="operator_MTN" onclick="handle_checkbox('MTN');">
                                        <img class="bd-placeholder-img card-img-top operator_MTN {if (empty($smarty.post.operator) || isset($smarty.post.operator) && $smarty.post.operator neq 'MTN') }gray_img{/if}" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}mtn.png{else}mtn_en.png{/if}" class="card-img-top" alt="شارژ مستقیم ایرانسل">
                                    </label>
                                </div>
                            </div>
                            <div class="col text-center">
                                <div class="card shadow-sm border-2 card_MCI {if isset($smarty.post.operator) && $smarty.post.operator eq 'MCI' }border-info{/if}"><!-- border-info -->
                                    <input {if isset($smarty.post.operator) && $smarty.post.operator eq 'MCI' }checked="checked"{/if} id="operator_MCI" type="radio" name="operator" class="hide-checkbox-circle" value="MCI" tabindex="3" />
                                    <label class="" for="operator_MCI" onclick="handle_checkbox('MCI');">
                                        <img class="bd-placeholder-img card-img-top operator_MCI {if (empty($smarty.post.operator) || isset($smarty.post.operator) && $smarty.post.operator neq 'MCI') }gray_img{/if}" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}mci.png{else}mci_en.png{/if}" class="card-img-top" alt="شارژ مستقیم همراه اول">
                                    </label>
                                </div>
                            </div>
                            <div class="col text-center ">
                                <div class="card shadow-sm border-2 card_RTL {if isset($smarty.post.operator) && $smarty.post.operator eq 'RTL' }border-perple{/if}"><!-- border-perple -->
                                    <input {if isset($smarty.post.operator) && $smarty.post.operator eq 'RTL' }checked="checked"{/if} id="operator_RTL" type="radio" name="operator" class="hide-checkbox-circle" value="RTL" tabindex="3" />
                                    <label class="" for="operator_RTL" onclick="handle_checkbox('RTL');">
                                        <img class="bd-placeholder-img card-img-top operator_RTL {if (empty($smarty.post.operator) || isset($smarty.post.operator) && $smarty.post.operator neq 'RTL') }gray_img{/if}" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}rtl.png{else}rtl_en.png{/if}" class="card-img-top" alt="شارژ مستقیم رایتل">
                                    </label>
                                </div>
                            </div>
                            <div class="col text-center ">
                                <div class="card shadow-sm border-2 card_SHT {if isset($smarty.post.operator) && $smarty.post.operator eq 'SHT' }border-primary{/if}"><!-- border-primary -->
                                    <input {if isset($smarty.post.operator) && $smarty.post.operator eq 'SHT' }checked="checked"{/if} id="operator_SHT" type="radio" name="operator" class="hide-checkbox-circle" value="SHT" tabindex="3" />
                                    <label class="" for="operator_SHT" onclick="handle_checkbox('SHT');" ><!-- operator_SHT -->
                                        <img class="bd-placeholder-img card-img-top operator_SHT {if (empty($smarty.post.operator) || isset($smarty.post.operator) && $smarty.post.operator neq 'SHT') }gray_img{/if}" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}sht.png{else}sht_en.png{/if}" class="card-img-top" alt="شارژ مستقیم شاتل موبایل">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>    
                    <hr/>

                    <div class="row row-cols-1 row-cols-sm-12 row-cols-md-12 g-12">
                        <div class="cc-selector">
                            <span class="radio_sim inax-font" style="display:none;" id="sim_credit" ><input {if isset($smarty.post.sim) && $smarty.post.sim eq 'sim_credit' }checked="checked"{/if} id="sim_credit_1" type="radio" name="sim" value="credit" tabindex="3" required/> <label for="sim_credit_1"> {__('سیم کارت اعتباری',"inax")}</label></span>
                            <span class="radio_sim inax-font" style="display:none;" id="sim_permanent"><input {if isset($smarty.post.sim) && $smarty.post.sim eq 'sim_permanent' }checked="checked"{/if} id="sim_permanent_1" type="radio" name="sim" value="permanent" tabindex="3" required/> <label for="sim_permanent_1"> {__('سیم کارت دائمی',"inax")}</label></span>
                            <span class="radio_sim inax-font" style="display:none;" id="sim_TDLTE_credit" ><input {if isset($smarty.post.sim) && $smarty.post.sim eq 'sim_TDLTE_credit' }checked="checked"{/if} id="sim_TDLTE_credit_1" type="radio" name="sim" value="TDLTE_credit" tabindex="3" required/> <label for="sim_TDLTE_credit_1"> {__('سیم کارت TD-LTE اعتباری','inax')}</label></span>
                            <span class="radio_sim inax-font" style="display:none;" id="sim_TDLTE_permanent"><input {if isset($smarty.post.sim) && $smarty.post.sim eq 'sim_TDLTE_permanent' }checked="checked"{/if} id="sim_TDLTE_permanent_1" type="radio" name="sim" value="TDLTE_permanent" tabindex="3" required/> <label for="sim_TDLTE_permanent_1"> {__('سیم کارت TD-LTE دائمی','inax')}</label></span>
                            <span class="radio_sim inax-font" style="display:none;" id="sim_data"><input {if isset($smarty.post.sim) && $smarty.post.sim eq 'sim_data' }checked="checked"{/if} id="sim_data_1" type="radio" name="sim" value="data" tabindex="3" required/> <label for="sim_data_1" > {__('سیم کارت دیتا','inax')}</label></span>                 
                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-sm-12 row-cols-md-12 g-12">
                        <div class="input-group mb-3">
                            <button class="btn btn-primary form-control border-primary inax-font" name="continue" type="submit"><i class="fa fa-check"></i> {__('ادامه',"inax")}</button>
                        </div>
                    </div>
                </form>
                
                <script>
                function handle_checkbox(operator) {
                    const operators = ['MTN', 'MCI', 'RTL', 'SHT'];
                    operators.forEach((op) => {
                        var img = document.querySelector(".operator_" + op);//operator_MTN - operator_MCI ...

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

                        //console.log( op );
                        if(op==operator){
                            img.classList.remove("gray_img");

                            //add border
                            document.querySelector(".card_" + op).classList.add(border_class);
                        }else{
                            img.classList.add("gray_img");

                            //remove border
                            document.querySelector(".card_" + op).classList.remove(border_class);
                        }
                    });

                    sim_type_auto(operator,event);
                    //console.log(operator);
                }

                function sim_type_auto(operator, event){
                    //console.log(event.target)
                    //console.log(event.type)

                    //uncheck all radio btn if change operator by click on operator icon or changing mobile number 
                    if(event.type!='mouseout'){
                        //console.log('clear checked');
                        
                        var ele = document.getElementsByName("sim");
                        for(var i=0;i<ele.length;i++){
                            ele[i].checked = false;
                        }                     
                    }

                    //hide all sim type except sim_credit
                    document.getElementById("sim_permanent").style.display = 'none';
                    document.getElementById("sim_TDLTE_credit").style.display = 'none';
                    document.getElementById("sim_TDLTE_permanent").style.display = 'none';
                    document.getElementById("sim_data").style.display = 'none';

                    document.getElementById("sim_credit").style.display = '';

                    if( operator=='MTN' || operator=='MCI' || operator=='RTL' ){
                        document.getElementById("sim_permanent").style.display = '';
                    }
                    if( operator=='MTN' || operator=='MCI' ){
                        document.getElementById("sim_TDLTE_credit").style.display = '';
                        document.getElementById("sim_TDLTE_permanent").style.display = '';
                    }
                    if( operator=='MCI' ){
                        document.getElementById("sim_data").style.display = '';
                    }

                }
                </script>
            </div>
        </div>
    {/if}

    {if isset($package_list) && $package_list }
        <div class="card bg-{if isset($mtn_active) }warning{elseif isset($mci_active)}info{elseif isset($rtl_active)}perple{elseif isset($sht_active)}info{/if}">
            <div class="card-header text-right  {if isset($mtn_active) }text-dark{elseif isset($mci_active)}text-light{elseif isset($rtl_active)}text-light{elseif isset($sht_active)}text-light{/if}">
                <i class="fa fa-shopping-cart fa-fw"></i> {__('خرید بسته اینترنت',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{elseif isset($sht_active)}{__('شاتل موبایل',"inax")}{/if}
                <a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$p_url}"> {__('بازگشت',"inax")}
                    <i class="fa fa-arrow-left fa-fw"></i></a>
            </div>
            <div class="card-body bg-light">

                {if isset($success_msg) }
                    <div class="alert alert-success">{$success_msg}</div>{/if}
                {if isset($error_msg) }
                    <div class="alert alert-danger">{$error_msg}</div>{/if}

                {if !isset($error_msg) }
                    <div class="alert alert-info">{__('لطفا نوع بسته اینترنت را انتخاب نمائید',"inax")}</div>

                    <form action="{$permalink}" method="POST">
                    {$wordpress_csrf}

                    {if isset($have_package)}
                        <div class="accordion" id="accordionExample">
                            {foreach from=$have_package key=key item=link}
                                <div class="accordion-item">
                                    <h2 class="accordion-header inax-font" id="heading_{$link.type_en}">
                                        <button class="accordion-button {if !$link@first }collapsed{/if}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{$link.type_en}" aria-expanded="{if $link@first }true{else}false{/if}" aria-controls="collapse_{$link.type_en}">
                                            {if $language_code eq 'fa'}{$link.type_fa}{else}{$link.type_en}{/if} ({$link.lists2|count} {__('بسته',"inax")})
                                        </button>
                                    </h2>
                                    <div id="collapse_{$link.type_en}" class="accordion-collapse collapse {if $link@first }show{/if}" aria-labelledby="heading_{$link.type_en}" data-bs-parent="#accordionExample">
                                        <div class="accordion-body ">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover listtable" style="border-collapse: separate;border:1px solid #BCBCBC;border-radius:7px !important;">
                                                    <thead >
                                                    <tr>
                                                        <th class="text-center d-none d-sm-block">{__('ردیف',"inax")}</th>
                                                        <th class="text-center">{__('نام بسته',"inax")}</th>
                                                        <th class="text-center">{__('قیمت',"inax")}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    {if isset($link.lists2 )}
                                                        {foreach from=$link.lists2 key=key2 item=link2}
                                                        <tr onclick="selectRow(this,event)" class="change_bg" style="cursor: pointer;" >
                                                            <td class="text-center d-none d-sm-block" style="padding: 13px 3px 11px 3px !important;">{$link2@iteration }</td>
                                                            <td class="text-center" style="padding: 13px 3px 11px 3px !important;">
                                                                <input type="radio" name="pid" value="{$link.type_en}-{$link2.id}" id="{$link2.id}" required/>
                                                                <label for="{$link2.id}" > {$link2.name}</label>
                                                            </td>
                                                            <td class="text-center text-dark" style="padding: 13px 3px 11px 3px !important;"> {$link2.amount|number_format} {__('تومان',"inax")}</td>
                                                        </tr>
                                                        {/foreach}
                                                    {else}
                                                        <tr>
                                                            <td colspan="3" class="text-center">{__('بسته ای یافت نشد',"inax")}</td>
                                                        </tr>
                                                    {/if}

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    {/if}

                    <script>
                        function selectRow(row, event){
                            //console.log(row);
                            if (event.target.nodeName !== "INPUT"){
                                var firstInput = row.getElementsByTagName('input')[0];
                                //firstInput.checked = !firstInput.checked;//toggle check
                                firstInput.checked = true;
                            }

                            //remove bg-info for all rows if exist 
                            var elements = document.getElementsByTagName('tr');
                            for (var i = 0; i < elements.length; i++) {
                                if(hasClass(elements[i], "bg-info")){
                                    elements[i].classList.remove("bg-info");
                                }
                            }

                            //add class for selected row if not exist
                            if( hasClass(row, "bg-info")==false) {
                                row.classList.add("bg-info");
                            }
                        }
                        function hasClass(element, className){
                            return (' ' + element.className + ' ').indexOf(' ' + className+ ' ') > -1;
                        }
                    </script>

                    <div class="text-center">
                        <hr/>
                        <button class="btn btn-success text-white btn-sm inax-font" name="submit" type="submit"><i class="fa fa-check"></i> {__('پرداخت آنلاین',"inax")}</button>
                        {if isset($credit_payment)}<button class="btn btn-warning border-success m-1 ext-white btn-sm inax-font" {if $user_credit eq 0}disabled{/if} name="submit_credit" type="submit"><i class="fa fa-check"></i> {__("خرید از کیف پول (اعتبار %s تومان)","inax")|sprintf:number_format($user_credit)}</button>{/if}
                    </div>
                    </form>
                {/if}

            </div>
        </div>
    {/if}

    </div>
</div>

{include file="client/$inax_theme/footer.tpl"}
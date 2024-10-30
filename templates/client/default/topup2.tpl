{include file="client/$inax_theme/header.tpl"}

<div class="row">
    <div class="col-lg-12">

    {if isset($buy_charge) && !isset($operator) }
        <div class="card shadow mb-4 p-2 border-default">
            <div class="card-header mb-2 p-2 text-right">
                <i class="fa fa-shopping-cart fa-fw"></i>
                {$title} {if $is_user_logged_in eq 1}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$trans_link}"><i class="fa fa-database fa-fw"></i> {__('لیست تراکنش ها',"inax")}</a>{/if}
                {if isset($main_link)}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none mx-1 inax-font" href="{$main_link}"><i class="fa fa-home fa-fw"></i> {__('صفحه اصلی فروشگاه',"inax")}</a>{/if}
            </div>
            <div class="card-body mt-2 text-right p-0">
                <div class="alert alert-primary">{__('لطفا شماره موبایل و اپراتور تلفن همراه خود را انتخاب نمائید',"inax")}</div>

                {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
                {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

                <form action="{$permalink}" method="POST">
                    {$wordpress_csrf}

                    <div class="row row-cols-1 row-cols-sm-12 row-cols-md-12 g-12">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon2"><i class="fa fa-phone fa-fw fa-1x"></i></span>
                            <input type="tel" class="form-control text-center" onmouseout="inax_check_numbers2('inlineFormInputGroup','topup');" onkeyup="inax_check_numbers2('inlineFormInputGroup','topup');" id="inlineFormInputGroup" maxlength="11" aria-label="{__('شماره تلفن همراه',"inax")}" aria-describedby="basic-addon1" placeholder="{__('شماره تلفن همراه',"inax")}" dir="ltr" name="mobile" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile}{/if}" tabindex="1" required>
                        </div>
                    </div>

                    {if $is_user_logged_in eq 1}
                    <div class="row row-cols-1 row-cols-sm-12 row-cols-md-12 g-12">
                        <div class="mb-3 text-center">
                       
                            <input type="checkbox" name="save_mobile" value="1" {if isset($smarty.post.save_mobile)}checked="checked"{/if} tabindex="2" id="save_mobile" >
                            <label for="save_mobile" > {__('ذخیره شماره برای استفاده های بعدی',"inax")}</label>
                            {if isset($saved_mobile_rows)}
                                <select name="use_saved_mobile" id="use_saved_mobile" onchange="handle_saved_mobile('topup')" class="form-control" style="display: unset;width:unset;"> 
                                    <option value="">{__('استفاده از شماره های ذخیره شده',"inax")}</option>
                                    {foreach from=$saved_mobile_rows key=key item=link}
                                    <option value="{$link.mobile}">{$link.mobile}</option>
                                    {/foreach}
                                </select>
                            {/if}

                        </div>
                    </div>
                    {/if}

                    <div class="album py-2 bg-light">
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

                    //console.log(operator);
                }
                </script>
            </div>
        </div>
    {/if}

    {if isset($operator) }
        <div class="card bg-{if isset($mtn_active) }warning{elseif isset($mci_active) }info{elseif isset($rtl_active) }perple{elseif isset($sht_active) }primary{/if}">
            <div class="card-header text-right  {if isset($mtn_active) }text-dark{elseif isset($mci_active) }text-light{elseif isset($rtl_active) }text-light{elseif isset($sht_active) }text-light{/if}">
                <i class="fa fa-shopping-cart fa-fw"></i> {__('خرید شارژ مستقیم',"inax")} {if $language_code eq 'fa'}{$operator|operator_fa}{else}{__( $operator|operator_fa,"inax")}{/if}
                <a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$p_url}">{__('بازگشت',"inax")}
                    <i class="fa fa-arrow-left fa-fw"></i>
                </a>
            </div>
            <div class="card-body mb-2 text-right p-0 bg-light">

                <div class="alert alert-info mb-0">
                    - {__('لطفا از بخش زیر شماره تلفن و مبلغ شارژ را وارد نمائید',"inax")}
                   {if isset($sht_active) }<br/>- {__('در اپراتور شاتل موبایل 9 درصد مالیات بر ارزش افزوده بر روی مبلغ تراکنش اضافه شده و سیم کارت به اندازه مبلغ اسمی شارژ می شود',"inax")}{/if}
                </div>

                {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
                {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

                <div class="table-responsive">

                    <form action="{$permalink}{$operator}" method="POST" >
                        {$wordpress_csrf}

                        <table class="table table-hover text-center text-dark table-borderless {if isset($mtn_active) }table-warning{elseif isset($mci_active)}table-info{elseif isset($rtl_active)}table-danger{elseif isset($sht_active)}table-info{/if}">
                            <tr style="display:none;">
                                <td colspan="3">
                                    <input type="text" class="form-control text-center" maxlength="11" dir="ltr" name="mobile" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile}{/if}" required>
                                    <input type="text" class="form-control text-center" maxlength="3" dir="ltr" name="operator" value="{if isset($smarty.post.operator)}{$smarty.post.operator}{/if}" required>
                                    <input type="checkbox" name="mnp" {if isset($mnp) && $mnp eq 1 }checked="checked"{/if} value="1" >
                                    <input type="checkbox" name="save_mobile" {if isset($save_mobile) && $save_mobile eq 1 }checked="checked"{/if} value="1" >
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-center">
                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=1000 }{if !isset($rtl_active) && !isset($mci_active) }<input type="radio" name="amount" value="1000" onclick="opeartor_handleClick(this);" id="1000" {if isset($smarty.post.amount) && $smarty.post.amount eq '1000' }checked="checked"{/if} required/> <label for="1000"> {__('شارژ',"inax")} {$operator|operator_fa:$language_code} <span>1,000</span> {__('تومانی',"inax")}</label><hr/>{/if}{/if}

                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=2000 }{if !isset($mci_active) }<input type="radio" name="amount" value="2000" onclick="opeartor_handleClick(this);" id="2000" {if isset($smarty.post.amount) && $smarty.post.amount eq '2000' }checked="checked"{/if} required/> <label for="2000"> {__('شارژ',"inax")} {$operator|operator_fa:$language_code} <span>2,000</span> {__('تومانی',"inax")}</label><hr/>{/if}{/if}

                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=5000 }<input type="radio" name="amount" value="5000" onclick="opeartor_handleClick(this);" id="5000" {if isset($smarty.post.amount) && $smarty.post.amount eq '5000' }checked="checked"{/if} required/> <label for="5000"> {__('شارژ',"inax")} {$operator|operator_fa:$language_code} <span>5,000</span> {__('تومانی',"inax")}</label><hr/>{/if}
                               
                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=10000 }<input type="radio" name="amount" value="10000" onclick="opeartor_handleClick(this);" id="10000" {if isset($smarty.post.amount) && $smarty.post.amount eq '10000' }checked="checked"{/if} required/> <label for="10000"> {__('شارژ',"inax")} {$operator|operator_fa:$language_code} <span>10,000</span> {__('تومانی',"inax")}</label><hr/>{/if}
                               
                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=20000 }<input type="radio" name="amount" value="20000" onclick="opeartor_handleClick(this);" id="20000" {if isset($smarty.post.amount) && $smarty.post.amount eq '20000' }checked="checked"{/if} required/> <label for="20000"> {__('شارژ',"inax")} {$operator|operator_fa:$language_code} <span>20,000</span> {__('تومانی',"inax")}</label><hr/>{/if}
                               
                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=50000 }<input type="radio" name="amount" value="50000" onclick="opeartor_handleClick(this);" id="50000" {if isset($smarty.post.amount) && $smarty.post.amount eq '50000' }checked="checked"{/if} required/> <label for="50000"> {__('شارژ',"inax")} {$operator|operator_fa:$language_code} <span>50,000</span> {__('تومانی',"inax")}</label>{/if}
                                
                                    {if isset($mtn_active) || isset($mci_active) || isset($rtl_active)}
                                        <!-- min="" max="" -->
                                    <hr/><input type="radio" name="amount" value="custom_amount" onclick="opeartor_handleClick(this);" id="custom_amount_rb" {if isset($smarty.post.amount) && $smarty.post.amount eq 'custom_amount' }checked="checked"{/if} /> <label for="custom_amount_rb"> {__('خرید شارژ با مبلغ دلخواه',"inax")} </label><br/>
                                    {/if}
                                </td>
                            </tr>
                            {if isset($mtn_active) || isset($mci_active) || isset($rtl_active)}
                                <tr id="custom" {if !isset($display_custom_amount_field)}style="display:none;{/if}">
                                    <td style="text-align:left">{__('مبلغ شارژ',"inax")}</td>
                                    <td>
                                        <input type="tel" maxlength="7" dir="ltr" class="form-control" min="500" max="200000" name="custom_amount" onkeyup="number_to_letter();" id="amount_field" value="{if isset($smarty.post.custom_amount)}{$smarty.post.custom_amount}{/if}" />
                                    </td>
                                    <td class="text-right"> {__('تومان',"inax")}</td>
                                </tr>
                            {/if}
                            <!--<tr>
                                <td colspan="3">
                                    <input type="checkbox" name="mnp" class="text-center" value="1" {if isset($smarty.post.mnp) && $smarty.post.mnp eq 1 }checked="checked"{/if} id="mnp_label"> <label for="mnp_label"> در صورتی که شماره فوق به {$operator|operator_fa} ترابرد شده است این گزینه را فعال نمائید.</label>
                                </td>
					    	</tr>-->
                            <tr>
                                <td colspan="3" >
                                    <select name="charge_type" class="form-control form-control-lg text-center" style="cursor:pointer;" required>
                                        <option value="">{__('- - - - انتخاب نوع شارژ - - - -',"inax")}</option>
                                        <option value="normal" {if isset($smarty.post.charge_type) && $smarty.post.charge_type eq "normal" }selected{/if} >{__('شارژ معمولی',"inax")}</option>
                                        {if isset($mtn_active) || isset($rtl_active)}<option value="amazing" {if isset($smarty.post.charge_type) && $smarty.post.charge_type eq "amazing" }selected{/if} >{if isset($mtn_active) }{__('شارژ شگفت انگیز',"inax")}{elseif isset($rtl_active)}{__('شارژ شور انگیز',"inax")}{/if}</option>{/if}
                                        {if isset($mtn_active) }<option value="permanent" {if isset($smarty.post.charge_type) && $smarty.post.charge_type eq "permanent" }selected{/if} >{__('شارژ سیم کارت دایمی',"inax")}</option>{/if}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <button class="btn btn-success form-control border-success inax-font" name="submit" type="submit"><i class="fa fa-check"></i> {__('پرداخت آنلاین',"inax")}</button>
                                    {if isset($credit_payment)}<button class="btn btn-warning form-control border-success m-1 inax-font" {if $user_credit eq 0}disabled{/if} name="submit_credit" type="submit"><i class="fa fa-check"></i> {__("خرید از کیف پول (اعتبار %s تومان)","inax")|sprintf:number_format($user_credit)}</button>{/if}
                                </td>
                            </tr>
                        </table>
                    </form>
                    
                </div>
            </div>
        </div>
    {/if}

    </div>
</div>

{include file="client/$inax_theme/footer.tpl"}
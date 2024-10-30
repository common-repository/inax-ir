{include file="client/$inax_theme/header.tpl"}

<div class="row">
    <div class="col-lg-12">

    {if isset($buy_charge) && !isset($mtn_active)  && !isset($mci_active) && !isset($rtl_active) && !isset($sht_active) && !isset($tr_list) }
        <div class="card shadow mb-4 p-2 border-default">
            <div class="card-header mb-2 p-2 text-right">
                <i class="fa fa-shopping-cart fa-fw"></i>
                {$title} {if $is_user_logged_in eq 1}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$trans_link}"><i class="fa fa-database fa-fw"></i> {__('لیست تراکنش ها',"inax")}</a>{/if}
                {if isset($main_link)}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none mx-1 inax-font" href="{$main_link}"><i class="fa fa-home fa-fw"></i> {__('صفحه اصلی فروشگاه',"inax")}</a>{/if}
            </div>
            <div class="card-body mt-2 text-right p-0">
                <div class="alert alert-primary">{__('لطفا اپراتور تلفن همراه خود را انتخاب نمائید',"inax")}</div>

                <div class="album py-2 bg-light">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                        <div class="col text-center">
                            <div class="card shadow-sm border-warning border-2">
                                <a title="{__('شارژ مستقیم ایرانسل',"inax")}" href="{$permalink}MTN" class="text-decoration-none">
                                    <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}mtn.png{else}mtn_en.png{/if}" class="card-img-top" alt="{__('شارژ مستقیم ایرانسل',"inax")}">

                                    <div class="card-body">
                                        <p class="card-text">{__('شارژ مستقیم ایرانسل',"inax")}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col text-center">
                            <div class="card shadow-sm border-info border-2">
                                <a title="{__('شارژ مستقیم همراه اول',"inax")}" href="{$permalink}MCI" class="text-decoration-none border-shadow ">
                                    <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}mci.png{else}mci_en.png{/if}" class="card-img-top" alt="{__('شارژ مستقیم همراه اول',"inax")}">
                                    <div class="card-body">
                                        <p class="card-text">{__('شارژ مستقیم همراه اول',"inax")}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col text-center ">
                            <div class="card shadow-sm border-perple border-2">
                                <a title="{__('شارژ مستقیم رایتل',"inax")}" href="{$permalink}RTL" class="text-decoration-none">
                                    <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}rtl.png{else}rtl_en.png{/if}" class="card-img-top" alt="{__('شارژ مستقیم رایتل',"inax")}">
                                    <div class="card-body">
                                        <p class="card-text">{__('شارژ مستقیم رایتل',"inax")}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col text-center ">
                            <div class="card shadow-sm border-primary border-2">
                                <a title="{__('شارژ مستقیم شاتل موبایل',"inax")}" href="{$permalink}SHT" class="text-decoration-none">
                                    <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}sht.png{else}sht_en.png{/if}" class="card-img-top" alt="{__('شارژ مستقیم شاتل موبایل',"inax")}">
                                    <div class="card-body">
                                        <p class="card-text">{__('شارژ مستقیم شاتل موبایل',"inax")}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {if isset($mtn_active)  || isset($mci_active) || isset($rtl_active) || isset($sht_active) }
        <div class="card bg-{if isset($mtn_active) }warning{elseif isset($mci_active)}info{elseif isset($rtl_active)}perple{elseif isset($sht_active)}primary{/if}">
            <div class="card-header text-right  {if isset($mtn_active) }text-dark{elseif isset($mci_active)}text-light{elseif isset($rtl_active)}text-light{elseif isset($sht_active)}text-light{/if}">
                <i class="fa fa-shopping-cart fa-fw"></i> {__('خرید شارژ مستقیم',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{elseif isset($sht_active)}{__('شاتل موبایل',"inax")}{/if}
                <a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$p_url}"> {__('بازگشت',"inax")}
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

                    <form action="{$permalink}{if isset($mtn_active) }MTN{elseif isset($mci_active)}MCI{elseif isset($rtl_active)}RTL{elseif isset($sht_active)}SHT{/if}" method="POST">
                        {$wordpress_csrf}

                        <table class="table table-hover text-center text-dark table-borderless {if isset($mtn_active) }table-warning{elseif isset($mci_active)}table-info{elseif isset($rtl_active)}table-danger{elseif isset($sht_active)}table-info{/if}">
                            <tr>
                                <td colspan="3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon2"><i class="fa fa-phone fa-fw fa-1x"></i></span>
                                        <input type="tel" class="form-control text-center" onkeyup="inax_check_numbers('inlineFormInputGroup');" id="inlineFormInputGroup" maxlength="11" aria-label="{__('شماره تلفن همراه',"inax")}" aria-describedby="basic-addon1" placeholder="{__('شماره تلفن همراه',"inax")}" dir="ltr" name="mobile" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile}{/if}" tabindex="1" required>
                                    </div>
                                </td>
                            </tr>
                            {if $is_user_logged_in eq 1}
                            <tr>
                                <td colspan="3">
                                    <div class="mb-3 text-center">
                                        <input type="checkbox" name="save_mobile" value="1" {if isset($smarty.post.save_mobile)}checked="checked"{/if} tabindex="2" id="save_mobile" > <label for="save_mobile" > {__('ذخیره شماره برای استفاده های بعدی',"inax")}</label>
                                        {if isset($saved_mobile_rows)}
                                        
                                        <select name="use_saved_mobile" id="use_saved_mobile" onchange="handle_saved_mobile('topup')"> 
                                            <option value="">{__('استفاده از شماره های ذخیره شده',"inax")}</option>
                                            {foreach from=$saved_mobile_rows key=key item=link}
                                            <option value="{$link.mobile}">{$link.mobile}</option>
                                            {/foreach}
                                        </select>
                                        {/if}
                                    </div>
                                </td>
                            </tr>
                            {/if}
                            <tr>
                                <td colspan="3" class="text-center" >
                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=1000 }{if !isset($rtl_active) && !isset($mci_active) }<input type="radio" name="amount" value="1000" onclick="opeartor_handleClick(this);" id="1000" {if isset($smarty.post.amount) && $smarty.post.amount eq '1000' }checked{/if} required/> <label for="1000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{elseif isset($sht_active)}{__('شاتل موبایل',"inax")}{/if} <span>1,000</span> {__('تومانی',"inax")}</label><hr/>{/if}{/if}

                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=2000 }{if !isset($mci_active) }<input type="radio" name="amount" value="2000" onclick="opeartor_handleClick(this);" id="2000" {if isset($smarty.post.amount) && $smarty.post.amount eq '2000' }checked{/if} required/> <label for="2000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{elseif isset($sht_active)}{__('شاتل موبایل',"inax")}{/if} <span>2,000</span> {__('تومانی',"inax")}</label><hr/>{/if}{/if}

                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=5000 }<input type="radio" name="amount" value="5000" onclick="opeartor_handleClick(this);" id="5000" {if isset($smarty.post.amount) && $smarty.post.amount eq '5000' }checked{/if} required/> <label for="5000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{elseif isset($sht_active)}{__('شاتل موبایل',"inax")}{/if} <span>5,000</span> {__('تومانی',"inax")}</label><hr/>{/if}
                               
                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=10000 }<input type="radio" name="amount" value="10000" onclick="opeartor_handleClick(this);" id="10000" {if isset($smarty.post.amount) && $smarty.post.amount eq '10000' }checked{/if} required/> <label for="10000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{elseif isset($sht_active)}{__('شاتل موبایل',"inax")}{/if} <span>10,000</span> {__('تومانی',"inax")}</label><hr/>{/if}
                               
                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=20000 }<input type="radio" name="amount" value="20000" onclick="opeartor_handleClick(this);" id="20000" {if isset($smarty.post.amount) && $smarty.post.amount eq '20000' }checked{/if} required/> <label for="20000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{elseif isset($sht_active)}{__('شاتل موبایل',"inax")}{/if} <span>20,000</span> {__('تومانی',"inax")}</label><hr/>{/if}
                               
                                    {if $inax_amount_limitation eq 0 or $inax_amount_limitation>=50000 }<input type="radio" name="amount" value="50000" onclick="opeartor_handleClick(this);" id="50000" {if isset($smarty.post.amount) && $smarty.post.amount eq '50000' }checked{/if} required/> <label for="50000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{elseif isset($sht_active)}{__('شاتل موبایل',"inax")}{/if} <span>50,000</span> {__('تومانی',"inax")}</label>{/if}
                                
                                    {if isset($mtn_active) || isset($mci_active) || isset($rtl_active)}
                                    <hr/><input type="radio" name="amount" value="custom_amount" onclick="opeartor_handleClick(this);" id="custom_amount_rb" {if isset($smarty.post.amount) && $smarty.post.amount eq 'custom_amount' }checked{/if} /> <label for="custom_amount_rb"> {__('خرید شارژ با مبلغ دلخواه',"inax")} </label><br/>
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
                            <tr>
                                <td colspan="3">
                                    <input type="checkbox" name="mnp" class="text-center" value="1" {if isset($smarty.post.mnp) && $smarty.post.mnp eq 1 }checked{/if} id="mnp_label"> <label for="mnp_label">{__("در صورتی که شماره فوق به %s ترابرد شده است این گزینه را فعال نمائید.","inax")|sprintf:operator_fa($operator)}</label>
                                </td>
					    	</tr>
                            <tr>
                                <td colspan="3">
                                    <select name="charge_type" class="form-control form-control-lg text-center" style="cursor:pointer;" required>
                                        <option value="">{__('- - - - انتخاب نوع شارژ - - - -',"inax")}</option>
                                        <option value="normal" {if isset($smarty.post.charge_type) && $smarty.post.charge_type eq "normal" }selected{/if} >{__('شارژ معمولی',"inax")}</option>
                                        {if isset($mtn_active) || isset($rtl_active)}
                                        <option value="amazing" {if isset($smarty.post.charge_type) && $smarty.post.charge_type eq "amazing" }selected{/if} >{if isset($mtn_active) }{__('شارژ شگفت انگیز',"inax")}{elseif isset($rtl_active)}{__('شارژ شور انگیز',"inax")}{/if}</option>{/if}
                                        <!--<option value="mnp" {if isset($smarty.post.charge_type) && $smarty.post.charge_type eq "mnp" }selected{/if} >{__('شارژ سیم کارت ترابرد شده',"inax")}</option>-->
                                        {if isset($mtn_active) }
                                        <option value="permanent" {if isset($smarty.post.charge_type) && $smarty.post.charge_type eq "permanent" }selected{/if} >{__('شارژ سیم کارت دایمی',"inax")}</option>{/if}
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
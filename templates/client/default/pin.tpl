{include file="client/$inax_theme/header.tpl"}

<div class="row">
    <div class="col-lg-12">

        {if isset($buy_pin) && !isset($mtn_active)  && !isset($mci_active) && !isset($rtl_active) && !isset($tr_list) }
            <div class="card shadow mb-4 border-primary">
                <div class="card-header text-right">
                    <i class="fa fa-shopping-cart fa-fw"></i>
                    {$title} {if $is_user_logged_in eq 1}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$trans_link}"><i class="fa fa-database fa-fw"></i> {__('لیست تراکنش ها',"inax")}</a>{/if}
                    {if isset($main_link)}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none mx-1 inax-font" href="{$main_link}"><i class="fa fa-home fa-fw"></i> {__('صفحه اصلی فروشگاه',"inax")}</a>{/if}
                </div>
                <div class="card-body text-right p-0">
                    <div class="alert alert-primary">{__('لطفا اپراتور تلفن همراه خود را انتخاب نمائید',"inax")}</div>
                  
                    <div class="album py-2 bg-light">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                            <div class="col text-center">
                                <div class="card shadow-sm border-warning border-2">
                                    <a title="{__('شارژ پین ایرانسل',"inax")}" href="{$permalink}MTN" class="text-decoration-none">
                                        <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}mtn.png{else}mtn_en.png{/if}" class="card-img-top" alt="{__('شارژ پین ایرانسل',"inax")}">

                                        <div class="card-body">
                                            <p class="card-text">{__('شارژ پین ایرانسل',"inax")}</p>
                                            <!--<p class="card-text">{__('irancell pin',"inax")}</p>-->
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col text-center">
                                <div class="card shadow-sm border-info border-2">
                                    <a title="{__('شارژ پین همراه اول',"inax")}" href="{$permalink}MCI" class="text-decoration-none border-shadow ">
                                        <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}mci.png{else}mci_en.png{/if}" class="card-img-top" alt="{__('شارژ پین همراه اول',"inax")}">
                                        <div class="card-body">
                                            <p class="card-text">{__('شارژ پین همراه اول',"inax")}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col text-center ">
                                <div class="card shadow-sm border-perple border-2">
                                    <a title="{__('شارژ پین رایتل',"inax")}" href="{$permalink}RTL" class="text-decoration-none">
                                        <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}rtl.png{else}rtl_en.png{/if}" class="card-img-top" alt="{__('شارژ پین رایتل',"inax")}">
                                        <div class="card-body">
                                            <p class="card-text">{__('شارژ پین رایتل',"inax")}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        {/if}

        {if isset($mtn_active)  || isset($mci_active) || isset($rtl_active) }
            <div class="card bg-{if isset($mtn_active) }warning{elseif isset($mci_active)}info{elseif isset($rtl_active)}perple{/if}">
                <div class="card-header text-right {if isset($mtn_active) }text-dark{elseif isset($mci_active)}text-light{elseif isset($rtl_active)}text-light{/if}">
                    <i class="fa fa-shopping-cart fa-fw"></i> {__('خرید شارژ پین',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{/if}
                    <a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$p_url}"> {__('بازگشت',"inax")}<i class="fa fa-arrow-left fa-fw"></i></a>
                </div>
                <div class="card-body bg-light">

                    <div class="alert alert-info">{__('لطفا از بخش زیر شماره تلفن و مبلغ شارژ را وارد نمائید',"inax")}</div>

                    {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
                    {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

                    <div class="table-responsive">
                        <!-- onsubmit="disable_submit_btn();return true;" -->
                        <!-- onsubmit="document.getElementsByName('submit')[0].disabled = true; return true;" -->
                        <script>
                        function disable_submit_btn(){
                            /*document.getElementsByName("submit")[0].disabled = true;
                            
                            var bt = document.getElementsByName("submit_credit")[0];
                            if (typeof(bt) != 'undefined' && bt != null){
                                bt.disabled = true;
                            }*/

                            var oForm = document.forms["submit_form1"];
                            oForm.action = "";

                            //var mobile = oForm.elements[1].value;
                            //var mobile = oForm.elements["mobile"].value;

                            //oForm.elements["submit"].disabled = true;
                            //console.log(oForm); 
                        }
                        </script>
                    
                        <form action="{$permalink}{if isset($mtn_active) }MTN{elseif isset($mci_active)}MCI{elseif isset($rtl_active)}RTL{/if}" name="submit_form1" method="POST" onsubmit="disable_submit_btn();return true;" >
                            {$wordpress_csrf}

                            <table class="table table-hover text-center text-dark table-borderless {if isset($mtn_active) }table-warning{elseif isset($mci_active)}table-info{elseif isset($rtl_active)}table-danger{/if}">
                                <tr>
                                    <td colspan="3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon2"><i class="fa fa-phone fa-fw fa-1x"></i></span>
                                            <input type="tel" class="form-control text-center" onkeyup="inax_check_numbers('inlineFormInputGroup');" id="inlineFormInputGroup" maxlength="11" aria-label="{__('شماره تلفن همراه',"inax")}" aria-describedby="basic-addon1" placeholder="{__('شماره تلفن همراه',"inax")}" dir="ltr" name="mobile" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile}{/if}" tabindex="1" required>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        {if isset($mtn_active) }<input type="radio" name="amount" value="1000" id="1000" {if isset($smarty.post.amount) && $smarty.post.amount eq '1000' }checked{/if} required/> <label for="1000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{/if} <span>1,000</span> {__('تومانی',"inax")}</label><hr/>{/if}
                                        
                                        {if !isset($mci_active) }<input type="radio" name="amount" value="2000" id="2000" {if isset($smarty.post.amount) && $smarty.post.amount eq '2000' }checked{/if} required/> <label for="2000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{/if}<span>2,000</span> {__('تومانی',"inax")}</label><hr/>{/if}
                                    
                                        <input type="radio" name="amount" value="5000" id="5000" {if isset($smarty.post.amount) && $smarty.post.amount eq '5000' }checked{/if} required/> <label for="5000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{/if} <span>5,000</span> {__('تومانی',"inax")}</label><hr/>
                               
                                        <input type="radio" name="amount" value="10000" id="10000" {if isset($smarty.post.amount) && $smarty.post.amount eq '10000' }checked{/if} required/> <label for="10000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{/if} <span>10,000</span> {__('تومانی',"inax")}</label><hr/>
                                    
                                        <input type="radio" name="amount" value="20000" id="20000" {if isset($smarty.post.amount) && $smarty.post.amount eq '20000' }checked{/if} required/> <label for="20000"> {__('شارژ',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{/if} <span>20,000</span> {__('تومانی',"inax")}</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <!--<input type="hidden" name="submit_form" />-->
                                        <button class="btn btn-success form-control border-success inax-font" name="submit" type="submit" ><i class="fa fa-check"></i> {__('پرداخت آنلاین',"inax")}</button>
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
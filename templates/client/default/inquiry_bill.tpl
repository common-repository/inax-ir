{include file="client/$inax_theme/header.tpl"}

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4 border-primary">

            {if isset($check_inquiry_bill)  }
                <div class="card-header text-right">
                    <i class="fa fa-shopping-cart fa-fw"></i>
                    {$title} {if $is_user_logged_in eq 1}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$permalink}list"><i class="fa fa-database fa-fw"></i> {__('لیست تراکنش ها',"inax")}</a>{/if}
                    {if isset($main_link)}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none mx-1 inax-font" href="{$main_link}"><i class="fa fa-home fa-fw"></i> {__('صفحه اصلی فروشگاه',"inax")}</a>{/if}
                </div>
                <div class="card-body text-right p-0">

                    {if !isset($bill_details) }
                        <div class="alert alert-info">- {__('توسط این بخش می توانید بدهی کلیه قبوض خدماتی را استعلام نمائید',"inax")}</div>

                        {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}

                        <div class="table-responsive">
                            <form action="{$permalink}" method="POST" >
                                {$wordpress_csrf}
                                <table class="table table-default table-hover table-bordered" >
                                    <tr>
                                        <th style="width:30%">{__('نوع قبض',"inax")}</th>
                                        <td>
                                            <select name="bill_type" id="bill_type" onchange="bill_type_action()" class="myform-control" required>
                                                <option value="" >{__('... انتخاب کنيد ...',"inax")}</option>
                                                <option {if isset($smarty.post.bill_type) && $smarty.post.bill_type eq 'mobile' }selected='selected'{/if} value="mobile" >{__('قبض موبایل (سیم کارت دایمی)',"inax")}</option>
                                                <option {if isset($smarty.post.bill_type) && $smarty.post.bill_type eq 'elec' }selected='selected'{/if} value="elec" >{__('قبض برق',"inax")}</option>
                                                <option {if isset($smarty.post.bill_type) && $smarty.post.bill_type eq 'gas' }selected='selected'{/if} value="gas" >{__('قبض گاز',"inax")}</option>
                                                <option {if isset($smarty.post.bill_type) && $smarty.post.bill_type eq 'water' }selected='selected'{/if} value="water" >{__('قبض آب',"inax")}</option>
                                                <option {if isset($smarty.post.bill_type) && $smarty.post.bill_type eq 'phone' }selected='selected'{/if} value="phone" >{__('قبض تلفن ثابت',"inax")}</option>
                                            </select> *

                                            <script>
                                                document.addEventListener("DOMContentLoaded", function(){
                                                    const selectfrom = document.getElementById("bill_type");
                                                    const selectedValue = selectfrom.options[selectfrom.selectedIndex].value;

                                                    if(selectedValue == "mobile"){
                                                        document.getElementById("input_value").removeAttribute('required');
                                                        document.getElementById("for_support").style.display = 'none';
                                                        document.getElementById("input_tr").style.display = 'none';
                                                    }
                                                    else{
                                                        document.getElementById("operator").style.display = 'none';
                                                        document.getElementById("operator_MTN").removeAttribute('required');
                                                        document.getElementById("period").style.display = 'none';
                                                        document.getElementById('input_value').setAttribute('required', 'required');
                                                        document.getElementById("for_support").style.display = '';
                                                        //document.getElementById("input_tr").style.display = '';
                                                    }
                                                });

                                                function bill_type_action(){
                                                    const selectfrom = document.getElementById("bill_type");
                                                    const selectedValue = selectfrom.options[selectfrom.selectedIndex].value;

                                                    //set required
                                                    document.getElementById('input_value').setAttribute('required', 'required');
                                                    document.getElementById("operator_MTN").removeAttribute('required');

                                                    //set display
                                                    document.getElementById("input_tr").style.display = 'none';
                                                    document.getElementById("operator").style.display = 'none';
                                                    document.getElementById("period").style.display = 'none';
                                                    document.getElementById("for_support").style.display = '';

                                                    document.getElementById("th_title").innerHTML="";
                                                    document.getElementById("th_title").placeholder = "";

                                                    //empty inputs value
                                                    document.getElementById('input_value').value = "";
                                                    document.getElementById('mobile').value = "";

                                                    if(selectedValue == "mobile"){
                                                        document.getElementById("operator").style.display = '';
                                                        document.getElementById("period").style.display = '';
                                                        document.getElementById("th_title").innerHTML= "{__('شماره موبایل','inax')}";
                                                        document.getElementById("th_title").placeholder = "0912...";
                                                        //document.getElementById("input_tr").style.display = '';
                                                        document.getElementById("for_support").style.display = 'none';
                                                        document.getElementById("input_value").removeAttribute('required');
                                                        document.getElementById('operator_MTN').setAttribute('required', 'required');
                                                    }
                                                    else if(selectedValue == "gas"){
                                                        console.log(1);
                                                        document.getElementById("th_title").innerHTML= "{__('کد اشتراک (موجود بر روی قبض)','inax')}";
                                                        document.getElementById("input_tr").style.display = '';
                                                    }
                                                    else if(selectedValue == "elec" || selectedValue == "water"){
                                                        document.getElementById("th_title").innerHTML= "{__('شناسه قبض (موجود بر روی قبض)','inax')}";
                                                        document.getElementById("input_tr").style.display = '';
                                                    }
                                                    else if(selectedValue == "phone" ){
                                                        document.getElementById("th_title").innerHTML= "{__('شماره تلفن به همراه کد شهر','inax')}";
                                                        document.getElementById("th_title").placeholder = "021...";
                                                        document.getElementById("input_tr").style.display = '';
                                                    }
                                                    else{
                                                        //$("#karmozd_type_text").empty();
                                                    }
                                                }
                                            </script>
                                        </td>
                                    </tr>
                                    <tr id="input_tr" style="{if !isset($smarty.post.input_value) || ( isset($smarty.post.bill_type) && $smarty.post.bill_type eq 'mobile') }display:none{else}{/if}">
                                        <th id="th_title" >-</th>
                                        <td>
                                            <input type="text" name="input_value"  id="input_value" class="myform-control" dir="ltr" size="30" value="{if isset($smarty.post.input_value)}{$smarty.post.input_value}{/if}" {if !isset($link.bill_type) || (isset($link.bill_type) && $link.bill_type neq 'mobile') }required{/if} />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{__('شماره موبایل',"inax")}</th>
                                        <td><input type="tel" name="mobile" dir="auto" id="mobile" maxlength="11" class="myform-control" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile}{/if}" size="35" required/> <span id="for_support">{__('(جهت پشتیبانی در صورت بروز مشکل)',"inax")}</span></td>
                                    </tr>
                                    <tr id="operator" style="{if !isset($smarty.post.operator) }display:none{else}{/if}">
                                        <th>{__('اپراتور',"inax")}</th>
                                        <td>
                                            <div class="cc-selector">
                                                <input {if isset($smarty.post.operator) && $smarty.post.operator eq 'MTN' }checked="checked"{/if} id="operator_MTN" type="radio" name="operator" value="MTN" /><label class="operator_img operator_MTN" for="operator_MTN"></label>
                                                <input {if isset($smarty.post.operator) && $smarty.post.operator eq 'MCI' }checked="checked"{/if} id="operator_MCI" type="radio" name="operator" value="MCI" /><label class="operator_img operator_MCI" for="operator_MCI"></label>
                                                <input {if isset($smarty.post.operator) && $smarty.post.operator eq 'RTL' }checked="checked"{/if} id="operator_RTL" type="radio" name="operator" value="RTL" /><label class="operator_img operator_RTL" for="operator_RTL"></label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr id="period" style="{if !isset($smarty.post.period) }display:none{else}{/if}">
                                        <th>{__('دوره زمانی',"inax")}</th>
                                        <td>
                                            <div>
                                                <input {if isset($smarty.post.period) && $smarty.post.period eq 'mid' }checked="checked"{else}checked{/if} type="radio" name="period" value="mid" /> <label for="mid"> {__('میان دوره',"inax")} </label>
                                                <input {if isset($smarty.post.period) && $smarty.post.period eq 'final' }checked="checked"{/if} type="radio" name="period" value="final" /> <label for="final"> {__('پایان دوره',"inax")} </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <td><button type="submit" name="submit_inquiry" class="btn btn-primary btn-sm inax-font" ><i class="fa fa-edit fa-fw"></i> {__('استعلام بدهی قبوض',"inax")}</button></td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    {else}
                        {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>
                        {else}
                            {foreach from=$bill_details key=key item=link}
                                <div class="table-responsive">
                                    <table class="table table-default table-hover table-bordered" >
                                        <tr>
                                            <th class="text-right" style="width:30%;" >{__('قبض',"inax")}</th>
                                            <td>{if $language_code eq 'fa'}{$link.bill_type|inax_bill_type_fa}{else}{$link.bill_type}{/if}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right" style="width:30%;" >{__('میزان بدهی قبض',"inax")}</th>
                                            <td>{if $link.amount eq 0 }{__('فاقد بدهی',"inax")}{else}{$link.amount|number_format} {__('تومان',"inax")}{/if}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right" >{__('شناسه قبض',"inax")}</th>
                                            <td>{$link.bill_id}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right" >{__('شناسه پرداخت',"inax")}</th>
                                            <td>{$link.pay_id}</td>
                                        </tr>

                                        <tr>
                                            <th class="text-right" ></th>
                                            <td>
                                                {if $link.amount > 0 }<a href="{$bill_page}bill_id={$link.bill_id}&pay_id={$link.pay_id}" class="btn btn-success btn-sm inax-font" ><i class="fa fa-check fa-fw"></i> {__('پرداخت قبض',"inax")}</a> {/if}
                                                <a href="{$permalink}" class="btn btn-warning btn-sm inax-font" ><i class="fa fa-arrow-left fa-fw"></i> {__('بازگشت',"inax")}</a>
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            {/foreach}
                        {/if}
                    {/if}

                </div>
            {/if}

            </div>
        </div>
    </div>

{include file="client/$inax_theme/footer.tpl"}
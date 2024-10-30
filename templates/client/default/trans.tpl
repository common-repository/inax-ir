{include file="client/$inax_theme/header.tpl"}

<!--<div class="alert alert-info">{if $client_id eq 0}لیست تراکنش های کاربر مهمان{else}تراکنش های کاربر {$client_id}{/if}</div>-->

{if isset($tr_list) }
    <div class="card text-right card-default">
        <div class="card-header"><i class="fa-solid fa-database fa-fw"></i> {$title}
            {*<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}{/if} text-decoration-none inax-font" href="{$p_url}">بازگشت<i class="fa fa-arrow-left fa-fw"></i></a>*}
            {if isset($main_link)}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none mx-1 inax-font" href="{$main_link}"><i class="fa fa-home fa-fw"></i> {__('صفحه اصلی فروشگاه',"inax")}</a>{/if}
            {if  $is_user_logged_in eq 1 && isset($smarty.get.id) }
               <a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$trans_link}"><i class="fa fa-database fa-fw"></i> {__('لیست تراکنش ها',"inax")}</a>
            {/if}

        </div>
        <div class="card-body">

            {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
            {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}
            
            {if isset($smarty.get.id) && isset($smarty.get.ok) }
                <div class="alert alert-success">
                    {__('خرید با موفقیت انجام شد',"inax")}<hr/>
                    {*display trans result*}
                    {if isset($trans_rows)}
                    <table class="table">
                    {foreach from=$trans_rows key=key item=link}
                         {if !empty($link.gateway_ref_code) }
                         <tr>
                             <td>{__('شماره پیگیری درگاه',"inax")}</td>
                             <td class="text-right ">{$link.gateway_ref_code}</td>
                         </tr>
                         {/if}
                        <tr>
                             <td style="width: 25%">{__('شماره پیگیری خرید',"inax")}</td>
                             <td class="text-right ">{$link.ref_code}</td>
                         </tr>
                         {if $link.type eq 'pin' }
                         <tr>
                             <td>{__('جزئیات شارژ',"inax")}</td>
                             <td class="text-right" >{$link.buyed_output}</td>
                         </tr>
                         {/if}
                    {/foreach}
                    </table>
                    {/if}
                </div>
            {/if}

            {if isset($smarty.get.id) && isset($smarty.get.nok) }
                {if isset($trans_rows)}
                {foreach from=$trans_rows key=key item=link}
                    {if $link.status eq 'unpaid'} <div class="alert alert-danger">{__('وضعیت تراکنش پرداخت نشده است',"inax")}</div>{/if}
                {/foreach}
                {/if}
            {/if}

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover listtable">
                    <thead>
                    <tr>
                        <th class="text-center" >{__('آیدی',"inax")}</th>
                        <th class="text-center" >{__('محصول',"inax")}</th>
                        <th class="text-center" >{__('موبایل',"inax")}</th>
                        <th class="text-center" >{__('اپراتور',"inax")}</th>
                        <th class="text-center" >{__('مبلغ (تومان)',"inax")}</th>
                        <th class="text-center" >{__('نحوه پرداخت',"inax")}</th>
                        <th class="text-center" >{__('رسید فراهم کننده',"inax")}</th>
                        <th class="text-center" >{__('تاریخ',"inax")}</th>
                        <th class="text-center" >{__('وضعیت پرداخت',"inax")}</th>
                        <th class="text-center" >{__('وضعیت تراکنش',"inax")}</th>
                        <th class="text-center" >{__('جزئیات تراکنش',"inax")}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {if isset($trans_rows)}
                        {foreach from=$trans_rows key=key item=link}
                            <tr>
                                <!--<td class="text-center">{if $is_user_logged_in eq 1}<a href="{$permalink}list&id={$link.id}" class="text-decoration-none">{$link.id}</a>{else}{$link.id}{/if}</td>-->
                                <td class="text-center"><a href="?id={$link.id}">{$link.id}</a></td>
                                <td class="text-center">
                                    {if $link.type eq 'topup'}{__('شارژ مستقیم',"inax")}
									{elseif $link.type eq 'pin'}{__('شارژ پین',"inax")}
									{elseif $link.type eq 'internet'}{__('بسته اینترنت',"inax")}
									{elseif $link.type eq 'bill'}{__('قبض',"inax")}
									{/if}
                                </td>
                                <td class="text-center">{$link.mobile}</td>
                                <td class="text-center">
                                    {if $link.operator eq 'MTN'}
                                        <span class="btn btn-warning btn-sm disabled inax-font">{__('ایرانسل',"inax")}</span>
                                    {elseif $link.operator eq 'MCI'}
                                        <span class="btn btn-info btn-sm disabled inax-font">{__('همراه اول',"inax")}</span>
                                    {elseif $link.operator eq 'RTL'}
                                        <span class="btn btn-danger btn-sm disabled inax-font">{__('رایتل',"inax")}</span>
                                    {elseif $link.operator eq 'SHT'}
                                        <span class="btn btn-info btn-sm disabled inax-font">{__('شاتل موبایل',"inax")}</span>
                                    {else}
                                        {$link.operator}
                                    {/if}
                                </td>
                                <td class="text-center">{$link.amount|number_format}</td>
                                <td class="text-center"> {if $link.payment_type eq 'online'}<span class="btn btn-info btn-sm disabled inax-font">{__('آنلاین',"inax")}</span>{elseif $link.payment_type eq 'credit'}<span class="btn btn-warning btn-sm disabled inax-font">{__('اعتبار',"inax")}</span>{/if}</td>
                                <td class="text-center">{$link.ref_code}</td>
                                <td class="text-center" style="direction:ltr">{$link.date|inax_jdate_format}</td>
                                <td class="text-center">
                                    {if $link.status eq 'paid'}<span class="btn btn-success btn-sm disabled inax-font"><i class="fa fa-check"></i> {__('پرداخت شده',"inax")}</span>
                                    {elseif $link.status eq 'unpaid'}<span class="btn btn-danger btn-sm disabled inax-font"><i class="fa fa-close"></i> {__('پرداخت نشده',"inax")}</span>
                                    {else}{$link.status}
                                    {/if}
                                </td>
                                <td class="text-center">
                                    {if $link.final_status eq 'success'}<span class="btn btn-success btn-sm disabled inax-font" ><i class="fa fa-check" ></i> {__('موفق',"inax")}</span>
                                    {elseif $link.final_status eq '' or $link.final_status eq null}<span class="btn btn-warning btn-sm disabled inax-font" ><i class="fa fa-close" ></i> {__('معلق',"inax")}</span>
                                    {else}{$link.final_status}
                                    {/if}
                                </td>
                                <td class="text-center">

                                    <button type="button" class="btn btn-primary inax-font" data-bs-toggle="modal" data-bs-target="#Modal_{$link.id}">{__('جزئیات',"inax")}</button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="Modal_{$link.id}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title inax-font" id="exampleModalLabel">{__('جزئیات تراکنش',"inax")} {$link.id} </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered table-hover">
                                                            {if $link.mnp eq 1}
                                                                <tr>
                                                                    <th class="myth" >{__('ترابرد پذیری',"inax")}</th>
                                                                    <td class="text-center ">{__('سیم کارت ترابرد شده است',"inax")}</td>
                                                                </tr>
                                                            {/if}
                                                            {if !empty($link.gateway_ref_code) }
                                                                <tr>
                                                                    <th class="myth" >{__('شماره پیگیری درگاه',"inax")}</th>
                                                                    <td class="text-center ">{$link.gateway_ref_code}</td>
                                                                </tr>
                                                            {/if}
                                                            {if $link.type eq 'internet'}
                                                                <tr>
                                                                    <th class="myth" >{__('نوع اینترنت',"inax")}</th>
                                                                    <td class="text-center ">{if $language_code eq 'fa'}{$link.internet_type|inax_internet_type_fa}{else}{$link.internet_type}{/if}</td>
                                                                </tr>
                                                            {/if}
                                                            {if $link.type eq 'topup' or $link.type eq 'internet'}
                                                                <tr>
                                                                    <th class="myth" >{__('نوع سیم کارت',"inax")}</th>
                                                                    <td class="text-center ">{if $link.sim_type eq 'credit'}{__('اعتباری',"inax")}
                                                                        {elseif $link.sim_type eq 'permanent'}{__('دایمی',"inax")}
                                                                        {elseif $link.sim_type eq 'TDLTE_credit'}{__('سیم کارت TD-LTE اعتباری',"inax")}
                                                                        {elseif $link.sim_type eq 'TDLTE_permanent'}{__('سیم کارت TD-LTE دائمی',"inax")}
                                                                        {elseif $link.sim_type eq 'data'}{__('سیم کارت دیتا',"inax")}
                                                                        {/if}
                                                                    </td>
                                                                </tr>
                                                            {/if}
                                                            <tr>
                                                                <th class="myth">{__('نحوه پرداخت',"inax")}</th>
                                                                <td class="text-center">{if $link.payment_type eq 'credit'}{__('پرداخت از اعتبار نمایندگی',"inax")}{elseif $link.payment_type eq 'online'}{__('آنلاین',"inax")}{else}{$link.payment_type}{/if}</td>
                                                            </tr>
                                                            {if !empty($link.product_id) }
                                                                <tr>
                                                                    <th class="myth" >{__('آیدی محصول',"inax")}</th>
                                                                    <td class="text-center ">{$link.product_id}</td>
                                                                </tr>
                                                            {/if}
                                                            {if !empty($link.product_name) }
                                                                <tr>
                                                                    <th class="myth" >{__('نام محصول',"inax")}</th>
                                                                    <td class="text-center ">{$link.product_name}</td>
                                                                </tr>
                                                            {/if}

                                                            {if $link.type eq 'bill'}
                                                                <tr>
                                                                    <th class="myth" >{__('نوع قبض',"inax")}</th>
                                                                    <td class="text-center ">{if $language_code eq 'fa'}{$link.bill_type|inax_bill_type_fa}{else}{$link.bill_type}{/if}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="myth" >{__('شناسه قبض',"inax")}</th>
                                                                    <td class="text-center ">{$link.bill_id}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="myth" >{__('شناسه پرداخت',"inax")}</th>
                                                                    <td class="text-center ">{$link.pay_id}</td>
                                                                </tr>
                                                            {/if}
                                                            <tr>
                                                                <th class="myth" >{__('شماره سفارش',"inax")}</th>
                                                                <td class="text-center ">{$link.order_id}</td>
                                                            </tr>
                                                            {if $link.type eq 'pin' && !empty($link.buyed_output) && $link.status eq 'paid' }
                                                                <tr>
                                                                    <th class="myth" >{__('جزئیات شارژ',"inax")}</th>
                                                                    <td class="text-right" >{$link.buyed_output}</td>
                                                                </tr>
                                                            {/if}
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary inax-font" data-bs-dismiss="modal">{__('بستن',"inax")}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="11" class="text-center">{__('تراکنشی یافت نشد',"inax")}</td>
                        </tr>
                    {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/if}

{include file="client/$inax_theme/footer.tpl"}
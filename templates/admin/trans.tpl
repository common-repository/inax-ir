{include file="admin/header.tpl"}

<div class="card shadow mb-4 p-2 border-primary">
    <div class="card-header mb-2 p-2">
        <h6 class="m-0 font-weight-bold text-primary text-right inax-font"><i class="fa fa-database fa-fw"></i> لیست تراکنش ها</h6>
    </div>
    <div class="card-body text-right p-0">

        {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
        {if isset($smarty.get.danger) }<div class="alert alert-danger">{$smarty.get.danger}</div>{/if}

        {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}
        {if isset($smarty.get.success) }<div class="alert alert-success">{$smarty.get.success}</div>{/if}

        {*<!-- Nav tabs -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {if !isset($smarty.get.bill)}active{/if}" href="admin.php?page=inax_trans">شارژ مستقیم - پین - اینترنت ({$tr_count|number_format})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {if isset($smarty.get.bill)}active{/if}"  href="admin.php?page=inax_trans&bill">قبوض ({$bill_tr_count|number_format})</a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            {if !isset($smarty.get.bill)}
            <div class="tab-pane {if !isset($smarty.get.bill)}active{/if}" id="charge">*}{*
            </div>
            {/if}
        </div>*}

        <div class="table-responsive mt-1">
            <table class="table table-striped table-bordered table-hover listtable">
                <thead>
                <tr>
                    <th class="text-center">آیدی</th>
                    <th class="text-center">نام کاربر</th>
                    <th class="text-center">محصول</th>
                    <th class="text-center">موبایل</th>
                    <th class="text-center">اپراتور</th>
                    <th class="text-center">مبلغ (تومان)</th>
                    <th class="text-center">نحوه پرداخت</th>
                    <th class="text-center">درگاه</th>
                    <th class="text-center">تاریخ ایجاد</th>
                    <th class="text-center">تاریخ پرداخت</th>
                    <th class="text-center">شماره پیگیری آینکس</th>
                    <th class="text-center">رسید بانکی</th>
                    <th class="text-center" style="width:150px;">وضعیت پرداخت</th>
                    <th class="text-center" style="width:150px;">وضعیت تراکنش</th>
                    <th class="text-center" style="width:150px;">جزئیات</th>
                </tr>
                </thead>
                <tbody>
                {if isset($charge_rows)}
                    {foreach from=$charge_rows key=key item=link}
                        <tr>
                            <td class="text-center"><a style="text-decoration: none;" href="admin.php?page=inax_trans&id={$link.id}">{$link.id}</a></td>
                            <td class="text-center">{if $link.client_id eq 0 }مهمان{else}<a class="text-decoration-none" href="user-edit.php?user_id={$link.client_id}" target="_blank">{$link.client_name}</a>{/if}</td>
                            <td class="text-center">
                                {if $link.type eq 'topup'}شارژ مستقیم
                                {elseif $link.type eq 'pin'}شارژ پین
                                {elseif $link.type eq 'internet'}بسته اینترنت
                                {elseif $link.type eq 'bill'}پرداخت قبض
                                {/if}
                            </td>
                            <td class="text-center">{$link.mobile}</td>
                            <td class="text-center">
                                {if $link.operator eq 'MTN'}
                                    <a class="btn btn-warning disabled inax-font"  >ایرانسل</a>
                                {elseif $link.operator eq 'MCI'}
                                    <a class="btn btn-info disabled inax-font">همراه اول</a>
                                {elseif $link.operator eq 'RTL'}
                                    <a class="btn btn-danger disabled inax-font">رایتل</a>
                                {elseif $link.operator eq 'SHT'}
                                    <a class="btn btn-default disabled inax-font">شاتل موبایل</a>
                                {else}
                                    {$link.operator}
                                {/if}
                            </td>
                            <td class="text-center">{$link.amount|number_format}</td>
                            <td class="text-center">{if $link.payment_type eq 'online'}<span class="btn btn-secondary disabled inax-font">آنلاین</span>{elseif $link.payment_type eq 'credit'}<span class="btn btn-outline-secondary disabled inax-font">اعتبار</span>{/if}</td>
                            <td class="text-center">{if $link.gateway eq 'irpul'}<a href="https://irpul.ir" style="text-decoration: none;" target="_blank">ایرپول</a>{elseif $link.gateway eq 'mellat'}ملت{else}{$link.gateway}{/if}</td>
                            <td class="text-center" ><span style="direction:ltr;display:inline-block;" >{if $link.date neq '0000-00-00 00:00:00' && !empty($link.date) }{$link.date|inax_jdate_format}{else}- - -{/if}</span></td>
                            <td class="text-center" ><span style="direction:ltr;display:inline-block;" >{if $link.pay_date neq '0000-00-00 00:00:00' && !empty($link.pay_date) }{$link.pay_date|inax_jdate_format}{else}- - -{/if}</span></td>
                            <td class="text-center">{$link.ref_code}</td>
                            <td class="text-center">{$link.gateway_ref_code}</td>
                            <td class="text-center">
                                {if $link.status eq 'paid'}
                                    <a class="btn btn-success disabled inax-font"><i class="fa fa-check"></i> پرداخت شده</a>
                                {elseif $link.status eq 'unpaid'}
                                    <a class="btn btn-danger disabled inax-font"><i class="fa fa-close"></i> پرداخت نشده</a>
                                {/if}
                            </td>
                            <td class="text-center">
                                {if $link.final_status eq 'success'}
                                    <span class="btn btn-success disabled inax-font"><i class="fa fa-check"></i> موفق</span>
                                {elseif $link.final_status eq '' or $link.final_status eq null}
                                    <span class="btn btn-warning disabled inax-font"><i class="fa fa-close"></i> معلق</span>
                                {if $link.status eq 'paid' and ($link.final_status eq '' or $link.final_status eq null)}<br/>اجازه صدور مجدد دارد{/if}

                                {/if}
                            </td>
                            <td class="text-center">
                                <!--<a href="#TB_inline?width=600&height=550&inlineId=modal-window-id-{$link.id}" class="thickbox">Modal Me</a>
                                <div id="modal-window-id-{$link.id}" style="display:none;">
                                    <p>Lorem Ipsum sit dolla amet.</p>
                                </div>-->

                                <a href="#TB_inline?width=600&height=550&inlineId=modal-{$link.id}" class="btn btn-info btn-xs thickbox inax-font" >جزئیات</a>

                                <div id="modal-{$link.id}" style="display:none;">
                                    <p style="direction: rtl;text-align: right">جزئیات تراکنش {$link.id}</p>

                                    <nav>
                                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            <button class="nav-link active" id="nav-detail-tab-{$link.id}" data-bs-toggle="tab" data-bs-target="#nav-detail-{$link.id}" type="button" role="tab" aria-controls="nav-detail-{$link.id}" aria-selected="true">جزئیات</button>
                                            <button class="nav-link" id="nav-actions-tab-{$link.id}" data-bs-toggle="tab" data-bs-target="#nav-actions-{$link.id}" type="button" role="tab" aria-controls="nav-actions-{$link.id}" aria-selected="false">عملگر</button>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="nav-tabContent">
                                        <!--  جزئیات -->
                                        <div class="tab-pane fade show active" id="nav-detail-{$link.id}" role="tabpanel" aria-labelledby="nav-detail-tab-{$link.id}">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover">
                                                    {if !empty($link.sim_type) }
                                                    <tr>
                                                        <th class="myth" >نوع سیم کارت</th>
                                                        <td class="text-center ">{if $link.sim_type eq 'credit'}اعتباری
                                                            {elseif $link.sim_type eq 'permanent'}دایمی
                                                            {elseif $link.sim_type eq 'TDLTE_credit'}سیم کارت TD-LTE اعتباری
                                                            {elseif $link.sim_type eq 'TDLTE_permanent'}سیم کارت TD-LTE دائمی
                                                            {elseif $link.sim_type eq 'data'}سیم کارت دیتا
                                                            {/if}
                                                        </td>
                                                    </tr>
                                                    {/if}
                                                    {if $link.type eq 'bill'}
                                                        <tr>
                                                            <th class="myth" >نوع قبض</th>
                                                            <td class="text-center ">{$link.bill_type|inax_bill_type_fa}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="myth" >شناسه قبض</th>
                                                            <td class="text-center ">{$link.bill_id}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="myth" >شناسه پرداخت</th>
                                                            <td class="text-center ">{$link.pay_id}</td>
                                                        </tr>
                                                    {/if}
                                                    {if $link.mnp eq 1}
                                                    <tr>
                                                        <th class="myth" >ترابرد پذیری</th>
                                                        <td class="text-center ">سیم کارت ترابرد شده است</td>
                                                    </tr>
                                                    {/if}
                                                    {if !empty($link.internet_type) }
                                                    <tr>
                                                        <th class="myth" >نوع اینترنت</th>
                                                        <td class="text-center ">{$link.internet_type|inax_internet_type_fa}</td>
                                                    </tr>
                                                    {/if}
                                                    {if !empty($link.description) }
                                                    <tr>
                                                        <th class="myth" style="width:150px;">توضیحات</th>
                                                        <td class="text-center">{$link.description}</td>
                                                    </tr>
                                                    {/if}
                                                    <tr>
                                                        <th class="myth">نحوه پرداخت مبلغ شارژ</th>
                                                        <td class="text-center">{if $link.payment_type eq 'credit'}پرداخت از اعتبار آینکس{elseif $link.payment_type eq 'online'}پرداخت آنلاین{else}{$link.payment_type}{/if}</td>
                                                    </tr>
                                                    {if !empty($link.product_id) }
                                                    <tr>
                                                        <th class="myth" >آیدی محصول</th>
                                                        <td class="text-center ">{$link.product_id}</td>
                                                    </tr>
                                                    {/if}
                                                    {if !empty($link.product_name) }
                                                    <tr>
                                                        <th class="myth" >نام محصول</th>
                                                        <td class="text-center ">{$link.product_name}</td>
                                                    </tr>
                                                    {/if}
                                                    {if !empty($link.trans_id) }
                                                    <tr>
                                                        <th class="myth" >شناسه تراکنش آینکس</th>
                                                        <td class="text-center "><a href="https://inax.ir/panel/transaction.php?id={$link.trans_id}" target="_blank">{$link.trans_id}</a></td>
                                                    </tr>
                                                    {/if}
                                                    
                                                    {if !empty($link.pay_result_string) }
                                                        <tr>
                                                            <th class="myth" >جزئیات خرید محصول</th>
                                                            <td class="text-left" ><span style="direction:ltr;display:inline-block;">{$link.pay_result_string}{*prevent javascript compile *}</span></td>
                                                        </tr>
                                                    {/if}

                                                    {if !empty($link.mode) }
                                                        <tr>
                                                            <th class="myth" >mode</th>
                                                            <td >{$link.mode}</td>
                                                        </tr>
                                                    {/if}
                                                </table>
                                            </div>
                                        </div>

                                        <!-- عملگر ها -->
                                        <div class="tab-pane fade" id="nav-actions-{$link.id}" role="tabpanel" aria-labelledby="nav-actions-tab-{$link.id}">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <tr>
                                                        <th class="myth" >تغییر وضعیت پرداخت</th>
                                                        <td class="text-center ">
                                                            <form action="admin.php?page=inax_trans&do_action=change_status&trans_id={$link.id}" method="post">
                                                                <select name="new_status" class="myform-control" >
                                                                    <option {if ($link.status) eq 'paid'}selected="selected"{/if} value="paid">پرداخت شده</option>
                                                                    <option {if ($link.status) eq 'unpaid'}selected="selected"{/if} value="unpaid">پرداخت نشده</option>
                                                                    <!--<option {if ($link.status) eq 'refund'}selected{/if} value="refund">عودت وجه به پنل</option>
                                                            <option {if ($link.status) eq 'refund_to_card'}selected{/if} value="refund_to_card">عودت وجه به کارت</option>-->
                                                                </select>
                                                                <input type="submit" name="submit" class="btn btn-info inax-font" value="تغییر وضعبت" />
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="myth" >تغییر وضعیت خرید محصول</th>
                                                        <td class="text-center ">
                                                            <form action="admin.php?page=inax_trans&do_action=change_pr_status&trans_id={$link.id}" method="post">
                                                                <select name="new_pr_status" class="myform-control" >
                                                                    <option {if ($link.final_status) eq 'success'}selected="selected"{/if} value="success">موفق</option>
                                                                    <option {if (($link.final_status) eq '') or (($link.final_status) eq null) }selected="selected"{/if} value="">معلق</option>
                                                                </select>
                                                                <input type="submit" name="submit" class="btn btn-info inax-font" value="تغییر وضعبت" />
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="myth" >تغییر وضعیت ترابرد</th>
                                                        <td class="text-center ">
                                                            <form action="admin.php?page=inax_trans&do_action=change_mnp&trans_id={$link.id}" method="post">
                                                                <select name="new_mnp" class="myform-control" >
                                                                    <option {if ($link.mnp) eq '1'}selected="selected"{/if} value="1">تربرد شده</option>
                                                                    <option {if ($link.mnp) eq ''}selected="selected"{/if} value="">ترابرد نشده</option>
                                                                </select>
                                                                <input type="submit" name="submit" class="btn btn-info inax-font" value="تغییر ترابرد" />
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="myth" >تغییر اپراتور</th>
                                                        <td class="text-center ">
                                                            <form action="admin.php?page=inax_trans&do_action=change_operator&trans_id={$link.id}" method="post">
                                                                <select name="new_operator" class="myform-control" >
                                                                    <option {if ($link.operator) eq 'MTN'}selected="selected"{/if} value="MTN">ایرانسل</option>
                                                                    <option {if ($link.operator) eq 'MCI'}selected="selected"{/if} value="MCI">همراه اول</option>
                                                                    <option {if ($link.operator) eq 'RTL'}selected="selected"{/if} value="RTL">رایتل</option>
                                                                    <option {if ($link.operator) eq 'SHT'}selected="selected"{/if} value="SHT">شاتل</option>
                                                                </select>
                                                                <input type="submit" name="submit" class="btn btn-info inax-font" value="تغییر اپراتور" />
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="myth" >تغییر نوع سیم کارت</th>
                                                        <td class="text-center ">
                                                            <form action="admin.php?page=inax_trans&do_action=change_sim_type&trans_id={$link.id}" method="post">
                                                                {*$link.sim_type*}
                                                                <select name="new_sim_type" class="myform-control" >
                                                                    <option {if ($link.sim_type) eq 'credit'}selected="selected"{/if} value="credit">اعتباری</option>
                                                                    <option {if ($link.sim_type) eq 'permanent'}selected="selected"{/if} value="permanent">دائمی</option>
                                                                    <option {if ($link.sim_type) eq 'TDLTE_credit'}selected="selected"{/if} value="TDLTE_credit">سیم کارت TD-LTE اعتباری</option>
                                                                    <option {if ($link.sim_type) eq 'TDLTE_permanent'}selected="selected"{/if} value="TDLTE_permanent">سیم کارت TD-LTE دائمی</option>
                                                                    <option {if ($link.sim_type) eq 'data'}selected="selected"{/if} value="data">سیم کارت دیتا</option>
                                                                </select>
                                                                <input type="submit" name="submit" class="btn btn-info inax-font" value="تغییر اپراتور" />
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="myth" >ذخیره شماره پیگیری آینکس</th>
                                                        <td class="text-center ">
                                                            <form action="admin.php?page=inax_trans&do_action=change_ref_code&trans_id={$link.id}" method="post">
                                                                <input type="text" dir="ltr" class="myform-control" name="new_ref_code" value="{$link.ref_code}" />
                                                                <input type="submit" name="submit" class="btn btn-info inax-font" value="ذخیره رسید" />
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    {if $link.status eq 'paid' and ($link.final_status eq '' or $link.final_status eq null)}
                                                        <tr>
                                                            <th class="myth">ارسال مجدد درخواست</th>
                                                            <td class="text-center">لطفا پیش از ارسال درخواست خرید مجدد از ناموفق بودن تراکنش در آینکس مطمئن شوید
                                                                <a onclick="retry_buy( '{$link.id}' );return false" href="#" target="_blank" class="btn btn-danger inax-font" >ارسال مجدد درخواست خرید</a>
                                                                <script>
                                                                    function retry_buy( id){
                                                                        if (confirm('آیا از ارسال مجدد درخواست خرید به آینکس مطمئن هستید ؟')){
                                                                           window.location="admin.php?page=inax_trans&do_action=buy&trans_id=" + id ;
                                                                        }
                                                                    }
                                                                </script>
                                                            </td>
                                                        </tr>
                                                    {/if}

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="16" class="text-center">هیچ تراکنشی یافت نشد.</td>
                    </tr>
                {/if}
                </tbody>
            </table>
            {$showpages}
        </div>

    </div>
</div>

{include file="admin/footer.tpl"}
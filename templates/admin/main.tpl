{include file="admin/header.tpl"}

<div class="card shadow mb-4 p-2 border-primary">
    <div class="card-header mb-2 p-2">
        <h6 class="m-0 font-weight-bold text-primary text-right inax-font"><i class="fa fa-gear fa-fw"></i> تنظیمات آینکس</h6>
    </div>
    <div class="card-body text-right p-0">
        <div class="alert alert-info"><a href="https://inax.ir/wordpress-plugin/" class="text-decoration-none" target="_blank">برای آموزش نحوه استفاده از این افزونه و همچنین دریافت اطلاعات وب سرویس اینجا کلیک کنید</a></div>
        <div style="clear:both;display:block"></div>

        <!--<a target="_blank" href="https://inax.ir/">
            <div class="inax-badge">آینکس</div>
        </a>-->

        <div class="table-responsive">


            {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
            {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

            <form method="POST" >
             {$wordpress_csrf}
            <table class="form-table" role="presentation">
                <tbody>
                    <!--<tr>
                        <th scope="row"><label for="blogname">عنوان سایت</label></th>
                        <td>
                            <input name="blogname" type="text" id="blogname" aria-describedby="blogname-description" value="لوکال" class="regular-text">
                            <p class="description" id="blogname-description">توضیح</p>
                        </td>
                    </tr>-->
                    <tr>
                        <th scope="row" style="width: 200px"><label for="inax_username">نام کاربری وب سرویس:</label></th>
                        <td>
                            <input type="text" id="inax_username" name="inax_username" class="regular-text" dir="ltr" size="40" value="{if isset($smarty.post.inax_username)}{$smarty.post.inax_username}{else}{$inax_option.username|esc_attr}{/if}" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_password">پسورد وب سرویس:</label></th>
                        <td>
                            <input type="text" id="inax_password" name="inax_password"  dir="ltr" size="40" value="{if isset($smarty.post.inax_password)}{$smarty.post.inax_password}{else}{$inax_option.password|esc_attr}{/if}" />

                            <a title="برای مشاهده راهنما کلیک کنید" data-bs-toggle="collapse" href="#collapse_pass" role="button" aria-expanded="false" aria-controls="collapse_pass"><i class="fa fa-info-circle fa-fw" ></i></a>
                            <div class="collapse" id="collapse_pass">
                                <div class="card-body alert-info my-1">
                                    در صورتی که اطلاعات وارد شده صحیح باشد در صفحه <a href="admin.php?page=inax_credit">موجودی آینکس</a>، اعتبار شما نمایش داده خواهد شد. ( اطلاعات ورود وب سرویس با اطلاعات ورود به پنل آینکس متفاوت است)
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_wallet">کیف پول پیش فرض</label></th>
                        <td>
                            <select id="inax_wallet" name="inax_wallet" class="myform-control">
                                <option {if (isset($inax_option.wallet) && $inax_option.wallet eq '') || (isset($smarty.post.inax_wallet) && $smarty.post.inax_wallet eq '' ) }selected{/if}  value="" >عدم استفاده از کیف پول (پرداخت آنلاین)</option>
                                <option {if isset($inax_option.wallet) && $inax_option.wallet eq 'TeraWallet' || (isset($smarty.post.inax_wallet) && $smarty.post.inax_wallet eq 'TeraWallet' ) }selected{/if} value="TeraWallet">کیف پول TeraWallet ووکامرس (پرداخت توسط موجودی کیف پول)</option>
                                <option {if isset($inax_option.wallet) && $inax_option.wallet eq 'yith' || (isset($smarty.post.inax_wallet) && $smarty.post.inax_wallet eq 'yith' ) }selected{/if} value="yith">کیف پول yith ووکامرس (پرداخت توسط موجودی کیف پول)</option>
                            </select>

                            <a title="برای مشاهده راهنما کلیک کنید" data-bs-toggle="collapse" href="#collapse_walet" role="button" aria-expanded="false" aria-controls="collapse_walet"><i class="fa fa-info-circle fa-fw" ></i></a>
                            <div class="collapse" id="collapse_walet">
                                <div class="card-body alert-info my-1">
                                    <i class="fa fa-circle fa-fw" ></i> در صورتی که از افزونه های کیف پول استفاده میکنید کاربران می توانند از موجودی کیف پول خود،  جهت پرداخت استفاده کنند.
                                    <br/><i class="fa fa-circle fa-fw" ></i> در صورت نداشتن موجودی به درگاه پرداخت بانکی هدایت خواهند شد.
                                    <br/><i class="fa fa-circle fa-fw" ></i> برای استفاده از قابلیت کیف پول ووکامرس نصب افزونه <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">ووکامرس</a> و <a href="https://wordpress.org/plugins/woo-wallet/" target="_blank">کیف پول</a>  ضروری است.
                                </div>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_display_error">نمایش خطاها: </label></th>
                        <td>
                            <input type="checkbox" id="inax_display_error" name="inax_display_error" value="1" {if isset($inax_option.display_error) && $inax_option.display_error eq '1' }checked{/if} /><label for="inax_display_error"> نمایش خطاهای PHP (پس از رفع ایراد غیرفعال شود)</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_ajaxless"> حالت بدون ajax: </label></th>
                        <td>
                            <input type="checkbox" id="inax_ajaxless" name="inax_ajaxless" value="1" {if isset($inax_option.ajaxless) && $inax_option.ajaxless eq '1' }checked{/if} /><label for="inax_ajaxless"> عدم استفاده از Ajax در صفحه پرداخت قبض (در صورت عدم کارکرد صفحه پرداخت قبض، این گزینه را فعال نمائید)</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_newtopup"> ورژن جدید شارژ مستقیم</label></th>
                        <td>
                            <input type="checkbox" id="inax_newtopup" name="inax_newtopup" value="1" {if isset($inax_option.newtopup) && $inax_option.newtopup eq '1' }checked{/if} /><label for="inax_newtopup"> انتخاب خودکار اپراتور و ترابرد پذیری از روی شماره موبایل در صفحه خرید شارژ مستقیم</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_newinternet"> ورژن جدید بسته اینترنت</label></th>
                        <td>
                            <input type="checkbox" id="inax_newinternet" name="inax_newinternet" value="1" {if isset($inax_option.newinternet) && $inax_option.newinternet eq '1' }checked{/if} /><label for="inax_newinternet"> انتخاب خودکار اپراتور و ترابرد پذیری از روی شماره موبایل در صفحه خرید بسته اینترنت</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_time_limitation">محدودیت زمانی خرید</label></th>
                        <td>
                            <select id="inax_time_limitation" name="inax_time_limitation" class="myform-control" id="inax_time_limitation" >
                                <option value="" {if (isset($inax_option.time_limitation) && $inax_option.time_limitation eq '') || (isset($smarty.post.time_limitation) && $smarty.post.inax_time_limitation eq '' ) }selected{/if} >بدون محدودیت</option>
                                <option value="1" {if (isset($inax_option.time_limitation) && $inax_option.time_limitation eq '1') || (isset($smarty.post.time_limitation) && $smarty.post.inax_time_limitation eq '1' ) }selected{/if} >1 دقیقه</option>
                                <option value="5" {if (isset($inax_option.time_limitation) && $inax_option.time_limitation eq '5') || (isset($smarty.post.time_limitation) && $smarty.post.inax_time_limitation eq '5' ) }selected{/if} >5 دقیقه</option>
                                <option value="15" {if (isset($inax_option.time_limitation) && $inax_option.time_limitation eq '15') || (isset($smarty.post.time_limitation) && $smarty.post.inax_time_limitation eq '15' ) }selected{/if} >15 دقیقه</option>
                                <option value="30" {if (isset($inax_option.time_limitation) && $inax_option.time_limitation eq '30') || (isset($smarty.post.time_limitation) && $smarty.post.inax_time_limitation eq '30' ) }selected{/if} >30 دقیقه</option>
                                <option value="60" {if (isset($inax_option.time_limitation) && $inax_option.time_limitation eq '60') || (isset($smarty.post.time_limitation) && $smarty.post.inax_time_limitation eq '60' ) }selected{/if} >60 دقیقه</option>
                            </select>
                            <p class="description" id="inax_time_limitation">برای جلوگیری از ایجاد تراکنش موفق برای یک شماره موبایل در بازه زمانی مشخص شده، این گزینه را فعال نمائید</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_amount_limitation">محدودیت قیمت</label></th>
                        <td>
                            <input type="text" id="inax_amount_limitation" name="inax_amount_limitation" class="regular-text" dir="ltr" size="30" onkeyup="number_to_letter('inax_amount_limitation');"   value="{if isset($smarty.post.inax_amount_limitation)}{$smarty.post.inax_amount_limitation}{else}{$inax_option.amount_limitation|esc_attr|number_format}{/if}" />
                            <p class="description" id="inax_amount_limitation">حداکثر مبلغ قابل قبول برای خرید شارژ مستقیم را در این فیلد وارد نمائید (پیش فرض سیستم : 0)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_theme">قالب کاربری</label></th>
                        <td>
                            <select id="inax_theme" name="inax_theme" class="myform-control">
                                <option {if (isset($inax_option.theme) && $inax_option.theme eq 'default') || (isset($smarty.post.theme) && $smarty.post.inax_theme eq 'default' ) }selected{/if}  value="default" >قالب پیش فرض</option>
                                <!--<option {if isset($inax_option.theme) && $inax_option.theme eq 'perple' || (isset($smarty.post.inax_theme) && $smarty.post.inax_theme eq 'perple' ) }selected{/if} value="perple">قالب بنفش</option>-->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="inax_payment">درگاه پرداخت آنلاین: </label></th>
                        <td>
                            <select id="inax_payment" name="inax_payment" class="myform-control">
                                <option {if (isset($inax_option.payment_gateway) && $inax_option.payment_gateway eq '') || (isset($smarty.post.inax_payment) && $smarty.post.inax_payment eq '' ) }selected{/if}  value="" >درگاه پرداخت آینکس</option>
                                {if isset($html_elements)}
                                {foreach from=$html_elements key=key item=link}
                                    <option value="{$link.gateway}" {if isset($inax_option.payment_gateway) && $inax_option.payment_gateway eq "{$link.gateway}" }selected="selected"{/if} >{$link.gateway_fa}</option>
                                {/foreach}
                                {/if}
                            </select>
                            <!--<option {if isset($inax_option.payment_gateway) && $inax_option.payment_gateway eq 'mine' || (isset($smarty.post.inax_payment) && $smarty.post.inax_payment eq 'mine' ) }selected{/if} {if !isset($html_elements)}disabled="disabled"{/if} value="mine">درگاه پرداخت خودم</option>-->

                            <p class="description" id="blogname-description">توسط این گزینه می توانید مشخص نمائید وجه تراکنش ها در خرید شارژ به صورت آنلاین، توسط کدام درگاه پرداخت شود (در صورتی که درگاه ندارید گزینه درگاه پرداخت آینکس را انتخاب نمائید)</p>
                        </td>
                    </tr>
                    {if isset($inax_option.payment_gateway) && $inax_option.payment_gateway eq 'mine' && isset($html_elements) }
                        <!---->
                    {foreach from=$html_elements key=key item=link}
                    {if $key eq 0 }<tr><th width="300"><label for="inax_field_status">وضعیت فعال بودن درگاه</label></th><td scope="row" ><input id="inax_field_status" type="checkbox" {if $link.status eq '1'}checked="checked"{/if} name="field[{$link.gateway}][status]" /> </td></tr>{/if}
                     <tr>
                        <th scope="row"><label for="inax_field_{$link.name}">{$link.label}</label></th>
                        <td>
                            {if $link.type eq 'text' }<input id="inax_field_{$link.name}"  type='{$link.type}' name='field[{$link.gateway}][{$link.name}]' size='{$link.size}' value='{$link.value}' />{/if}
                            {if $link.description neq ''}<p class='description' id='blogname-description'>{$link.description}</p>{/if}
                        </td>
                    </tr>
                    {/foreach}
                    {/if}
                    <!--<tr>
                        <th></th>
                        <td>
                            <p class="submit">
                                <button type="submit" name="save_inax_options" class="btn btn-primary btn-sm inax-font" id="save_inax_options" ><i class="fa fa-copy fa-fw"></i> ذخیره تغییرات</button>
                            </p>
                        </td>
                    </tr>-->

                </tbody>
            </table>

                <p class="submit">
                    <button type="submit" name="save_inax_options" class="button button-primary" id="save_inax_options" ><i class="fa fa-copy fa-fw"></i> ذخیره تغییرات</button>
                </p>
            </form>
        </div>

    </div>
</div>

{include file="admin/footer.tpl"}
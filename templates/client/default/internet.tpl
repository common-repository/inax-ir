{include file="client/$inax_theme/header.tpl"}

<div class="row">
    <div class="col-lg-12">

        {if (isset($select_operator) && !isset($internet_result)) }
			<div class="card shadow mb-4 border-primary">
				<div class="card-header text-right">
                    <i class="fa fa-shopping-cart fa-fw"></i>
                    {$title} {if $is_user_logged_in eq 1}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$trans_link}"><i class="fa fa-database fa-fw"></i> {__('لیست تراکنش ها',"inax")}</a>{/if}
                    {if isset($main_link)}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none mx-1 inax-font" href="{$main_link}"><i class="fa fa-home fa-fw"></i> {__('صفحه اصلی فروشگاه',"inax")}</a>{/if}
                </div>
				<div class="card-body text-right p-0">
                    <div class="alert alert-primary">{__('لطفا اپراتور تلفن همراه خود را انتخاب نمائید',"inax")}</div>

                    <div class="album py-2 bg-light">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                            <div class="col text-center">
                                <div class="card shadow-sm border-warning border-2">
                                    <a title="{__('بسته اینترنت ایرانسل',"inax")}" href="{$permalink}MTN" class="text-decoration-none">
                                        <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}mtn_internet.png{else}mtn_internet_en.png{/if}" class="card-img-top" alt="{__('بسته اینترنت ایرانسل',"inax")}">

                                        <div class="card-body">
                                            <p class="card-text">{__('بسته اینترنت ایرانسل',"inax")}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col text-center">
                                <div class="card shadow-sm border-info border-2">
                                    <a title="{__('بسته اینترنت همراه اول',"inax")}" href="{$permalink}MCI" class="text-decoration-none border-shadow ">
                                        <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}mci_internet.png{else}mci_internet_en.png{/if}" class="card-img-top" alt="{__('بسته اینترنت همراه اول',"inax")}">
                                        <div class="card-body">
                                            <p class="card-text">{__('بسته اینترنت همراه اول',"inax")}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col text-center ">
                                <div class="card shadow-sm border-perple border-2">
                                    <a title="{__('بسته اینترنت رایتل',"inax")}" href="{$permalink}RTL" class="text-decoration-none">
                                        <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}rtl_internet.png{else}rtl_internet_en.png{/if}" class="card-img-top" alt="{__('بسته اینترنت رایتل',"inax")}">
                                        <div class="card-body">
                                            <p class="card-text">{__('بسته اینترنت رایتل',"inax")}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col text-center ">
                                <div class="card shadow-sm border-primary border-2">
                                    <a title="{__('بسته اینترنت شاتل موبایل',"inax")}ل" href="{$permalink}SHT" class="text-decoration-none">
                                        <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}sht_internet.png{else}sht_internet_en.png{/if}" class="card-img-top" alt="{__('بسته اینترنت شاتل موبایل',"inax")}">
                                        <div class="card-body">
                                            <p class="card-text">{__('بسته اینترنت شاتل موبایل',"inax")}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

				</div>
            </div>
        {/if}

        {if isset($request_sim_type) && $request_sim_type }
            <div class="card shadow mb-4 border-primary bg-{if isset($mtn_active) }warning{elseif isset($mci_active)}info{elseif isset($rtl_active)}perple{elseif isset($sht_active)}info{/if}">
                <div class="card-header text-right {if isset($mtn_active) }text-dark{elseif isset($mci_active)}text-light{elseif isset($rtl_active)}text-light{elseif isset($sht_active)}text-light{/if}">
                    <i class="fa fa-shopping-cart fa-fw"></i> {__('خرید بسته اینترنت',"inax")} {if isset($mtn_active) }{__('ایرانسل',"inax")}{elseif isset($mci_active)}{__('همراه اول',"inax")}{elseif isset($rtl_active)}{__('رایتل',"inax")}{elseif isset($sht_active)}{__('شاتل موبایل',"inax")}{/if}
                    <a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$p_url}"> {__('بازگشت',"inax")} <i class="fa fa-arrow-left fa-fw"></i></a>
                </div>
                <div class="card-body bg-light">
                    <div class="alert text-right alert-info">{__('لطفا نوع سیم کارت تلفن همراهی که قصد خرید بسته اینترنت برای آن را دارید انتخاب نمائید:',"inax")}</div>

					{if isset($mtn_active) || isset($mci_active) || isset($rtl_active) || isset($sht_active) }
						<a href="{$permalink}{$operator}&sim=credit" title="{__('سیم کارت اعتباری','inax')}" class="sim_type_box inax-font" >{__('سیم کارت اعتباری',"inax")}</a>
					{/if}
					
					{if isset($mtn_active) || isset($mci_active) || isset($rtl_active) }
						<a href="{$permalink}{$operator}&sim=permanent" title="{__('سیم کارت دائمی','inax')}" class="sim_type_box inax-font" >{__('سیم کارت دائمی',"inax")}</a>
					{/if}
					
					{if isset($mtn_active) || isset($mci_active) }
						<a href="{$permalink}{$operator}&sim=TDLTE_credit" title="{__('سیم کارت TD-LTE اعتباری','inax')}" class="sim_type_box inax-font" >{__('سیم کارت TD-LTE اعتباری','inax')}</a>
						<a href="{$permalink}{$operator}&sim=TDLTE_permanent" title="{__('سیم کارت TD-LTE دائمی','inax')}" class="sim_type_box inax-font" >{__('سیم کارت TD-LTE دائمی','inax')}</a>
					{/if}
					
					{if  isset($mci_active) }
						<a href="{$permalink}{$operator}&sim=data" title="{__('سیم کارت دیتا','inax')}" class="sim_type_box inax-font" >{__('سیم کارت دیتا','inax')}</a>
					{/if}
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

						{if isset($have_package)}
                            <div class="accordion" id="accordionExample">
								{foreach from=$have_package key=key item=link}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header inax-font" id="heading_{$link.type_en}">
                                            <button class="accordion-button {if !$link@first }collapsed{/if}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{$link.type_en}" aria-expanded="{if $link@first }true{else}false{/if}" aria-controls="collapse_{$link.type_en}">
                                                {if $language_code eq 'fa' || $language_code eq 'ar'}{$link.type_fa}{else}{$link.type_en}{/if} ({$link.lists2|count} {__('بسته',"inax")})
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
                                                                <th class="text-center d-none d-sm-block">{__('دکمه خرید',"inax")}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            {if isset($link.lists2 )}
                                                                {foreach from=$link.lists2 key=key2 item=link2}
                                                                    <tr onclick="window.location='{$permalink}{$operator}&sim={$sim_type}&i={$link.type_en}&pid={$link2.id}';" style="cursor: pointer;" >
                                                                        <td class="text-center d-none d-sm-block" style="padding: 13px 3px 11px 3px !important;"><a href="{$permalink}{$operator}&sim={$sim_type}&i={$link.type_en}&pid={$link2.id}" disabled="" class="btn btn-default btn-sm inax-font">{$link2@iteration }</a></td>
                                                                        <td class="text-center" style="padding: 13px 3px 11px 3px !important;"><a class="text-decoration-none" href="{$permalink}{$operator}&sim={$sim_type}&i={$link.type_en}&pid={$link2.id}" title="{$link2.name}" >{$link2.name}</a></td>
                                                                        <td class="text-center text-{if isset($mtn_active) }dark{elseif isset($mci_active)}info{elseif isset($rtl_active)}danger{elseif isset($sht_active)}info{/if}" style="padding: 13px 3px 11px 3px !important;"> {$link2.amount|number_format} {__('تومان',"inax")}</td>
                                                                        <td class="text-center d-none d-sm-block" style="padding: 13px 3px 11px 3px !important;">
                                                                            <a href="{$permalink}{$operator}&sim={$sim_type}&i={$link.type_en}&pid={$link2.id}"  class="btn btn-{if isset($mtn_active) }warning{elseif isset($mci_active)}info{elseif isset($rtl_active)}danger{elseif isset($sht_active)}info{/if} text-decoration-none text-white btn-sm inax-font">{__('خرید آنلاین',"inax")}</a>
                                                                        </td>
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
                    {/if}

                </div>
            </div>
        {/if}

        {if  isset($enter_mobile) }
			<div class="card bg-{if isset($mtn_active) }warning{elseif isset($mci_active)}info{elseif isset($rtl_active)}perple{elseif isset($sht_active)}info{/if}">
                <div class="card-header text-right  {if isset($mtn_active) }text-dark{elseif isset($mci_active)}text-light{elseif isset($rtl_active)}text-light{elseif isset($sht_active)}text-light{/if}">
                    <i class="fa fa-shopping-cart fa-fw"></i> {__('خرید بسته اینترنت',"inax")} {if $operator eq 'MTN' }{__('ایرانسل',"inax")}{elseif $operator eq 'MCI' }{__('همراه اول',"inax")}{elseif $operator eq 'RTL' }{__('رایتل',"inax")}{elseif $operator eq 'SHT' }{__('شاتل موبایل',"inax")}{/if} <!--{*if isset($logged_in) && $logged_in*}<a class="btn btn-default btn-sm {if $language_code eq 'fa'}btn-left{else}btn-right{/if}" href="./?list"><i class="fa fa-database fa-fw"></i> گزارش خرید بسته های اینترنتی</a>{*/if*}-->
                    <a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$p_url}"> {__('بازگشت',"inax")} <i class="fa fa-arrow-left fa-fw"></i></a>
                </div>
                <div class="card-body bg-light">

                    {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
                    {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

                    {if isset($product_find) }
                        <div class="alert alert-info">{__('لطفا در فیلد زیر شماره تلفنی که قصد دارید بسته اینترنتی را برای آن خریداری نمائید را وارد کنید:',"inax")}</div>
                        <div class="table-responsive">

                            <form action="{$permalink}{$operator}&sim={$sim_type}&i={$internet_type}&pid={$product_id}" method="POST">
                                {$wordpress_csrf}

                                <table class="table table-hover text-center text-dark table-borderless {if isset($mtn_active) }table-warning{elseif isset($mci_active)}table-info{elseif isset($rtl_active)}table-danger{elseif isset($rtl_active)}table-info{/if}">
                                    <tr>
                                        <td class="text-center">
                                            {__('نام بسته',"inax")} : {$product_name}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            {__('مبلغ بسته',"inax")} : {$product_amount|number_format} {__('تومان',"inax")}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            {__('سیم کارت',"inax")} : {if $language_code eq 'fa'}{$sim_type|inax_sim_type_fa}{else}{$sim_type}{/if}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon2"><i class="fa fa-phone fa-fw fa-1x"></i></span>
                                                <input type="tel" autocomplete="off" class="form-control text-center" onkeyup="inax_check_numbers('inlineFormInputGroup');" id="inlineFormInputGroup" dir="ltr" maxlength="11" aria-label="{__('شماره تلفن همراه',"inax")}" aria-describedby="basic-addon1" placeholder="{__('شماره تلفن همراه',"inax")}" dir="ltr" name="mobile" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile}{/if}" tabindex="1" required>
                                            </div>
                                        </td>
                                    </tr>
                                    {if $is_user_logged_in eq 1}
                                    <tr>
                                        <td colspan="3">
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
                                        </td>
                                    </tr>
                                    {/if}
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="mnp" value="1" id="mnp_label"> <label for="mnp_label"> {__("در صورتی که شماره فوق به %s ترابرد شده است این گزینه را فعال نمائید.","inax")|sprintf:operator_fa($operator,$language_code)} </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <button class="btn btn-success form-control inax-font" name="submit" type="submit"><i class="fa fa-check"></i> {__('پرداخت آنلاین',"inax")}</button>
                                            {if isset($credit_payment)}<button class="btn btn-warning form-control border-success m-1 inax-font" {if $user_credit eq 0}disabled{/if} name="submit_credit" type="submit"><i class="fa fa-check"></i> {__("خرید از کیف پول (اعتبار %s تومان)","inax")|sprintf:number_format($user_credit)}</button>{/if}
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    {/if}
                </div>
            </div>
        {/if}

    </div>
</div>

{include file="client/$inax_theme/footer.tpl"}
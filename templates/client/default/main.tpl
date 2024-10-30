{include file="client/$inax_theme/header.tpl"}

<div class="row">
    <div class="col-lg-12">

        <div class="card shadow mb-4 border-default">
            <div class="card-header text-right">
                <i class="fa fa-shopping-cart fa-fw"></i> {__('صفحه اصلی فروشگاه',"inax")}
                {if $is_user_logged_in eq 1}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$trans_link}"><i class="fa fa-database fa-fw"></i> {__('لیست تراکنش ها',"inax")}</a>{/if}
            </div>
            <div class="card-body text-right p-0">

                <div class="alert alert-info">{__('به فروشگاه خرید شارژ و بسته اینترنت خوش آمدید',"inax")}</div>

                <div class="album py-2 bg-light">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                        {if !in_array('topup', $disabled_page)}
                        <div class="col text-center">
                            <div class="card shadow-sm border-warning border-2 p-2">
                                <a title="{__('خرید شارژ مستقیم', "inax" )}" href="{$topup_link}" class="text-decoration-none">
                                    <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}topup.png{else}topup_en.png{/if}" class="card-img-top" alt="{__('خرید شارژ مستقیم', "inax" )}">

                                    <!--<div class="card-body">
                                        <p class="card-text">خرید شارژ مستقیم</p>
                                    </div>-->
                                </a>
                            </div>
                        </div>
                        {/if}

                        {if !in_array('pin', $disabled_page)}
                        <div class="col text-center">
                            <div class="card shadow-sm border-info border-2 p-2">
                                <a title="{__('خرید شارژ پین', "inax" )}" href="{$pin_link}" class="text-decoration-none border-shadow ">
                                    <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}pin.png{else}pin_en.png{/if}" class="card-img-top" alt="{__('خرید شارژ پین', "inax" )}">
                                    <!--<div class="card-body">
                                        <p class="card-text">خرید کارت شارژ</p>
                                    </div>-->
                                </a>
                            </div>
                        </div>
                        {/if}

                        {if !in_array('internet', $disabled_page)}
                        <div class="col text-center ">
                            <div class="card shadow-sm border-perple border-2 p-2">
                                <a title="{__('خرید بسته اینترنت', "inax" )}" href="{$internet_link}" class="text-decoration-none">
                                    <img class="bd-placeholder-img card-img-top" width="90%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}internet.png{else}internet_en.png{/if}" class="card-img-top" alt="{__('خرید بسته اینترنت', "inax" )}">
                                    <!--<div class="card-body">
                                      <p class="card-text">خرید بسته اینترنت</p>
                                    </div>-->
                                </a>
                            </div>
                        </div>
                        {/if}

                        {if !in_array('bill', $disabled_page)}
                        <div class="col text-center ">
                            <div class="card shadow-sm border-danger border-2 p-2">
                                <a title="{__('پرداخت قبض', "inax" )}" href="{$bill_link}" class="text-decoration-none">
                                    <img class="bd-placeholder-img card-img-top" width="100%" height="auto" src="{$inax_img_url}/{if $language_code eq 'fa'}bill.png{else}bill_en.png{/if}" class="card-img-top" alt="{__('پرداخت قبض', "inax" )}">
                                    <!--<div class="card-body">
                                        <p class="card-text">پرداخت قبض</p>
                                    </div>-->
                                </a>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{include file="client/$inax_theme/footer.tpl"}
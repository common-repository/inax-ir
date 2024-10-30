<!--begin inax-ir plugin-->
<div class="wrap">
    <nav class="nav-tab-wrapper woo-nav-tab-wrapper" style="margin-bottom:20px">
        <a href="admin.php?page=inaxir" class="nav-tab {if isset($smarty.get.page) && $smarty.get.page eq 'inaxir' }nav-tab-active{/if}" >عمومی</a>
        <!--<a href="admin.php?page=inaxir&tab=checkout" class="nav-tab ">درگاه ها</a>-->
        <a href="admin.php?page=inax_page" class="nav-tab {if isset($smarty.get.page) && $smarty.get.page eq 'inax_page' }nav-tab-active{/if}" >صفحات خرید</a>
        <a href="admin.php?page=inax_credit" class="nav-tab {if isset($smarty.get.page) && $smarty.get.page eq 'inax_credit' }nav-tab-active{/if}" >موجودی آینکس</a>
        <a href="admin.php?page=inax_trans" class="nav-tab {if isset($smarty.get.page) && $smarty.get.page eq 'inax_trans' }nav-tab-active{/if}" >تراکنش ها</a>
        <a href="admin.php?page=inax_gateway" class="nav-tab {if isset($smarty.get.page) && $smarty.get.page eq 'inax_gateway' }nav-tab-active{/if}" >درگاه های بانکی</a>
        <a href="admin.php?page=inax_system" class="nav-tab {if isset($smarty.get.page) && $smarty.get.page eq 'inax_system' }nav-tab-active{/if}" >جزئیات سیستم</a>
        <a href="admin.php?page=inax_about" class="nav-tab {if isset($smarty.get.page) && $smarty.get.page eq 'inax_about' }nav-tab-active{/if}">درباره ما</a>
    </nav>

    {if isset($head_error) }<div class="alert alert-danger">{$head_error}</div>{/if}
    {if isset($test_mode) && $test_mode }<div class="alert alert-danger">حالت Test Mode در خرید از آینکس روشن است.</div>{/if}
    {if isset($gateway_test_mode) && $gateway_test_mode }<div class="alert alert-danger">{$gateway_test_mode}</div>{/if}
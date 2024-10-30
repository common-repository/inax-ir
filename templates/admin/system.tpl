{include file="admin/header.tpl"}

<div class="card shadow mb-4 p-2 border-primary">
    <div class="card-header mb-2 p-2">
        <h6 class="m-0 font-weight-bold text-primary text-right inax-font"><i class="fa fa-star fa-fw"></i> جزئیات سیستم</h6>
    </div>
    <div class="card-body text-right p-0">

        <div style="clear:both;"></div>

        <!--<h6 class="inax-font" >اطلاعات سیستم</h6>-->
        <!--<small>-->
            - ورژن افزونه آینکس : <b class="inax-font">{$inax_plugin_version}</b><br/>
            - ورژن دیتابیس آینکس : <b class="inax-font">{$inax_db_version}</b><br/>
            <hr/>
            - سوکت تایم اوت : <b class="inax-font">{$default_socket_timeout} ثانیه</b> (مقدار توصبه شده مابین 30 الی 60 ثانیه)<br/>
            - زمان لود صفحه : <b class="inax-font">{$execute_time} ثانیه</b><br/>
            <hr/>

            - ساعت پی اچ پی : <b class="inax-font">{$timestamp|inax_jdate_format}</b><br/>
            - ساعت دیتابیس : <b class="inax-font">{$phpmyadmin_time|inax_jdate_format}</b><br/>
            <br/>
            <div class="alert alert-info">ساعت php باید برابر با ساعت دیتابیس باشد. در صورتی که برابر نیست با مدیریت هاستینگ خود تماس بگیرید</div>
            <div class="alert alert-info">افزونه های کش وردپرس ممکن است در عملکرد این افزونه اختلال ایجاد کند. در صورت استفاده از این افزونه ها، <a href="admin.php?page=inax_page">صفحات خرید</a> را مستثنی نمائید.</div>

            {if isset($change_sql_mode) }<hr/><br/><div class="alert alert-danger">لطفا از مدیر هاست خود بخواهید دستور زیر را در تیبل محل نصب وردپرس اجرا نماید<br/>
                <pre style="direction: ltr;text-align: left"><b class="inax-font">SET GLOBAL sql_mode = '';</b></pre>
                <hr/>
                مقادیر فعلی <br/><pre style="direction: ltr;text-align: left">SESSION sql_mode : {$session_sql_mode}<br/>GLOBAL sql_mode : {$global_sql_mode}</pre>
            </div>
            {/if}

        <!--</small>-->

    </div>
</div>

{include file="admin/footer.tpl"}
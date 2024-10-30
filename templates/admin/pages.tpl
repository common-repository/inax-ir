{include file="admin/header.tpl"}

<div class="card shadow mb-4 p-2 border-primary">
    <div class="card-header mb-2 p-2">
        <h6 class="m-0 font-weight-bold text-primary text-right inax-font"><i class="fa fa-database fa-fw"></i> صفحات خرید فروشگاه</h6>
    </div>
    <div class="card-body text-right p-0">
        {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
        {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

        <div class="table-responsive">
            <table class="form-table" role="presentation">
                <tbody>

                {if isset($inax_pages)}
                    <tr>
                        <th class="text-center" style="width:80px">ردیف</th>
                        <th class="text-center" scope="row" style="width: 220px" >صفحه</th>
                        <th class="text-center" style="width:80px">زبان برگه</th>
                        <th class="text-center">مشاهده برگه</th>
                        <th class="text-center" >توضیح</th>
                        <th class="text-center" colspan ="2">عملگر</th>
                    </tr>
                {foreach from=$inax_pages key=key item=link}
                    <tr>
                        <td class="text-center">{$link@iteration}</td>
                        <td class="text-center"><b class="inax-font">{$link.page_fa}</b></td>
                        <td class="text-center">{$link.lang}</td>
                        <td class="text-center">
                            {if isset($link.url) }
                                <a href="{$link.url}" target="_blank"  class="button button-primary" ><i class="fa fa-eye fa-fw"></i> مشاهده {$link.title}</a>
                            {else}
                                <a href="#" target="_blank" disabled class="button button-primary disabled" ><i class="fa fa-eye fa-fw"></i> صفحه وجود ندارد</a>
                            {/if}
                        </td>
                        <td class="text-center">
                            {$link.note}
                        </td>
                        <td class="text-center">
                            {if isset($link.id) }
                                <a href="post.php?post={$link.id}&action=edit" target="_blank" class="button button-default" ><i class="fa fa-edit fa-fw"></i> ویرایش صفحه</a>
                            {else}
                                <a href="admin.php?page=inax_page&insert={$link.page}" target="_blank" class="btn btn-success inax-font" ><i class="fa fa-pencil fa-fw"></i> ایجاد صفحه</a>
                            {/if}
                        </td>
                        <td class="text-center">
                            {if isset($link.id) }
                                {if $link.page_status eq 'enable'}
                                <a href="admin.php?page=inax_page&p={$link.page}&disable" class="btn btn-danger btn-sm inax-font" ><i class="fa fa-eye-slash fa-fw"></i> غیرفعال کردن</a>
                                {else if $link.page_status eq 'disable'}
                                <a href="admin.php?page=inax_page&p={$link.page}&enable" class="btn btn-success btn-sm inax-font" ><i class="fa fa-eye fa-fw"></i> فعال کردن</a>
                                {/if}
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                {else}
                    <tr>
                        <td colspan="7" class="text-center">هیچ صفحه خریدی ایجاد نشده است.</td>
                    </tr>
                {/if}
                </tbody>
            </table>
        </div>
    </div>
</div>

{include file="admin/footer.tpl"}
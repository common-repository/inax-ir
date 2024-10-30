{include file="admin/header.tpl"}

<div class="card shadow mb-4 p-2 border-primary">
    <div class="card-header mb-2 p-2">
        <h6 class="m-0 font-weight-bold text-primary text-right inax-font"><i class="fa fa-database fa-fw"></i> لیست درگاه های پرداخت بانکی</h6>
    </div>
    <div class="card-body text-right p-0">

        {if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
        {if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

        <div class="table-responsive ">

           {if isset($html_elements)}
           <form method="POST" >
               {$wordpress_csrf}
               <table class="form-table" role="presentation" >
                    <tbody>
                        {foreach from=$html_elements key=key item=link}

                            {if isset($link.name) && $link.name eq 'label' }
                                {*insert horizontal line after per gateway*}
                                {if $key neq 0}<tr><td colspan="3"><hr/></td></tr>{/if}

                                <tr>
                                    <th rowspan="{$link.elements_count}">
                                        <img src="{$link.logo}" width="120" height="120" />
                                    </th>
                                    <th style="width: 300px;" >
                                        <label for="inax_field_status">وضعیت {$link.gateway_fa}</label>
                                    </th>
                                    <td scope="row" >
                                        <input id="inax_field_status" type="checkbox" {if $link.status eq '1'}checked="checked"{/if} name="field[{$link.gateway}][status]" id="{$link.gateway}_status" />
                                        <label style="font-size: 14px!important;" for="{$link.gateway}_status"> برای فعال کردن {$link.gateway_fa} این گزینه را فعال نمائید</label>

                                        {if $link.is_default}<b class="text-success inax-font">( <i class="fa fa-check fa-fw text-success"></i>درگاه پیش قبض )</b>{/if}
                                    </td>
                                </tr>
                            {/if}

                            {if isset($link.name) && $link.name neq 'label' }
                             <tr>
                                <th scope="row"><label for="inax_field_{$link.name}">{$link.label}</label></th>
                                <td>
                                    {if $link.type eq 'text' }<input id="inax_field_{$link.name}"  type='{$link.type}' name='field[{$link.gateway}][{$link.name}]' size='{$link.size}' value='{$link.value}' />{/if}
                                    {if $link.type eq 'select' }
                                        <select name="field[{$link.gateway}][{$link.name}]">
                                            {foreach from=$link.options key=k item=v}
                                                <option {if $link.value eq "$k" }selected{/if} value="{$k}">{$v}</option>
                                            {/foreach}
                                        </select>
                                    {/if}
                                    {if $link.description neq ''}<p class='description' id='blogname-description'>{$link.description}</p>{/if}
                                </td>
                            </tr>
                            {/if}

                        {/foreach}
                    </tbody>
                </table>
                <p class="submit">
                    <button type="submit" name="save_inax_options" class="button button-primary" id="save_inax_options" ><i class="fa fa-copy fa-fw"></i> ذخیره تغییرات</button>
                </p>
            </form>
            {else}
                <div class="alert alert-warning">هیچ درگاه پرداخت اختصاصی وجود ندارد</div>
            {/if}
        </div>
    </div>
</div>

{include file="admin/footer.tpl"}
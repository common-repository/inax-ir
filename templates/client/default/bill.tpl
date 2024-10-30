{include file="client/$inax_theme/header.tpl"}

<div class="row">
	<div class="col-lg-12">
		<div class="card shadow mb-4 border-primary">

		{if isset($pay_bill)  }
			<div class="card-header text-right">
				<i class="fa fa-shopping-cart fa-fw"></i>
				{$title} {if $is_user_logged_in eq 1}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar' }btn-left{else}btn-right{/if} text-decoration-none inax-font" href="{$trans_link}"><i class="fa fa-database fa-fw"></i> {__('لیست تراکنش ها',"inax")}</a>{/if}
				{if isset($main_link)}<a class="btn btn-primary btn-sm {if $language_code eq 'fa' || $language_code eq 'ar'}btn-left{else}btn-right{/if} text-decoration-none mx-1 inax-font" href="{$main_link}"><i class="fa fa-home fa-fw"></i> {__('صفحه اصلی فروشگاه',"inax")}</a>{/if}
			</div>
			<div class="card-body text-right p-0">

			{if !$ajaxless  }
				<!-- use ajax -->
				<div class="alert alert-info">- {__('توسط این بخش می توانید نسبت به پرداخت قبوض آب، برق، گاز، تلفن همراه، تلفن ثابت ثابت، عوارض شهرداری، سازمان مالیات و جریمه راهنمایی و رانندگی اقدام کنید..',"inax")}</div>

				{if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}

				<div class="table-responsive">
					<table class="table table-default table-hover table-bordered" >
						<tr>
							<th>{__('شناسه قبض',"inax")}</th>
							<td><input type="tel" name="bill_id" dir="auto" onkeyup="inax_check_numbers('bill_id');" id="bill_id" maxlength="20" class="form-control" value="{if isset($smarty.post.bill_id)}{$smarty.post.bill_id}{elseif isset($bill_id)}{$bill_id}{/if}" size="35" required/></td>
						</tr>
						<tr>
							<th>{__('شناسه پرداخت',"inax")}</th>
							<td><input type="tel" name="pay_id" dir="auto" onkeyup="inax_check_numbers('pay_id');" id="pay_id" maxlength="20" class="form-control" value="{if isset($smarty.post.pay_id)}{$smarty.post.pay_id}{elseif isset($pay_id)}{$pay_id}{/if}" size="35" required/></td>
						</tr>
						<tr>
							<th>{__('شماره موبایل',"inax")}</th>
							<td><input type="tel" name="mobile" dir="auto" onkeyup="inax_check_numbers('mobile');" id="mobile" maxlength="11" class="form-control" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile}{/if}" size="35" required/> {__('(جهت پشتیبانی در صورت بروز مشکل)',"inax")}</td>
						</tr>
						<tr>
							<th></th>
							<td>
								<input type="hidden" dir="auto" id="mode" class="form-control" value="{if isset($test_mode) && $test_mode }test_mode{/if}" />
								<button class="btn btn-primary btn-sm inax-font" type="button" onclick="check_bill();return false;" ><i class="fa fa-check"></i> {__('بررسی اطلاعات',"inax")}</button>
							</td>
						</tr>
					</table>
				</div>
			{else}
				<!-- dont use ajax -->
				{if !isset($bill_details) }
					<div class="alert alert-info">- {__('توسط این بخش می توانید نسبت به پرداخت قبوض آب، برق، گاز، تلفن همراه، تلفن ثابت ثابت، عوارض شهرداری، سازمان مالیات و جریمه راهنمایی و رانندگی اقدام کنید..',"inax")}</div>
					{if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}

					<div class="table-responsive">
						<form action="{$permalink}" method="POST" >
							{$wordpress_csrf}
							<table class="table table-default table-hover table-bordered" >
								<tr>
									<th>{__('شناسه قبض',"inax")}</th>
									<td><input type="tel" name="bill_id" dir="auto" id="bill_id" maxlength="20" class="form-control" value="{if isset($smarty.post.bill_id)}{$smarty.post.bill_id}{elseif isset($bill_id)}{$bill_id}{/if}" size="35" required/></td>
								</tr>
								<tr>
									<th>{__('شناسه پرداخت',"inax")}</th>
									<td><input type="tel" name="pay_id" dir="auto" id="pay_id" maxlength="20" class="form-control" value="{if isset($smarty.post.pay_id)}{$smarty.post.pay_id}{elseif isset($pay_id)}{$pay_id}{/if}" size="35" required/></td>
								</tr>
								<tr>
									<th>{__('شماره موبایل',"inax")}</th>
									<td><input type="tel" name="mobile" dir="auto" onkeyup="inax_check_numbers('mobile');" id="mobile" maxlength="11" class="form-control" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile}{/if}" size="35" required/> {__('(جهت پشتیبانی در صورت بروز مشکل)',"inax")}</td>
								</tr>
								<tr>
									<th></th>
									<td class="text-right"><button class="btn btn-primary btn-sm inax-font" type="submit" name="check_bill" ><i class="fa fa-check"></i> {__('بررسی اطلاعات',"inax")}</button></td>
								</tr>
							</table>
						</form>
					</div>
				{else}
					{if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
					{if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

					{if !isset($error_msg) }
					{foreach from=$bill_details key=key item=link}
						<div class="table-responsive">
							<table class="table table-default table-hover table-bordered" >
								<tr>
									<th class="text-right" style="width:30%;" >{__('نوع قبض',"inax")}</th>
									<td>{if $language_code eq 'fa'}{$link.bill_type_name}{else}{$link.bill_type}{/if}</td>
								</tr>
								<tr>
									<th class="text-right" >{__('مبلغ',"inax")}</th>
									<td>{$link.amount|number_format} {__('ریال',"inax")}</td>
								</tr>
								{if $link.pay_type_fa neq ''}
								<tr>
									<th class="text-right" >{__('نحوه پرداخت',"inax")}</th>
									<td>{$link.pay_type_fa}</td>
								</tr>
								{/if}
								<tr>
									<th class="text-right" ></th>
									<td>
										<form action="{$permalink}" method="POST" >
											{$wordpress_csrf}
											<input type="hidden" class='bill_dbid' name="bill_dbid" value="{$link.db_id}">
											<button class="btn btn-success btn-sm inax-font" name="pay_submit" type="submit"><i class="fa fa-check"></i> {__('پرداخت',"inax")}</button>
											<a href="{$permalink}" class="btn btn-info btn-sm inax-font" ><i class="fa fa-arrow-left"></i> {__('بازگشت',"inax")}</a>
										</form>

									</td>
								</tr>
							</table>
						</div>
					{/foreach}
					{/if}

				{/if}
			{/if}

			{if !$ajaxless}
				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header text-right">
								<h4 class="modal-title inax-font" id="myModalLabel">{__('بررسی اطلاعات',"inax")}</h4>
								<button type="button" class="btn-close inax-font" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<div class="table-responsive">
									<div id="modal_loading" style="text-align:center;padding:20px 0px 20px 0px;">
										{__('در حال بررسی اطلاعات ...',"inax")}<br/>
										<img src="{$inax_img_url}/loader.gif" />
									</div>
									<table id="modal_result" class="table table-bordered " style="display:none;">
										<tr id="tr1" style="display:none">
											<th class="text-right" style="width:120px;" >{__('نوع قبض',"inax")}</th>
											<td><span class="display_bill_type" ></span>
											</td>
										</tr>
										<tr id="tr2" style="display:none">
											<th class="text-right" >{__('مبلغ',"inax")}</th>
											<td><span class="display_bill_amount" ></span></td>
										</tr>
										<tr id="tr5" style="display:none">
											<th class="text-right" >{__('نحوه پرداخت',"inax")}</th>
											<td><span class="display_pay_type" ></span></td>
										</tr>
										<tr id="error" style="display:none">
											<th class="text-right" > {__('خطا',"inax")}</th>
											<td><span class="display_error_msg" ></span></td>
										</tr>
										<tr id="tr3" style="display:none">
											<th class="text-right" ></th>
											<td>
												<form action="{$permalink}" method="POST" >
													{$wordpress_csrf}
													<input type="hidden" class='bill_dbid' name="bill_dbid" value="">
													<button class="btn btn-success btn-sm inax-font" name="pay_submit" type="submit"><i class="fa fa-check"></i> {__('پرداخت',"inax")}</button>
												</form>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn close inax-font" data-bs-dismiss="modal" aria-label="Close">{__('بستن',"inax")}</button>
							</div>
						</div>
					</div>
				</div>
			{/if}

			</div>
		{/if}

		</div>
	</div>
</div>

{include file="client/$inax_theme/footer.tpl"}
{include file="admin/header.tpl"}

<div class="card shadow mb-4 p-2 border-primary">
	<div class="card-header mb-2 p-2">
		<h6 class="m-0 font-weight-bold text-primary text-right inax-font"><i class="fa fa-database fa-fw"></i> اعتبار آینکس</h6>
	</div>
	<div class="card-body text-right p-0">

		{if isset($credit) }<div class="alert alert-info">اعتبار شما: {$credit|number_format} تومان است</div>{/if}
		{if isset($have_err) }<div class="alert alert-danger">{$have_err}</div>{/if}

		{if isset($credit) }
		{if isset($error_msg) }<div class="alert alert-danger">{$error_msg}</div>{/if}
		{if isset($success_msg) }<div class="alert alert-success">{$success_msg}</div>{/if}

		<form action="admin.php?page=inax_credit" method="POST" >
			{$wordpress_csrf}
			<table class="form-table" role="presentation">
				<tr>
					<th style="width:100px">افزایش اعتبار</th>
					<td>
						<input type="tel" name="amount" class="form-control tfont" placeholder="مبلغ افزایش اعتبار به تومان" dir="ltr" value="{if isset($smarty.post.amount)}{$smarty.post.amount}{/if}" required/>
						<!--<span id="letter_result"></span> تومان-->
					</td>
				</tr>

				<tr>
					<th colspan="2"><button type="submit" name="submit" class="btn btn-primary btn-sm inax-font" ><i class="fa fa-money fa-fw"></i> افزایش اعتبار</button></th>

				</tr>
			</table>
		</form >
		{/if}
	</div>
	</div>
</div>

{include file="admin/footer.tpl"}
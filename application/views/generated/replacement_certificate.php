<style type="text/css">
	body{
		font-family: "-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"";		
		font-size: 16px;
		margin: -10px 23px 23px 23px; 
	}

	#dswd_cert_logo{
		/*margin-top: 23px; 	*/
	}

	.text-center{
		text-align: center;
	}

	table{
		border-spacing: 0;
		border-collapse: collapse;
	}

	table th{
		font-weight: bold;
	}

	#replacement_table td,
	#replacement_table th{
		padding: 5px;
		text-align: center;		
	}

	#replacement_table th{
		background: #c6e0b4;		
	}

	header h3{
		font-size: 18px;
	}

	p{
		margin-bottom: 0px;
	}

	#dswd_cert_logo{
		display: inline-block;
		float: left;
	}

	#auto_logo{
		display: inline-block;
		float: right;
	}

	.a-d{
		padding: 0;
		text-align: center !important;
		list-style: none;
		margin: 0 auto;
	}

	.a-d li{		
		display: inline-block;
		margin: 0 35px;		
	}

	.a-d .square{
		margin-right: 5px; 
		padding: 5px;
		height: 10px;
		width: 10px;
		border: 1px solid;
	}
	
	.page_break { 
		page-break-before: always; 
	}

</style>

<?php date_default_timezone_set('Asia/Manila'); ?>
<img src="assets/img/logo_cert.png?>" width="30%;" id="dswd_cert_logo">
<img src="assets/img/autonomylogo.png?>" width="15%;" id="auto_logo">
<br>
<br>
<br>
<br>
<br>
<br>
<header>
	<h2 class="text-center" style="font-weight: bold; ">CERTIFICATION OF REPLACEMENT</h2>	
</header>
<br>
<div class="document-body">
	<p style="text-align: justify; line-height: 1.7">
		This is to certify that based on the downloaded list of eligible waitlist from DSWD – Central Office, the following barangay of Social Pension Beneficiaries to be replaced in <b><?= $municipality_name . ', ' . $province_name;?></b> has no qualified indigent senior citizens within the barangay. Thus, the undersigned considered the cleaned waitlist from another barangay within the municipality as replacements:
	</p>	
	<br>
	<table border="1" cellpadding="0" align="center" width="100%" id="replacement_table">
		<thead>
			<tr>
				<th rowspan="2" class="text-center">#</th>
				<th class="text-center">TO BE REPLACED</th>
				<th rowspan="2" class="text-center">BARANGAY</th>
				<th rowspan="2" class="text-center">NAME OF REPLACEMENT</th>
				<th rowspan="2" class="text-center">BARANGAY</th>
				<th rowspan="2" class="text-center">STARTING PERIOD</th>
			</tr>
			<tr>				
				<th class="text-center" style="border: 1px solid;">Name</th>				
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data as $key => $value): ?>
				<tr>
					<td><?= $key + 1; ?></td>
					<td><?= $value['replacee_name']; ?></td>
					<td><?= $value['replacee_address']; ?></td>
					<td><?= $value['replacer_name']; ?></td>
					<td><?= $value['replacer_address']; ?></td>
					<td><?= $value['period_start']; ?></td>
				</tr>			
			<?php endforeach; ?>
		<tbody>
	</table>
	<br>
	<p class="text-center">
		This certification is issued to support the claim of Social Pension stipend of the above cited replacement.
	</p>	

</div>
<br>
<div class="document-footer">
	<table  border="0" cellpadding="2" align="center" width="100%">
		<tr>
			<td>Prepared by:</td>
			<td width="25%">&nbsp;&nbsp;&nbsp;&nbsp;Reviewed by:</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>			
			<td width="25%"><b>MYRNA B. BERSALONA</b></td>
			<td width="25%" class="text-center"><b>CONCEPCION E. NAVALES</b></td>
		</tr>
		<tr>			
			<td width="25%">SWO III, SPP Head</td>
			<td width="25%" class="text-center">SWO IV, OIC DC PSD</td>
		</tr>
	</table>
	<br>
	<br>
	<br>	
	<table class="a-d" border="0" cellpadding="2" align="center" width="50%" style="margin: 0 auto;">
		<tr>
			<td class="text-center"><span class="square">&nbsp;</span> Approved</td>
			<td class="text-center"><span class="square">&nbsp;</span> Disapproved</td>
		</tr>		
	</table>	
	<br>		
	<p class="text-center">
		<b>LEO L. QUINTILLA</b><br>
		OIC-Regional Director
	</p> 		
	
</div>

<div class="page_break"></div>
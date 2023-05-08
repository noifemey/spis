<div class="row">
	<div class="col-md-3">
		<a href="<?=base_url("generateLBP/blankLBP");?>" class="btn btn-info" target="_blank">
			<i class="material-icons">print</i>&nbsp;&nbsp;&nbsp;Blank LBP Cash Card Enrollment Form
		</a>
	</div>
</div>

<div class="card">
	<div class="card-content">
		<div class="tab-content">

			<div class="row">
				<div class="col-md-12">
					<div class="card card-plain">
						<div class="card-header card-header-icon" data-background-color="rose">
							<i class="material-icons">assignment</i>
						</div>
						<h4 class="card-title">Search</h4>
						<div class="card-content">
							<div class="row">
								<hr>
							</div>
							<form method="GET" action="<?=base_url("generateLBP/form");?>" target="_blank">
								<div class="row">
									<div class="col-md-4 "> 
										<label for="">Province</label>
										<select class="selectpicker" data-live-search="true" data-style="btn btn-primary" title="Select Province" name ="province" id = "province-select" >
											<option value="0" disabled="">Select Province</option>
											<?php if (!empty($provincelist)): ?>
												<?php foreach ($provincelist as $pr): ?>
													<option value="<?=$pr->prov_code?>" data-pid = "<?=$pr->prov_code?>" ><?=$pr->prov_name?></option>
												<?php endforeach ?>
											<?php endif ?>
										</select>
									</div>
									<div class="col-md-4"> 
										<label for="">Municipality</label>
										<select class="selectpicker" data-live-search="true" data-style="btn btn-primary" title="Select Municipality" name ="municipality" id = "municipality-select" disabled="true">
										</select>
									</div>
									<div class="col-md-4"> 
										<label> </label>
										<button type="submit" value="1" class="btn btn-link btn-fill btn-wd" style="width: 100%" name="export">
											<i class="material-icons">file_download</i> Generate LBP Form
										</button>
									</div>	
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-md-4">
										</div>
										<div class="col-md-4">
										</div>        
										<div class="col-md-4">
										</div>
								</div>                        
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
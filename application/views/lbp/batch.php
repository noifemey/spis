<style>
    .checkbox{
        margin: 0px !important;
    }
    hr {
        border: none;
        height: 2px;
        background-color: #333;
    }
</style>
<div class="card">
	<div class="card-content">
		<!-- <div class="tab-content"> -->

			<!-- <div class="row"> -->
				<!-- <div class="col-md-12"> -->
					<!-- <div class="card card-plain"> -->
						<!-- <div class="card-header card-header-icon" data-background-color="rose">
							<i class="material-icons">assignment</i>
						</div> -->
						<h5 class="card-title">GENERATE LBP BATCH APPLICATION FORM</h5><hr/>
						<div class="card-content">
							<form method="POST" id="exportlbpbatchgene" action="<?=base_url('GenerateLBP/exportlbpbatch')?>" target="_blank">
								<div class="row">
									<div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php if(!empty($provincelist)){ 
                                                    $ctr = 0;
                                                        foreach($provincelist as $pr){ ?>
                                                            <div class="checkbox row">
                                                                <label><input type="checkbox" class="province_select" name="province_select" value="<?=$pr->prov_code?>"></label><?=$pr->prov_name?><br/>
                                                                <!-- <br/><?=$pr->prov_name?><br/> -->
                                                                <?php
                                                                    if(!empty($municipalitylist)){
                                                                        foreach($municipalitylist as $mu){
                                                                            if($mu->prov_code==$pr->prov_code){ $ctr++;  ?>
                                                                            <!-- <?php //if ($ctr == 5): ?> -->
                                                                                <div class="checkbox col-md-3">
                                                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                    <label><input type="checkbox" class="municipality_select<?=$mu->prov_code?>" name="municipality_select[]" value="<?=$mu->mun_code?>"></label><?=$mu->mun_name?><br/>                                                                                
                                                                                </div>
                                                                            <!-- <?php //endif; ?> -->
                                                                <?php
                                                                            }
                                                                        }
                                                                    }
                                                                ?>
                                                            </div>
                                                            <hr>                                                            
                                                <?php
                                                        }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4"> 
                                            <label> </label>
                                            <button type="submit" value="1" class="btn btn-link btn-fill btn-wd" style="width: 100%">
                                                <i class="material-icons">file_download</i> Generate Batch Application
                                            </button>
                                        </div>
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
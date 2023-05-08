<style>
.control-label {
	font-size: 12px;
	color: #424141;
}
.form-control {
    border-bottom: 1px solid black;
}
table, th, td {
  border: 1px solid black;
}

.wizard-card {
  min-height: 410px;
  box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14), 0 6px 30px 5px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
}
.wizard-card .picture-container {
  position: relative;
  cursor: pointer;
  text-align: center;
}
.wizard-card .wizard-navigation {
  position: relative;
}
.wizard-card .picture {
  width: 106px;
  height: 106px;
  background-color: #999999;
  border: 4px solid #CCCCCC;
  color: #FFFFFF;
  border-radius: 50%;
  margin: 5px auto;
  overflow: hidden;
  transition: all 0.2s;
  -webkit-transition: all 0.2s;
}
.wizard-card .picture:hover {
  border-color: #2ca8ff;
}


.wizard-card .picture-src {
  width: 100%;
}

</style>

<div id = "m_details">
    <input type="hidden" value="<?= $spid ?>" id="spid">  
    <div class="row justify-content-center">
        <div class="col-md-12 text-center">
            <br><h3>Pensioners Details</h3>
            <h4>{{[spid]}} - {{memDetails["lastname"]}} , {{memDetails["firstname"]}} {{memDetails["middlename"]}}   {{memDetails["extensionname"]}}</h4>
            <h4 v-if = "memDetails.sp_status == 'Active'">Status: {{memDetails["sp_status"]}}</h4>
            <h4 v-else>Status: {{memDetails["sp_status"]}} - {{getreasonname(memDetails.inactive_reason_id)}} ( {{memDetails["sp_inactive_remarks"]}} ) </h4>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-2">
            <a href="<?=base_url('member')?>" class="btn btn-primary  btn-block">
                <i class="fa fa-arrow-left"></i>  Masterlist
            </a>
        </div>

        <?php if(getUserRole() <= 2):?>
        <div class="col-md-2">
            <button class="btn btn-success  btn-block" @click = "editMember(memDetails.connum)"><i class="fa fa-pencil"></i> Edit Profile </button>
        </div>
        <?php endif;?>
        
        <div class="col-md-2">
            <button class="btn btn-success  btn-block" @click = "printLbp(memDetails.connum)"><i class="fa fa-print"></i> Print LBP </button>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success  btn-block" @click = "printBuf(memDetails.connum)"><i class="fa fa-print"></i> Print SPBUF </button>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-2"></div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-8">
            <div class="card wizard-card">
                <div class="card-header">
                <h3>BASIC INFORMATION</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="picture-container">
                                <div class="picture">
                                    <template v-if = "memDetails.photo">
                                        <img :src="memDetails['photo']" class="picture-src"  title="" /> 
                                    </template>
                                    <template v-else = "!memDetails.photo">
                                        <img :src="defaultPhoto" class="picture-src"  title="" /> 
                                    </template>							
                                </div>							
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class = "row">
                                <div class="col-md-3 form-group">
                                    <label class="control-label" for="lastname">Last Name: </label>
                                    <label class="form-control"><strong>{{memDetails["lastname"]}} </strong> </label>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label" for="firstname">First Name: </label>
                                    <label class="form-control"><strong>{{memDetails["firstname"]}} </strong> </label>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="control-label" for="middlename">Middle Name: </label>
                                    <label class="form-control"><strong>{{memDetails["middlename"]}} </strong> </label>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="control-label" for="extname">Ext: </label>	
                                    <label class="form-control"><strong>{{memDetails["extensionname"]}} </strong> </label>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-md-6 form-group">
                                    <label class="control-label" for="lastname">OSCA ID: </label>
                                    <label class="form-control"><strong>{{memDetails["osca_id"]}} </strong> </label>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label" for="lastname">UCT ID: </label>
                                    <label class="form-control"><strong>{{memDetails["uct_id"]}} </strong> </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class = "row">
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="lastname">Houshold ID: </label>
                            <label class="form-control"><strong>{{memDetails["hh_id"]}} </strong> </label>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="lastname">Houshold Size: </label>
                            <label class="form-control"><strong>{{memDetails["hh_size"]}} </strong> </label>
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-md-4 form-group">
                            <label class="control-label" for="dateofbirth">Date of Birth: </label>		
                            <label class="form-control"><strong>{{memDetails["birthdate"]}} </strong> </label>
                        </div>						
                        <div class="col-md-2 form-group">
                            <label class="control-label" for="birthplace">Age: </label>
                            <label class="form-control"><strong>{{getAge(memDetails.birthdate)}} </strong> </label>							
                        </div>				
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="birthplace">Place of Birth: </label>
                            <label class="form-control"><strong>{{memDetails["birthplace"]}} </strong> </label>							
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="gender">Gender: </label>	
                            <label class="form-control"><strong>{{memDetails["gender"]}} </strong> </label>
                        </div>						
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="maritalstatus">Marital Status: </label>	
                            <label class="form-control"><strong>{{getmaritalstat(memDetails.marital_status_id)}} </strong> </label>								
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="mothersMaidenName">Mother's Maiden Name: </label>	
                            <label class="form-control"><strong>{{memDetails["mothersMaidenName"]}} </strong> </label>
                        </div>						
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="localname">Local Name: </label>
                            <label class="form-control"><strong>{{memDetails["localname"]}} </strong> </label>							
                        </div>
                    </div>
                    <div class = "row">
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="firstname">Start of Pension: </label>
                            <label class="form-control"><strong>{{memDetails["year_start"]}} </strong> </label>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="lastname">Contact Number: </label>
                            <label class="form-control"><strong>{{memDetails["contact_no"]}} </strong> </label>
                        </div>
                    </div>
                    
                    <div class = "row">
                        <div class="col-md-12 form-group">	
                            <label class="control-label" for="mothersMaidenName">SP Status : </label>	
                            <label class="form-control"><strong>{{memDetails["sp_status"]}} </strong> </label>							
                        </div>
                    </div>
                    
                    <div class = "row" v-if="memDetails['sp_status'] != 'Active' && memDetails['sp_status'] != 'Additional'">
                        <div class="col-md-6 form-group" v-if="memDetails['sp_status'] == 'Inactive'">
                            <label class="control-label" for="mothersMaidenName">Date of Replacement : </label>	
                            <label class="form-control"><strong>{{memDetails["sp_status_inactive_date"]}} </strong> </label>
                        </div>						
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="mothersMaidenName">Reason: </label>	
                            <label class="form-control"><strong>{{getreasonname(memDetails.inactive_reason_id)}} - {{memDetails["sp_inactive_remarks"]}} </strong> </label>							
                        </div>
                    </div>

                    <div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="remarks">Remarks</label>
                            <label class="form-control"><strong>{{memDetails["remarks"]}} </strong> </label>	
                            <!-- <textarea name="remarks" id="" cols="30" rows="4" class="form-control" placeholder=""></textarea> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="card wizard-card">
                <div class="card-header">
                <h3>LOCATION</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="info-text text-center"> PRESENT ADDRESS </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">PROVINCE: </label>
                            <label class="form-control"><strong>{{getprovname(memDetails.province)}} </strong> </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">MUNICIPALITY: </label>
                            <label class="form-control"><strong>{{getmunname(memDetails.province, memDetails.city)}} </strong> </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">BARANGAY: </label>
                            <label class="form-control"><strong>{{getbarname(memDetails.city, memDetails.barangay)}} </strong> </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">ADDRESS: </label>
                            <label class="form-control"><strong>{{memDetails["address"]}} </strong> </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="info-text text-center"> PERMANENT ADDRESS </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">PERMANENT PROVINCE: </label>
                            <label class="form-control"><strong>{{getprovname(memDetails.permanent_province)}} </strong> </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">PERMANENT MUNICIPALITY: </label>
                            <label class="form-control"><strong>{{getmunname(memDetails.permanent_province, memDetails.permanent_city)}} </strong> </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">PERMANENT BARANGAY: </label>
                            <label class="form-control"><strong>{{getbarname(memDetails.permanent_barangay, memDetails.permanent_barangay)}} </strong> </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">PERMANENT ADDRESS: </label>
                            <label class="form-control"><strong>{{memDetails.permanent_address}} </strong> </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card wizard-card">
                <div class="card-header">
                <h3>REPRESENTATIVES</h3>
                </div>

                <div class="card-body">
                <div class = "row">
                    <div class="col-sm-4">
                        <h4 class="info-text text-center"> Representative 1 </h4>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeAddress1">Name</label>
                            <label class="form-control"><strong>{{memDetails.representativeName1}} </strong> </label>
                        </div>		
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeName1">Relationship</label>
                            <label class="form-control"><strong>{{getrelname(memDetails.representativeRelationship1)}} </strong> </label>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeRelationship1">Contact No.</label>
                            <label class="form-control"><strong>{{memDetails.representativeContact1}} </strong> </label>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeContact1">Address</label>
                            <label class="form-control"><strong>{{memDetails.representativeAddress1}} </strong> </label>
                        </div>						
                    </div>	
                    <div class="col-sm-4">
                        <h4 class="info-text text-center"> Representative 2 </h4>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeName2">Name</label>
                            <label class="form-control"><strong>{{memDetails.representativeName2}} </strong> </label>
                        </div>	
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeRelationship1">Relationship</label> 
                            <label class="form-control"><strong>{{getrelname(memDetails.representativeRelationship2)}} </strong> </label>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeContact2">Contact No.</label>
                            <label class="form-control"><strong>{{memDetails.representativeContact2}} </strong> </label>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeAddress2">Address</label>
                            <label class="form-control"><strong>{{memDetails.representativeAddress2}} </strong> </label>
                        </div>								
                    </div>						
                    <div class="col-sm-4">
                        <h4 class="info-text text-center"> Representative 3 </h4>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeName3">Name</label>
                            <label class="form-control"><strong>{{memDetails.representativeName3}} </strong> </label>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeRelationship3">Relationship</label>
                            <label class="form-control"><strong>{{getrelname(memDetails.representativeRelationship3)}} </strong> </label>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeContact3">Contact No.</label>
                            <label class="form-control"><strong>{{memDetails.representativeContact3}} </strong> </label>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeAddress3">Address</label>
                            <label class="form-control"><strong>{{memDetails.representativeAddress3}} </strong> </label>
                        </div>									
                    </div>
                </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>REPLACEMENT HISTORY</h3>
                    </div>
                    <div class="card-body">
                        <template v-if="replacerData.lastname">
                        <button v-if="userrole == 1" class="btn btn-danger btn-block" data-toggle="modal" data-target="#confirmUndoModal">Undo Replacement</button>
                        <a class="btn btn-warning btn-block" target="_blank" :href ="geturl(replacerData.connum)" style="text-align:left;"> 
                            <strong> Replaced by:          </strong><br> 
                            <strong> Name:                 </strong> {{replacerData.lastname}} , {{replacerData.firstname}}  {{replacerData.middlename}} {{replacerData.extensionname}} <br> 
						    <strong> SPID:                 </strong> {{replacerData.connum}} <br><br>
						    <strong> Date Replaced:        </strong> {{replacerData.registrationdate}} <br>
						    <strong> Period/Year Replaced: </strong> {{getperiodStart(replacerData.quarter_start)}} ,{{replacerData.year_start}}
                        </a>
                        </template>

                        <template v-if="replaceeData.lastname">
                        <a class="btn btn-info btn-block" target="_blank" :href ="geturl(replaceeData.connum)" style="text-align:left;"> 
                            <strong> Replaced:          </strong><br> 
                            <strong> Name:                 </strong> {{replaceeData.lastname}} , {{replaceeData.firstname}}  {{replaceeData.middlename}} {{replaceeData.extensionname}} <br> 
						    <strong> SPID:                 </strong> {{replaceeData.connum}} <br><br>
						    <strong> Date Replaced:        </strong> {{replaceeData.registrationdate}} <br>
						    <strong> Period/Year Replaced: </strong> {{getperiodStart(replaceeData.quarter_start)}} ,{{replaceeData.year_start}}
                        </a>
                        </template>
                    </div>
                </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>PAYMENT HISTORY</h3>
                    </div>
                    <div class="card-body" style="height:500px; overflow-y:auto; scroll">
                        <table class="table table-condensed">
                            <thead>
                                <th>YEAR</th>
                                <th>PERIOD</th>
                                <th>AMOUNT</th>
                                <th>PAYMENT STATUS</th>
                            </thead>
                            <tbody>
                                <template v-for="ml,i in memberPayments">
                                    <tr class="text-center">
                                        <td> {{ml.year}} </td>
                                        <td> {{getperiod(ml.period)}} {{ml.mode_of_payment}}</td>
                                        <td> {{ml.amount}} </td>
                                        <td> {{getpaymentstatus(ml.liquidation)}} </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>EDIT HISTORY</h3>
                    </div>
                    <div class="card-body" style="height:500px; overflow-y:auto; scroll">
                        <table class="table table-condensed">
                            <thead>
                                <th style="width:26%;">Date</th>
                                <th style="width:8%;">User</th>
                                <th>Log Details</th>
                            </thead>
                            <tbody>
                                <template v-for="ml,i in membereditlogs">
                                    <tr class="text-center">
                                        <td style="width:26%;">
                                            {{ml.date}} <br> {{ml.lapse}}
                                        </td>
                                        <td style="width:8%;">
                                            {{ml.user}}
                                        </td>
                                        <td>
                                            {{ml.action}} <br> {{ml.log_details}} {{ml.edits}}
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmUndoModal" tabindex="-1" role="dialog" aria-labelledby="confirmUndoModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="replace_form" class="form-horizontal form-data" enctype="multipart/form-data" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Undo Replacement</h5>
                    </div>
                    <div class="modal-body">
                        <h5>Are you sure you want to undo this replacement?</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="2" v-if="replacerData.lastname">Replacer</th>
                                    <th colspan="2" v-if="memDetails.lastname">Replacee</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <template v-if="replacerData.lastname">
                                        <td>{{replacerData.connum}}</td>
                                        <td>{{replacerData.lastname}} , {{replacerData.firstname}} {{replacerData.middlename}} {{replacerData.extensionname}}</td>
                                    </template>

                                    <template v-if="memDetails.lastname">
                                        <td>{{memDetails.connum}}</td>
                                        <td>{{memDetails.lastname}} , {{memDetails.firstname}} {{memDetails.middlename}} {{memDetails.extensionname}}</td>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                        <h6><i>*Replacer will be returned to eligible waitlist</i></h6>
                        <h6><i>*Replacee will be returned to ForReplacement status</i></h6>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect" @click="undoReplace()">Yes, Undo Replacement</button>
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
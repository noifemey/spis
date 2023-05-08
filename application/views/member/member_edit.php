<style>
    .control-label {
        font-size: 12px;
        color: #424141;
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

<div id = "m_edit">
    <input type="hidden" value="<?= $spid ?>" id="spid">  
    <div class="row justify-content-center">
        <div class="col-md-12 text-center">
            <br><h3>Pensioners Details</h3>
            <h4>{{[spid]}} - {{memDetails.lastname}} , {{memDetails.firstname}} {{memDetails.middlename}}   {{memDetails.extensionname}}</h4>
            <h4>Status: {{memDetails.sp_status}} - {{getreasonname(memDetails.inactive_reason_id)}} ( {{memDetails.sp_inactive_remarks}} ) </h4>
        </div>
    </div>
    <br>

    <div class="row">
    
        <div class="col-md-2">
            <a href="<?=base_url('member')?>" class="btn btn-primary  btn-block">
                <i class="fa fa-arrow-left"></i>  Masterlist
            </a>
        </div>

        <div class="col-md-2">
        <button class="btn btn-warning" id="btnMemPaymentEditInvidivual" data-toggle="modal" data-target="#memPaymentModalEdit" @click="selectData(memDetails),getMemPayment(spid,memDetails.sp_status)"><i class="fa fa-pencil "></i>Edit Payment</button>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success btn-block" @click = viewMember(memDetails.connum)><i class="fa fa-eye"></i> View Profile </button>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success btn-block"><i class="fa fa-print"></i> Print LBP </button>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success btn-block"><i class="fa fa-print"></i> Print SPBUF </button>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-2"></div>
    </div>
    <br>

<form v-on:submit.prevent="updateMember">
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
                                    <input class="form-control" v-model = "memDetails.lastname" required></input>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label" for="firstname">First Name: </label>
                                    <input class="form-control" v-model = "memDetails.firstname" required></input>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="control-label" for="middlename">Middle Name: </label>
                                    <input class="form-control" v-model = "memDetails.middlename"></input>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="control-label" for="extname">Ext: </label>	
                                    <input class="form-control" v-model = "memDetails.extensionname"></input>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-md-6 form-group">
                                    <label class="control-label" for="lastname">OSCA ID: </label>
                                    <input class="form-control" v-model = "memDetails.osca_id"></input>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="control-label" for="lastname">UCT ID: </label>
                                    <input class="form-control" v-model = "memDetails.uct_id"></input>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="lastname">Houshold ID: </label>
                            <input class="form-control" v-model = "memDetails.hh_id"></input>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="lastname">Houshold Size: </label>
                            <input class="form-control" v-model = "memDetails.hh_size"></input>
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-md-4 form-group">
                            <label class="control-label" for="dateofbirth">Date of Birth: </label>		
                            <input class="form-control" type = "date" v-model = "memDetails.birthdate" required></input>
                        </div>						
                        <div class="col-md-2 form-group">
                            <label class="control-label" for="birthplace">Age: </label>
                            <label class="form-control"><strong>{{getAge(memDetails.birthdate)}} </strong> </label>							
                        </div>				
                        <div class="col-md-5 form-group">
                            <label class="control-label" for="birthplace">Place of Birth: </label>
                            <input class="form-control" v-model = "memDetails.birthplace" required> </input>							
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="gender">Gender: </label>	
                            <select class="form-control" v-model = "memDetails.gender" required><strong>{{memDetails.gender}} </strong> 
                                <option value = "MALE"> MALE </option>
                                <option value = "FEMALE"> FEMALE </option>
                            </select>
                        </div>						
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="maritalstatus">Marital Status: </label>	
                            <select class="form-control" v-model = "memDetails.marital_status_id" required> 
                                <template v-for = "(list,index) in lib.maritalstatlibrary">
                                    <option :value="list.id">{{list.name}}</option>
                                </template>	
                            </select>
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="mothersMaidenName">Mother's Maiden Name: </label>	
                            <input class="form-control" v-model = "memDetails.mothersMaidenName"></input>
                        </div>						
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="localname">Local Name: </label>
                            <input class="form-control" v-model = "memDetails.localname"></input>							
                        </div>
                    </div>
                    <div class = "row">
                        <div class="col-md-3 form-group">
                            <label class="control-label" for="firstname">Start Period of Pension: </label>
                            <select class="form-control" v-model = "memDetails.quarter_start" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="control-label" for="firstname">Start Year of Pension: </label>
                            <input class="form-control" type = "number" v-model = "memDetails.year_start" required></input>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="control-label" for="lastname">Contact Number: </label>
                            <input class="form-control" v-model = "memDetails.contact_no"></input>
                        </div>
                    </div>
                    
                    <div class = "row">
                        <div class="col-md-6 form-group">	
                            <label class="control-label">SP Status : </label>	
                            <label class="form-control"><strong>{{memDetails.sp_status}} </strong> </label>							
                        </div>

                        <div class="col-md-6 form-group" v-if="memDetails['sp_status'] != 'Active' && memDetails['sp_status'] != 'Additional'">
                            <label class="control-label">Reason of Replacement : </label>	
                            <select class="form-control" v-model = "memDetails.inactive_reason_id"> 
                                <template v-for = "(list,index) in lib.inactivereason">
                                    <option :value="list.id">{{list.name}}</option>
                                </template>	
                            </select>
                        </div>	
                    </div>
                    
                    <div class = "row" v-if="memDetails['sp_status'] != 'Active' && memDetails['sp_status'] != 'Additional'">
                        <div class="col-md-6 form-group" v-if="memDetails['sp_status'] == 'Inactive'">
                            <label class="control-label">Date of Replacement : </label>	
                            <input type = "date" class="form-control" v-model = "memDetails.sp_status_inactive_date"></label>
                        </div>						
                        <div class="col-md-6 form-group">
                            <label class="control-label">Reason Remarks: </label>	
                            <textarea class="form-control"  v-model = "memDetails.sp_inactive_remarks"></textarea>							
                        </div>
                    </div>

                    <div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="remarks">Remarks</label>
                            <textarea name="remarks" id="" cols="30" rows="4" class="form-control" placeholder=""  v-model = "memDetails.remarks"></textarea>
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
                            <!-- <label class="form-control"><strong>{{getprovname(memDetails.province)}} </strong> </label> -->
                            <select class="form-control"   @change = "getLocation('mun_code',memDetails.province,'present')" v-model="memDetails.province"  name="province_present" required>
                                <template v-for = "(list,index) in lib.location.provinces">
                                    <option :value="list.prov_code">{{list.prov_name}}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">MUNICIPALITY: </label>
                            <!-- <label class="form-control"><strong>{{getmunname(memDetails.province, memDetails.city)}} </strong> </label> -->
                            <select class="form-control"   @change = "getLocation('bar_code',memDetails.city,'present')" v-model = "memDetails.city" name="municipality_present"  required>
                                <template v-for = "(list,index) in lib.location.present_municipalities">
                                    <option :value="list.mun_code">{{list.mun_name}}</option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">BARANGAY: </label>
                            <!-- <label class="form-control"><strong>{{getbarname(memDetails.city, memDetails.barangay)}} </strong> </label> -->
                            
                            <select class="form-control"   v-model = "memDetails.barangay" name="barangay_present" required>
                                <template v-for = "(list,index) in lib.location.present_barangays">
                                    <option :value="list.bar_code">{{list.bar_name}}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">ADDRESS: </label>
                            <input class="form-control" v-model = "memDetails.address"></input>
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
                            <!-- <label class="form-control"><strong>{{getprovname(memDetails.permanent_province)}} </strong> </label> -->
                            <select class="form-control"   @change = "getLocation('mun_code',memDetails.permanent_province,'permanent')" v-model="memDetails.permanent_province"  name="province_permanent" required>
                                <template v-for = "(list,index) in lib.location.provinces">
                                    <option :value="list.prov_code">{{list.prov_name}}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">PERMANENT MUNICIPALITY: </label>
                            <!-- <label class="form-control"><strong>{{getmunname(memDetails.permanent_province, memDetails.permanent_city)}} </strong> </label> -->
                            
                            <select class="form-control"   @change = "getLocation('bar_code',memDetails.permanent_city,'permanent')" v-model = "memDetails.permanent_city" name="municipality_permanent"  required>
                                <template v-for = "(list,index) in lib.location.permanent_municipalities">
                                    <option :value="list.mun_code">{{list.mun_name}}</option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">PERMANENT BARANGAY: </label>
                            <!-- <label class="form-control"><strong>{{getbarname(memDetails.permanent_barangay, memDetails.permanent_barangay)}} </strong> </label> -->
                            
                            <select class="form-control"   v-model = "memDetails.permanent_barangay" name="barangay_permanent" required>
                                <template v-for = "(list,index) in lib.location.permanent_barangays">
                                    <option :value="list.bar_code">{{list.bar_name}}</option>
                                </template>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label" for="lastname">PERMANENT ADDRESS: </label>
                            <input class="form-control" v-model = "memDetails.permanent_address"></input>
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
                            <input class="form-control" v-model = "memDetails.representativeName1"></input>
                        </div>		
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeName1">Relationship</label>
                            <!-- <input class="form-control" v-model = "memDetails.representativeRelationship1"></input> -->
                            
                            <select class="form-control" v-model = "memDetails.representativeRelationship1"> 
                                <template v-for = "(list,index) in lib.relationshiplibrary">
                                    <option :value="list.relid">{{list.relname}}</option>
                                </template>	
                            </select>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeRelationship1">Contact No.</label>
                            <input class="form-control" v-model = "memDetails.representativeContact1"></input>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeContact1">Address</label>
                            <input class="form-control" v-model = "memDetails.representativeAddress1"></input>
                        </div>						
                    </div>	
                    <div class="col-sm-4">
                        <h4 class="info-text text-center"> Representative 2 </h4>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeName2">Name</label>
                            <input class="form-control" v-model = "memDetails.representativeName2"></input>
                        </div>	
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeRelationship2">Relationship</label> 
                            <!-- <input class="form-control" v-model = "memDetails.representativeRelationship2"></input> -->
                            
                            <select class="form-control" v-model = "memDetails.representativeRelationship2"> 
                                <template v-for = "(list,index) in lib.relationshiplibrary">
                                    <option :value="list.relid">{{list.relname}}</option>
                                </template>	
                            </select>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeContact2">Contact No.</label>
                            <input class="form-control" v-model = "memDetails.representativeContact2"></input>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeAddress2">Address</label>
                            <input class="form-control" v-model = "memDetails.representativeAddress2"></input>
                        </div>								
                    </div>						
                    <div class="col-sm-4">
                        <h4 class="info-text text-center"> Representative 3 </h4>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeName3">Name</label>
                            <input class="form-control" v-model = "memDetails.representativeName3"></input>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeRelationship3">Relationship</label>
                            <!-- <input class="form-control" v-model = "memDetails.representativeRelationship3"></input> -->
                            
                            <select class="form-control" v-model = "memDetails.representativeRelationship3"> 
                                <template v-for = "(list,index) in lib.relationshiplibrary">
                                    <option :value="list.relid">{{list.relname}}</option>
                                </template>	
                            </select>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeContact3">Contact No.</label>
                            <input class="form-control" v-model = "memDetails.representativeContact3"></input>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label" for="representativeAddress3">Address</label>
                            <input class="form-control" v-model = "memDetails.representativeAddress3"></input>
                        </div>									
                    </div>
                </div>
                </div>
            </div>

            <div class = "row">
                <button type="submit" class="btn btn-success btn-block"><i class="fa fa-pencil"></i> Finish </button>
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
                        <a class="btn btn-warning btn-block" target="_blank" href="#" style="text-align:left;"> 
                            <strong> Replaced by:          </strong><br> 
                            <strong> Name:                 </strong> {{replacerData.lastname}} , {{replacerData.firstname}}  {{replacerData.middlename}} {{replacerData.extensionname}} <br> 
						    <strong> SPID:                 </strong> {{replacerData.connum}} <br><br>
						    <strong> Date Replaced:        </strong> {{replacerData.registrationdate}} <br>
						    <strong> Period/Year Replaced: </strong> {{getperiodStart(replacerData.quarter_start)}} ,{{replacerData.year_start}}
                        </a>
                        </template>

                        <template v-if="replaceeData.lastname">
                        <a class="btn btn-info btn-block" target="_blank" href="#" style="text-align:left;"> 
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

</form>




            <!-- Member Payments -->
            <div id="memPaymentModalEdit" class="modal-form modal fade in" tabindex="-1" role="dialog" aria-labelledby="memPaymentModalEdit" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Payments History </h4>
                            <!-- of <b>{{getFullname(activeSP.lastname,activeSP.firstname,activeSP.middlename,activeSP.extensionname)}} ({{spid}})</b></h4> -->
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary waves-effect" @click="newSelect()">Add New Payment</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <form v-on:submit.prevent="submitPayment">
                                        <div class="form-group row">
                                            <label for="year" class="col-sm-4 col-form-label">Year: <span class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <select :disabled="!memPayments.new" class="form-control p-0" v-model="memPayments.activeMP.year" required>
                                                    <option value="">Select Year</option>
                                                    <option>2025</option>
                                                    <option>2024</option>
                                                    <option>2023</option>
                                                    <option>2022</option>
                                                    <option>2021</option>
                                                    <option>2020</option>
                                                    <option>2019</option>
                                                    <option>2018</option>
                                                    <option>2017</option>
                                                    <option>2016</option>
                                                    <option>2015</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="period" class="col-sm-4 col-form-label">Period: <span class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <select :disabled="!memPayments.new" class="form-control p-0" v-model="memPayments.activeMP.period" id="period" name="period" required>
                                                    <option value="">Select Period</option>
                                                    <option value="5">1st Semester</option>
                                                    <option value="6">2nd Semester</option>
                                                    <option value="1">1st Quarter</option>
                                                    <option value="2">2nd Quarter</option>
                                                    <option value="3">3rd Quarter</option>
                                                    <option value="4">4th Quarter</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="date_received" class="col-sm-4 col-form-label">Date of Payout: <span class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control" v-model="memPayments.activeMP.date_receive" id="date_received" name="date_received" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="amount" class="col-sm-4 col-form-label">Amount: <span class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control" v-model="memPayments.activeMP.amount" id="amount" name="amount" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="liquidation" class="col-sm-4 col-form-label">Payment Status: <span class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <select class="form-control p-0" v-model="memPayments.activeMP.liquidation" id="liquidation" name="liquidation" required>
                                                <option value="0">UNPAID</option>
                                                    <option value="1">PAID</option>
                                                    <option value="3">Offset</option>
                                                    <option value="4">Onhold</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="receiver" class="col-sm-4 col-form-label">Receiver: <span class="text-danger">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" v-model="memPayments.activeMP.receiver" id="receiver" name="receiver" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="remarks" class="col-sm-4 col-form-label">Remarks: </label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" rows="3" v-model="memPayments.activeMP.remarks" id="remarks" name="remarks"></textarea>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-center ">
                                                <template v-if="memPayments.new">
                                                    <button type="submit" :disabled="memPayments.activeMP.year >= '2020'" class="btn btn-primary waves-effect  btn-block">Save</button>
                                                </template>
                                                <template v-else>
                                                    <button type="submit" class="btn btn-primary waves-effect  btn-block">Update</button>
                                                </template>
                                                <!-- <button type="button"  class="btn btn-danger float-right">Delete Item</button> -->
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-8">
                                    <table class="table table-responsive-sm table-hover table-outline mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Year</th>
                                                <th>Period</th>
                                                <th>Date of Payout</th>
                                                <th>Receiver</th>
                                                <th>Amount</th>
                                                <th>Payment Status</th>
                                                <th>Remarks</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="list,index in memPayments.data">
                                                <tr>
                                                    <td>
                                                        {{list.year}}
                                                    </td>
                                                    <td>
                                                        {{list.mode_of_payment}} {{list.period}}
                                                    </td>
                                                    <td>
                                                        {{list.date_receive}}
                                                    </td>
                                                    <td>
                                                        {{list.receiver}}
                                                    </td>
                                                    <td>
                                                        {{list.amount}}
                                                    </td>
                                                    <td>
                                                        {{getPaymentStatus(list.liquidation)}}
                                                    </td>
                                                    <td>
                                                        {{list.remarks}}
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-pill btn-warning btn-secondary" type="button" @click="editSelect(list)">
                                                                <i class="fa fa-pencil text-center"></i>
                                                            </button>
                                                            <button v-if="list.year < 2020" class="btn btn-sm btn-pill btn-danger btn-secondary" type="button" data-toggle="modal" @click="deleteMemPayment(list)">
                                                                <i class="fa fa-trash text-center"></i>
                                                            </button>
                                                            <button v-if="memDetails.sp_status == 'Inactive'" class="btn btn-sm btn-pill btn-info btn-secondary" type="button" data-toggle="modal" @click="getMemberTransferInfo(list.p_id)">
                                                                Transfer
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
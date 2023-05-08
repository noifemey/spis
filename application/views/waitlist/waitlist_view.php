<style>
    th,
    td {
        border: 1px solid black;
        /* font-size: 12px; */
        padding: 8px;
    }

    hr {
        border: none;
        height: 2px;
        background-color: #333;
    }

    .vl {
        border-right: 1px solid #333;
        height: 300px;
    }

    /* .form-control{
        background-color: #ffffff !important;
        border: 1px solid black !important;
    } */
    .col-sm-12,
    .col-sm-6,
    .col-sm-4,
    .col-sm-3,
    .form-group,
    .label-floating {
        margin: 0% !important;
        padding: 0.5% !important;
    }
</style>

<div id="w_index">
    <div class="row">
        <div class="col-md-12 ">
            <br>
            <h3>Waitlist</h3><br>
            
            <div class="row">
                <div class="col-md-3">
                    <a href="" class="btn btn-success btn-block" data-toggle="modal" data-target="#WaitlistTemplateModal">
                        <i class="fa fa-download"></i> Download Blank Waitlist Template
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="" class="btn btn-success btn-block" @click="dlBlankBuf()">
                        <i class="fa fa-download"></i> Download Blank BUF
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="" class="btn btn-success btn-block" data-toggle="modal" data-target="#exportWaitlistModal">
                        <i class="fa fa-download"></i> Export Waitlist BUF
                    </a>
                </div>
            </div>

            <br>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 ">
                            <form v-on:submit.prevent="searchWaitlist">
                                <div class="row">
                                    <div class="col-md-3 ">
                                        <label for="Province">Province</label>
                                        <select class="form-control p-0" @change="getLocation('mun_code',global_search.search.prov_code)" v-model="global_search.search.prov_code" name="Province">
                                            <template v-for="(list,index) in location.provinces">
                                                <option :value="list.prov_code">{{list.prov_name}}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-md-3 ">
                                        <label for="Municipality">Municipality</label>
                                        <select class="form-control p-0" @change="getLocation('bar_code',global_search.search.mun_code)" v-model="global_search.search.mun_code" :disabled="location.municipalities.length <=0" name="Municipality">
                                            <template v-for="(list,index) in location.municipalities">
                                                <option :value="list.mun_code">{{list.mun_name}}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-md-3 ">
                                        <label for="Barangay">Barangay</label>
                                        <select class="form-control p-0" v-model="global_search.search.bar_code" :disabled="location.barangays.length <=0" name="Barangay">
                                            <template v-for="(list,index) in location.barangays">
                                                <option :value="list.bar_code">{{list.bar_name}}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-md-1 ">
                                        <label for="Gender">Gender</label>
                                        <select class="form-control p-0" v-model="global_search.search.gender" name="Gender" id="Gender">
                                            <option value="0"></option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 ">
                                        <label for="Status">Waitlist Status</label>
                                        <select class="form-control p-0" v-model="global_search.search.status" name="Status" id="Status">
                                            <option value=""></option>
                                            <option value="1">Eligible Waitlist</option>
                                            <option value="2">Not Eligible Waitlist</option>
                                            <option value="0">No Eligibility Status Yet</option>
                                            <option value="3">Waiting For Eligibility (Sent to C.O)</option>
                                            <option value="4">For Sending to Central Office</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        From Birthdate: <input type="date" class="form-control" v-model='global_search.search.birth_from'>
                                    </div>

                                    <div class="col-md-3">
                                        To Birthdate: <input type="date" class="form-control" v-model='global_search.search.birth_to'>
                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-md-4 ">
                                        <button type="submit" class="btn btn-info btn-block">
                                            <i class="fa fa-search"></i> SEARCH</button>
                                    </div>
                                    <div class="col-md-4 ">
                                        <button type="button" class="btn btn-warning btn-block" @click="ClearSearch()">
                                            <i class="fa fa-times"></i> CLEAR FILTER</button>
                                    </div>
                                    <div class="col-md-4 ">
                                        <button type="button" class="btn btn-success btn-block" @click="downloadList()">
                                            <i class="fa fa-download"></i> Download List</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="" class="btn btn-success btn-block" data-toggle="modal" data-target="#newWaitlistModal" @click="addWaitlist()">
                                <i class="fa fa-plus"></i> Add New Waitlist
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="" class="btn btn-success btn-block" data-toggle="modal" data-target="#importwaitinglistModal">
                                <i class="fa fa-upload"></i> Import NEW Waitlist
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="" class="btn btn-success btn-block" data-toggle="modal" data-target="#importEligibilityModal">
                                <i class="fa fa-upload"></i> Import WAITLIST UPDATE
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="row row-search">
                        <div class="col-md-12 ">
                            <div class="row">
                                <div v-if="userrole == 1" class="col-md-3">
                                    <button class="btn btn-warning btn-block " @click="UpdateWaitlistStatus('bulk','priority',1,'Bulk Add to Eligible')"><i class="fa fa-plus"></i> Bulk Add to Eligible </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-warning btn-block " @click="UpdateWaitlistStatus('bulk','priority',2,'Bulk Add to NOT Eligible')"><i class="fa fa-minus"></i> Bulk Add to NOT Eligible </button>
                                </div>
                                <div v-if="userrole == 1" class="col-md-3">
                                    <button class="btn btn-warning btn-block " @click="AddAsNewBene('bulk')"><i class="fa fa-plus"></i> Bulk Add As New Beneficiary </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <v-server-table :columns="new_waitlist.column" :options="new_waitlist.options" ref='New_waitlisttable'>
                                        <template slot="id" slot-scope="e">
                                            <input type="checkbox" @change="unmarkAll()" class="form-control" :value="e.row.w_id" v-model="markedRows">
                                        </template>
                                        <template slot="Reference_Code" slot-scope="e">
                                            {{e.row.reference_code}}
                                        </template>
                                        <template slot="Full_Name" slot-scope="e">
                                            {{getFullname(e.row.lastname,e.row.firstname,e.row.middlename,e.row.extname)}}
                                        </template>
                                        <template slot="Province" slot-scope="e">
                                            {{getprovname(e.row.prov_code)}}
                                        </template>
                                        <template slot="Municipality" slot-scope="e">
                                            {{getmunname(e.row.prov_code,e.row.mun_code)}}
                                        </template>
                                        <template slot="Barangay" slot-scope="e">
                                            {{getbarname(e.row.mun_code,e.row.bar_code)}}
                                        </template>
                                        <template slot="Birth_Date" slot-scope="e">
                                            {{e.row.birthdate}}
                                        </template>
                                        <template slot="Age" slot-scope="e">
                                            {{e.row.Age}}
                                        </template>
                                        <template slot="Gender" slot-scope="e">
                                            {{e.row.gender}}
                                        </template>
                                        <template slot="OSCA_ID" slot-scope="e">
                                            {{e.row.osca_id}}
                                        </template>
                                        <template slot="Status" slot-scope="e">
                                            {{getstatus(e.row.priority, e.row.sent_to_co, e.row.remarks,e.row.duplicate)}}
                                        </template>
                                        <template slot="actions" slot-scope="e">
                                            <button class="btn btn-warning btn-sm" @click="selectData(e.row),editWaitlist(e.row)" data-toggle="modal" data-target="#newWaitlistModal" title="Edit from Waiting List"><i class="fa fa-edit"></i></button>
                                            <button class="btn btn-danger btn-sm" @click="selectData(e.row)" data-toggle="modal" data-target="#archiveWaitlist" title="Remove from Waiting List"><i class="fa fa-trash" ></i></button>
                                        </template>
                                    </v-server-table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade " id="exportWaitlistModal" tabindex="0" role="dialog" aria-labelledby="exportWaitlistModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Export Waitlist</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">X</span>
                            </button>
                        </div>
                        <form v-on:submit.prevent="exportWaitlist">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="">PROVINCE</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select class="form-control" data-live-search="true" @change="getLocation('mun_code',exportdata.prov_code,'export')" v-model="exportdata.prov_code">
                                                    <option value="all">All</option>
                                                    <template v-for="(list,index) in lib.location.provinces">
                                                        <option :value="list.prov_code">{{list.prov_name}}</option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="">MUNICIPALITY</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select class="form-control" v-model="exportdata.mun_code">
                                                    <option value="all">All</option>
                                                    <template v-for="(list,index) in exportdata.municipalities">
                                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="">WAITLIST STATUS</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select class="form-control" v-model="exportdata.status" name="status">
                                                    <option value="all">All</option>
                                                    <option value='1'>Eligible (Priority List)</option>
                                                    <option value='2'>Not Eligible (For Revalidation)</option>
                                                    <option value='0'>No Eligibility Status Yet</option>
                                                    <option value='3'>Waiting For Eligibility (Sent to C.O)</option>
                                                    <option value='4'>For Sending to Central Office</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Export Waitlist BUF</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            
            <div class="modal fade " id="WaitlistTemplateModal" tabindex="0" role="dialog" aria-labelledby="WaitlistTemplateModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Download Waitlist Blank Template</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">X</span>
                            </button>
                        </div>
                        <form v-on:submit.prevent="dlWaitlistTemplate">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="">PROVINCE</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select class="form-control" data-live-search="true" @change="getLocation('mun_code',waitlisttemp.prov_code,'template')" v-model="waitlisttemp.prov_code">
                                                    <option value="">All</option>
                                                    <template v-for="(list,index) in lib.location.provinces">
                                                        <option :value="list.prov_code">{{list.prov_name}}</option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="">MUNICIPALITY</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select class="form-control" v-model="waitlisttemp.mun_code">
                                                    <option value="">All</option>
                                                    <template v-for="(list,index) in waitlisttemp.municipalities">
                                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Download</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>


            <!-- ADD NEW WAITLIST -->
            <div class="modal fade modal-form" id="newWaitlistModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" style="width:90%; max-width:initial;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 v-if="isEditing === false" class="modal-title" id="myLargeModalLabel">Add New Waitlist</h4>
                            <h4 v-else class="modal-title" id="myLargeModalLabel">Update Waitlist</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="col-md-12" v-if="!probableDuplicate.clean">
                            <h2 v-if="probableDuplicate.probableActiveData != '' || probableDuplicate.probableWaitlistData != ''" style="font-weight: bold; color:red; text-align: center;" id="list-of-prob-duplicates">Lists of Probable Duplicates</h2>

                            <template v-if="probableDuplicate.probableActiveData != ''">
                                <table class="table table-striped" style="margin-bottom: 15px;">
                                    <thead>
                                        <th class="text-center">#</th>
                                        <th class="text-center">SPID # / Reference Code</th>
                                        <th class="text-center">Full Name</th>
                                        <th class="text-center">Address</th>
                                        <th class="text-center">Status</th>
                                    </thead>
                                    <tbody>
                                        <template v-for="(list,index) in probableDuplicate.probableActiveData">
                                            <tr>
                                                <td class="text-center">{{index+1}}</td>
                                                <td class="text-center">{{list.connum}}</td>
                                                <td class="text-center">{{list.lastname}}, {{list.firstname}} {{list.middlename}}</td>
                                                <td class="text-center">{{getprovname(list.province)}} , {{getmunname(list.province,list.city)}}, {{getbarname(list.city,list.barangay)}}</td>
                                                <template v-if="list.sp_status >= 0 ">
                                                    <td class="text-center" v-if="list.sp_status == 0 ">No eligibility status yet</td>
                                                    <td class="text-center" v-if="list.sp_status == 1 ">Eligible</td>
                                                    <td class="text-center" v-if="list.sp_status == 2 ">Not eligible</td>
                                                </template>
                                                <template v-else>
                                                    <td class="text-center">{{list.sp_status}}</td>
                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </template>
                        </div>
                        <form v-on:submit.prevent="addWaitlistData">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group row">
                                            <div class="col-sm-12 col-lg-6 col-md-12">
                                                <label for="oscaid">OSCA ID: </label>
                                                <input name="oscaid" id="oscaid" type="text" class="form-control" v-model="wdata.oscaid" />
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="input_grantee">Grantee / Not Grantee</label>
                                                <div class="custom-checkbox">
                                                    <div class="custom-checkbox-50">
                                                        <div class="custom-checkbox-primary">
                                                            <input type="radio" value="1" v-model="wdata.input_grantee" id="grantee_yes" name="input_grantee">
                                                            <label for="grantee_yes">Grantee</label>
                                                        </div>
                                                        <div class="custom-checkbox-primary">
                                                            <input type="radio" value="0" v-model="wdata.input_grantee" id="grantee_no" name="input_grantee">
                                                            <label for="grantee_no">Not Grantee</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="col-sm-12 col-lg-3 col-md-12">
                                        <input type="radio" v-model="wdata.input_grantee" value = "1" name="input_grantee" id="grantee_yes"/> Grantee  &nbsp;&nbsp;&nbsp;
                                        <input type="radio" v-model="wdata.input_grantee"  value = "0" name="input_grantee" id="grantee_no"/> Not Grantee
                                    </div> -->
                                            <div class="col-sm-12 col-lg-3 col-md-12" style="padding-bottom: 0 !important;">
                                                <label for="respondentName">Name of Respondent: </label>
                                                <input v-model="wdata.respondentName" class="form-control" name="respondentName" id="respondentName" type="text" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="lastname">Last Name <span style="color:red">*</span></label>
                                                <input type="text" class="form-control" v-model="wdata.lastname" name="lastname" id="lastname" @change="checkDup()" />
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="firstname">First Name <span style="color:red">*</span></label>
                                                <input type="text" class="form-control" v-model="wdata.firstname" name="firstname" id="firstname" @change="checkDup()" />
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="middlename">Middle Name</label>
                                                <input type="text" class="form-control" v-model="wdata.middlename" name="middlename" id="middlename" @change="checkDup()" />
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="extname">Ext Name</label>
                                                <input type="text" class="form-control" v-model="wdata.extname" name="extname" id="extname" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12 col-lg-2 col-md-12" style="padding-top: 9px; padding-left: 8px;">
                                                <label for="dateofbirth">Date of Birth: <span style="color:red">*</span></label>
                                                <input type="date" v-model="wdata.dateofbirth" name="dateofbirth" class="form-control datepicker" @change="computeAge()">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-2 col-md-12" style="padding-top: 9px;">
                                                <label for="view_age">Age</label>
                                                <input type="text" v-model="wdata.view_age" name="view_age" id="view_ageadd" class="form-control" disabled>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-2 col-md-12" style="padding-top: 9px;">
                                                <label for="gender">Sex <span style="color:red">*</span></label>
                                                <select name="gender" v-model="wdata.gender" id="gender" class="form-control">
                                                    <option disabled selected></option>
                                                    <option value="Male" name="genderselect" id="gsel_m"> Male </option>
                                                    <option value="Female" name="genderselect" id="gsel_f"> Female </option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="maritalstatus">Marital Status <span style="color:red">*</span></label>
                                                <select class="form-control" v-model="wdata.maritalstatus" name="maritalstatus">
                                                    <template v-for="(list,index) in lib.maritalstatlibrary">
                                                        <option :value="list.id">{{list.name}}</option>
                                                    </template>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="birthplace">Place of Birth: <span style="color:red">*</span></label>
                                                <input name="birthplace" v-model="wdata.birthplace" id="birthplace" type="text" class="form-control">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="mothersMaidenName">Mother's Maiden Name: <span style="color:red">*</span></label>
                                                <input v-model="wdata.mothersMaidenName" name="mothersMaidenName" id="mothersMaidenName" type="text" class="form-control">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="contactno">Contact No:</label>
                                                <input v-model="wdata.contactno" name="contactno" id="contactno" type="text" class="form-control">
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="hhid">HH ID:</label>
                                                <input v-model="wdata.hhid" name="hhid" id="hhid" type="text" class="form-control">
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="hhsize">HH Size:</label>
                                                <input v-model="wdata.hhsize" name="hhsize" id="hhsize" type="number" class="form-control">
                                            </div>
                                        </div>
                                        <hr />
                                        <div class="form-group row">
                                            <p><b><u>
                                                        <h5>PERMANENT ADDRESS</h5>
                                                    </u></b></p>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12 col-lg-2 col-md-12">
                                                <label for="province_permanent">Province: <span style="color:red">*</span></label>
                                                <select class="form-control" @change="getLocation('mun_code',wdata.province_permanent,'permanent')" v-model="wdata.province_permanent" name="province_permanent">
                                                    <template v-for="(list,index) in lib.location.provinces">
                                                        <option :value="list.prov_code">{{list.prov_name}}</option>
                                                    </template>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-2 col-md-12">
                                                <label for="municipality_permanent">Municipality: <span style="color:red">*</span></label>
                                                <select class="form-control" @change="getLocation('bar_code',wdata.municipality_permanent,'permanent')" v-model="wdata.municipality_permanent" :disabled="lib.location.permanent_municipalities.length <=0" name="municipality_permanent">
                                                    <template v-for="(list,index) in lib.location.permanent_municipalities">
                                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                                    </template>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-2 col-md-12">
                                                <label for="barangay_permanent">Barangay: <span style="color:red">*</span></label>
                                                <select class="form-control" v-model="wdata.barangay_permanent" :disabled="lib.location.permanent_barangays.length <= 0" name="barangay_permanent">
                                                    <template v-for="(list,index) in lib.location.permanent_barangays">
                                                        <option :value="list.bar_code">{{list.bar_name}}</option>
                                                    </template>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="address_permanent">Address: (House No. / Purok)</label>
                                                <input type="text" class="form-control" v-model="wdata.address_permanent" name="address_permanent" id="address-select_permanent" />
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="street_permanent">Street:</label>
                                                <input type="text" class="form-control" v-model="wdata.street_permanent" name="street_permanent" id="street_permanent" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <h5 class="col-sm-2"><b><u>PRESENT ADDRESS</u></b></h5>
                                            <label for="same-address" class="col-sm-2 text-right">Same as Permanent Address?</label>
                                            <div class="custom-checkbox col-sm-8">
                                                <div class="custom-checkbox-50">
                                                    <div class="custom-checkbox-primary">
                                                        <input type="radio" value="1" v-model="sameAddress" id="yes" name="same-address" @change="copyAddress(1)">
                                                        <label for="yes">Yes</label>
                                                    </div>
                                                    <div class="custom-checkbox-primary">
                                                        <input type="radio" value="0" v-model="sameAddress" id="no" name="same-address" @change="copyAddress(0)">
                                                        <label for="no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12 col-lg-2 col-md-12">
                                                <label for="province_present">Province: <span style="color:red">*</span></label>
                                                <select class="form-control" @change="getLocation('mun_code',wdata.province_present,'present')" v-model="wdata.province_present" name="province_present">
                                                    <template v-for="(list,index) in lib.location.provinces">
                                                        <option :value="list.prov_code">{{list.prov_name}}</option>
                                                    </template>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-2 col-md-12">
                                                <label for="municipality_present">Municipality: <span style="color:red">*</span></label>
                                                <select class="form-control" @change="getLocation('bar_code',wdata.municipality_present,'present')" v-model="wdata.municipality_present" :disabled="lib.location.present_municipalities.length <=0" name="municipality_present">
                                                    <template v-for="(list,index) in lib.location.present_municipalities">
                                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                                    </template>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-2 col-md-12">
                                                <label for="barangay_present">Barangay: <span style="color:red">*</span></label>
                                                <select class="form-control" v-model="wdata.barangay_present" :disabled="lib.location.present_barangays.length <=0" name="barangay_present">
                                                    <template v-for="(list,index) in lib.location.present_barangays">
                                                        <option :value="list.bar_code">{{list.bar_name}}</option>
                                                    </template>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="address_present">Address: (House No. / Purok)</label>
                                                <input type="text" class="form-control" v-model="wdata.address_present" name="address_present" id="address-select_present" />
                                            </div>
                                            <div class="col-sm-12 col-lg-3 col-md-12">
                                                <label for="street_present">Street:</label>
                                                <input type="text" class="form-control" v-model="wdata.street_present" name="street_present" id="street_present" />
                                            </div>
                                        </div>
                                        <hr />
                                        <div class="form-group row">
                                            <p><b><u>PENSIONER REPRESENTATIVES</u> </b></p>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12 col-lg-6 col-md-12">
                                                <div class="form-group row">
                                                    <label for="caregivername">Name of Caregiver (1st Representative): <span style="color:red">*</span></label>
                                                    <input v-model="wdata.caregivername" name="caregivername" id="caregivername" type="text" class="form-control">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-lg-6 col-md-12">
                                                <div class="form-group row">
                                                    <label for="caregiverrelp">Relationship of Caregiver: <span style="color:red">*</span></label>
                                                    <select class="form-control" v-model="wdata.caregiverrelp" name="caregiverrelp">
                                                        <template v-for="(list,index) in lib.relationshiplibrary">
                                                            <option :value="list.relid">{{list.relname}}</option>
                                                        </template>
                                                    </select>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <br />

                                            <div class="col-sm-12 col-lg-6 col-md-12">
                                                <div class="form-group row">
                                                    <label for="rep2name">2nd Representative Name:</label>
                                                    <input v-model="wdata.rep2name" name="rep2name" id="rep2name" type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-lg-6 col-md-12">
                                                <div class="form-group row">
                                                    <label for="rep2rel">Relationship of 2nd Rep to Senior:</label>
                                                    <select class="form-control" title="Select Relationship" v-model="wdata.rep2rel" name="rep2rel" id="rep2rel">
                                                        <template v-for="(list,index) in lib.relationshiplibrary">
                                                            <option :value="list.relid">{{list.relname}}</option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12 col-lg-6 col-md-12">
                                                <div class="form-group row">
                                                    <label for="rep3name">3rd Representative Name:</label>
                                                    <input v-model="wdata.rep3name" name="rep3name" id="rep3name" type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-lg-6 col-md-12">
                                                <div class="form-group row">
                                                    <label for="rep3rel">Relationship of 3rd Rep to Senior:</label>
                                                    <select class="form-control" title="Select Relationship" v-model="wdata.rep3rel" name="rep3rel" id="rep3rel">
                                                        <template v-for="(list,index) in lib.relationshiplibrary">
                                                            <option :value="list.relid">{{list.relname}}</option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <hr />

                                        <div class="form-group row">

                                            <div class="col-sm-12 col-lg-6 col-md-12">
                                                <div class="form-group row">
                                                    <b>10. Do you receive any form of pension?</b>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="form-check col-sm-12" style="padding-left: 1.25rem !important;">
                                                        <input class="form-check-input" type="radio" v-model="wdata.pensionreceiver" name="pensionreceiver" id="preceiver_yes" value="1" />
                                                        <label style="font-weight: initial; !important;" class="form-check-label" for="preceiver_yes">1 YES</label>
                                                    </div>
                                                    <div class="form-check col-sm-12" style="padding-left: 1.25rem !important;">
                                                        <input class="form-check-input" type="radio" v-model="wdata.pensionreceiver" name="pensionreceiver" id="preceiver_no" value="2" />
                                                        <label style="font-weight: initial; !important;" class="form-check-label" for="preceiver_no">2 NO</label>
                                                    </div>
                                                    <div class="form-check col-sm-12" style="padding-left: 1.25rem !important;">
                                                        <input class="form-check-input" type="radio" v-model="wdata.pensionreceiver" name="pensionreceiver" id="preceiver_dontknow" value="3" />
                                                        <label style="font-weight: initial; !important;" class="form-check-label" for="preceiver_dontknow">3 DON'T KNOW</label>
                                                    </div>
                                                </div>

                                                <!-- <div class="form-group row">
                                            
                                        </div>
                                        <div class="form-group row">
                                            
                                        </div> -->
                                                <hr />
                                                <div class="form-group row">
                                                    <b>11. What pension/s did you receive in the past 6 months?</b>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="form-check col-sm-12" style="padding-left: 1.25rem !important;">
                                                        <input class="form-check-input" type="checkbox" v-model="wdata.pensionsreceived_dswd" id="pensionsreceived_dswd" name="pensionsreceived_dswd" />
                                                        <label class="form-check-label" style="font-weight: initial; !important;" for="pensionsreceived_dswd">1 DSWD Social Pension</label>
                                                    </div>

                                                    <div class="form-check col-sm-12" style="padding-left: 1.25rem !important;">
                                                        <input class="form-check-input" type="checkbox" v-model="wdata.pensionsreceived_gsis" id="pensionsreceived_gsis" name="pensionsreceived_gsis" />
                                                        <label class="form-check-label" style="font-weight: initial; !important;" for="pensionsreceived_gsis">2 GSIS</label>
                                                    </div>

                                                    <div class="form-check col-sm-12" style="padding-left: 1.25rem !important;">
                                                        <input class="form-check-input" type="checkbox" v-model="wdata.pensionsreceived_sss" id="pensionsreceived_sss" name="pensionsreceived_sss" />
                                                        <label class="form-check-label" style="font-weight: initial; !important;" for="pensionsreceived_sss">3 SSS</label>
                                                    </div>

                                                    <div class="form-check col-sm-12" style="padding-left: 1.25rem !important;">
                                                        <input class="form-check-input" type="checkbox" v-model="wdata.pensionsreceived_afpslai" id="pensionsreceived_afpslai" name="pensionsreceived_afpslai" />
                                                        <label class="form-check-label" style="font-weight: initial; !important;" for="pensionsreceived_afpslai">4 AFPSLAI</label>
                                                    </div>
                                                    <div class="form-check col-sm-3" style="padding-left: 1.25rem !important;">
                                                        <input class="form-check-input" type="checkbox" v-model="wdata.pensionsreceived_others" id="pensionsreceived_others" name="pensionsreceived_others" />
                                                        <label class="form-check-label" style="font-weight: initial; !important;" for="pensionsreceived_others">5 Other</label>
                                                    </div>

                                                    <div class="form-check col-sm-9" style="padding-left: 1.25rem !important;">
                                                        <input type="text" v-model="wdata.pensionsreceived_other" name="pensionsreceived_other" />
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="form-group row">
                                                    <b>12. What are your sources of income and financial support in the past 6 months (other than your pension/s)? For each source, indicate if it is regular then record the estimated amount of income and divide by the household size, if applicable.</b><br />
                                                    <table id="source_income_table">
                                                        <thead>
                                                            <th>A. Source</th>
                                                            <th>B. Is it regular?</th>
                                                            <th>C. Amount of Income</th>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" v-model="wdata.income_wages" id="income_wages" name="income_wages" value="1" />
                                                                        <label class="form-check-label" for="income_wages" style="font-weight: initial !important">1 Wages/ Salaries</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans4" name="ans4" id="editreg_wages_y" value="1"/ :disabled="wdata.income_wages == false">
                                                                        <label class="form-check-label" for="editreg_wages_y" style="font-weight: initial !important">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans4" name="ans4" id="editreg_wages_n" value="0"/ :disabled="wdata.income_wages == false">
                                                                        <label class="form-check-label" for="editreg_wages_n" style="font-weight: initial !important">No</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check-label" for="editreg_wages_amt" style="font-weight: initial !important">PhP</label>
                                                                    <input class="form-check-input php-input" type="text" v-model="wdata.ans4_amt" name="ans4_amt" id="editreg_wages_amt" :disabled="wdata.ans4 == ''" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" v-model="wdata.income_entrep" id="income_entrep" namesourcesOfIncom="income_entrep" value="1" />
                                                                        <label class="form-check-label" for="income_entrep" style="font-weight: initial !important">2 Profits from Entrepreneurial Activities</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans6" name="ans6" id="editreg_entrep_y" value="1"/ :disabled="wdata.income_entrep == false">
                                                                        <label class="form-check-label" for="editreg_entrep_y" style="font-weight: initial !important">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans6" name="ans6" id="editreg_entrep_n" value="0"/ :disabled="wdata.income_entrep == false">
                                                                        <label class="form-check-label" for="editreg_entrep_n" style="font-weight: initial !important">No</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check-label" for="editreg_entrep_amt" style="font-weight: initial !important">PhP</label>
                                                                    <input class="form-check-input php-input" type="text" v-model="wdata.ans6_amt" name="ans6_amt" id="editreg_entrep_amt" :disabled="wdata.ans6 == ''" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" v-model="wdata.income_household" id="income_household" name="income_household" value="1" />
                                                                        <label class="form-check-label" for="income_household" style="font-weight: initial !important">3 Household Family Members/ Relatives</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans8" name="ans8" id="editreg_household_y" value="1"/ :disabled="wdata.income_household == false">
                                                                        <label class="form-check-label" for="editreg_household_y" style="font-weight: initial !important">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans8" name="ans8" id="editreg_household_n" value="0"/ :disabled="wdata.income_household == false">
                                                                        <label class="form-check-label" for="editreg_household_n" style="font-weight: initial !important">No</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check-label" for="editreg_household_amt" style="font-weight: initial !important">PhP</label>
                                                                    <input class="form-check-input php-input" type="text" v-model="wdata.ans8_amt" name="ans8_amt" id="editreg_household_amt" :disabled="wdata.ans8 == ''" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" v-model="wdata.income_domestic" id="income_domestic" name="income_domestic" value="1" />
                                                                        <label class="form-check-label" for="income_domestic" style="font-weight: initial !important">4 Domestic Family Members / Relatives</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans10" name="ans10" id="editreg_domestic_y" value="1"/ :disabled="wdata.income_domestic == false">
                                                                        <label class="form-check-label" for="editreg_domestic_y" style="font-weight: initial !important">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans10" name="ans10" id="editreg_domestic_n" value="0"/ :disabled="wdata.income_domestic == false">
                                                                        <label class="form-check-label" for="editreg_domestic_n" style="font-weight: initial !important">No</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check-label" for="editreg_domestic_amt" style="font-weight: initial !important">PhP</label>
                                                                    <input class="form-check-input php-input" type="text" v-model="wdata.ans10_amt" name="ans10_amt" id="editreg_domestic_amt" :disabled="wdata.ans10 == ''" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" v-model="wdata.income_international" id="income_international" name="income_international" value="1" />
                                                                        <label class="form-check-label" for="income_international" style="font-weight: initial !important">5 International Family Members/ Relatives</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans12" name="ans12" id="editreg_international_y" value="1"/ :disabled="wdata.income_international == false">
                                                                        <label class="form-check-label" for="editreg_international_y" style="font-weight: initial !important">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans12" name="ans12" id="editreg_international_n" value="0"/ :disabled="wdata.income_international == false">
                                                                        <label class="form-check-label" for="editreg_international_n" style="font-weight: initial !important">No</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check-label" for="editreg_international_amt" style="font-weight: initial !important">PhP</label>
                                                                    <input class="form-check-input php-input" type="text" v-model="wdata.ans12_amt" name="ans12_amt" id="editreg_international_amt" :disabled="wdata.ans12 == ''" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" v-model="wdata.income_friends" id="income_friends" name="income_friends" value="1" />
                                                                        <label class="form-check-label" for="income_friends" style="font-weight: initial !important">6 Friends/ Neighbors</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans14" name="ans14" id="editreg_friends_y" value="1"/ :disabled="wdata.income_friends == false">
                                                                        <label class="form-check-label" for="editreg_friends_y" style="font-weight: initial !important">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans14" name="ans14" id="editreg_friends_n" value="0"/ :disabled="wdata.income_friends == false">
                                                                        <label class="form-check-label" for="editreg_friends_n" style="font-weight: initial !important">No</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check-label" for="editreg_friends_amt" style="font-weight: initial !important">PhP</label>
                                                                    <input class="form-check-input php-input" type="text" v-model="wdata.ans14_amt" name="ans14_amt" id="editreg_friends_amt" :disabled="wdata.ans14 == ''" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" v-model="wdata.income_government" id="income_government" name="income_government" value="1" />
                                                                        <label class="form-check-label" for="income_government" style="font-weight: initial !important">7 Transfers from the Government</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans16" name="ans16" id="editreg_government_y" value="1"/ :disabled="wdata.income_government == false">
                                                                        <label class="form-check-label" for="editreg_government_y" style="font-weight: initial !important">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans16" name="ans16" id="editreg_government_n" value="0"/ :disabled="wdata.income_government == false">
                                                                        <label class="form-check-label" for="editreg_government_n" style="font-weight: initial !important">No</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check-label" for="editreg_government_amt" style="font-weight: initial !important">PhP</label>
                                                                    <input class="form-check-input php-input" type="text" v-model="wdata.ans16_amt" name="ans16_amt" id="editreg_government_amt" :disabled="wdata.ans16 == ''" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" v-model="wdata.income_others" id="income_others" name="income_others" value="1" />
                                                                        <label class="form-check-label" for="income_others" style="font-weight: initial !important">8 Others</label>
                                                                        <input class="form-check-input" style="margin-left: 5px; margin-top: 0;" type="text" v-model="wdata.sourcesOfIncome_other" name="sourcesOfIncome_other" :disabled="wdata.income_others == false" />
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans18" name="ans18" id="editreg_others_y" value="1"/ :disabled="wdata.income_others == false">
                                                                        <label class="form-check-label" for="editreg_others_y" style="font-weight: initial !important">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" v-model="wdata.ans18" name="ans18" id="editreg_others_n" value="0"/ :disabled="wdata.income_others == false">
                                                                        <label class="form-check-label" for="editreg_others_n" style="font-weight: initial !important">No</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check-label" for="editreg_others_amt" style="font-weight: initial !important">PhP</label>
                                                                    <input class="form-check-input php-input" type="text" v-model="wdata.ans18_amt" name="ans18_amt" id="editreg_others_amt" :disabled="wdata.ans18 == ''" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-lg-6 col-md-12">
                                                <div class="form-group row">
                                                    <label for="livingArrangement">13. Who are you living with? <span style="color:red">*</span></label>
                                                    <select class="form-control" id="livingArrangement" v-model="wdata.livingArrangement" name="livingArrangement">
                                                        <template v-for="(list,index) in lib.livingarrangementlibrary">
                                                            <option :value="list.id">{{list.name}}</option>
                                                        </template>
                                                    </select>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <hr />
                                                <div class="form-group row">
                                                    <b>14. Frailty Questions</b><br />
                                                    <table style="background-color: #FFF;">
                                                        <tbody>
                                                            <tr>
                                                                <td>14.1 Are you older than 85 years?</td>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" v-model="wdata.ans20" name="frailty_older85_y" type="radio" id="frailty_older85_y" value="1" disabled="true">
                                                                        <label class="form-check-label" for="frailty_older85_y"  style="font-weight: initial !important">Yes</div>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" v-model="wdata.ans20" name="frailty_older85_n" type="radio" id="frailty_older85_n" disabled="true" value="0" />
                                                    <label class="form-check-label" for="frailty_older85_n" style="font-weight: initial !important">No</label>
                                                </div>
                                                </td>
                                                </tr>
                                                <tr>
                                                    <td>14.2 In general, do you have any health problems that require you to limit your activities?</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans21" name="frailty_healthlimit_y" type="radio" id="frailty_healthlimit_y" value="1" />
                                                            <label class="form-check-label" for="frailty_healthlimit_y" style="font-weight: initial !important">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans21" name="frailty_healthlimit_n" type="radio" id="frailty_healthlimit_n" value="0" />
                                                            <label class="form-check-label" for="frailty_healthlimit_n" style="font-weight: initial !important">No</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>14.3 Do you need someone to help you on a regular basis?</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans22" name="frailty_needregularhelp_y" type="radio" id="frailty_needregularhelp_y" value="1" />
                                                            <label class="form-check-label" for="frailty_needregularhelp_y" style="font-weight: initial !important">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans22" name="frailty_needregularhelp_n" type="radio" id="frailty_needregularhelp_n" value="0" />
                                                            <label class="form-check-label" for="frailty_needregularhelp_n" style="font-weight: initial !important">No</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>14.4 In general, do you have any health problems that require you to stay at home?</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans23" name="frailty_healthhome_y" type="radio" id="frailty_healthhome_y" value="1" />
                                                            <label class="form-check-label" for="frailty_healthhome_y" style="font-weight: initial !important">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans23" name="frailty_healthhome_n" type="radio" id="frailty_healthhome_n" value="0" />
                                                            <label class="form-check-label" for="frailty_healthhome_n" style="font-weight: initial !important">No</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>14.5 If you need help, can you count on someone close to you?</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans24" name="frailty_countonsomeone_y" type="radio" id="frailty_countonsomeone_y" value="1" />
                                                            <label class="form-check-label" for="frailty_countonsomeone_y" style="font-weight: initial !important">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans24" name="frailty_countonsomeone_n" type="radio" id="frailty_countonsomeone_n" value="0" />
                                                            <label class="form-check-label" for="frailty_countonsomeone_n" style="font-weight: initial !important">No</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>14.6 Do you regularly use a stick/ walker/ wheelchair to move about?</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans25" name="frailty_moveabout_y" type="radio" id="frailty_moveabout_y" value="1" />
                                                            <label class="form-check-label" for="frailty_moveabout_y" style="font-weight: initial !important">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" v-model="wdata.ans25" name="frailty_moveabout_n" type="radio" id="frailty_moveabout_n" value="0" />
                                                            <label class="form-check-label" for="frailty_moveabout_n" style="font-weight: initial !important">No</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                            </div>
                                            <hr />
                                            <div class="form-group row">
                                                <b>15. Do you have any disability?</b><br />
                                                <select v-model="wdata.disability" name="disability" id="edit_disability" class="form-control">
                                                    <template v-for="(list,index) in lib.disabilitylibrary">
                                                        <option :value="list.id">{{list.name}}</option>
                                                    </template>
                                                </select>
                                            </div>
                                            <hr />
                                            <div class="form-group row">
                                                <b>16. Do you have any critical illness or disease?</b>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-check" style="width: 100%;">
                                                    <input class="form-check-input" type="radio" v-model="wdata.ans27" name="ans27_y" id="ans27_y" value="1" />
                                                    <label class="form-check-label" for="ans27_y" style="font-weight: initial !important;">Yes - Illness:</label>
                                                    <input class="form-check-input" type="text" v-model="wdata.illness" name="edit_illness" id="edit_illness" style="margin-top: 0; margin-left: 5px;" :disabled="wdata.ans27 == false" />
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" v-model="wdata.ans27" name="ans27_n" id="ans27_n" value="0" />
                                                    <label class="form-check-label" for="ans27_n" style="font-weight: initial !important;">None</label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                            </div>
                                        </div>
                                    </div>

                                    <hr />
                                    <div class="form-group row">
                                        <div class="col-sm-12 col-lg-6 col-md-12">
                                            <div class="form-group row">
                                                <b>17. Where do you spend your Social Pension?</b>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" v-model="wdata.sp_food" id="sp_food" name="sp_food" value="1" />
                                                    <label class="form-check-label" for="sp_food" style="font-weight: initial !important; ">1 Food </label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" v-model="wdata.sp_med" id="sp_med" name="sp_med" value="1" />
                                                    <label class="form-check-label" for="sp_med" style="font-weight: initial !important; ">2 Medicines and Vitamins </label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" v-model="wdata.sp_checkup" id="sp_checkup" name="sp_checkup" value="1" />
                                                    <label class="form-check-label" for="sp_checkup" style="font-weight: initial !important; ">3 Health check-up and other hospital/ medical services </label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" v-model="wdata.sp_cloth" id="sp_cloth" name="sp_cloth" value="1" />
                                                    <label class="form-check-label" for="sp_cloth" style="font-weight: initial !important; ">4 Clothing </label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" v-model="wdata.sp_util" id="sp_util" name="sp_util" value="1" />
                                                    <label class="form-check-label" for="sp_util" style="font-weight: initial !important; ">5 Utilities (e.g. electric and water bills) </label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" v-model="wdata.sp_debt" id="sp_debt" name="sp_debt" value="1" />
                                                    <label class="form-check-label" for="sp_debt" style="font-weight: initial !important; ">6 Debt payment </label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" v-model="wdata.sp_entrep" id="sp_entrep" name="sp_entrep" value="1" />
                                                    <label class="form-check-label" for="sp_entrep" style="font-weight: initial !important; ">7 Livelihood / Entrepreneurial Activities </label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" v-model="wdata.sp_others" id="sp_others" name="sp_others" value="1" />
                                                    <label class="form-check-label" for="sp_others" style="font-weight: initial !important; ">8 Others</label>
                                                    <input type="text" v-model="wdata.ans28_other" name="ans28_other" id="sp_other" :disabled="wdata.sp_others == false" />
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-lg-6 col-md-12">
                                            <div class="form-group row">
                                                <b> Name of Worker:</b> <span style="color:red">*</span>
                                                <input type="text" v-model="wdata.workerName" name="workerName" id="workerName" class="form-control" />
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="form-group row">
                                                <b> Date Accomplished: </b> <span style="color:red">*</span>
                                                <input type="date" class="form-control datepicker" id="date_accomplished" v-model="wdata.date_accomplished" name="date_accomplished" />
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <div v-if="isEditing === false">
                            <!-- <button class="btn btn-primary" :disabled="!probableDuplicate.clean">Save Waitlist</button> -->
                            <button class="btn btn-primary">Save Waitlist</button>
                        </div>
                        <div v-else>
                            <button class="btn btn-primary">Update Waitlist</button>
                        </div>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Set To For Archive Waiting List MODAL -->
        <div id="archiveWaitlist" class="modal-form modal fade in" tabindex="-1" role="dialog" aria-labelledby="edit_ppmp_title" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="edit_ppmp_title">DELETE WAITINGLIST</h4>
                    </div>
                    <div class="modal-body">
                        <form class="floating-labels m-t-40 m-b-40">
                            <h4>Are you sure you want to delete <b>{{activeWaitlist.Full_Name}}</b> from Waiting List?</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-b-40">
                                        <label for="reason-select">Reason For Deleting</label>
                                        <select class="form-control p-0" title="Select Reason for Deleting" id="reason-select" name="reason-select" v-model="archive_reason">
                                            <option v-for="list in lib.ReplacementReason" :value="list.id">{{ list.name }}</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-b-40">
                                        <label for="reason-select">Remarks (Please input details of reason of deleting waitinglist)</label>
                                        <textarea class="form-control" rows="5" v-model="archive_remarks" id="remarks" name="remarks"></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="save btn btn-primary waves-effect" @click="archiveWaitlist('single')">Yes, Delete Waiting List </button>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Set To For Replacement MODAL -->
        <div id="setToForReplacement" class="modal-form modal fade in" tabindex="-1" role="dialog" aria-labelledby="edit_ppmp_title" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="edit_ppmp_title">Set To "For Replacement"</h4>
                    </div>
                    <div class="modal-body">
                        <form class="floating-labels m-t-40 m-b-40">
                            <h4>Are you sure you want to set <b>{{activeWaitlist.Full_Name}}</b> SP status to "for replacement"?</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-b-40">
                                        <label for="reason-select">Reason For Replacement</label>
                                        <select class="form-control p-0" title="Select Reason for Replacement" id="reason-select" name="reason-select" @change="reason_onchange(repreason.reason_id)" v-model="repreason.reason_id">
                                            <option value=""></option>
                                            <option v-for="list in lib.ReplacementReason" :value="list.id">{{ list.name }}</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div v-if="repreason.isDoubleEntry" class="col-md-12">
                                    <div class="form-group m-b-40">
                                        <label for="duplicate">SPID of Duplicate</label>
                                        <input type="text" id="duplicate" name="duplicate" v-model="repreason.reason_desc" class="form-control">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div v-if="repreason.isDateOfDeath" class="col-md-12">
                                    <div class="form-group m-b-40">
                                        <label for="dateofdeath">Date Of Death</label>
                                        <input type="date" id="dateofdeath" name="dateofdeath" v-model="repreason.reason_desc" class="form-control">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div v-if="repreason.isTransfered" class="col-md-12">
                                    <div class="form-group m-b-40">
                                        <label for="placeoftransfer">Place of Transfer</label>
                                        <input type="text" id="placeoftransfer" name="placeoftransfer" v-model="repreason.reason_desc" class="form-control">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div v-if="repreason.isWithPension" class="col-md-12">
                                    <div class="form-group m-b-40">
                                        <label for="pension-select">Type of Pension</label>
                                        <select class="form-control" title="Select Type of Pension" name="pension-select" id="pension-select" v-model="repreason.reason_desc">
                                            <option disabled selected></option>
                                            <option value="SSS">SSS</option>
                                            <option value="GSIS">GSIS</option>
                                            <option value="PVAO">PVAO</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <div v-if="repreason.isOthers" class="col-md-12">
                                    <div class="form-group m-b-40">
                                        <label for="otherreason">Place of Transfer</label>
                                        <input type="text" id="otherreason" name="otherreason" v-model="repreason.reason_desc" class="form-control">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="save btn btn-primary waves-effect" @click="change_sp_status()">Yes, Set To "For Replacement" </button>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- uploadwaitinglist modal -->
        <div class="modal fade" id="importwaitinglistModal" tabindex="-1" role="dialog" aria-labelledby="masterlistData" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">WAITLIST IMPORT</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row  justify-content-center">
                            <div class="col-md-12">
                                <ul>
                                    <li>Use the excel waitlist template in encoding waitlist, otherwise, upload will not proceed and errors will occur.</li>
                                    <li>Ensure that the format, codes and spelling of the following are correct:
                                        <ol>
                                            <li>Province</li>
                                            <li>Municipality</li>
                                            <li>Barangay</li>
                                            <li>Birthday</li>
                                            <li>Relationship of Representative</li>
                                            <li>Living Arrangement</li>
                                            <li>Columns with Dropdowns (data validations)</li>
                                        </ol>
                                    </li>
                                    <li>To apply and copy the formulas and data validations in the succeeding rows, highlight the cell rows and drag down the fill handle located at the corner bottom right of a cell, as shown:<br>
                                        <img src="<?= base_url('assets/img/fillhandle.png') ?>" />
                                    </li>
                                    <ul>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                <button type="button" class="save btn btn-primary waves-effect" @click="dlWaitlistTemplate()">
                                    <i class="fa fa-download"></i> Export Waitlist Excel Template
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <ul>
                                    <li>Save your excel as .csv (Comma Delimited) file before uploading.</li>
                                    <ul>
                            </div>
                            <div class="col-md-8 col-md-offset-2  text-center">
                                <template>
                                    <input class="btn btn-info btn-block" type="file" id="file" ref="file" v-on:change="handleFileUpload()" accept=".csv" />
                                </template>
                                <!-- <input class="btn btn-info btn-block" type="file" name="uploadWaitlistData" size="1000"  accept=".csv"/>    -->
                            </div>
                        </div>

                        <!-- <template>
                            <div class="container">
                                <div class="large-12 medium-12 small-12 cell">
                                <label>File
                                    <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
                                </label>
                                </div>
                            </div>
                        </template> -->

                    </div>
                    <div class="modal-footer">
                        <!-- <input type="submit" class="btn btn-primary" id="uploadWaitlistData-data" value="Upload"> -->

                        <button class="btn btn-primary" v-on:click="submitFile()">Submit</button>
                        <button class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Eligibility Status modal -->
        <div class="modal fade" id="importEligibilityModal" tabindex="-1" role="dialog" aria-labelledby="masterlistData" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">IMPORT ELIGIBILITY STATUS</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                    Save your excel as .csv (Comma Delimited) file before uploading. <br>
                                    The file should contain the following columns. <br>
                                    REFERENCE CODE | eligibility | batch_no | remarks | duplicate | archived
                            </div>
                            <div class="col-md-8 col-md-offset-2  text-center">
                                <template>
                                    <input class="btn btn-info btn-block" type="file" id="eligible_file" ref="eligible_file" v-on:change="eligibilityFileUpload()" accept=".csv" />
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" v-on:click="submitUpdateEligibleFile()">Submit</button>
                        <button class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Probable Duplicate Modal -->
        <div class="modal fade" id="probableDuplicateModal" tabindex="-1" role="dialog" aria-labelledby="probableDuplicate" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Probable duplicate/s for <span class="red">{{wdata.firstname}} {{wdata.middlename}} {{wdata.lastname}}</span> have been found in the SOCPEN database. Are you sure you want to proceed?</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <template v-if="probableDuplicate.probableActiveData != ''">
                                    <table class="table table-striped" style="margin-bottom: 15px;">
                                        <thead>
                                            <th class="text-center">#</th>
                                            <th class="text-center">SPID #</th>
                                            <th class="text-center">Full Name</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Status</th>
                                        </thead>
                                        <tbody>
                                            <template v-for="(list,index) in probableDuplicate.probableActiveData">
                                                <tr>
                                                    <td class="text-center">{{index+1}}</td>
                                                    <td class="text-center">{{list.connum}}</td>
                                                    <td class="text-center">{{list.fullname}}</td>
                                                    <td class="text-center">{{list.address}}</td>
                                                    <td class="text-center">{{list.sp_status}}</td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </template>

                                <template v-if="probableDuplicate.probableWaitlistData != ''">
                                    <p>Waitlist Database</p>
                                    <table class="table table-striped">
                                        <thead>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Reference Code</th>
                                            <th class="text-center">Full Name</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Status</th>
                                        </thead>
                                        <tbody>
                                            <template v-for="(list,index) in probableDuplicate.probableWaitlistData">
                                                <tr>
                                                    <td class="text-center">{{index+1}}</td>
                                                    <td class="text-center">{{list.reference_code}}</td>
                                                    <td class="text-center">{{list.fullname}}</td>
                                                    <td class="text-center">{{list.address}}</td>
                                                    <td class="text-center" v-if="list.priority == 0 ">No eligibility status yet</td>
                                                    <td class="text-center" v-if="list.priority == 1 ">Eligible</td>
                                                    <td class="text-center" v-if="list.priority == 2 ">Not eligible</td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" v-on:click="updateWaitlist()">Proceed</button>
                        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
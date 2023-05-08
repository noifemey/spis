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

<div id="m_index">
    <div class="row">
        <div class="col-md-12 ">
            <br>
            <h3>Active Social Pension Beneficiaries </h3><br>
            <div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form v-on:submit.prevent="searchMember">
                        <div class="row">
                            <div class="col-md-12 ">
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
                                        <label for="Status">SP Status</label>
                                        <select class="form-control p-0" v-model="global_search.search.status" name="Status" id="Gender">
                                            <option value="0"></option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="ForReplacement">For Replacement</option>
                                        </select>
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
                                        <button type="button" class="btn btn-success btn-block" @click="exportMasterlist()">
                                            <i class="fa fa-download"></i> DOWNLOAD MASTERLIST</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <h3> Social Pension Masterlist </h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <v-server-table :columns="masterlist.column" :options="masterlist.options" ref='servermembertable'>
                                        <template slot="No" slot-scope="e">
                                            {{e.index}}
                                        </template>
                                        <template slot="Full_Name" slot-scope="e">
                                            {{getFullname(e.row.lastname,e.row.firstname,e.row.middlename,e.row.extensionname)}}
                                        </template>
                                        <template slot="Birth_Date" slot-scope="e">
                                            {{e.row.birthdate}}
                                        </template>
                                        <template slot="Age" slot-scope="e">
                                            {{getAge(e.row.birthdate)}}
                                        </template>
                                        <template slot="Gender" slot-scope="e">
                                            {{e.row.gender}}
                                        </template>
                                        <template slot="SP_Status" slot-scope="e">
                                            <div v-if="e.row.sp_status === 'Inactive'">
                                                <label class="btn btn-danger btn-sm ">Inactive </label>
                                            </div>
                                            <div v-else-if="e.row.sp_status === 'ForReplacement'">
                                                <strong class="text-nowrap"><label>FOR REPLACEMENT</label> </strong><br>

                                                <?php if(getUserRole() <= 2):?>
                                                <button class="btn btn-warning btn-sm "@click="selectData(e.row),getEligibleWaitlist(e.row.SPID)">Replace</button>
                                                <button class="btn btn-info btn-sm " data-toggle="modal" data-target="#setToActive" @click="selectData(e.row),setToActive()">Set To Active</button>
                                                <?php endif;?>
                                            </div>
                                            <div v-else>
                                                <strong><label>ACTIVE</label> </strong><br>
                                                <?php if(getUserRole() <= 2):?>
                                                <button class="btn btn-info btn-sm " data-toggle="modal" data-target="#setToForReplacement" @click="selectData(e.row)">Set to For Replacement</button>
                                                <?php endif;?>
                                            </div>
                                        </template>
                                        <template slot="Registration_Date" slot-scope="e">
                                            {{formatDate(e.row.registrationdate)}} <br>
                                            <strong>{{getregistration(e.row.additional)}}</strong>
                                        </template>
                                        <template slot="Province" slot-scope="e">
                                            {{getprovname(e.row.province)}}
                                        </template>
                                        <template slot="Municipality" slot-scope="e">
                                            {{getmunname(e.row.province,e.row.city)}}
                                        </template>
                                        <template slot="Barangay" slot-scope="e">
                                            {{getbarname(e.row.city,e.row.barangay)}}
                                        </template>

                                        <template slot="actions" slot-scope="e">
                                            <a :href="getUrl(e.row.SPID)" class="btn btn-pill btn-info btn-round btn-sm btn-secondary text-nowrap"><i class="fa fa-eye"></i>Details</a>
                                            <?php if(getUserRole() <= 2):?>
                                            <template v-if="e.row.sp_status === 'Active'">
                                                <a :href="getUrl(e.row.SPID,2)" class="btn btn-pill btn-warning btn-round btn-sm btn-secondary"><i class="fa fa-edit"></i>Edit</a>
                                            </template>
                                            <button class="btn btn-pill btn-success btn-sm " data-toggle="modal" data-target="#memPaymentModal" @click="selectData(e.row),getMemPayment(e.row.SPID,e.row.sp_status)"><i class="fa fa-eye "></i> Payment</button>
                                            <?php endif;?>
                                        </template>
                                    </v-server-table>
                                </div>
                            </div>
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
                                <h4>Are you sure you want to set <b>{{getFullname(activeSP.lastname,activeSP.firstname,activeSP.middlename,activeSP.extensionname)}}</b> SP status to "for replacement"?</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-40">
                                            <label for="reason-select">Reason For Replacement</label>
                                            <select class="form-control p-0" title="Select Reason for Replacement" id="reason_id" name="reason_id" @change="reason_onchange(repreason.reason_id)" v-model="repreason.reason_id" required>
                                                <!-- <option value=""></option>-->
                                                <option v-for="list in ReplacementReason" :value="list.id" v-if="list.status == 1">{{ list.name }}</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div v-if="repreason.isDoubleEntry" class="col-md-12">
                                        <div class="form-group m-b-40">
                                            <label for="duplicate">SPID of Duplicate</label>
                                            <input type="text" id="duplicate" name="duplicate" v-model="repreason.duplicate" class="form-control" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div v-if="repreason.isDateOfDeath" class="col-md-12">
                                        <div class="form-group m-b-40">
                                            <label for="dateofdeath">Date Of Death</label>
                                            <input type="date" id="dateofdeath" name="dateofdeath" v-model="repreason.dateofdeath" class="form-control">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div v-if="repreason.isTransfered" class="col-md-12">
                                        <div class="form-group m-b-40">
                                            <label for="placeoftransfer">Place of Transfer</label>
                                            <input type="text" id="placeoftransfer" name="placeoftransfer" v-model="repreason.placeoftransfer" class="form-control">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div v-if="repreason.isWithPension" class="col-md-12">
                                        <div class="form-group m-b-40">
                                            <label for="pension-select">Type of Support</label>
                                            <select class="form-control" title="Select Type of Pension" name="pension_select" id="pension_select" v-model="repreason.pension_select">
                                                <option disabled selected></option>
                                                <option value="SSS">SSS</option>
                                                <option value="GSIS">GSIS</option>
                                                <option value="PVAO">PVAO</option>
                                                <option value="AFPMBAI">AFPMBAI</option>
                                                <option value="Living Abroad">Living Abroad</option>
                                                <option value="Others">Others</option>
                                                <!-- <option value="Social Pension provided by the Province">Social Pension provided by the Province</option> -->
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div v-if="repreason.isWithIncome" class="col-md-12">
                                        <div class="form-group m-b-40">
                                            <label for="pension-select">Type of Regular Income</label>
                                            <select class="form-control" title="Select Type of Pension" name="pension_select" id="pension_select" v-model="repreason.pension_select">
                                                <option disabled selected></option>
                                                <option value="Barangay Official">Barangay Official</option>
                                                <option value="OFW">OFW</option>
                                                <option value="Others">Others</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div v-if="repreason.isOthers" class="col-md-12">
                                        <div class="form-group m-b-40">
                                            <label for="otherreason">Position</label>
                                            <input type="text" id="otherreason" name="otherreason" v-model="repreason.otherreason" class="form-control">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div v-if="repreason.pension_select == 'Others'" class="col-md-12">
                                        <div class="form-group m-b-40">
                                            <label for="otherreason">Please specify:</label>
                                            <input type="text" id="otherreason" name="otherreason" v-model="repreason.otherreason" class="form-control">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect" @click="change_sp_status()">Yes, Set To "For Replacement" </button>
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Replace Button Click Modal -->
            <div class="modal fade" id="replaceMember" tabindex="-1" role="dialog" aria-labelledby="replaceMember" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <form id="replace_form" class="form-horizontal form-data" enctype="multipart/form-data" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="replaceMember">Replace Beneficiary</h5>
                            </div>
                            <div class="modal-body">
                                <h4>Please Select Replacement of <b>{{getFullname(activeSP.lastname,activeSP.firstname,activeSP.middlename,activeSP.extensionname)}}</b> from Eligible waitlist below</h4>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label for="Province">Province</label>
                                        <select class="form-control p-0" @change="getWaitlistFilterLocation('mun_code', filterWaitlist.prov_code)" v-model="filterWaitlist.prov_code" name="Province">
                                            <template v-for="(list,index) in waitlistLocation.provinces">
                                                <option :value="list.prov_code">{{list.prov_name}}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="Municipality">Municipality</label>
                                        <select class="form-control p-0" v-model="filterWaitlist.mun_code" :disabled="waitlistLocation.municipalities.length <=0" name="Municipality">
                                            <template v-for="(list,index) in waitlistLocation.municipalities">
                                                <option :value="list.mun_code">{{list.mun_name}}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-primary btn-block waves-effect" @click="filterEligibleWaitlist()" :disabled = "waitlistLocation.municipalities.length <=0">Filter</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-40">
                                            <label class="control-label" for="w-select">Eligible Waitlist <span class = "text-danger"><b>*</b><span></label>
                                            <v-select v-model="wdata.data" :options="woptions" :reduce="option => option" label="label"  @input="getWaitlistInfo()"  />
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Birthdate: </label>
                                        <input type="text" class="form-control" readonly v-model = "wdata.data.birthdate">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="">Age: </label>
                                        <input type="text" class="form-control" readonly v-model = "wdata.data.age">


                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group label-floating">
                                            <label class="control-label" for="dateofreplacement">Date Of Replacement  <span class = "text-danger"><b>*</b><span></label>
                                            <input v-model="wdata.dateofreplacement" type="date" name="dateofreplacement" class="form-control datepicker" required>
                                        </div>
                                    </div>
                                </div>
                             
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group label-floating">
                                            <label class="control-label">Name of Worker  <span class = "text-danger"><b>*</b><span></label>
                                            <input v-model="wdata.work_name" name="r_nameOfWorker" type="text" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group label-floating">
                                            <label class="control-label" for="r_dateAccomplish">Date Accomplished  <span class = "text-danger"><b>*</b><span>:  </label>
                                            <input v-model="wdata.dateAccomplish" type="date" name="r_dateAccomplish" class="form-control datepicker" required>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h3>Payment History</h3>
                                <h5><i class="text-info icon icon-info"></i> Check to transfer history payment.</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-responsive-sm table-hover table-outline mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-center">Action</th>
                                                    <th>Year</th>
                                                    <th>Period</th>
                                                    <th>Date of Payout</th>
                                                    <th>Receiver</th>
                                                    <th>Amount</th>
                                                    <th>Payment Status</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template v-for="list,index in memPayments.data">
                                                    <tr>
                                                        <td>
                                                           <template v-if="list.liquidation ==0 && checkSemi(list.mode_of_payment,list.period) && wdata.data.w_id >=1 && list.year>=2019 "> 
                                                                <input type="checkbox" v-model="wdata.liquidation" :value="list.p_id">
                                                                <!-- {{checkSemi(list.mode_of_payment,list.period) }} -->
                                                            </template>

                                                        </td>
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

                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary waves-effect" @click="replaceMember()" :disabled = "memberReplaceDisable">Yes, Replace Beneficiary </button>
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Member Payments -->
            <div id="memPaymentModal" class="modal-form modal fade in" tabindex="-1" role="dialog" aria-labelledby="memPaymentModal" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Payments History of <b>{{getFullname(activeSP.lastname,activeSP.firstname,activeSP.middlename,activeSP.extensionname)}} ({{activeSP.SPID}})</b></h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <!-- <div class="col-md-12">
                                    <button type="button" class="btn btn-primary waves-effect" @click="newSelect()">Add New Payment</button>
                                </div> -->
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
                                                    <!-- <button type="submit" :disabled="memPayments.activeMP.year >= '2020'" class="btn btn-primary waves-effect  btn-block">Save</button> -->
                                                    <!-- <button type="submit" class="btn btn-primary waves-effect  btn-block">Save</button> -->
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
                                    <button v-if="typeof memPayments.selected != 'undefined' && memPayments.selected.length > 0" class="btn btn-sm btn-success mb-1 pull-right" type="button" @click="transferPaymentsNewLocation()">Transfer checked payments to current address</button>
                                    <table class="table table-responsive-sm table-hover table-outline mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>
                                                    <label class="form-checkbox">
                                                        <input type="checkbox" v-model="memPayments.selectAll" @click="select">
                                                        <i class="form-icon"></i>
                                                    </label>
                                                </th>
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
                                                        <template v-if="list.liquidation != 1">
                                                            <label class="form-checkbox">
                                                                <input type="checkbox" :value="list" v-model="memPayments.selected">
                                                                <i class="form-icon"></i>
                                                            </label>
                                                        </template>
                                                    </td>
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
                                                            <!-- <button v-if="list.year < 2020" class="btn btn-sm btn-pill btn-danger btn-secondary" type="button" data-toggle="modal" @click="deleteMemPayment(list)">
                                                                <i class="fa fa-trash text-center"></i>
                                                            </button> -->
                                                            <button v-if="list.liquidation != 2" class="btn btn-sm btn-pill btn-danger btn-secondary" type="button" data-toggle="modal" @click="deleteMemPayment(list)">
                                                                <i class="fa fa-trash text-center"></i>
                                                            </button>
                                                            <button v-if="memPayments.spstatus == 'Inactive' && (list.liquidation == 0 || list.liquidation == 1)" class="btn btn-sm btn-pill btn-info btn-secondary" type="button" data-toggle="modal" @click="getMemberTransferInfo(list)">
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

            <!-- ADD COA -->
        </div>
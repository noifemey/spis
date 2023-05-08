<style>
    th, td {
        border: 1px solid black;
        font-size: 12px;
        padding: 8px;
    }
    th{
        background-color: blue;
        text-align: center;
        color: white;
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
    .col-sm-12, .col-sm-6, .col-sm-4, .col-sm-3, .form-group, .label-floating {
        margin: 0% !important;
        padding: 0.5% !important;
    }
</style>

<div id = "l_index">
    <div class="row">
        <div class="col-md-12 ">
            <br> <h3>Liquidation </h3><br>
        <div>
    </div>
    <div class="card">
        <div class="card-body">
            <form v-on:submit.prevent="getallPayroll">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="row" >
                            <div class="col-md-4 "> 
                                <label for="Province">Province</label>
                                <select class="form-control p-0"   @change = "getLocation('mun_code',search.prov_code)" v-model = "search.prov_code" name="Province" required>
                                    <template v-for = "(list,index) in location.provinces">
                                    <option :value="list.prov_code">{{list.prov_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-4 "> 
                                <label for="Municipality">Municipality</label>
                                <select class="form-control p-0"   @change = "getLocation('bar_code',search.mun_code)" v-model = "search.mun_code" :disabled = "location.municipalities.length <=0" name="Municipality" >
                                    <template v-for = "(list,index) in location.municipalities">
                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-4 "> 
                                <label for="Barangay">Barangay</label>
                                <select class="form-control p-0"   v-model = "search.bar_code" :disabled = "location.barangays.length <=0" name="Barangay">
                                    <template v-for = "(list,index) in location.barangays">
                                        <option :value="list.bar_code">{{list.bar_name}}</option>
                                    </template>
                                </select>
                            </div>
                        </div>   

                        <div class="row" >
                            <div class="col-md-3 ">                                 
                                <label for="Year">Year</label>
                                <select class="form-control p-0"   v-model = "search.year" required>
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
                            </div>
                            <div class="col-md-3 "> 
                                <label for="Year">Period</label>
                                <select class="form-control p-0"   v-model = "search.period" required>
                                    <option value="">Select Period</option>
                                    <option value="5">1st Semester</option>
                                    <option value="6">2nd Semester</option>
                                    <option value="1">1st Quarter</option>
                                    <option value="2">2nd Quarter</option>
                                    <option value="3">3rd Quarter</option>
                                    <option value="4">4th Quarter</option>
                                </select>
                            </div>
                            <div class="col-md-3 "> 
                                <label for="Type">Payment Status</label>
                                <select class="form-control p-0"   v-model = "search.liquidation" required>
                                    <option value="">All</option>
                                    <option value="0">UNPAID</option>
                                    <option value="1">PAID</option>
                                    <option value="2">TRANSFERRED</option>
                                    <option value="3">OFFSET</option>
                                    <option value="4">ONHOLD</option>
                                </select>
                            </div>
                            <div class="col-md-3 "> 
                                <label for="Type">Type</label>
                                <select class="form-control p-0"   v-model = "search.type" required>
                                    <option value="all">All</option>
                                    <option value="0">Regular</option>
                                    <option value="3">All Additional</option>
                                    <option value="1">Additional Batch 1</option>
                                    <option value="2">Additional Batch 2</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 "> 
                                <button type="submit" class="btn btn-info btn-block"  @click = "ShowList()">
                                <i class="fa fa-search"></i> Show List</button>
                            </div>
                            <!-- <div class="col-md-3 "> 
                                <button type="submit" class="btn btn-primary btn-block" @click = "ShowPaid()">
                                <i class="fa fa-search"></i> Show List of PAID</button>
                            </div> -->
                            <div class="col-md-3 "> 
                                <button class="btn btn-success btn-block" @click = "dlPayrollList()">
                                <i class="fa fa-download"></i> Download List</button>
                            </div>
                            <div class="col-md-3 "> 
                                <button class="btn btn-success btn-block" @click = "dlPaymentLiquidation()">
                                <i class="fa fa-download"></i> Download Payment Liquidation </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div v-if="userrole == 1" class="row">
        <div class="col-md-3">
            <a href="" class="btn btn-success btn-block" data-toggle="modal" data-target="#importPaymentModal">
                <i class="fa fa-upload"></i> Import PAYMENTS
            </a>    
        </div>
        <div class="col-md-3 offset-md-6">
            <a href="" class="btn btn-success btn-block" @click = "dlPaidRegistry()">
                <i class="fa fa-download"></i> Download PAID REGISTRY
            </a>    
        </div>    
    </div>
    <div v-if="userrole == 2" class="row">
        <div class="col-md-3 offset-md-6">
            <a href="" class="btn btn-success btn-block" @click = "dlPaidRegistry()">
                <i class="fa fa-download"></i> Download PAID REGISTRY
            </a>    
        </div>    
    </div>

    <div class="card">
        <div class="row">
            <div class="col-12">
                <div class="card-header">
                    <!-- <button class="btn btn-success" data-toggle="modal" data-target="#export_Active">
                        <i class="fa fa-download"></i> Download List
                    </button> -->

                    <div class="row">
                        <div class="col-md-12"> 
                            <h5>{{getmunname(search.prov_code, search.mun_code)}} {{getprovname(search.prov_code) }} {{getpaymentstatus(search.liquidation)}}</h5>
                            <span>{{getperiodname(search.period)}} {{search.year}} <span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Total Target</th>
                                    <th>Total PAID</th>
                                    <th>UNPAID</th>
                                    <th>OFFSET</th>
                                    <th>ONHOLD</th>
                                    <th>Total UNPAID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="loading">
                                    <tr >
                                        <td colspan="6" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td class="text-center">{{total_target}}</td>
                                        <td class="text-center">{{total_paid}}</td>
                                        <td class="text-center">{{total_unpaid}}</td>
                                        <td class="text-center">{{total_offset}}</td>
                                        <td class="text-center">{{total_onhold}}</td>
                                        <td class="text-center">{{total_unclaimed}}</td>
                                    </tr>
                                </template>

                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control" id="myInput" type="text" placeholder="Search Name" v-on:keyup="keymonitor" v-model="tblFilter">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-info btn-block" @click = "batchSave()" >
                                <i class="fa fa-save"></i> Save All Changes on the Checked List
                            </button>
                        </div>
                        <div class="col-md-4">
                            <template v-if="selected.length > 0">
                                <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#batchPayment" >
                                    <i class="fa fa-minus"></i> Set Checked List to {{liquidationText}}
                                </button>
                            </template>
                        </div>
                    </div>
                    <hr>
                    <template v-if="selected.length > 0"> <h6>Selected : {{selected.length}}<h6></template>
                    <div class="row">
                        <div class="col-md-12">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <template v-if="search.liquidation == '0' || search.liquidation == '1'">
                                        <th>
                                            <label class="form-checkbox">
                                                <input type="checkbox" v-model="selectAll" @click="select">
                                                <i class="form-icon"></i>
                                            </label>
                                        </th>
                                    </template>
                                    <th>No</th>
                                    <th>SPID</th>
                                    <th>Full Name</th>
                                    <th>Amount</th>
                                    <th>Receiver</th>
                                    <th>Date Receive</th>
                                    <th>Remarks</th>
                                    <th>Payment Status</th>
                                    <th>Barangay</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="myTable">
                                <template v-if="loading">
                                    <tr >
                                        <td colspan="11" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <template v-if="payrollList.length != 0">
                                        <tr v-for="(i, index) in payrollList">
                                            <template v-if="search.liquidation == '0' || search.liquidation == '1'">
                                                <td>
                                                    <label class="form-checkbox">
                                                        <input type="checkbox" :value="i.spid" v-model="selected" @click="selectIndividual(i,$event)">
                                                        <i class="form-icon"></i>
                                                    </label>
                                                </td>
                                            </template>
                                            <td>{{index + 1}}</td>
                                            <td>{{i.spid}}</td>
                                            <template v-if="i.spstatus == 'ForReplacement'">
                                                <td style="background-color:#fa0000" > {{i.fullname}} * </td>
                                            </template>
                                            <template v-else> <td>{{i.fullname}}</td></template>
                                            <td> <input class="form-control" type="text" v-model="i.amount"></td>
                                            <td> <input class="form-control" type="text" v-model="i.receiver"></td>
                                            <td> <input class="form-control" type="text" v-model="i.date_receive"></td>
                                            <td> <textarea class="form-control" v-model="i.remarks"></textarea></td>
                                            <td>
                                                <template v-if="i.liquidation != '2'">
                                                    <select class="form-control p-0"   v-model = "i.liquidation" required>
                                                        <option value="0">UNPAID</option>
                                                        <option value="1">PAID</option>
                                                        <option value="3">OFFSET</option>
                                                        <option value="4">ONHOLD</option>
                                                    </select>
                                                </template>
                                                <template v-else> {{getpaymentstatus(i.liquidation)}} </template>
                                            </td>
                                            <td> {{i.bar_name}}</td>
                                            <td> 
                                                <!-- <button class="btn btn-warning btn-sm " @click = "setBeneStatus(i,index)" title="Set Beneficiary Payment Status"><i class="fa fa-money">Payment</i></button> -->
                                                <button class="btn btn-primary btn-sm " @click = "saveBeneDetails(i)" title="Save Details"><i class="fa fa-save">Save</i></button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template v-if="payrollList.length == 0">
                                        <tr>
                                            <td colspan="11" class="text-center">No Data Available.</td>
                                        </tr>
                                    </template>
                                </template>

                            </tbody>
                        </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="batchPayment" tabindex="-1" role="dialog" aria-labelledby="batchPayment" aria-hidden="true">
        <div class="modal-dialog" role="document"style="width:700px;">
            <div class="modal-content">                
                <form v-on:submit.prevent="batchPayment">
                    <div class="modal-header">
                        <h5 class="modal-title">Batch Payment</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <h5 style="text-align:center"> You are about to set payment of {{selected.length}} Beneficiaries to {{liquidationText}}</h5>
                            <div class="col-md-12" style="background-color:red; padding:3%; margin:auto; font-size:10pt;"> 
                                <label for="form-control"></label>
                                <b>IMPORTANT:</b> Before proceeding with Batch Payment, please
                                make sure you have marked <b>checked</b> from the list all beneficiaries
                                that were <b>actually {{liquidationText}} - {{search.liquidation}}</b>.
                            </div>
                        </div>
                        <hr>
                            <h6 style="text-align:center"> BATCH  PAYMENT SETTINGS </h6>
                            <h5 style="text-align:center">{{getmunname(search.prov_code, search.mun_code)}} {{getprovname(search.prov_code) }}
                            <span>({{getperiodname(search.period)}} {{search.year}}) <span>
                            </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="amount">Amount</label>
                                <input class="form-control" type="text" name ="amount" v-model="batch.amount" required></td>
                            </div>
                            <div class="col-md-6">
                                <label for="date_receive">Date Received</label>
                                <input class="form-control" type="date" name ="date_receive"  v-model="batch.date_receive"  required></td>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12" style="color:blue; font-size:7pt;"> 
                                <label for="form-control"></label>
                                <b>Notes:</b>
                                <ul>
                                    <li>Amount and Date Received will be applied to all selected beneficiaries. Receiver will be automatically set as the beneficiary's name. 
                                    If you wish to change details of individual beneficiary. Edit the table and click save under action.
                                    </li>
                                </ul>
                                <ul>
                                    <li>If you have tagged an actual "UNPAID" pensioner as "PAID" or vice versa ("PAID" as "UNPAID") and proceeded to Batch Payment, you can revert the payment (after the Batch Payment is done) through the following steps:
                                        <ol>
                                            <li>Go to Masterlist module > Search for the SPID or name,</li>
                                            <li>Click payment button of details row,</li>
                                            <li>Mark the Payment as Paid or Unpaid, whichever is applicable,</li>
                                            <li>Then Click Submit.</li>
                                        </ol>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" id="batchpayment_start" value="Start Payment">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Eligibility Status modal -->
    <div class="modal fade" id="importPaymentModal" tabindex="-1" role="dialog" aria-labelledby="masterlistData" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">                
                <div class="modal-header">
                    <h5 class="modal-title">IMPORT PAYMENT STATUS</h5>                        
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <ul>
                                <li>Save your excel as .csv (Comma Delimited) file before uploading.</li>
                            <ul>
                        </div>
                        <div class="col-md-8 col-md-offset-2  text-center">
                            <template>
                                <input class="btn btn-info btn-block"  type="file" id="payment_file" ref="payment_file" v-on:change="paymentFileUpload()" required accept=".csv"/>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" v-on:click="submitUpdatePaymentFile()">Submit</button>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
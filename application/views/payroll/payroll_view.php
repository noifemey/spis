<style>
    th, td {
        border: 1px solid black;
        font-size: 12px;
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
    .col-sm-12, .col-sm-6, .col-sm-4, .col-sm-3, .form-group, .label-floating {
        margin: 0% !important;
        padding: 0.5% !important;
    }
</style>

<div id = "p_index">
    <div class="row">
        <div class="col-md-12 ">
            <br> <h3>Payroll </h3><br>
        <div>
    </div>
    <div class="card">
        <div class="card-body">
            <form v-on:submit.prevent="getallPayroll">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="row" >
                            <div class="col-md-6 "> 
                                <label for="Province">Province</label>
                                <select class="form-control p-0"   @change = "getLocation('mun_code',search.prov_code)" v-model = "search.prov_code" name="Province" required>
                                    <template v-for = "(list,index) in location.provinces">
                                    <option :value="list.prov_code">{{list.prov_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-6 "> 
                                <label for="Municipality">Municipality</label>
                                <select class="form-control p-0" v-model = "search.mun_code" :disabled = "location.municipalities.length <=0" name="Municipality" >
                                    <template v-for = "(list,index) in location.municipalities">
                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class = "row">
                            <div class="col-md-4 ">                                 
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
                            <div class="col-md-4 "> 
                                <label for="Year">Period</label>
                                <select class="form-control p-0"   v-model = "search.period">
                                    <option value="">Select Period</option>
                                    <option value="5">1st Semester</option>
                                    <option value="6">2nd Semester</option>
                                    <option value="1">1st Quarter</option>
                                    <option value="2">2nd Quarter</option>
                                    <option value="3">3rd Quarter</option>
                                    <option value="4">4th Quarter</option>
                                </select>
                            </div>
                            <div class="col-md-4 "> 
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
                        <div class="row justify-content-center " >
                            <br> <h4 style="text-align:center"> Cash Assistance Payroll </h4><br>
                        </div>
                        <div class="row">
                            <div class="col-md-6 "> 
                                <button type="submit" class="btn btn-warning btn-lg btn-block"  @click = "exportCE()">
                                <i class="fa fa-download"></i> Export Certificate of Eligibility</button>
                            </div>
                            <div class="col-md-6 "> 
                                <button type="submit" class="btn btn-warning btn-lg btn-block" @click = "generateCAP()">
                                <i class="fa fa-download"></i> Generate Cash Assistance Payroll</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row justify-content-center ">
                            <br> <h4 style="text-align:center"> UNPAID Cash Assistance Payroll </h4><br>
                        </div>
                        <div class="row">
                            <div class="col-md-6 "> 
                                <button type="submit" class="btn btn-primary btn-lg btn-block"  @click = "exportUnpaidCE()">
                                <i class="fa fa-download"></i> Export <strong>UNPAIDS</strong> Certificate of Eligibility</button>
                            </div>
                            <div class="col-md-6 "> 
                                <button type="submit" class="btn btn-primary btn-lg btn-block" @click = "generateCAPUnpaid()">
                                <i class="fa fa-download"></i> Generate <strong>UNPAIDS</strong> Cash Assistance Payroll</button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
    </div>

    <div class = "row">
        
    </div>

    <div class="card">
        <div class="row">
            <div class="col-12">
                <div class="card-header">
                    <!-- <button class="btn btn-success" data-toggle="modal" data-target="#export_Active">
                        <i class="fa fa-download"></i> Download List
                    </button> -->

                    <div class="row">
                        <div class="col-md-6 "> 
                            <h5>Generated Payroll</h5>
                            <!-- <span>{{getperiodname(search.period)}} {{search.year}} <span> -->
                        </div>
                        <!-- <div class="col-md-2 "> 
                            <h6>Total Target: {{total_target}}</h6>
                        </div>
                        <div class="col-md-2 "> 
                            <h6>Total PAID: {{total_paid}}</h6>
                        </div>
                        <div class="col-md-2 "> 
                            <h6>Total UNPAID: {{total_unpaid}}</h6>
                        </div> -->
                    </div>
                </div>

                <div class="card-body">
                    <!-- <div class="row">
                        <div class="col-md-5">
                            <input class="form-control" id="myInput" type="text" placeholder="Search Name" v-on:keyup="keymonitor" v-model="tblFilter">
                        </div>
                        <div class="col-md-7">
                            <template v-if="selected.length > 0">
                                <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#batchPayment" >
                                    <i class="fa fa-minus"></i> Set Checked List to {{liquidationText}}
                                </button>
                            </template>
                        </div>
                    </div> <hr>
                    <template v-if="selected.length > 0"> <h6>Selected : {{selected.length}}<h6></template>
                    <div class="row">
                        <div class="col-md-12">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <label class="form-checkbox">
                                            <input type="checkbox" v-model="selectAll" @click="select">
                                            <i class="form-icon"></i>
                                        </label>
                                    </th>
                                    <th>No</th>
                                    <th>SPID</th>
                                    <th>Full Name</th>
                                    <th>Amount</th>
                                    <th>Receiver</th>
                                    <th>Date Receive</th>
                                    <th>Action</th>
                                    <th>Barangay</th>
                                </tr>
                            </thead>
                            <tbody id="myTable">
                                <template v-if="loading">
                                    <tr >
                                        <td colspan="9" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <template v-if="payrollList.length != 0">
                                        <tr v-for="(i, index) in payrollList">
                                            <td>
                                                <label class="form-checkbox">
                                                    <input type="checkbox" :value="i.spid" v-model="selected">
                                                    <i class="form-icon"></i>
                                                </label>
                                            </td>
                                            <td>{{index + 1}}</td>
                                            <td>{{i.spid}}</td>
                                            <template v-if="i.spstatus == 'ForReplacement'">
                                                <td style="background-color:#fa0000" > {{i.fullname}} * </td>
                                            </template>
                                            <template v-else> <td>{{i.fullname}}</td></template>
                                            <td> <input class="form-control" type="text" v-model="i.amount"></td>
                                            <td> <input class="form-control" type="text" v-model="i.receiver"></td>
                                            <td> <input class="form-control" type="text" v-model="i.date_receive"></td>
                                            <td> <button class="btn btn-warning btn-sm " @click = "setBeneStatus(i,index)" title="Set Beneficiary Payment Status"><i class="fa fa-money">Payment</i></button>
                                                <button class="btn btn-primary btn-sm " @click = "saveBeneDetails(i)" title="Save Details"><i class="fa fa-save">Save</i></button>
                                            </td>
                                            <td> {{i.bar_name}}</td>
                                        </tr>
                                    </template>
                                    <template v-if="payrollList.length == 0">
                                        <tr>
                                            <td colspan="9" class="text-center">No Data Available.</td>
                                        </tr>
                                    </template>
                                </template>

                            </tbody>
                        </table>

                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    


    <!-- <div class="modal fade" id="batchPayment" tabindex="-1" role="dialog" aria-labelledby="batchPayment" aria-hidden="true">
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
    </div> -->
</div>
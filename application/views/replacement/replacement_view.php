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
    /* .form-control{
        background-color: #ffffff !important;
        border: 1px solid black !important;
    } */
    .col-sm-12, .col-sm-6, .col-sm-4, .col-sm-3, .form-group, .label-floating {
        margin: 0% !important;
        padding: 0.5% !important;
    }
</style>

<div id = "rep_index">
    <div class="row">
        <div class="col-md-12 ">
            <br> <h3> Replacements </h3><br>
        <div>
    </div>
    <div class="card">
        <div class="card-body">
            <form v-on:submit.prevent="getallPayroll">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="row" >
                            <div class="col-md-2 "> 
                                <label for="Province">Province</label>
                                <select class="form-control p-0"   @change = "getLocation('mun_code',search.prov_code)" v-model = "search.prov_code" name="Province" required>
                                    <template v-for = "(list,index) in location.provinces">
                                    <option :value="list.prov_code">{{list.prov_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-2 "> 
                                <label for="Municipality">Municipality</label>
                                <select class="form-control p-0"   @change = "getLocation('bar_code',search.mun_code)" v-model = "search.mun_code" :disabled = "location.municipalities.length <=0" name="Municipality" >
                                    <template v-for = "(list,index) in location.municipalities">
                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-2 "> 
                                <label for="Barangay">Barangay</label>
                                <select class="form-control p-0"   v-model = "search.bar_code" :disabled = "location.barangays.length <=0" name="Barangay">
                                    <template v-for = "(list,index) in location.barangays">
                                        <option :value="list.bar_code">{{list.bar_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-2 ">                                 
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
                            <div class="col-md-2 "> 
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
                            <!-- <div class="col-md-2 "> 
                                <label for="Claimant">Claimant</label>
                                <select class="form-control p-0"   v-model = "search.claimant" required>
                                    <option value="0">Replacers Only</option>
                                    <option value="1">Former and/or Replacer</option>
                                </select>
                            </div> -->
                        </div>   
                        <hr>
                        <div class="row">
                            <div class="col-md-4 "> 
                                <button type="submit" class="btn btn-info btn-block">
                                <i class="fa fa-search"></i> SEARCH </button>
                            </div>
                            <div class="col-md-4"> 
                                <button @click="generateCertificate()" class="btn btn-danger btn-block">
                                <i class="fa fa-download"></i> GENERATE CERTIFICATION</button>
                            </div>
                            <div class="col-md-4"> 
                                <button @click="exportReplacment()" class="btn btn-warning btn-block">
                                <i class="fa fa-download"></i> EXPORT REPLACEMENTS </button>
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
                    <!-- <button class="btn btn-success" data-toggle="modal" data-target="#export_Active">
                        <i class="fa fa-download"></i> Download List
                    </button> -->

                    <div class="row">
                        <div class="col-md-6 "> 
                            <h5>{{getmunname(search.prov_code, search.mun_code)}} {{getprovname(search.prov_code) }} {{getPaymentStatus()}}</h5>
                            <span>{{getperiodname(search.period)}} {{search.year}} <span>
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
                    <div class="row">
                        <div class="col-md-5">
                            <input class="form-control" id="myInput" type="text" placeholder="Search Name" v-on:keyup="keymonitor" v-model="tblFilter">
                        </div>
                        <div class="col-md-7">
                            <template v-if="selected.length > 0">
                                <button class="btn btn-danger btn-block" @click = "bulkreplace()" title="Replace Checked List" >
                                    <i class="fa fa-plus"></i> Replace Checked List
                                </button>
                            </template>

                            <template v-if="selectedTransfer.length > 0">
                                <button class="btn btn-danger btn-block" @click = "bulkTransfer()" title="Transfer Checked List" >
                                    <i class="fa fa-plus"></i> Transfer Checked List
                                </button>
                            </template>
                        </div>
                    </div> 
                    <hr>
                    <template v-if="selected.length > 0"> <h6>Selected for replacement : {{selected.length}}<h6></template>
                    <template v-if="selectedTransfer.length > 0"> <h6>Selected for Transfer : {{selectedTransfer.length}}<h6></template>

                    <ul class="nav nav-tabs mb-2" id="myTab" role="tablist">
                        <li class="nav-item">
                        <a class="nav-link active" id="for-vali-tab" data-toggle="tab" href="#replacement" role="tab" aria-controls="replacement" aria-selected="true">UNPAID FOR REPLACEMENT</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" id="dup-tab" data-toggle="tab" href="#for-vali" role="tab" aria-controls="for-vali" aria-selected="false">AVAILABLE WAITLIST</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="replacement" role="tabpanel" aria-labelledby="replacement-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <h3> UNPAID FORREPLACEMENTS </h3>
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
                                                <th>SP Status</th>
                                                <th>Adress</th>
                                                <th>Birthdate</th>
                                                <th>Reason</th>
                                                <th>Replacer SPID: FullName</th>
                                                <th>Replacer Birthdate</th>
                                                <th>Replacer Age</th>
                                                <th>Replacer Adress</th>
                                                <th>Claimant</th>
                                                <th>
                                                    <label class="form-checkbox"> Bulk Transfer
                                                        <input type="checkbox" v-model="selectAllTransfer" @click="selectTransfer">
                                                        <i class="form-icon"></i>
                                                    </label>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="myTable">
                                            <template v-if="loading">
                                                <tr >
                                                    <td colspan="14" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <template v-if="payrollList.length != 0">
                                                    <tr v-for="(i, index) in payrollList">
                                                        <td>
                                                            <template v-if="i.spstatus == 'ForReplacement' && i.replacer_refcode != ''">
                                                                <label class="form-checkbox">
                                                                    <input type="checkbox" :value="i.spid" v-model="selected">
                                                                    <i class="form-icon"></i>
                                                                </label>
                                                            </template>
                                                        </td>
                                                        <td>{{index + 1}}</td>
                                                        <td>{{i.spid}}</td>
                                                        <td>{{i.fullname}}</td>
                                                        <td>{{i.spstatus}}</td>
                                                        <td>{{i.adress}}</td>
                                                        <td>{{i.birthdate}}</td>
                                                        <td>{{i.reason}}</td>
                                                        <td>{{i.replacer_name}}</td>
                                                        <td>{{i.replacer_birthdate}}</td>
                                                        <td>{{i.replacer_age}}</td>
                                                        <td>{{i.replacer_adress}}</td>                                            
                                                        <td> 
                                                            <template v-if="i.spstatus == 'ForReplacement' && i.replacer_refcode != ''">
                                                                <button class="btn btn-warning btn-sm " @click = "replace(i,index)" title="Replace"><i class="fa fa-money">Replace</i></button>
                                                            </template>
                                                            <template v-if="i.spstatus == 'Inactive'">
                                                                <button class="btn btn-danger btn-sm " @click = "transfer(i,index)" title="Transfer"><i class="fa fa-money">Transfer</i></button>
                                                                <!-- <template v-if="i.eligible == '0'">
                                                                    Eligible Claimant : REPLACER ONLY 
                                                                    <button class="btn btn-danger btn-sm " @click = "transfer(i,index)" title="Transfer"><i class="fa fa-money">Transfer</i></button>
                                                                </template>
                                                                <template v-if="i.eligible == '1'">
                                                                    Eligible Claimant : FORMER PENSIONER ONLY 
                                                                    <button class="btn btn-danger btn-sm " @click = "transfer(i,index)" title="Transfer"><i class="fa fa-money">Transfer</i></button>
                                                                </template>
                                                                <template v-if="i.eligible == '2'">
                                                                    Eligible Claimant : EITHER FORMER PENSIONER OR REPLACER 
                                                                    <button class="btn btn-danger btn-sm " @click = "transfer(i,index)" title="Transfer"><i class="fa fa-money">Transfer</i></button>
                                                                </template>
                                                                <template v-if="i.eligible == '3'">
                                                                    NO ELIGIBLE CLAIMANT
                                                                </template>
                                                                <template v-if="i.eligible == '4'">
                                                                    BARANGAY OFFICIAL ONHOLD
                                                                </template> -->
                                                            </template>
                                                            <template v-if="i.liquidation == '4'">
                                                                BARANGAY OFFICIAL ONHOLD
                                                            </template>
                                                        </td>
                                                        <td>
                                                            {{i.amount}}
                                                            <template v-if="i.spstatus == 'Inactive' && i.liquidation != 4">
                                                                <label class="form-checkbox">
                                                                    <input type="checkbox" :value="i.spid" v-model="selectedTransfer">
                                                                    <i class="form-icon"></i>
                                                                </label>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <template v-if="payrollList.length == 0">
                                                    <tr>
                                                        <td colspan="14" class="text-center">No Data Available.</td>
                                                    </tr>
                                                </template>
                                            </template>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="for-vali" role="tabpanel" aria-labelledby="for-vali-tab">
                            <div class="row">

                                <div class="col-md-12">
                                    <h3> AVAILABLE WAITLIST </h3>
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Reference Number</th>
                                                <th>Full Name</th>
                                                <th>Province</th>
                                                <th>Municipality</th>
                                                <th>Barangay</th>
                                                <th>Birthdate</th>
                                                <th>Age</th>
                                            </tr>
                                        </thead>
                                        <tbody id="myTable">
                                            <template v-if="loading">
                                                <tr >
                                                    <td colspan="9" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <template v-if="waitlist.length != 0">
                                                    <tr v-for="(i, index) in waitlist">
                                                        <td>{{index + 1}}</td>
                                                        <td>{{i.reference_code}}</td>
                                                        <td>{{i.fullname}}</td>
                                                        <td>{{getprovname(i.prov_code)}}</td>
                                                        <td>{{getmunname(i.prov_code,i.mun_code)}}</td>
                                                        <td>{{i.bar_name}}</td>
                                                        <td>{{i.birthdate}}</td>
                                                        <td>{{i.age}}</td>
                                                    </tr>
                                                </template>
                                                <template v-if="waitlist.length == 0">
                                                    <tr>
                                                        <td colspan="9" class="text-center">No Data Available.</td>
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
        </div>
    </div>
</div>
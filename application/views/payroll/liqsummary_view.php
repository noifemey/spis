
<div id = "summary_index">
    <div class="row">
        <div class="col-md-12 ">
            <br> <h3>Export Social Pension Payroll Liquidation Report </h3><br>
        <div>
    </div>
    <div class="card">
        <div class="card-body">
            <form v-on:submit.prevent="getallPayroll">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="row" >
                            <div class="col-md-3 "> 
                                <label for="Province">Province</label>
                                <select class="form-control p-0"   @change = "getLocation('mun_code',search.prov_code)" v-model = "search.prov_code" name="Province" required>
                                    <template v-for = "(list,index) in location.provinces">
                                    <option :value="list.prov_code">{{list.prov_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-3 "> 
                                <label for="Municipality">Municipality</label>
                                <select class="form-control p-0" v-model = "search.mun_code" :disabled = "location.municipalities.length <=0" name="Municipality" >
                                    <template v-for = "(list,index) in location.municipalities">
                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                    </template>
                                </select>
                            </div>
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
                        </div>
                        <br>
                        <div class="row" >
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
                            <div class="col-md-3 "> 
                                <label for="no_sp">No. of SP</label>
                                <input class="form-control" v-model = "search.no_sp" id="no_sp"/>
                            </div>
                            <div class="col-md-3 "> 
                                <label for="forrep_ex">Exclude Forreplacement</label>
                                <select class="form-control p-0" v-model = "search.forrep_ex" required>
                                    <option value="0">NO</option>
                                    <option value="1">YES</option>
                                </select>
                            </div>
                        </div>
                        <br><hr>
                        <div class="row" >
                            <br> <h4 style="text-align:center"> SIGNATORIES:  </h4><br>
                        </div>
                        <hr>
                        <div class="row justify-content-center " >
                            <br> <h5 style="text-align:center"> LIQUIDATION SUMMARY: </h5><br>
                        </div>
                        <br> 
                        <div class="row">
                            <div class="col-md-4"> 
                                <label for="">Claimant</label>
                                <input class="form-control" v-model = "search.input_claimant" id="input_claimant"/>
                            </div>
                            <div class="col-md-4"> 
                                <label for="">Immediate Supervisor</label>
                                <input class="form-control" v-model = "search.input_supervisor" id="input_supervisor"/>
                            </div>
                            <div class="col-md-4"> 
                                <label for="">Head, Accounting Division Unit</label>
                                <input class="form-control" v-model = "search.input_acctng" id="input_acctng"/>
                            </div>
                        </div>
                        <br> 
                        <div class="row justify-content-center " >
                            <h5 style="text-align:center"> REPORT OF CASH DISBURSEMENTS: </h5><br>
                        </div>
                        <br> 
                        <div class="row">
                            <div class="col-md-4"> 
                                <label for="">Name of Disbursing Officer/Cashier</label>
                                <input class="form-control" v-model = "search.input_disbursing" id="input_disbursing"/>
                            </div>
                            <div class="col-md-4"> 
                                <label for="">Position Disbursing Officer/Cashier</label>
                                <input class="form-control" v-model = "search.input_disbursingposi" id="input_disbursingposi"/>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 "> 
                                <button type="submit" class="btn btn-primary btn-lg btn-block" @click = "generatePayrollReports()">
                                <i class="fa fa-download"></i> DOWNLOAD</button>
                            </div>
                        </div>
                    </div>
                </div>
   
                
            </form>
        </div>
    </div>

</div>
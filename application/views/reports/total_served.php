<!-- <style>
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
    .form-control{
        background-color: #ffffff !important;
        border: 1px solid black !important;
    }
    .col-sm-12, .col-sm-6, .col-sm-4, .col-sm-3, .form-group, .label-floating {
        margin: 0% !important;
        padding: 0.5% !important;
    }
</style> -->

<div id="totalServed">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">SERVED BENEFICIARIES</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 ">
                    <form v-on:submit.prevent="searchList">
                        <div class="row align-items-end" >
                            <div class="col-md-4 "> 
                                <label for="Year">Year</label>
                                <select class="form-control p-0"   v-model = "search.year">
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
                            <div class="col-md-2"> 
                                <label for="Year">Month (as of)</label>
                                <select class="form-control p-0" v-model = "search.month">
                                    <option value="">Select Month</option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="col-md-2"> 
                                <button type="submit" class="btn btn-info btn-block" >
                                <i class="fa fa-search"></i> SEARCH</button>
                            </div>
                        </div>   
                    </form>
                </div>
            </div>

            <hr>
            <br>

            <template v-if = "!loading">
                <button type="button" class="btn btn-warning btn-block" @click = "sendData()"> <i class="fa fa-upload"></i> SEND DATA TO GOOGLE SHEET</button>
            </template>
            <br>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2"></th>
                        <th class="text-center" rowspan="2">TOTAL TARGET</th>
                        <th class="text-center" colspan="4">PAID BENEFICIARIES</th>
                        <th class="text-center" colspan="4">UNPAID BENEFICIARIES</th>
                        <th class="text-center" rowspan="2">Total Beneficiaries</th>
                        <th class="text-center" rowspan="2">% of Accomplishment <br> (AS TO TARGET)</th>
                        <th class="text-center" rowspan="2">% of Accomplishment <br> (AS TO ACTUAL BENEFICIARIES)</th>
                    </tr>
                    <tr>
                        <th class="text-center">PAID (Male)</th>
                        <th class="text-center">PAID (Female)</th>
                        <th class="text-center">Total PAID </th>
                        <th class="text-center">Total PAID Amount </th>
                        <th class="text-center">UNPAID (Male)</th>
                        <th class="text-center">UNPAID (Female)</th>
                        <th class="text-center">Total UNPAID </th>
                        <th class="text-center">Total UNPAID Amount </th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="14" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <template v-if="region_served.length != 0">
                        <tr>
                            <th><h3> CAR Total: </h3></th>
                            <td class="text-center"><h4>{{region_served.target}}</h4></td>
                            <td class="text-center"><h4>{{region_served.r_male}}</h4></td>
                            <td class="text-center"><h4>{{region_served.r_female}}</h4></td>
                            <td class="text-center"><h4>{{region_served.r_paidtotal}}</h4></td>
                            <td class="text-center"><h4>₱{{region_served.paidamount}}</h4></td>
                            <td class="text-center"><h4>{{region_served.r_unpaidmale}}</h4></td>
                            <td class="text-center"><h4>{{region_served.r_unpaidfemale}}</h4></td>
                            <td class="text-center"><h4>{{region_served.r_unpaidtotal}}</h4></td>
                            <td class="text-center"><h4>₱{{region_served.unpaidamount}}</h4></td>
                            <td class="text-center"><h4>{{region_served.total}}</h4></td>
                            <td class="text-center"><h4>{{getAccomplishment(region_served.r_paidtotal, region_served.target)}}</h4></td>
                            <td class="text-center"><h4>{{getAccomplishment(region_served.r_paidtotal, region_served.total)}}</h4></td>
                        </tr>
                        </template>
                        <template v-if="region_served.length == 0">
                            <tr>
                                <td colspan="14" class="text-center">No Data Available.</td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
            <br>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2"></th>
                        <th class="text-center" rowspan="2">Province</th>
                        <th class="text-center" rowspan="2">TOTAL TARGET</th>
                        <th class="text-center" colspan="4">PAID BENEFICIARIES</th>
                        <th class="text-center" colspan="4">UNPAID BENEFICIARIES</th>
                        <th class="text-center" rowspan="2">Total Beneficiaries</th>
                        <th class="text-center" rowspan="2">% of Accomplishment <br> (AS TO TARGET)</th>
                        <th class="text-center" rowspan="2">% of Accomplishment <br> (AS TO ACTUAL BENEFICIARIES)</th>
                    </tr>
                    <tr>
                        <th class="text-center">PAID (Male)</th>
                        <th class="text-center">PAID (Female)</th>
                        <th class="text-center">Total PAID </th>
                        <th class="text-center">Total PAID Amount </th>
                        <th class="text-center">UNPAID (Male)</th>
                        <th class="text-center">UNPAID (Female)</th>
                        <th class="text-center">Total UNPAID </th>
                        <th class="text-center">Total UNPAID Amount </th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="14" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <template v-if="served_data.length != 0" v-for="list,index in served_data">
                            <tr>
                                <td width="3%" @click="list.mun_show = !list.mun_show" ><i :class="list.mun_show?'fa fa-minus':'fa fa-plus'"></i></td> 
                                <th><h4>{{list.name}}</h4> </th>
                                <td class="text-center"><h4>{{list.target}}</h4></td>
                                <td class="text-center"><h4>{{list.male}}</h4></td>
                                <td class="text-center"><h4>{{list.female}}</h4></td>
                                <td class="text-center"><h4>{{list.paidtotal}}</h4></td>
                                <td class="text-center"><h4>₱{{list.amount}}</h4></td>
                                <td class="text-center"><h4>{{list.unpaidmale}}</h4></td>
                                <td class="text-center"><h4>{{list.unpaidfemale}}</h4></td>
                                <td class="text-center"><h4>{{list.unpaidtotal}}</h4></td>
                                <td class="text-center"><h4>₱{{list.unpaidamount}}</h4></td>
                                <td class="text-center"><h4>{{list.total}}</h4></td>
                                <td class="text-center"><h4>{{getAccomplishment(list.paidtotal, list.target)}}</h4></td>
                                <td class="text-center"><h4>{{getAccomplishment(list.paidtotal, list.total)}}</h4></td>
                            </tr>
                            <template v-if="list.mun_show" style="height:300px;overflow: auto;">
                                <template v-for="cl,i in list.children">
                                    <tr>
                                        <th></th>
                                        <th>{{cl.name}}</th>
                                        <td class="text-center">{{cl.target}}</td>
                                        <td class="text-center">{{cl.male}}</td>
                                        <td class="text-center">{{cl.female}}</td>
                                        <td class="text-center">{{cl.paidtotal}}</td>
                                        <td class="text-center">₱{{cl.amount}}</td>
                                        <td class="text-center">{{cl.unpaidmale}}</td>
                                        <td class="text-center">{{cl.unpaidfemale}}</td>
                                        <td class="text-center">{{cl.unpaidtotal}}</td>
                                        <td class="text-center">₱{{cl.unpaidamount}}</td>
                                        <td class="text-center">{{cl.total}}</td>
                                        <td class="text-center">{{getAccomplishment(cl.paidtotal, cl.target)}}</td>
                                        <td class="text-center">{{getAccomplishment(cl.paidtotal, cl.total)}}</td>
                                    </tr>
                                </template>
                                
                                <tr >
                                    <td colspan="14" class="text-center">----> END <----</td>
                                </tr>

                            </template>
                        </template>
                        <template v-if="served_data.length == 0">
                            <tr>
                                <td colspan="14" class="text-center">No Data Available.</td>
                            </tr>
                        </template>
                        
                    </template>
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
        </div>
    </div>
</div>
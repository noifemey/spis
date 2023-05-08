<div id="inactiveReport">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Inactive Beneficiaries Report</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 ">
                    <form v-on:submit.prevent="getTotalInactive">
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
                                    <option value="1">1st Semester</option>
                                    <option value="2">2nd Semester</option>
                                </select>
                            </div>
                            <div class="col-md-2"> 
                                <label for="Year">Month</label>
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
            <br>
            <table class="table table-striped table-bordered table-sm">
                <thead>
                    <tr>
                        <th class="text-center"></th>
                        <template v-for="r,i in reasons">
                            <th>{{r}}</th>
                        </template>
                        <th class="text-center">Total Inactive</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="21" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <template v-if="region_inactive.length != 0">
                        <tr>
                            <th><h3> CAR Total: </h3></th>
                            <template v-for="r,i in reasons">
                                <td class="text-center"><h4>{{getNumberFormat(region_inactive[r])}}</h4></td>
                            </template>
                            <td class="text-center"><h4>{{getNumberFormat(region_inactive.total)}}</h4></td>
                        </tr>
                        </template>
                        <template v-if="region_inactive.length == 0">
                            <tr>
                                <td colspan="21" class="text-center">No Data Available.</td>
                            </tr>
                        </template>
                    </template> 
                </tbody>
            </table>
            <br>

            <table class="table table-striped table-bordered table-sm">
                <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">Province</th>
                        <template v-for="r,i in reasons">
                            <th>{{r}}</th>
                        </template>
                        <th class="text-center">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="21" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <template v-if="inactive_data.length != 0" v-for="list,index in inactive_data">
                            <tr>
                                <td width="3%" @click="list.show_child = !list.show_child" ><i :class="list.show_child?'fa fa-minus':'fa fa-plus'"></i></td> 
                                <th><h4>{{list.name}}</h4> </th>
                                <template v-for="r,i in reasons">
                                    <td><h4>{{getNumberFormat(list[r])}}</h4></td>
                                </template>
                                <th class="text-center"><h4>{{getNumberFormat(list.total)}}</h4></th>
                            </tr>
                            <template v-if="list.show_child" style="height:300px;overflow: auto;">
                                <template v-for="cl,i in list.children">
                                    <tr>
                                        <th></th>
                                        <th>{{cl.name}}</th>
                                        <template v-for="r,i in reasons">
                                            <td>{{getNumberFormat(cl[r])}}</td>
                                        </template>
                                        <th class="text-center">{{getNumberFormat(cl.total)}}</th>
                                    </tr>
                                </template>
                                
                                <tr >
                                    <td colspan="21" class="text-center">----> END <----</td>
                                </tr>

                            </template>
                        </template>
                        <template v-if="inactive_data.length == 0">
                            <tr>
                                <td colspan="21" class="text-center">No Data Available.</td>
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
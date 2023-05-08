
<div id="totalUnclaimed">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">UNPAID BENEFICIARIES</h3>
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
                            <div class="col-md-4 "> 
                                <button type="submit" class="btn btn-info btn-block" >
                                <i class="fa fa-search"></i> SEARCH</button>
                            </div>
                        </div>   
                    </form>
                </div>
            </div>

            <hr>
            <br>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-center">Total Target Benefeciaries </th>
                        <th class="text-center">Total Unpaid (Male)</th>
                        <th class="text-center">Total Unpaid (Female)</th>
                        <th class="text-center">Total Unpaid </th>
                        <th class="text-center">Total Amount </th>
                        <th class="text-center">% of Unpaid </th>
                        <th class="text-center">Onhold </th>
                        <th class="text-center">Offset </th>
                        <th class="text-center">Unreleased </th>
                        <th class="text-center">For replacement </th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="11" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <template v-if="region_unclaimed.length != 0">
                        <tr>
                            <th><h3> CAR Total: </h3></th>
                            <td class="text-center"><h4>{{region_unclaimed.target}}</h4></td>
                            <td class="text-center"><h4>{{region_unclaimed.r_male}}</h4></td>
                            <td class="text-center"><h4>{{region_unclaimed.r_female}}</h4></td>
                            <td class="text-center"><h4>{{region_unclaimed.total}}</h4></td>
                            <td class="text-center"><h4>₱{{region_unclaimed.amount}}</h4></td>
                            <td class="text-center"><h4>{{region_unclaimed.accomplishment}} %</h4></td>
                            <td class="text-center"><h4>{{region_unclaimed.r_onhold}}</h4></td>
                            <td class="text-center"><h4>{{region_unclaimed.r_offset}}</h4></td>
                            <td class="text-center"><h4>{{region_unclaimed.r_unreleased}}</h4></td>
                            <td class="text-center"><h4>{{region_unclaimed.r_forrep}}</h4></td>
                        </tr>
                        </template>
                        <template v-if="region_unclaimed.length == 0">
                            <tr>
                                <td colspan="11" class="text-center">No Data Available.</td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
            <br>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Province</th>
                        <th class="text-center">Total Target Benefeciaries </th>
                        <th class="text-center">Total Unpaid (Male)</th>
                        <th class="text-center">Total Unpaid (Female)</th>
                        <th class="text-center">Total Unpaid </th>
                        <th class="text-center">Total Amount </th>
                        <th class="text-center">% of Unpaid </th>
                        <th class="text-center">Onhold </th>
                        <th class="text-center">Offset </th>
                        <th class="text-center">Unreleased </th>
                        <th class="text-center">For replacement </th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="11" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <template v-if="unclaimed_data.length != 0" v-for="list,index in unclaimed_data">

                            <tr>
                                <th><h4><button type="button" class="btn btn-xs btn-primary" @click="list.mun_show = !list.mun_show">Expand</button> {{list.name}}</h4> </th>
                                <td class="text-center"><h4>{{list.target}}</h4></td>
                                <td class="text-center"><h4>{{list.male}}</h4></td>
                                <td class="text-center"><h4>{{list.female}}</h4></td>
                                <td class="text-center"><h4>{{list.total}}</h4></td>
                                <td class="text-center"><h4>₱{{list.amount}}</h4></td>
                                <td class="text-center"><h4>{{getAccomplishment(list.total, list.target)}}</h4></td>

                                <td class="text-center"><h4>{{list.onhold}}</h4></td>
                                <td class="text-center"><h4>{{list.offset}}</h4></td>
                                <td class="text-center"><h4>{{list.unreleased}}</h4></td>
                                <td class="text-center"><h4>{{list.forreplacement}}</h4></td>
                            </tr>
                            <tr v-if="list.mun_show">
                                <td colspan="11">
                                    <div class="row" style="height:500px;overflow: auto;">
                                        <div class="col-md-12">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Municipality</th>
                                                        <th class="text-center">Total Target Benefeciaries </th>
                                                        <th class="text-center">Total Unpaid (Male)</th>
                                                        <th class="text-center">Total Unpaid (Female)</th>
                                                        <th class="text-center">Total Unpaid </th>
                                                        <th class="text-center">Total Amount </th>
                                                        <th class="text-center">% of Unpaid </th>

                                                        <th class="text-center">Onhold </th>
                                                        <th class="text-center">Offset </th>
                                                        <th class="text-center">Unreleased </th>
                                                        <th class="text-center">For replacement </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template v-for="cl,i in list.children">
                                                        <tr>
                                                            <th>{{cl.name}}</th>
                                                            <td class="text-center">{{cl.target}}</td>
                                                            <td class="text-center">{{cl.male}}</td>
                                                            <td class="text-center">{{cl.female}}</td>
                                                            <td class="text-center">{{cl.total}}</td>
                                                            <td class="text-center">₱{{cl.amount}}</td>
                                                            <td class="text-center">{{getAccomplishment(cl.total, cl.target)}}</td>

                                                            <td class="text-center"><h4>{{cl.onhold}}</h4></td>
                                                            <td class="text-center"><h4>{{cl.offset}}</h4></td>
                                                            <td class="text-center"><h4>{{cl.unreleased}}</h4></td>
                                                            <td class="text-center"><h4>{{cl.forreplacement}}</h4></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>    
                            </tr>
                        </template>
                        <template v-if="unclaimed_data.length == 0">
                            <tr>
                                <td colspan="11" class="text-center">No Data Available.</td>
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
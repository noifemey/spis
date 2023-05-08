
<div id="activeBreakdown">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">ACTIVE BREAKDOWN</h3>
        </div>
        <div class="card-body">

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2" ></th>
                        <th colspan="3" class="text-center" >ACTIVE</th>
                        <th colspan="3" class="text-center" >FOR REPLACEMENT</th>
                        <th rowspan="2" class="text-center">Total Social Pension Beneficiaries</th>
                    </tr>
                    <tr>
                        <th class="text-center">Male</th>
                        <th class="text-center">Female</th>
                        <th class="text-center">Total Active</th>
                        <th class="text-center">Male</th>
                        <th class="text-center">Female</th>
                        <th class="text-center">Total</th>
                        <!-- <th class="text-center">Total Social Pension Beneficiaries</th> -->
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="8" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <th><h3> GRAND Total: </h3></th>
                            <td class="text-center"><h4>{{total_active.active_male}}</h4></td>
                            <td class="text-center"><h4>{{total_active.active_female}}</h4></td>
                            <td class="text-center"><h4>{{total_active.total_active}}</h4></td>
                            <td class="text-center"><h4>{{total_active.forrep_male}}</h4></td>
                            <td class="text-center"><h4>{{total_active.forrep_female}}</h4></td>
                            <td class="text-center"><h4>{{total_active.total_forrep}}</h4></td>
                            <td class="text-center"><h4>{{total_active.total}}</h4></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <br>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2" >Province </th>
                        <th colspan="3" class="text-center" >ACTIVE</th>
                        <th colspan="3" class="text-center" >FOR REPLACEMENT</th>
                        <th rowspan="2" class="text-center">Total Social Pension Beneficiaries</th>
                    </tr>
                    <tr>
                        <th class="text-center">Male</th>
                        <th class="text-center">Female</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Male</th>
                        <th class="text-center">Female</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="8" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <template v-if="active_data.length != 0" v-for="list,index in active_data">

                            <tr>
                                <th><h4><button type="button" class="btn btn-xs btn-primary" @click="list.mun_show = !list.mun_show">Expand</button> {{list.name}}</h4> </th>
                                <td class="text-center"><h4>{{list.active_male}}</h4></td>
                                <td class="text-center"><h4>{{list.active_female}}</h4></td>
                                <td class="text-center"><h4>{{list.total_active}}</h4></td>
                                <td class="text-center"><h4>{{list.forrep_male}}</h4></td>
                                <td class="text-center"><h4>{{list.forrep_female}}</h4></td>
                                <td class="text-center"><h4>{{list.total_forrep}}</h4></td>
                                <td class="text-center"><h4>{{list.total}}</h4></td>
                            </tr>
                            <tr v-if="list.mun_show">
                                <td colspan="8">
                                    <div class="row" style="height:500px;overflow: auto;">
                                        <div class="col-md-12">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2" >Municipality</th>
                                                        <th colspan="3" class="text-center" >ACTIVE</th>
                                                        <th colspan="3" class="text-center" >FOR REPLACEMENT</th>
                                                        <th rowspan="2" class="text-center">Total Social Pension Beneficiaries</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">Male</th>
                                                        <th class="text-center">Female</th>
                                                        <th class="text-center">Total</th>
                                                        <th class="text-center">Male</th>
                                                        <th class="text-center">Female</th>
                                                        <th class="text-center">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template v-for="cl,i in list.municipality">
                                                        <tr>
                                                            <th>{{cl.name}}</th>                                
                                                            <td class="text-center"><h4>{{cl.active_male}}</h4></td>
                                                            <td class="text-center"><h4>{{cl.active_female}}</h4></td>
                                                            <td class="text-center"><h4>{{cl.total_active}}</h4></td>
                                                            <td class="text-center"><h4>{{cl.forrep_male}}</h4></td>
                                                            <td class="text-center"><h4>{{cl.forrep_female}}</h4></td>
                                                            <td class="text-center"><h4>{{cl.total_forrep}}</h4></td>
                                                            <td class="text-center"><h4>{{cl.total}}</h4></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>    
                            </tr>
                        </template>
                        <template v-if="active_data.length == 0">
                            <tr>
                                <td colspan="8" class="text-center">No Data Available.</td>
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
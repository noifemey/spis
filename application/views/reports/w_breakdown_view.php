
<div id="waitlistBreakdown">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">WAITLIST BREAKDOWN</h3>
        </div>
        <div class="card-body">

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-center">Eligible Waitlist</th>
                        <th class="text-center">Not Eligible Waitlist</th>
                        <th class="text-center">Waiting for Eligibility</th>
                        <th class="text-center">For Sending to C.O</th>
                        <th class="text-center">Total Waitlist</th>
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
                            <th><h3> GRAND Total: </h3></th>
                            <td class="text-center"><h4>{{total_waitlist.eligible}}</h4></td>
                            <td class="text-center"><h4>{{total_waitlist.not_eligible}}</h4></td>
                            <td class="text-center"><h4>{{total_waitlist.wfe}}</h4></td>
                            <td class="text-center"><h4>{{total_waitlist.fstoco}}</h4></td>
                            <td class="text-center"><h4>{{total_waitlist.total}}</h4></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <br>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Province</th>
                        <th class="text-center">Eligible Waitlist</th>
                        <th class="text-center">Not Eligible Waitlist</th>
                        <th class="text-center">Waiting for Eligibility</th>
                        <th class="text-center">For Sending to C.O</th>
                        <th class="text-center">Total Waitlist</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="6" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <template v-if="waitlist_data.length != 0" v-for="list,index in waitlist_data">

                            <tr>
                                <th><h4><button type="button" class="btn btn-xs btn-primary" @click="list.mun_show = !list.mun_show">Expand</button> {{list.name}}</h4> </th>
                                <td class="text-center"><h4>{{list.eligible}}</h4></td>
                                <td class="text-center"><h4>{{list.not_eligible}}</h4></td>
                                <td class="text-center"><h4>{{list.wfe}}</h4></td>
                                <td class="text-center"><h4>{{list.fstoco}}</h4></td>
                                <td class="text-center"><h4>{{list.total}}</h4></td>
                            </tr>
                            <tr v-if="list.mun_show">
                                <td colspan="6">
                                    <div class="row" style="height:500px;overflow: auto;">
                                        <div class="col-md-12">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Municipality</th>
                                                        <th class="text-center">Eligible Waitlist</th>
                                                        <th class="text-center">Not Eligible Waitlist (For Revalidation)</th>
                                                        <th class="text-center">Waiting for Eligibility</th>
                                                        <th class="text-center">For Sending to C.O</th>
                                                        <th class="text-center">Total Waitlist</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template v-for="cl,i in list.municipality">
                                                        <tr>
                                                            <th>{{cl.name}}</th>
                                                            <td class="text-center">{{cl.eligible}}</td>
                                                            <td class="text-center">{{cl.not_eligible}}</td>
                                                            <td class="text-center">{{cl.wfe}}</td>
                                                            <td class="text-center">{{cl.fstoco}}</td>
                                                            <td class="text-center">{{cl.total}}</td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>    
                            </tr>
                        </template>
                        <template v-if="waitlist_data.length == 0">
                            <tr>
                                <td colspan="5" class="text-center">No Data Available.</td>
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
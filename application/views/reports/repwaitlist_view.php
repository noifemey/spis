<div id="repwaitlist_view">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">REPLACEMENT AND WAITLIST MONITORING</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-center">PROVINCE</th>
                        <th class="text-center">Target</th>
                        <th class="text-center">Active</th>
                        <th class="text-center">For Replacement</th>
                        <th class="text-center">Total Current Beneficiaries</th>
                        <th class="text-center">Eligible Waitlist</th>
                        <th class="text-center">Variance (Eligibile Waitlist - For Replacement)</th>
                        <th class="text-center">Total Actual Slot (Active + Additional)</th>
                        <th class="text-center">Variance (Target - Actual)</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr >
                            <td colspan="10" class="text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></td>
                        </tr>
                    </template>
                    <template v-else>
                        <template v-if="prov_data.length != 0" v-for="list,index in prov_data">
                            <tr>
                                <td width="3%" @click="list['mun_show'] = !list['mun_show']" ><i :class="list['mun_show']?'fa fa-minus':'fa fa-plus'"></i></td> 
                                <td>{{list["name"]}}</td> 
                                <td class="text-center">{{getNumberFormat(list["target"])}}</td>
                                <td class="text-center">{{getNumberFormat(list["active"])}}</td>
                                <td class="text-center">{{getNumberFormat(list["forReplacement"])}}</td>
                                <td class="text-center">{{getNumberFormat(list["current_benes"])}}</td>
                                <td class="text-center">{{getNumberFormat(list["eligible_waitlist"])}}</td>
                                <td class="text-center">{{list["eligible_waitlist"] - list["forReplacement"]}}</td>
                                <td class="text-center"> {{getactual(list["eligible_waitlist"], list["forReplacement"] , list["current_benes"])}}</td>
                                <td class="text-center">{{gettargetVariance(list["target"],list["eligible_waitlist"], list["forReplacement"] , list["current_benes"])}}</td>
                            </tr>
                            <template v-if="list['mun_show']" style="height:300px;overflow: auto;">
                                <template v-for="cl,i in list.child">
                                    <tr>
                                        <td width="3%"></td> 
                                        <td>{{cl["name"]}}</td> 
                                        <td class="text-center">{{getNumberFormat(cl["target"])}}</td>
                                        <td class="text-center">{{getNumberFormat(cl["active"])}}</td>
                                        <td class="text-center">{{getNumberFormat(cl["forReplacement"])}}</td>
                                        <td class="text-center">{{getNumberFormat(cl["current_benes"])}}</td>
                                        <td class="text-center">{{getNumberFormat(cl["eligible_waitlist"])}}</td>
                                        <td class="text-center">{{cl["eligible_waitlist"] - cl["forReplacement"]}}</td>
                                        <td class="text-center">{{getactual(cl["eligible_waitlist"], cl["forReplacement"] , cl["current_benes"])}}</td>
                                        <td class="text-center">{{gettargetVariance(cl["target"],list["eligible_waitlist"], list["forReplacement"] , list["current_benes"])}}</td>
                                    </tr>
                                </template>
                                <tr >
                                    <td colspan="10" class="text-center">----> END <----</td>
                                </tr>
                            </template>
                        </template>
                        <template v-if="prov_data.length == 0">
                            <tr>
                                <td colspan="10" class="text-center">No Data Available.</td>
                            </tr>
                        </template>
                        
                    </template>
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            <h3> Legends: </h3>
            <ul>
                <li><strong>Target          : </strong>Assigned target for the current year per municipalities</li>
                <li><strong>Active          : </strong>Current Active Social Pension Beneficiaries</li>
                <li><strong>For Replacement : </strong>Current For Replacement Social Pension Beneficiaries (Deceased or Delisted)</li>
                <li><strong>Total Current Beneficiaries : </strong>Total Current Slot (Active + For Replacement)</li>
                <li><strong>Eligible Waitlist : </strong>Eligible Waitlist validated by C.O</li>
                <li><strong>Variance (Eligibile Waitlist - For Replacement)</strong>
                    <ol>
                        <li><strong>Positive value: </strong>Will add as Additional Beneficiaries</li>
                        <li><strong>Negative value: </strong>Lack of Eligible Waitlist</li>
                    </ol>
                </li>
                <li><strong> Total Actual Slot (Active + Additional)</strong></li>
                <li><strong> Variance (Target - Actual)</strong>
                    <ol>
                        <li><strong> Zero : </strong>Target is equal to current slot</li>
                        <li><strong> Positive Value : </strong>The target exceeds</li>
                        <li><strong> Negative Value : </strong>Lack of Beneficiaries</li>
                    </ol>
                </li>
            <ul>
        </div>
    </div>
</div>
<div class="app-body" id = "dashboard_page">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body pb-0">
                            <div class="btn-group float-right">
                                <a class="btn btn-transparent p-0 float-right" type="button" href = "<?= base_url("report-target") ?>">
                                    <i class="fa fa-hand-o-right"> Breakdown</i>
                                </a>
                            </div>
                            <div class="text-value">{{targetChart.data.total}}</div>
                            <div><strong>SP Target</strong></div>
                        </div>
                        <div class="chart-wrapper mt-3 mx-3" style="height:70px;">
                            <canvas class="chart" id="card-target" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-info">
                        <div class="card-body pb-0">
                            <a class="btn btn-transparent p-0 float-right" type="button" href = "<?= base_url("report-active") ?>">
                                <i class="fa fa-hand-o-right"> Breakdown</i>
                            </a>
                            <div class="text-value">{{activeChart.data.total}}</div>
                            <div><strong>Active Beneficiaries</strong></div>
                        </div>
                        <div class="chart-wrapper mt-3 mx-3" style="height:70px;">
                            <canvas class="chart" id="card-active" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body pb-0">
                            <div class="btn-group float-right">
                                <a class="btn btn-transparent p-0 float-right" type="button" href = "<?= base_url("report-active") ?>">
                                    <i class="fa fa-hand-o-right"> Breakdown</i>
                                </a>
                            </div>
                            <div class="text-value">{{forrepChart.data.total}}</div>
                            <div><strong>For Replacement</strong></div>  
                        </div>
                        <div class="chart-wrapper mt-3" style="height:70px;">
                            <canvas class="chart" id="card-forrep" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body pb-0">
                            <div class="btn-group float-right">
                                <a class="btn btn-transparent p-0 float-right" type="button" href = "<?= base_url("report-waitlist") ?>">
                                    <i class="fa fa-hand-o-right"> Breakdown</i>
                                </a>
                            </div>
                            <div class="text-value">{{waitlistChart.data.total}}</div>
                            <div><strong>Eligible Waitlist</strong></div>
                        </div>
                        <div class="chart-wrapper mt-3 mx-3" style="height:70px;">
                            <canvas class="chart" id="card-waitlist" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
            </div>
            <!-- /.row-->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4 class="card-title mb-0">Served Beneficiaries
                                <a class="btn float-right" type="button" href = "<?= base_url("report-served") ?>">
                                    <i class="fa fa-hand-o-right"> Details</i>
                                </a>
                            </h4>
                            <div class="small text-muted">{{getPeriod()}}</div>

                        </div>
                        <!-- /.col-->
                        <div class="col-sm-2  d-none d-md-block"> 
                            <label for="Year">Year</label>
                            <select class="form-control" v-model = "search.year">
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
                        <div class="col-sm-2  d-none d-md-block"> 
                            <label for="Year">Period</label>
                            <select class="form-control p-0" v-model = "search.period">
                                <option value="">Select Period</option>
                                <option value="5">1st Semester</option>
                                <option value="6">2nd Semester</option>
                                <option value="1">1st Quarter</option>
                                <option value="2">2nd Quarter</option>
                                <option value="3">3rd Quarter</option>
                                <option value="4">4th Quarter</option>
                            </select>
                        </div>
                        <div class="col-sm-1  d-none d-md-block"> 
                            <br>
                            <button type="button" class="btn btn-info float-left"  @click = "searchDashData()">
                            <i class="fa fa-search"></i></button>
                        </div>

                        <div class="col-sm-1 d-none d-md-block">
                            <button class="btn btn-primary float-right" type="button">
                                <i class="icon-cloud-download"></i>
                            </button>
                            <!-- <div class="btn-group btn-group-toggle float-right mr-3" data-toggle="buttons">
                                <label class="btn btn-outline-secondary">
                                    <input id="option1" type="radio" name="options" autocomplete="off"> Day
                                </label>
                                <label class="btn btn-outline-secondary active">
                                    <input id="option2" type="radio" name="options" autocomplete="off" checked=""> Month
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input id="option3" type="radio" name="options" autocomplete="off"> Year
                                </label>
                            </div> -->
                        </div>
                        <!-- /.col-->
                    </div>
                    <!-- /.row-->
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-wrapper" style="height:300px;margin-top:40px;">
                                <canvas class="chart" id="main-chart" height="300"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <v-client-table  
                                :columns="served_table.columns"
                                :data="served_table.data.rm"
                                :options="served_table.options"
                                >
                            </v-client-table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col-sm-12 col-md mb-sm-2 mb-0">
                            <div class="text-muted">Total Unpaid ( {{served_table.data.unpaid_progress}}% )</div>
                            <strong>{{getNumberFormat(served_table.data.region_unpaid)}}</strong>
                            <div class="progress progress-xs mt-2">
                                <div class="progress-bar bg-info" role="progressbar" :style="'width:'+ served_table.data.unpaid_progress+'%'" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md mb-sm-2 mb-0">
                            <div class="text-muted">Total Served ( {{served_table.data.accomplishment}}% )</div>
                            <strong>{{getNumberFormat(served_table.data.region_served)}}</strong>
                            <div class="progress progress-xs mt-2">
                                <div class="progress-bar bg-success" role="progressbar" :style="'width:'+ served_table.data.accomplishment+'%'" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md mb-sm-2 mb-0">
                            <div class="text-muted">Total Target</div>
                            <strong>{{getNumberFormat(served_table.data.region_targets)}}</strong>
                            <div class="progress progress-xs mt-2">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>

</div>

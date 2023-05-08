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
    .form-control{
        /*background-color: #ffffff !important;*/
        border: 1px solid black !important;
    }
    .col-sm-12, .col-sm-6, .col-sm-4, .col-sm-3, .form-group, .label-floating {
        margin: 0% !important;
        padding: 0.5% !important;
    }
</style>

<div id="targetPensioner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title d-inline">Social Pension Target</h3>
                    <button class="btn btn-warning float-right" data-toggle="modal" data-target="#clonePensionersModal">Clone Target (Year)</button>
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addPensionersModal">Add New Target</button>
                </div>
                <div class="card-body">
                    <div class="row row-search">
                        <div class="col-md-12 ">
                            <form v-on:submit.prevent="searchList">
                                <div class="row align-items-end" >
                                    <div class="col-md-3 "> 
                                        <label for="Province">Province</label>
                                        <select class="form-control p-0" v-model = "search.prov_code" @change="limitMunicipality($event,'search')" name="Province">
                                            <option value="">Select Province</option>
                                            <template v-for = "(list,index) in location.provinces">
                                            <option :value="list.prov_code">{{list.prov_name}}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-md-3 "> 
                                        <label for="Municipality">Municipality</label>
                                        <select class="form-control p-0" v-model = "search.mun_code" :disabled="disable_municipality" name="Municipality" >
                                            <option value="">Select Municipality</option>
                                            <template v-for = "(list,index) in location.limit_municipalities">
                                                <option :value="list.mun_code">{{list.mun_name}}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-md-2"> 
                                        <label for="Year">Year</label>
                                        <select class="form-control p-0"   v-model = "search.year" name="Year">
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
                                    <div class="col-md-2"> 
                                        <label for="Semester">Period</label>
                                        <select class="form-control p-0"   v-model = "search.quarter" name="quarter">
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
                                        <button type="submit" class="btn btn-info btn-block" >
                                        <i class="fa fa-search"></i> SEARCH</button>
                                    </div>
                                </div>   
                            </form>
                        </div>
                    </div>

                    <v-client-table :columns="table.columns" :data="table.data.list" :options="table.options">
                        <template slot="#" slot-scope="props">
                            <!-- <span>{{props.index}}</span> -->
                            <span>{{props.row.id}}</span>
                        </template>
                        <template slot="quarter" slot-scope="props">
                            <!-- <span v-if="props.row.semester == 1">1st Semester</span>
                            <span v-else>2nd Semester</span> -->

                            <span v-if="props.row.quarter == 5">1st Semester</span>
                            <span v-if="props.row.quarter == 6">2nd Semester</span>
                            <span v-if="props.row.quarter == 1">1st Quarter</span>
                            <span v-if="props.row.quarter == 2">2nd Quarter</span>
                            <span v-if="props.row.quarter == 3">3rd Quarter</span>
                            <span v-if="props.row.quarter == 4">4th Quarter</span>
                        </template>
                        <template slot="status" slot-scope="props">
                            <span v-if="props.row.status == 1" class="badge badge-success">Active</span>
                            <span v-else class="badge badge-danger">Inactive</span>
                        </template>
                        <template slot="action" slot-scope="props">
                            <button type="button" class="btn btn-warning btn-sm" @click="updatePensioners(props.row)" data-toggle="modal" data-target="#updatePensionersModal"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-sm" @click="deletePensioners(props.row)" data-toggle="modal" data-target="#deletePensionerModal"><i class="fa fa-archive"></i></button>
                        </template>
                    </v-client-table>
                </div>
                <div class="card-footer clearfix">
                </div>
            </div>
        </div>
    </div>

    <!-- ADD -->
    <div class="modal" id="addPensionersModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form v-on:submit.prevent="save">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Social Pension Target</h5>
                        <a href="#" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Province</label>
                                <select class="form-control p-0" @change="limitMunicipality($event)" v-model="form.prov_code">
                                    <option value="">Select Province</option>
                                    <template v-for = "(list,index) in location.provinces">
                                    <option :value="list.prov_code">{{list.prov_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">Municipality</label>
                                <select class="form-control" v-model="form.mun_code" :disabled="disable_crud" required>
                                    <option value="">Select Municipality</option>
                                    <template v-for = "(list,index) in location.limit_municipalities">
                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Year</label>
                                <input type="number" class="form-control" v-model="form.year" min="2015" max="2025">
                            </div>
                            <div class="col-md-6">
                                <label for="">Semester</label>
                                <select class="form-control" v-model="form.quarter" required>
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
                        <div class="row">
                            <div class="col -md-12">
                                <label for="">Target</label>
                                <input type="text" class="form-control" v-model="form.target">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Add</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>





    <!--  UPDATE -->
    <div class="modal" id="updatePensionersModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form v-on:submit.prevent="update_target_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Social Pension Target</h5>
                        <a href="#" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body">
                        <input type="text" v-model="update_data.id" hidden>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Province</label>
                                <select class="form-control" v-model="update_data.prov_code" @change="limitMunicipality($event)" disabled>
                                    <option value="">Select Province</option>
                                    <template v-for = "(list,index) in location.provinces">
                                    <option :value="list.prov_code">{{list.prov_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">Municipality</label>
                                <select class="form-control" v-model="update_data.mun_code" :disabled="disable_crud" disabled>
                                    <option value="">Select Municipality</option>
                                    <template v-for = "(list,index) in location.limit_municipalities">
                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                    </template>
                                </select>
                            </div>
                       </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Year</label>
                                <input type="number" class="form-control" v-model="update_data.year" min="2015" max="2025" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="">Semester</label>
                                <select class="form-control" v-model="update_data.quarter" disabled>
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
                        <div class="row">
                            <div class="col -md-12">
                                <label for="">Target</label>
                                <input type="text" class="form-control" v-model="update_data.target">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- REMOVE -->
    <div class="modal" id="deletePensionerModal" tabindex="-1" role="dialog">
        <form v-on:submit.prevent="delete_target_form">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archive Social Pension Target</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" v-model="delete_data.id" hidden>
                        Are you sure you want to archive this Target?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Confirm</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <!-- CLONE -->
    <div class="modal" id="clonePensionersModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form v-on:submit.prevent="clone_target_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Clone Target</h5>
                        <a href="#" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">From</label>
                                <select class="form-control p-0" @change="limitMunicipality($event)" v-model="clone.prev_year">
                                    <option value="">Select Year</option>
                                    <template v-for = "list in target_years">
                                        <option :value="list.year">{{list.year}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">&nbsp;</label>
                                <select class="form-control" v-model="clone.prev_sem" required>
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
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">To</label>
                                <input type="number" class="form-control" v-model="clone.new_year" min="2015" max="2025">
                            </div>
                            <div class="col-md-6">
                                <label for="">&nbsp;</label>
                                <select class="form-control" v-model="clone.new_sem" required>
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
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Clone</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
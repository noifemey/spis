<style>
    hr {
        border: none;
        height: 2px;
        background-color: #333;
    }

    .vl {
        border-right: 1px solid #333;
        height: 300px;
    }

    /* .form-control {
        background-color: #ffffff !important;
        border: 1px solid black !important;
    } */

    .col-sm-12,
    .col-sm-6,
    .col-sm-4,
    .col-sm-3,
    .form-group,
    .label-floating {
        margin: 0% !important;
        padding: 0.5% !important;
    }
</style>

<div id="requestform">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title d-inline">Change Request Form</h4><br><br>
                    <button type="button" v-on:click="getListOfUsers(),selectUser(<?= sesdata('id'); ?>)" class="btn btn-primary" data-toggle="modal" data-target="#addNewRequest">
                        Add New Change Request
                    </button>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Filter By Due Date:</label>
                            <input type="date" class="form-control" @change="filterDate" v-model="searchKeys.keyDate">
                        </div>
                        <div class="col-md-4">
                            <label for="">Filer By Status:</label>
                            <select class="form-control" @change="filterStatus" v-model="searchKeys.keyStatus">
                                <option value=""></option>
                                <option value="For Action">For Action</option>
                                <option value="On Going">On Going</option>
                                <option value="Resolved">Resolved</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <v-client-table :columns="table.columns" :data="table.data.list" :options="table.options">
                        <template slot="prioritylvl" slot-scope="e">
                            <span v-if="e.row.prioritylvl == 1" class="badge badge-success">Low</span>
                            <span v-if="e.row.prioritylvl == 2" class="badge badge-warning">Medium</span>
                            <span v-if="e.row.prioritylvl == 3" class="badge badge-danger">High</span>
                        </template>
                        <!-- <template slot="status" slot-scope="e">
                            <select name="" id="" @change="changeStatus()" class="form-control btn-danger" v-if="e.row.assign_to == addNewRequest.req_by || userRole == 1">
                                <option :value="e.row.status">{{e.row.status}}</option>
                                <option value="For Action" :hidden="e.row.status ==  'For Action'">For Action</option>
                                <option value="On Going" :hidden="e.row.status ==  'On Going'">On Going</option>
                                <option value="Resolved" :hidden="e.row.status ==  'Resolved'">Resolved</option>
                            </select>
                        </template> -->
                        <template slot="status" slot-scope="e">
                            <div v-if="e.row.assign_to == addNewRequest.req_by || userRole == 1">
                                <button type="button" data-toggle="modal" data-target="#updateActionModal" class="btn btn-danger" v-on:click="updateAction(e.row)">{{e.row.status}}</button>
                            </div>
                            <div v-else>
                                <span v-if="e.row.status == 'For Action'" class="badge badge-danger">For Action</span>
                                <span v-if="e.row.status == 'On Going'" class="badge badge-warning">On Going</span>
                                <span v-if="e.row.status == 'Resolved'" class="badge badge-success">Resolved</span>
                            </div>
                        </template>
                    </v-client-table>
                </div>
                <div class="card-footer clearfix">
                </div>
            </div>
        </div>
    </div>

    <!-- UPDATE -->
    <div class="modal fade" id="updateActionModal" tabindex="-1" role="dialog" aria-labelledby="changeRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeRequestModalLabel">Change Request Details</h5>
                </div>
                <div class="container modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Subject</label>
                            <span class="badge badge-success">{{updateForm.updateSubject}}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Requested by: </label>
                            <span class="badge badge-success">{{updateForm.updateReqBy}}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">System Module: </label>
                            <span class="badge badge-success">{{updateForm.updateSystemModule}}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Priority Level: </label>
                            <span v-if="updateForm.updatePriority == 1" class="badge badge-success">Low</span>
                            <span v-if="updateForm.updatePriority == 2" class="badge badge-warning">Medium</span>
                            <span v-if="updateForm.updatePriority == 3" class="badge badge-danger">High</span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="">Change Request Details:</label>
                            <textarea name="" id="" cols="30" rows="5" class="form-control" class="form-control" v-model="updateForm.updateReqDetails" placeholder="Enter Request Details"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Status:</label>
                            <select class="form-control btn btn-danger" v-model="updateForm.updateStatus">
                                <option value="For Action" :disabled="updateForm.updateStatus ==  'For Action'">For Action</option>
                                <option value="On Going" :disabled="updateForm.updateStatus ==  'On Going'">On Going</option>
                                <option value="Resolved" :disabled="updateForm.updateStatus ==  'Resolved'">Resolved</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="">Action Taken:</label>
                            <textarea name="" id="" cols="30" rows="5" class="form-control" v-model="updateForm.updateActionTaken" placeholder="Enter Action Taken"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="updateRequest">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>




    <!-- Modal -->
    <div class="modal fade" id="addNewRequest" tabindex="-1" role="dialog" aria-labelledby="RequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="RequestModalLabel">Change Request Form</h5>
                </div>
                <input type="text" value="" v-model="addNewRequest.req_by" hidden>
                <div class="container modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Date:&nbsp</label><span><?php date_default_timezone_set("Asia/Singapore");
                                                                    echo date('M D Y H:i A'); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="subject">Subject</label>
                            <input type="text" class="form-control" :class="[addNewRequest.issue_subject == '' ? 'border border-danger' : '']" placeholder="Enter Subject" v-model="addNewRequest.issue_subject">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <label for="system_module">System Module</label>
                            <input type="text" class="form-control" :class="[addNewRequest.modulename == '' ? 'border border-danger' : '']" placeholder="Enter System Module" v-model="addNewRequest.modulename">
                        </div>
                        <div class="col-md-4">
                            <label for="system_module">Priority Level</label>
                            <select v-model="addNewRequest.prioritylvl" class="form-control" :class="[addNewRequest.prioritylvl == '' ? 'border border-danger' : '']">
                                <option value="1">Low</option>
                                <option value="2">Meduim</option>
                                <option value="3">High</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <label for="assign">Assign To</label>
                            <select v-model="addNewRequest.assign_to" class="form-control" :class="[addNewRequest.assign_to == '' ? 'border border-danger' : '']">
                                <option v-for="list,i in listOfUsers" :value="list.id">{{list.fname}} {{list.mname}} {{list.lname}}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="due">Due Date</label>
                            <input type="date" class="form-control" placeholder="Enter Due Date" v-model="addNewRequest.duedate" :class="[addNewRequest.duedate == '' ? 'border border-danger' : '']">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="reqDetails">Change Request Details</label>
                            <textarea name="" id="" cols="30" rows="10" class="form-control" placeholder="Enter Request Details" v-model="addNewRequest.issue_details" :class="[addNewRequest.issue_details == '' ? 'border border-danger' : '']"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" :disabled="addNewRequest.issue_details == ''" v-on:click="submitRequest">Submit Request</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 
    <div class="modal" id="activateUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form v-on:submit.prevent="activate_user_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Activate User</h5>
                        <a href="#" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body">
                        <input type="text" v-model="update_data.id" hidden>
                        Are you sure you want to change the activated status this user?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div> -->
</div>
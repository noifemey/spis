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
        background-color: #ffffff !important;
        border: 1px solid black !important;
    }
    .col-sm-12, .col-sm-6, .col-sm-4, .col-sm-3, .form-group, .label-floating {
        margin: 0% !important;
        padding: 0.5% !important;
    }
</style>

<div id="Reason">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title d-inline">Social Pension Inactive Reason</h3>
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addReasonModal">Add New Inactive Reason</button>
                </div>
                <div class="card-body">
                    <v-client-table :columns="table.columns" :data="table.data.list" :options="table.options">
                        <template slot="status" slot-scope="props">
                            <span v-if="props.row.status == 1" class="badge badge-success">Active</span>
                            <span v-else class="badge badge-danger">Inactive</span>
                        </template>
                        <template slot="action" slot-scope="props">
                            <button type="button" class="btn btn-warning btn-sm" @click="updateReason(props.row)" data-toggle="modal" data-target="#updateReasonModal"><i class="fa fa-edit"></i></button>
                            <!-- <button type="button" class="btn btn-danger btn-sm" @click="deleteDivision(props.row)" data-toggle="modal" data-target="#deleteDivisionModal"><i class="fa fa-trash"></i></button> -->
                        </template>
                    </v-client-table>
                </div>
                <div class="card-footer clearfix">
                </div>
            </div>
        </div>
    </div>

    <!-- ADD -->
    <div class="modal" id="addReasonModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form v-on:submit.prevent="save">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Inactive Reason</h5>
                        <a href="#" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="">Inactive Reason</label>
                                <input class="form-control" type="text" v-model="form.name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning">Add</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



   <!--  UPDATE -->
    <div class="modal" id="updateReasonModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form v-on:submit.prevent="update_reasons_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Inactive Reason</h5>
                        <a href="#" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body">
                        <input type="text" v-model="update_data.id" hidden>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="">Inactive Reason</label>
                                <input class="form-control" type="text" v-model="update_data.name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col -md-12">
                                <label for="">Inactive Reason Status</label>
                                <select class="form-control" v-model="update_data.status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning">Update</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


</div>
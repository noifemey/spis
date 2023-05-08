<style>
    /* th, td {
        border: 1px solid black;
        font-size: 12px;
        padding: 8px;
    } */
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

<div id="userList">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">   
                    <h4 class="card-title d-inline">Users List</h4>
                </div>
                <div class="card-body">
                    <v-client-table :columns="table.columns" :data="table.data.list" :options="table.options">
                        <template slot="fullname" slot-scope="props">
                            {{props.row.fname}} {{props.row.mname}} {{props.row.lname}}
                        </template>
                        <template slot="role" slot-scope="props">
                            <span v-if="props.row.role == 1">Admin</span>
                            <span v-if="props.row.role == 2">Focal</span>
                            <span v-if="props.row.role == 3">User</span>
                            <span v-if="props.row.role == 4">Guest</span>
                        </template>
                        <template slot="activate" slot-scope="props">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" :id="'status_index'+props.row.id" :checked="props.row.active_status==1" @change="activateUser(props.row)" data-toggle="modal" data-target="#activateUserModal">
                                <label class="custom-control-label" :for="'status_index'+props.row.id"></label>
                            </div>
                        </template>
                        <template slot="action" slot-scope="props">
                            <button type="button" class="btn btn-warning btn-sm" @click="updateUser(props.row)" data-toggle="modal" data-target="#updateUserModal"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-sm" @click="updateUser(props.row)" data-toggle="modal" data-target="#deleteUserModal"><i class="fa fa-trash"></i></button>
                        </template>
                        <template slot="Reset" slot-scope="e">
                        <button type="button" class="btn btn-info btn-sm" @click="confirmReset(e.row)"><i class="fa fa-undo"></i></button>
                        </template>
                    </v-client-table>

                </div>
                
                <div class="card-footer clearfix">
                </div>
            </div>
        </div>
    </div>  

    <!--  UPDATE -->
    <div class="modal" id="updateUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <form v-on:submit.prevent="update_user_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update User</h5>
                        <a href="#" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body">
                        <input type="text" v-model="update_data.id" hidden>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Username</label>
                                <input class="form-control" type="text" v-model="update_data.username" required>
                            </div>
                            <div class="col-md-4">
                                <label for="">Email Address</label>
                                <input class="form-control" type="text" v-model="update_data.emailadd" required>
                            </div>
                            <div class="col-md-4">
                                <label for="">Position</label>
                                <input class="form-control" type="text" v-model="update_data.position" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Last Name</label>
                                <input class="form-control" type="text" v-model="update_data.lname" required>
                            </div>
                            <div class="col-md-4">
                                <label for="">First Name</label>
                                <input class="form-control" type="text" v-model="update_data.fname" required>
                            </div>
                            <div class="col-md-4">
                                <label for="">Middle Name</label>
                                <input class="form-control" type="text" v-model="update_data.mname" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="user_role">User Role</label>
                                <select class="form-control" v-model="update_data.role" id="user_role" required>
                                    <option value="1">Admin</option>
                                    <option value="2">Focal</option>
                                    <option value="3">User</option>
                                    <option value="4">Guest</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="province">Province</label>
                                <select class="form-control" v-model="update_data.province" id="province" required>
                                    <template v-for="p,i in provList">
                                        <option :value="p.prov_code">{{p.prov_name}}</option>
                                    </template>
                                </select>
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
    <div class="modal" id="deleteUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form v-on:submit.prevent="delete_user_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archive User</h5>
                        <a href="#" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body">
                        <input type="text" v-model="update_data.id" hidden>
                        Are you sure you want to archive this user?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Archive</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
    </div>
</div>
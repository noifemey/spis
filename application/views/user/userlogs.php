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

    .form-control {
        background-color: #ffffff !important;
        border: 1px solid black !important;
    }

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

<div id="userlogs">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title d-inline">Users Activity Logs</h4>
                </div>
                <div class="row mx-2">
                    <div class="col-md-3">
                        From Date: <input type="date" class="form-control" @change="checkFromDate" v-model="formFilter.fromUser">
                    </div>
                    <div class="col-md-3">
                        To Date: <input type="date" class="form-control" @change="checkToDate" v-model="formFilter.toUser">
                    </div>
                    <!-- <div class="col-md-3 mt-3">
                        <button class="btn btn-primary" @click="getPageInfo">Filter</button>
                    </div> -->
                    <div class="col-md-3">
                        By User:
                        <select v-model.trim="formFilter.user" class="form-control" @change="getPageInfo" data-live-search="true">
                            <option value=''></option>
                            <template v-for="list,li in listOfUser">
                                <option :value="list.id">{{list.fullname}}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <v-client-table :columns="table.columns" :data="table.data.list" :options="table.options">
                        <template slot="laction" slot-scope="e">
                            <span v-if="e.row.laction == 'LOGIN'" class="badge badge-success">Login</span>
                            <span v-if="e.row.laction == 'Logout'" class="badge badge-success">Logout</span>
                            <span v-if="e.row.laction == 'EDIT'" class="badge badge-warning">Edit</span>
                            <span v-if="e.row.laction == 'ADD'" class="badge badge-info">Add</span>
                            <span v-if="e.row.laction == 'EXPORT'" class="badge badge-secondary">Export</span>
                            <span v-if="e.row.laction == 'DELETE'" class="badge badge-danger">Delete</span>
                            <span v-if="e.row.laction == 'REGISTRATION'" class="badge badge-primary">Registration</span>
                            <span v-if="e.row.laction == 'ChangePassword'" class="badge badge-dark">Change Password</span>
                        </template>
                    </v-client-table>
                </div>
                <div class="card-footer clearfix">
                </div>
            </div>
        </div>
    </div>
</div>
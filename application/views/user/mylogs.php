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

<div id="mylogs">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title d-inline">My Activity Logs</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            Start Date: <input type="date" class="form-control" v-model = "form.from">
                        </div>
                        <div class="col-md-3">
                            End Date: <input type="date" class="form-control" v-model = "form.to">
                        </div>
                        <!-- <div class="col-md-3">
                            Username:
                            <select name="" id="" class='form-control' v-model = "form.uid">
                                <option value="">Reset</option>
                                <template v-for="list, index in list_of_users">
                                    <option :value="list.id">{{list.username}}</option>
                                </template>
                            </select>
                        </div> -->
                        <div class="col-md-3 mt-3">
                            <button class="btn btn-primary" @click = "getPageInfo">Filter</button>
                        </div>
                        <div class="col-md-12">
                            <v-client-table :columns="table.columns" :data="table.data.list" :options="table.options">
                            </v-client-table>
                        </div>
                    </div>
                </div>

                <div class="card-footer clearfix">
                </div>
            </div>
        </div>
    </div>
</div>
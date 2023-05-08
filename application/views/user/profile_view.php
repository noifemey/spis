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

<div id="user_profile">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form v-on:submit.prevent="save">
                    <div class="card-header">   
                        <h4 class="card-title d-inline">User Profile</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Username</label>
                                <input class="form-control" type="text" v-model="form.username" required>
                            </div>
                            <div class="col-md-4">
                                <label for="">Email Address</label>
                                <input class="form-control" type="text" v-model="form.emailadd" required>
                            </div>
                            <div class="col-md-4">
                                <label for="">Position</label>
                                <input class="form-control" type="text" v-model="form.position" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Last Name</label>
                                <input class="form-control" type="text" v-model="form.lname" required>
                            </div>
                            <div class="col-md-4">
                                <label for="">First Name</label>
                                <input class="form-control" type="text" v-model="form.fname" required>
                            </div>
                            <div class="col-md-4">
                                <label for="">Middle Name</label>
                                <input class="form-control" type="text" v-model="form.mname" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Password</label>
                                <input class="form-control" type="password" v-model="form.password">
                            </div>
                            <div class="col-md-4">
                                <label for="">Confirm Password</label>
                                <input class="form-control" type="password" v-model="form.c_password">
                            </div>
                            <div class="col-md-4">
                                <label for="">Province</label>
                                <select class="form-control" v-model="form.province" id="province" required>
                                    <template v-for="p,i in provList">
                                        <option :value="p.prov_code">{{p.prov_name}}</option>
                                    </template>
                                </select>
                            </div>
                        </div>

                    </div>
                    
                    <div class="card-footer clearfix text-right">
                        <button class="btn btn-success">Update User Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>    
</div>

</div>
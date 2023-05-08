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

<div id="Signatories">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title d-inline">Social Pension Signatories</h3>
                </div>
                <div class="card-body">

                <ul class="nav nav-tabs mb-2 mt-5" id="myTab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="cash-assistance-tab" data-toggle="tab" href="#cash-assistance" role="tab" aria-controls="cash-assistance" aria-selected="true">Cash Assistance Payroll</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="signatories-tab" data-toggle="tab" href="#signatories" role="tab" aria-controls="signatories" aria-selected="false">Social Pension Active Beneficiaries Masterlist</a>
                  </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="cash-assistance" role="tabpanel" aria-labelledby="cash-assistance-tab">


                        <form v-on:submit.prevent="update_signatories_form(1)">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-primary ">
                                        <div class="card-header">
                                            <h3 class="card-title">Cash Assistance Payroll Signatories</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                Signatory 1:
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Name</label>
                                                    <input class="form-control" type="text" v-model="signatories_cap.sign1_name"  />
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Position</label>
                                                    <input class="form-control" type="text" v-model="signatories_cap.sign1_position" />
                                                </div>
                                            </div>
                                            <br/>
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                Signatory 2:
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Name</label>
                                                    <input class="form-control" type="text" v-model="signatories_cap.sign2_name" />
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Position</label>
                                                    <input class="form-control" type="text" v-model="signatories_cap.sign2_position" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <button class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="signatories" role="tabpanel" aria-labelledby="signatories-tab">
                        <form v-on:submit.prevent="update_signatories_form(2)">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-primary ">
                                        <div class="card-header">
                                            <h3 class="card-title">Social Pension Active Beneficiaries Masterlist Signatories</h3>
                                        </div>
                                       <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                Prepared by:
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Name</label>
                                                    <input class="form-control" type="text"  v-model="signatories_masterlist.sign2_name" />
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Position</label>
                                                    <input class="form-control" type="text" v-model="signatories_masterlist.sign2_position" />
                                                </div>
                                            </div>
                                            <br/>
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                Recommending Approval:
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Name</label>
                                                    <input class="form-control" type="text"  v-model="signatories_masterlist.sign3_name" />
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Position</label>
                                                    <input class="form-control" type="text" v-model="signatories_masterlist.sign3_position" />
                                                </div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                Approved by:
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Name</label>
                                                    <input class="form-control" type="text" v-model="signatories_masterlist.sign4_name" />
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="">Position</label>
                                                    <input class="form-control" type="text" v-model="signatories_masterlist.sign4_position" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <button class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>   


                   
                </div>
            </div>
        </div>
    </div>
</div>
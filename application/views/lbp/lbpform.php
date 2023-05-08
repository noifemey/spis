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
    .col-sm-12, .col-sm-6, .col-sm-4, .col-sm-3, .form-group, .label-floating {
        margin: 0% !important;
        padding: 0.5% !important;
    }
</style>

<div id = "lbp_index">
    <div class="row">
        <div class="col-md-12 ">
            <br> <h3>Social Land Bank Card Enrollment Form </h3><br>
        <div>
    </div>
    <div class="card">
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="row" >
                            <div class="col-md-6 "> 
                                <label for="Province">Province</label>
                                <select class="form-control p-0"   @change = "getLocation('mun_code',search.prov_code)" v-model = "search.prov_code" name="Province" required>
                                    <template v-for = "(list,index) in location.provinces">
                                    <option :value="list.prov_code">{{list.prov_name}}</option>
                                    </template>
                                </select>
                            </div>
                            <div class="col-md-6 "> 
                                <label for="Municipality">Municipality</label>
                                <select class="form-control p-0" v-model = "search.mun_code" :disabled = "location.municipalities.length <=0" name="Municipality" >
                                    <template v-for = "(list,index) in location.municipalities">
                                        <option :value="list.mun_code">{{list.mun_name}}</option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 "> 
                                <button type="submit" class="btn btn-info btn-lg btn-block"  @click = "exportblank()">
                                <i class="fa fa-download"></i> Export Blank LBP CASH CARD ENROLLMENT FORM</button>
                            </div>
                            <div class="col-md-6 "> 
                                <button type="submit" class="btn btn-warning btn-lg btn-block" @click = "generatelbpform()">
                                <i class="fa fa-download"></i> Generate LBP FORM</button>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
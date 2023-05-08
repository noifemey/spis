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

<div id="sp_crossmatching_page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">   
                    <h4 class="card-title d-inline">SP Beneficiaries</h4>                    
                    <!-- <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addDataModal">Add a Raw Material</button> -->
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="" class="btn btn-danger btn-block" data-toggle="modal" data-target="#importsapModal">
                                <i class="fa fa-plus text-center"></i> Import SP Beneficiaries For Crossmatching
                            </a>        
                        </div>
                    </div>
                    <div class="row mt-2" hidden>
                        <div class="col-md-12">
                            <a href="" class="btn btn-danger btn-block" data-toggle="modal" data-target="#importuctModal">
                                <i class="fa fa-plus text-center"></i> Import SP-UCT Beneficiaries For Crossmatching
                            </a>        
                        </div>
                    </div>
                    <br>

                    <div class="row" style = "border:solid; padding:20px">
                    <div class="col-md-12 ">
                        <form v-on:submit.prevent="getAllData">
                            <div class="row" >
                                <div class="col-md-3 "> 
                                    <label for="Agency">Table Source</label>
                                    <select class="form-control p-0"  v-model="form.search.agency" name="Agency">
                                        <option value="all">ALL</option>
                                        <option value="tblgeneral">ACTIVE</option>
                                        <option value="tblwaitinglist">WAITLIST</option>
                                    </select>
                                </div>
                                <div class="col-md-3 "> 
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" v-model="form.search.last_name"  name="last_name" id="last_name"/>
                                </div>
                                <div class="col-md-3 "> 
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" v-model="form.search.first_name"  name="first_name" id="first_name"/>
                                </div>
                                <div class="col-md-3 "> 
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" class="form-control" v-model="form.search.middle_name"  name="middle_name" id="middle_name"/>
                                </div>
                            </div>  
                            <br> 
                            <div class="row">
                                <div class="col-md-12 "> 
                                    <button type="submit" class="btn btn-info btn-block" >
                                    <i class="fa fa-eye"></i> SEARCH</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>

                    <br>
                    
                    <div v-if="content !== ''">
                        <div class="row">
                            <div class="col-md-12" >
                                <button type="button" class="btn btn-primary" @click = "exportDuplicates()">
                                <i class="fa fa-download"></i> Download Duplicates </button>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <v-client-table  
                                :columns="raw_materials.columns"
                                :data="raw_materials.data.rm"
                                :options="raw_materials.options"
                                >
                            </v-client-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- uploadwaitinglist modal -->
    <div class="modal fade" id="importsapModal" tabindex="-1" role="dialog" aria-labelledby="masterlistData" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">                
                <div class="modal-header">
                    <h5 class="modal-title">SP BENEFICIARIES IMPORT</h5>                        
                </div>
                <div class="modal-body">
                    <h5 class="modal-title">Select .csv file with the following column: "last_name", "first_name", "middle_name", "ext_name"</h5> 
                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-md-12  text-center">
                            <template>
                                <input class="btn btn-info btn-block"  type="file" id="file" ref="file" v-on:change="handleFileUpload()" required accept=".csv"/>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">            
                    <button class="btn btn-primary" v-on:click="submitFile()">Submit</button>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="importuctModal" tabindex="-1" role="dialog" aria-labelledby="masterlistData" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">                
                <div class="modal-header">
                    <h5 class="modal-title">SP-UCT BENEFICIARIES IMPORT</h5>                        
                </div>
                <div class="modal-body">
                    <h5 class="modal-title">Select .csv file with the following column: "last_name", "first_name", "middle_name", "ext_name"</h5> 
                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-md-12  text-center">
                            <template>
                                <input class="btn btn-info btn-block"  type="file" id="file2" ref="file2" v-on:change="handleFileUpload2()" required accept=".csv"/>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">            
                    <button class="btn btn-primary" v-on:click="submitUCTFile()">Submit</button>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
Vue.use(VueTables.ClientTable);
var pensioner = new Vue({
    el: '#targetPensioner',
    data: {
        activenav: '',
        form: {
            prov_code: "",
            mun_code: "",
            year: "",
            semester: "",
            quarter: "",
            target: "",
        },
        update_data: {
            id: 0,
            mun_code: "",
            prov_code: "",
            year: "",
            semester: "",
            quarter: "",
            target: "",
        },
        search: {
            mun_code: "",
            prov_code: "",
            year: "",
            semester: "",
            quarter: "",
        },
        delete_data: {
            id: 0,
            mun_code: "",
            quarter: "",
        },
        clone: {
            prev_year: "",
            prev_sem: "",
            new_year: "",
            new_sem: "",
        },
        location: {
            provinces:[],
            municipalities:[],
            limit_municipalities:[],
        },
        table: {
            columns: ["#", 
                      "prov_name", 
                      "mun_name",  
                      "year", 
                      // "semester", 
                      "quarter", 
                      "target",
                      "action",
                      ],
            data: {
                list: []
            },
            options: {
                headings: {
                    province:"prov_name",
                    municipality:"mun_name",
                },
                sortable: ["prov_name", "mun_name", "year","semester","quarter","target"]
            }
        },
        target_years:[],
        disable_municipality:true,
        disable_crud:true,
        loading: false,
    }, methods: {
        save: function () {
            this.loading = true;
            var data = frmdata(this.form);
            var urls = window.App.baseUrl + "save-pensioners";
            axios.post(urls, data)
                .then(function (e) {
                    pensioner.getPageInfo();
                    pensioner.resetFields();
                    $("#addPensionersModal").modal('hide');

                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-pensioners";
            var data = frmdata(this.search);
            axios.post(urls,data)
                .then(function (e) {
                    pensioner.table.data.list = e.data.data;
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 

        searchList(){
            var data = frmdata(this.search);
            var urls = window.App.baseUrl + "get-pensioners";
            axios.post(urls,data)
                .then(function (e) {
                    pensioner.table.data.list = e.data.data;
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getProvinces: function () {
            var urls = window.App.baseUrl + "get-all-provinces";
            axios.post(urls)
                .then(function (e) {
                    pensioner.location.provinces = e.data.provinces;
                    // console.log(location.provinces)
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getMunicipalities: function () {
            var urls = window.App.baseUrl + "get-all-municipalities";
            axios.post(urls)
                .then(function (e) {
                    pensioner.location.municipalities = e.data.municipalities;
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getTargetYears: function () {
            var urls = window.App.baseUrl + "get-target-years";
            axios.post(urls)
                .then(function (e) {
                    pensioner.target_years = e.data.years;
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        updatePensioners: function(data){

            let prov_value = data.prov_code;

            pensioner.location.limit_municipalities = pensioner.location.municipalities.filter(obj => obj.prov_code == prov_value);

            this.update_data.id = data.id;
            this.update_data.mun_code = data.mun_code;
            this.update_data.year = data.year;
            this.update_data.semester = data.semester;
            this.update_data.quarter = data.quarter;
            this.update_data.target = data.target;
            this.update_data.prov_code = data.prov_code;
        },

        deletePensioners: function(data){
            this.delete_data.id = data.id;
            this.delete_data.mun_code = data.mun_code;
            this.delete_data.quarter = data.quarter;
        },

        update_target_form: function () {
            this.loading = true;
            var data = frmdata(this.update_data);
            var urls = window.App.baseUrl + "update-pensioners";
            axios.post(urls, data)
                .then(function (e) {
                    $('#updatePensionersModal').modal('hide')
                    pensioner.getPageInfo();

                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        delete_target_form: function () {
            this.loading = true;
            var data = frmdata(this.delete_data);
            var urls = window.App.baseUrl + "delete-pensioners";
            axios.post(urls, data)
                .then(function (e) {
                    $('#deletePensionerModal').modal('hide')
                    pensioner.getPageInfo();

                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        clone_target_form: function () {
            this.loading = true;
            var data = frmdata(this.clone);
            var urls = window.App.baseUrl + "clone-pensioners";
            axios.post(urls, data)
                .then(function (e) {
                    pensioner.getPageInfo();
                    pensioner.resetCloneFields();
                    $("#clonePensionersModal").modal('hide');

                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        limitMunicipality(event,type = "") {

            if(type == "search")
            {
                pensioner.disable_municipality = (event.target.value != "")?false:true;
            }
            else
            {
                pensioner.disable_crud = (event.target.value != "")?false:true;
            }
            
            let prov_value = event.target.value;

            pensioner.location.limit_municipalities = pensioner.location.municipalities.filter(obj => obj.prov_code == prov_value);

        },

        resetFields(){
            this.form.prov_code = "";
            this.form.mun_code = "";
            this.form.year = "";
            this.form.semester = "";
            this.form.quarter = "";
            this.form.target = "";
        },

        resetCloneFields(){
            this.clone.prev_year = "";
            this.clone.prev_sem = "";
            this.clone.new_year = "";
            this.clone.new_sem = "";
        },


    }, computed: {

    }, watch: {

    },
    mounted: function () {
        this.getPageInfo();
        this.getMunicipalities();
        this.getProvinces();
        this.getTargetYears();
        $("#addPensionersModal").on("hidden.bs.modal",function(){ pensioner.resetFields(); });
        $("#clonePensionersModal").on("hidden.bs.modal",function(){ pensioner.resetCloneFields(); });
    },
})
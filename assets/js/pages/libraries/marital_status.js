Vue.use(VueTables.ClientTable);
var marital_status = new Vue({
    el: '#maritalStatus',
    data: {
        activenav: '',
        form: {
            name: "",
        },
        update_data: {
            id: "",
            name: "",
            status: 0,
        },
        table: {
            columns: ["id", 
                      "name", 
                      "status",
                      "action",
                      ],
            data: {
                list: []
            },
            options: {
                headings: {
                    id: "#",
                    name: "Marital Status",
                },
                sortable: ["id", "name", "status"]
            }
        },
        loading: false,
    }, methods: {

        save: function () {
            this.loading = true;
            var data = frmdata(this.form);
            var urls = window.App.baseUrl + "save-marital-status";
            axios.post(urls, data)
                .then(function (e) {
                    marital_status.getPageInfo();
                    marital_status.resetFields();
                    $('#addMsModal').modal('hide');
                    methods.toastr('success','Success',e.data.message);
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-marital-status";
            axios.post(urls)
                .then(function (e) {
                    marital_status.table.data.list = e.data.data;
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 

        updateMaritalStatus: function(data){
            this.update_data.id = data.id;
            this.update_data.name = data.name;
            this.update_data.status = data.status;
        },

        update_marital_status_form: function () {
            this.loading = true;
            var data = frmdata(this.update_data);
            var urls = window.App.baseUrl + "update-marital-status";
            axios.post(urls, data)
                .then(function (e) {
                    $('#updateMsModal').modal('hide');
                    marital_status.getPageInfo();
                    methods.toastr('success','Success',e.data.message);

                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        resetFields(){
            this.form.name = "";
        },


    }, computed: {

    }, watch: {

    },
    mounted: function () {
        this.getPageInfo();
    },
})
Vue.use(VueTables.ClientTable);
var reasons = new Vue({
    el: '#Reason',
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
                    name: "Inactive Reasons",
                },
                sortable: ["id", "name", "status"]
            }
        },
        loading: false,
    }, methods: {

        save: function () {
            this.loading = true;
            var data = frmdata(this.form);
            var urls = window.App.baseUrl + "save-reasons";
            axios.post(urls, data)
                .then(function (e) {
                    reasons.getPageInfo();
                    reasons.resetFields();
                    $('#addReasonModal').modal('hide');
                    methods.toastr('success','Success',e.data.message);
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-reasons";
            axios.post(urls)
                .then(function (e) {
                    reasons.table.data.list = e.data.data;
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 

        updateReason: function(data){
            this.update_data.id = data.id;
            this.update_data.name = data.name;
            this.update_data.status = data.status;
        },

        update_reasons_form: function () {
            this.loading = true;
            var data = frmdata(this.update_data);
            var urls = window.App.baseUrl + "update-reasons";
            axios.post(urls, data)
                .then(function (e) {
                    $('#updateReasonModal').modal('hide');
                    reasons.getPageInfo();
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
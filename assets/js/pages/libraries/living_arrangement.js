Vue.use(VueTables.ClientTable);
var living_arrangement = new Vue({
    el: '#livingArrangement',
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
                    name: "Living Arrangement",
                },
                sortable: ["id", "name", "status"]
            }
        },
        loading: false,
    }, methods: {

        save: function () {
            this.loading = true;
            var data = frmdata(this.form);
            var urls = window.App.baseUrl + "save-living-arrangement";
            axios.post(urls, data)
                .then(function (e) {
                    living_arrangement.getPageInfo();
                    living_arrangement.resetFields();
                    $('#addLaModal').modal('hide');
                    methods.toastr('success','Success',e.data.message);
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-living-arrangement";
            axios.post(urls)
                .then(function (e) {
                    living_arrangement.table.data.list = e.data.data;
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 

        updateLivingArrangement: function(data){
            this.update_data.id = data.id;
            this.update_data.name = data.name;
            this.update_data.status = data.status;
        },

        update_living_arrangement_form: function () {
            this.loading = true;
            var data = frmdata(this.update_data);
            var urls = window.App.baseUrl + "update-living-arrangement";
            axios.post(urls, data)
                .then(function (e) {
                    $('#updateLaModal').modal('hide');
                    living_arrangement.getPageInfo();
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
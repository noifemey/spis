Vue.use(VueTables.ClientTable);
var house_type = new Vue({
    el: '#houseType',
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
                    name: "House Type",
                },
                sortable: ["id", "name", "status"]
            }
        },
        loading: false,
    }, methods: {

        save: function () {
            console.log("Pumasok dito");
            this.loading = true;
            var data = frmdata(this.form);
            var urls = window.App.baseUrl + "save-house-type";
            axios.post(urls, data)
                .then(function (e) {
                    house_type.getPageInfo();
                    house_type.resetFields();
                    $('#addHtModal').modal('hide');
                    methods.toastr('success','Success',e.data.message);
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-house-type";
            axios.post(urls)
                .then(function (e) {
                    house_type.table.data.list = e.data.data;
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 

        updateHouseType: function(data){
            this.update_data.id = data.id;
            this.update_data.name = data.name;
            this.update_data.status = data.status;
        },

        update_house_type_form: function () {
            this.loading = true;
            var data = frmdata(this.update_data);
            var urls = window.App.baseUrl + "update-house-type";
            axios.post(urls, data)
                .then(function (e) {
                    $('#updateHtModal').modal('hide');
                    house_type.getPageInfo();
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
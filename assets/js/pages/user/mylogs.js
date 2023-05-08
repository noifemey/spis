Vue.use(VueTables.ClientTable);
var app = new Vue({
    el: '#mylogs',
    data: {
        list_of_users :[],
        table: {
            columns: [  "id",
                        "luid",	
                        "lunam",	
                        "laction",
                        "ldesc",
                        "day",
                        "datetime",
                        "datedesc" ],
            data: {
                list: []
            },
            options: {
                headings: {
                    id : "ID",
                    lunam : "Full Name",
                },
                sortIcon: {
                  base : 'fa',
                  is: 'fa-sort',
                  up: 'fa-sort-asc',
                  down: 'fa-sort-desc'
                },
                sortable: ["id", "lunam", "ldesc"],
                perPage: 100,
                perPageValues: [100,500,1000]
            }
        },
        provList: [],
        loading: false,
        form:{
            from:"",
            to:"",
            uid:0
        }
    }, methods: {
        getPageInfo: function () {
            var data = frmdata(this.form);
            var urls = window.App.baseUrl + "getmyLogs";
            axios.post(urls,data)
                .then(function (e) {
                    app.table.data.list = e.data.data;
                    app.list_of_users = e.data.listOfUsers;               
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 

    }, computed: {

    }, watch: {

    },
    mounted: function () {
        this.getPageInfo();
    },
})
Vue.use(VueTables.ClientTable);
var app = new Vue({
    el: '#userlogs',
    data: {
        table: {
            columns: [
                //"id",
                //"luid",
                "lunam",
                "laction",
                "ldesc",
                "day",
                "datetime",
                "datedesc"],
            data: {
                list: []
            },
            options: {
                headings: {
                    // id: "ID",
                    lunam: "Full Name",
                    laction:"Action",
                    ldesc:"Description",
                    date:"Date",
                    datetime:"Time",
                    datedesc:"Hours"
                },
                sortIcon: {
                    base: 'fa',
                    is: 'fa-sort',
                    up: 'fa-sort-asc',
                    down: 'fa-sort-desc'
                },
                sortable: ["lunam", "ldesc","datedesc","datetime"],
                perPage: 100,
                perPageValues: [100, 500, 1000],
                filterable:true
            }
        },
        provList: [],
        loading: false,
        formFilter: {
            fromUser: "",
            toUser: "",
            user: "",
        },
        listOfUser:[],
    }, methods: {
        getPageInfo: function () {
            $('.VueTables__search-field label').css('display','none');
            let date = new Date().toISOString().slice(0, 10);
            if (this.formFilter.fromUser == '') {
                this.formFilter.fromUser = date;
            }
            if (this.formFilter.toUser == '') {
                this.formFilter.toUser = date;
            }
            showloading();
            var data = frmdata(this.formFilter);
            var urls = window.App.baseUrl + "get-user-Logs";
            axios.post(urls, data)
                .then(function (e) {
                    swal.close();
                    app.table.data.list = e.data.data;
                })
                .catch(function (error) {
                    console.log(error)
                });
        },
        checkFromDate(e) {
            let current_date = new Date().toISOString().slice(0, 10);
            let input_date = e.target.value;
            if (input_date > current_date) {
                Swal.fire('Invalid From Date');
                this.formFilter.fromUser = current_date;
            } else {
                this.getPageInfo();
            }
        },
        checkToDate(e) {
            let current_date = new Date().toISOString().slice(0, 10);
            let input_date = e.target.value;
            if (input_date > current_date) {
                Swal.fire('Invalid To Date', '', 'warning');
                this.formFilter.toUser = current_date;
            }
            else if (this.formFilter.fromUser > input_date) {
                Swal.fire('Date Must be Greater than To Date');
                this.formFilter.toUser = current_date;
            } else {
                this.getPageInfo();
            }
        },
        getUsers() {
            var urls = window.App.baseUrl + "get-all-user-for-logs";
            axios.post(urls)
                .then(function (e) {
                    app.listOfUser = e.data.response;
                    console.log(app.listOfUser);
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
        this.getUsers();
    },
})
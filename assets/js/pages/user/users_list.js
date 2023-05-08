Vue.use(VueTables.ClientTable);
var app = new Vue({
    el: '#userList',
    data: {
        activenav: '',
        form: {
            name: "",
        },
        update_data: {
            id: "",
            username: "",
            fname: "",
            mname: "",
            lname: "",
            emailadd: "",
            position: "",
            province: "",
            role: "",
        },
        activate_data: {
            id: "",
            active_status: '',
        },
        table: {
            columns: ["id",
                "fullname",
                "username",
                "emailadd",
                "role",
                "activate",
                "action",
                "Reset"
            ],
            data: {
                list: []
            },
            options: {
                headings: {
                    id: "ID",
                    emailadd: "Email Address",
                },
                sortIcon: {
                    base: 'fa',
                    is: 'fa-sort',
                    up: 'fa-sort-asc',
                    down: 'fa-sort-desc'
                },
                sortable: ["id", "fullname", "activate"]
            }
        },
        provList: [],
        loading: false,
    }, methods: {

        save: function () {
            this.loading = true;
            var data = frmdata(this.form);
            var urls = window.App.baseUrl + "save-house-type";
            axios.post(urls, data)
                .then(function (e) {
                    app.getPageInfo();
                    app.resetFields();
                    $('#addHtModal').modal('hide');
                    methods.toastr('success', 'Success', e.data.message);
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-user-list";
            axios.post(urls)
                .then(function (e) {
                    app.table.data.list = e.data.users;
                    app.provList = e.data.provinces;
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        updateUser: function (data) {
            console.log(data)
            this.update_data.id = data.id;
            this.update_data.username = data.username;
            this.update_data.fname = data.fname;
            this.update_data.mname = data.mname;
            this.update_data.lname = data.lname;
            this.update_data.emailadd = data.emailadd;
            this.update_data.position = data.position;
            this.update_data.role = data.role;
            this.update_data.province = data.province;
        },

        update_user_form: function () {
            this.loading = true;
            var data = frmdata(this.update_data);
            var urls = window.App.baseUrl + "update-user";
            axios.post(urls, data)
                .then(function (e) {
                    $('#updateUserModal').modal('hide');
                    app.getPageInfo();
                    methods.toastr('success', 'Success', e.data.message);

                })
                .catch(function (error) {
                    console.log(error)
                });
        },
        delete_user_form: function () {
            this.loading = true;
            var data = frmdata(this.update_data);
            var urls = window.App.baseUrl + "delete-user";
            axios.post(urls, data)
                .then(function (e) {
                    $('#deleteUserModal').modal('hide');
                    app.getPageInfo();
                    methods.toastr('success', 'Success', e.data.message);

                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        activateUser: function (data) {
            this.activate_data.id = data.id;
            this.activate_data.active_status = !data.active_status;
        },

        activate_user_form: function () {
            this.loading = true;
            var data = frmdata(this.activate_data);
            var urls = window.App.baseUrl + "activate-user";
            axios.post(urls, data)
                .then(function (e) {
                    $('#activateUserModal').modal('hide');
                    app.getPageInfo();
                    methods.toastr('success', 'Success', e.data.message);

                })
                .catch(function (error) {
                    console.log(error)
                });
        },
        resetFields() {
            this.form.name = "";
        },
        confirmReset(e) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Reset it!',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.value) {
                    var data = frmdata(e);
                    var urls = window.App.baseUrl + "reset-user-password";
                    axios.post(urls, data)
                        .then(function (e) {
                            if (e.data.success) {
                                Swal.fire(
                                    'Reset!',
                                    'Your Default Password is dswd1234',
                                    'success'
                                )
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    '',
                                    'error'
                                )
                            }
                        })
                        .catch(function (error) {
                            console.log(error)
                        });

                }
            })

        }

    }, computed: {

    }, watch: {

    },
    mounted: function () {
        this.getPageInfo();
    },
})
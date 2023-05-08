Vue.use(VueTables.ClientTable);
var app = new Vue({
    el: '#requestform',
    data: {
        table: {
            columns: ["id",
                "req_by",
                "req_date",
                "modulename",
                "issue_subject",
                "assign_to_name",
                "duedate",
                "prioritylvl",
                "status",
            ],
            data: {
                list: []
            },
            options: {
                headings: {
                    id: "ID",
                    req_by: "Requested By",
                    req_date: 'Requested Date',
                    modulename: 'Module',
                    issue_subject: 'Subject',
                    assign_to: 'Assign To',
                    duedate: 'Due Date',
                    prioritylvl: 'Priority',
                },
                sortIcon: {
                    base: 'fa',
                    is: 'fa-sort',
                    up: 'fa-sort-asc',
                    down: 'fa-sort-desc'
                },
                sortable: ["id", "req_by", "req_date"],
                filterable: true //TRUE LATER
            }
        },
        addNewRequest: {
            req_by: '',
            issue_subject: '',
            modulename: '',
            prioritylvl: '',
            assign_to: '',
            duedate: '',
            issue_details: '',
            duedate: '',
            prioritylvl: '',
            status: 'For Action'
        },
        userRole: '',
        searchKeys: {
            keyStatus: '',
            keyDate: '',
        },
        updateForm:{
            updateId:'',
            updateSubject:'',
            updateReqBy:'',
            updateSystemModule:'',
            updatePriority:'',
            updateReqDetails:'',
            updateStatus:'',
            updateActionTaken:'',
        },
        listOfUsers: [],
    }, methods: {
        updateAction(e){
            
            app.updateForm = {
                updateId:e.id,
                updateSubject:e.issue_subject,
                updateReqBy:e.req_by,
                updateSystemModule:e.modulename,
                updatePriority:e.prioritylvl,
                updateReqDetails:e.issue_details,
                updateStatus:e.status,
                updateActionTaken:e.status,
            }
        },
        updateRequest(){
           
            var form = methods.formData(app.updateForm);
            var urls = window.App.baseUrl + "update-request-form";
            axios.post(urls, form)
                .then(function (e) {
                    
                    if (e.data.success) {
                        Swal.fire(
                            'Success',
                            'Request Form Updated!',
                            'success'
                        )
                        $('#updateActionModal').modal('hide');
                        app.clearModal();
                        app.getAllRequest();
                    } else {
                        Swal.fire(
                            'Failed',
                            '',
                            'error'
                        )
                    }
                })
        },
        filterStatus() {
            var form = methods.formData(app.searchKeys);
            var urls = window.App.baseUrl + "get-list-users-by-status";
            axios.post(urls, form)
                .then(function (e) {
                    app.table.data.list = e.data.requests;
                })
        },
        filterDate() {
            var form = methods.formData(app.searchKeys);
            var urls = window.App.baseUrl + "get-list-users-by-date";
            axios.post(urls, form)
                .then(function (e) {
                    app.table.data.list = e.data.requests;
                })
        },
        selectUser(id) {
            app.addNewRequest.req_by = id;
        },
        getListOfUsers() {
            var urls = window.App.baseUrl + "get-list-users";
            axios.post(urls)
                .then(function (e) {
                  
                    app.listOfUsers = e.data.users;
                })
        },
        getAllRequest() {
            var urls = window.App.baseUrl + "get-all-request";
            axios.post(urls)
                .then(function (e) {
                    app.table.data.list = e.data.requests;
                    app.addNewRequest.req_by = e.data.sesID;
                    app.userRole = e.data.role;
                })
        },
        submitRequest() {
            var form = methods.formData(app.addNewRequest);
            var urls = window.App.baseUrl + "add-new-request";
            axios.post(urls, form)
                .then(function (e) {
                    if (e.data.success) {
                        Swal.fire(
                            'Success',
                            'Request Form Added!',
                            'success'
                        )
                        $('#addNewRequest').modal('hide');
                        app.clearModal();
                        app.getAllRequest();
                    } else {
                        Swal.fire(
                            'Failed',
                            '',
                            'error'
                        )
                    }
                })
        },
        clearModal() {
            app.addNewRequest = {
                issue_subject: '',
                modulename: '',
                prioritylvl: '',
                assign_to: '',
                duedate: '',
                issue_details: '',
                duedate: '',
                prioritylvl: '',
            }

            app.updateForm = {
                updateId:'',
                updateSubject:'',
                updateReqBy:'',
                updateSystemModule:'',
                updatePriority:'',
                updateReqDetails:'',
                updateStatus:'',
                updateActionTaken:'',
            }
        }
    },
    mounted: function () {
        this.getAllRequest();
    },
})
Vue.use(VueTables.ClientTable);
var app = new Vue({
    el: '#user_profile',
    data: {
        activenav: '',
        form: {
            username: "",
            password: "",
            c_password: "",
            fname: "",
            mname: "",
            lname: "",
            emailadd: "",
            position: "",
            province: "",
        },
        register_form: {
            username: "",
            password: "",
            c_password: "",
            fname: "",
            mname: "",
            lname: "",
            emailadd: "",
            position: "",
            province: "",
        },
        update_data: {
            id: "",
            name: "",
            status: 0,
        },
        provList: [],
        loading: false,
    }, methods: {

        save: function () {
            this.loading = true;

            if(this.form.password != "" && this.form.password !== this.form.c_password){
                
                methods.toastr('warning',"Password doesn't match",'');
                return false;
            }

            var data = frmdata(this.form);
            var urls = window.App.baseUrl + "save-user-profile";
            axios.post(urls, data)
                .then(function (e) {
                    app.getPageInfo();
                    app.resetFields();
                    methods.toastr('success','Success',e.data.message);
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-user-profile";
            axios.post(urls)
                .then(function (response) {
                    if(response.data.user != null){             
                        app.form.username = response.data.user.username;
                        app.form.fname = response.data.user.fname;
                        app.form.lname = response.data.user.lname;
                        app.form.mname = response.data.user.mname;
                        app.form.emailadd = response.data.user.emailadd;
                        app.form.email = response.data.user.email;
                        app.form.position = response.data.user.position;
                        app.form.province = response.data.user.province;
                    }
                    app.provList = response.data.provinces;                 
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 

        register: function () {
            this.loading = true;

            if(this.register_form.password != "" && this.register_form.password !== this.register_form.c_password){
                
                methods.toastr('warning',"Password doesn't match",'');
                return false;
            }

            var data = frmdata(this.register_form);
            var urls = window.App.baseUrl + "save-register";
            axios.post(urls, data)
                .then(function (e) {
                    app.getPageInfo();
                    app.resetFields();
                    methods.toastr('success','Success',e.data.message);
                    window.location.href = window.App.baseUrl + "/Login/activate";
                })
                .catch(function (error) {
                    console.log(error)
                });
        },


        resetFields(){
            this.form.password = "";
            this.form.c_password = "";

            this.register_form.password = "";
            this.register_form.c_password = "";
        },


    }, computed: {

    }, watch: {

    },
    mounted: function () {
        this.getPageInfo();
    },
})
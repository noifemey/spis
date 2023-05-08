Vue.use(VueTables.ClientTable);
var sign = new Vue({
    el: '#Signatories',
    data: {
        activenav: '',
        form: {
            name: "",
        },
        signatories_cap:{},
        signatories_masterlist:{},
        loading: false,
    }, methods: {

        getSignatoriesInfo: function () {
            var urls = window.App.baseUrl + "get-signatories";
            axios.post(urls)
                .then(function (e) {
                    sign.signatories_cap = e.data.data[0];
                    sign.signatories_masterlist = e.data.data[1];

                    console.log(sign.signatories[0])
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 
        update_signatories_form: function (type) {
            var data =(type == 1)? frmdata(this.signatories_cap):frmdata(this.signatories_masterlist);
            this.loading = true;
            var urls = window.App.baseUrl + "update-signatories";
            axios.post(urls, data)
                .then(function (e) {
                    sign.getSignatoriesInfo();
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
        this.getSignatoriesInfo();
    },
})
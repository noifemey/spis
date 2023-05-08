Vue.use(VueTables.ClientTable);
var total_served = new Vue({
    el: '#totalServed',
    data: {
        activenav: '',
        search: {
            year: "",
            period: "",
            month: "",
        },
        served_data:[],
        region_served:[],
        loading: true,
    }, methods: {

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-total-served";
            axios.post(urls)
                .then(function (e) {
                    total_served.served_data = e.data.served;
                    total_served.region_served = e.data.region_served;
                    total_served.loading = false;
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 

        searchList(){
            if(this.search.month != ""){
                this.getMonthlyTotalServed();
            } else {
                this.getTotalServed()
            }
        },
        getTotalServed(){
            this.served_data = [];
            this.loading = true;
            var data = frmdata(this.search);
            var urls = window.App.baseUrl + "get-total-served";
            axios.post(urls,data)
                .then(function (e) {
                    total_served.served_data = e.data.served;
                    total_served.region_served = e.data.region_served;
                    total_served.loading = false;
                })
                .catch(function (error) {
                    console.log(error)
                });
        },
        getMonthlyTotalServed(){
            this.served_data = [];
            this.loading = true;
            var data = frmdata(this.search);
            var urls = window.App.baseUrl + "get-monthly-total-served";
            axios.post(urls,data)
                .then(function (e) {
                    total_served.served_data = e.data.month_served;
                    total_served.region_served = e.data.month_region_served;
                    total_served.loading = false;
                })
                .catch(function (error) {
                    console.log(error)
                });
        },
        getAccomplishment(paid,target){
            
            var i_paid = parseFloat(paid.replace(/,/g, ''));
            var i_target = parseFloat(target.replace(/,/g, ''));

            var accomp = (target != 0) ? (i_paid / i_target) * 100 : 0.00;
            var n = accomp.toFixed(2);

            var response = n + "%";
            return response;

        },

        sendData(){
            swal.fire({
                title: 'Warning',
                text: "Are you sure you want to send Served Data to googlesheet?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                confirmButtonText: 'Yes, change status!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: false	
                }).then((result) => {
                if (result.value) {
                    showloading();
                    var urls = window.App.baseUrl +"send-served-data";
                    var datas = {"data" : total_served.served_data, "search" : total_served.search};
                    var formData = methods.formData(datas);
                    axios.post(urls, formData).then(function (e) {
                        if(e.data.success){
                            swal.close();
                            swal.fire('Info',e.data.message,'success');
                        }else{
                            swal.fire('Error',e.data.message,'error');
                        }
                    })
                } else if ( result.dismiss === Swal.DismissReason.cancel) {
                    swal.fire('Cancelled','Action Cancelled','error')
                }
            })

        },

    }, computed: {

    }, watch: {

    },
    mounted: function () {
        this.getPageInfo();
    },
})
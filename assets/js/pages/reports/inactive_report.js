Vue.use(VueTables.ClientTable);
var total_inactive = new Vue({
    el: '#inactiveReport',
    data: {
        activenav: '',
        search: {
            year: "",
            period: "",
            month: "",
        },
        inactive_data:[],
        region_served:[],
        reasons:[],
        loading: true,
    }, methods: {

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-total-inactive";
            axios.post(urls)
                .then(function (e) {
                    total_inactive.reasons = e.data.reasons;
                    total_inactive.inactive_data = e.data.inactive;
                    total_inactive.region_inactive = e.data.region_inactive;
                    total_inactive.loading = false;
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 
        getTotalInactive(){
            if(total_inactive.search.year == "" && (total_inactive.search.period != "" || total_inactive.search.month != "")){
                swal.fire('Error',"Please Enter Year",'error');
            }else{

                this.inactive_data = [];
                this.loading = true;
                var data = frmdata(this.search);
                var urls = window.App.baseUrl + "get-total-inactive";
                axios.post(urls,data)
                    .then(function (e) {
                        total_inactive.inactive_data = e.data.inactive;
                        total_inactive.region_inactive = e.data.region_inactive;
                        total_inactive.reasons = e.data.reasons;
                        total_inactive.loading = false;
                    })
                    .catch(function (error) {
                        console.log(error)
                    });
            }
        },
        getNumberFormat(x){ 

            if(typeof x !== 'undefined'){              
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");          
            } else {
                return '';
            }           
        }

    }, computed: {

    }, watch: {

    },
    mounted: function () {
        this.getPageInfo();
    },
})
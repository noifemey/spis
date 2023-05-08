Vue.use(VueTables.ClientTable);
var total_unclaimed = new Vue({
    el: '#totalUnclaimed',
    data: {
        activenav: '',
        search: {
            year: "",
            period: "",
        },
        unclaimed_data:[],
        region_unclaimed:[],
        loading: true,
    }, methods: {

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-total-unclaimed";
            axios.post(urls)
                .then(function (e) {
                    total_unclaimed.unclaimed_data = e.data.unclaimed;
                    total_unclaimed.region_unclaimed = e.data.region_unclaimed;
                    total_unclaimed.loading = false;
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 

        searchList(){
            this.unclaimed_data = [];
            this.loading = true;
            var data = frmdata(this.search);
            var urls = window.App.baseUrl + "get-total-unclaimed";
            axios.post(urls,data)
                .then(function (e) {
                    total_unclaimed.unclaimed_data = e.data.unclaimed;
                    total_unclaimed.region_unclaimed = e.data.region_unclaimed;
                    total_unclaimed.loading = false;
                })
                .catch(function (error) {
                    console.log(error)
                });
        },

        getAccomplishment(paid,target){
            
            var i_paid = parseFloat(paid.replace(/,/g, ''));
            var i_target = parseFloat(target.replace(/,/g, ''));

            var accomp = (i_paid / i_target) * 100;
            var n = accomp.toFixed(2);

            var response = n + "%";
            return response;

        },

    }, computed: {

    }, watch: {

    },
    mounted: function () {
        this.getPageInfo();
    },
})
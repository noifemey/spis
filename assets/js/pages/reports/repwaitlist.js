Vue.use(VueTables.ClientTable);
var app = new Vue({
    el: '#repwaitlist_view',
    data: {
        prov_data:[],
        region_data:[],
        loading: true,
    }, methods: {

        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-repwaitlistMonitoring";
            axios.post(urls)
                .then(function (e) {
                    app.prov_data = e.data.prov_data;
                    app.region_data = e.data.region_data;
                    app.loading = false;
                })
                .catch(function (error) {
                    console.log(error)
                });
        }, 
        getNumberFormat(x){ 
            if(typeof x !== 'undefined'){        
              return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");      
            } else {
              return '';
            }     
          },
        getactual( waitlist, forrep, $current){
            var x = waitlist - forrep;
            if(x < 0){        
                return $current;      
              } else {
                return $current + x;
              }     
        },
        gettargetVariance(target, waitlist, forrep, $current){
            var x = waitlist - forrep;
            var actual = 0;
            if(x < 0){        
                actual = $current;      
              } else {
                actual = $current + x;
              }
            
             return actual - target;
        }
    }, computed: {

    }, watch: {

    },
    mounted: function () {
        this.getPageInfo();
    },
})
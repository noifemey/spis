Vue.use(VueTables.ClientTable);
var wBreakdown = new Vue({
    el: '#waitlistBreakdown',
    data: {
        waitlist_data:[],
        total_waitlist:[],
        loading: true,
    }, methods: {
        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-total-waitlist";
            axios.post(urls)
                .then(function (e) {
                    wBreakdown.waitlist_data = e.data.data;
                    wBreakdown.total_waitlist = e.data.total_data;
                    wBreakdown.loading = false;
                })
                .catch(function (error) {
                    console.log(error)
                });
        },
    },
    mounted: function () {
        this.getPageInfo();
    },
})
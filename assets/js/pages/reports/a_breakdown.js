Vue.use(VueTables.ClientTable);
var aBreakdown = new Vue({
    el: '#activeBreakdown',
    data: {
        active_data:[],
        total_active:[],
        loading: true,
    }, methods: {
        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-total-active";
            axios.post(urls)
                .then(function (e) {
                    aBreakdown.active_data = e.data.data;
                    aBreakdown.total_active = e.data.total_data;
                    aBreakdown.loading = false;
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
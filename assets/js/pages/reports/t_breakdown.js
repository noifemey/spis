Vue.use(VueTables.ClientTable);
var d = new Date();
var cur_year = d.getFullYear();
var cur_sem = Math.floor((d.getMonth() + 6) / 6);

var tBreakdown = new Vue({
    el: '#targets',
    data: {
        search: {
            year: cur_year,
            semester: cur_sem,
        },
        target_data:[],
        region_target:[],
        loading: true,
    }, methods: {
        getPageInfo: function () {
            var urls = window.App.baseUrl + "get-total-target";
            var data = frmdata(this.search);
            axios.post(urls,data)
                .then(function (e) {
                    tBreakdown.target_data = e.data.data;
                    tBreakdown.region_target = e.data.total_data;
                    tBreakdown.loading = false;
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
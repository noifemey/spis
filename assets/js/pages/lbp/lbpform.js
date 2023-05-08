
Vue.use(VueTables.ServerTable);
Vue.component('v-select', VueSelect.VueSelect);
if ($('#lbp_index').length) {
	// var today = new Date();
	// var date_today = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();

	var app = new Vue({
		el: '#lbp_index',
		data: {
			location: {
				prov_names: [],
				mun_names: [],
				bar_names: [],
				provinces: [],
				municipalities: [],
				barangays: [],
			},
			batch:{
				amount : 3000,
				date_receive : new Date().toISOString().slice(0,10),
			},
			search: {
				prov_code: '',
				mun_code: '',
				year: '',
				period: '',
				liquidation: 0,
				unpaid: false,
				batch: 1,
			},
		}, methods: {   
			generatelbpform(){
				if(app.search.prov_code == ""){
					swal.fire('Error',"Please Enter Required Fields[province]",'error');
				}else{
					var urls = window.App.baseUrl +"export-lbp-form?prov_code="+app.search.prov_code+"&mun_code="+app.search.mun_code;
					window.open(urls, '_blank');
				}
			},
			exportblank(){
				var urls = window.App.baseUrl + "export-blank-lbp";
				window.open(urls, '_blank');
			},
			//GET Libraries
			getAllLocation() {
				var urls = window.App.baseUrl + 'get-all-location';
				axios.get(urls).then(function (e) {
					console.log(e.data);
					app.location.prov_names = e.data.provinces;
					app.location.mun_names = e.data.municipalities;
					app.location.bar_names = e.data.barangays;
					app.location.provinces = e.data.provinces;
				})
			},
			getLocation(type = 'prov_code', val = "") {
				if (type == 'mun_code') {
					app.search.mun_code = '';
					app.location.barangays = [];
					app.location.municipalities = app.location.mun_names[val];
				} else if(type == 'bar_code') {
					app.search.bar_code = '';
					app.location.barangays = app.location.bar_names[val];
				} else{
					app.location.provinces = app.location.prov_names;
				}
			},
	
			ClearSearch(){
				app.search.prov_code = '';
				app.search.mun_code = '';
				app.search.bar_code = '';
				app.search.gender = '';
				app.search.status = '';
				app.location.municipalities = [];
				app.location.barangays = [];
			},
			////// END EVENTS ///////////////
		},
		mounted: function () {
			this.getAllLocation();
		},
	})
}
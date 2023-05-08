
Vue.use(VueTables.ServerTable);
Vue.component('v-select', VueSelect.VueSelect);
if ($('#summary_index').length) {
	// var today = new Date();
	// var date_today = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();

	var app = new Vue({
		el: '#summary_index',
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
				type: 'all',
				no_sp: '0',
				forrep_ex : '0',
				input_claimant: "MYRNA B. BERSALONA",
				input_supervisor: "CONCEPCION E. NAVALES",
				input_acctng: "WILBOURN B. BACOLONG",
				input_disbursing: "MYRNA B. BERSALONA",
				input_disbursingposi: "SWO III",
			},
		}, methods: {   
			getallPayroll() {
				this.payrollList = [];
				this.loading = true;
				var data = frmdata(this.search);
				var urls = window.App.baseUrl + "get-all-liquidation";
				axios.post(urls,data)
					.then(function (e) {
						app.payrollList = e.data.data;
						app.total_target = e.data.total_target;
						app.total_paid = e.data.total_paid;
						app.total_unpaid = e.data.total_unpaid;
						app.loading = false;
					})
					.catch(function (error) {
						console.log(error)
					});
			},
			exportCE(){
				// showloading();
				// var formData = frmdata(app.search);
				// var urls = window.App.baseUrl +"dl-payroll-list";
				// axios.post(urls, formData).then(function (e) {
				// 	if(e.data.success){
				// 		swal.close();
				// 		swal.fire('Info',e.data.message,'success');
				// 		window.open(e.data.data, '_blank');
				// 	}else{
				// 		swal.fire('Error',e.data.data,'error');
				// 	}
				// })

				if(app.search.prov_code == "" || app.search.year == "" || app.search.period == ""){
					swal.fire('Error',"Please Enter Required Fields[province, Year , Period]",'error');
				}else{
					var urls = window.App.baseUrl +"export-ce-active?prov_code="+app.search.prov_code+"&mun_code="+app.search.mun_code+"&year="+app.search.year+"&period="+app.search.period+"&liquidation="+app.search.liquidation;
					window.open(urls, '_blank');
				}
			},
			exportUnpaidCE(){
				if(app.search.prov_code == "" || app.search.year == "" || app.search.period == ""){
					swal.fire('Error',"Please Enter Required Fields[province, Year , Period]",'error');
				}else{
					//var urls = window.App.baseUrl +"export-ce-unpaid?prov_code="+app.search.prov_code+"&mun_code="+app.search.mun_code+"&year="+app.search.year+"&period="+app.search.period+"&liquidation="+app.search.liquidation;
					var urls = window.App.baseUrl +"Payroll/exportReplacement?prov_code="+app.search.prov_code+"&mun_code="+app.search.mun_code+"&year="+app.search.year+"&period="+app.search.period+"&liquidation="+app.search.liquidation;
					window.open(urls, '_blank');
				}
			},
			generateCAP(){
				app.search.unpaid = false;
				if(app.search.prov_code == "" || app.search.year == "" || app.search.period == ""){
					swal.fire('Error',"Please Enter Required Fields[province, Year , Period]",'error');
				}else{
					var urls = window.App.baseUrl +"generate-cap-active?prov_code="+app.search.prov_code+"&mun_code="+app.search.mun_code+"&year="+app.search.year+"&period="+app.search.period+"&liquidation="+app.search.liquidation+"&unpaid="+app.search.unpaid;
					window.open(urls, '_blank');
				}
			},
			generateCAPUnpaid(){
				app.search.unpaid = true;
				if(app.search.prov_code == "" || app.search.year == "" || app.search.period == ""){
					swal.fire('Error',"Please Enter Required Fields[province, Year , Period]",'error');
				}else{
					var urls = window.App.baseUrl +"Payroll/generateUnpaidCAP?prov_code="+app.search.prov_code+"&mun_code="+app.search.mun_code+"&year="+app.search.year+"&period="+app.search.period+"&liquidation="+app.search.liquidation+"&unpaid="+app.search.unpaid;
					//var urls = window.App.baseUrl +"generate-cap-active?prov_code="+app.search.prov_code+"&mun_code="+app.search.mun_code+"&year="+app.search.year+"&period="+app.search.period+"&liquidation="+app.search.liquidation+"&unpaid="+app.search.unpaid;
					window.open(urls, '_blank');
				}
			},
			generatePayrollReports(){
				showloading();
				if(app.search.prov_code == "" || app.search.mun_code == ""){
					swal.fire('Error',"Please Enter Required Fields",'error');
				}else{
					var urls = window.App.baseUrl +"Liquidation/generatePayrollReports?prov_code=" + app.search.prov_code;
					urls += "&mun_code=" + app.search.mun_code;
					urls += "&year=" + app.search.year;
					urls += "&period=" + app.search.period;
					urls += "&type=" + app.search.type;
					urls += "&claimant=" + app.search.input_claimant;
					urls += "&supervisor=" + app.search.input_supervisor;
					urls += "&acctng=" + app.search.input_acctng;
					urls += "&disbursing=" + app.search.input_disbursing;
					urls += "&disbursingposi=" + app.search.input_disbursingposi;
					urls += "&no_sp=" + app.search.no_sp;
					urls += "&forrep_ex=" + app.search.forrep_ex;
					window.open(urls, '_blank');
				}
				swal.close();
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
			getperiodname(period) {
				if(period == 5){
					return "1st Semester";
				}else if(period == 6){
					return "2nd Semester";
				}else if(period == 1){
					return "1st Quarter";
				}else if(period == 2){
					return "2nd Quarter";
				}else if(period == 3){
					return "3rd Quarter";
				}else if(period == 4){
					return "4th Quarter";
				}
			},
			getPaymentStatus() {
				if(this.search.liquidation === 0){
					return "UNPAID List";
				}else if(this.search.liquidation === 1){
					return "PAID List";
				}else{
					return "";
				}
			},
			getprovname(prov_code){
				var prov_name = "";
				if(prov_code !== ""){
					app.location.prov_names.forEach(prov => {
						if(prov.prov_code == prov_code){
							prov_name = prov.prov_name;
						}
					});
				}
				return prov_name;
			},
			getmunname(prov_code,mun_code){
				var mun_name = "";
				if(mun_code !== ""){
					if(app.location.mun_names.hasOwnProperty(prov_code)){
						var munlist = app.location.mun_names[prov_code];
						munlist.forEach(mun => {
							if(mun.mun_code == mun_code){
								mun_name = mun.mun_name;
							}
						});
					}
				}
				return mun_name;
			},
			getbarname(mun_code,bar_code){
				var bar_names = "";
				if(app.location.bar_names.hasOwnProperty(mun_code)){
					var barlist = app.location.bar_names[mun_code];
					barlist.forEach(bar => {
						if(bar.bar_code == bar_code){
							bar_names = bar.bar_name;
						}
					});
				}
				return bar_names;
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
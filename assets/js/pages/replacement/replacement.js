
Vue.use(VueTables.ServerTable);
Vue.component('v-select', VueSelect.VueSelect);
if ($('#rep_index').length) {
	// var today = new Date();
	// var date_today = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();

	var app = new Vue({
		el: '#rep_index',
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
				bar_code: '',
				year: '',
				period: '',
				liquidation: 3,
				claimant: 0
			},
			liquidationText : "",
			loading : false,
			payrollList: [],
			waitlist: [],
			total_target : 0,
			total_paid : 0,
			total_unpaid : 0,
			selected: [],
			selectedData: [],
			selectAll: false,

			selectedTransfer: [],
			selectedDataTransfer: [],
			selectAllTransfer: false,

			searchCol: '',
			tblFilter : '',
		}, methods: {   
			getallPayroll() {		
				this.payrollList = [];
				this.waitlist = [];

				this.selected = [];
				this.selectedData = [];
				this.selectAll = false;
	
				this.selectedTransfer = [];
				this.selectedDataTransfer = [];
				this.selectAllTransfer = false;


				this.loading = true;
				var data = frmdata(this.search);
				var urls = window.App.baseUrl + "get-replacement";
				axios.post(urls,data)
					.then(function (e) {
						app.selected = [];
						app.selectAll = false;
						app.payrollList = e.data.unpaid_data;
						app.waitlist = e.data.waitlist_data;
						app.loading = false;
					})
					.catch(function (error) {
						console.log(error)
					});
			},
			ShowUnpaid() {
				this.search.liquidation = 0;
				this.liquidationText = "PAID";
			},
			ShowPaid() {
				this.search.liquidation = 1;
				this.liquidationText = "UNPAID";
			},
			generateCertificate(){
				if(app.search.prov_code == ''){
					swal.fire('Error','No filter is selected. Please select a location to filter.','error');
				} else {
					window.open(window.App.baseUrl+"Replacement/generateCertificate?prov_code="+app.search.prov_code+"&mun_code="+app.search.mun_code+"&bar_code="+app.search.bar_code+"&year="+app.search.year, "_blank");
				}
			},
			exportReplacment(){
				if(app.search.prov_code == '' && app.search.year == '' ){
					swal.fire('Error','No filter is selected.','error');
				} else {
					if(app.search.year == '' ){
						swal.fire('Error','No year is selected.','error');
					} else {
						var urls = window.App.baseUrl+"Replacement/exportReplacement?prov_code="+app.search.prov_code+"&mun_code="+app.search.mun_code+"&bar_code="+app.search.bar_code+"&year="+app.search.year;
							window.open(urls, '_blank');	
					}
				}
					
			},
			keymonitor() {
				// var value = this.tblFilter;
				// $("#myTable tr").filter(function() {
				//   $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				// });

				 // Declare variables
				var input, filter, table, tr, td, i, txtValue;
				input = document.getElementById("myInput");
				filter = input.value.toUpperCase();
				table = document.getElementById("myTable");
				tr = table.getElementsByTagName("tr");

				// Loop through all table rows, and hide those who don't match the search query
				for (i = 0; i < tr.length; i++) {
					td = tr[i].getElementsByTagName("td")[3];
					if (td) {
						txtValue = td.textContent || td.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
						} else {
							tr[i].style.display = "none";
						}
					}
				}

			},
			select() {
				this.selected = [];
				if (!this.selectAll) {
					for (let i in this.payrollList) {
						if(this.payrollList[i].spstatus == 'ForReplacement'  && this.payrollList[i].replacer_refcode != ''){
							this.selected.push(this.payrollList[i].spid);
							this.selectedData.push(this.payrollList[i]);
						}
					}
				}
			},
			selectTransfer() {
				this.selectedTransfer = [];
				if (!this.selectAllTransfer) {
					for (let i in this.payrollList) {
						if(this.payrollList[i].spstatus == 'Inactive'){
							this.selectedTransfer.push(this.payrollList[i].spid);
							this.selectedDataTransfer.push(this.payrollList[i]);
						}
					}
				}
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
			getReplacementReason(){			
				var urls = window.App.baseUrl + 'get-all-ReplacementReason';
				axios.get(urls, {
					params: {}
				}).then(function (e) {
					app.ReplacementReason = e.data;
				})
			},
			//END Get Libraries
			bulkreplace(){			
				var textmsg = "Are sure you want to replace the selected rows?";
				swal.fire({
					title: 'Warning',
					text: textmsg,
					icon: 'warning',
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					confirmButtonText: 'Yes, Replace!',
					cancelButtonText: 'No, cancel!',
					buttonsStyling: false	
					}).then((result) => {
					if (result.value) {
						showloading();
						var urls = window.App.baseUrl +"bulk-replace-unpaid";
						var datas = {"data" : app.selectedData};
						var formData = methods.formData(datas);
						axios.post(urls, formData).then(function (e) {
							if(e.data.success){
								app.getallPayroll();
								swal.close();
								swal.fire('Info',e.data.message,'success');
							}else{
								swal.fire('Error',e.data.message,'error');
							}
						})
						//var formData = frmdata(data);
						// axios.post(urls, formData).then(function (e) {
						// 	console.log(e.data.message);
							
						// 	swal.close();
						// 	swal.fire('Info',e.data.message,'success');
						// 	// if(e.data.success){
						// 	// 	app.getallPayroll();
						// 	// 	swal.close();
						// 	// 	swal.fire('Info',e.data.message,'success');
						// 	// }else{
						// 	// 	swal.fire('Error',e.data.message,'error');
						// 	// }
						// })
					} else if ( result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled','Action Cancelled','error')
					}
				})
			},
			replace(data,index){			
				//Individual Save 
				var textmsg = "Are sure you want to replace " + data.fullname + "to " + data.replacer_name;

				swal.fire({
					title: 'Warning',
					text: textmsg,
					icon: 'warning',
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					confirmButtonText: 'Yes, Replace!',
					cancelButtonText: 'No, cancel!',
					buttonsStyling: false	
					}).then((result) => {
					if (result.value) {
						showloading();
						var urls = window.App.baseUrl +"replace-unpaid";
						var formData = frmdata(data);
						axios.post(urls, formData).then(function (e) {
							if(e.data.success){
								app.getallPayroll();
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
			bulkTransfer(){			
				var textmsg = "Are sure you want to Transfer payment the selected rows?";
				swal.fire({
					title: 'Warning',
					text: textmsg,
					icon: 'warning',
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					confirmButtonText: 'Yes, Transfer!',
					cancelButtonText: 'No, cancel!',
					buttonsStyling: false	
					}).then((result) => {
					if (result.value) {
						showloading();
						var urls = window.App.baseUrl +"bulk-transfer-unpaid";
						var datas = {"data" : app.selectedDataTransfer};
						var formData = methods.formData(datas);
						axios.post(urls, formData).then(function (e) {
							if(e.data.success){
								app.getallPayroll();
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
			transfer(data,index){			
				//Individual Save 
				var textmsg = "Are sure you want to transfer claimant from " + data.fullname + "to " + data.replacer_name + "?";
				textmsg += "Note that " + data.fullname + " will no longer appear in this period payroll.";

				swal.fire({
					title: 'Warning',
					text: textmsg,
					icon: 'warning',
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					confirmButtonText: 'Yes, Transfer!',
					cancelButtonText: 'No, cancel!',
					buttonsStyling: false	
					}).then((result) => {
					if (result.value) {
						showloading();
						var urls = window.App.baseUrl +"transfer-unpaid";
						var formData = frmdata(data);
						axios.post(urls, formData).then(function (e) {
							if(e.data.success){
								app.getallPayroll();
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
			saveBeneDetails(data){			
				//Individual Save 
				var textmsg = "Note: this will only save the details of payment (Amount, Receiver, and Date Receive). It will not change the payment status (Paid / Unpaid).";
				var datas = {
					prov_code 	: data.prov_code,
					mun_code 	: data.mun_code,
					bar_code 	: data.bar_code,
					year 		: this.search.year,
					period 		: this.search.period,
					spid 		: data.spid,
					amount 		: data.amount,
					receiver 	: data.receiver,
					date_receive : data.date_receive,
				};

				swal.fire({
					title: 'Warning',
					text: textmsg,
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
						var urls = window.App.baseUrl +"update-bene-payment";
						var formData = frmdata(datas);
						axios.post(urls, formData).then(function (e) {
							if(e.data.success){
								//app.searchWaitlist();
								//remove list 
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
			batchPayment(){			
				//Bulk Save payment
				var textmsg = "Are sure you want to set payment of the selected items to ";

				var payment_stat = 0
				if(this.search.liquidation == 1){
					textmsg += "UNPAID?";
					payment_stat = 0;
				}else{
					textmsg += "PAID?";
					payment_stat = 1;
				}

				var datas = {
					prov_code 	: this.search.prov_code,
					mun_code 	: this.search.mun_code,
					bar_code 	: this.search.bar_code,
					year 		: this.search.year,
					period 		: this.search.period,
					amount 		: this.batch.amount,
					date_receive : this.batch.date_receive,
					liquidation  : payment_stat,
					spids 		: this.selected,
				};

				swal.fire({
					title: 'Warning',
					text: textmsg,
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
						var urls = window.App.baseUrl +"batch-payment";
						var formData = frmdata(datas);
						axios.post(urls, formData).then(function (e) {
							if(e.data.success){
								$('#batchPayment').modal('hide');
								swal.close();
								swal.fire('Info',e.data.message,'success');
								app.getallPayroll();
							}else{
								swal.fire('Error',e.data.message,'error');
								app.getallPayroll();
							}
						})
					} else if ( result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled','Action Cancelled','error')
					}
				})
			},	
			dlPayrollList(){
				showloading();
				if(app.search.prov_code == "" || app.search.year == "" || app.search.period == ""){
					swal.fire('Error',"Please Enter Required Fields",'error');
				}else{
					var urls = window.App.baseUrl +"dl-payroll-list?prov_code=" + app.search.prov_code;
					urls += "&mun_code=" + app.search.mun_code;
					urls += "&bar_code=" + app.search.bar_code;
					urls += "&year=" + app.search.year;
					urls += "&period=" + app.search.period;
					urls += "&liquidation=" + app.search.liquidation;
					window.open(urls, '_blank');
				}
				swal.close();

				// axios.post(urls, formData).then(function (e) {
				// 	if(e.data.success){
				// 		swal.close();
				// 		swal.fire('Info',e.data.message,'success');
				// 		// window.open(e.data.data, '_blank');
				// 	}else{
				// 		swal.fire('Error',e.data.data,'error');
				// 	}
				// })
			},
			//END Get Libraries
			getEligibleWaitlist(){
				mun_code = app.activeSP.city;
				var urls = window.App.baseUrl +"get-Eligible-Waitlist";
				var params = {mun_code: mun_code};
				axios.get(urls, {params: params}).then(function (e) {
					console.log(e.data);
					if(e.data.success){
						app.woptions = e.data.data; 
					}else{
						swal.fire('Error',"There's No available eligible waitlist for the Municipality",'error');
						$('#replaceMember').modal('hide');
					}
				})
			},
			replaceMember(){
				console.log("replaceMember")
				app.wdata.m_id = app.activeSP.b_id;
				var formData = methods.formData(app.wdata);
				var urls = window.App.baseUrl + 'replace-Member';
				axios.post(urls, formData).then(function (e) {
					if(e.data.success){					
						console.log(e.data.message);
						app.searchMember();
						methods.toastr('success','Success',e.data.message);
						$('#replaceMember').modal('hide');
					}else{
						swal.fire('Error',"Something Went Wrong. Please Contact Your Administrator",'error');
					}
				})
			},
			clearRepReasonModal(){		
				app.repreason = {
					isDoubleEntry : false,
					isDateOfDeath : false,
					isTransfered : false,
					isWithPension : false,
					isOthers : false,
					mem_id : '',
					reason_id : '',
					reason_desc : ''
				};
			},
			resetModalFormOnClose(){
				$('#setToForReplacement').modal('hide');
				//$('#editPPMPModal').modal('hide');
				methods.destroyModalData();
				app.clearRepReasonModal();
			},
			////// EVENTS //////////////////
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
			reason_onchange(val = ""){
				app.repreason.reason_id = val;
				app.repreason.reason_desc = "";
				switch (val) {
					case "1":
						app.repreason.isDateOfDeath = false;
						app.repreason.isTransfered = false;
						app.repreason.isWithPension = false;
						app.repreason.isOthers = false;
						app.repreason.isDoubleEntry = true;
						break;
					case "2":
						app.repreason.isDateOfDeath = true;
						app.repreason.isTransfered = false;
						app.repreason.isWithPension = false;
						app.repreason.isOthers = false;
						app.repreason.isDoubleEntry = false;
						break;
					case "4":
						app.repreason.isWithPension = true;
						app.repreason.isDateOfDeath = false;
						app.repreason.isTransfered = false;
						app.repreason.isOthers = false;
						app.repreason.isDoubleEntry = false;
						break;
					case "6":
						app.repreason.isTransfered = true;
						app.repreason.isDateOfDeath = false;
						app.repreason.isWithPension = false;
						app.repreason.isOthers = false;
						app.repreason.isDoubleEntry = false;
						break;
					case "16":
						app.repreason.isOthers = true;
						app.repreason.isDateOfDeath = false;
						app.repreason.isTransfered = false;
						app.repreason.isWithPension = false;
						app.repreason.isDoubleEntry = false;
						break;
					default:
						app.repreason.isDateOfDeath = false;
						app.repreason.isTransfered = false;
						app.repreason.isWithPension = false;
						app.repreason.isOthers = false;
						app.repreason.isDoubleEntry = false;
						break;
				}
			},
			change_sp_status(){			
				//reason, remarks, memberid
				app.repreason.mem_id = app.activeSP.SPID;
				var formData = methods.formData(app.repreason);

				var urls = window.App.baseUrl + 'set-ForReplacementIndividual';
				axios.post(urls, formData).then(function (e) {
					if(e.data.success){					
						console.log(e.data.message);
						app.searchMember();
						methods.toastr('success','Success',e.data.message);
						app.resetModalFormOnClose();
						app.clearRepReasonModal();		
						methods.clearError();	
					}else{
						methods.errorFormValidation(e.data.message);
					}
				})
			},
			setToActive(){
				  
				swal.fire({
					title: 'Warning',
					text: "Are you sure you want to change this member's SP status to active?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					confirmButtonText: 'Yes, change status!',
					cancelButtonText: 'No, cancel!',
					buttonsStyling: false	
				  }).then((result) => {
					if (result.value) {
						console.log(app.activeSP.b_id);
						var urls = window.App.baseUrl +"set-ActiveIndividual";
						var params = {bid: app.activeSP.b_id};
						axios.get(urls, {params: params}).then(function (e) {
							if(e.data.success){
								app.searchMember();
								methods.toastr('success','Success',e.data.message);
							}else{
								swal.fire('Error',e.data.message,'error');
							}
						})
					} else if ( result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled','SP status not changed.','error')
					}
				  })
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
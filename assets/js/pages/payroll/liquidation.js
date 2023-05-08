
Vue.use(VueTables.ServerTable);
Vue.component('v-select', VueSelect.VueSelect);
if ($('#l_index').length) {
	// var today = new Date();
	// var date_today = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();

	var app = new Vue({
		el: '#l_index',
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
				amount : '',
				date_receive : new Date().toISOString().slice(0,10),
			},
			search: {
				prov_code: '',
				mun_code: '',
				bar_code: '',
				year: '',
				period: '',
				liquidation: '0',
				type: 'all'
			},
			liquidationText : "",
			loading : false,
			payrollList: [],
			total_target : 0,
			total_paid : 0,
			total_unpaid : 0,
			total_offset : 0,
			total_onhold : 0,
			total_unclaimed : 0,
			selected: [],
			selectedData: [],
			selectAll: false,
			searchCol: '',
			tblFilter : '',
			payment_file: '',
			userrole: 2,
		}, methods: {   
			getallPayroll() {
				this.payrollList = [];
				this.loading = true;
				var data = frmdata(this.search);
				var urls = window.App.baseUrl + "get-all-liquidation";
				axios.post(urls,data)
					.then(function (e) {
						app.selected = [];
						app.selectedData = [];
						app.selectAll = false;
						app.payrollList = e.data.data;
						app.total_target = e.data.total_target;
						app.total_paid = e.data.total_paid;
						app.total_unpaid = e.data.total_unpaid;
						app.total_offset = e.data.total_offset;
						app.total_onhold = e.data.total_onhold;
						app.total_unclaimed = e.data.total_unclaimed;
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
			ShowList() {
				if(this.search.liquidation == 0){ this.liquidationText =   "PAID";}
				else if(this.search.liquidation == 1){ this.liquidationText =   "UNPAID";}
				else if(this.search.liquidation == 2){ this.liquidationText =   "TRANSFERED";}
				else if(this.search.liquidation == 3){ this.liquidationText =   "OFFSET";}
				else if(this.search.liquidation == 4){ this.liquidationText =   "ON HOLD";}
			},
			getpaymentstatus(liquidation){
				if(liquidation == 0){ return  "UNPAID";}
				else if(liquidation == 1){ return  "PAID";}
				else if(liquidation == 2){ return  "TRANSFERED";}
				else if(liquidation == 3){ return  "OFFSET";}
				else if(liquidation == 4){ return  "ON HOLD";}
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
				this.selectedData = [];
				if (!this.selectAll) {
					for (let i in this.payrollList) {
						this.selected.push(this.payrollList[i].spid);
						this.selectedData.push(this.payrollList[i]);
					}
				}
			},
			selectIndividual(data,event) {
				if (event.target.checked) {
					this.selectedData.push(data);
				}else{
					var index = this.selectedData.indexOf(data);
					if (index > -1) {
						this.selectedData.splice(index, 1);
					}
				}
			},
			paymentFileUpload(){
				this.payment_file = this.$refs.payment_file.files[0];
			},
			submitUpdatePaymentFile(){
				showloading();
				let formData = new FormData();
				formData.append('file', this.payment_file);
				var urls = window.App.baseUrl +"Liquidation/updatePaymentStatus";
				axios.post( urls, formData,{
						headers: { 'Content-Type': 'multipart/form-data' }
					}).then(function(e){
						swal.close();
						swal.fire('Info',e.data.message,'success');
						$('#importPaymentModal').modal('hide');
						//app.searchWaitlist();
					})
				.catch(function(){
					console.log('FAILURE!!');
				});
			},
			checkUser(){
				var urls = window.App.baseUrl + 'get-login-user';
				axios.get(urls).then(function (e) {
					console.log(e.data);
					app.userrole = e.data.role;
				})
			},
			formatDate(pdate){
				let date = new Date(pdate);
				let mo = (date.getMonth()+1);
				let m = (mo < 10 ? '0' : '') + mo;
				let d = (date.getDate() < 10 ? '0' : '') + date.getDate();
				return (date.getFullYear()) + "-" + m + "-" + d;
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
			getCurrentPeriod(){
				var urls = window.App.baseUrl + 'get-current-period';
				axios.get(urls).then(function (e) {
					console.log(e.data);
					app.search.year = e.data.year;
					app.search.period = e.data.period;
				})
			},
			getperiodname(period) {
				if(period == 5){
					this.batch.amount = '3000';
					return "1st Semester";
				}else if(period == 6){
					this.batch.amount = '3000';
					return "2nd Semester";
				}else if(period == 1){
					this.batch.amount = '1500';
					return "1st Quarter";
				}else if(period == 2){
					this.batch.amount = '1500';
					return "2nd Quarter";
				}else if(period == 3){
					this.batch.amount = '1500';
					return "3rd Quarter";
				}else if(period == 4){
					this.batch.amount = '1500';
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
			setBeneStatus(data,index){			
				//Individual Save 
				
				var textmsg = "Are sure you want to set payment of " + data.fullname + "to ";
				var payment_stat = 0
				if(this.search.liquidation == 1){
					textmsg += "UNPAID?";
					payment_stat = 0;
				}else{
					textmsg += "PAID?";
					payment_stat = 1;
				}

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
					liquidation : payment_stat,
					remarks : data.remarks,
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
						var urls = window.App.baseUrl +"set-bene-status";
						var formData = frmdata(datas);
						axios.post(urls, formData).then(function (e) {
							if(e.data.success){
								//app.searchWaitlist();
								//remove list 
								app.payrollList.splice(index,1);
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
				var textmsg = "Are you sure you want to save the changes on this row?";
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
					remarks 	: data.remarks,
					liquidation : data.liquidation,
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
			batchSave(){			
				//Bulk Save payment
				var datas = {
					prov_code 		: this.search.prov_code,
					mun_code 		: this.search.mun_code,
					bar_code 		: this.search.bar_code,
					year 			: this.search.year,
					period 			: this.search.period,
					amount 			: this.batch.amount,
					date_receive 	: this.batch.date_receive,
					liquidation  	: "",
					spids 			: this.selected,
					selectedData 	: this.selectedData,
					saveType 		: 1,
				};

				swal.fire({
					title: 'Warning',
					text: "Are sure you want to save the changes made in the selected rows? ",
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
						var formData = methods.formData(datas);
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
			batchPayment(){			
				//Bulk Save payment
				var textmsg = "Are sure you want to set payment of the selected items to ";

				var payment_stat = "";
				if(this.search.liquidation == 1){
					textmsg += "UNPAID?";
					payment_stat = 0;
				}else if(this.search.liquidation == 0){
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
					selectedData : this.selectedData,
					saveType : 0,
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
						//var formData = frmdata(datas);
						var formData = methods.formData(datas);
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
			dlPaymentLiquidation(){
				showloading();
				if(app.search.prov_code == "" || app.search.mun_code == ""){
					swal.fire('Error',"Please Enter Required Fields",'error');
				}else{
					var urls = window.App.baseUrl +"Liquidation/exportPaymentLiquidation?prov_code=" + app.search.prov_code;
					urls += "&mun_code=" + app.search.mun_code;
					urls += "&bar_code=" + app.search.bar_code;
					urls += "&year=" + app.search.year;
					urls += "&type=" + app.search.type;
					window.open(urls, '_blank');
				}
				swal.close();
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
					urls += "&type=" + app.search.type;
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
			dlPaidRegistry(){
				var urls = window.App.baseUrl +"dl-paid-registry";
				window.open(urls, '_blank');
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
			this.getCurrentPeriod();
			this.checkUser();
		},
	})
}
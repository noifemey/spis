
Vue.use(VueTables.ServerTable);
Vue.component('v-select', VueSelect.VueSelect);
if ($('#m_details').length) {
	var spid = $('#spid').val();
	var mem = new Vue({
		el: '#m_details',
		data: {
			spid : spid,
			replacerData : [],
			replaceeData : [],
			memDetails : [],
			membereditlogs : [],
			memberPayments : [],
			defaultPhoto : window.App.baseUrl + "/assets/img/default-avatar.png",
			lib:{
				ReplacementReason:{},
				livingarrangementlibrary:{},
				maritalstatlibrary:{},
				relationshiplibrary:{},
				disabilitylibrary:{},	
				inactivereason:{},				
				location: {
					provinces: [],
					present_municipalities: [],
					present_barangays: [],
					permanent_municipalities: [],
					permanent_barangays: [],
				},
			},
			location: {
				provinces: [],
				municipalities: [],
				barangays: [],
				prov_names: [],
				mun_names: [],
				bar_names: [],
			},
			ReplacementReason:{},
			repreason:{
				isDoubleEntry : false,
				isDateOfDeath : false,
				isTransfered : false,
				isWithPension : false,
				isOthers : false,
				mem_id : '',
				reason_id : '',
				duplicate : '',
				dateofdeath : '',
				placeoftransfer : '',
				pension_select : '',
				otherreason : '',
			},
			memPayments : {
				new : true,
				activeMP : {
					p_id : "",
					prov_code : "",
					mun_code : "",
					bar_code : "",
					spid : "",
					year : "",
					period : "",
					date_receive : "",
					liquidation : "0",
					receiver : "",
					amount : 3000,
					remarks : "",
				},
				data : []
			},
			userrole: 2,
		}, methods: {   
			getMemDetails(){
				//this.loading = true;
				console.log(this.spid);
				var frmdata = {"spid" : this.spid};
				var data = methods.formData(frmdata);
				var urls = window.App.baseUrl + "get-member-detail";
				axios.post(urls,data)
					.then(function (e) {
						console.log(e.data);
						mem.replacerData = e.data.replacerData;
						mem.replaceeData = e.data.replaceeData;
						mem.memDetails = e.data.memData;
						mem.membereditlogs = e.data.membereditlogs;
						mem.memberPayments = e.data.memberPayments;
					})
					.catch(function (error) {
						console.log(error)
					});
			},
			//GET Libraries			
			getallLibrary(){
				var urls = window.App.baseUrl + 'get-all-libraries';
				axios.get(urls).then(function (e){
					mem.lib.livingarrangementlibrary = e.data.livingArr;
					mem.lib.maritalstatlibrary = e.data.marStatus;
					mem.lib.relationshiplibrary = e.data.relList;
					mem.lib.disabilitylibrary = e.data.disabilities;
					mem.lib.inactivereason = e.data.inactivereason;
				})
			},
			getAllLocation() {
				var urls = window.App.baseUrl + 'get-all-location';
				axios.get(urls).then(function (e) {
					console.log(e.data);
					mem.location.prov_names = e.data.provinces;
					mem.location.mun_names = e.data.municipalities;
					mem.location.bar_names = e.data.barangays;
					mem.location.provinces = e.data.provinces;
					mem.lib.location.provinces = e.data.provinces;
				})
			},
			getLocation(type = 'prov_code', val = "", source="search") {
				if (type == 'mun_code') {
					if(source == "permanent"){
						//app.wdata.municipality_permanent = "";
						mem.lib.location.permanent_barangays = [];
						mem.lib.location.permanent_municipalities = mem.location.mun_names[val];
					}else if(source == "present"){
						//app.wdata.municipality_present = "";
						mem.location.present_barangays = [];
						mem.lib.location.present_municipalities = mem.location.mun_names[val];
					}else if(source == "export"){
						console.log(val);
						if(val == "all"){
							 mem.exportdata.municipalities = []; 
						}else{
							mem.exportdata.municipalities = mem.location.mun_names[val];
						}
					}else{
						// global_search.search.mun_code = '';
						// global_search.search.bar_code = '';
						mem.location.barangays = [];
						mem.location.municipalities = mem.location.mun_names[val];
					}
				} else if(type == 'bar_code') {
					if(source == "permanent"){
						//app.wdata.barangay_permanent = "";
						mem.lib.location.permanent_barangays = mem.location.bar_names[val];
					}else if(source == "present"){
						//app.wdata.barangay_present = "";
						mem.lib.location.present_barangays = mem.location.bar_names[val];
					}else {
						// global_search.search.bar_code = '';
						mem.location.barangays = mem.location.bar_names[val];
					}
				} else{
					if(source == "search"){ mem.location.provinces = mem.location.prov_names;}
					else{ mem.lib.location.provinces = mem.location.prov_names;}
				}
			},
			getprovname(prov_code){
				var prov_name = "";
				this.location.prov_names.forEach(prov => {
					if(prov.prov_code == prov_code){
						prov_name = prov.prov_name;
					}
				});
				return prov_name;
			},
			getmunname(prov_code,mun_code){
				var mun_name = "";
				if(this.location.mun_names.hasOwnProperty(prov_code)){
					var munlist = this.location.mun_names[prov_code];
					munlist.forEach(mun => {
						if(mun.mun_code == mun_code){
							mun_name = mun.mun_name;
						}
					});
				}
				return mun_name;
			},
			getbarname(mun_code,bar_code){
				var bar_names = "";
				if(this.location.bar_names.hasOwnProperty(mun_code)){
					var barlist = mem.location.bar_names[mun_code];
					barlist.forEach(bar => {
						if(bar.bar_code == bar_code){
							bar_names = bar.bar_name;
						}
					});
				}
				return bar_names;
			},
			getFullname(lastname,firstname,middlename = "",extname = ""){			
				var fullname = lastname + ", " + firstname + " " + middlename + " " + extname;
				return fullname.toUpperCase();
			},			
			getAge(bday) {
				if(bday == null || bday == ""){return "";}
				var dob = bday;
				var dob = dob.split("-");
				var dob = new Date(dob[0], dob[1], dob[2])
				var diff_ms = Date.now() - dob.getTime();
				var age_dt = new Date(diff_ms);
				return Math.abs(age_dt.getUTCFullYear() - 1970);
			},
			getmaritalstat(id){
				var maritalStatus = "";

				Object.keys(this.lib.maritalstatlibrary).forEach(key => {
					let val = this.lib.maritalstatlibrary[key] // value of the current key
					if(val.id == id){
						maritalStatus = val.name;
					}
				});

				return maritalStatus;
			},
			getrelname(relid){
				var rel = "";
				Object.keys(this.lib.relationshiplibrary).forEach(key => {
					let val = this.lib.relationshiplibrary[key] // value of the current key
					if(val.relid == relid){
						rel = val.relname;
					}
				});
				return rel;
			},
			getreasonname(id){
				var reason = "";
				Object.keys(this.lib.inactivereason).forEach(key => {
					let val = this.lib.inactivereason[key] // value of the current key
					if(val.id == id){
						reason = val.name;
					}
				});
				return reason;
			},
			getReplacementReason(){
				var urls = window.App.baseUrl + 'get-all-ReplacementReason';
				axios.get(urls, {
					params: {}
				}).then(function (e) {
					mem.ReplacementReason = e.data;
				})
			},
			getperiodStart(period){
				if(period==1){ return "1st quarter";}
				else if(period==2){ return "2nd quarter";}
				else if(period==3){ return "3rd quarter";}
				else if(period==4){ return "4th quarter";}
			},
			//END Get Libraries
			selectData(data){			
				//console.log(data);
				mem.activeSP = data;
				$('.form-group').addClass('focused');
			},			
			getEligibleWaitlist(){
				mun_code = mem.activeSP.city;
				var urls = window.App.baseUrl +"get-Eligible-Waitlist";
				var params = {mun_code: mun_code};
				axios.get(urls, {params: params}).then(function (e) {
					console.log(e.data);
					if(e.data.success){
						mem.woptions = e.data.data; 
					}else{
						swal.fire('Error',"There's No available eligible waitlist for the Municipality",'error');
						$('#replaceMember').modal('hide');
					}
				})
			},
			replaceMember(){
				console.log("replaceMember")
				mem.wdata.m_id = mem.activeSP.b_id;
				var formData = methods.formData(mem.wdata);
				var urls = window.App.baseUrl + 'replace-Member';
				axios.post(urls, formData).then(function (e) {
					if(e.data.success){					
						console.log(e.data.message);
						mem.searchMember();
						methods.toastr('success','Success',e.data.message);
						$('#replaceMember').modal('hide');
					}else{
						swal.fire('Error',"Something Went Wrong. Please Contact Your Administrator",'error');
					}
				})
			},
			editMember(spid){
				window.location.href = window.App.baseUrl + 'edit-member/' + spid;
			},
			printLbp(spid){
				window.location.href = window.App.baseUrl + 'print-Lbp/' + spid;
			},
			printBuf(spid){
				window.location.href = window.App.baseUrl + 'print-BUF/' + spid;
			},
			clearRepReasonModal(){		
				mem.repreason = {
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
				mem.clearRepReasonModal();
			},
			////// EVENTS //////////////////
			getLocation(type = 'prov_code', val = "") {
				if (type == 'mun_code') {
					// global_search.search.mun_code = '';
					mem.location.barangays = [];
					mem.location.municipalities = mem.location.mun_names[val];
				} else if(type == 'bar_code') {
					// global_search.search.bar_code = '';
					mem.location.barangays = mem.location.bar_names[val];
				} else{
					mem.location.provinces = mem.location.prov_names;
				}
			},
			reason_onchange(val = ""){
				mem.repreason.reason_id = val;
				mem.repreason.reason_desc = "";
				switch (val) {
					case "1":
						mem.repreason.isDateOfDeath = false;
						mem.repreason.isTransfered = false;
						mem.repreason.isWithPension = false;
						mem.repreason.isOthers = false;
						mem.repreason.isDoubleEntry = true;
						break;
					case "2":
						mem.repreason.isDateOfDeath = true;
						mem.repreason.isTransfered = false;
						mem.repreason.isWithPension = false;
						mem.repreason.isOthers = false;
						mem.repreason.isDoubleEntry = false;
						break;
					case "4":
						mem.repreason.isWithPension = true;
						mem.repreason.isDateOfDeath = false;
						mem.repreason.isTransfered = false;
						mem.repreason.isOthers = false;
						mem.repreason.isDoubleEntry = false;
						break;
					case "6":
						mem.repreason.isTransfered = true;
						mem.repreason.isDateOfDeath = false;
						mem.repreason.isWithPension = false;
						mem.repreason.isOthers = false;
						mem.repreason.isDoubleEntry = false;
						break;
					case "16":
						mem.repreason.isOthers = true;
						mem.repreason.isDateOfDeath = false;
						mem.repreason.isTransfered = false;
						mem.repreason.isWithPension = false;
						mem.repreason.isDoubleEntry = false;
						break;
					default:
						mem.repreason.isDateOfDeath = false;
						mem.repreason.isTransfered = false;
						mem.repreason.isWithPension = false;
						mem.repreason.isOthers = false;
						mem.repreason.isDoubleEntry = false;
						break;
				}
			},
			change_sp_status(){			
				//reason, remarks, memberid
				mem.repreason.mem_id = mem.activeSP.SPID;
				var formData = methods.formData(mem.repreason);

				var urls = window.App.baseUrl + 'set-ForReplacementIndividual';
				axios.post(urls, formData).then(function (e) {
					if(e.data.success){					
						console.log(e.data.message);
						mem.searchMember();
						methods.toastr('success','Success',e.data.message);
						mem.resetModalFormOnClose();
						mem.clearRepReasonModal();		
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
						console.log(mem.activeSP.b_id);
						var urls = window.App.baseUrl +"set-ActiveIndividual";
						var params = {bid: mem.activeSP.b_id};
						axios.get(urls, {params: params}).then(function (e) {
							if(e.data.success){
								mem.searchMember();
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
			////// END EVENTS ///////////////
			////// Payment History /////////
			formatDate(pdate) {
				let date = new Date(pdate);
				let mo = (date.getMonth()+1);
				let m = (mo < 10 ? '0' : '') + mo;
				let d = (date.getDate() < 10 ? '0' : '') + date.getDate();
				return (date.getFullYear()) + "-" + m + "-" + d;
			},
			getMemPayment(spid){
				//this.loading = true;
				console.log(spid);
				var frmdata = {"spid" : spid};
				var data = methods.formData(frmdata);
				var urls = window.App.baseUrl + "get-member-payment";
				axios.post(urls,data)
					.then(function (e) {
						mem.memPayments.data = e.data;
					})
					.catch(function (error) {
						console.log(error)
					});
			},
			// getPaymentStatus(ps){
			// 	if(ps == 0){
			// 		return "UNPAID";
			// 	}else {
			// 		return "PAID";
			// 	}
			// },
			geturl(spid){
				var urls = window.App.baseUrl + 'view-member/' + spid;

				return urls;
			},
			getperiod(period){
				if(period == 1){ return  "1ST";}
				else if(period == 2){ return  "2ND";}
				else if(period == 3){ return  "3RD";}
				else if(period == 4){ return  "4TH"; }
				else {return "";}
			},
			getpaymentstatus(liquidation){
				if(liquidation == 0){ return  "UNPAID";}
				else if(liquidation == 1){ return  "PAID";}
				else if(liquidation == 2){ return  "TRANSFERED";}
				else if(liquidation == 3){ return  "OFFSET";}
				else if(liquidation == 4){ return  "ON HOLD";}
			},
			undoReplace(){
				data = {'replacerData': this.replacerData, 'memDetails': this.memDetails};

				var formData = methods.formData(data);

				var urls = window.App.baseUrl + 'undo-replace';
				axios.post(urls, formData).then(function (e) {
					if(e.data.success){					
						$('#confirmUndoModal').modal('hide');
						mem.getMemDetails();
						methods.toastr('success','Success',e.data.message);
					}else{
						swal.fire('Error',e.data.message,'error');
					}
				})
			},
			checkUser(){
				var urls = window.App.baseUrl + 'get-login-user';
				axios.get(urls).then(function (e) {
					mem.userrole = e.data.role;
				})
			},
			///// End History /////////////
		},
		mounted: function () {
			this.getMemDetails();
			this.checkUser();
		},
		created: function () {
			this.getAllLocation();
			this.getallLibrary();
			//this.getReplacementReason();
		}
	})
}
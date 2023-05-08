Vue.use(VueTables.ServerTable);
if ($('#w_index').length) {
	var global_search = new Vue({
		data:{
			search: {
				prov_code: '',
				mun_code: '',
				bar_code: '',
				gender: '',
				status: '',
				birth_to:'',
				birth_from:''
			},
		}
	})
	var app = new Vue({
		el: '#w_index',
		data: {
			global_search : global_search,
			activeWaitlist:{},
			isLoading: false,
			file: '',
			eligible_file: '',
			archive_reason: '',
			archive_remarks: '',
			repreason:{
				isDoubleEntry : false,
				isDateOfDeath : false,
				isTransfered : false,
				isWithPension : false,
				isOthers : false,
				mem_id : '',
				reason_id : '',
				reason_desc : ''
			},
			allMarked:false,
			markedRows:[],
			new_waitlist: {
				column: [
					"id",
					"Reference_Code",
					"Full_Name",
					"Province",
					"Municipality",
					"Barangay",
					"Birth_Date",
					"Age",
					"Gender",
					"OSCA_ID",
					"Status",
					"actions"
				],
				options: {
					requestFunction: function (data) {
						var datas = {
							params: {
								query: data.query,
								limit: data.limit,
								page: data.page,
								byColumn: data.byColumn,
								ascending: data.ascending
							}
						};
						datas.params.condition = global_search.search;
						 console.log(datas);

						var urls = window.App.baseUrl + 'Waitlist/getAllWaitlist';
						return axios.get(urls, datas)
							.catch(function (e) {
						  		this.dispatch('error', e);
					  		}.bind(this));
					},
					headings: {
						id: function(h) {
							return h('input', {
								attrs: {
								  type: 'checkbox',
								  id: 'selectAllCheckbox'
								},
								on: {
								  change: (e) => {
									this.toggleAll()
								  }
								},
								ref: 'selectAllCheckbox'
							  })
						  }
					},
					sortIcon: {
					  base : 'fa',
					  is: 'fa-sort',
					  up: 'fa-sort-asc',
					  down: 'fa-sort-desc'
					},
					sortable: ['Full_Name','Birth_Date','Age', 'Gender', 'Province',"Municipality","Barangay"],
    				perPageValues: [10,25,50,100,150,200,250,500]
				}
			},
			location: {
				provinces: [],
				municipalities: [],
				barangays: [],
			},
			isEditing: false,
			prov_names: [],
			mun_names: [],
			bar_names: [],
			wdata: {
				reference_code: "",
				oscaid: "",input_grantee: "",respondentName: "",lastname: "",firstname: "",middlename: "",extname: "",dateofbirth: "",
				view_age: "",gender: "",maritalstatus: "",birthplace: "",mothersMaidenName: "",contactno: "",hhid: "",hhsize: "",
				province_permanent: "",municipality_permanent: "",barangay_permanent: "",address_permanent: "", street_permanent: "",
				province_present: "",municipality_present: "",barangay_present: "",address_present: "", street_present: "",
				caregivername: "",caregiverrelp: "",rep2name: "",rep2rel: "",rep3name: "",rep3rel: "",pensionreceiver: "",pensionsreceived_other: "",
				pensionsreceived_dswd: "",pensionsreceived_gsis: "",pensionsreceived_sss: "",pensionsreceived_afpslai: "",pensionsreceived_others: "",
				income_wages: "",income_entrep:"",income_household:"",income_domestic:"", income_international:"",income_friends:"",income_government:"",income_others:"",sourcesOfIncome_other:"",
				ans4: "",ans4_amt: "",ans6: "",ans6_amt: "",ans8: "",ans8_amt: "",ans10: "",ans10_amt: "",ans12: "",ans12_amt: "",
				ans14: "",ans14_amt: "",ans16: "",ans16_amt: "",ans18: "",ans18_amt: "",livingArrangement: "",ans20: "",ans21: "",
				ans22: "",ans23: "",ans24: "",ans25: "",ans27: "",illness: "",ans28_other: "",workerName: "",date_accomplished: "",
				sp_food: "",sp_med: "",sp_checkup: "",sp_cloth: "",sp_util: "",sp_debt: "",sp_entrep: "",sp_others: ""
			},
			lib:{
				ReplacementReason:{},
				livingarrangementlibrary:{},
				maritalstatlibrary:{},
				relationshiplibrary:{},
				disabilitylibrary:{},				
				location: {
					provinces: [],
					present_municipalities: [],
					present_barangays: [],
					permanent_municipalities: [],
					permanent_barangays: [],
				},
			},
			exportdata:{
				municipalities: [],
				prov_code: "",
				mun_code: "",
				status: ""
			},
			waitlisttemp:{
				municipalities: [],
				prov_code: "",
				mun_code: "",
			},
			probableDuplicate:{
				probableActiveData: '',
				probableWaitlistData: '',
				showModal: false,
				clean: false,
			},
			isFormValid: false,			
			sameAddress: 0,
			userrole: 2,
		}, methods: {  
			//GET Libraries
			getallLibrary(){
				var urls = window.App.baseUrl + 'get-all-libraries';
				axios.get(urls).then(function (e){
					app.lib.livingarrangementlibrary = e.data.livingArr;
					app.lib.maritalstatlibrary = e.data.marStatus;
					app.lib.relationshiplibrary = e.data.relList;
					app.lib.disabilitylibrary = e.data.disabilities;
				})
			},
			getAllLocation() {
				var urls = window.App.baseUrl + 'get-all-location';
				axios.get(urls).then(function (e) {
					console.log(e.data);
					app.prov_names = e.data.provinces;
					app.mun_names = e.data.municipalities;
					app.bar_names = e.data.barangays;
					app.location.provinces = e.data.provinces;
					app.lib.location.provinces = e.data.provinces;
				})
			},
			getLocation(type = 'prov_code', val = "", source="search") {
				if (type == 'mun_code') {
					if(source == "permanent"){
						//app.wdata.municipality_permanent = "";
						app.lib.location.permanent_barangays = [];
						app.lib.location.permanent_municipalities = app.mun_names[val];
					}else if(source == "present"){
						//app.wdata.municipality_present = "";
						app.location.present_barangays = [];
						app.lib.location.present_municipalities = app.mun_names[val];
					}else if(source == "export"){
						console.log(val);
						if(val == "all"){
							 app.exportdata.municipalities = []; 
						}else{
							app.exportdata.municipalities = app.mun_names[val];
						}
					}else if(source == "template"){
						if(val == "all"){
							 app.waitlisttemp.municipalities = []; 
						}else{
							app.waitlisttemp.municipalities = app.mun_names[val];
						}
					}else{
						global_search.search.mun_code = '';
						global_search.search.bar_code = '';
						app.location.barangays = [];
						app.location.municipalities = app.mun_names[val];
					}
				} else if(type == 'bar_code') {
					if(source == "permanent"){
						//app.wdata.barangay_permanent = "";
						app.lib.location.permanent_barangays = app.bar_names[val];
					}else if(source == "present"){
						//app.wdata.barangay_present = "";
						app.lib.location.present_barangays = app.bar_names[val];
					}else {
						global_search.search.bar_code = '';
						app.location.barangays = app.bar_names[val];
					}
				} else{
					if(source == "search"){ app.location.provinces = app.prov_names;}
					else{ app.lib.location.provinces = app.prov_names;}
				}
			},
			getstatus(status,sent_to_co,remarks,duplicate) {
				var retvalue = "";
				switch (status) {
					case "1":
						retvalue = "ELIGIBLE"
						break;
					case "2":
						retvalue = "NOT ELIGIBLE";
						break;
					case "3":
						retvalue = "NOT ELIGIBLE - Barangay Official";
					default:
						if (sent_to_co == "1") { retvalue =  "WAITING FOR ELIGIBILITY"; 
						} else { retvalue =  "NO ELIGIBLE STATUS YET"; } 
						break;
				}
				
				if(remarks != null && remarks != "") { retvalue += " - (" + remarks + ")";}
				if(duplicate != null && duplicate != "") { retvalue += " - (duplicate - " + duplicate + ")";}

				return retvalue;
			},
			getprovname(prov_code){
				var prov_name = "";
				app.prov_names.forEach(prov => {
					if(prov.prov_code == prov_code){
						prov_name = prov.prov_name;
					}
				});
				return prov_name;
			},
			getmunname(prov_code,mun_code){
				var mun_name = "";
				if(app.mun_names.hasOwnProperty(prov_code)){
					var munlist = app.mun_names[prov_code];
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
				if(app.bar_names.hasOwnProperty(mun_code)){
					var barlist = app.bar_names[mun_code];
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
			//END GET Libraries

			//SEARCH
			searchWaitlist(type = '', obj = "") {
				var datas = {
					params: {
						limit: 10,
						page: 1,
						byColumn: 1,
						ascending: "ASC",
						condition: global_search.search
					}
				};
				// datas.params.condition = global_search.search;

				var urls = window.App.baseUrl + 'Waitlist/getAllWaitlist';
				axios.get(urls, datas)
					.then(function (e) {
						console.log(e.data);
						app.$refs.New_waitlisttable.data = e.data.data;
						app.$refs.New_waitlisttable.count = parseInt(e.data.count);
					})
			},
			//END SEARCH

			unmarkAll() {
				app.allMarked = false;
			},
			toggleAll() {
				app.markedRows = [];
				app.markedRows = app.allMarked? [] : app.$refs.New_waitlisttable.data.map(row=>row.w_id);
				app.allMarked = !app.allMarked;	
				console.log(app.markedRows);		
			},
			selectData(data){			
				//console.log(data);
				app.activeWaitlist = data;
				app.archive_remarks = data.remarks;
				$('.form-group').addClass('focused');
			},
			getReplacementReason(){
				var urls = window.App.baseUrl + 'get-all-ReplacementReason';
				axios.get(urls, {
					params: {}
				}).then(function (e) {
					app.lib.ReplacementReason = e.data;
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
			handleFileUpload(){
				this.file = this.$refs.file.files[0];
			},
			submitFile(){
				showloading();
				let formData = new FormData();
				formData.append('file', this.file);
				axios.post( 'Waitlist/uploadWaitlist', formData,{
						headers: { 'Content-Type': 'multipart/form-data' }
					}).then(function(e){
						swal.close();
						swal.fire('Info',e.data.message,'success');
						$('#importwaitinglistModal').modal('hide');
						app.searchWaitlist();
					})
				.catch(function(){
					console.log('FAILURE!!');
				});
			},
			eligibilityFileUpload(){
				this.eligible_file = this.$refs.eligible_file.files[0];
			},
			submitUpdateEligibleFile(){
				showloading();
				let formData = new FormData();
				formData.append('file', this.eligible_file);
				var urls = window.App.baseUrl +"update-waitlist-eligibility";
				axios.post( urls, formData,{
						headers: { 'Content-Type': 'multipart/form-data' }
					}).then(function(e){
						swal.close();
						swal.fire('Info',e.data.message,'success');
						$('#importwaitinglistModal').modal('hide');
						app.searchWaitlist();
					})
				.catch(function(){
					console.log('FAILURE!!');
				});
			},
			addWaitlistData(){
				methods.clearError();				
				if(app.probableDuplicate.clean === false && !app.isEditing){
					$('#newWaitlistModal').scrollTop(0);
					var text = "Are you sure you want to continue? There is/are possible duplicate/s for " + app.wdata.lastname + ', ' + app.wdata.firstname + ' ' + app.wdata.middlename;					
				} else {
					var text = "Are you sure you want to continue?";
				}

				swal.fire({
					title: 'Warning',
					text: text,
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
							app.updateWaitlist();
						} else if (result.dismiss === Swal.DismissReason.cancel) {
							swal.fire('Cancelled','Action Cancelled','error')
						}
					});
			},
			checkDup(dupCheckOnUpdate){							
				if(app.wdata.firstname == '' || app.wdata.lastname == ''){
					app.probableDuplicate.probableActiveData = '';
				}

				if(app.wdata.firstname != '' && app.wdata.lastname != '' && app.isEditing == false){

					var params = {
						waitlistid: app.activeWaitlist.w_id,
						wdata: app.wdata,
						isEditing: app.isEditing
					};				
					var formData = methods.formData(params);

					var urlDuplicate = window.App.baseUrl + "Waitlist/checkProbableDuplicate";

					axios.post(urlDuplicate, formData).then(function (dupResult) {
						swal.close();
						
						if(dupResult.data.success){							
							app.probableDuplicate.clean = true;
						}else{
							app.probableDuplicate.clean = false;							
							app.probableDuplicate.probableActiveData = dupResult.data.probableActiveData;
						}
					});
				}
			},
			validateWaitlistForm(){
				var errors = [];				

				if(app.wdata.lastname == ''){
					errors['lastname'] = 'The Last Name Field is required.';
				}

				if(app.wdata.firstname == ''){
					errors['firstname'] = 'The First Name Field is required.';
				}

				if(app.wdata.dateofbirth == ''){
					errors['dateofbirth'] = 'The Date of Birth Field is required.';
				}

				if(app.wdata.gender == ''){
					errors['gender'] = 'The Gender Field is required.';
				}

				if(app.wdata.view_age == ''){
					errors['view_age'] = 'The Age Field is required.';
				}

				if(app.wdata.view_age < 58){
					errors['view_age'] = 'Age should be greater than 59.';
				}

				if(app.wdata.gender == ''){
					errors['gender'] = 'The Gender Field is required.';
				}

				if(app.wdata.maritalstatus == ''){
					errors['maritalstatus'] = 'The Marital Status Field is required.';
				}

				if(app.wdata.birthplace == ''){
					errors['birthplace'] = 'The Birth Place Field is required.';
				}

				if(app.wdata.mothersMaidenName == ''){
					errors['mothersMaidenName'] = 'The Mothers Maiden Name Field is required.';
				}

				if(app.wdata.province_permanent == ''){
					errors['province_permanent'] = 'The Permanent Province Field is required.';
				}

				if(app.wdata.municipality_permanent == ''){
					errors['municipality_permanent'] = 'The Permanent Municipality Field is required.';
				}

				if(app.wdata.barangay_permanent == ''){
					errors['barangay_permanent'] = 'The Permanent Barangay Field is required.';
				}

				if(app.wdata.province_present == ''){
					errors['province_present'] = 'The Present Province Field is required.';
				}

				if(app.wdata.municipality_present == ''){
					errors['municipality_present'] = 'The Present Municipality Field is required.';
				}

				if(app.wdata.barangay_present == ''){
					errors['barangay_present'] = 'The Present Barangay Field is required.';
				}

				if(app.wdata.caregivername == ''){
					errors['caregivername'] = 'The Caregiver Name Field is required.';
				}

				if(app.wdata.caregiverrelp == ''){
					errors['caregiverrelp'] = 'The Relationship of Caregiver Field is required.';
				}

				if(app.wdata.livingArrangement == ''){
					errors['livingArrangement'] = 'The Who are you living with? field is required.';
				}

				if(app.wdata.workerName == ''){
					errors['workerName'] = 'The Name of Worker field is required.';
				}

				if(app.wdata.date_accomplished == ''){
					errors['date_accomplished'] = 'The Date Accomplished field is required.';
				}		
				
				if(errors.length >= 0){
					app.isFormValid = true;
				} else {
					methods.errorFormValidation(errors);
					swal.fire('Error','Please Input Required Fields.','error');
					app.isFormValid = false;
				}				
											
			},
			computeAge(){							

			  	var d = app.wdata.dateofbirth.split('-');
				var bdate = new Date(d[0],d[1],d[2]);
				var diff_ms = Date.now() - bdate;
			    var age_dt = new Date(diff_ms); 
			    var age = Math.abs(age_dt.getUTCFullYear() - 1970);

			    if(age < 58){
			    	swal.fire('Warning',"Age should be greater than 59",'warning');
			    }
			    if(age > 85){
			    	app.wdata.ans20 = "1";
			    } else {
			    	app.wdata.ans20 = "0";
			    }
			    app.wdata.view_age = age;
			},			
			updateWaitlist(){
				methods.clearError();
				showloading();

				app.validateWaitlistForm();
				if(!app.isFormValid){
					return;
				}

				var params = {
					waitlistid: app.activeWaitlist.w_id,
					wdata: app.wdata,
					isEditing: app.isEditing
				};

				var formData = methods.formData(params);

				//if(app.isEditing){
				var url = window.App.baseUrl +"Waitlist/updateWaitlistData";
				// }else{
				// 	var url = window.App.baseUrl +"Waitlist/addnewWaitlist";
				// }

				axios.post(url, formData).then(function (e) {
					if(e.data.success){
						app.searchWaitlist();
						swal.close();
						swal.fire('Info',e.data.message,'success');
						$('#newWaitlistModal').modal('hide');
						methods.destroyModalData();
						//methods.toastr('success','Success',e.data.message);
					}else{
						methods.errorFormValidation(e.data.message);
						swal.fire('Error','Please Input Required Fields.','error');
					}
				});
			},
			editWaitlist(data){		
				//populate muni
				app.isEditing = true;
				this.getLocation('mun_code', data.prov_code, "present");
				this.getLocation('bar_code', data.mun_code, "present");
				this.getLocation('mun_code', data.permanent_prov_code, "permanent");
				this.getLocation('bar_code', data.permanent_mun_code, "permanent");

				app.probableDuplicate.probableActiveData = '';

				app.wdata.lastname = data.lastname;
				app.wdata.firstname = data.firstname;
				app.wdata.middlename = data.middlename;
				app.wdata.extname = data.extname ;
				app.wdata.respondentName = data.respondentName ;
				app.wdata.province_present = data.prov_code;
				app.wdata.municipality_present = data.mun_code;
				app.wdata.barangay_present = data.bar_code;
				app.wdata.address_present = data.address;
				app.wdata.street_present = data.street;
				app.wdata.province_permanent = data.permanent_prov_code;
				app.wdata.municipality_permanent = data.permanent_mun_code;
				app.wdata.barangay_permanent = data.permanent_bar_code;
				app.wdata.address_permanent = data.permanent_address;
				app.wdata.street_permanent = data.permanent_street;
				app.wdata.gender = data.gender;
				app.wdata.dateofbirth = data.birthdate;
				app.wdata.input_grantee = data.grantee;				

				var d = data.birthdate.split('-');
				
				var bdate = new Date(d[0],d[1],d[2]);
				var diff_ms = Date.now() - bdate;
			    var age_dt = new Date(diff_ms); 
			    var age = Math.abs(age_dt.getUTCFullYear() - 1970);

				app.wdata.view_age = age;

				app.wdata.birthplace = data.birthplace;
				app.wdata.hhid = data.hh_id;
				app.wdata.hhsize = data.hh_size;
				app.wdata.contactno = data.contact_no;
				app.wdata.oscaid = data.osca_id ;
				app.wdata.maritalstatus = data.marital_status;
				app.wdata.mothersMaidenName = data.mothersMaidenName;
				app.wdata.livingArrangement = data.livingArrangement;
				app.wdata.caregivername = data.nameofCaregiver;
				app.wdata.caregiverrelp = data.relationshipofCaregiver;
				app.wdata.rep2name = data.repname2;
				app.wdata.rep2rel = data.reprel2;
				app.wdata.rep3name = data.repname3;
				app.wdata.rep3rel = data.reprel3;
				app.wdata.reference_code = data.reference_code;

				

				var d_aa = "";

				var urls = window.App.baseUrl + 'Waitlist/getWaitlistDetails';
				axios.get(urls, {params: {reference_code: data.reference_code}}).then(function (e){
					
					app.wdata.pensionreceiver = parseInt(e.data.wdetails.pension_receiver);
					app.wdata.pensionsreceived_dswd = parseInt(e.data.wdetails.pension_dswd);
					app.wdata.pensionsreceived_gsis = parseInt(e.data.wdetails.pension_gsis);
					app.wdata.pensionsreceived_sss = parseInt(e.data.wdetails.pension_sss);
					app.wdata.pensionsreceived_afpslai = parseInt(e.data.wdetails.pension_afpslai);
					app.wdata.pensionsreceived_others = e.data.wdetails.pension_others != '' ? 1 : 0;
					app.wdata.pensionsreceived_other = e.data.wdetails.pension_others;
					app.wdata.income_wages = e.data.wdetails.income_wages == 1 || e.data.wdetails.income_wages == 0 ? 1 : 0;
					//app.wdata.ans4 = parseInt(e.data.wdetails.income_wages);
					app.wdata.ans4 = e.data.wdetails.income_wages;
					app.wdata.ans4_amt = e.data.wdetails.income_wages_amt;
					app.wdata.income_entrep = e.data.wdetails.income_entrep == 1 || e.data.wdetails.income_entrep == 0 ? 1 : 0;
					app.wdata.ans6 = parseInt(e.data.wdetails.income_entrep);
					app.wdata.ans6_amt = e.data.wdetails.income_entrep_amt;
					app.wdata.income_household = e.data.wdetails.income_household == 1 || e.data.wdetails.income_household == 0 ? 1 : 0;
					app.wdata.ans8 = parseInt(e.data.wdetails.income_household);
					app.wdata.ans8_amt = e.data.wdetails.income_household_amt;
					app.wdata.income_domestic = e.data.wdetails.income_domestic == 1 || e.data.wdetails.income_domestic == 0 ? 1 : 0;
					app.wdata.ans10 = parseInt(e.data.wdetails.income_domestic);
					app.wdata.ans10_amt = e.data.wdetails.income_domestic_amt;
					app.wdata.income_international = e.data.wdetails.income_international == 1 || e.data.wdetails.income_international == 0 ? 1 : 0;
					app.wdata.ans12 = parseInt(e.data.wdetails.income_international);
					app.wdata.ans12_amt = e.data.wdetails.income_international_amt;
					app.wdata.income_friends = e.data.wdetails.income_friends == 1 || e.data.wdetails.income_friends == 0 ? 1 : 0;
					app.wdata.ans14 = parseInt(e.data.wdetails.income_friends);
					app.wdata.ans14_amt = e.data.wdetails.income_friends_amt;
					app.wdata.income_government = e.data.wdetails.income_government == 1 || e.data.wdetails.income_government == 0 ? 1 : 0;
					app.wdata.ans16 = parseInt(e.data.wdetails.income_government);
					app.wdata.ans16_amt = e.data.wdetails.income_governement_amt;
					app.wdata.income_others = e.data.wdetails.income_others == 1 || e.data.wdetails.income_others == 0 ? 1 : 0;
					app.wdata.ans18 = parseInt(e.data.wdetails.income_others_amt);
					app.wdata.ans18_amt = e.data.wdetails.income_others_amt;
					app.wdata.sourcesOfIncome_other = e.data.wdetails.pension_others;
					
					let frailty_older85 = e.data.wdetails.frailty_older85;
					if(frailty_older85 == null){
						if(age > 85){
					    	frailty_older85 = "1";
					    } else {
					    	frailty_older85 = "0";
					    }
					} 

					app.wdata.ans20 = frailty_older85;
					app.wdata.ans21 = e.data.wdetails.frailty_healthlimit;
					app.wdata.ans22 = e.data.wdetails.frailty_needregularhelp;
					app.wdata.ans23 = e.data.wdetails.frailty_healthhome;
					app.wdata.ans24 = e.data.wdetails.frailty_countonsomeone;
					app.wdata.ans25 = e.data.wdetails.frailty_moveabout;
					app.wdata.disability = e.data.wdetails.disability_id;
					app.wdata.illness = e.data.wdetails.illness;
					app.wdata.ans27 = e.data.wdetails.illness != 'NONE' ? 1 : 0 ;
					app.wdata.sp_food = parseInt(e.data.wdetails.sp_food);
					app.wdata.sp_med = parseInt(e.data.wdetails.sp_med);
					app.wdata.sp_checkup = parseInt(e.data.wdetails.sp_checkup);
					app.wdata.sp_cloth = parseInt(e.data.wdetails.sp_cloth);
					app.wdata.sp_util = parseInt(e.data.wdetails.sp_util);
					app.wdata.sp_debt = parseInt(e.data.wdetails.sp_debt);
					app.wdata.sp_entrep = parseInt(e.data.wdetails.sp_entrep);
					app.wdata.sp_others = e.data.wdetails.sp_others != '' ? 1 : 0;
					app.wdata.ans28_other = e.data.wdetails.sp_others;
					app.wdata.workerName = e.data.wdetails.worker_name;
					//app.wdata.date_accomplished = new Date(e.data.wdetails.date_accomplished);
					var d = new Date(e.data.wdetails.date_accomplished);
					var month = '' + (d.getMonth() + 1);
					var day = '' + d.getDate();
					var year = d.getFullYear();
					if (month.length < 2) month = '0' + month;
					if (day.length < 2) day = '0' + day;
					formatted_date = [year, month, day].join('-');
					app.wdata.date_accomplished =  formatted_date;
				})	
				$('.form-group').addClass('focused');
			},
			addWaitlist(){	
				app.isEditing = false; 
				app.sameAddress = 0;
				//this.clearRepReasonModal();
				app.wdata.lastname = "" ;
				app.wdata.firstname = "" ;
				app.wdata.middlename = "" ;
				app.wdata.extname = "" ;
				app.wdata.respondentName = "" ;
				app.wdata.province_present = "" ;
				app.wdata.municipality_present = "" ;
				app.wdata.barangay_present = "" ;
				app.wdata.address_present = "" ;
				app.wdata.street_present = "" ;
				app.wdata.province_permanent = "" ;
				app.wdata.municipality_permanent = "" ;
				app.wdata.barangay_permanent = "" ;
				app.wdata.address_permanent = "" ;
				app.wdata.street_permanent = "" ;
				app.wdata.gender = "" ;
				app.wdata.dateofbirth = ""  ;
				app.wdata.birthplace = "" ;
				app.wdata.hhid = "" ;
				app.wdata.hhsize = "" ;
				app.wdata.contactno = "" ;
				app.wdata.oscaid = ""  ;
				app.wdata.maritalstatus = "" ;
				app.wdata.mothersMaidenName = "" ;
				app.wdata.livingArrangement = "" ;
				app.wdata.caregivername = "" ;
				app.wdata.caregiverrelp = "" ;
				app.wdata.rep2name = "" ;
				app.wdata.rep2rel = "" ;
				app.wdata.rep3name = "" ;
				app.wdata.rep3rel = "" ;
                app.wdata.reference_code = "" ;
                app.wdata.pensionreceiver = "" ;
                app.wdata.pensionsreceived_dswd = "" ;
                app.wdata.pensionsreceived_gsis = "" ;
                app.wdata.pensionsreceived_sss = "" ;
                app.wdata.pensionsreceived_afpslai = "" ;
                app.wdata.pensionsreceived_others = "" ;
                app.wdata.pensionsreceived_other = "" ;
                app.wdata.income_wages = "" ;
                app.wdata.ans4 = "" ;
                app.wdata.ans4_amt = "" ;
                app.wdata.income_entrep = "" ;
                app.wdata.ans6 = "" ;
                app.wdata.ans6_amt = "" ;
                app.wdata.income_household = "" ;
                app.wdata.ans8 = "" ;
                app.wdata.ans8_amt = "" ;
                app.wdata.income_domestic = "" ;
                app.wdata.ans10 = "" ;
                app.wdata.ans10_amt = "" ;
                app.wdata.income_international = "" ;
                app.wdata.ans12 = "" ;
                app.wdata.ans12_amt = "" ;
                app.wdata.income_friends = "" ;
                app.wdata.ans14 = "" ;
                app.wdata.ans14_amt = "" ;
                app.wdata.income_government = "" ;
                app.wdata.ans16 = "" ;
                app.wdata.ans16_amt = "" ;
                app.wdata.income_others = "" ;
                app.wdata.ans18 = "" ;
                app.wdata.ans18_amt = "" ;
                app.wdata.sourcesOfIncome_other = "" ;
                app.wdata.ans20 = "" ;
                app.wdata.ans21 = "" ;
                app.wdata.ans22 = "" ;
                app.wdata.ans23 = "" ;
                app.wdata.ans24 = "" ;
                app.wdata.ans25 = "" ;
                app.wdata.disability = "" ;
                app.wdata.illness = "" ;
                app.wdata.ans27 = "" ;
                app.wdata.sp_food = "" ;
                app.wdata.sp_med = "" ;
                app.wdata.sp_checkup = "" ;
                app.wdata.sp_cloth = "" ;
                app.wdata.sp_util = "" ;
                app.wdata.sp_debt = "" ;
                app.wdata.sp_entrep = "" ;
                app.wdata.sp_others = "" ;
                app.wdata.ans28_other = "" ;
                app.wdata.workerName = "" ;
                app.wdata.date_accomplished =  "";
                app.wdata.input_grantee = "";
                app.probableDuplicate.clean = '';
                app.probableDuplicate.probableActiveData = '';				
			},		
			exportWaitlist(){
				var urls = window.App.baseUrl +"Waitlist/exportWaitlist?prov_code="+app.exportdata.prov_code+"&mun_code="+app.exportdata.mun_code+"&status="+app.exportdata.status;

				$('#exportWaitlistModal').modal('hide');
				window.open(urls, '_blank');
			},	
			dlWaitlistTemplate(){
				var urls = window.App.baseUrl +"Waitlist/waitlisttemplate?prov_code="+app.waitlisttemp.prov_code+"&mun_code="+app.waitlisttemp.mun_code;

				$('#WaitlistTemplateModal').modal('hide');
				app.waitlisttemp.prov_code = "";
				app.waitlisttemp.mun_code = "";
				app.waitlisttemp.municipalities = [];

				window.open(urls, '_blank');
			},
			dlBlankBuf(){
				var urls = window.App.baseUrl +"Waitlist/blankBUF";
				window.open(urls, '_blank');
			},
			download_template(){
				var urls = window.App.baseUrl +"Waitlist/dlTemplate";
				$('#importwaitinglistModal').modal('hide');
				window.open(urls, '_blank');
			},
			downloadList(){
				var urls = window.App.baseUrl +"download-waitlist?prov_code="+global_search.search.prov_code+"&mun_code="+global_search.search.mun_code+"&status="+global_search.search.status+"&birth_from="+global_search.search.birth_from+"&birth_to="+global_search.search.birth_to;
				window.open(urls, '_blank');
			},
			format_Date(date){
				var d = new Date(date);
				var month = '' + (d.getMonth() + 1);
				var day = '' + d.getDate();
				var year = d.getFullYear();
				if (month.length < 2) month = '0' + month;
				if (day.length < 2) day = '0' + day;
				formatted_date = [year, month, day].join('-');
				return  formatted_date;
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
					case "15":
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
			setForReplacement(){			
				//reason, remarks, memberid
				app.repreason.mem_id = app.activeWaitlist.SPID;
				var formData = methods.formData(app.repreason);

				var urls = window.App.baseUrl + 'Member/setForReplacementIndividual';
				axios.post(urls, formData).then(function (e) {
					if(e.data.success){					
						
						app.searchWaitlist();
						methods.toastr('success','Success',e.data.message);
						app.resetModalFormOnClose();
						app.clearRepReasonModal();		
						methods.clearError();	
					}else{
						methods.errorFormValidation(e.data.message);
					}
				})
			},
			UpdateWaitlistStatus(type = 'single', col="priority", val=1, message=""){
				swal.fire({
					title: 'Warning',
					text: "Are you sure you want to Update this member's status?",
					icon: 'warning',
					showCancelButton: true,								
					confirmButtonText: 'Yes, change status!',
					cancelButtonText: 'No, cancel!',
					buttonsStyling: false	
				  }).then((result) => {
					if (result.value) {
						
						showloading();
						var urls = window.App.baseUrl +"Waitlist/UpdateWaitlistStatus";

						if(type == 'single'){
							pensionList = {w_id : app.activeWaitlist.w_id};
						}else{
							pensionList = app.markedRows;
						}
						var params = {
							pensionersList: pensionList,
							col: col,
							val: val,
							message: message
						};

						var formData = methods.formData(params);
						
						axios.post(urls, formData).then(function (e) {
							console.log("ENTER RESPONSE");
							if(e.data.success){
								app.searchWaitlist();
								swal.close();
								swal.fire('Info',e.data.message,'success');
								
								//methods.toastr('success','Success',e.data.message);
							}else{
								swal.fire('Error',e.data.message,'error');
							}
						})
					} else if ( result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled','Data was not updated.','error')
					}
				  })
			},
			archiveWaitlist(type = 'single'){
				swal.fire({
					title: 'Warning',
					text: "Are you sure you want to Delete this waitlist??",
					icon: 'warning',
					showCancelButton: true,								
					confirmButtonText: 'Yes, Delete waitlist!',
					cancelButtonText: 'No, cancel!',
				  }).then((result) => {
					if (result.value) {

						showloading();
						var urls = window.App.baseUrl +"Waitlist/archiveWaitlist";

						if(type == 'single'){
							pensionList = {
								w_id : app.activeWaitlist.w_id,
								reason_id : app.archive_reason,
								remarks : app.archive_remarks,
							};
						}else{
							pensionList = app.markedRows;
						}
						var params = {
							pensionersList: pensionList,
						};

						var formData = methods.formData(params);
						
						axios.post(urls, formData).then(function (e) {
							console.log("ENTER RESPONSE");
							if(e.data.success){
								app.searchWaitlist();
								swal.close();
								swal.fire('Info',e.data.message,'success');
							}else{
								swal.fire('Error',e.data.message,'error');
							}
						})
					} else if ( result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled','Data was not updated.','error')
					}
				  })
			},
			AddAsNewBene(type = ''){
				swal.fire({
					title: 'Warning',
					text: "Are you sure you want to Add this member as new beneficiary?",
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
						if(type == 'single'){
							var urls = window.App.baseUrl +"Waitlist/addAsNewBeneficiary";
							var params = {
								w_id: app.activeWaitlist.w_id
							};
						}else{
							var urls = window.App.baseUrl +"Waitlist/BulkaddAsNewBene";
							var params = {
								eligiblePensioners: app.markedRows
							};
						}
						console.log(params);
						var formData = methods.formData(params);
						
						axios.post(urls, formData).then(function (e) {
							if(e.data.success){
								app.searchWaitlist();
								swal.close();
								swal.fire('Info',e.data.message,'success');
								//methods.toastr('success','Success',e.data.message);
							}else{
								swal.close();
								swal.fire('Error',e.data.message,'error');
							}
						})
					} else if ( result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled','Data was not updated.','error')
					}
				  })
			},
			ClearSearch(){
				global_search.search.prov_code = '';
				global_search.search.mun_code = '';
				global_search.search.bar_code = '';
				global_search.search.gender = '';
				global_search.search.status = '';
				global_search.search.birth_from = '';
				global_search.search.birth_to = '';
				app.location.municipalities = [];
				app.location.barangays = [];
				app.searchWaitlist();
			},
			copyAddress(status){
				if(status == 1){

					app.wdata.province_present     = app.wdata.province_permanent;
					app.wdata.municipality_present = app.wdata.municipality_permanent;
					app.wdata.barangay_present     = app.wdata.barangay_permanent;
					app.wdata.address_present      = app.wdata.address_permanent;
					app.wdata.street_present       = app.wdata.street_permanent;

					if( app.wdata.province_present != ''){
						app.getLocation('mun_code',app.wdata.province_present,'present');	
					}

					if( app.wdata.municipality_present != ''){
						app.getLocation('bar_code',app.wdata.municipality_present,'present');
					}										

				} else {
					app.wdata.province_present     = '';
					app.wdata.municipality_present = '';
					app.wdata.barangay_present     = '';
					app.wdata.address_present      = '';
					app.wdata.street_present      = '';


				}

			},
			checkUser(){
				var urls = window.App.baseUrl + 'get-login-user';
				axios.get(urls).then(function (e) {
					console.log(e.data);
					app.userrole = e.data.role;
				})
			},
			////// END EVENTS ///////////////

		},
		mounted: function () {
			this.getAllLocation();
			this.getallLibrary();
			this.checkUser();			
			$(".modal-form").on('hidden.bs.modal', function () {			
				app.resetModalFormOnClose();
			});
			this.getReplacementReason();
		},
	})
}


Vue.use(VueTables.ServerTable);
Vue.component('v-select', VueSelect.VueSelect);
if ($('#m_index').length) {
	var global_search = new Vue({
		data: {
			search: {
				prov_code: '',
				mun_code: '',
				bar_code: '',
				gender: '',
				status: ''
			},
		}
	})
	var mem = new Vue({
		el: '#m_index',
		data: {
			activeSP: {},
			btnReplace: true,
			global_search: global_search,
			ReplacementReason: {},
			repreason: {
				isDoubleEntry: false,
				isDateOfDeath: false,
				isTransfered: false,
				isWithPension: false,
				isWithIncome: false,
				isOthers: false,
				mem_id: '',
				reason_id: '',
				duplicate: '',
				dateofdeath: '',
				placeoftransfer: '',
				pension_select: '',
				otherreason: '',
			},
			wdata: {
				data: {
					barangay: "",
					birthdate: "",
					age: 0,
					w_id: 0,
					label: ""
				},
				w_id: "",
				m_id: "",
				work_name: "",
				dateAccomplish: "",
				dateofreplacement: "",
				liquidation: []
			},
			woptions: [{ w_id: "", label: "" }],
			masterlist: {
				column: [
					"No",
					"SPID",
					"Full_Name",
					"Birth_Date",
					"Age",
					"Gender",
					"SP_Status",
					"Registration_Date",
					"Province",
					"Municipality",
					"Barangay",
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

						var urls = window.App.baseUrl + 'get-all-Members';
						return axios.get(urls, datas)
							.catch(function (e) {
								this.dispatch('error', e);
							}.bind(this));
					},
					filterable: ['Gender', 'Province', "Municipality", "Barangay"],
					filterbyColumn: true,
					sortIcon: {
						base: 'fa',
						is: 'fa-sort',
						up: 'fa-sort-asc',
						down: 'fa-sort-desc'
					},
					sortable: ['Full_Name', 'Birth_Date', 'Age', 'Gender', 'Province', "Municipality", "Barangay"]
				}
			},
			location: {
				prov_names: [],
				mun_names: [],
				bar_names: [],
				provinces: [],
				municipalities: [],
				barangays: [],
			},
			memPayments: {
				spstatus: "",
				new: true,
				activeMP: {
					p_id: "",
					prov_code: "",
					mun_code: "",
					bar_code: "",
					spid: "",
					year: "",
					period: "",
					date_receive: "",
					liquidation: "0",
					receiver: "",
					amount: 3000,
					remarks: "",
				},
				data: [],
				selected: [],
				selectAll: false,
			},
			filterWaitlist: {
				prov_code: '',
				mun_code: '',
			},
			waitlistLocation: {
				provinces: [],
				municipalities: [],
				prov_names: [],
				mun_names: [],
			},
		}, 
		computed:{
			memberReplaceDisable() {
				let repButton = this.btnReplace;
				if(repButton == true){
					let w_id = this.wdata.w_id;
					let dateofreplacement = this.wdata.dateofreplacement;
					let work_name = this.wdata.work_name;
					let dateAccomplish = this.wdata.dateAccomplish;
	
					if (w_id =="" || dateofreplacement =="" ||	work_name =="" || work_name == undefined || dateAccomplish =="" || dateAccomplish  == undefined ) {
						return true;
					}else{
						return false;
					}
				}else{
					return true;
				}
			}
		},
		methods: {
			//GET Libraries
			getAllLocation() {
				var urls = window.App.baseUrl + 'get-all-location';
				axios.get(urls).then(function (e) {
					console.log(e.data);
					mem.location.prov_names = e.data.provinces;
					mem.location.mun_names = e.data.municipalities;
					mem.location.bar_names = e.data.barangays;
					mem.location.provinces = e.data.provinces;

					mem.waitlistLocation.provinces = e.data.provinces;
					mem.waitlistLocation.prov_names = e.data.provinces;
					mem.waitlistLocation.mun_names = e.data.municipalities;
				})
			},
			getprovname(prov_code) {
				var prov_name = "";
				mem.location.prov_names.forEach(prov => {
					if (prov.prov_code == prov_code) {
						prov_name = prov.prov_name;
					}
				});
				return prov_name;
			},
			getmunname(prov_code, mun_code) {
				var mun_name = "";
				if (mem.location.mun_names.hasOwnProperty(prov_code)) {
					var munlist = mem.location.mun_names[prov_code];
					munlist.forEach(mun => {
						if (mun.mun_code == mun_code) {
							mun_name = mun.mun_name;
						}
					});
				}
				return mun_name;
			},
			getbarname(mun_code, bar_code) {
				var bar_names = "";
				if (mem.location.bar_names.hasOwnProperty(mun_code)) {
					var barlist = mem.location.bar_names[mun_code];
					barlist.forEach(bar => {
						if (bar.bar_code == bar_code) {
							bar_names = bar.bar_name;
						}
					});
				}
				return bar_names;
			},
			getFullname(lastname, firstname, middlename = "", extname = "") {
				var fullname = lastname + ", " + firstname + " " + middlename + " " + extname;
				return fullname.toUpperCase();
			},
			getAge(bday) {
				if (bday == null || bday == "") { return ""; }
				var dob = bday;
				var dob = dob.split("-");
				var dob = new Date(dob[0], dob[1], dob[2])
				var diff_ms = Date.now() - dob.getTime();
				var age_dt = new Date(diff_ms);
				return Math.abs(age_dt.getUTCFullYear() - 1970);
			},
			getReplacementReason() {
				var urls = window.App.baseUrl + 'get-all-ReplacementReason';
				axios.get(urls, {
					params: {}
				}).then(function (e) {
					mem.ReplacementReason = e.data;
				})
			},
			//END Get Libraries
			selectData(data) {
				//console.log(data);
				mem.activeSP = data;
				$('.form-group').addClass('focused');
			},
			getEligibleWaitlist(spid = 0) {
				this.btnReplace = true;
				mun_code = mem.activeSP.city;
				mem.memPayments = {
					spstatus: "",
					new: true,
					activeMP: {
						p_id: "",
						prov_code: "",
						mun_code: "",
						bar_code: "",
						spid: "",
						year: "",
						period: "",
						date_receive: "",
						liquidation: "0",
						receiver: "",
						amount: 3000,
						remarks: "",
					},
					data: []
				};

				mem.wdata = {
					data: {
						barangay: "",
						birthdate: "",
						age: 0,
						w_id: 0,
					},
					w_id: "",
					m_id: "",
					work_name: "",
					dateAccomplish: "",
					dateofreplacement: "",
					liquidation: []
				}
				showloading();
				var urls = window.App.baseUrl + "get-Eligible-Waitlist";
				var params = { mun_code: mun_code };
				this.getMemPayment(spid);
				axios.get(urls, { params: params }).then(function (e) {
					console.log(e.data);
					swal.close();
					if (e.data.success) {
						$('#replaceMember').modal('show');
						mem.woptions = e.data.data;
					} else {
						swal.fire('Error', "There's No available eligible waitlist for the Municipality", 'error');
						$('#replaceMember').modal('hide');
					}
				})
			},
			replaceMember() {
				console.log("replaceMember")
				this.btnReplace = false;
				mem.wdata.m_id = mem.activeSP.b_id;
				var formData = methods.formData(mem.wdata);
				var urls = window.App.baseUrl + 'replace-Member';
				
				axios.post(urls, formData).then(function (e) {
					if (e.data.success) {
						console.log(e.data.message);
						mem.searchMember();
						methods.toastr('success', 'Success', e.data.message);
						$('#replaceMember').modal('hide');
					} else {
						swal.fire('Error', "Something Went Wrong. Please Contact Your Administrator", 'error');
					}
				})
			},
			addToTransferReplaceMember(p_id = 0, isChecked) {
				console.log(p_id);
				// mem.wdata.liquidation = "test";
				mem.wdata.liquidation.push(
					{
						p_id: p_id,
					}
				);
				console.log(mem.wdata);

			},
			editMember() {
				window.location.href = window.App.baseUrl + 'member-Edit' + '?spid=' + mem.activeSP.SPID;
				// var urls = window.App.baseUrl + 'member-Edit';
				// var params = {spid : mem.activeSP.SPID};
				// axios.post(urls,params).then(function (e) {})
			},
			searchMember(type = '', obj = "") {
				var datas = {
					params: {
						limit: 10,
						page: 1,
						byColumn: 1,
						ascending: "ASC"
					}
				};
				datas.params.condition = global_search.search;

				console.log(datas);
				var urls = window.App.baseUrl + 'get-all-Members';
				axios.get(urls, datas)
					.then(function (e) {
						mem.$refs.servermembertable.data = e.data.data;
						mem.$refs.servermembertable.count = parseInt(e.data.count);
					})
			},
			clearRepReasonModal() {
				mem.repreason = {
					isDoubleEntry: false,
					isDateOfDeath: false,
					isTransfered: false,
					isWithPension: false,
					isWithIncome: false,
					isOthers: false,
					mem_id: '',
					reason_id: '',
					reason_desc: '',
					otherreason: '',
					pension_select: '',
				};
			},
			resetModalFormOnClose() {
				$('#setToForReplacement').modal('hide');
				//$('#editPPMPModal').modal('hide');
				methods.destroyModalData();
				mem.clearRepReasonModal();
			},
			////// EVENTS //////////////////
			getLocation(type = 'prov_code', val = "") {
				if (type == 'mun_code') {
					global_search.search.mun_code = '';
					mem.location.barangays = [];
					mem.location.municipalities = mem.location.mun_names[val];
				} else if (type == 'bar_code') {
					global_search.search.bar_code = '';
					mem.location.barangays = mem.location.bar_names[val];
				} else {
					mem.location.provinces = mem.location.prov_names;
				}
			},
			reason_onchange(val = "") {
				mem.repreason.reason_id = val;
				mem.repreason.reason_desc = "";
				mem.repreason.otherreason = "";
				mem.repreason.pension_select = "";
				switch (val) {
					case "1":
						mem.repreason.isDateOfDeath = false;
						mem.repreason.isTransfered = false;
						mem.repreason.isWithPension = false;
						mem.repreason.isWithIncome = false;
						mem.repreason.isOthers = false;
						mem.repreason.isDoubleEntry = true;
						break;
					case "2":
						mem.repreason.isDateOfDeath = true;
						mem.repreason.isTransfered = false;
						mem.repreason.isWithPension = false;
						mem.repreason.isWithIncome = false;
						mem.repreason.isOthers = false;
						mem.repreason.isDoubleEntry = false;
						break;
					// case "4":
					case "3":
						mem.repreason.isWithPension = true;
						mem.repreason.isWithIncome = false;
						mem.repreason.isDateOfDeath = false;
						mem.repreason.isTransfered = false;
						mem.repreason.isOthers = false;
						mem.repreason.isDoubleEntry = false;
						break;
					case "12":
						mem.repreason.isWithIncome = true;
						mem.repreason.isWithPension = false;
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
			change_sp_status() {
				//reason, remarks, memberid
				mem.repreason.mem_id = mem.activeSP.SPID;
				var formData = methods.formData(mem.repreason);

				var urls = window.App.baseUrl + 'set-ForReplacementIndividual';
				axios.post(urls, formData).then(function (e) {
					if (e.data.success) {
						console.log(e.data.message);
						mem.searchMember();
						methods.toastr('success', 'Success', e.data.message);
						mem.resetModalFormOnClose();
						mem.clearRepReasonModal();
						methods.clearError();
					} else {
						methods.errorFormValidation(e.data.message);
					}
				})
			},
			setToActive() {

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
						var urls = window.App.baseUrl + "set-ActiveIndividual";
						var params = { bid: mem.activeSP.b_id };
						axios.get(urls, { params: params }).then(function (e) {
							if (e.data.success) {
								mem.searchMember();
								methods.toastr('success', 'Success', e.data.message);
							} else {
								swal.fire('Error', e.data.message, 'error');
							}
						})
					} else if (result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled', 'SP status not changed.', 'error')
					}
				})
			},
			exportMasterlist() {
				var urls = window.App.baseUrl + "download-member?prov_code=" + global_search.search.prov_code + "&mun_code=" + global_search.search.mun_code + "&bar_code=" + global_search.search.bar_code + "&status=" + global_search.search.status;
				window.open(urls, '_blank');
			},
			ClearSearch() {
				global_search.search.prov_code = '';
				global_search.search.mun_code = '';
				global_search.search.bar_code = '';
				global_search.search.gender = '';
				global_search.search.status = '';
				mem.location.municipalities = [];
				mem.location.barangays = [];
				mem.searchMember();
			},
			////// END EVENTS ///////////////
			////// Payment History /////////
			formatDate(pdate) {
				let date = new Date(pdate);
				let mo = (date.getMonth() + 1);
				let m = (mo < 10 ? '0' : '') + mo;
				let d = (date.getDate() < 10 ? '0' : '') + date.getDate();
				return (date.getFullYear()) + "-" + m + "-" + d;
			},
			getMemPayment(spid, sptatus = "") {
				mem.memPayments.new = true;
				mem.memPayments.activeMP.p_id = "";
				mem.memPayments.activeMP.prov_code = "";
				mem.memPayments.activeMP.mun_code = "";
				mem.memPayments.activeMP.bar_code = "";
				mem.memPayments.activeMP.spid = "";
				mem.memPayments.activeMP.year = "";
				mem.memPayments.activeMP.period = "";
				mem.memPayments.activeMP.date_receive = "";
				mem.memPayments.activeMP.liquidation = "0";
				mem.memPayments.activeMP.receiver = "";
				mem.memPayments.activeMP.amount = 3000;
				mem.memPayments.activeMP.remarks = "";
				mem.memPayments.spstatus = sptatus;

				var frmdata = { "spid": spid };
				var data = methods.formData(frmdata);
				var urls = window.App.baseUrl + "get-member-payment";
				axios.post(urls, data)
					.then(function (e) {
						mem.memPayments.data = e.data;
					})
					.catch(function (error) {
						console.log(error)
					});
			},
			getPaymentStatus(ps) {
				
				if(ps == 0){ return  "UNPAID";}
				else if(ps == 1){ return  "PAID";}
				else if(ps == 2){ return  "TRANSFERED";}
				else if(ps == 3){ return  "OFFSET";}
				else if(ps == 4){ return  "ON HOLD";}
			},
			editSelect(data) {
				mem.memPayments.new = false;

				mem.memPayments.activeMP.p_id = data.p_id;
				mem.memPayments.activeMP.year = data.year;
				mem.memPayments.activeMP.date_receive = this.formatDate(data.date_receive);
				mem.memPayments.activeMP.liquidation = data.liquidation;
				mem.memPayments.activeMP.amount = data.amount;
				mem.memPayments.activeMP.receiver = data.receiver;
				mem.memPayments.activeMP.remarks = data.remarks;

				//assign period base on data mode of payment and period
				if (data.mode_of_payment.toUpperCase() == "SEMESTER") {
					if (data.period == 1) { mem.memPayments.activeMP.period = "5"; }
					else { mem.memPayments.activeMP.period = "6"; }
				} else {
					mem.memPayments.activeMP.period = data.period;
				}

				//extract mode of payment and period

			},
			deleteMemPayment(data) {
				var spid = data.spid;
				var year = data.year;
				var period = data.period;
				var p_id = data.p_id;
				var textmsg = "Are sure you want to DELETE payment of " + p_id + "-" + spid + "? ";
				textmsg += "Note that by doing so, you are changing the list in the generated payroll. "
				textmsg += "By clicking the confirm button you agree that you are ACCOUNTABLE TO any discrepancies on the data due to this action."
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
						var urls = window.App.baseUrl + "delete-member-payment";
						var frmdata = { "p_id": p_id, "spid": spid, "year": year, "period": period };
						var formData = methods.formData(frmdata);
						axios.post(urls, formData).then(function (e) {
							if (e.data.success) {
								mem.getMemPayment(spid);
								swal.close();
								swal.fire('Info', e.data.message, 'success');
							} else {
								swal.fire('Error', e.data.message, 'error');
							}
						})
					} else if (result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled', 'Action Cancelled', 'error')
					}
				})

			},
			newSelect() {
				mem.memPayments.new = true;
				mem.memPayments.activeMP.year = "";
				mem.memPayments.activeMP.period = "";
				mem.memPayments.activeMP.date_receive = "";
				mem.memPayments.activeMP.liquidation = "0";
				mem.memPayments.activeMP.receiver = "";
				mem.memPayments.activeMP.amount = 3000;
				mem.memPayments.activeMP.remarks = "";
			},
			getregistration(additional){			
				var ret = "";
				if(additional != "" && additional != "null"  && additional != null && additional != 0){
					ret += "ADDDITIONAL - ";
					ret += additional;
				}
				return ret;
			},	
			submitPayment() {
				mem.memPayments.activeMP.spid = mem.activeSP.SPID;
				mem.memPayments.activeMP.prov_code = mem.activeSP.province;
				mem.memPayments.activeMP.mun_code = mem.activeSP.city;
				mem.memPayments.activeMP.bar_code = mem.activeSP.barangay;
				if (mem.memPayments.new == true) {
					this.addMemPayment();
				} else {
					this.updateMemPayment();
				}
			},
			addMemPayment() {
				var spid = mem.memPayments.activeMP.spid;
				var textmsg = "Are sure you want to add payment for " + spid + "? ";
				textmsg += "Note that by doing so, you are changing the list in the generated payroll. "
				textmsg += "By clicking the confirm button you agree that you are ACCOUNTABLE TO any discrepancies on the data due to this action."
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
						var urls = window.App.baseUrl + "add-member-payment";
						var formData = methods.formData(mem.memPayments.activeMP);
						axios.post(urls, formData).then(function (e) {
							if (e.data.success) {
								mem.getMemPayment(spid);
								swal.close();
								swal.fire('Info', e.data.message, 'success');
							} else {
								swal.fire('Error', e.data.message, 'error');
							}
						})
					} else if (result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled', 'Action Cancelled', 'error')
					}
				})
			},
			updateMemPayment() {
				var textmsg = "Are sure you want to update payment details of " + mem.memPayments.activeMP.spid + "? ";
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
						var urls = window.App.baseUrl + "update-member-payment";
						var formData = methods.formData(mem.memPayments.activeMP);
						axios.post(urls, formData).then(function (e) {
							if (e.data.success) {
								mem.getMemPayment(mem.memPayments.activeMP.spid);
								swal.close();
								swal.fire('Info', e.data.message, 'success');
							} else {
								swal.fire('Error', e.data.message, 'error');
							}
						})
					} else if (result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled', 'Action Cancelled', 'error')
					}
				})
			},
			getUrl: function (spid, type = 1) {
				if (type == 1) {
					return window.App.baseUrl + 'view-member/' + spid;
				} else {
					return window.App.baseUrl + 'edit-member/' + spid;
				}
			},

			getMemberTransferInfo(payrollInfo) {
				showloading();
				var urls = window.App.baseUrl + "member/memberTransferInfo";
				var datas = { "spid": mem.activeSP.SPID };
				var formData = frmdata(datas);
				axios.post(urls, formData).then(function (e) {
					console.log(e.data)
					let mydata = e.data;
					console.log(mydata);
					swal.close();
					if (mydata.success) {
						var textmsg = "Are sure you want to Transfer <b>" + mem.activeSP.lastname + ", " + mem.activeSP.firstname + "</b> to REPLACER <b>" + mydata.data.fullname + "</b>?";
						swal.fire({
							title: 'Warning',
							html: textmsg,
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
								var urls = window.App.baseUrl + "member/memberTransfer";
								var datas = {
									"tranSPID": mydata.data.connum,
									"tran_provcode": mydata.data.province,
									"tran_muncode": mydata.data.city,
									"tran_barcode": mydata.data.barangay,
									"tran_fullname": mydata.data.fullname,
									"curSPID": mem.activeSP.SPID,
									"curb_id": mem.activeSP.b_id,
									"p_id": payrollInfo.p_id,
									"p_liquidation": payrollInfo.liquidation,
									"p_year": payrollInfo.year,
									"p_mode_of_payment": payrollInfo.mode_of_payment,
									"p_period": payrollInfo.period,
									"p_date_receive": payrollInfo.date_receive,
									"p_amount": payrollInfo.amount,
								};
								var formData = frmdata(datas);
								axios.post(urls, formData).then(function (e) {
									mem.getMemPayment(mem.activeSP.SPID);
									swal.close();
									swal.fire('Info', e.data.message, 'success');
									console.log(e);
								})
							} else if (result.dismiss === Swal.DismissReason.cancel) {
								swal.fire('Cancelled', 'Action Cancelled', 'error')
							}
						})
					}
				})

			},

			getWaitlistInfo(val) {
				this.checkSemi()
			
				mem.wdata.dateAccomplish = 	mem.wdata.data.buf.date_accomplished;
				mem.wdata.work_name = mem.wdata.data.buf.worker_name;
				mem.wdata.w_id = mem.wdata.data.w_id;
			},

			checkSemi(mode_of_payment = "", period = 0) {
				// mode_of_payment => "QUARTER" || "SEMISTER"
				// period => 1-4
				let mop = mode_of_payment.toUpperCase();
				var ret = true;
				let age = mem.wdata.data.age;
				if (age == 60 || age == "60") {
					let birthmonth = mem.wdata.data.birthdate;
					var d = new Date(birthmonth);
					var n = d.getMonth() + 1;
					if (mop == "QUARTER") {
						switch (period) {
							case 1:
								if (n >= 3 && n <= 1) {
									ret = false;
								}
								break;
							case 2:
								if (n >= 4 && n <= 6) {
									ret = false;
								}
								break;
							case 3:
								if (n >= 7 && n <= 9) {
									ret = false;
								}
								break;
							case 4:
								if (n >= 10 && n <= 12) {
									ret = false;
								}
								break;
							default:
								break;
						}

					} else if (mop == "SEMESTER") {
						console.log(n)
						if (n >= 7 && period == 2) {
							ret = false;
						} else if (n <= 6 && period == 1) {
							ret = false;
						}
					}
				}else{
					if (mop == "QUARTER") {

					}
				}
				return ret;

			},
			select() {
				this.memPayments.selected = [];
				if (!this.memPayments.selectAll) {
					$.each(this.memPayments.data, function(index, value){
						if(value.liquidation != 1){
							mem.memPayments.selected.push(value);
						}
					})
				}
			},
			transferPaymentsNewLocation() {
				var spid = mem.memPayments.activeMP.spid;
				var textmsg = "Are sure you want to transfer checked payments to current address? ";
				textmsg += 'Make sure that the PRESENT ADDRESS of this beneficiary is updated.'
				swal.fire({
					title: 'Warning',
					text: textmsg,
					icon: 'warning',
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					confirmButtonText: 'Yes, change payment details!',
					cancelButtonText: 'No, cancel!',
					buttonsStyling: false
				}).then((result) => {
					if (result.value) {
						showloading();
						let data = {'selected': this.memPayments.selected, 'member': this.activeSP};
						let formData = methods.formData(data);
						var urls = window.App.baseUrl + 'transfer-payment-location';
						axios.post(urls, formData).then(function (e) {
							swal.close();
							swal.fire('Info', e.data.message, 'success');
							mem.memPayments.selected = [];
							mem.memPayments.selectAll = false;
						})
					} else if (result.dismiss === Swal.DismissReason.cancel) {
						swal.fire('Cancelled', 'Action Cancelled', 'error')
					}
				})
			},
			getWaitlistFilterLocation(type = 'prov_code', val = "") {
				if (type == 'mun_code') {
					mem.filterWaitlist.mun_code = '';
					mem.waitlistLocation.barangays = [];
					mem.waitlistLocation.municipalities = mem.waitlistLocation.mun_names[val];
				} else {
					mem.waitlistLocation.provinces = mem.waitlistLocation.prov_names;
				}
			},
			filterEligibleWaitlist() {
				showloading();
				mun_code = mem.filterWaitlist.mun_code;
				spid = mem.activeSP.SPID;

				mem.wdata = {
					data: {
						barangay: "",
						birthdate: "",
						age: 0,
						w_id: 0,
					},
					w_id: "",
					m_id: "",
					work_name: "",
					dateAccomplish: "",
					dateofreplacement: "",
					liquidation: []
				}

				var urls = window.App.baseUrl + "get-Eligible-Waitlist";
				var params = { mun_code: mun_code };
				axios.get(urls, { params: params }).then(function (e) {
					console.log(e.data);
					swal.close();
					if (e.data.success) {
						// $('#replaceMember').modal('show');
						mem.woptions = e.data.data;
					} else {
						swal.fire('Error', "There's No available eligible waitlist for the Municipality", 'error');
						// $('#replaceMember').modal('hide');
					}
				})
			},
			// deleteMemPayment(data){

			// },

			///// End History /////////////
		},
		mounted: function () {
			this.getAllLocation();
			this.getReplacementReason();
		},
	})
}
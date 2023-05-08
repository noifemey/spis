Vue.use(VueTables.ClientTable);
Vue.use(VueChartJs.Line);

let form = {
	search:{
		agency: 'all',
		last_name: '',
		first_name: '',	
		middle_name: '',		
	},
};

let app =  new Vue({
	  el: '#sp_crossmatching_page',
	  data:{
		url: window.App.baseUrl,
        datacollection: null,
		form:form,
		file: '',
		file2: '',
		content: '',
		raw_materials:{
			columns: [
				// 'last_name',
				// 'first_name',
				// 'middle_name',
				// 'ext_name',
				'search_fullname',
				'dup_fullname',
				'table_source',
				'spid',
				'status',
				'percentage',
			],
			data: {
				rm:[]
			},
			options: {
				headings: {
					// 'last_name' : "Last Name",
					// 'first_name' : "First Name",
					// 'middle_name' : "Middle Name",
					// 'ext_name' : "Extension Name",
					'search_fullname' : "Full Name",
					'dup_fullname' : "Duplicate Fullname",
					'table_source' : "Duplicate Category",
					'spid' : "Duplicate SPID / Reference Code",
					'status' : "Duplicate Status",
					'percentage': "Duplicate Percentage",
				},
				sortIcon: {
				  base : 'fa',
				  is: 'fa-sort',
				  up: 'fa-sort-asc',
				  down: 'fa-sort-desc'
				},
				sortable: ['last_name','first_name','middle_name'],
				filterable: []
			}
		},
	},
	created: function () {
	},
	methods: {	
		getAllData(){	
			showloading();
			var formData = methods.formData(this.form.search);
			axios.post(this.url+'search-name',formData)
				.then(function (response) {
					if(response.data.data != null){				
						app.raw_materials.data.rm = response.data.data;
						swal.close();
						swal.fire('Info',e.data.message,'success');
					}				
			})
			.catch(function (error) {

			});
		},
		handleFileUpload(){
			this.file = this.$refs.file.files[0];
		},
		handleFileUpload2(){
			this.file2 = this.$refs.file2.files[0];
		},
		submitFile(){
			showloading();
			let formData = new FormData();
			formData.append('file', this.file);
			axios.post( this.url+'import-sp', formData,{
					headers: { 'Content-Type': 'multipart/form-data' }
				}).then(function(e){
					app.raw_materials.data.rm = e.data.data;
					app.content = e.data.content;
					swal.close();
					swal.fire('Info',"Success",'success');
					$('#importsapModal').modal('hide');

					var urls = "";
					urls = window.App.baseUrl + app.content;
					//urls += "content=" + e.data.content;
					window.open(urls, '_blank');

					// var urls = "";
					// urls = window.App.baseUrl + "export-sp?";
					// urls += "content=" + e.data.content;
					// window.open(urls, '_blank');

					//app.searchWaitlist();
				})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		submitUCTFile(){
			showloading();
			let formData = new FormData();
			formData.append('file', this.file2);
			axios.post( this.url+'import-sp-uct', formData,{
					headers: { 'Content-Type': 'multipart/form-data' }
				}).then(function(e){
					app.raw_materials.data.rm = e.data.data;
					app.content = e.data.content;
					swal.close();
					swal.fire('Info',"Success",'success');
					$('#importuctModal').modal('hide');

					var urls = "";
					urls = window.App.baseUrl + app.content;
					//urls += "content=" + e.data.content;
					window.open(urls, '_blank');

					// var urls = "";
					// urls = window.App.baseUrl + "export-sp?";
					// urls += "content=" + e.data.content;
					// window.open(urls, '_blank');

					//app.searchWaitlist();
				})
			.catch(function(){
				console.log('FAILURE!!');
			});
		},
		exportDuplicates(){
			// var urls = "";
			// urls = window.App.baseUrl + "export-sp?";
			// urls += "content=" + app.content;
			// window.open(urls, '_blank');

			var urls = "";
			urls = window.App.baseUrl + app.content;
			window.open(urls, '_blank');

		},
	}
});
	
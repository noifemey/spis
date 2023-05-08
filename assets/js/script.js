function showloading() {
	swal.fire({
		text:'Processing Request. Please wait!',
		showCloseButton: false,
		showCancelButton: false,
		showConfirmButton: false,
		allowOutsideClick: false,
		imageUrl: window.App.baseUrl + 'assets/img/loader.gif'			
	});
}


function showAlert(message, type) {
	// type = ['', 'info', 'danger', 'success', 'warning', 'rose', 'primary'];
	$.notify({
		icon: "add_alert",
		message: message
	}, {
		type: type,
		timer: 2000,
		placement: {
			from: "top",
			align: "right"
		}
	});
}




function refreshpicker(timer = 1000) {
	setTimeout(function () {
		$('.selectpicker').selectpicker('refresh');
		$('.lods').removeClass('fa-spin');

	}, timer);
}

function frmdata(obj) {
	var formData = new FormData();
	var fd = new FormData();
	for (var key in obj) {
		formData.append(key, obj[key]);
	}
	return formData;
}

if ($('#navigationpanel').length) {
	var nav = new Vue({
		el: '#navigationpanel',
		data: {
			activenav: ""
		},
		methods: {
			checkactive: function (page = '') {
				var path = window.location.pathname;

				var path = path.split("/", 3).slice(-1)[0];

				return this.activenav == page ? "active" : '';
			}
		}
	});
}

function clearinput(forminput,me) {
	Object.keys(this.data.form).forEach(function (key, index) {
		self.data.form[key] = '';
	})
}

$('.datepicker').datetimepicker({
	format: 'YYYY-MM-DD',
	minDate: new Date(),
	icons: {
		time: "fa fa-clock-o",
		date: "fa fa-calendar",
		up: "fa fa-chevron-up",
		down: "fa fa-chevron-down",
		previous: 'fa fa-chevron-left',
		next: 'fa fa-chevron-right',
		today: 'fa fa-screenshot',
		clear: 'fa fa-trash',
		close: 'fa fa-remove',
		inline: true
	}
});

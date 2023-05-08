var app = new Vue({
	el: '#login',
	data: {
		password: "",
        username: ""

	},
	methods: {
		checklogin() {
            var dt = { password: this.password, username: this.username };
            var dt = methods.formData(dt);
            var urls = window.App.baseUrl + "checklogin";
            axios.post(urls, dt)
                .then(function (e) {
                    console.log(e);
                    if (e.data.success == "true") {
                        if (e.data.active == "false") {
                            window.location.replace(window.App.baseUrl + "activate");
                        } else {
                            window.location.replace(window.App.baseUrl + "report-waitlist");
                        }
                    } else {
                        console.log("wrong input");
                        swal.fire("Login Failed", e.data.message, "error");
                    }

                })
        }
	},	
	mounted: function () {


	},
})
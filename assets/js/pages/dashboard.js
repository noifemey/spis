"use strict";
/* eslint-disable no-magic-numbers */
// Disable the on-canvas tooltip
Chart.defaults.global.pointHitDetectionRadius = 1;
Chart.defaults.global.tooltips.enabled = false;
Chart.defaults.global.tooltips.mode = 'index';
Chart.defaults.global.tooltips.position = 'nearest';
Chart.defaults.global.tooltips.custom = CustomTooltips;
Chart.defaults.global.tooltips.intersect = true;

Vue.use(VueTables.ClientTable);

//Targets
let targetChart = {
  type: 'bar',
  data: {
    total : 0,
    labels: [],
    datasets: [{
      label: 'Targets',
      backgroundColor: 'rgba(255,255,255,.2)',
      borderColor: 'rgba(255,255,255,.55)',
      data: []
    }]
  },
  options: {
    maintainAspectRatio: false,
    legend: {
      display: false
    },
    scales: {
      xAxes: [{
        display: false,
        barPercentage: 0.6
      }],
      yAxes: [{
        display: false
      }]
    }
  }
}; // eslint-disable-next-line no-unused-vars

//Active Beneficiaries
let activeChart = {
  type: 'bar',
  data: {
    total : 0,
    labels: [],
    datasets: [{
      label: 'Targets',
      backgroundColor: 'rgba(255,255,255,.2)',
      borderColor: 'rgba(255,255,255,.55)',
      data: []
    }]
  },
  options: {
    maintainAspectRatio: false,
    legend: {
      display: false
    },
    scales: {
      xAxes: [{
        display: false,
        barPercentage: 0.6
      }],
      yAxes: [{
        display: false
      }]
    }
  }
}; // eslint-disable-next-line no-unused-vars

//For Replacement
let forrepChart = {
  type: 'bar',
  data: {
    total : 0,
    labels: [],
    datasets: [{
      label: 'Targets',
      backgroundColor: 'rgba(255,255,255,.2)',
      borderColor: 'rgba(255,255,255,.55)',
      data: []
    }]
  },
  options: {
    maintainAspectRatio: false,
    legend: {
      display: false
    },
    scales: {
      xAxes: [{
        display: false,
        barPercentage: 0.6
      }],
      yAxes: [{
        display: false
      }]
    }
  }
}; // eslint-disable-next-line no-unused-vars

//Waitlist
let waitlistChart = {
  type: 'bar',
  data: {
    total : 0,
    labels: [],
    datasets: [{
      label: 'Waitlist',
      backgroundColor: 'rgba(255,255,255,.2)',
      borderColor: 'rgba(255,255,255,.55)',
      data: []
    }]
  },
  options: {
    maintainAspectRatio: false,
    legend: {
      display: false
    },
    scales: {
      xAxes: [{
        display: false,
        barPercentage: 0.6
      }],
      yAxes: [{
        display: false
      }]
    }
  }
}; // eslint-disable-next-line no-unused-vars

//Served
var servedChart = {
  type: 'bar',
  data: {
    labels: [],
    datasets: [{
      label: 'Total Target',
      yAxisID: 'B',
      backgroundColor: 'rgba(12, 4, 175, 0.94)',
      borderColor: getStyle('--info'),
      pointHoverBackgroundColor: '#fff',
      borderWidth: 2,
      data: []
    }, {
      label: 'Total Paid',
      yAxisID: 'B',
      backgroundColor: 'rgba(9, 175, 4, 0.94)',
      borderColor: getStyle('--success'),
      pointHoverBackgroundColor: '#fff',
      borderWidth: 2,
      data: []
    }, {
      type: 'line',
      yAxisID: 'C',
      label: 'Accomplishment',
      backgroundColor: 'rgba(175, 4, 4, 0.94)',
      borderColor: getStyle('--danger'),
      pointHoverBackgroundColor: '#fff',
      borderWidth: 2,
      data: []
    }]
  },
  options: {
    maintainAspectRatio: false,
    legend: {
      display: true
    },
    scales: {
      xAxes: [{
        gridLines: {
          drawOnChartArea: false
        }    
      }],
      yAxes: [{
        id: 'B',
        display: true,
        ticks: {
          beginAtZero: true,
          maxTicksLimit: 5,
          stepSize: Math.ceil(20000 / 5),
          max: 23000
        },
        scaleLabel: {
            display: true,
            labelString: "TARGET | PAID"
        }
      },{
        id: 'C',
        display: true,
        type: 'linear',
        position: 'right',
        ticks: {
          beginAtZero: true,
          maxTicksLimit: 5,
          stepSize: Math.ceil(100 / 5),
          max: 100,
          callback: function(value) {
            return value + "%"
          }
        },
        scaleLabel: {
            display: true,
            labelString: "Accomplishment"
        }
      }]
    },
    elements: {
      point: {
        radius: 0,
        hitRadius: 10,
        hoverRadius: 4,
        hoverBorderWidth: 3
      }
    }
  }
};

let app =  new Vue({
  el: '#dashboard_page',
  data:{
    search: {
        year: "",
        period: "",
    },
    targetChart: targetChart,
    activeChart: activeChart,
    forrepChart: forrepChart,
    waitlistChart: waitlistChart,
    servedChart : servedChart,

    served_table:{
      columns: [
        'province',
        'total_target',
        'total_paid',
        'total_amount',
        'accomplishment'
      ],
      data: {
        rm:[],
        region_served : 0,
        region_unpaid  : 0,
        region_targets : 0,
        total_amount  : 0,
        accomplishment : 0,
        unpaid_progress : 0,
      },
      options: {
        headings: {
          'province' : "Province",
          'total_target' : "Total Target",
          'total_paid' : "Total Paid",
          'total_amount' : "Total Amount",
          'accomplishment' : "Accomplishment",
        },
        sortIcon: {
          base : 'fa',
          is: 'fa-sort',
          up: 'fa-sort-asc',
          down: 'fa-sort-desc'
        },
        sortable: ['province','total_paid','total_target'],
        filterable: []
      }
    },
},
mounted: function () {
  this.createTargetChart();
  this.getBenesData();
},
methods: {	
  getNumberFormat(num){
    return methods.getNumberFormat(num);
  },
  getPeriod: function () {
    var period = "1st Semester";

    if(this.search.period == 1){
      period = "1st Quarter";
    }else if(this.search.period == 2){
      period = "2nd Quarter";
    }else if(this.search.period == 3){
      period = "3rd Quarter";
    }else if(this.search.period == 4){
      period = "4th Quarter";
    }else if(this.search.period == 5){
      period = "1st Semester";
    }else if(this.search.period == 6){
      period = "2nd Semester";
    }

    return period + " " + this.search.year;

  },
  getBenesData: function () {
    //console.log(app.targetChart);
    var urls = window.App.baseUrl + "dash-get-data";
    axios.post(urls)
        .then(function (e) {
            app.search.year = e.data.served.year;
            app.search.period = e.data.served.period;
            
            app.served_table.data.rm = e.data.served.table_data;

            app.served_table.data.region_served = e.data.served.region_served;
            app.served_table.data.region_unpaid = e.data.served.region_unpaid;
            app.served_table.data.region_targets = e.data.served.region_targets;
            app.served_table.data.total_amount = e.data.served.total_amount;
            app.served_table.data.accomplishment = e.data.served.accomplishment;
            app.served_table.data.unpaid_progress = e.data.served.unpaid_progress;

            //document.getElementById("p_accomplishment").width = e.data.served.accomplishment + "%";

            //servedChart.data.total = e.data.targets.total;
            servedChart.data.labels = e.data.served.table_labels;
            servedChart.data.datasets[0].data = e.data.served.table_target;
            servedChart.data.datasets[1].data = e.data.served.table_paid;
            servedChart.data.datasets[2].data = e.data.served.table_accom;
            app.servedChart.update();

            targetChart.data.total = e.data.targets.total;
            targetChart.data.labels = e.data.targets.data_keys;
            targetChart.data.datasets[0].data = e.data.targets.data_values;
            app.targetChart.update();

            activeChart.data.total = e.data.active.total;
            activeChart.data.labels = e.data.active.data_keys;
            activeChart.data.datasets[0].data = e.data.active.data_values;
            app.activeChart.update();

            forrepChart.data.total = e.data.forrep.total;
            forrepChart.data.labels = e.data.forrep.data_keys;
            forrepChart.data.datasets[0].data = e.data.forrep.data_values;
            app.forrepChart.update();

            waitlistChart.data.total = e.data.waitlist.total;
            waitlistChart.data.labels = e.data.waitlist.data_keys;
            waitlistChart.data.datasets[0].data = e.data.waitlist.data_values;
            app.waitlistChart.update();



        })
        .catch(function (error) {
            console.log(error)
        });
  },
  searchDashData: function(){    
    showloading();
    var urls = window.App.baseUrl + "search-served-data";
    var data = frmdata(this.search);
    axios.post(urls,data)
      .then(function (e) {
          swal.close();
          app.served_table.data.rm = e.data.served.table_data;

          app.served_table.data.region_served = e.data.served.region_served;
          app.served_table.data.region_unpaid = e.data.served.region_unpaid;
          app.served_table.data.region_targets = e.data.served.region_targets;
          app.served_table.data.total_amount = e.data.served.total_amount;
          app.served_table.data.accomplishment = e.data.served.accomplishment;
          app.served_table.data.unpaid_progress = e.data.served.unpaid_progress;

          //document.getElementById("p_accomplishment").width = e.data.served.accomplishment + "%";

          //servedChart.data.total = e.data.targets.total;
          servedChart.data.labels = e.data.served.table_labels;
          servedChart.data.datasets[0].data = e.data.served.table_target;
          servedChart.data.datasets[1].data = e.data.served.table_paid;
          servedChart.data.datasets[2].data = e.data.served.table_accom;
          app.servedChart.update();
      })
      .catch(function (error) {
          console.log(error)
      });

  },
  createTargetChart() {
    this.servedChart = new Chart($('#main-chart'), {
      type: servedChart.type,
      data: servedChart.data,
      options: servedChart.options,
    });

    this.targetChart = new Chart($('#card-target'), {
      type: targetChart.type,
      data: targetChart.data,
      options: targetChart.options,
    });

    this.activeChart = new Chart($('#card-active'), {
      type: activeChart.type,
      data: activeChart.data,
      options: activeChart.options,
    });

    this.forrepChart = new Chart($('#card-forrep'), {
      type: forrepChart.type,
      data: forrepChart.data,
      options: forrepChart.options,
    });

    this.waitlistChart = new Chart($('#card-waitlist'), {
      type: waitlistChart.type,
      data: waitlistChart.data,
      options: waitlistChart.options,
    });
  }
}
});


// var myChart = new Chart($('#card-unpaid'), {
//   type: 'doughnut',
//   data: {
//     labels: ['OK', 'WARNING', 'CRITICAL', 'UNKNOWN'],
//     datasets: [{
//       label: '# of Unpaid Reason',
//       data: [10, 10, 10, 10],
//       backgroundColor: [
//         'rgba(255, 99, 132, 0.5)',
//         'rgba(54, 162, 235, 0.2)',
//         'rgba(255, 206, 86, 0.2)',
//         'rgba(75, 192, 192, 0.2)'
//       ],
//       borderColor: [
//         'rgba(255,99,132,1)',
//         'rgba(54, 162, 235, 1)',
//         'rgba(255, 206, 86, 1)',
//         'rgba(75, 192, 192, 1)'
//       ],
//       borderWidth: 1
//     }]
//   },
//   options: {
//    	//cutoutPercentage: 40,
//     responsive: false,

//   }
// });



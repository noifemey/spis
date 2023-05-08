var methods = (function() { 

  var toastr = function(type,title,message) {

    switch(type){
      case "error":
      $.toast({
        heading: title,
        text: message,
        position: 'top-right',
        loaderBg:'#ff6849',
        icon: 'error',
        hideAfter: 3500

      });
      break;

      case "warning":
      $.toast({
        heading: title,
        text: message,
        position: 'top-right',
        loaderBg:'#ff6849',
        icon: 'warning',
        hideAfter: 3500, 
        stack: 6
      });
      break;

      case "success":
      $.toast({
        heading: title,
        text: message,
        position: 'top-right',
        loaderBg:'#ff6849',
        icon: 'success',
        hideAfter: 3500, 
        stack: 6
      });
      break;

      default:
      $.toast({
        heading: title,
        text: message,
        position: 'top-right',
        loaderBg:'#ff6849',
        icon: 'info',
        hideAfter: 3000, 
        stack: 6
      });

    }
  }

  var disableSaveBtn = function(){        
    $(".save").attr("disabled","true");
    $(".save").html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> Loading...');    
  }

  var enableSaveBtn = function(){
    setInterval(function(){ 
      $(".save").removeAttr("disabled");
      $(".save").html('Save');
    }, 300);
  }

  var destroyModalData = function(){
    methods.clearError();
    methods.enableSaveBtn();
    $(this).find("form").trigger("reset");

    // $(this).find('.selectpicker').selectpicker('refresh');
  }

  var formData = function (obj) {
    var formData = new FormData();
    for ( var key in obj ) {     
      let val = (typeof(obj[key]) === 'object')?JSON.stringify(obj[key]):obj[key];
      formData.append(key, val);
    }    
    return formData;
  }

  var errorFormValidation = function(message) {              

    if(typeof message == 'object' && Object.keys(message).length == 1 && message.file != 'undefined'){
      $("#file-invalid-feedback").html(message.file);      
    } else {
      $.each(message, function(key,val){
        $("label[for="+key+"]").next().addClass("is-invalid");
        $("button[data-id="+key+"]").addClass("form-control");
        $("#"+key+"").addClass('is-invalid');      
        if($("#"+key+"").next().is("button")){
          $("label[for="+key+"]").next().next().html(val);
        } else {
          $("input[name="+key+"]").next().html(val);
          $("select[name="+key+"]").next().html(val);
        }
      }); 
    }

  }

  var clearError = function(){
    $('.invalid-feedback').html('');
    $('.form-control').removeClass('is-invalid');    
    $('.milestone').removeClass('is-invalid');    
    $(".milestone .invalid-milestone-feedback").html('');
  }

  var getFullDateFormat = function(dateObj){

    const monthNames = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
    ];

    let date = dateObj.split(" ");
    let path = date[0].split("-");        
    
    return monthNames[path[1]-1] + " " + path[2] + ", " + path[0];

  }

  var getTimeFormat = function (timeObj) {
      // Check correct time format and split into components
      
      let time = timeObj.split(" ")[1];
      
      time = time.toString ().match (/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

      if (time.length > 1) { // If time format correct
        time = time.slice (1);  // Remove full string match value
        time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
        time[0] = +time[0] % 12 || 12; // Adjust hours
      }
      return time.join (''); // return adjusted time or original string
    
  }

  var getNumberFormat = function(x){ 
      if(x !== 'undefined'){        
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");      
      } else {
        return '';
      }     
    }

  return {
    toastr: function(type,title,message) {
      toastr(type,title,message);
    },
    disableSaveBtn: function() {
      disableSaveBtn();
    },
    enableSaveBtn: function() {
      enableSaveBtn();
    },
    destroyModalData: function() {
      destroyModalData();
    },
    formData: function (obj){
      return formData(obj);
    },
    errorFormValidation: function(message) {
      errorFormValidation(message);
    },
    clearError: function(){
      clearError();
    },
    getFullDateFormat: function(date) {
      return getFullDateFormat(date);
    },
    getTimeFormat: function(time) {
      return getTimeFormat(time);
    },
    getNumberFormat: function(x){
      return getNumberFormat(x);
    }
  }

})(); 
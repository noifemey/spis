<style>
    .form-control{
        background-color: #ffffff !important;
        font-size: 18px!important; 
        height:40px !important; 
    }
    .btn{
        font-size: 20px!important; 
        height:40px !important; 
    }
</style>

<div id="login-welcome-page">
<div class="container">
  <div class="d-flex justify-content-center h-100">
    <div class="card">
      <div class="card-header" >
        <img src="<?=base_url();?>assets/img/dswdlogo.png" class="img-responsive" style = "width: 90%; margin: 15px"/>
        <h6>Social Pension Information System (SPIS)<h6>
        
      </div>
      <div id="login" class="card-body">
        <h2>Sign In</h2>
        <hr>
        <form @submit.prevent="checklogin">
          <div class="input-group form-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-user"></i></span>
            </div>
            <input type="text" v-model="username" class="form-control" placeholder="username">
            
          </div>
          <div class="input-group form-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-key"></i></span>
            </div>
            <input type="password" v-model="password" class="form-control" placeholder="password">
          </div>
          <div class="form-group">
            <input type="submit" value="Login" class="btn btn-primary btn-block">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>

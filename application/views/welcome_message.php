<!DOCTYPE html>
<html>
<head>
  <title>Social Pension Information System</title>

    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta charset="utf-8">
    <meta name="description" content="Social Pension Information System"/>
    <meta name="author" content="">

    <meta name="keywords" content="Social Pension Information System" />

      <!-- <link rel="icon" href="assets/img/logo_icon.png" type="image/x-icon" /> -->
      <!-- <link rel="shortcut icon" type="image/png" href="assets/img/logo_icon.png" /> -->

      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title><?php echo isset($title) ? $title : 'Social Pension Information System' ; ?></title>

      <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>assets/css/style_login.css">
      <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>assets/css/bootstrap.min.css">
      <link href="<?=base_url();?>assets/vendors/sweetalert/sweetalert.css" rel="stylesheet">
      <link href="<?=base_url();?>assets/css/custom.css" rel="stylesheet">
      <link href="<?=base_url();?>assets/vendors/sweetalert/sweetalert.css" rel="stylesheet">
      <link href="<?=base_url();?>assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
      <script type="text/javascript">
        window.App = {
            "baseUrl": "<?= base_url() ?>",            
            "removeDOM": "",
        };
    </script>
</head>
<body id="login-welcome-page">
<div class="container">
  <div class="d-flex justify-content-center h-100">
    <div class="card">
      <div class="card-header">
        <h3>Sign In</h3>
      </div>
      <div id="login" class="card-body">
        <form @submit.prevent="checkUser">
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
            <input type="submit" value="Login" class="btn btn-primary float-right">
          </div>
        </form>
      </div>
<!--       <div class="card-footer">
        <div class="d-flex justify-content-center links">
          Don't have an account?<a href="#">Register</a>
        </div>
      </div> -->
    </div>
  </div>
</div>
<script src="<?=base_url();?>assets/vendors/jquery/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/vendors/popper.js/js/popper.min.js"></script>
<script src="<?=base_url();?>assets/vendors/bootstrap/js/bootstrap.min.js"></script>
<script src="<?=base_url();?>assets/vendors/@coreui/coreui/js/coreui.min.js"></script>
<script src="<?=base_url();?>assets/vendors/@coreui/coreui/js/coreui.min.js"></script>
<!-- vue -->
<script src="<?=base_url();?>assets/js/vue.js"></script>
<script src="<?=base_url();?>assets/js/axios.min.js"></script>
<script src="<?=base_url();?>assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="<?=base_url();?>assets/js/global.js"></script>
<script src="<?=base_url();?>assets/js/pages/login.js"></script>

</body>
</html>
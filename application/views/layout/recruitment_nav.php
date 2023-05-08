<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title><?= $title ?></title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?= base_url("assets/template/plugins/fontawesome-free/css/all.min.css") ?>">
  <!-- IonIcons -->
  <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url("assets/template/dist/css/adminlte.min.css") ?>">
  <!-- Google Font: Source Sans Pro -->
  <link href="<?= base_url('assets/css/fonts.css') ?>" rel="stylesheet">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url("assets/template/plugins/select2\css\select2.min.css") ?>">
  <link rel="stylesheet" href="<?= base_url("assets/template/plugins/select2-bootstrap4-theme\select2-bootstrap4.min.css") ?>">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?= base_url("assets/template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css") ?>">
  <!-- Swal toastr -->
  <link rel="stylesheet" href="<?= base_url("assets/template/plugins/sweetalert2/sweetalert2.min.css") ?>">
  <link rel="stylesheet" href="<?= base_url("assets/template/plugins/toastr/toastr.min.css") ?>">

  <script src="<?= base_url('assets/js/plugins/vue.js') ?>"></script>
  <script type="text/javascript">
    window.App = {
      "baseUrl": "<?= base_url() ?>",
      "removeDOM": "",
    };
  </script>
</head>

<body class="hold-transition layout-top-nav">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
      <div class="container">
        <a class="navbar-brand">
          <img src="<?= base_url() ?>assets/img/dswdlogo.png" class="brand-image elevation-3">
          <span class="brand-text font-weight-light"><?= $title ?></span>
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
          <!-- Left navbar links -->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a href="<?= base_url() ?>recruitment" class="nav-link">Home</a>
            </li>
            <!-- <li class="nav-item">
            <a href="#" class="nav-link">Contact</a>
          </li> -->
          </ul>
        </div>

        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
          <a href="<?= base_url() ?>recruitment" class="nav-link">LOGIN</a>
        </ul>
      </div>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container">
          <div class="row mb-2">
            <div class="col-sm-12">
              <h5 class="m-0 text-dark"> Department of Social Welfare and Development <br /> <small> Cordillera Administrative Region </small></h5>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->
      <?php $this->load->view($vfile) ?>
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <!-- <aside class="control-sidebar control-sidebar-dark">
    Control sidebar content goes here
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside> -->
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
      <!-- To the right -->
      <!-- <div class="float-right d-none d-sm-inline">
      Anything you want
    </div> -->
      <!-- Default to the left -->
      <strong>Copyright &copy; <?= date("Y") ?> <a href="car.dswd.gov.ph">DSWD-CAR</a>.</strong> All rights reserved.
    </footer>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="<?= base_url("assets/template/plugins/jquery/jquery.min.js") ?>"></script>
  <!-- Bootstrap -->
  <script src="<?= base_url("assets/template/plugins/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
  <script src="<?= base_url("assets/template/plugins/sweetalert2/sweetalert2.min.js") ?>"></script>
  <script src="<?= base_url("assets/template/plugins/select2/js/select2.full.min.js") ?>"></script>
  <!-- Select2 -->
  <script src="<?= base_url("assets/template/plugins/select2/js/select2.min.js") ?>"></script>
  <!-- AdminLTE -->
  <script src="<?= base_url("assets/template/dist/js/adminlte.js") ?>"></script>
  <script src="<?= base_url('assets/js/plugins/axios.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/plugins/vue-tables-2.min.js') ?>"></script>
  <script src="<?= base_url('assets/template/plugins/toastr/toastr.min.js') ?>"></script>

  <script src="<?= base_url('assets/js/script.js') ?>"></script>

  <?php if (!empty($js)) : ?>
    <?php foreach ($js as $j) : ?>
      <script src="<?= base_url('assets/js/' . $j . '?ver=') . filemtime(FCPATH) ?>"></script>
    <?php endforeach ?>
  <?php endif ?>
  <script>
    $(function() {
      //Initialize Select2 Elements
      $('.select-picker').select2({
        theme: 'bootstrap4'
      })

    });

    var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 4500
    });

    //Initialize Select2 Elements
    // $('.select2bs4').select2({
    //   theme: 'bootstrap4'
    // })
    //Initialize Select2 Elements
    // $('.select2').select2()
  </script>

</body>

</html>
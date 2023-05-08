<!DOCTYPE html>

<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <meta charset="utf-8">
  <meta name="description" content="HRMIS" />
  <meta name="author" content="DSWD-CAR-RICTMS">

  <meta name="keywords" content="HRMIS" />
  <!-- Icons-->
  <!-- <link rel="icon" type="image/ico" href="./img/favicon.ico" sizes="any" /> -->
  <!-- <link rel="shortcut icon" type="image/png" href="assets/img/logo_icon.png" /> -->
  <link href="<?= base_url(); ?>assets/vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
  <link href="<?= base_url(); ?>assets/vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
  <link href="<?= base_url(); ?>assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="<?= base_url(); ?>assets/vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
  <!-- Main styles for this application-->
  <link href="<?= base_url(); ?>assets/css/style.css" rel="stylesheet">
  <link href="<?= base_url(); ?>assets/vendors/pace-progress/css/pace.min.css" rel="stylesheet">
  <script src="<?= base_url(); ?>assets/js/vue.js"></script>
  <link href="<?= base_url(); ?>assets/css/custom.css" rel="stylesheet">

  <!-- <link rel="icon" href="assets/img/logo_icon.png" type="image/x-icon" /> -->
  <!-- <link rel="shortcut icon" type="image/png" href="assets/img/logo_icon.png" /> -->
  <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
  <title><?php echo isset($title) ? $title : 'HRMIS'; ?></title>
  <script type="text/javascript">
    window.App = {
      "baseUrl": "<?= base_url() ?>",
      "removeDOM": "",
    };
  </script>

    <style>
      .maincontent {
          width: 100%;
          margin-top: 2%;
      }
    </style>
</head>

<body class="app header-fixed">
  <?= $template['partials']['header']; ?>
  <div class="app-body">
    <main class="main ">
      <div id="<?= !empty($vueid) ? $vueid : '' ?>" class="maincontent">
        <?= !empty($template['partials']['breadcrumbs']) ? $template['partials']['breadcrumbs'] : "" ?>
        <div class="container-fluid">
          <?= $template['body']; ?>
        </div>
      </div>
    </main>
  </div>

  <?= $template['partials']['footer']; ?>

  <!-- CoreUI and necessary plugins-->
  <link href="<?= base_url('assets/css/bootstrap-select.min.css') ?>" rel="stylesheet" />
  <script src="<?= base_url(); ?>assets/vendors/jquery/js/jquery.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/jquery.select-bootstrap.js"></script>
  <script src="<?= base_url(); ?>assets/vendors/popper.js/js/popper.min.js"></script>
  <script src="<?= base_url(); ?>assets/vendors/bootstrap/js/bootstrap.min.js"></script>
  <script src="<?= base_url(); ?>assets/vendors/pace-progress/js/pace.min.js"></script>
  <script src="<?= base_url(); ?>assets/vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
  <script src="<?= base_url(); ?>assets/vendors/@coreui/coreui/js/coreui.min.js"></script>
  <!-- Plugins and scripts required by this view-->
  <!-- <script src="<?= base_url(); ?>assets/vendors/chart.js/js/Chart.min.js"></script>
  <script src="<?= base_url(); ?>assets/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script> -->
  <!-- <script src="<?= base_url(); ?>assets/js/main.js"></script> -->


  <!-- vue -->
  <script src="<?= base_url(); ?>assets/js/vue-tables-2.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/axios.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/moment.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/bootstrap-datetimepicker.js"></script>
  <script src="<?= base_url(); ?>assets/js/script.js"></script>
  <?php echo $template['metadata']; ?>


</body>

</html>
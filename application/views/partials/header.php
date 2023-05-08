<?php 
  $loggedin = TRUE;
  $_CI = &get_instance();
  if (empty($_CI->session->userdata('loggedin'))) {
    $body_class = "app header-fixed";
    $loggedin = FALSE;
  }
  ?>

<header class="app-header navbar">
  <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href=<?= base_url("dashboard") ?>>
    <!-- <img class="navbar-brand-full" src="<?=base_url()?>assets/img/brand/procurement-logo.png" width="160" alt="Procurement IS Logo"> -->
    <!-- <img class="navbar-brand-minimized" src="<?=base_url()?>assets/img/brand/procurement-logo-min.png" width="45" height="45" alt="Procurement IS Logo"> -->
    <b>SPIS</b>
  </a>
  <?php if ($loggedin == TRUE) { ?>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
      <span class="navbar-toggler-icon"></span>
    </button>
  <?php  } ?>
  
  <ul class="nav navbar-nav d-md-down-none">
    <li class="nav-item px-3">
      <a class="nav-link" href=<?= base_url("dashboard") ?>>
        <i class="fa fa-home" aria-hidden="true"></i>Dashboard</a>
    </li>
    
    <li class="nav-item nav-dropdown px-3">
      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="nav-icon icon-book-open"></i> SP Beneficiaries
      </a>
      <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href=<?= base_url("report-repMonitoring") ?>><i class="fa fa-file-text-o"></i> Monitoring </a>
        <a class="dropdown-item" href=<?= base_url("report-target") ?>><i class="fa fa-file-text-o"></i> Targets </a>
        <a class="dropdown-item" href=<?= base_url("report-active") ?>><i class="fa fa-file-text-o"></i> Active Breakdown </a>
        <a class="dropdown-item" href=<?= base_url("report-waitlist") ?>><i class="fa fa-file-text-o"></i> Waitlist Breakdown </a>
      </div>
    </li>

    <li class="nav-item nav-dropdown px-3">
      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fa fa-bar-chart " aria-hidden="true"></i> Served Beneficiaries
      </a>
      <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href=<?= base_url("report-served") ?>><i class="fa fa-bar-chart " aria-hidden="true"></i> Served Beneficiaries </a>
        <a class="dropdown-item" href=<?= base_url("report-unclaimed") ?>><i class="fa fa-bar-chart " aria-hidden="true"></i> Unpaid Beneficiaries </a>
        <a class="dropdown-item" href=<?= base_url("report-inactive") ?>><i class="fa fa-bar-chart " aria-hidden="true"></i> Inactive Beneficiaries </a>
      </div>
    </li>

    <li class="nav-item px-3">
      <a class="nav-link" href=<?= base_url("SP_Crossmatching") ?>>
        <i class="fa fa-users" aria-hidden="true"></i> SP Crossmatching</a>
    </li>
    
  </ul>
  
  <ul class="nav navbar-nav ml-auto">
  <?php if ($loggedin == FALSE) { ?>
    <li class="nav-item px-3">
      <a class="nav-link" href="<?= base_url("login") ?>"><i class="fa fa-sign-in"></i> Login </a>
    </li>
    <li class="nav-item px-3">
      <a class="nav-link" href="<?= base_url("register") ?>"><i class="fa fa-registered"></i> Register </a>
    </li>
  <?php  }else{ ?>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
        <img class="img-avatar" src="<?=base_url()?>assets/img/avatars/default.png">
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-header text-center">
          <strong>Account</strong>
        </div>
      <!--   <a class="dropdown-item" href="#">
          <i class="fa fa-envelope-o"></i> Messages
          <span class="badge badge-success">42</span>
        </a>
        <a class="dropdown-item" href="#">
          <i class="fa fa-comments"></i> Comments
          <span class="badge badge-warning">42</span>
        </a>
        <div class="dropdown-header text-center">
          <strong>Settings</strong>
        </div> -->
        <a class="dropdown-item" href="<?=base_url()?>profile">
          <i class="fa fa-user"></i> Profile</a>
        <!-- <a class="dropdown-item" href="#">
          <i class="fa fa-wrench"></i> Settings</a> -->
        <!-- <div class="dropdown-divider"></div> -->
        <a class="dropdown-item" href="<?=base_url()?>Login/logout">
          <i class="fa fa-lock"></i>Logout</a>
      </div>
    </li>
  <?php } ?>
  </ul>
  <!-- <button class="navbar-toggler aside-menu-toggler d-md-down-none" type="button" data-toggle="aside-menu-lg-show">
    <span class="navbar-toggler-icon"></span>
  </button>   -->
</header>
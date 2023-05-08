<nav class="sidebar-nav">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url("dashboard") ?>">
        <i class="nav-icon cui-dashboard"></i>
        Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url("member") ?>">
        <i class="nav-icon cui-dashboard"></i>
        Masterlist
      </a>
    </li>
    <?php if(getUserRole() <= 2):?>
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url("replacement") ?>">
        <i class="nav-icon cui-dashboard"></i>
        Replacement
      </a>
    </li>
    <?php endif;?>
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url("waitlist") ?>">
        <i class="nav-icon cui-dashboard"></i>
        Waitlist
      </a>
    </li>

    <?php if(getUserRole() <= 2):?>
    <!-- ___________PAYROLL___________ -->
    <li class="nav-title">Payroll</li>
      <li class="nav-item">
        <a class="nav-link" href=<?= base_url("payroll") ?>>
          <i class="nav-icon icon-drop"></i>Generate Payroll</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href=<?= base_url("liquidation") ?>>
          <i class="nav-icon icon-pencil"></i> Liquidation</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href=<?= base_url("liquidation-summary") ?>>
          <i class="nav-icon icon-pencil"></i> Generate Liquidation Summary</a>
      </li>
      <!-- ___________END PAYROLL WAITLIST___________ -->
    <?php endif;?>

      <!-- ___________EXTRAS___________ -->
      <li class="nav-title">Reports</li>

        <li class="nav-item nav-dropdown">
          <a class="nav-link nav-dropdown-toggle" href="#">
            <i class="nav-icon icon-puzzle"></i>Reports</a>
              <ul class="nav-dropdown-items">
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("report-served") ?>>
                    <i class="nav-icon icon-puzzle"></i>Served Beneficiaries</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("report-unclaimed") ?>>
                    <i class="nav-icon icon-puzzle"></i>Unpaid Beneficiaries</a>
                </li>
              </ul>
        </li>

        <li class="nav-title">Extras</li>
        
        <?php if(getUserRole() <= 2):?>
        <li class="nav-item nav-dropdown">
          <a class="nav-link nav-dropdown-toggle" href="#">
            <i class="nav-icon icon-puzzle"></i>LBP Enrollment</a>
              <ul class="nav-dropdown-items">
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("lbp-enrollment") ?>>
                    <i class="nav-icon icon-puzzle"></i>Batch Application</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("lbp-enrollment") ?>>
                    <i class="nav-icon icon-puzzle"></i>Generate LBP Enrollment Form</a>
                </li>
              </ul>
        </li>

        <li class="nav-item nav-dropdown">
          <a class="nav-link nav-dropdown-toggle" href="#">
            <i class="nav-icon icon-puzzle"></i>Libraries</a>
              <ul class="nav-dropdown-items">
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("libraries/target-pensioners") ?>>
                    <i class="nav-icon icon-puzzle"></i>Target Pensioners</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("libraries/signatories") ?>>
                    <i class="nav-icon icon-puzzle"></i>Signatories</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("libraries/reasons") ?>>
                    <i class="nav-icon icon-puzzle"></i>Inactive Reasons</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("libraries/marital-status") ?>>
                    <i class="nav-icon icon-puzzle"></i>Marital Status</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("libraries/house-type") ?>>
                    <i class="nav-icon icon-puzzle"></i>House Type</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("libraries/living-arrangement") ?>>
                    <i class="nav-icon icon-puzzle"></i> Living Arrangement</a>
                </li>
              </ul>
        </li>
        <?php endif;?>
      
      <?php if(getUserRole() == 1) { ?>
        <li class="nav-item nav-dropdown">
          <a class="nav-link nav-dropdown-toggle" href="#">
            <i class="nav-icon icon-puzzle"></i>Users</a>
              <ul class="nav-dropdown-items">
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("user-list") ?>>
                    <i class="nav-icon icon-people"></i>Users List</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href=<?= base_url("user-logs") ?>>
                    <i class="nav-icon icon-book-open"></i>Users Logs</a>
                </li>
              </ul>
        </li>

        <!-- <li class="nav-item">
          <a class="nav-link" href=<?= base_url("SwadLogs") ?>>
            <i class="nav-icon icon-pencil"></i>SWAD Edit History</a>
        </li> -->
        
      <?php } ?>
        
        <li class="nav-item">
          <a class="nav-link" href=<?= base_url("RequestForm") ?>>
            <i class="nav-icon icon-pencil"></i>Issues and Concerns</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href=<?= base_url("myLogs") ?>>
            <i class="nav-icon icon-pencil"></i>My Activity Logs</a>
        </li>

        <!-- ___________END EXTRAS___________ -->

  </ul>
</nav>
<button class="sidebar-minimizer brand-minimizer" type="button"></button>
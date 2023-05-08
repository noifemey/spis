<nav class="mt-2" id = "navigationpanel">
  <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact" data-widget="treeview" role="menu" data-accordion="false">

    <li class="nav-item has-treeview">
      <a href="#" class="nav-link " >
        <i class="nav-icon fas fa-th"></i>
        <p>
          Libraries
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="<?= base_url('libraries/office') ?>" class="nav-link "  v-bind:class="checkactive('office')">
            <i class="far fa-circle nav-icon"></i>
            <p>Office</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('libraries/division') ?>" class="nav-link" v-bind:class="checkactive('division')">
            <i class="far fa-circle nav-icon"></i>
            <p>Division</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= base_url('libraries/section') ?>" class="nav-link" v-bind:class="checkactive('section')">
            <i class="far fa-circle nav-icon"></i>
            <p>Section</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= base_url('libraries/unit') ?>" class="nav-link" v-bind:class="checkactive('unit')">
            <i class="far fa-circle nav-icon"></i>
            <p>Unit</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('libraries/fund') ?>" class="nav-link"  v-bind:class="checkactive('fund')">
            <i class="far fa-circle nav-icon"></i>
            <p>Source of Fund</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('libraries/position') ?>" class="nav-link" v-bind:class="checkactive('position')">
            <i class="far fa-circle nav-icon"></i>
            <p>Position</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('libraries/requirement') ?>" class="nav-link" v-bind:class="checkactive('requirement')">
            <i class="far fa-circle nav-icon"></i>
            <p>Requirement</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('libraries/tranche') ?>" class="nav-link "  v-bind:class="checkactive('tranche')">
            <i class="far fa-circle nav-icon"></i>
            <p>Salary Grade</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('libraries/plantilla') ?>" class="nav-link" v-bind:class="checkactive('plantilla')">
            <i class="far fa-circle nav-icon"></i>
            <p>Plantilla</p>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('vacant-positions') ?>" class="nav-link" v-bind:class="checkactive('vacant-positions')">
        <i class="nav-icon fas fa-list-ol"></i>
        <p>
          List of Vacant Positions
        </p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('vacant-positions') ?>" class="nav-link" v-bind:class="checkactive('vacant-positions')">
        <i class="nav-icon fas fa-th-list"></i>
        <p>
          List of Posted Positions
        </p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('examination/t') ?>" class="nav-link" v-bind:class="checkactive('examination/t')">
        <i class="nav-icon fas fa-tasks"></i>
        <p>
          Take Exam
        </p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('dashboard') ?>" class="nav-link" v-bind:class="checkactive('dashboard')">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>
          Dashboard
          <!-- <span class="right badge badge-danger">New</span> -->
        </p>
      </a>
    </li>
  </ul>
</nav>
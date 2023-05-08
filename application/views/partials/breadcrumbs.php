<ol class="breadcrumb">
    <template v-for="(list,index) in breadcrumbs">
        <li class="breadcrumb-item" :class="list.class">
            <!-- <a :href="list.link" v-if="list.link != '' && breadcrumbs.length-1 < index">
                {{list.title}}
            </a>
            <span v-else>
                {{list.title}}
            </span> -->

            {{list.title}}
        </li>
    </template>

    <!-- Breadcrumb Menu
    <li class="breadcrumb-menu d-md-down-none">
        <div class="btn-group" role="group" aria-label="Button group">
            <a class="btn" href="#">
                <i class="icon-speech"></i>
            </a>
            <a class="btn" href="./">
                <i class="icon-graph"></i>  Dashboard</a>
            <a class="btn" href="#">
                <i class="icon-settings"></i>  Settings</a>
        </div>
    </li>
    -->
</ol>
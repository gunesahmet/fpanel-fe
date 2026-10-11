<div class="row">
    <div class="col-lg-12">
        <div class="margin-top-10">
            <div class="row">
                <div class="col-lg-6 col-xs-12 hidden-xs">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?= __('User Overview');?></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <ul class="list-group clear-list m-t">
                                <li class="list-group-item fist-item">
                                   <span class="float-right">
                                        <?= $user_info['fullname'];?>
                                    </span>
                                    <?= __('Name Lastname');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $create_date;?>
                                    </span>
                                    <?= __('Created Date');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $expire_date;?>
                                    </span>
                                    <?= __('Expire Date');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $last_login_date;?>
                                    </span>
                                    <?= __('Last Login Date');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $user_info['last_login_ip'];?>
                                    </span>
                                    <?= __('Last Login IP');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $ipaddresses;?>
                                    </span>
                                    <?= __('Owned IP Address');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= ($user_info['status'] == "1" ? "<b class='badge badge-primary'>".__("Active")."</b>" : "<b class='badge badge-danger'>".__("Passive")."</b>");?>
                                    </span>
                                    <?= __('Status');?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-xs-12">
                    <div class="action-nav-normal">
                        <div class="row action-nav-row">
                            <?
                            if (!empty($system_items)) {
                                $dashboard_buttons = $system_items['dashboard'];
                                foreach ($dashboard_buttons as $dashboard) { ?>
                                    <div class="col-md-4 col-xs-12 action-nav-button">
                                        <a href="<?= $dashboard['link'];?>" title="<?= $dashboard['name'];?>">
                                            <?= $dashboard['icon'];?>
                                            <span>
                                                <?= $dashboard['name'];?>
                                            </span>
                                            <? if(!empty($dashboard['count'])) { ?>
                                                <span class="triangle-button red"> <span class="inner"><?= $dashboard['count'];?> </span></span>
                                            <? } ?>
                                        </a>
                                    </div>
                                <? } } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?= __('Resource Usages');?></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="row text-center">
                                <div class="col-md-4 col-xs-4 statistics-chart">
                                    <span class="chart" id="disk_usage" data-percent="<?= $user_data['user']['statistics']['percent_disk'];?>">
                                        <span class="title"><?= __('Disk'); ?></span>
                                        <span class="percent"></span>
                                    </span>
                                </div>
                                <div class="col-md-4 col-xs-4 statistics-chart">
                                    <span class="chart" id="traffic_usage" data-percent="<?= $user_data['user']['statistics']['percent_traffic'];?>">
                                        <span class="title"><?= __('Traffic'); ?></span>
                                        <span class="percent"></span>
                                    </span>
                                </div>
                                <div class="col-md-4 col-xs-4 statistics-chart">
                                    <span class="chart" id="dbsize_usage" data-percent="<?= $user_data['user']['statistics']['percent_db_size'];?>">
                                        <span class="title"><?= __('DB Size'); ?></span>
                                        <span class="percent"></span>
                                    </span>
                                </div>
                            </div>

                            <ul class="list-group clear-list m-t">
                                <? if(($user_type < 3) || (isset($client_type) && ($client_type < 3))) { ?>
                                    <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $user_data['user']['usage']['clients']; ?>
                                    </span>
                                        <span class="text-success"><?= __('Clients');?></span>
                                    </li>
                                <? } ?>

                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $user_data['user']['usage']['domains']; ?>
                                    </span>
                                    <span class="text-success"><?= __('Domains');?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?= __('Limits');?></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <ul class="list-group clear-list m-t">
                                <?
                                $data_limit = array(
                                    'client_limit' =>  __('Client Limit'),
                                    'domain_limit' =>  __('Domain Limit'),
                                    'disk_limit' =>  __('Disk Limit'),
                                    'traffic_limit' =>  __('Traffic Limit'),
                                    'subdomain_limit' =>  __('Subdomain Limit'),
                                    'domain_alias_limit' =>  __('Domain Alias Limit'),
                                    'email_box_limit' =>  __('Email Box Limit'),
                                    'email_box_size_limit' =>  __('Email Box Size Limit'),
                                    'database_limit' =>  __('Database Limit'),
                                    'database_size_limit' =>  __('Database Size Limit'),
                                    'template_limit' =>  __('Template Limit'),
                                );

                                if (($user_info['user_type'] > RESELLER_USER ) && ($user_info['user_type'] < SUPPORT_USER )) {
                                    unset($data_limit['client_limit']);
                                }
                                if ((isset($client_type)) && ($client_type > RESELLER_USER)) {
                                    unset($data_limit['client_limit']);
                                }

                                $first_class = "";
                                $i = 0;
                                foreach ($data_limit as $key => $value) {

                                    if($i == 0) { $first_class = "fist-item"; } else { $first_class = "";}
                                    $i++;
                                    if (($key == "disk_limit") || ($key == "traffic_limit") || ($key == "email_box_size_limit") || ($key == "database_size_limit")) {
                                        $limit = $this->env->convert($limits[$key]);
                                    }
                                    else {
                                        if(!is_numeric($limits[$key])) {
                                            $limit = __('Unlimited');
                                        }
                                        else {
                                            $limit = $limits[$key];
                                        }
                                    } ?>

                                    <li class="list-group-item <?= $first_class;?>">
                                    <span class="float-right">
                                        <?= $limit;?>
                                    </span>
                                    <span class="text-success"><i></i> &nbsp;<?= $value;?>
                                    </li>

                                <? } ?>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?= __('Permissions');?></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <ul class="list-group clear-list m-t">
                                <?
                                $data_permission = array(
                                    'man_client' => __('Access Client Management'),
                                    'man_domain' => __('Access Domain Management'),
                                    'man_template' => __('Access Template Management'),
                                    'man_hosting_settings' => __('Access Hosting Settings Management'),
                                    'man_subdomain' => __('Access Subdomain Management'),
                                    'man_database' => __('Access Database Management'),
                                    'man_domain_alias' => __('Access Domain Alias Management'),
                                    'man_email' => __('Access Email Management'),
                                    'man_email_alias' => __('Access Email Alias Management'),
//                                    'man_mailing_lists' => __('Access Mailing List Management'),
//                                    'man_spam_filters' => __('Access Spam Filters Management'),
                                    'man_task' => __('Access Scheduled Task Management'),
                                    'man_dns' => __('Access DNS Management'),
                                    'man_ssl' => __('Access SSL Management'),
                                    'man_backup' => __('Access Backup Management'),
                                    'man_protect_dir' => __('Access Protect Directory Management'),
                                );

                                if (($user_info['user_type'] > RESELLER_USER ) && ($user_info['user_type'] < SUPPORT_USER )) {
                                    unset($limits['client_limit']);
                                    unset($data_limit['clients']);
                                }
                                $first_class = "";
                                $i = 0;
                                foreach ($data_permission as $key => $value) {

                                    if($i == 0) { $first_class = "fist-item"; } else { $first_class = "";}
                                    $i++;
                                    if($permissions[$key] == "allowed") {
                                        $perm = __('Allowed');
                                    }
                                    else {
                                        $perm = __('Disallowed');
                                    }
                                    ?>
                                    <li class="list-group-item <?= $first_class;?>">
                                    <span class="float-right">
                                        <?= $perm;?>
                                    </span>
                                        <?= $value;?>
                                    </li>
                                <? } ?>

                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    var client_usage = {
        'traffic': {
            'used': '<?= $user_data['user']['statistics']['percent_traffic'];?>'
        },
        'disk': {
            'used': '<?= $user_data['user']['statistics']['percent_disk'];?>'
        },
        'db': {
            'used': '<?= $user_data['user']['statistics']['percent_db_size'];?>'
        }
    };
    core.dashboardClient(client_usage);
</script>

<div class="row">
    <div class="col-lg-12">
        <div class="margin-top-10">
            <div class="row">
                <div class="col-lg-4 col-xs-12 hidden-xs">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?= __('Informations');?></h5>
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
                                        <?= $last_login_date;?>
                                    </span>
                                    <span class="label label-info">*</span> <?= __('Last Login Time');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $general_items['last_login_ip'];?>
                                    </span>
                                    <span class="label label-info">*</span> <?= __('Last Login IP');?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12 col-xs-12">
                    <div class="action-nav-normal">
                        <div class="row action-nav-row">
                            <? if (!empty($system_items)) {
                                $dashboard_buttons = $system_items['dashboard'];
                                foreach ($dashboard_buttons as $dashboard) { ?>
                                    <div class="col-md-4 col-xs-12 action-nav-button">
                                        <a href="<?= $dashboard['link'];?>" class="fp <?= $dashboard['class'] ?? "";?>" >
                                            <?= $dashboard["icon"] ?? "<i></i>"; ?>
                                        <span>
                                            <?= $dashboard['name'];?>
                                        </span>
                                            <? if(!empty($dashboard['count'])) { ?>
                                                <span class="triangle-button red"> <span class="inner"><?= $dashboard['count'];?> </span></span>
                                            <? } ?>
                                        </a>
                                    </div>
                                <? }
                            } ?>
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
                                    <span class="chart" id="cpu_usage" data-percent="<?= $cpu_used;?>">
                                        <span class="title"><?= __('CPU'); ?></span>
                                        <span class="percent"></span>
                                    </span>
                                </div>

                                <div class="col-md-4 col-xs-4 statistics-chart">
                                    <span class="chart" id="memory_usage" data-percent="<?= $memory_used;?>">
                                        <span class="title"><?= __('Memory'); ?></span>
                                        <span class="percent"></span>
                                    </span>
                                </div>

                                <div class="col-md-4 col-xs-4 statistics-chart">
                                    <span class="chart" id="disk_usage" data-percent="<?= $disk_used;?>">
                                        <span class="title"><?= __('Disk'); ?></span>
                                        <span class="percent"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="flot-chart" style="height:300px;width: 100%">
                                    <div class="flot-chart-content" id="network-status"></div>
                                </div>
                            </div>


                            <ul class="list-group clear-list m-t">
                                <li class="list-group-item fist-item">
                                    <span class="float-right">
                                        <?= $system_items['user']['usage']['reseller_count']; ?>
                                    </span>
                                    <span class="text-success"><?= __('Resellers');?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $system_items['user']['usage']['client_count']; ?>
                                    </span>
                                    <span class="text-success"><?= __('Clients');?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $system_items['user']['usage']['domains']; ?>
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
                            <h5><?= __('System Overview');?></h5>
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
                                        <?= $logged_total_users;?>
                                    </span>
                                    <i class="fas fa-user-lock"></i>
                                    <span class="text-success"><?= __('Logged Total Users');?></span>
                                </li>

                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $api_status;?>
                                    </span>
                                    <i class="fas fa-laptop-code"></i>
                                    <span class="text-success"><?= __('Api Status');?></span>
                                </li>

                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $support_status;?>
                                    </span>
                                    <i class="fas fa-user-md"></i>
                                    <span class="text-success"><?= __('Support Status');?></span>
                                </li>

                                <?
                                if($system_type == CLOUD)
                                    $sys_type_icon = '<i class="fas fa-cloud"></i>';
                                else
                                    $sys_type_icon = '<i class="fas fa-desktop"></i>';
                                ?>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= ucfirst($system_type);?>
                                    </span>
                                    <?= $sys_type_icon;?>
                                    <span class="text-success"><?= __('System Type');?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $system_version;?>
                                    </span>
                                    <i class="fas fa-code-branch"></i>
                                    <span class="text-success"><?= __('Fpanel Version');?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $hostname;?>
                                    </span>
                                    <i class="fp fp-domain-name"></i>
                                    <span class="text-success"><?= __('Hostname');?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $default_ip_address;?>
                                    </span>
                                    <i class="fp fp-ipcontrol"></i>
                                    <span class="text-success"><?= __('Default IP Address');?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $operating_system;?>
                                    </span>
                                    <?
                                    if(PANEL_OS_TYPE == LINUX)
                                        $os_class = '<i class="fp fp-linux"></i>';
                                    else
                                        $os_class = '<i class="fp fp-windows"></i>';
                                    ?>

                                    <?= $os_class;?>
                                    <span class="text-success"><?= __('Operating System');?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $cpu_type;?>
                                    </span>
                                    <i class="fas fa-microchip"></i>
                                    <span class="text-success "><?= __('CPU Type');?></span>
                                </li>
                                <? if(PANEL_OS_TYPE == LINUX) {?>
                                    <li class="list-group-item">
                                        <span class="float-right" id="load-avg">
                                            <?= $load_average;?>
                                        </span>
                                        <i class="fas fa-signal"></i>
                                        <span class="text-success"><?= __('Load Average');?></span>
                                    </li>
                                <? } ?>
                                <li class="list-group-item">
                                    <span class="float-right" id="server-uptime">
                                        <?= $server_uptime;?>
                                    </span>
                                    <i class="fas fa-hourglass-half"></i>
                                    <span class="text-success"><?= __('Server Uptime');?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $this->env->convert($system_items['system_info']['memory']['total']); ?>
                                    </span>
                                    <i class="fas fa-memory"></i>
                                    <span class="text-success"><?= __('Total Memory');?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $this->env->convert($system_items['system_info']['disk']['total']); ?>
                                    </span>
                                    <i class="fas fa-hdd"></i>
                                    <span class="text-success"><?= __('Total Disk');?></span>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><?= __('Process List');?></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless" id="process-list">
                                    <thead class="thead-light">
                                    <tr>
                                        <th width="10%"><?= __("Pid");?></th>
                                        <th width="40%"><?= __("Process");?></th>
                                        <th width="5%"><?= __("CPU");?>%</th>
                                        <th width="5%"><?= __("Memory");?>%</th>
                                        <th width="10%"><?= __("User");?></th>
                                        <th width="30%"><?= __("Command Line");?></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var admin_usage = {
        'cpu': {
            'used': '<?= $cpu_used;?>'
        },
        'memory': {
            'used': '<?= $memory_used;?>'
        },
        'disk': {
            'used': '<?= $disk_used;?>'
        }
    };
    core.dashboardAdmin(admin_usage);
</script>

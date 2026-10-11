    <? if(isset($client_id)) {
        $user_id = $client_id;
        $user_type = $client_type;
        $user_data = $client_data;
        $suspend_link = '<span class="pull-right success" id="modals-bootbox-confirm" data-url="'. $this->uri->url_encrypt('users/suspend/'.$client_id.'/suspend') .'">'. __('Suspend').'</span>';
        $unsuspend_link = '<span class="pull-right success" id="modals-bootbox-confirm" data-url="'. $this->uri->url_encrypt('users/suspend/'.$client_id.'/unsuspend') .'">'. __('Unsuspend').'</span>';
    }
    else {
        $user_id = $system_items['user']['info']['user_id'];
        $user_type = $system_items['user']['info']['user_type'];
        $user_data = $system_items;
        $suspend_link = "";
        $unsuspend_link = "";
    }

    $user_info = $user_data['user']['info'];
    $limits = $user_data['user']['limits'];
    $permissions = $user_data['user']['permissions'];
    $statistics = $user_data['user']['statistics'];
    $usage = $user_data['user']['usage'];

    $ipaddresses = "";
    foreach($user_data['user']['ip_address'] as $id => $ip) {
        if($ipaddresses == null)
            $ipaddresses = $ip;
        else
            $ipaddresses = $ipaddresses . ', ' . $ip;
    } ?>
<!--    --><?// echo $this->env->getMessages();?>
    <?
    $percent_disk = $user_data['user']['statistics']['percent_disk'];
    $percent_traffic = $user_data['user']['statistics']['percent_traffic'];
    if($statistics['remain_total_traffic'] == "unlimited")
        $remain_traffic = __('Unlimited');
    else
        $remain_traffic = $this->env->convert($statistics['remain_total_traffic']);

    if($statistics['remain_total_disk'] == "unlimited")
        $remain_disk = __('Unlimited');
    else
        $remain_disk = $this->env->convert($statistics['remain_total_disk']);


    if ($user_info['status'] == "1") {
        $status = '<span class="label label-success green">' . __('Active') . '</span>';
    }
    else {
        $status = '<span class="label label-danger red">' . __('Passive') . '</span>';
    }


    $expire_date = $this->env->convertDate($user_info['expire_date'],__('Never'));
    $create_date = $this->env->convertDate($user_info['create_date'],__('Never'));


    $last_login_date = $this->env->convertDate($user_info['last_login'],__('Not yet login'));
    ?>
    <? if($nested == true) { ?>
        <div class="col-lg-12">
            <div class="margin-top-10">
                <div class="row">
                    <div class="col-lg-6 col-xs-12 hidden-xs">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><? echo __('User Overview');?></h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <ul class="list-group clear-list m-t">
                                    <li class="list-group-item fist-item">
                                   <span class="pull-right">
                                        <? echo $user_info['fullname'];?>
                                    </span>
                                        <? echo __('Name Lastname');?>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="pull-right">
                                        <? echo $create_date;?>
                                    </span>
                                        <? echo __('Created Date');?>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="pull-right">
                                        <? echo $expire_date;?>
                                    </span>
                                        <? echo __('Expire Date');?>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="pull-right">
                                        <? echo $last_login_date;?>
                                    </span>
                                        <? echo __('Last Login Time');?>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="pull-right">
                                        <? echo $user_info['last_login_ip'];?>
                                    </span>
                                        <? echo __('Last Login IP');?>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="pull-right">
                                        <? echo $ipaddresses;?>
                                    </span>
                                        <? echo __('Owned IP Address');?>
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
                                            <a href="<? echo $dashboard['link'];?>" class="<? echo $dashboard['class'];?>" title="<? echo $dashboard['name'];?>">
                                                <i></i>
                                        <span>
                                            <? echo $dashboard['name'];?>
                                        </span>
                                                <? if(!empty($dashboard['count'])) { ?>
                                                    <span class="triangle-button red"> <span class="inner"><? echo $dashboard['count'];?> </span></span>
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
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><? echo __('Resource Usages');?></h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="row text-center">
                                    <div class="col-md-6 col-xs-6">
                                        <canvas id="traffic_usage" width="125" height="125"></canvas>
                                        <h5 ><? echo __('Traffic Usage'); ?></h5>
                                    </div>

                                    <div class="col-md-6 col-xs-6">
                                        <canvas id="disk_usage" width="125" height="125"></canvas>
                                        <h5 ><? echo __('Disk Usage'); ?></h5>
                                    </div>
                                </div>

                                <ul class="list-group clear-list m-t">
                                    <? if(($user_type < 3) || (isset($client_type) && ($client_type < 3))) { ?>
                                        <li class="list-group-item">
                                    <span class="pull-right">
                                        <? echo $system_items['user']['usage']['clients']; ?>
                                    </span>
                                            <span class="text-success"><? echo __('Clients');?></span>
                                        </li>
                                    <? } ?>

                                    <li class="list-group-item">
                                    <span class="pull-right">
                                        <? echo $system_items['user']['usage']['domains']; ?>
                                    </span>
                                        <span class="text-success"><? echo __('Domains');?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><? echo __('Limits');?></h5>
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
                                        'email_forward_limit' =>  __('Email Forward Limit'),
                                        'email_groups_limit' =>  __('Email Group Limit'),
                                        'email_autoresponder_limit' =>  __('Email Auto Responder Limit'),
                                        'email_lists_limit' =>  __('Email List Limit'),
                                        'database_limit' =>  __('Database Limit'),
                                        'database_size_limit' =>  __('Database Size Limit'),
                                        'template_limit' =>  __('Template Limit'),
                                    );

                                    if (($user_info['user_type'] > "2" ) && ($user_info['user_type'] < "6" )) {
//                                    unset($limits['client_limit']);
                                        unset($data_limit['client_limit']);
                                    }
                                    if ((isset($client_type)) && ($client_type > 2)) {
//                                    unset($limits['client_limit']);
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

                                        <li class="list-group-item <? echo $first_class;?>">
                                    <span class="pull-right">
                                        <? echo $limit;?>
                                    </span>
                                    <span class="text-success"><i></i> &nbsp;<? echo $value;?>
                                        </li>

                                    <? } ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><? echo __('Permissions');?></h5>
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
                                        'man_ftp_user' => __('Access FTP User Management'),
                                        'man_email' => __('Access Email Management'),
                                        'man_email_alias' => __('Access Email Alias Management'),
                                        'man_mailing_lists' => __('Access Mailing List Management'),
                                        'man_spam_filters' => __('Access Spam Filters Management'),
                                        'man_task' => __('Access Scheduled Task Management'),
                                        'man_dns' => __('Access DNS Management'),
                                        'man_ssl' => __('Access SSL Management'),
                                        'man_statistics' => __('Access Statistics Management'),
                                        'man_backup_to_server' => __('Access Backup (Server) Management'),
                                        'man_backup_to_ftp' => __('Access Backup (FTP) Management'),
                                        'man_protect_dir' => __('Access Protect Directory Management'),
                                    );

                                    if (($user_info['user_type'] > "2" ) && ($user_info['user_type'] < "6" )) {
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


                                        <li class="list-group-item <? echo $first_class;?>">
                                    <span class="pull-right">
                                        <? echo $perm;?>
                                    </span>
                                            <? echo $value;?>
                                        </li>
                                    <? } ?>

                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    <? } else { ?>
    <ul class="gridster connectedLists" id="desktop">

    </ul>
        <div class="system-monitoring widget-panel widget-panel-default hidden-xs">
            <div class="widget-panel-heading"><? echo __('System Monitoring');?></div>
            <ul class="list-group">
                <li class="list-group-item no-bg">
                    <div class="media">
                        <h5><? echo __('Disk Usage');?></h5>
                        <div class="media-left">
                            <p class="data-attributes media-object">
                                <span class="disk_usage" data-peity='{ "fill": ["#2D99DC", "#222D33"],    "innerRadius": 20, "radius": 39 }'><? echo $percent_disk;?>/100</span>
                            </p>
                            <h5><? echo __('Traffic Usage');?></h5>
                            <p class="data-attributes media-object">
                                <span class="traffic_usage" data-peity='{ "fill": ["#2D99DC", "#222D33"],    "innerRadius": 20, "radius": 39 }'><? echo $percent_traffic;?>/100</span>
                            </p>
                        </div>
                        <div class="media-body media-top">
                            <table class="table">
                                <tr>
                                    <td><? echo __('Status');?>:</td>
                                    <td>
                                        <? if($user_data['user']['info']['status'] == "1") {?>
                                            <strong><span class="label label-success" data-toggle="tooltip" data-original-title="<? echo $user_data['user']['info']['status_msg'];?>"> <i class="fa fa-info-circle"></i> <? echo __('Active')?> </span></strong>
                                        <? } else { ?>
                                            <strong><span class="label label-danger" data-toggle="tooltip" data-original-title="<? echo $user_data['user']['info']['status_msg'];?>"> <i class="fa fa-info-circle"></i> <? echo __('Passive')?> </span> </strong>
                                        <? } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><? echo __('Last Login Time');?>:</td>
                                    <td><? echo $last_login_date;?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('Last Login IP');?>:</td>
                                    <td><? echo $user_data['user']['info']['last_login_ip'];?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('Email Address');?>:</td>
                                    <td><? echo $user_data['user']['info']['email'];?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('Created Date');?>:</td>
                                    <td><? echo $this->env->convertDate($user_data['user']['info']['create_date'], __('Unknown'));?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('Expire Date');?>:</td>
                                    <td><? echo $this->env->convertDate($user_data['user']['info']['expire_date'], __('Unlimited'));?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li class="list-group-item no-bg">
                    <h5><? echo __('Build')?></h5>
                    <p><? echo 'Fpanel ' . $system_version;?> <i class="fa fa-fw fa-info-circle"></i></p>
                </li>
            </ul>
        </div>
        <style>
            .icon-web-hostings-settings-2 .path4::before {
                color: rgb(252, 98, 32);
            }
            .icon-web-hosting-settings .path2::before, .icon-web-hosting-settings .path3::before, .icon-web-hosting-settings .path4::before  {
                color: #fff;
            }
            .icon-mail-server .path1::before {
                color: #fff;
            }
            .icon-mail-server .path2::before, .icon-mail-server .path3::before, .icon-mail-server .path4::before {
                color: rgb(252, 98, 32);
            }
            .icon-mail-accounts .path2::before, .icon-mail-accounts .path3::before {
                color: rgb(252, 98, 32);
            }
        </style>
    <? } ?>
</div>



<?
    if(isset($client_data)) {
       $user_data = $client_data;
    }
    $limits = $user_data['user']['limits'];

    $disk_limit = $this->env->convert($limits['disk_limit']);
    $traffic_limit = $this->env->convert($limits['traffic_limit']);
    $used_disk = $this->env->convert($user_data['user']['statistics']['used_total_disk']);
    $used_traffic = $this->env->convert($user_data['user']['statistics']['used_total_traffic']);
    $remain_disk = $this->env->convert($user_data['user']['statistics']['remain_total_disk']);
    $remain_traffic = $this->env->convert($user_data['user']['statistics']['remain_total_traffic']);

    $percent_disk = $user_data['user']['statistics']['percent_disk'];
    $percent_traffic = $user_data['user']['statistics']['percent_traffic'];
    $last_login_date = "-";
    if (!empty($user_data['user']['info']['last_login']) ) {
        $last_login_date = $user_data['user']['info']['last_login'];
    }
?>


<div class="row">
    <div class="col-lg-4 col-xs-12">
        <div class="ibox hidden-xs">
            <div class="ibox-title">
                <h5><?= $user_data['user']['domain_info']['domain_name'] . ' ' . __('Resource Usages');?></h5>
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
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('Overview');?></h5>
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
                                <? if(isset($domain_id)) { ?>
                                    <? if($user_data['user']['info']['status'] != ACTIVE) { ?>
                                        <input id="client_status" type="checkbox" class="make-switch" data-toggle="toggle" data-offstyle="danger" data-size="mini" data-url="<?= $this->uri->url_encrypt("domains/suspend/". $user_data['user']['domain_info']['domain_id'] ."/unsuspend")?>" data-on-text="<?= __('On'); ?>" data-off-text="<?= __('Off'); ?>">
                                    <? } else { ?>
                                        <input id="client_status" type="checkbox" checked class="make-switch" data-toggle="toggle" data-offstyle="danger" data-size="mini" data-url="<?= $this->uri->url_encrypt("domains/suspend/". $user_data['user']['domain_info']['domain_id'] ."/suspend")?>" data-on-text="<?= __('On'); ?>" data-off-text="<?= __('Off'); ?>">
                                    <? } ?>

                                <? } else { ?>
                                    <? if($user_data['user']['info']['status'] != ACTIVE) { ?>
                                        <strong><span class="text-danger" data-toggle="tooltip" title="<?= $user_data['user']['info']['status_msg'];?>"> <?= __('Passive')?> </span> </strong>
                                    <? } else { ?>
                                        <strong><span class="text-navy" data-toggle="tooltip" title="<?= $user_data['user']['info']['status_msg'];?>"> <?= __('Active')?> </span></strong>
                                    <? } ?>
                                <? } ?>

                        </span>
                         <?= __('Status');?>
                    </li>
                    <li class="list-group-item">
                        <span class="float-right">
                            <?= $last_login_date;?>
                        </span>
                         <?= __('Last Login Time');?>
                    </li>
                    <li class="list-group-item">
                        <span class="float-right">
                            <?= $user_data['user']['info']['last_login_ip'];?>
                        </span>
                         <?= __('Last Login IP');?>
                    </li>
                    <li class="list-group-item">
                        <span class="float-right">
                            <?= $user_data['user']['info']['email'];?>
                        </span>
                         <?= __('Email Address');?>
                    </li>
                    <li class="list-group-item">
                        <span class="float-right">
                            <?= $this->date->getCustomDateTime($user_data['user']['info']['created_date'], "Y-m-d");?>
                        </span>
                         <?= __('Created Date');?>
                    </li>
                    <li class="list-group-item">
                        <span class="float-right">
                            <?= $this->date->getCustomDateTime($user_data['user']['info']['expire_date'], "Y-m-d");?>
                        </span>
                         <?= __('Expire Date');?>
                    </li>
                </ul>
            </div>
        </div>
        <? if(isset($domain_id)) { ?>
            <div class="ibox hidden-xs">
                <div class="ibox-title">
                    <h5><?= __('Services');?></h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <? $dns = ""; $mail = ""; ?>
                        <? if($user_data['user']['domain_info']['dns_service'] == ACTIVE) $dns = "checked"; ?>
                        <? if($user_data['user']['domain_info']['mail_service'] == ACTIVE) $mail = "checked"; ?>
                        <div class="col-md-6"><?= __('DNS Service');?></div>
                        <div class="col-md-6"><?= __('Mail Service');?></div>
                        <div class="col-md-6">
                            <input name="enable_dns" id="enable_dns" type="checkbox" data-on-color="primary" data-toggle="toggle" data-offstyle="danger" <?= $dns;?> class="make-switch enable_service" data-size="mini" data-url="<?= $this->uri->url_encrypt("domains/service/". $user_data['user']['domain_info']['domain_id'])?>" data-on-text="<?= __('On'); ?>" data-off-text="<?= __('Off'); ?>">
                        </div>
                        <div class="col-md-6">
                            <input name="enable_mail" id="enable_mail" type="checkbox" data-on-color="primary" data-toggle="toggle" data-offstyle="danger" <?= $mail;?> class="make-switch enable_service" data-size="mini" data-url="<?= $this->uri->url_encrypt("domains/service/". $user_data['user']['domain_info']['domain_id'])?>" data-on-text="<?= __('On'); ?>" data-off-text="<?= __('Off'); ?>">
                        </div>
                    </div>

                </div>
            </div>
        <? } ?>

    </div>
    <div class="col-lg-8 col-md-12 col-xs-12">
        <? foreach ($system_items['dashboard'] as $dash_menu => $dash_menu_array) { ?>
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= $dash_menu_array['menu_name']; ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="action-nav-normal">
                        <div class="row action-nav-row">
                            <? foreach($dash_menu_array['menu_items'] as $dash_menu_buttons)  { ?>
                                <div class="col-md-3 col-xs-12 action-nav-button">
                                    <? if(isset($dash_menu_buttons['data-toggle'])) { ?>
                                    <a class="icon-btn <?= $dash_menu_buttons['class']; ?>" data-id="<?= $dash_menu_buttons['link']; ?>" data-url="<?= $dash_menu_buttons['data-url']; ?>">
                                        <? } else { ?>
                                        <a href="<?= $dash_menu_buttons['link']; ?>" <? if(!empty($dash_menu_buttons['target'])) echo 'target="'. $dash_menu_buttons['target'] .'"'; ?> class="icon-btn <?= $dash_menu_buttons['class']; ?>">
                                            <? } ?>
                                            <?= $dash_menu_buttons['icon'];?>
                                            <div>
                                                <?= $dash_menu_buttons['name'];?>
                                            </div>
                                            <? if(!empty($dash_menu_buttons['count'])) { ?>
                                                <span class="triangle-button red">  <span class="inner"><?= $dash_menu_buttons['count'];?> </span></span>
                                            <? } ?>
                                        </a>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>

<script>
    var domain_usage = {
        'traffic': {
            'used': '<?= $client_data['user']['statistics']['percent_traffic'];?>'
        },
        'disk': {
            'used': '<?= $client_data['user']['statistics']['percent_disk'];?>'
        },
        'db': {
            'used': '<?= $client_data['user']['statistics']['percent_db_size'];?>'
        }
    };
    core.dashboardClient(domain_usage);
    <? if(isset($domain_id)) { ?>
    core.alertChangeService();
    <? } ?>
</script>

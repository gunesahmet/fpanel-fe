<div class="row">
    <div class="col-sm-8">
        <div class="ibox">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="tabbable-line">
                    <ul class="nav nav-pills nav-fill" id="advanced-settings-tabs">
                        <li class="nav-item active"><a data-toggle="tab" href="#limits"><i class="fa fa-warning"></i> <?= __('Limits'); ?></a></li>
                        <li class="nav-item"><a data-toggle="tab" href="#permissions"><i class="fa fa-shield"></i> <?= __('Permissions'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="limits" class="tab-pane active">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="40%"><?= __('Source Management'); ?></th>
                                        <th width="30%"><?= __('Limits'); ?></th>
                                        <th width="30%"><?= __('Usage'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <? $data['template_limits'] = array(
                                        'disk_limit' =>             array('check' => 'disk_checks', 'size' => true, 'display_name' => __('Disk')),
                                        'traffic_limit' =>          array('check' => 'traffic_checks', 'size' => true, 'display_name' => __('Traffic')),
                                        'subdomain_limit' =>        array('check' => 'subdomain_checks', 'size' => false, 'display_name' => __('Subdomain')),
                                        'domain_alias_limit' =>     array('check' => 'domain_alias_checks', 'size' => false, 'display_name' => __('Domain Alias')),
                                        'email_box_limit' =>        array('check' => 'email_box_checks', 'size' => false, 'display_name' => __('Email Box')),
                                        'email_box_size_limit' =>   array('check' => 'email_box_size_checks', 'size' => true, 'display_name' => __('Email Box Size')),
                                        'database_limit' =>         array('check' => 'database_checks', 'size' => false, 'display_name' => __('Database')),
                                        'database_size_limit' =>    array('check' => 'database_size_checks', 'size' => true, 'display_name' => __('Database Size')),
                                    );
                                    foreach ($data['template_limits'] as $key => $value) { ?>
                                        <tr>
                                            <td><label><?= $value['display_name']; ?></label></td>
                                            <? if ($value["size"]) { ?>
                                            <td><?= $this->env->convert($domain_data[$key]); ?></td>
                                            <? } else { ?>
                                                <td><?= __(ucfirst($domain_data[$key])); ?></td>
                                            <? } ?>
                                            <td><?= $resource_usages[$key]; ?></td>
                                        </tr>
                                    <? } ?>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                        <div id="permissions" class="tab-pane">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <td width="40%"><?= __('Permissions'); ?></td>
                                        <td width="20%"><?= __('Status'); ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <? $data['template_permissions'] = array(
                                        'access_panel' => array('display_name' => __('Access Panel')),
                                        'man_hosting_settings' => array('display_name' => __('Access Hosting Settings Management')),
                                        'man_subdomain' => array('display_name' => __('Access Subdomain Management')),
                                        'man_database' => array('display_name' => __('Access Database Management')),
                                        'man_domain_alias' => array('display_name' => __('Access Domain Alias Management')),
                                        'man_email' => array('display_name' => __('Access Email Management')),
                                        'man_email_alias' => array('display_name' => __('Access Email Alias Management')),
                                        'man_task' => array('display_name' => __('Access Scheduled Task Management')),
                                        'man_dns' => array('display_name' => __('Access DNS Management')),
                                        'man_ssl' => array('display_name' => __('Access SSL Management')),
                                        'man_backup' => array('display_name' => __('Access Backup Management')),
                                        'man_protect_dir' => array('display_name' => __('Access Protect Directory Management')),
                                    );
                                    foreach ($data['template_permissions'] as $key => $value) {
                                        ?>
                                        <tr>
                                            <td><label for="<?= $key;?>"><?= $value['display_name']; ?></label></td>
                                            <td><? if($domain_data[$key] == ALLOWED) echo '<span class="text-success">' . __('Allowed') . '</span>'; else echo '<span class="text-danger">' . __('Disallowed') . '</span>'; ?></td>
                                        </tr>
                                    <? } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div id="contact-1" class="tab-pane active">
                    <div class="row m-b-lg">
                        <? if(isset($domain_id)) { ?>
                            <div class="col-lg-12">
                                <a href="<?= $this->uri->url_encrypt("resources/edit/domain/".$domain_id);?>" class="btn btn-primary btn-sm btn-block"> <?= __('Edit');?> </a>
                            </div>
                        <? } ?>
                    </div>
                    <div class="client-detail">
                        <div class="">
                            <strong><?= __('About');?> <?= $domain_data['name'];?></strong>
                            <hr />
                            <ul class="list-group clear-list m-t">
                                <li class="list-group-item fist-item">
                                    <span class="float-right">
                                         <? if($domain_data['status'] == "1") { ?>
                                             <span class="text-success"><?= __('Active');?></span>
                                         <? } else { ?>
                                             <span class="text-danger"><?= __('Passive');?></span>
                                         <? } ?>
                                    </span>
                                    <span class="label label-success">*</span> <?= __('Status')?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                      <? if($domain_data['overuse'] == ACTIVE) { ?>
                                          <span class="text-success"><?= __('Allowed');?></span>
                                      <? } else { ?>
                                          <span class="text-danger"><?= __('Disallowed');?></span>
                                      <? } ?>
                                    </span>
                                    <span class="label label-info">*</span> <?= __('Overuse')?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $this->date->getOnlyDate($domain_data['created_date'], 'Y/m/d');?>
                                    </span>
                                    <span class="label label-primary">*</span> <?= __('Setup Date')?>
                                </li>
                                <li class="list-group-item">
                                    <span class="float-right">
                                        <?= $domain_data["expire_date"] == UNLIMITED ? __('Unlimited') : $this->date->getOnlyDate($domain_data['expire_date'], 'Y/m/d');?>
                                    </span>
                                    <span class="label label-danger">*</span> <?= __('Expire Date')?>
                                </li>
                            </ul>


                            <strong><?= __('Usage Overview');?></strong>
                            <hr />
                            <ul class="list-group clear-list m-t">
                                <li class="list-group-item fist-item">
                                    <span class="label label-danger float-right"><?= $domain_data['subdomain_count'];?></span>
                                    <?= __('Used Subdomains');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="label label-danger float-right"><?= $domain_data['domain_alias_count'];?></span>
                                    <?= __('Used Domain Aliases');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="label label-danger float-right"><?= $domain_data['mail_count'];?></span>
                                    <?= __('Used Emails');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="label label-danger float-right"><?= $domain_data['database_count'];?></span>
                                    <?= __('Used Databases');?>
                                </li>
                                <li class="list-group-item">
                                    <span class="label label-danger float-right"><?= $domain_data['email_alias_count'];?></span>
                                    <?= __('Used Email Aliases');?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
<form class="form-horizontal" method="post">
    <div class="row">
        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
        <div class="col-md-8">
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
                            <li class="nav-item active"><a data-toggle="tab" href="#limits"><i class="fas fa-warning"></i> <?= __('Limits'); ?></a></li>
                            <li class="nav-item"><a data-toggle="tab" href="#permissions"><i class="fas fa-shield"></i> <?= __('Permissions'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="limits" class="tab-pane active">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <td width="40%"><strong><?= __('Source Management'); ?></strong></td>
                                            <td width="40%"><strong><?= __('Limits'); ?></strong></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <? $data['template_limits'] = array(
                                            'disk_limit' => array('input' => 'disk','display_name' => __('Disk Limit') .' <a class="text-danger">(MB)</a>'),
                                            'traffic_limit' => array('input' => 'traffic','display_name' => __('Traffic Limit').' <a class="text-danger">(MB)</a>'),
                                            'subdomain_limit' => array('input' => 'subdomain','display_name' => __('Subdomain Limit')),
                                            'domain_alias_limit' => array('input' => 'domain_alias','display_name' => __('Domain Alias Limit')),
                                            'email_box_limit' => array('input' => 'email_box','display_name' => __('Email Box Limit')),
                                            'email_box_size_limit' => array('input' => 'email_box_size','display_name' => __('Email Box Size Limit').' <a class="text-danger">(MB)</a>'),
                                            'database_limit' => array('input' => 'database','display_name' => __('Database Limit')),
                                            'database_size_limit' => array('input' => 'database_size','display_name' => __('Database Size Limit').' <a class="text-danger">(MB)</a>'),
                                        );
                                        foreach ($data['template_limits'] as $key => $value) { ?>
                                            <tr>
                                                <td><label for="<?= $key;?>"><?= $value['display_name']; ?></label></td>
                                                <td><?= $form->render($key,array('class' => 'form-control form-control-sm input-sm')); ?></td>
                                                <td>
                                                    <?= $form->render($value['input'],array('class' => 'checkbox-blue')); ?>
                                                    <label class="checkbox-inline" for="<?= $value['input'];?>">
                                                        <strong><?= __('Unlimited');?></strong>
                                                    </label>
                                                </td>
                                            </tr>
                                        <? } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="permissions" class="tab-pane">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <td width="40%"><h4><?= __('Permissions');?></h4></td>
                                            <td width="20%"><h4><?= __('Status');?></h4></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <? $data['template_permissions'] = array(
                                            'overuse' => array('display_name' => __('Overuse')),
                                            'access_panel' => array('display_name' => __('Access Panel')),
                                            'access_hosting_settings' => array('display_name' => __('Access Hosting Settings Management')),
                                            'access_subdomain' => array('display_name' => __('Access Subdomain Management')),
                                            'access_database' => array('display_name' => __('Access Database Management')),
                                            'access_domain_alias' => array('display_name' => __('Access Domain Alias Management')),
                                            'access_email' => array('display_name' => __('Access Email Management')),
                                            'access_email_alias' => array('display_name' => __('Access Email Alias Management')),
                                            'access_task' => array('display_name' => __('Access Scheduled Task Management')),
                                            'access_dns' => array('display_name' => __('Access DNS Management')),
                                            'access_ssl' => array('display_name' => __('Access SSL Management')),
                                            'access_backup' => array('display_name' => __('Access Backup Management')),
                                            'access_protect_dir' => array('display_name' => __('Access Protect Directory Management')),
                                        ); ?>
                                        <? foreach ($data['template_permissions'] as $key => $value) { ?>
                                            <tr>
                                                <td><label for="<?= $key;?>"><?= $value['display_name']; ?></label></td>
                                                <td>
                                                    <?= $form->render($key,array('class' => 'checkbox-blue')); ?>
                                                </td>
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
        <div class="col-md-4">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row m-b-lg">
                        <div class="col-lg-4 text-center">
                            <h2><?= $domain_data['name'];?></h2>
                            <div class="m-b-sm">
                                <i class="fa fa-globe fa-3x"></i>
                            </div>
                        </div>
                        <? if(isset($domain_id)) { ?>
                            <div class="col-lg-8">
                                <button class="btn btn-sm btn-block btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                <button class="btn btn-sm btn-block btn-primary" type="submit"> <?= __('Save');?> </button>
                            </div>
                        <? } ?>
                    </div>
                    <div class="client-detail">
                        <ul class="list-group clear-list">
                            <li class="list-group-item fist-item">
                                <? if($domain_data['status'] == ACTIVE) { ?>
                                    <span class="text-success float-right"><?= __('Active');?></span>
                                <? } else { ?>
                                    <span class="text-danger float-right"><?= __('Passive');?></span>
                                <? } ?>
                                <b><?= __('Status');?></b>
                            </li>
                            <li class="list-group-item">
                                <? if($domain_data['overuse'] == ACTIVE) { ?>
                                    <span class="text-success float-right"><?= __('Allowed');?></span>
                                <? } else { ?>
                                    <span class="text-danger float-right"><?= __('Disallowed');?></span>
                                <? } ?>
                                <b><?= __('Overuse');?></b>
                            </li>
                            <li class="list-group-item">
                                <span class="float-right">
                                    <?= $this->date->getOnlyDate($domain_data['created_date'], 'Y/m/d');?>
                                </span>
                                <b><?= __('Setup Date');?></b>
                            </li>
                            <li class="list-group-item">
                                <span class="float-right">
                                <?= $domain_data["expire_date"] == UNLIMITED ? __('Unlimited') : $this->date->getOnlyDate($domain_data['expire_date'], 'Y/m/d');?>
                                </span>
                                <b><?= __('Expire Date');?></b>
                            </li>
                        </ul>
                        <br />
                        <br />

                        <ul class="list-group clear-list">
                            <li class="list-group-item">
                                <b><?= __('Used Subdomains');?></b>
                                <span class="float-right badge badge-danger"><?= $domain_data['subdomain_count'];?></span>
                            </li>
                            <li class="list-group-item">
                                <b><?= __('Used Domain Aliases');?></b>
                                <span class="float-right badge badge-danger"><?= $domain_data['domain_alias_count'];?></span>
                            </li>
                            <li class="list-group-item">
                                <b><?= __('Used Emails');?></b>
                                <span class="float-right badge badge-danger"><?= $domain_data['mail_count'];?></span>
                            </li>
                            <li class="list-group-item">
                                <b><?= __('Used Databases');?></b>
                                <span class="float-right badge badge-danger"><?= $domain_data['database_count'];?></span>
                            </li>
                            <li class="list-group-item">
                                <b><?= __('Used Email Aliases');?></b>
                                <span class="float-right badge badge-danger"><?= $domain_data['email_alias_count'];?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
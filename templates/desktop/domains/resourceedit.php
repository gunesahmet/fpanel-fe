<form class="form-horizontal" method="post">
    <div class="col-sm-8">
        <div class="ibox">
            <div class="ibox-content">
                <span class="text-muted small pull-right"></span>
                <h2><? echo $domain_name;?></h2>
                <br />
                <div>
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#limits"><i class="fa fa-warning"></i> <? echo __('Limits'); ?></a></li>
                        <li><a data-toggle="tab" href="#permissions"><i class="fa fa-shield"></i> <? echo __('Permissions'); ?></a></li>
                    </ul>
                    <div class="tab-content">

                        <div id="limits" class="tab-pane active">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td width="40%"><h4><? echo __('Source Management'); ?></h4></td>
                                        <td width="40%"><h4><? echo __('Limits'); ?></h4></td>
                                        <td></td>
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
                                            <td><label form="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                            <td><? echo $form->render($key,array('class' => 'form-control')); ?></td>
                                            <td>
                                                <? echo $form->render($value['input'],array('class' => 'checkbox')); ?>
                                                <label class="checkbox-inline" for="<? echo $value['input'];?>">
                                                    <strong><? echo __('Unlimited');?></strong>
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
                                        <td width="40%"><h4><? echo __('Permissions');?></h4></td>
                                        <td width="20%"><h4><? echo __('Status');?></h4></td>
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
                                        'access_statistics' => array('display_name' => __('Access Statistics Management')),
                                        'access_backup_to_server' => array('display_name' => __('Access Backup (Server) Management')),
                                        'access_backup_to_ftp' => array('display_name' => __('Access Backup (FTP) Management')),
                                        'access_protect_dir' => array('display_name' => __('Access Protect Directory Management')),
                                    ); ?>
                                    <? foreach ($data['template_permissions'] as $key => $value) { ?>
                                        <tr>
                                            <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                            <td>
                                                <? echo $form->render($key,array('class' => 'checkbox')); ?>
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
    <div class="col-sm-4">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row m-b-lg">
                    <div class="col-lg-4 text-center">
                        <h2><? echo $domain_data['name'];?></h2>
                        <div class="m-b-sm">
                            <i class="fa fa-globe mid-icon"></i>
                        </div>
                    </div>
                    <? if(isset($domain_id)) { ?>
                        <div class="col-lg-8">
                            <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                            <button type="submit" class="btn btn-primary btn-sm btn-block"> <? echo __('Save');?> </button>
                        </div>
                    <? } ?>
                </div>
                <div class="client-detail">
                    <strong><? echo __('About Domain');?></strong>

                    <ul class="list-group clear-list">
                        <li class="list-group-item fist-item">
                            <? if($domain_data['status'] == "1") { ?>
                                <span class="text-success pull-right"><? echo __('Active');?></span>
                            <? } else { ?>
                                <span class="text-danger pull-right"><? echo __('Passive');?></span>
                            <? } ?>
                            <? echo __('Status');?>
                        </li>
                        <li class="list-group-item">
                            <? if($domain_data['overuse'] == "true") { ?>
                                <span class="text-success pull-right"><? echo __('Allowed');?></span>
                            <? } else { ?>
                                <span class="text-danger pull-right"><? echo __('Disallowed');?></span>
                            <? } ?>
                            <? echo __('Overuse');?>
                        </li>
                        <li class="list-group-item">
                            <span class="pull-right"><? echo date('d/m/Y', $domain_data['create_date']);?></span>
                            <? echo __('Setup Date');?>
                        </li>
                        <li class="list-group-item">
                                                <span class="pull-right">
                                                    <? if($domain_data['expire_date'] == "unlimited") { echo __('Unlimited');} else { echo date('d/m/Y', $domain_data['expire_date']);}?>
                                                </span>
                            <? echo __('Expire Date');?>
                        </li>
                    </ul>
                    <strong><? echo __('Usage Overview');?></strong>

                    <ul class="list-group clear-list">
                        <li class="list-group-item">
                            <span class="ellipsis"><? echo __('Used Subdomains');?></span>
                            <span class="badge badge-danger"><? echo $domain_data['subdomain_count'];?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="ellipsis"><? echo __('Used Domain Aliases');?></span>
                            <span class="badge badge-danger"><? echo $domain_data['domain_alias_count'];?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="ellipsis"><? echo __('Used Emails');?></span>
                            <span class="badge badge-danger"><? echo $domain_data['mail_count'];?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="ellipsis"><? echo __('Used Databases');?></span>
                            <span class="badge badge-danger"><? echo $domain_data['database_count'];?></span>
                        </li>
                        <li class="list-group-item">
                            <span class="ellipsis"><? echo __('Used Email Aliases');?></span>
                            <span class="badge badge-danger"><? echo $domain_data['email_alias_count'];?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

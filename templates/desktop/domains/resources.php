<div class="row">
    <div class="col-sm-8">
        <div class="tabbable-line">
            <ul class="nav nav-tabs nav-justified" id="advanced-settings-tabs">
                <li class="active"><a data-toggle="tab" href="#limits"><i class="fa fa-warning"></i> <? echo __('Limits'); ?></a></li>
                <li class=""><a data-toggle="tab" href="#permissions"><i class="fa fa-shield"></i> <? echo __('Permissions'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div id="limits" class="tab-pane active">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <td width="40%"><? echo __('Source Management'); ?></td>
                                <td width="40%"><? echo __('Limits'); ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <? $data['template_limits'] = array(
                                'disk_limit' => array('check' => 'disk_checks','display_name' => __('Disk Limit')),
                                'traffic_limit' => array('check' => 'traffic_checks','display_name' => __('Traffic Limit')),
                                'subdomain_limit' => array('check' => 'subdomain_checks','display_name' => __('Subdomain Limit')),
                                'domain_alias_limit' => array('check' => 'domain_alias_checks','display_name' => __('Domain Alias Limit')),
                                'email_box_limit' => array('check' => 'email_box_checks','display_name' => __('Email Box Limit')),
                                'email_box_size_limit' => array('check' => 'email_box_size_checks','display_name' => __('Email Box Size Limit')),
                                'database_limit' => array('check' => 'database_checks','display_name' => __('Database Limit')),
                                'database_size_limit' => array('check' => 'database_size_checks','display_name' => __('Database Size Limit')),
                            );
                            foreach ($data['template_limits'] as $key => $value) { ?>
                                <tr>
                                    <td><label><? echo $value['display_name']; ?></label></td>
                                    <td><? echo $this->env->convert($domain_data[$key]); ?></td>
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
                                <td width="40%"><? echo __('Permissions'); ?></td>
                                <td width="20%"><? echo __('Status'); ?></td>
                            </tr>
                            </thead>
                            <tbody>

                            <? $data['template_permissions'] = array(
                                'access_panel_perm' => array('display_name' => __('Access Panel')),
                                'man_hosting_settings' => array('display_name' => __('Access Hosting Settings Management')),
                                'man_subdomain' => array('display_name' => __('Access Subdomain Management')),
                                'man_database' => array('display_name' => __('Access Database Management')),
                                'man_domain_alias' => array('display_name' => __('Access Domain Alias Management')),
                                'man_ftp_user' => array('display_name' => __('Access FTP User Management')),
                                'man_email' => array('display_name' => __('Access Email Management')),
                                'man_email_alias' => array('display_name' => __('Access Email Alias Management')),
//                                            'man_mailing_lists' => array('display_name' => __('Access Mailing List Management')),
//                                            'man_spam_filters' => array('display_name' => __('Access Spam Filters Management')),
                                'man_task' => array('display_name' => __('Access Scheduled Task Management')),
                                'man_dns' => array('display_name' => __('Access DNS Management')),
                                'man_ssl' => array('display_name' => __('Access SSL Management')),
                                'man_statistics' => array('display_name' => __('Access Statistics Management')),
                                'man_backup_to_server' => array('display_name' => __('Access Backup (Server) Management')),
                                'man_backup_to_ftp' => array('display_name' => __('Access Backup (FTP) Management')),
                                'man_protect_dir' => array('display_name' => __('Access Protect Directory Management')),
                            );
                            foreach ($data['template_permissions'] as $key => $value) {
                                ?>
                                <tr>
                                    <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                    <td><? if($domain_data[$key] == "allowed") echo '<span class="text-success">' . __('Allowed') . '</span>'; else echo '<span class="text-danger">' . __('Disallowed') . '</span>'; ?></td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div id="contact-1" class="tab-pane active">
            <div class="row m-b-lg">
                <? if(isset($domain_id)) { ?>
                    <div class="col-lg-8">
                        <a href="<? echo $this->uri->url_encrypt("resources/edit/domain/".$domain_id);?>" class="btn btn-primary btn-sm btn-block"> <? echo __('Edit');?> </a>
                    </div>
                <? } ?>
            </div>
            <div class="client-detail">
                <div class="">
                    <strong><? echo __('About');?> <? echo $domain_data['name'];?></strong>

                    <ul class="list-group clear-list">
                        <li class="list-item fist-item">
                            <? if($domain_data['status'] == "1") { ?>
                                <span class="text-success pull-right"><? echo __('Active');?></span>
                            <? } else { ?>
                                <span class="text-danger pull-right"><? echo __('Passive');?></span>
                            <? } ?>
                            <? echo __('Status');?>
                        </li>
                        <li class="list-item">
                            <? if($domain_data['overuse'] == "true") { ?>
                                <span class="text-success pull-right"><? echo __('Allowed');?></span>
                            <? } else { ?>
                                <span class="text-danger pull-right"><? echo __('Disallowed');?></span>
                            <? } ?>
                            <? echo __('Overuse');?>
                        </li>
                        <li class="list-item">
                            <span class="pull-right"><? echo date('d/m/Y', $domain_data['create_date']);?></span>
                            <? echo __('Setup Date');?>
                        </li>
                        <li class="list-item">
                            <span class="pull-right">
                                <? if($domain_data['expire_date'] == "unlimited") { echo __('Unlimited');} else { echo date('d/m/Y', $domain_data['expire_date']);}?>
                            </span>
                            <? echo __('Expire Date');?>
                        </li>
                    </ul>
                    <strong><? echo __('Usage Overview');?></strong>

                    <ul class="list-group clear-list">
                        <li class="list-item fist-item">
                            <span class="label label-danger pull-right"><? echo $domain_data['subdomain_count'];?></span>
                            <? echo __('Used Subdomains');?>
                        </li>
                        <li class="list-item">
                            <span class="label label-danger pull-right"><? echo $domain_data['domain_alias_count'];?></span>
                            <? echo __('Used Domain Aliases');?>
                        </li>
                        <li class="list-item">
                            <span class="label label-danger pull-right"><? echo $domain_data['mail_count'];?></span>
                            <? echo __('Used Emails');?>
                        </li>
                        <li class="list-item">
                            <span class="label label-danger pull-right"><? echo $domain_data['database_count'];?></span>
                            <? echo __('Used Databases');?>
                        </li>
                        <li class="list-item">
                            <span class="label label-danger pull-right"><? echo $domain_data['email_alias_count'];?></span>
                            <? echo __('Used Email Aliases');?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

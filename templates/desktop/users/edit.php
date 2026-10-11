<div class="row" id="user-edit-panel" data-width="70%">
    <form id="edit-user-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo $client_name;?>
                    <small class="text-muted small pull-right"><? echo __('Total Domain') . ' ' .$user_edit_data['domain_count'];?></small>
                </div>
                <div class="panel-body">
                    <div class="tabbable-line">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="active"><a data-toggle="tab" href="#account"><i class="fa fa-edit"></i> <? echo __('Account'); ?></a></li>
                            <li class=""><a data-toggle="tab" href="#limits"><i class="fa fa-warning"></i> <? echo __('Limits'); ?></a></li>
                            <li class=""><a data-toggle="tab" href="#permissions"><i class="fa fa-shield"></i> <? echo __('Permissions'); ?></a></li>
                            <li class=""><a data-toggle="tab" href="#hosting_settings"><i class="fa fa-globe"></i> <? echo __('Hosting Settings'); ?></a></li>
                            <li class=""><a data-toggle="tab" href="#ip_address"><i class="fa fa-sitemap"></i> <? echo __('IP Addresses'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="account" class="tab-pane active">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong><? echo __('Change Password');?></strong>
                                        <p class="muted"><? echo __('You can change your account password from here');?></p>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                               <div class="form-group">
                                                   <label for="fullname"><? echo __('Fullname');?></label>
                                                   <? echo $form->render('fullname',array('class' => 'form-control input-sm')); ?>
                                               </div>

                                                <div class="form-group">
                                                    <label for="username"><? echo __('Username');?></label>
                                                    <? echo $form->render('username',array('class' => 'form-control input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-md-12"><? echo __('New Password');?> *</label>
                                                    <div class="input-group input-group-sm" id="input-group-password">
                                                        <? echo $form->render("password",array('class' => 'form-control')) ?>
                                                        <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                                                        <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-12"><? echo __('Confirm Password');?> *</label>
                                                    <input id="confirm-password" name="confirm-password" type="password" class="form-control input-sm">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-3">
                                        <strong><? echo __('Contact details');?></strong>
                                        <p class="muted"><? echo __('You can change contact informations from here');?></p>
                                    </div>

                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="telephone"><? echo __('Phone');?></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                        <? echo $form->render('telephone',array('class' => 'form-control')); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email_addr"><? echo __('E-Mail');?></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                                        <? echo $form->render('email_addr',array('class' => 'form-control')); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="fax"><? echo __('Fax');?></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon"><i class="fa fa-fax"></i></span>
                                                        <? echo $form->render('fax',array('class' => 'form-control')); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="company"><? echo __('Company');?></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon"><i class="fa fa-tags"></i></span>
                                                        <? echo $form->render('company',array('class' => 'form-control')); ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="inputAddress"><? echo __('Address');?></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon"><i class="fa fa-university"></i></span>
                                                        <? echo $form->render('address',array('class' => 'form-control')); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="city"><? echo __('City');?></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                                        <? echo $form->render('city',array('class' => 'form-control')); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="region"><? echo __('Region');?></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon"><i class="fa fa-road"></i></span>
                                                        <? echo $form->render('region',array('class' => 'form-control')); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="country"><? echo __('Country');?></label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                                                        <? echo $form->render('country',array('class' => 'form-control')); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="limits" class="tab-pane">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <td width="40%"><? echo __('Source Management'); ?></td>
                                            <td width="40%"><? echo __('Limits'); ?></td>
                                            <td width="20%"><input type="checkbox" name="select-all-limits" checked="checked" class="checkbox-aero"/> <? echo __('Select All'); ?></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <? $data['template_limits'] = array(
                                            'clients' => array('check' => 'clients_checks','display_name' => __('Client Limit')),
                                            'domain' => array('check' => 'domain_checks','display_name' => __('Domain Limit')),
                                            'disk' => array('check' => 'disk_checks','display_name' => __('Disk Limit')),
                                            'traffic' => array('check' => 'traffic_checks','display_name' => __('Traffic Limit')),
                                            'subdomain' => array('check' => 'subdomain_checks','display_name' => __('Subdomain Limit')),
                                            'domain_alias' => array('check' => 'domain_alias_checks','display_name' => __('Domain Alias Limit')),
                                            'email_box' => array('check' => 'email_box_checks','display_name' => __('Email Box Limit')),
                                            'email_box_size' => array('check' => 'email_box_size_checks','display_name' => __('Email Box Size Limit')),
//                                            'email_forward' => array('check' => 'email_forward_checks','display_name' => __('Email Forward Limit')),
//                                            'email_groups' => array('check' => 'email_groups_checks','display_name' => __('Email Group Limit')),
//                                            'email_autoresponder' => array('check' => 'email_autoresponder_checks','display_name' => __('Email Auto Responder Limit')),
//                                            'email_list' => array('check' => 'email_list_checks','display_name' => __('Email List Limit')),
//                                                'postgresql' => array('check' => 'postgresql_checks','display_name' => _('_PostgreSQL Limit_')),
//                                                'mssql' => array('check' => 'mssql_checks','display_name' => _('_MSSQL Limit_')),
//                                                'mysql' => array('check' => 'mysql_checks','display_name' => _('_MySQL Limit_')),
                                            'database' => array('check' => 'database_checks','display_name' => __('Database Limit')),
                                            'database_size' => array('check' => 'database_size_checks','display_name' => __('Database Size Limit')),
                                            'template' => array('check' => 'template_checks','display_name' => __('Template Limit')),
                                        );
                                        if ($user_edit_data['type'] > "2") {
                                            unset($data['template_limits']['clients']);
                                        }
                                        if ($user_edit_data['type'] > "3") {
                                            unset($data['template_limits']['template']);
                                        }
                                        foreach ($data['template_limits'] as $key => $value) { ?>
                                            <tr>
                                                <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                                <td><? echo $form->render($key,array('class' => 'form-control input-sm limits')); ?></td>
                                                <td><label><? echo $form->render($value['check'],array('class' => 'checkbox-blue')); ?> <? echo __('Unlimited'); ?></label></td>
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
                                            <td width="20%"><input type="checkbox" name="select-all-perms" checked="checked" class="checkbox-aero"/> <? echo __('Select All'); ?></td>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <? $data['template_permissions'] = array(
                                            'access_panel' => array('display_name' => __('Access Panel')),
                                            'access_client' => array('display_name' => __('Access Client Management')),
                                            'access_domain' => array('display_name' => __('Access Domain Management')),
                                            'access_template' => array('display_name' => __('Access Template Management')),
                                            'access_hosting_settings' => array('display_name' => __('Access Hosting Settings Management')),
                                            'access_subdomain' => array('display_name' => __('Access Subdomain Management')),
                                            'access_database' => array('display_name' => __('Access Database Management')),
                                            'access_domain_alias' => array('display_name' => __('Access Domain Alias Management')),
                                            'access_ftp_user' => array('display_name' => __('Access FTP User Management')),
                                            'access_email' => array('display_name' => __('Access Email Management')),
                                            'access_email_alias' => array('display_name' => __('Access Email Alias Management')),
//                                        'access_mailing_lists' => array('display_name' => __('Access Mailing List Management')),
//                                        'access_spam_filters' => array('display_name' => __('Access Spam Filters Management')),
                                            'access_task' => array('display_name' => __('Access Scheduled Task Management')),
                                            'access_dns' => array('display_name' => __('Access DNS Management')),
                                            'access_ssl' => array('display_name' => __('Access SSL Management')),
                                            'access_statistics' => array('display_name' => __('Access Statistics Management')),
                                            'access_backup_to_server' => array('display_name' => __('Access Backup (Server) Management')),
                                            'access_backup_to_ftp' => array('display_name' => __('Access Backup (FTP) Management')),
                                            'access_protect_dir' => array('display_name' => __('Access Protect Directory Management')),
                                        );
                                        if($user_edit_data['type'] > "2") {
                                            unset($data['template_permissions']['access_client']);
                                        }
                                        if($user_edit_data['type'] > "3") {
                                            unset($data['template_permissions']['access_template']);
                                        }
                                        foreach ($data['template_permissions'] as $key => $value) {
                                            ?>
                                            <tr>
                                                <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                                <td><label><? echo $form->render($key,array("class" => 'checkbox-blue')); ?> <? echo __('Allow'); ?></label></td>
                                            </tr>
                                        <? } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="hosting_settings" class="tab-pane">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <td width="40%"><? echo __('Settings'); ?></td>
                                            <td width="40%"><? echo __('Options'); ?></td>
                                        </tr>
                                        </thead>
                                        <tbody>


                                        <? $data = array(
                                            'hosting_settings' => array('display_name' => __('Hosting Type'), 'type' => 'selectbox'),
                                            'php_support' => array('display_name' => __('PHP Support'), 'type' => 'checkbox'),
//                                    'php_safe_mode' => array('display_name' => __('PHP Safe Mode'), 'type' => 'checkbox'),
                                            'php_version' => array('display_name' => __('PHP Version'), 'type' => 'selectbox'),
                                            'asp_support' => array('display_name' => __('ASP Support'), 'type' => 'checkbox'),
                                            'asp_net_support' => array('display_name' => __('ASP.NET Support'), 'type' => 'checkbox'),
                                            'asp_net_version' => array('display_name' => __('ASP.NET Version'), 'type' => 'selectbox'),
                                            'cgi_support' => array('display_name' => __('CGI Support'), 'type' => 'checkbox'),
                                            'perl_support' => array('display_name' => __('Perl Support'), 'type' => 'checkbox'),
                                            'python_support' => array('display_name' => __('Python Support'), 'type' => 'checkbox'),
                                            'web_statistics_support' => array('display_name' => __('Web Statistics Support'), 'type' => 'checkbox'),
                                            'web_statistics_type' => array('display_name' => __('Web Statistics Type'), 'type' => 'selectbox'),
                                            'custom_error_pages' => array('display_name' => __('Custom Error Page Support'), 'type' => 'checkbox'),
                                            'dedicated_pool' => array('display_name' => __('Dedicated Pool'), 'type' => 'checkbox'),
                                            'max_cpu_usage' => array('display_name' => __('Max CPU Usage'), 'type' => 'text')
                                        );
                                        foreach ($data as $key => $value) {
                                            if(PANEL_OS_TYPE == "linux") {
                                                if(($key == "asp_support") || ($key == "asp_net_support") || ($key == "asp_net_version") || ($key == "php_version") || ($key == "dedicated_pool") || ($key == "max_cpu_usage"))
                                                    continue;
                                            } ?>

                                            <tr>
                                                <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                                <? if($value['type'] == "checkbox")  { ?>
                                                    <td><label><? echo $form->render($key,array("class" => 'checkbox-blue')); ?> <? echo __('Allow'); ?></label></td>
                                                <? } else { ?>
                                                    <td><? echo $form->render($key,array('class' => 'form-control input-sm')); ?></td>
                                                <? } ?>
                                            </tr>

                                        <? } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="ip_address" class="tab-pane">
                                <div class="form-group">
                                    <label class="col-md-12"><? echo __('IP Address');?></label>
                                    <? echo $form->render('ipaddress_list',array('class' => 'multiselect')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo __('About User');?></div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <ul class="list-group clear-list">
                            <li class="list-item fist-item">
                                <span class="pull-right"> <? echo $user_edit_data['fullname'];?> </span>
                                <? echo __('Fullname');?>
                            </li>
                            <li class="list-item">
                                <span class="pull-right"> <? echo ($user_edit_data['type'] == "2" ? __('Reseller') : __('Client'));?> </span>
                                <? echo __('User Type');?>
                            </li>
                            <li class="list-item">
                                <span class="pull-right"> <? echo $this->env->convert($user_edit_data['disk']);?> </span>
                                <? echo __('Disk Used');?>
                            </li>
                            <li class="list-item">
                                <span class="pull-right"> <? echo $this->env->convert($user_edit_data['traffic']);?> </span>
                                <? echo __('Traffic Used');?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
            <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
        </div>
    </form>
</div>
<script>
    core.setWidth({
        target: 'user-edit-panel'
    });

    core.inputInits();
    core.systemInit();
</script>
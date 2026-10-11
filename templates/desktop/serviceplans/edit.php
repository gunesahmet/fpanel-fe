<div class="row" id="serviceplans-edit-panel" data-width="70%" data-height="50%">
    <form method="post" autocomplete="off" action="<? echo $post_url;?>" id="serviceplan-edit">
        <div class="row">
            <div class="col-md-7">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Edit Serviceplan');/*Servis Planı Düzenleme*/?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <? echo $form->render('serviceplan_name',array('class' => 'input form-control','placeholder' => __('Serviceplan Name'))); ?>
                                </div>
                                <div class="col-md-3"><label><? echo $form->render('overuse',array('class' => 'checkbox-blue')); ?> <? echo __('Overuse'); ?></label></div>
                                <div class="col-md-3"><label><? echo $form->render('is_default',array('class' => 'checkbox-blue')); ?> <? echo __('Default?'); ?></label></div>
                            </div>
                        </div>
                        <br />
                        <hr />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs nav-justified">
                                        <li class="active"><a href="#serviceplans-tab-limits" data-toggle="tab"><? echo __('Limits'); /*Limitler*/?></a></li>
                                        <li><a href="#serviceplans-tab-hosting-settings" data-toggle="tab"><? echo __('Hosting Settings');/*Hosting Ayarları*/?></a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="serviceplans-tab-limits" class="tab-pane active">
                                            <div class="">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th><? echo __('Source Management'); ?></th>
                                                            <th><? echo __('Limits'); ?></th>
                                                            <th> <input type="checkbox" name="select-all-limits" class="checkbox-aero" checked"/> <? echo __('Select All'); ?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <? $data['template_limits'] = array(
                                                            'clients' => array('check' => 'clients_checks','display_name' => __('Client Limit')),
                                                            'domain' => array('check' => 'domain_checks','display_name' => __('Domain Limit')),
                                                            'disk' => array('check' => 'disk_checks','display_name' => __('Disk Limit') . ' ' .'(MB)'),
                                                            'traffic' => array('check' => 'traffic_checks','display_name' => __('Traffic Limit') . ' ' .'(MB)'),
                                                            'subdomain' => array('check' => 'subdomain_checks','display_name' => __('Subdomain Limit')),
                                                            'domain_alias' => array('check' => 'domain_alias_checks','display_name' => __('Domain Alias Limit')),
                                                            'email_box' => array('check' => 'email_box_checks','display_name' => __('Email Box Limit')),
                                                            'email_box_size' => array('check' => 'email_box_size_checks','display_name' => __('Email Box Size Limit') . ' ' .'(MB)'),
                                                            //                                                'email_forward' => array('check' => 'email_forward_checks','display_name' => __('Email Forward Limit')),
                                                            //                                                'email_groups' => array('check' => 'email_groups_checks','display_name' => __('Email Group Limit')),
                                                            //                                                'email_autoresponder' => array('check' => 'email_autoresponder_checks','display_name' => __('Email Auto Responder Limit')),
                                                            //                                                'email_list' => array('check' => 'email_list_checks','display_name' => __('Email List Limit')),
                                                            'database' => array('check' => 'database_checks','display_name' => __('Database Limit')),
                                                            'database_size' => array('check' => 'database_size_checks','display_name' => __('Database Size Limit') . ' ' .'(MB)'),
                                                            'template' => array('check' => 'template_checks','display_name' => __('Template Limit')),
                                                            'expire_time_limit' => array('check' => 'expire_time_limit_checks','display_name' => __('Expire Time Limit') . ' ' .'(Year)')
                                                        );
                                                        if(isset($client_type)) {
                                                            if($client_type > 1) {
                                                                unset($data['template_limits']['clients']);
                                                            }
                                                            if($client_type > 2) {
                                                                unset($data['template_limits']['domain']);
                                                                unset($data['template_limits']['template']);
                                                            }
                                                        }
                                                        else {
                                                            if ($user_type > 1) {
                                                                unset($data['template_limits']['clients']);
                                                            }
                                                            if ($user_type > 2) {
                                                                unset($data['template_limits']['domain']);
                                                                unset($data['template_limits']['template']);
                                                            }
                                                        }


                                                        foreach ($data['template_limits'] as $key => $value) { ?>
                                                            <tr>
                                                                <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label> </td>
                                                                <td><? echo $form->render($key,array('class' => 'form-control input-sm limits')); ?></td>
                                                                <td><label><? echo $form->render($value['check'],array('class' => 'checkbox-blue')); ?> <? echo __('Unlimited'); ?></label></td>
                                                            </tr>
                                                        <? } ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="serviceplans-tab-hosting-settings" class="tab-pane">
                                            <div class="">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <tbody>
                                                        <? $data = array(
                                                            'hosting_settings' => array('display_name' => __('Hosting Type'), 'type' => 'selectbox'),
                                                            'php_support' => array('display_name' => __('PHP Support'), 'type' => 'checkbox'),
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
                                                            'max_cpu_usage' => array('display_name' => __('Max CPU Usage') . ' ' .'('. __('Percent') .')', 'type' => 'text')
                                                        );
                                                        foreach ($data as $key => $value) {
                                                            if(PANEL_OS_TYPE == "linux") {
                                                                if(($key == "asp_support") || ($key == "asp_net_support") || ($key == "asp_net_version") || ($key == "php_version") || ($key == "dedicated_pool") || ($key == "max_cpu_usage"))
                                                                    continue;
                                                            } ?>
                                                            <tr>
                                                                <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                                                <? if($value['type'] == "checkbox")  { ?>
                                                                    <td><label><? echo $form->render($key,array('class' => 'checkbox-blue')); ?> <? echo __('Allow'); ?></label></td>
                                                                <? } else { ?>
                                                                    <td><? echo $form->render($key,array('class' => 'form-control input-sm')); ?></td>
                                                                <? } ?>
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
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Permissions');/*İzinler*/?></div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div id="contact-1" class="tab-pane active">
                                <div class="">
                                    <div class="">
                                        <hr/>
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th><? echo __('Source Management'); ?></th>
                                                <th> <input type="checkbox" name="select-all-perms" class="checkbox-aero" checked"/> <? echo __('Select All'); ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            <?
                                            $data['template_permissions'] = array(
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
                                                //                                            'access_mailing_lists' => array('display_name' => __('Access Mailing List Management')),
                                                //                                            'access_spam_filters' => array('display_name' => __('Access Spam Filters Management')),
                                                'access_task' => array('display_name' => __('Access Scheduled Task Management')),
                                                'access_dns' => array('display_name' => __('Access DNS Management')),
                                                'access_ssl' => array('display_name' => __('Access SSL Management')),
                                                'access_statistics' => array('display_name' => __('Access Statistics Management')),
                                                'access_backup_to_server' => array('display_name' => __('Access Backup (Server) Management')),
                                                'access_backup_to_ftp' => array('display_name' => __('Access Backup (FTP) Management')),
                                                'access_protect_dir' => array('display_name' => __('Access Protect Directory Management')),
                                            );
                                            if(isset($client_type)) {
                                                if($client_type > 1) {
                                                    unset($data['template_permissions']['access_client']);
                                                }
                                                if($client_type > 2) {
                                                    unset($data['template_permissions']['access_domain']);
                                                    unset($data['template_permissions']['access_template']);
                                                }
                                            }
                                            else {
                                                if ($user_type > 1) {
                                                    unset($data['template_permissions']['access_client']);
                                                }
                                                if ($user_type > 2) {
                                                    unset($data['template_permissions']['access_domain']);
                                                    unset($data['template_permissions']['access_template']);
                                                }
                                            }


                                            foreach ($data['template_permissions'] as $key => $value) { ?>
                                                <tr>
                                                    <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                                    <td class="checkbox-list"><? echo $form->render($key,array('class' => 'checkbox-blue')); ?> <? echo __('Allow'); ?></td>
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
            <div class="col-md-1">
                <button class="btn btn-sm btn-default btn-block" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-sm btn-primary btn-block" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </form>
</div>


<script>
    core.setWidth({
        target: 'serviceplans-edit-panel'
    });
    core.inputInits();
    core.clearUnlimited();
    core.systemInit();
</script>

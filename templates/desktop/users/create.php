<div class="row" id="user-create-panel" data-width="70%" data-height="50%">
    <form id="create-user-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="col-lg-5">
            <div class="panel panel-default" id="user_features">
                <div class="panel-heading"><? echo __('User Features');/*Kullanıcı Bilgileri*/?></div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <h2>
                            <? echo __('Create New User');?>
                        </h2>
                        <p>
                            <? echo __('Do not use prefix in domain name such as www, http://');?> <br/>
                            <? echo __('This form create for domain web hosting.');?>
                        </p>
                        <div class="form-group">
                            <label class="col-md-6" for="overuse"><? echo __('User must change password after login');?></label>
                            <? echo $form->render('change_password_after_login',array('class' => 'checkbox-blue')); ?>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6" for="overuse"><? echo __('Overuse');?></label>
                            <? echo $form->render('overuse',array('class' => 'checkbox-blue')); ?>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12"><? echo __('Fullname');?> *</label>
                            <div class="col-md-12"><? echo $form->render('fullname',array('class' => 'form-control input-sm')); ?></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12"><? echo __('Username');?> *</label>
                            <div class="col-md-12"><? echo $form->render('username',array('class' => 'form-control input-sm')); ?></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12"><? echo __('Password');?> *</label>
                            <div class="col-md-12">
                                <div class="input-group input-group-sm" id="input-group-password">
                                    <? echo $form->render("password",array('class' => 'form-control')) ?>
                                    <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                                    <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12"><? echo __('Confirm Password');?> *</label>
                            <div class="col-md-12"><input id="confirm-password" name="confirm-password" type="password" class="form-control input-sm"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12"><? echo __('Email Address');?> *</label>
                            <div class="col-md-12"><? echo $form->render('email_addr',array('class' => 'form-control input-sm')); ?></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12"><? echo __('Select a Service Plan');?></label>
                            <div class="col-md-12"><? echo $form->render('template_select',array('class' => 'form-control input-sm','data-url' => $this->uri->url_encrypt("serviceplans/get"))); ?></div>
                        </div>

                        <? if (($user_type < '2') && (!isset($client_type))){ ?>
                            <div class="form-group">
                                <label class="col-md-12"><? echo __('User Type');?></label>
                                <div class="col-md-12"><? echo $form->render('type_of_user',array('class' => 'form-control input-sm')); ?></div>
                            </div>
                        <? } ?>
                        <div class="form-group">
                            <label class="col-md-12"><? echo __('IP Address');?></label>
                            <? echo $form->render('ipaddress_list',array('class' => 'multiselect')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default" id="user_features">
                <div class="panel-heading"><? echo __('Settings');/*Ayarlar*/?></div>
                <div class="panel-body">
                   <div class="col-md-12">
                       <h2>
                           <? echo __('User Hosting & Panel Features');?>
                       </h2>
                       <p>
                           <? echo __('Choose user settings from here');?>
                       </p>
                       <div class="tabbable-line">
                           <ul class="nav nav-tabs nav-justified" id="user-create-tabs">
                               <li class="active"><a href="#user-tab-limits" data-toggle="tab"><? echo __('Limits'); /*Limitler*/?></a></li>
                               <li><a href="#user-tab-permissions" data-toggle="tab"><? echo __('Permissions');/*İzinler*/?></a></li>
                               <li><a href="#user-tab-hosting-settings" data-toggle="tab"><? echo __('Hosting Settings');/*Hosting Ayarları*/?></a></li>
                           </ul>
                           <div class="tab-content">
                               <div id="user-tab-limits" class="tab-pane fade in active">
                                   <table class="table table-striped table-hover">
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
                                           'expire_time_limit' => array('check' => 'expire_time_limit_checks','display_name' => __('Expire Time Limit'))
                                       );
                                       if(isset($client_id)) {
                                           unset($data['template_limits']['clients']);

                                           if($client_type > "2") {
                                               unset($data['template_limits']['domain']);
                                               unset($data['template_limits']['template']);
                                           }
                                       }
                                       if ($user_type > "1") {
                                           unset($data['template_limits']['clients']);
                                           if ($user_type > "2") {
                                               unset($data['template_limits']['domain']);
                                               unset($data['template_limits']['template']);
                                           }
                                       }

                                       foreach ($data['template_limits'] as $key => $value) { ?>
                                           <tr>
                                               <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label> </td>
                                               <td><? echo $form->render($key,array('class' => 'limits form-control input-sm')); ?></td>
                                               <td><label class="col-md-12"><? echo $form->render($value['check'],array('class' => 'checkbox-blue')); ?> <? echo __('Unlimited'); ?></label></td>
                                           </tr>
                                       <? } ?>

                                       </tbody>
                                   </table>
                               </div>
                               <div id="user-tab-permissions" class="tab-pane fade">
                                   <table class="table table-striped table-hover">
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
                                       if(isset($client_id)) {
                                           unset($data['template_permissions']['access_client']);
                                           if($client_type > "2") {
                                               unset($data['template_permissions']['access_domain']);
                                           }
                                       }
                                       if ($user_type > "0") {
                                           unset($data['template_permissions']['access_client']);
                                           if($user_type > '2') {
                                               unset($data['template_permissions']['access_domain']);
                                           }
                                       }

                                       foreach ($data['template_permissions'] as $key => $value) {
                                           ?>
                                           <tr>
                                               <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                               <td class="checkbox-list"><label class="col-md-12"><? echo $form->render($key,array('class' => 'checkbox-blue')); ?> <? echo __('Allow'); ?></label></td>
                                           </tr>

                                       <? } ?>
                                       </tbody>
                                   </table>
                               </div>
                               <div id="user-tab-hosting-settings" class="tab-pane fade">
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
                                           'max_cpu_usage' => array('display_name' => __('Max CPU Usage'), 'type' => 'text')
                                       );
                                       foreach ($data as $key => $value) {
                                           if(PANEL_OS_TYPE == "linux") {
                                               if(($key == "asp_support") || ($key == "asp_net_support") || ($key == "asp_net_version") || ($key == "_version") || ($key == "dedicated_pool") || ($key == "max_cpu_usage"))
                                                   continue;
                                           } ?>
                                           <tr>
                                               <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                               <? if($value['type'] == "checkbox")  { ?>
                                                   <td><label class="col-md-12"><? echo $form->render($key,array('class' => 'checkbox-blue')); ?> <? echo __('Allow'); ?></label></td>
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
        <div class="col-md-1">
            <button class="btn btn-sm btn-block btn-default" type="reset"><? echo __('Cancel'); ?></button>
            <button class="btn btn-sm btn-block btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
        </div>
    </form>
</div>
<script>
    core.setWidth({
        target: 'user-create-panel'
    });

    core.inputInits();
    core.systemInit();
    core.changeServicePlan();
</script>
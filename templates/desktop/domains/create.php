<div class="row" id="domain-create-panel" data-width="70%" data-height="50%">
    <form id="create-domain-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="row">
            <div class="col-md-5">
                <div class="panel panel-default" id="domain_features">
                    <div class="panel-heading"><? echo __('Domain Features');/*Domain Bilgileri*/?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>
                                    <? echo __('Create New Domain');?>
                                </h2>
                                <p>
                                    <? echo __('Do not use prefix in domain name such as www, http://');?> <br/>
                                    <? echo __('This form create for domain web hosting.');?>
                                </p>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4"><? echo __('DNS Service'); ?></div>
                                        <div class="col-md-4"><? echo __('Mail Service'); ?></div>
                                        <div class="col-md-4"><? echo __('Overuse'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"><? echo $form->render('enable_dns',array('class' => 'checkbox-blue')); ?></div>
                                        <div class="col-md-4"><? echo $form->render('enable_mail',array('class' => 'checkbox-blue')); ?></div>
                                        <div class="col-md-4"><? echo $form->render('overuse',array('class' => 'checkbox-blue')); ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><? echo __('Domain Name');?> *</label>
                                    <div class="input-group input-group-sm" id="input-group-domain_name">
                                        <span class="input-group-addon">www.</span>
                                        <? echo $form->render('domain_name',array('class' => 'form-control')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><? echo __('IP Address');?> *</label>
                                    <? echo $form->render('ipaddress_list',array('class' => 'form-control input-sm')); ?>
                                </div>
                                <div class="form-group">
                                    <label><? echo __('FTP Username');?> *</label>
                                    <? echo $form->render('ftp_user',array('class' => 'form-control input-sm')); ?>
                                </div>
                                <div class="form-group">
                                    <label><? echo __('FTP and Panel Password');?> *</label>
                                    <div class="input-group input-group-sm" id="input-group-password">
                                        <? echo $form->render("password",array('class' => 'form-control')) ?>
                                        <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                                        <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><? echo __('Confirm Password');?> *</label>
                                    <input id="confirm-password" name="confirm-password" type="password" class="form-control input-sm">
                                </div>
                                <div class="form-group">
                                    <label><? echo __('Select a Service Plan');?></label>
                                    <? echo $form->render('template_select',array('class' => 'form-control input-sm','data-url' => $this->uri->url_encrypt("serviceplans/get"))); ?>
                                </div>
                                <? if($user_type < "3") {
                                    if(isset($client_type)) {
                                        if($client_type < 3) { ?>
                                            <div class="form-group">
                                                <label><? echo __('Assign Other User');?></label>
                                                <? echo $form->render('user_select',array('class' => 'form-control input-sm')); ?>
                                            </div>
                                        <? } ?>
                                    <? } ?>
                                    <div class="form-group">
                                        <label><? echo __('Assign Other User');?></label>
                                        <? echo $form->render('user_select',array('class' => 'form-control input-sm')); ?>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default" id="domain_settings">
                    <div class="panel-heading"><? echo __('Settings');/*Ayarlar*/?></div>
                    <div class="panel-body">
                        <h2><? echo __('Domain Hosting Settings');?></h2>
                        <p><? echo __('You can choose domain settings from here');?><br />
                            <? if($server_type == "cloud") echo '<span class="label label-danger">'. __('Important') .':</span> ' .__('If you select a linux server some domain features (ASP.NET and version, PHP version etc.) are will be disable after create.');?></p>
                        <div class="tabbable-line">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="active"><a href="#tab-limits" data-toggle="tab"><? echo __('Limits'); /*Limitler*/?></a></li>
                                <li><a href="#tab-permissions" data-toggle="tab"><? echo __('Permissions');/*İzinler*/?></a></li>
                                <li><a href="#tab-hosting-settings" data-toggle="tab"><? echo __('Hosting Settings');/*Hosting Ayarları*/?></a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-limits" class="tab-pane fade in active">
                                    <table class="table table-striped table-hover">
                                        <tbody>
                                        <? $data['template_limits'] = array(
                                            'disk' => array('check' => 'disk_checks','display_name' => __('Disk Limit') . ' ' .'(MB)'),
                                            'traffic' => array('check' => 'traffic_checks','display_name' => __('Traffic Limit') . ' ' .'(MB)'),
                                            'subdomain' => array('check' => 'subdomain_checks','display_name' => __('Subdomain Limit')),
                                            'domain_alias' => array('check' => 'domain_alias_checks','display_name' => __('Domain Alias Limit')),
                                            'email_box' => array('check' => 'email_box_checks','display_name' => __('Email Box Limit')),
                                            'email_box_size' => array('check' => 'email_box_size_checks','display_name' => __('Email Box Size Limit') . ' ' .'(MB)'),
        //                                        'email_forward' => array('check' => 'email_forward_checks','display_name' => __('Email Forward Limit')),
        //                                        'email_groups' => array('check' => 'email_groups_checks','display_name' => __('Email Group Limit')),
        //                                        'email_autoresponder' => array('check' => 'email_autoresponder_checks','display_name' => __('Email Auto Responder Limit')),
        //                                        'email_list' => array('check' => 'email_list_checks','display_name' => __('Email List Limit')),
                                            'database' => array('check' => 'database_checks','display_name' => __('Database Limit')),
                                            'database_size' => array('check' => 'database_size_checks','display_name' => __('Database Size Limit') . ' ' .'(MB)'),
                                            'expire_time_limit' => array('check' => 'expire_time_limit_checks','display_name' => __('Expire Time Limit'))
                                        );

                                        foreach ($data['template_limits'] as $key => $value) { ?>
                                            <tr>
                                                <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label> </td>
                                                <td><? echo $form->render($key,array('class' => 'limits form-control input-sm')); ?></td>
                                                <td><label><? echo $form->render($value['check'],array('class' => 'checkbox-blue')); ?> <? echo __('Unlimited'); ?></label></td>
                                            </tr>
                                        <? } ?>

                                        </tbody>
                                    </table>
                                </div>
                                <div id="tab-permissions" class="tab-pane fade">
                                    <table class="table table-striped table-hover">
                                        <tbody>

                                        <?
                                        $data['template_permissions'] = array(
                                            'access_panel' => array('display_name' => __('Access Panel')),
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


                                        foreach ($data['template_permissions'] as $key => $value) {
                                            ?>
                                            <tr>
                                                <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                                <td class="checkbox-list"><label><? echo $form->render($key,array('class' => 'checkbox-blue')); ?> <? echo __('Allow'); ?></label></td>
                                            </tr>

                                        <? } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="tab-hosting-settings" class="tab-pane fade">
                                    <table class="table table-striped table-hover">
                                        <tbody>

                                        <? $data = array(
                                            'hosting_settings' => array('display_name' => __('Hosting Type')/*Hosting Türü*/, 'type' => 'selectbox'),
                                            'php_support' => array('display_name' => __('PHP Support')/*PHP Desteği*/, 'type' => 'checkbox'),
                                            'cgi_support' => array('display_name' => __('CGI Support')/*CGI Desteği*/, 'type' => 'checkbox'),
                                            'perl_support' => array('display_name' => __('Perl Support')/*PERL Desteği*/, 'type' => 'checkbox'),
                                            'python_support' => array('display_name' => __('Python Support')/*Python Desteği*/, 'type' => 'checkbox'),
                                            'web_statistics_support' => array('display_name' => __('Web Statistics Support')/*Web İstatistik Desteği*/, 'type' => 'checkbox'),
                                            'web_statistics_type' => array('display_name' => __('Web Statistics Type')/*Web İstatistik Türü*/, 'type' => 'selectbox'),
                                            'custom_error_pages' => array('display_name' => __('Custom Error Page Support')/*Özel Hata Sayfası Desteği*/, 'type' => 'checkbox'),
                                        );
                                        if(($server_type == "standalone") && PANEL_OS_TYPE == "windows") {
                                            $data['asp_support'] = array('display_name' => __('ASP Support')/*ASP Desteği*/, 'type' => 'checkbox');
                                            $data['asp_net_support'] = array('display_name' => __('ASP.NET Support')/*ASP.NET Desteği*/, 'type' => 'checkbox');
                                            $data['asp_net_version'] = array('display_name' => __('ASP.NET Version')/*ASP.NET Versiyon*/, 'type' => 'selectbox');
                                            $data['php_version'] = array('display_name' => __('PHP Version')/*PHP Versiyon*/, 'type' => 'selectbox');
                                            $data['dedicated_pool'] = array('display_name' => __('Dedicated Pool')/*Dedicated Pool*/, 'type' => 'checkbox');
                                            $data['max_cpu_usage'] = array('display_name' => __('Max CPU Usage')/*Max. CPU Kullanımı*/, 'type' => 'text');
                                        }

                                        foreach ($data as $key => $value) { ?>
                                            <tr>
                                                <td><label for="<? echo $key;?>"><? echo $value['display_name']; ?></label></td>
                                                <? if($value['type'] == "checkbox")  { ?>
                                                    <td><label><? echo $form->render($key,array('class' => 'checkbox-blue')); ?> <? echo __('Allow');/*İzinli*/ ?></label></td>
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
            <div class="col-md-1">
                <a class="btn btn-default btn-sm btn-block" type="reset"><i class="fa fa-times"></i> <? echo __('Cancel');/*İptal*/ ?></a>
                <button class="btn btn-primary btn-sm btn-block" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Submit')/*Tamam*/; ?></button>
            </div>
        </div>
    </form>
</div>
<script>
    core.setWidth({
        target: 'domain-create-panel'
    });
    core.inputInits();
    core.systemInit();
    core.changeServicePlan();
</script>
<div class="row" id="domain-create-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <form id="create-domain-form" method="post" class="form-horizontal row" action="<?= $post_url;?>">
            <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
            <div class="col-md-5">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= __('Domain Features')?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <p>
                            <?= __('Do not use prefix in domain name such as www, http:// etc...');?> <br />
                            <?= __('This form create a domain for web hosting.');?> <br />
                            <? if ($system_type == CLOUD) { ?>
                            <?= __('After creating with these hosting settings, domain services will be created on the most suitable servers.');?>
                            <? } ?>
                        </p>
                        <div class="form-group row">
                            <div class="col-md-4"></div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-3"><label for="enable_dns"><?= __('DNS Service'); ?></label></div>
                                    <div class="col-md-3"><label for="enable_mail"><?= __('Mail Service'); ?></label></div>
                                    <div class="col-md-3"><label for="overuse"><?= __('Overuse'); ?></label></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3"><?= $form->render('enable_dns',array('class' => 'checkbox-blue')); ?></div>
                                    <div class="col-md-3"><?= $form->render('enable_mail',array('class' => 'checkbox-blue')); ?></div>
                                    <div class="col-md-3"><?= $form->render('overuse',array('class' => 'checkbox-blue')); ?></div>
                                </div>
                            </div>
                        </div>
                        <? if ($system_type == CLOUD) { ?>
                            <div class="form-group row">
                                <div class="col-md-4"><label for="manual_select_server"><?= __('Manual Select Server'); ?></label></div>
                                <div class="col-md-8">
                                    <?= $form->render('manual_select_server',array('class' => 'checkbox-blue', "data-sub-elements" => "dns_servers,web_servers,mail_servers")); ?>
                                    <b class="text-danger"> <?= __('Domain name will be created on random servers. If you want to set it manually, tick here.');?></b>
                                </div>
                            </div>
                        <? } ?>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label><?= __('Domain Name');?> *</label>

                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm" id="input-group-domain_name">
                                    <span class="input-group-addon">www.</span>
                                    <?= $form->render('domain_name',array('class' => 'form-control form-control-sm', "autofocus"=> "")); ?>
                                </div>
                            </div>
                        </div>
                        <? if ($system_type == CLOUD) { ?>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label><?= __('DNS Server');?> *</label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('dns_servers',array('class' => 'form-control form-control-sm input-sm select-picker', "data-live-search" => "true")); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label><?= __('Web Server');?> *</label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('web_servers',array('class' => 'form-control form-control-sm input-sm select-picker', "data-live-search" => "true")); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label><?= __('Mail Server');?> *</label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('mail_servers',array('class' => 'form-control form-control-sm input-sm select-picker', "data-live-search" => "true")); ?>
                                </div>
                            </div>
                        <? } else { ?>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label><?= __('IP Address');?> *</label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('ipaddress_list',array('class' => 'form-control form-control-sm input-sm select-picker', "data-live-search" => "true")); ?>
                                </div>
                            </div>
                        <? } ?>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label><?= __('FTP Username');?> *</label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('ftp_user',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label><?= __('FTP and Panel Password');?> *</label>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm" id="input-group-password">
                                    <?= $form->render("password",array('class' => 'form-control form-control-sm')) ?>
                                    <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                    <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label><?= __('Confirm Password');?> *</label>
                            </div>
                            <div class="col-md-8">
                                <input id="confirm-password" name="confirm-password" type="password" class="form-control form-control-sm input-sm">
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-md-4">
                                <label><?= __('Select a Service Plan');?></label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('template_select',array('class' => 'form-control form-control-sm input-sm select-picker', "data-live-search" => "true")); ?>
                            </div>
                        </div>

                        <? if($user_type < CLIENT_USER) {
                            if(isset($client_type)) {
                                if($client_type < CLIENT_USER) { ?>
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label><?= __('Assign Other User');?></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('user_select',array('class' => 'form-control form-control-sm input-sm select-picker', "data-live-search" => "true")); ?>
                                        </div>
                                    </div>

                                <? } ?>
                            <? } else { ?>
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label><?= __('Assign Other User');?></label>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $form->render('user_select',array('class' => 'form-control form-control-sm input-sm select-picker', "data-live-search" => "true")); ?>
                                    </div>
                                </div>
                            <? } ?>
                        <? } ?>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="float-right">
                                    <a class="btn btn-default" type="reset"><i class="fa fa-times"></i> <?= __('Cancel') ?></a>
                                    <button class="btn btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Submit'); ?></button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= __('Domain Hosting Settings');?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <p><?= __('You can choose domain settings from here');?><br />
                            <? if($system_type == STANDALONE) echo '<span class="label label-danger">'. __('Important') .':</span> ' .__('If you select a linux server some domain features (ASP.NET and version, PHP version etc.) are will be disable after create.');?></p>
                        <div class="tabbable-line create-tabs">
                            <ul class="nav nav-pills nav-fill" id="domain-create-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active" href="#tab-limits" data-toggle="tab"><?= __('Limits'); ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="#tab-permissions" data-toggle="tab"><?= __('Permissions');?></a></li>
                                <li class="nav-item"><a class="nav-link" href="#tab-hosting-settings" data-toggle="tab"><?= __('Hosting Settings');?></a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-limits" class="tab-pane fade show active" role="tabpanel">
                                            <? $data['template_limits'] = array(
                                                'disk' => array('check' => 'disk_checks','display_name' => __('Disk Limit') . ' ' .'(MB)'),
                                                'traffic' => array('check' => 'traffic_checks','display_name' => __('Traffic Limit') . ' ' .'(MB)'),
                                                'subdomain' => array('check' => 'subdomain_checks','display_name' => __('Subdomain Limit')),
                                                'domain_alias' => array('check' => 'domain_alias_checks','display_name' => __('Domain Alias Limit')),
                                                'email_box' => array('check' => 'email_box_checks','display_name' => __('Email Box Limit')),
                                                'email_box_size' => array('check' => 'email_box_size_checks','display_name' => __('Email Box Size Limit') . ' ' .'(MB)'),
                                                'database' => array('check' => 'database_checks','display_name' => __('Database Limit')),
                                                'database_size' => array('check' => 'database_size_checks','display_name' => __('Database Size Limit') . ' ' .'(MB)'),
                                                'expire_time_limit' => array('check' => 'expire_time_limit_checks','display_name' => __('Expire Time Limit'))
                                            );

                                            foreach ($data['template_limits'] as $key => $value) { ?>
                                            <div class="row ">
                                                <div class="col-lg-3 d-none d-sm-block">
                                                    <label for="<?= $key;?>"><?= $value['display_name']; ?></label>
                                                </div>
                                                <div class="col-lg-6 col-8">
                                                    <?= $form->render($key,array('class' => 'limits form-control form-control-sm input-sm', "placeholder" => $value['display_name'])); ?>
                                                </div>
                                                <div class="col-lg-3 col-4">
                                                    <?= $form->render($value['check'],array('class' => 'checkbox-blue')); ?> <?= __('Unlimited'); ?></label>
                                                </div>
                                            </div>

                                            <? } ?>

                                </div>
                                <div id="tab-permissions" class="tab-pane fade">
                                            <?
                                            $data['template_permissions'] = array(
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
                                            );


                                            foreach ($data['template_permissions'] as $key => $value) {
                                                ?>
                                                <div class="row ">
                                                    <div class="col-lg-4 col-8">
                                                        <label for="<?= $key;?>"><?= $value['display_name']; ?></label>
                                                    </div>
                                                    <div class="col-lg-8 col-4 checkbox-list">
                                                        <?= $form->render($key,array('class' => 'checkbox-blue', "data-id" => "permissions")); ?> <label><?= __('Allow'); ?></label>
                                                    </div>
                                                </div>
                                            <? } ?>
                                </div>
                                <div id="tab-hosting-settings" class="tab-pane fade">
                                            <? $data = array(
                                                'php_support' => array('display_name' => __('PHP Support'), 'type' => 'checkbox', "data-sub-elements" => "php_version", "server" => "all"),
                                                'php_version' => array('display_name' => __('PHP Version'), 'type' => 'selectbox', "server" => "all"),
                                                'asp_support' => array('display_name' => __('ASP Support'), 'type' => 'checkbox', "server" => "Windows"),
                                                'asp_net_core_support' => array('display_name' => __('ASP.NET CORE Support'), 'type' => 'checkbox', "server" => "Windows"),
                                                'asp_net_support' => array('display_name' => __('ASP.NET Support'), 'type' => 'checkbox', "data-sub-elements" => "asp_net_version", "server" => "Windows"),
                                                'asp_net_version' => array('display_name' => __('ASP.NET Version'), 'type' => 'selectbox', "server" => "Windows"),
                                                'application_pool_mode' => array('display_name' => __('Application Pool Mode'), 'type' => 'selectbox', "server" => "Windows"),
                                                'perl_support' => array('display_name' => __('Perl Support'), 'type' => 'checkbox', "server" => "all"),
                                                'python_support' => array('display_name' => __('Python Support'), 'type' => 'checkbox', "server" => "all"),
                                                'web_statistics_support' => array('display_name' => __('Web Statistics Support'), 'type' => 'checkbox', "data-sub-elements" => "web_statistics_type", "server" => "all"),
                                                'web_statistics_type' => array('display_name' => __('Web Statistics Type'), 'type' => 'selectbox', "server" => "all"),
                                                'dedicated_pool' => array('display_name' => __('Dedicated Pool'), 'type' => 'checkbox', "data-sub-elements" => "max_cpu_usage", "server" => "Windows"),
                                                'max_cpu_usage' => array('display_name' => __('Max CPU Usage') . ' ' .'('. __('Percent') .')', 'type' => 'text', "server" => "Windows"),
                                            );
                                            foreach ($data as $key => $value) {
                                                if(($system_type == STANDALONE) && (PANEL_OS_TYPE == LINUX)) {
                                                    if(($key == "asp_support") || ($key == "asp_net_support") || ($key == "asp_net_version") || ($key == "asp_net_core_support") || ($key == "php_version") || ($key == "dedicated_pool") || ($key == "max_cpu_usage") || ($key == "application_pool_mode"))
                                                        continue;
                                                } ?>
                                                <div class="row ">
                                                    <div class="col-lg-4 col-4">
                                                        <label for="<?= $key;?>"><?= $value['display_name']; ?><?= ($value["server"] != "all" ? " <b class='text-success'>(".$value["server"].")</b>" : "")?></label>
                                                    </div>
                                                    <div class="col-lg-8 col-8">
                                                    <? if($value['type'] == "checkbox")  { ?>
                                                            <? if(isset($value["data-sub-elements"])) { ?>
                                                            <label><?= $form->render($key,array('class' => 'checkbox-blue', "data-sub-elements" => $value["data-sub-elements"])); ?> <?= __('Allow'); ?></label>
                                                                    <? } else { ?>
                                                            <label><?= $form->render($key,array('class' => 'checkbox-blue')); ?> <?= __('Allow'); ?></label>
                                                        <? } ?>
                                                    <? } else if($value["type"] == "selectbox") { ?>
                                                        <?= $form->render($key, array('class' => 'form-control form-control-sm select-picker')); ?>
                                                    <? } else { ?>
                                                        <?= $form->render($key,array('class' => 'form-control form-control-sm')); ?>
                                                    <? } ?>
                                                    </div>
                                                </div>
                                            <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
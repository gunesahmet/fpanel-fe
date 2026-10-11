    <form id="create-user-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
        <div class="row" id="user-create-panel" data-width="70%" data-height="50%">
        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('User Features');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <h2>
                        <?= __('Create New User');?>
                    </h2>
                    <div class="form-group row">
                        <label class="col-md-4" for="change_password_after_login"><?= __('User must change password after login');?></label>
                        <div class="col-md-8">
                            <?= $form->render('change_password_after_login',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4" for="overuse"><?= __('Overuse');?></label>
                        <div class="col-md-8">
                            <?= $form->render('overuse',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4" for="fullname"><?= __('Fullname');?> *</label>
                        <div class="col-md-8"><?= $form->render('fullname',array('class' => 'form-control form-control-sm input-sm')); ?></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4" for="username"><?= __('Username');?> *</label>
                        <div class="col-md-8"><?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4" for="password"><?= __('Password');?> *</label>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" id="input-group-password">
                                <?= $form->render("password",array('class' => 'form-control form-control-sm')) ?>
                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fas fa-sync-alt"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4" for="confirm-password"><?= __('Confirm Password');?> *</label>
                        <div class="col-md-8"><input id="confirm-password" name="confirm-password" type="password" class="form-control form-control-sm input-sm"></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4" for="email_addr"><?= __('Email Address');?> *</label>
                        <div class="col-md-8"><?= $form->render('email_addr',array('class' => 'form-control form-control-sm input-sm')); ?></div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4"><?= __('Select a Service Plan');?></label>
                        <div class="col-md-8"><?= $form->render('template_select',array('class' => 'form-control form-control-sm select-picker','data-url' => $this->uri->url_encrypt("serviceplans/get"))); ?></div>
                    </div>

                    <? if (($user_type < '2') && (!isset($client_type))){ ?>
                        <div class="form-group row">
                            <label class="col-md-4" for="type_of_user"><?= __('User Type');?></label>
                            <div class="col-md-8"><?= $form->render('type_of_user',array('class' => 'form-control form-control-sm select-picker')); ?></div>
                        </div>
                    <? } ?>
                    <div class="form-group row">
                        <label class="col-md-4" for="ipaddress_list"><?= __('IP Address');?></label>
                       <div class="col-md-8">
                           <?= $form->render('ipaddress_list',array('class' => 'multiselect')); ?>
                       </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-default" type="reset"><?= __('Cancel'); ?></button>
                                <button class="btn btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Settings');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <h2>
                        <?= __('User Hosting & Panel Features');?>
                    </h2>
                    <p>
                        <?= __('Choose user settings from here');?>
                    </p>
                    <div class="tabbable-line create-tabs">
                        <ul class="nav nav-pills nav-fill" id="user-create-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" href="#user-tab-limits" data-toggle="tab"><?= __('Limits');?></a></li>
                            <li class="nav-item"><a class="nav-link" href="#user-tab-permissions" data-toggle="tab"><?= __('Permissions');?></a></li>
                            <li class="nav-item"><a class="nav-link" href="#user-tab-hosting-settings" data-toggle="tab"><?= __('Hosting Settings');?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="user-tab-limits" class="tab-pane fade show active" role="tabpanel">
                                        <? $data['template_limits'] = array(
                                            'clients' => array('check' => 'clients_checks','display_name' => __('Client Limit')),
                                            'domain' => array('check' => 'domain_checks','display_name' => __('Domain Limit')),
                                            'disk' => array('check' => 'disk_checks','display_name' => __('Disk Limit')),
                                            'traffic' => array('check' => 'traffic_checks','display_name' => __('Traffic Limit')),
                                            'subdomain' => array('check' => 'subdomain_checks','display_name' => __('Subdomain Limit')),
                                            'domain_alias' => array('check' => 'domain_alias_checks','display_name' => __('Domain Alias Limit')),
                                            'email_box' => array('check' => 'email_box_checks','display_name' => __('Email Box Limit')),
                                            'email_box_size' => array('check' => 'email_box_size_checks','display_name' => __('Email Box Size Limit')),
                                            'database' => array('check' => 'database_checks','display_name' => __('Database Limit')),
                                            'database_size' => array('check' => 'database_size_checks','display_name' => __('Database Size Limit')),
                                            'template' => array('check' => 'template_checks','display_name' => __('Template Limit')),
                                            'expire_time_limit' => array('check' => 'expire_time_limit_checks','display_name' => __('Expire Time Limit'))
                                        );
                                        if(isset($client_id)) {
                                            unset($data['template_limits']['clients']);

                                            if($client_type > RESELLER_USER) {
                                                unset($data['template_limits']['domain']);
                                                unset($data['template_limits']['template']);
                                            }
                                        }
                                        if ($user_type > ADMIN_USER) {
                                            unset($data['template_limits']['clients']);
                                            if ($user_type > RESELLER_USER) {
                                                unset($data['template_limits']['domain']);
                                                unset($data['template_limits']['template']);
                                            }
                                        }

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
                            <div id="user-tab-permissions" class="tab-pane fade" role="tabpanel">
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
                                            'access_email' => array('display_name' => __('Access Email Management')),
                                            'access_email_alias' => array('display_name' => __('Access Email Alias Management')),
                                            //                                            'access_mailing_lists' => array('display_name' => __('Access Mailing List Management')),
                                            //                                            'access_spam_filters' => array('display_name' => __('Access Spam Filters Management')),
                                            'access_task' => array('display_name' => __('Access Scheduled Task Management')),
                                            'access_dns' => array('display_name' => __('Access DNS Management')),
                                            'access_ssl' => array('display_name' => __('Access SSL Management')),
                                            'access_backup' => array('display_name' => __('Access Backup Management')),
                                            'access_protect_dir' => array('display_name' => __('Access Protect Directory Management')),
                                        );
                                        if(isset($client_id)) {
                                            unset($data['template_permissions']['access_client']);
                                            if($client_type > RESELLER_USER) {
                                                unset($data['template_permissions']['access_domain']);
                                            }
                                        }
                                        if ($user_type > ADMIN_USER) {
                                            unset($data['template_permissions']['access_client']);
                                            if($user_type > RESELLER_USER) {
                                                unset($data['template_permissions']['access_domain']);
                                            }
                                        }

                                        foreach ($data['template_permissions'] as $key => $value) {
                                            ?>
                                            <div class="row ">
                                                <div class="col-lg-4 col-8">
                                                    <label for="<?= $key;?>"><?= $value['display_name']; ?></label>
                                                </div>
                                                <div class="col-lg-8 col-4 checkbox-list">
                                                    <label><?= $form->render($key,array('class' => 'checkbox-blue')); ?> <?= __('Allow'); ?></label>
                                                </div>
                                            </div>

                                        <? } ?>
                            </div>
                            <div id="user-tab-hosting-settings" class="tab-pane fade" role="tabpanel">
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
                                            'dedicated_pool' => array('display_name' => __('Dedicated Pool'), 'type' => 'checkbox', "server" => "Windows"),
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
    </div>
</form>
    <script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
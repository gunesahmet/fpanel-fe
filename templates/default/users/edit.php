<div id="user-edit-panel">
    <form id="edit-user-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
        <div class="row">
            <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
            <div class="col-md-7">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= $client_name;?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="tabbable-line">
                            <ul class="nav nav-pills nav-fill" id="domain-create-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#account" data-toggle="tab"><?= __('Account'); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#limits" data-toggle="tab"><?= __('Limits'); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#permissions" data-toggle="tab"><?= __('Permissions'); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#hosting-settings" data-toggle="tab"><?= __('Hosting Settings'); ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#ip_address" data-toggle="tab"><?= __('IP Addresses'); ?></a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="account" class="tab-pane active">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong><?= __('Change Password');?></strong>
                                            <p class="muted"><?= __('You can change your account password from here');?></p>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputFullname"><?= __('Fullname');?></label>
                                                        <div class="col-md-12">
                                                            <?= $form->render('fullname',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputUsername"><?= __('Username');?></label>
                                                        <div class="col-md-12">
                                                            <?= $form->render('username',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="password"><?= __('New Password');?></label>
                                                        <div class="col-md-12">
                                                            <div class="input-group input-group-sm" id="input-group-password">
                                                                <?= $form->render("password",array('class' => 'form-control form-control-sm')) ?>
                                                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                                                <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="confirm"><?= __('Confirm Password');?> *</label>
                                                        <div class="col-md-12"><input id="confirm-password" name="confirm-password" type="password" class="form-control form-control-sm input-sm"></div>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong><?= __('Contact details');?></strong>
                                            <p class="muted"><?= __('You can change contact informations from here');?></p>
                                        </div>

                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputPhone"><?= __('Phone');?></label>
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                                <?= $form->render('telephone',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputEmail"><?= __('E-Mail');?></label>
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                                                <?= $form->render('email_addr',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputFax"><?= __('Fax');?></label>
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-fax"></i></span>
                                                                <?= $form->render('fax',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputCompany"><?= __('Company');?></label>
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-tags"></i></span>
                                                                <?= $form->render('company',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputAddress"><?= __('Address');?></label>
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-university"></i></span>
                                                                <?= $form->render('address',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputCity"><?= __('City');?></label>
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                                                <?= $form->render('city',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputRegion"><?= __('Region');?></label>
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-road"></i></span>
                                                                <?= $form->render('region',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-12" for="inputCountry"><?= __('Country');?></label>
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                                                                <?= $form->render('country',array('class' => 'form-control form-control-sm input-sm')); ?>
                                                            </div>
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
                                                <td width="40%"><?= __('Source Management'); ?></td>
                                                <td width="40%"><?= __('Limits'); ?></td>
                                                <td width="20%"><input type="checkbox" name="select-all-limits" checked="checked" class="checkbox-aero"/> <?= __('Select All'); ?></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <? $data['template_limits'] = array(
                                                'clients' => array('check' => 'clients_checks','display_name' => __('Client Limit')),
                                                'domain' => array('check' => 'domain_checks','display_name' => __('Domain Limit')),
                                                'disk' => array('check' => 'disk_checks','display_name' => __('Disk Limit') . " <b class='text-danger'>(MB)</b>"),
                                                'traffic' => array('check' => 'traffic_checks','display_name' => __('Traffic Limit') . " <b class='text-danger'>(MB)</b>"),
                                                'subdomain' => array('check' => 'subdomain_checks','display_name' => __('Subdomain Limit')),
                                                'domain_alias' => array('check' => 'domain_alias_checks','display_name' => __('Domain Alias Limit')),
                                                'email_box' => array('check' => 'email_box_checks','display_name' => __('Email Box Limit')),
                                                'email_box_size' => array('check' => 'email_box_size_checks','display_name' => __('Email Box Size Limit') . " <b class='text-danger'>(MB)</b>"),
//                                            'email_forward' => array('check' => 'email_forward_checks','display_name' => __('Email Forward Limit')),
//                                            'email_groups' => array('check' => 'email_groups_checks','display_name' => __('Email Group Limit')),
//                                            'email_autoresponder' => array('check' => 'email_autoresponder_checks','display_name' => __('Email Auto Responder Limit')),
//                                            'email_list' => array('check' => 'email_list_checks','display_name' => __('Email List Limit')),
//                                                'postgresql' => array('check' => 'postgresql_checks','display_name' => _('_PostgreSQL Limit_')),
//                                                'mssql' => array('check' => 'mssql_checks','display_name' => _('_MSSQL Limit_')),
//                                                'mysql' => array('check' => 'mysql_checks','display_name' => _('_MySQL Limit_')),
                                                'database' => array('check' => 'database_checks','display_name' => __('Database Limit')),
                                                'database_size' => array('check' => 'database_size_checks','display_name' => __('Database Size Limit') . " <b class='text-danger'>(MB)</b>"),
                                                'template' => array('check' => 'template_checks','display_name' => __('Template Limit')),
                                            );
                                            if ($user_edit_data['type'] > RESELLER_USER) {
                                                unset($data['template_limits']['clients']);
                                            }
                                            if ($user_edit_data['type'] > CLIENT_USER) {
                                                unset($data['template_limits']['template']);
                                            }
                                            foreach ($data['template_limits'] as $key => $value) { ?>
                                                <tr>
                                                    <td><label for="<?= $key;?>"><?= $value['display_name']; ?></label></td>
                                                    <td><?= $form->render($key,array('class' => 'form-control form-control-sm input-sm limits')); ?></td>
                                                    <td><label><?= $form->render($value['check'],array('class' => 'checkbox-blue')); ?> <?= __('Unlimited'); ?></label></td>
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
                                                <td width="20%"><input type="checkbox" name="select-all-perms" checked="checked" class="checkbox-aero"/> <?= __('Select All'); ?></td>
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
                                                'access_email' => array('display_name' => __('Access Email Management')),
                                                'access_email_alias' => array('display_name' => __('Access Email Alias Management')),
//                                        'access_mailing_lists' => array('display_name' => __('Access Mailing List Management')),
//                                        'access_spam_filters' => array('display_name' => __('Access Spam Filters Management')),
                                                'access_task' => array('display_name' => __('Access Scheduled Task Management')),
                                                'access_dns' => array('display_name' => __('Access DNS Management')),
                                                'access_ssl' => array('display_name' => __('Access SSL Management')),
                                                'access_backup' => array('display_name' => __('Access Backup Management')),
                                                'access_protect_dir' => array('display_name' => __('Access Protect Directory Management')),
                                            );
                                            if($user_edit_data['type'] > RESELLER_USER) {
                                                unset($data['template_permissions']['access_client']);
                                            }
                                            if($user_edit_data['type'] > CLIENT_USER) {
                                                unset($data['template_permissions']['access_template']);
                                            }
                                            foreach ($data['template_permissions'] as $key => $value) {
                                                ?>
                                                <tr>
                                                    <td><label for="<?= $key;?>"><?= $value['display_name']; ?></label></td>
                                                    <td><label><?= $form->render($key,array("class" => 'checkbox-blue')); ?> <?= __('Allow'); ?></label></td>
                                                </tr>
                                            <? } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="hosting-settings" class="tab-pane">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <td width="40%"><?= __('Settings'); ?></td>
                                                <td width="40%"><?= __('Options'); ?></td>
                                            </tr>
                                            </thead>
                                            <tbody>


                                            <? $data = array(
                                                'php_support' => array('display_name' => __('PHP Support'), 'type' => 'checkbox'),
                                                'php_version' => array('display_name' => __('PHP Version'), 'type' => 'selectbox'),
                                                'asp_support' => array('display_name' => __('ASP Support'), 'type' => 'checkbox'),
                                                'asp_net_core_support' => array('display_name' => __('ASP.NET CORE Support'), 'type' => 'checkbox'),
                                                'asp_net_support' => array('display_name' => __('ASP.NET Support'), 'type' => 'checkbox'),
                                                'asp_net_version' => array('display_name' => __('ASP.NET Version'), 'type' => 'selectbox'),
                                                'perl_support' => array('display_name' => __('Perl Support'), 'type' => 'checkbox'),
                                                'python_support' => array('display_name' => __('Python Support'), 'type' => 'checkbox'),
                                                'web_statistics_support' => array('display_name' => __('Web Statistics Support'), 'type' => 'checkbox'),
                                                'web_statistics_type' => array('display_name' => __('Web Statistics Type'), 'type' => 'selectbox'),
                                                'dedicated_pool' => array('display_name' => __('Dedicated Pool'), 'type' => 'checkbox'),
                                                'max_cpu_usage' => array('display_name' => __('Max CPU Usage'), 'type' => 'text')
                                            );
                                            foreach ($data as $key => $value) {
                                                if(($system_type != CLOUD) && PANEL_OS_TYPE == LINUX) {
                                                    if(($key == "asp_support") || ($key == "asp_net_support") || ($key == "asp_net_core_support") || ($key == "asp_net_version") || ($key == "php_version") || ($key == "dedicated_pool") || ($key == "max_cpu_usage"))
                                                        continue;
                                                } ?>

                                                <tr>
                                                    <td><label for="<?= $key;?>"><?= $value['display_name']; ?></label></td>
                                                    <? if($value['type'] == "checkbox")  { ?>
                                                        <td><label><?= $form->render($key,array("class" => 'checkbox-blue')); ?> <?= __('Allow'); ?></label></td>
                                                    <? } else { ?>
                                                        <td><?= $form->render($key,array('class' => 'form-control form-control-sm input-sm')); ?></td>
                                                    <? } ?>
                                                </tr>

                                            <? } ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="ip_address" class="tab-pane">
                                    <div class="form-group">
                                        <label class="col-md-12"><?= __('IP Address');?></label>
                                        <?= $form->render('ipaddress_list',array('class' => 'multiselect')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= __('About User');?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <ul class="list-group clear-list m-t">
                            <li class="list-group-item fist-item">
                                <span class="float-right">
                                    <?= $user_edit_data['fullname'];?>
                                </span>
                                <span class="label label-success">*</span> <?= __('Fullname')?>
                            </li>
                            <li class="list-group-item">
                                <span class="float-right">
                                    <?= ($user_edit_data['type'] == RESELLER_USER ? __('Reseller') : __('Client'));?>
                                </span>
                                <span class="label label-info">*</span> <?= __('User Type')?>
                            </li>
                            <li class="list-group-item">
                                <span class="float-right">
                                    <?= $this->env->convert($user_edit_data['traffic']);?>
                                </span>
                                <span class="label label-primary">*</span> <?= __('Disk Used')?>
                            </li>
                            <li class="list-group-item">
                                <span class="float-right">
                                    <?= $this->env->convert($user_edit_data['disk']);?>
                                </span>
                                <span class="label label-danger">*</span> <?= __('Traffic Used')?>
                            </li>
                            <li class="list-group-item">
                                <span class="float-right">
                                    <?= $this->env->convertDate($user_edit_data['last_login']);?>
                                </span>
                                <span class="label label-default">*</span> <?= __('Last Login')?>
                            </li>
                            <li class="list-group-item">
                                <span class="float-right">
                                    <?= isset($user_edit_data['last_login_ip']) ? $user_edit_data['last_login_ip'] : "-";?>
                                </span>
                                <span class="label label-default">*</span> <?= __('Last Login IP')?>
                            </li>
                        </ul>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-right">
                                    <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                    <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
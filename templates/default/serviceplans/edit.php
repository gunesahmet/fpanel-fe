<div id="serviceplans-edit-panel" data-width="70%" data-height="50%">
    <form method="post" autocomplete="off" action="<?= $post_url;?>" id="serviceplan-create">
        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
        <div class="row">
            <div class="col-md-7">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= __('Edit Serviceplan');?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <?= $form->render('serviceplan_name',array('class' => 'input form-control form-control-sm','placeholder' => __('Serviceplan Name'))); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="overuse"><?= __('Overuse'); ?></label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('overuse',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="is_default"><?= __('Default'); ?></label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('is_default',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                        <br />
                        <hr />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tabbable-line">
                                    <ul class="nav nav-pills nav-fill" id="advanced-settings-tabs">
                                        <li class="nav-item active"><a data-toggle="tab" href="#tab-limits"><i class="fa fa-warning"></i> <?= __('Limits'); ?></a></li>
                                        <li class="nav-item"><a data-toggle="tab" href="#tab-hosting-settings"><i class="fa fa-shield"></i> <?= __('Permissions'); ?></a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="tab-limits" class="tab-pane active">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                <tr>
                                                    <th><?= __('Source Management'); ?></th>
                                                    <th><?= __('Limits'); ?></th>
                                                    <th> <input type="checkbox" name="select-all-limits" class="checkbox-aero" checked"/> <?= __('Select All'); ?></th>
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
                                                        <td><label for="<?= $key;?>"><?= $value['display_name']; ?></label> </td>
                                                        <td><?= $form->render($key,array('class' => 'form-control form-control-sm input-sm limits')); ?></td>
                                                        <td><label><?= $form->render($value['check'],array('class' => 'checkbox-blue')); ?> <?= __('Unlimited'); ?></label></td>
                                                    </tr>
                                                <? } ?>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="tab-hosting-settings" class="tab-pane">
                                            <table class="table table-striped table-hover">
                                                <tbody>

                                                <? $data = array(
                                                    'php_support' => array('display_name' => __('PHP Support'), 'type' => 'checkbox'),
                                                    'php_version' => array('display_name' => __('PHP Version'), 'type' => 'selectbox'),
                                                    'asp_support' => array('display_name' => __('ASP Support'), 'type' => 'checkbox'),
                                                    'asp_net_core_support' => array('display_name' => __('ASP.NET CORE Support'), 'type' => 'checkbox'),
                                                    'asp_net_support' => array('display_name' => __('ASP.NET Support'), 'type' => 'checkbox'),
                                                    'asp_net_version' => array('display_name' => __('ASP.NET Version'), 'type' => 'selectbox'),
                                                    'application_pool_mode' => array('display_name' => __('Application Pool Mode'), 'type' => 'selectbox'),
                                                    'perl_support' => array('display_name' => __('Perl Support'), 'type' => 'checkbox'),
                                                    'python_support' => array('display_name' => __('Python Support'), 'type' => 'checkbox'),
                                                    'web_statistics_support' => array('display_name' => __('Web Statistics Support'), 'type' => 'checkbox'),
                                                    'web_statistics_type' => array('display_name' => __('Web Statistics Type'), 'type' => 'selectbox'),
                                                    'dedicated_pool' => array('display_name' => __('Dedicated Pool'), 'type' => 'checkbox'),
                                                    'max_cpu_usage' => array('display_name' => __('Max CPU Usage') . ' ' .'('. __('Percent') .')', 'type' => 'text'),
                                                );
                                                foreach ($data as $key => $value) {
                                                    if(($system_type != CLOUD) && PANEL_OS_TYPE == LINUX) {
                                                        if(($key == "asp_support") || ($key == "asp_net_support") || ($key == "asp_net_core_support") || ($key == "asp_net_version") || ($key == "php_version") || ($key == "dedicated_pool") || ($key == "max_cpu_usage") || ($key == "application_pool_mode"))
                                                            continue;
                                                    } ?>
                                                    <tr>
                                                        <td><label for="<?= $key;?>"><?= $value['display_name']; ?></label></td>
                                                        <? if($value['type'] == "checkbox")  { ?>
                                                            <td><label><?= $form->render($key,array('class' => 'checkbox-blue')); ?> <?= __('Allow'); ?></label></td>
                                                        <? } else { ?>
                                                            <td><?= $form->render($key,array('class' => 'form-control form-control-sm')); ?></td>
                                                        <? } ?>
                                                    </tr>


                                                <? } ?>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="float-right">
                                    <button class="btn btn-default " type="reset"><?= __('Cancel'); ?></button>
                                    <button class="btn btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-5">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5><?= __('Permissions');?></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="tab-content">
                            <div id="contact-1" class="tab-pane active">
                                <hr/>
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th><?= __('Source Management'); ?></th>
                                        <th> <input type="checkbox" name="select-all-perms" class="checkbox-aero" checked"/> <?= __('Select All'); ?></th>
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
                                        'access_email' => array('display_name' => __('Access Email Management')),
                                        'access_email_alias' => array('display_name' => __('Access Email Alias Management')),
                                        'access_task' => array('display_name' => __('Access Scheduled Task Management')),
                                        'access_dns' => array('display_name' => __('Access DNS Management')),
                                        'access_ssl' => array('display_name' => __('Access SSL Management')),
                                        'access_backup' => array('display_name' => __('Access Backup Management')),
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
                                            <td><label for="<?= $key;?>"><?= $value['display_name']; ?></label></td>
                                            <td class="checkbox-list"><?= $form->render($key,array('class' => 'checkbox-blue', "data-id" => "permissions")); ?> <?= __('Allow'); ?></td>
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
    </form>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
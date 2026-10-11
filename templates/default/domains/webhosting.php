<form id="webhosting-domain-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-md-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Web Hosting Settings');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="ipaddress_list"><?= __('IP Address'); ?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('ipaddress_list',array('class' => 'form-control form-control-sm input-sm select-picker'));?>
                        </div>
                    </div>
                    <? if($ssl == true) { ?>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="ssl_support"><?= __('SSL Support'); ?> </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render('ssl_support',array('class' => 'checkbox-blue'));?>
                            </div>
                        </div>
                        <? $display = "display:none"; if($ssl_status == true) $display = "";?>
                        <div class="form-group" id="ssl_lists" style="<?= $display;?>">
                            <div class="col-md-6">
                                <label for="ssl_lists"> <?= __('SSL Lists'); ?> </label>
                            </div>
                            <div class="col-md-6">
                                <?= $form->render('ssl_list',array('class' => 'form-control form-control-sm input-sm select-picker'));?>
                            </div>
                        </div>
                    <? } ?>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="ftp_username"><?= __('FTP Username'); ?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('ftp_username',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="ftp_password"><?= __('FTP Password'); ?></label>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm" id="input-group-password">
                                <?= $form->render("ftp_password",array('class' => 'form-control form-control-sm')) ?>
                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                <span class="input-group-append"><button type="button" data-type="password-generate"  tabindex="-1" class="btn btn-primary btn-sm"><i class="fa fa-sync-alt"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="ftp_password"><?= __('Confirm Password'); ?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('ftp_password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
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
        <div class="col-md-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Web Hosting Features');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <?
                    $data = array(
                        'php_support' => array('display_name' => __('PHP Support'), 'type' => 'checkbox', "data-sub-elements" => "php_version",),
                        'php_version' => array('display_name' => __('PHP Version'), 'type' => 'selectbox'),

                        'asp_support' => array('display_name' => __('ASP Support'), 'type' => 'checkbox'),
                        'asp_net_core_support' => array('display_name' => __('ASP.NET Core Support'), 'type' => 'checkbox'),

                        'asp_net_support' => array('display_name' => __('ASP.NET Support'), 'type' => 'checkbox', "data-sub-elements" => "aspnet_version",),
                        'aspnet_version' => array('display_name' => __('ASP.NET Version'), 'type' => 'selectbox'),

                        'application_pool_mode' => array('display_name' => __('Application Pool Mode'), 'type' => 'selectbox'),
                        'perl_support' => array('display_name' => __('Perl Support'), 'type' => 'checkbox'),
                        'python_support' => array('display_name' => __('Python Support'), 'type' => 'checkbox'),
                        'web_statistics_support' => array('display_name' => __('Web Statistics Support'), 'type' => 'checkbox', "data-sub-elements" => "web_statistics_type",),
                        'web_statistics_type' => array('display_name' => __('Web Statistics Type'), 'type' => 'selectbox'),
                        'dedicated_pool' => array('display_name' => __('Dedicated Pool'), 'type' => 'checkbox', "data-sub-elements" => "max_cpu_usage",),
                        'max_cpu_usage' => array('display_name' => __('Max CPU Usage'), 'type' => 'text'),
                    );

                    if(($web_server_os != WINDOWS) || ($system_type != CLOUD)) {
                        unset($data['asp_support']);
                        unset($data['asp_net_support']);
                        unset($data['asp_net_core_support']);
                        unset($data['aspnet_version']);
                        unset($data['dedicated_pool']);
                        unset($data['max_cpu_usage']);
                        unset($data['php_version']);
                        unset($data['application_pool_mode']);
                    }
                    ?>
                    <? foreach ($data as $key => $value) { ?>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="<?= $key;?>"><?= $value['display_name']; ?></label>
                            </div>
                            <div class="col-md-6">
                                <?  if ($value['type'] == "checkbox") { ?>
                                <? if(!isset($value["data-sub-elements"])) { ?>
                                    <?= $form->render($key,array('class' => 'checkbox-blue')); ?>
                                <? } else { ?>
                                        <? if($user_type == DOMAIN_USER) { ?>
                                            <?= $form->render($key,array('class' => 'checkbox-blue')); ?>
                                        <? } else { ?>
                                            <?= $form->render($key,array('class' => 'checkbox-blue', "data-sub-elements" => $value["data-sub-elements"])); ?>
                                        <? } ?>
                                <? } ?>
                                <? } else if ($value["type"] == "selectbox") { ?>
                                    <?= $form->render($key,array('class' => 'form-control form-control-sm input-sm select-picker')); ?>
                                <?  } else { ?>
                                    <?= $form->render($key,array('class' => 'form-control form-control-sm input-sm')); ?>
                                <? } ?>
                            </div>
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</form>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
<div class="row">
    <form id="webhosting-domain-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo __('Web Hosting Settings');?></div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label><? echo __('IP Address'); ?> </label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('ipaddress_list',array('class' => 'form-control input-sm'));?>
                        </div>
                    </div>
                    <? if($ssl == true) { ?>
                        <div class="form-group">
                            <div class="col-md-6">
                                <label><? echo __('SSL Support'); ?> </label>
                            </div>
                            <div class="col-md-6">
                                <? echo $form->render('ssl_support',array('class' => 'checkbox-blue'));?>
                            </div>
                        </div>
                        <? $display = "display:none"; if($ssl_status == true) $display = "";?>
                        <div class="form-group" id="ssl_lists" style="<? echo $display;?>">
                            <div class="col-md-6">
                                <label> <? echo __('SSL Lists'); ?> </label>
                            </div>
                            <div class="col-md-6">
                                <? echo $form->render('ssl_list',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>
                    <? } ?>
                    <?

                    $hosting_settings = array('display_name' => __('Hosting Type'), 'type' => 'selectbox');

                    $data = array(
                        'php_support' => array('display_name' => __('PHP Support'), 'type' => 'checkbox'),
                        'php_version' => array('display_name' => __('PHP Version'), 'type' => 'selectbox'),
                        'asp_support' => array('display_name' => __('ASP Support'), 'type' => 'checkbox'),
                        'asp_net_support' => array('display_name' => __('ASP.NET Support'), 'type' => 'checkbox'),
                        'aspnet_version' => array('display_name' => __('ASP.NET Version'), 'type' => 'selectbox'),
                        'cgi_support' => array('display_name' => __('CGI Support'), 'type' => 'checkbox'),
                        'perl_support' => array('display_name' => __('Perl Support'), 'type' => 'checkbox'),
                        'python_support' => array('display_name' => __('Python Support'), 'type' => 'checkbox'),
                        'web_statistics_support' => array('display_name' => __('Web Statistics Support'), 'type' => 'checkbox'),
                        'web_statistics_type' => array('display_name' => __('Web Statistics Type'), 'type' => 'selectbox'),
                        'custom_error_pages' => array('display_name' => __('Custom Error Page Support'), 'type' => 'checkbox'),
                        'dedicated_pool' => array('display_name' => __('Dedicated Pool'), 'type' => 'checkbox'),
                        'max_cpu_usage' => array('display_name' => __('Max CPU Usage'), 'type' => 'text')
                    );

                    if((PANEL_OS_TYPE != "windows") && ($server_type != "cloud") ) {
                        unset($data['asp_support']);
                        unset($data['asp_net_support']);
                        unset($data['aspnet_version']);
                        unset($data['dedicated_pool']);
                        unset($data['max_cpu_usage']);
                        unset($data['php_version']);
                    }
                    ?>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label><? echo $hosting_settings['display_name']; ?></label>
                        </div>
                        <div class="col-md-6">
                            <?  echo $form->render('hosting_settings',array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="ftp_username"><? echo __('FTP Username'); ?> </label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('ftp_username',array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="ftp_password"><? echo __('FTP Password'); ?></label>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm" id="input-group-password">
                                <? echo $form->render("ftp_password",array('class' => 'form-control')) ?>
                                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="ftp_username"><? echo __('Confirm Password'); ?> </label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('ftp_password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control input-sm')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                                <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo __('Web Hosting Features');?></div>
                <div class="panel-body">
                    <? foreach ($data as $key => $value) { ?>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><? echo $value['display_name']; ?></label>
                            </div>
                            <div class="col-md-6">
                                <?  if ($value['type'] == "checkbox") { ?>
                                    <? echo $form->render($key,array('class' => 'checkbox-blue')); ?>
                                <?  } else { ?>
                                    <? echo $form->render($key,array('class' => 'form-control input-sm')); } ?>
                            </div>
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    core.inputInits();
</script>
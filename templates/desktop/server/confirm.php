<form method="post" autocomplete="off" class="form-horizontal">
    <div class="row">
        <div class="col-sm-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><? echo __('Server Configuration'); ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="status"> <strong><? echo __('IP Addresses');?></strong></label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('ipaddress_list',array('class' => 'multiselect-custom')); ?>
                                </div>
                            </div>
                            <? if($web == true) { ?>
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="status"> <strong><? echo __('Domain Limit');?></strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <? echo $form->render('domain_limit_check',array('class' => 'checkbox')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <? echo $form->render('domain_limit',array('class' => 'form-control')); ?>
                                    </div>
                                </div>
                            <? } ?>
                            <? if($database == true) { ?>
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="status"> <strong><? echo __('Database Limit');?></strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <? echo $form->render('database_limit_check',array('class' => 'checkbox')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <? echo $form->render('database_limit',array('class' => 'form-control')); ?>
                                    </div>
                                </div>
                            <? } ?>

                            <div class="form-actions col-md-12">
                                <div class="pull-right">
                                    <a class="btn btn-danger" href="<? echo $this->uri->url_encrypt('server/create');?>" type="button"><i class="fa fa-backward"></i> <? echo __('Go Back'); ?></a>
                                    <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Confirm'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-sm-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><? echo __('Server Data'); ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h5><? echo __('System Overview');?></h5>
                            <hr />
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><? echo __('System OS');?></strong></a> <? echo $server_data['sysinfo']['system']['os'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><? echo __('System OS Kernel');?></strong></a> <? echo $server_data['sysinfo']['system']['kernel'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><? echo __('Hostname');?></strong></a> <? echo $server_data['sysinfo']['network']['hostname'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><? echo __('Default IP Address');?></strong></a> <? echo $server_data['sysinfo']['network']['ipaddr'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><? echo __('Total Disk');?></strong></a> <? echo $this->env->convert($server_data['sysinfo']['disk']['total']);?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><? echo __('Total Ram');?></strong></a> <? echo $this->env->convert($server_data['sysinfo']['memory']['total']);?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><? echo __('CPU');?></strong></a> <? echo $server_data['sysinfo']['cpu_info'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><? echo __('Uptime');?></strong></a> <? echo $this->env->secondsToTime($server_data['sysinfo']['uptime']);?></p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</form>

<script>
    if($('#domain_limit_check').is(':checked')) {
        $('#domain_limit').prop('disabled',true);
    }
    else {
        $('#domain_limit').prop('disabled',false);
    }

    $('#domain_limit_check').on('click', function () {
        if($(this).is(':checked')) {
            $('#domain_limit').prop('disabled',true);
        }
        else {
            $('#domain_limit').prop('disabled',false);
        }
    });
    if($('#database_limit_check').is(':checked')) {
        $('#database_limit').prop('disabled',true);
    }
    else {
        $('#database_limit').prop('disabled',false);
    }

    $('#database_limit_check').on('click', function () {
        if($(this).is(':checked')) {
            $('#database_limit').prop('disabled',true);
        }
        else {
            $('#database_limit').prop('disabled',false);
        }
    });
</script>
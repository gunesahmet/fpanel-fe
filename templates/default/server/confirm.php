<form method="post" autocomplete="off" class="form-horizontal">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-sm-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Server Configuration'); ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="status"> <strong><?= __('IP Addresses');?></strong></label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('ipaddress_list',array('class' => 'multiselect-custom')); ?>
                                </div>
                            </div>
                            <? if($web == true) { ?>
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="status"> <strong><?= __('Domain Limit');?></strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <?= $form->render('domain_limit_check',array('class' => 'checkbox')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('domain_limit',array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>
                            <? } ?>
                            <? if($database == true) { ?>
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="status"> <strong><?= __('Database Limit');?></strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <?= $form->render('database_limit_check',array('class' => 'checkbox')); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('database_limit',array('class' => 'form-control form-control-sm')); ?>
                                    </div>
                                </div>
                            <? } ?>

                            <div class="form-actions col-md-12">
                                <div class="float-right">
                                    <a class="btn btn-danger" href="<?= $this->uri->url_encrypt('server/create');?>" type="button"><i class="fa fa-backward"></i> <?= __('Go Back'); ?></a>
                                    <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Confirm'); ?></button>
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
                    <h5><?= __('Server Data'); ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h5><?= __('System Overview');?></h5>
                            <hr />
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><?= __('System OS');?></strong></a> <?= $server_data['sysinfo']['system']['os'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><?= __('System OS Kernel');?></strong></a> <?= $server_data['sysinfo']['system']['kernel'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><?= __('Hostname');?></strong></a> <?= $server_data['sysinfo']['network']['hostname'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><?= __('Default IP Address');?></strong></a> <?= $server_data['sysinfo']['network']['ipaddr'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><?= __('Total Disk');?></strong></a> <?= $this->env->convert($server_data['sysinfo']['disk']['total']);?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><?= __('Total Ram');?></strong></a> <?= $this->env->convert($server_data['sysinfo']['memory']['total']);?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><?= __('CPU');?></strong></a> <?= $server_data['sysinfo']['cpu_info'];?></p>
                                </li>
                                <li class="list-group-item">
                                    <p><a class="text-success"><strong><?= __('Uptime');?></strong></a> <?= $this->env->secondsToTime($server_data['sysinfo']['uptime']);?></p>
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
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
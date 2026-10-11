<div class="row" id="domain-backup-create-panel" data-width="60%">
    <form id="domain-backup-create-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo __('Backup Features');?></div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="backup_name"> <? echo __('Backup Name');?> </label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('backup_name',array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="description"> <? echo __('Backup Description');?> </label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('description',array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="backup_repo"> <? echo __('Backup Location');?> </label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('backup_repo',array('class' => 'form-control input-sm')); ?>
                        </div>

                    </div>

                    <div class="form-group ftp_repos">
                        <div class="col-md-5">
                            <label for="repo"> <? echo __('FTP Repos');?> </label>
                        </div>
                        <div class="col-md-6">
                            <select name="repo" id="repo" class="form-control">
                                <? foreach($ftp_repos as $repo) { ?>
                                    <option value="<? echo $repo['id'];?>"><? echo $repo['ftp_server'];?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group cloud_storages">
                        <div class="col-md-5">
                            <label for="storages"> <? echo __('Cloud Storages');?> </label>
                        </div>
                        <div class="col-md-6">
                            <select name="storages" id="storages" class="form-control">
                                <? foreach($storages as $storage) { ?>
                                    <option value="<? echo $storage['id'];?>"><? echo $storage['name'];?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="send_mail"> <? echo __('When complete backup send email');?> </label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('send_mail',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="email_address"> <? echo __('Email Address');?> </label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('email_address',array('class' => 'form-control input-sm')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo __('Settings');?></div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="all_content"> <? echo __('All');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <? echo $form->render('all_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="domain_settings"> <? echo __('Domain Settings');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <? echo $form->render('domain_settings',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="www_content"> <? echo __('FTP Directories');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <? echo $form->render('www_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="subdomain_content"> <? echo __('Subdomains');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <? echo $form->render('subdomain_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="database_content"> <? echo __('Databases');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <? echo $form->render('database_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="dns_content"> <? echo __('DNS Settings');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <? echo $form->render('dns_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="mail_content"> <? echo __('Mail Addresses');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <? echo $form->render('mail_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <button class="btn btn-sm btn-default btn-block" type="reset"> <? echo __('Cancel'); ?></button>
            <button class="btn btn-sm btn-primary btn-block" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
        </div>
    </form>
</div>

<style>
    .ftp_repos,.cloud_storages {
        display: none;
    }
</style>
<script>
    core.setWidth({target:'domain-backup-create-panel'});
    core.inputInits();
    $('input[id="all_content"]').on('ifChecked ifUnchecked', function(event) {
        console.log(event);

        if (event.type == 'ifChecke d') {
            $('input[id="domain_settings"]').iCheck('check');
            $('input[id="www_content"]').iCheck('check');
            $('input[id="database_content"]').iCheck('check');
            $('input[id="dns_content"]').iCheck('check');
            $('input[id="mail_content"]').iCheck('check');
            $('input[id="subdomain_content"]').iCheck('check');
        }
        else {
            $('input[id="domain_settings"]').iCheck('uncheck');
            $('input[id="www_content"]').iCheck('uncheck');
            $('input[id="database_content"]').iCheck('uncheck');
            $('input[id="dns_content"]').iCheck('uncheck');
            $('input[id="mail_content"]').iCheck('uncheck');
            $('input[id="subdomain_content"]').iCheck('uncheck');
        }
    });
    $('input[id="send_mail"]').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('input[id="email_address"]').prop('disabled', false);
        }
        else {
            $('input[id="email_address"]').prop('disabled', true);
        }
    });
</script>


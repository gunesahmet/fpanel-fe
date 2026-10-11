<div class="row" id="create-server-backup-panel" data-width="50%">
    <form id="edit-server-repo-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Backup Features');?></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-md-6">
                                <label for="backup_name"> <? echo __('Backup Name');?> </label>
                            </div>
                            <div class="col-md-6">
                                <? echo $form->render('backup_name',array('class' => 'form-control input-sm')); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label for="description"> <? echo __('Backup Description');?> </label>
                            </div>
                            <div class="col-md-6">
                                <? echo $form->render('description',array('class' => 'form-control input-sm', 'row' => '5')); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label for="backup_repo"> <? echo __('Backup Location');?> </label>
                            </div>
                            <div class="col-md-6">
                                <? echo $form->render('backup_repo',array('class' => 'form-control input-sm')); ?>
                            </div>

                        </div>

                        <div class="form-group ftp_repos">
                            <div class="col-md-6">
                                <label for="repo"> <? echo __('FTP Repos');?> </label>
                            </div>
                            <div class="col-md-6">
                                <select name="repo" id="repo" class="form-control input-sm">
                                    <? foreach($ftp_repos as $repo) { ?>
                                        <option value="<? echo $repo['id'];?>"><? echo $repo['ftp_server'];?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group cloud_storages">
                            <div class="col-md-6">
                                <label for="storages"> <? echo __('Cloud Storages');?> </label>
                            </div>
                            <div class="col-md-6">
                                <select name="storages" id="storages" class="form-control input-sm">
                                    <? foreach($storages as $storage) { ?>
                                        <option value="<? echo $storage['id'];?>"><? echo $storage['name'];?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label for="send_mail"> <? echo __('When complete backup send email');?> </label>
                            </div>
                            <div class="col-md-6">
                                <div class="uniformjs">
                                    <? echo $form->render('send_mail',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label for="email_address"> <? echo __('Email Address');?> </label>
                            </div>
                            <div class="col-md-6">
                                <? echo $form->render('email_address',array('class' => 'form-control input-sm')); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Contents');?></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-md-6">
                                <label for="include_all"> <? echo __('Select All');?> </label>
                            </div>
                            <div class="col-md-6">
                                <div class="uniformjs">
                                    <? echo $form->render('include_all',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6">
                                <i class="fa fa-angle-right"></i>
                                <label for="www_content"> <? echo __('FTP Root'). ' (httpdoc) ' . __('Directory');?>   </label>
                            </div>
                            <div class="col-md-6">
                                <div class="uniformjs">
                                    <? echo $form->render('www_content',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6">
                                <i class="fa fa-angle-right"></i>
                                <label for="subdomain_content"> <? echo __('Subdomains');?> </label>
                            </div>
                            <div class="col-md-6">
                                <div class="uniformjs">
                                    <? echo $form->render('subdomain_content',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <i class="fa fa-angle-right"></i>
                                <label for="database_content"> <? echo __('Databases');?> </label>
                            </div>
                            <div class="col-md-6">
                                <? echo $form->render('database_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <i class="fa fa-angle-right"></i>
                                <label for="dns_content"> <? echo __('DNS Settings');?> </label>
                            </div>
                            <div class="col-md-6">
                                <? echo $form->render('dns_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <i class="fa fa-angle-right"></i>
                                <label for="mail_content"> <? echo __('Mail Addresses');?> </label>
                            </div>
                            <div class="col-md-6">
                                <? echo $form->render('mail_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Users'); ?> &amp; <? echo __('Domain'); ?></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label for="select_all_client"> <? echo __('Select All'); ?></label>
                            </div>
                            <div class="col-md-8">
                                <input type="checkbox" class="checkbox-blue" id="select_all_client"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <? echo $form->render('client_list',array('class' => 'multiselect')); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <button class="btn btn-sm btn-block btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-sm btn-block btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </form>
</div>
<style>
    .ftp_repos,.cloud_storages {
        display: none;
    }
    #description {
        resize: none;
    }
</style>
<script>
    core.setWidth({
        target: 'create-server-backup-panel'
    });
    $('input[id="backup_location_ftp"]').unbind();
    $('input[id="backup_location_ftp"]').bind('click', function () {
        if ($(this).is(':checked')) {
            $('#repo').attr('disabled',false);
            $(".ftp_repos").show();
        }
    });
    $('input[id="backup_location_server"]').unbind();
    $('input[id="backup_location_server"]').bind('click', function () {
        if ($(this).is(':checked')) {
            $('#repo').attr('disabled',true);
            $(".ftp_repos").hide();
        }
    });

    $('input[id="include_all"]').unbind();
    $('input[id="include_all"]').bind('click', function(){
        if ($(this).is(':checked')) {
            $('input[id="www_content"]').prop('checked', true);
            $('input[id="subdomain_content"]').prop('checked', true);
            $('input[id="database_content"]').prop('checked', true);
            $('input[id="dns_content"]').prop('checked', true);
            $('input[id="mail_content"]').prop('checked', true);
            $.uniform.update('input[type="checkbox"]');
        }
        else {
            $('input[id="www_content"]').prop('checked', false);
            $('input[id="subdomain_content"]').prop('checked', false);
            $('input[id="database_content"]').prop('checked', false);
            $('input[id="dns_content"]').prop('checked', false);
            $('input[id="mail_content"]').prop('checked', false);
            $.uniform.update('input[type="checkbox"]');
        }
    });


    $('input[id="send_mail"]').unbind();
    $('input[id="send_mail"]').bind('click', function () {
        if ($(this).is(':checked')) {
            $('input[id="email_address"]').prop('disabled', false);
        }
        else {
            $('input[id="email_address"]').prop('disabled', true);
        }
    });

    $('input[id="select_all_client"]').unbind();
    $('input[id="select_all_client"]').bind('click', function(){
        if ($(this).is(':checked')) {
            $('.multiselect-custom').multiSelect('select_all');
        }
        else {
            $('.multiselect-custom').multiSelect('deselect_all');
        }
    });
</script>


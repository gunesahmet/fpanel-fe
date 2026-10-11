<div class="row" id="domain-backup-restore-panel" data-width="60%">
    <form id="domain-backup-restore-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="col-lg-5">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo __('Settings');?></div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="select_all_contents"> <strong><? echo __('Select All');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" class="checkbox-blue" id="select_all_contents">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_domain_configs"> <strong><? echo __('Restore Domain Configurations');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_domain_configs',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_dns"> <strong><? echo __('Restore DNS Records');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_dns',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_httpdoc"> <strong><? echo __('Restore Ftp Root (httpdoc)');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_httpdoc',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_subdomains"> <strong><? echo __('Restore Subdomains');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_subdomains',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_databases"> <strong><? echo __('Restore Databases');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_databases',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_mails"> <strong><? echo __('Restore Mails');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_mails',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-11">
                            <div class="pull-right">
                                <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>

                                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Start'); ?></button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo __('Features');?></div>
                <div class="panel-body">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th><? echo __('Content');?></th>
                            <th><? echo __('Status');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><? echo __('Domain Settings');?></td>
                            <td><? if($backup_content['content']['domain_settings'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                        </tr>
                        <tr>
                            <td><? echo __('FTP Root (httpdoc, error-docs and cgi-bin folders)');?></td>
                            <td><? if($backup_content['content']['www_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                        </tr>
                        <tr>
                            <td><? echo __('Databases');?></td>
                            <td><? if($backup_content['content']['database_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                        </tr>
                        <tr>
                            <td><? echo __('DNS Records');?></td>
                            <td><? if($backup_content['content']['dns_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                        </tr>
                        <tr>
                            <td><? echo __('Mail Accounts');?></td>
                            <td><? if($backup_content['content']['mail_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                        </tr>
                        <tr>
                            <td><? echo __('Subdomains');?></td>
                            <td><? if($backup_content['content']['subdomain_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th width="40%"><? echo __('Included Subdomains');?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach($backup_content['subdomains'] as $subdomain) { ?>
                            <tr>
                                <td></td>
                                <td><? echo $subdomain['full_name']; ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>

                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th width="40%"><? echo __('Included Mail Accounts');?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach($backup_content['mail_accounts'] as $mail) { ?>
                            <tr>
                                <td></td>
                                <td><? echo $mail['mail_name']."@".$domain_name; ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>

                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th width="40%"><? echo __('Included Databases');?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach($backup_content['databases'] as $database) { if($database['type'] == "mysql") $type = '<span class="label label-warning">'. __('MySQL') .'</span>'; elseif ($database['type'] == "mssql")  $type = '<span class="label label-info">'. __('MSSQL') .'</span>';?>
                            <tr>
                                <td><? echo $type; ?></td>
                                <td><? echo $database['name']; ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <button class="btn btn-sm btn-default btn-block" type="reset"> <? echo __('Cancel'); ?></button>
            <button class="btn btn-sm btn-primary btn-block" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
        </div>
    </form>
</div>

<script>
    core.setWidth({target:'domain-backup-restore-panel'});
    core.inputInits();
    $('input[id="select_all_contents"]').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('#domain-backup-restore-form input[class="checkbox-blue"]').iCheck('check');
        }
        else {
            $('#domain-backup-restore-form input[class="checkbox-blue"]').iCheck('uncheck');
        }
    });
</script>
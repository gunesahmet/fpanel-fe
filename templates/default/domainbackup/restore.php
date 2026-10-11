<form id="domain-backup-restore-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
    <div class="row">
        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Settings');?></h5>
                    <div class="ibox-tools">
                        <label class="label label-danger"><?= __("After it starts, your all data will be deleted. Please make sure your existing data is saved.");?></label>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="select_all_contents"> <strong><?= __('Select All');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <input type="checkbox" class="checkbox-blue" id="select_all_contents" data-select-elements="restore_dns,restore_httpdoc,restore_subdomains,restore_databases,restore_mails">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="restore_dns"> <strong><?= __('Restore DNS Records');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <?= $form->render('restore_dns',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="restore_httpdoc"> <strong><?= __('Restore Ftp Root (httpdoc)');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <?= $form->render('restore_httpdoc',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row bg-light">
                        <div class="col-md-3">
                            <label for="restore_subdomains"> <strong><?= __('Restore Subdomains');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <?= $form->render('restore_subdomains',array('class' => 'checkbox-blue', "data-select-elements" => implode(array_keys($backup_content['subdomains']), ","))); ?>
                        </div>
                    </div>
                    <div class="row">
                        <? foreach($backup_content['subdomains'] as $key => $subdomain) { ?>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-5 offset-1">
                                        <label for="<?= $key;?>"><strong><?= $subdomain;?></strong></label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="checkbox" id="<?= $key;?>" class="checkbox-blue" name="subdomains[]" value="<?= $subdomain;?>"/>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>

                    <div class="form-group row bg-light">
                        <div class="col-md-3">
                            <label for="restore_databases"> <strong><?= __('Restore Databases');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <?= $form->render('restore_databases',array('class' => 'checkbox-blue', "data-select-elements" => implode($backup_content['database_names'], ","))); ?>
                        </div>
                    </div>
                    <div class="row">
                    <? foreach($backup_content['databases'] as $k => $database) { ?>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-5 offset-1">
                                    <label for="<?= $backup_content['database_names'][$k];?>"><strong><?= $database["name"];?> (<?= DATABASE_TYPES[$database['type']]; ?>)</strong></label>
                                </div>
                                <div class="col-md-6">
                                    <input type="checkbox" id="<?= $backup_content['database_names'][$k];?>" class="checkbox-blue" name="databases[]" value="<?= $database["name"];?>"/>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                    </div>

                    <div class="form-group row bg-light">
                        <div class="col-md-3">
                            <label for="restore_mails"> <strong><?= __('Restore Mails');?></strong></label>
                        </div>
                        <div class="col-md-3">
                            <?= $form->render('restore_mails',array('class' => 'checkbox-blue', "data-select-elements" => implode($backup_content['mail_names'], ","))); ?>
                        </div>
                    </div>
                    <div class="row">
                    <? foreach($backup_content['mail_accounts'] as $mail) { ?>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-5 offset-1">
                                    <label for="<?= md5($mail['mail_name']. "@". $domain_name);?>"><strong><?= $mail['mail_name']."@".$domain_name;?></strong></label>
                                </div>
                                <div class="col-md-6">
                                    <input type="checkbox" id="<?= md5($mail['mail_name']. "@". $domain_name);?>" class="checkbox-blue" name="mails[]" value="<?= $mail["mail_name"];?>"/>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Start'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Contents');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><?= __('Content');?></th>
                            <th><?= __('Status');?></th>
                            <th><?= __("Total Size");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= __('FTP Root (httpdoc, error-docs and cgi-bin folders)');?></td>
                            <td><? if($backup_content['content']['www_content'] == ON) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                            <td><?= $this->env->convert($backup_content['sizes']["httpdoc"]);?></td>
                        </tr>
                        <tr>
                            <td><?= __('Databases');?></td>
                            <td><? if($backup_content['content']['database_content'] == ON) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                            <td><?= $this->env->convert(array_sum($backup_content['sizes']["databases"]));?></td>
                        </tr>
                        <tr>
                            <td><?= __('DNS Records');?></td>
                            <td><? if($backup_content['content']['dns_content'] == ON) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                            <td>0 B</td>
                        </tr>
                        <tr>
                            <td><?= __('Mail Accounts');?></td>
                            <td><? if($backup_content['content']['mail_content'] == ON) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                            <td><?= $this->env->convert(array_sum($backup_content['sizes']["mails"]));?></td>
                        </tr>
                        <tr>
                            <td><?= __('Subdomains');?></td>
                            <td><? if($backup_content['content']['subdomain_content'] == ON) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                            <td><?= $this->env->convert(array_sum($backup_content['sizes']["subdomains"]));?></td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th width="40%"><?= __('Included Subdomains');?></th>
                            <th><?= __("Size");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach($backup_content['subdomains'] as $subdomain) { ?>
                            <tr>
                                <td><?= $subdomain; ?></td>
                                <td><?= $this->env->convert($backup_content['sizes']["subdomains"][str_replace(".".$domain_name, "", $subdomain)]);?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th width="40%"><?= __('Included Mail Accounts');?></th>
                            <th><?= __("Size");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach($backup_content['mail_accounts'] as $mail) { ?>
                            <tr>
                                <td><?= $mail['mail_name']."@".$domain_name; ?></td>
                                <td><?= $this->env->convert($backup_content['sizes']["mails"][$mail['mail_name']]);?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th width="40%"><?= __('Included Databases');?></th>
                            <th><?= __('Old Name');?></th>
                            <th><?= __('New Name');?></th>
                            <th><?= __('Size');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach($backup_content['databases'] as $database) {
                            if($database['type'] == "mysql")
                                $type = '<span class="label label-primary">'. __('MySQL') .'</span>';
                            else if ($database['type'] == "mssql")
                                $type = '<span class="label label-success">'. __('MSSQL') .'</span>';
                            else if ($database['type'] == "pgsql")
                                $type = '<span class="label label-default">'. __('PostgreSQL') .'</span>';
                            ?>
                            <tr>
                                <td><?= $type; ?></td>
                                <td><?= $database['name']; ?></td>
                                <td><?= $domain_prefix ."_". $database["name"]; ?></td>
                                <td><?= $this->env->convert($backup_content['sizes']["databases"][$database['type']."_".$database["name"].".dump"]);?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
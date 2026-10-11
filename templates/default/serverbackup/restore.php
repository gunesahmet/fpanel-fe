<form id="server-restore-form" method="post" class="form-horizontal">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-lg-7">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Restore Options');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_all"> <strong><?= __('Select All');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('restore_all',['class' => 'checkbox-blue']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_domain_ftproot"> <strong><?= __('Restore Domains FTP Root');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('restore_domain_ftproot',['class' => 'checkbox-blue']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_subdomains"> <strong><?= __('Restore Domain Subdomains');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('restore_subdomains',['class' => 'checkbox-blue']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_databases"> <strong><?= __('Restore Domain Databases');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('restore_databases',['class' => 'checkbox-blue']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_dns"> <strong><?= __('Restore Domain DNS Records');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('restore_dns',['class' => 'checkbox-blue']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_mails"> <strong><?= __('Restore Domain Mails');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('restore_mails',['class' => 'checkbox-blue']); ?>
                        </div>
                    </div>

                    <hr />

                    <div class="tabbable-line">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="active"><a href="#server-settings" data-toggle="tab"><?= __('Server Settings');?></a></li>
                            <li><a href="#selectable-users" data-toggle="tab"><?= __('Users');?> &amp; <?= __('Domains');?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="server-settings" class="tab-pane active">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label for="restore_cronjobs"> <strong><?= __('Restore Cronjobs');?></strong></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('restore_cronjobs',['class' => 'checkbox-blue']); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label for="restore_blacklists"> <strong><?= __('Restore Blacklists');?></strong></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('restore_blacklists',['class' => 'checkbox-blue']); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label for="restore_dns_server"> <strong><?= __('Restore DNS Settings');?></strong></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('restore_dns_server',['class' => 'checkbox-blue']); ?>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label for="restore_mail_server_settings"> <strong><?= __('Restore Mail Server Settings');?></strong></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('restore_mail_server_settings',['class' => 'checkbox-blue']); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label for="restore_spam_settings"> <strong><?= __('Restore Spam Settings');?></strong></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('restore_spam_settings',['class' => 'checkbox-blue']); ?>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label for="restore_spam_list"> <strong><?= __('Restore Spam Lists');?></strong></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->render('restore_spam_list',['class' => 'checkbox-blue']); ?>
                                    </div>
                                </div>
                            </div>
                            <div id="selectable-users" class="tab-pane">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?= $form->render('domain_list',array('class' => 'multiselect')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-11">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Overview');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><?= __('Backup Name');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $config['backup_data']['name']; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><?= __('Description');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $config['backup_data']['description']; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><?= __('Created Date');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $this->env->convertDate($config['backup_data']['created_date']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><?= __('Backup Size');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $config['backup_data']['sizes']; ?>
                        </div>
                    </div>



                    <hr class="separator" />

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><?= __('Server Backup Contains');?></strong></label>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <th><?= __('Content');?></th>
                                    <th><?= __('Status');?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?= __('Server Settings');?></td>
                                    <td><?= "<span class='label label-success'>".__('Included')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><?= __('Client Accounts');?></td>
                                    <td><?= "<span class='label label-success'>".__('Included')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><?= __('Domains');?></td>
                                    <td><? if(!empty($config['selected_domains'])) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><?= __('Domain FTP Root Directories');?></td>
                                    <td><? if($config['backup_data']['content']['www_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><?= __('Subdomains');?></td>
                                    <td><? if($config['backup_data']['content']['subdomain_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><?= __('Databases');?></td>
                                    <td><? if($config['backup_data']['content']['database_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><?= __('DNS Settings');?></td>
                                    <td><? if($config['backup_data']['content']['dns_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><?= __('Mail Accounts');?></td>
                                    <td><? if($config['backup_data']['content']['mail_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
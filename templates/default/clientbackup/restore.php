<form id="client-restore-form" method="post" class="form-horizontal">
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
                            <?= $form->render('restore_dns', ['class' => 'checkbox-blue']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_mails"> <strong><?= __('Restore Domain Mails');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('restore_mails', ['class' => 'checkbox-blue']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="divider"><span></span><span> <?= __('Users');?> &amp; <?= __('Domains');?> </span><span></span></div>
                        </div>

                        <div class="col-md-12">
                            <?= $form->render('domain_list',array('class' => 'multiselect','data-selectable' => __('Available'),'data-selected' => __('Selected'))); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-11">
                            <div class="float-right">
                                <button type="reset" class="btn btn-default btn-sm"> <?= __('Cancel');?> </button>
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
                            <?= $this->env->convert($backup_content['size']['backup_size']); ?>
                        </div>
                    </div>

                    <hr class="separator" />

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><?= __('Backup Content');?></strong></label>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th><?= __('Content');?></th>
                                        <th><?= __('Status');?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
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
                                        <td><? if($config['backup_data']['content']['www_content'] == true) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                    </tr>
                                    <tr>
                                        <td><?= __('Subdomains');?></td>
                                        <td><? if($config['backup_data']['content']['subdomain_content'] == true) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                    </tr>
                                    <tr>
                                        <td><?= __('Databases');?></td>
                                        <td><? if($config['backup_data']['content']['database_content'] == true) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                    </tr>
                                    <tr>
                                        <td><?= __('DNS Settings');?></td>
                                        <td><? if($config['backup_data']['content']['dns_content'] == true) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                    </tr>
                                    <tr>
                                        <td><?= __('Mail Accounts');?></td>
                                        <td><? if($config['backup_data']['content']['mail_content'] == true) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>

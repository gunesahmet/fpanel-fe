<form id="domain-backup-create-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
    <div class="row">
        <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
        <div class="col-md-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Backup Features');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <div class="col-md-5">
                            <label for="backup_name"> <?= __('Backup Name');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('backup_name',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-5">
                            <label for="description"> <?= __('Backup Description');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('description',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-5">
                            <label for="backup_repo"> <?= __('Backup Location');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('backup_repo',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>

                    </div>

                    <div class="form-group row ftp_repos">
                        <div class="col-md-5">
                            <label for="repo"> <?= __('FTP Repos');?> </label>
                        </div>
                        <div class="col-md-6">
                            <select name="repo" id="repo" class="form-control form-control-sm">
                                <? foreach($ftp_repos as $repo) { ?>
                                    <option value="<?= $repo['id'];?>"><?= $repo['ftp_server'];?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row cloud_storages">
                        <div class="col-md-5">
                            <label for="storages"> <?= __('Cloud Storages');?> </label>
                        </div>
                        <div class="col-md-6">
                            <select name="storages" id="storages" class="form-control form-control-sm">
                                <? foreach($storages as $storage) { ?>
                                    <option value="<?= $storage['id'];?>"><?= $storage['name'];?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-5">
                            <label for="send_mail"> <?= __('When complete backup send email');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('send_mail',array('class' => 'checkbox-blue', "data-sub-elements" => "email_address")); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-5">
                            <label for="email_address"> <?= __('Email Address');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('email_address',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-11">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
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
                    <h5><?= __('Settings');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="all_content"> <?= __('All');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <?= $form->render('all_content',array('class' => 'checkbox-blue', "data-select-elements" => "domain_settings,www_content,subdomain_content,database_content,dns_content,mail_content")); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="domain_settings"> <?= __('Domain Settings');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <?= $form->render('domain_settings',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="www_content"> <?= __('FTP Directories');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <?= $form->render('www_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="subdomain_content"> <?= __('Subdomains');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <?= $form->render('subdomain_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="database_content"> <?= __('Databases');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <?= $form->render('database_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="dns_content"> <?= __('DNS Settings');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <?= $form->render('dns_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="mail_content"> <?= __('Mail Addresses');?> </label>
                        </div>
                        <div class="col-md-4">
                            <div class="uniformjs">
                                <?= $form->render('mail_content',array('class' => 'checkbox-blue')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .ftp_repos,.cloud_storages {
        display: none;
    }
</style>

<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
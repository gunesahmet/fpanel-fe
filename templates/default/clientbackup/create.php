<form id="client-backup-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-lg-7">
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
                        <div class="col-md-6">
                            <label for="backup_name"> <?= __('Backup Name');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('backup_name',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="description"> <?= __('Backup Description');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('description',array('class' => 'form-control form-control-sm', 'row' => '5')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="backup_repo"> <?= __('Backup Location');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('backup_repo',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>

                    </div>

                    <div class="form-group row ftp_repos">
                        <div class="col-md-6">
                            <label for="repo"> <?= __('FTP Repos');?> </label>
                        </div>
                        <div class="col-md-6">
                            <select name="repo" id="repo" class="form-control form-control-sm input-sm">
                                <? foreach($ftp_repos as $repo) { ?>
                                    <option value="<?= $repo['id'];?>"><?= $repo['ftp_server'];?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row cloud_storages">
                        <div class="col-md-6">
                            <label for="storages"> <?= __('Cloud Storages');?> </label>
                        </div>
                        <div class="col-md-6">
                            <select name="storages" id="storages" class="form-control form-control-sm input-sm">
                                <? foreach($storages as $storage) { ?>
                                    <option value="<?= $storage['id'];?>"><?= $storage['name'];?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="send_mail"> <?= __('When complete backup send email');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('send_mail',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="email_address"> <?= __('Email Address');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('email_address',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-default btn-sm" type="reset"> <?= __('Cancel');?> </button>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="include_all"> <?= __('Select All');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('include_all',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <i class="fa fa-angle-right"></i>
                            <label for="www_content"> <?= __('FTP Root'). ' (httpdoc) ' . __('Directory');?>   </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('www_content',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-md-6">
                            <i class="fa fa-angle-right"></i>
                            <label for="subdomain_content"> <?= __('Subdomains');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('subdomain_content',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <i class="fa fa-angle-right"></i>
                            <label for="database_content"> <?= __('Databases');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('database_content',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <i class="fa fa-angle-right"></i>
                            <label for="dns_content"> <?= __('DNS Settings');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('dns_content',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <i class="fa fa-angle-right"></i>
                            <label for="mail_content"> <?= __('Mail Addresses');?> </label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('mail_content',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Users'); ?> &amp; <?= __('Domain'); ?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="select_all_client"> <?= __('Select All');?> </label>
                        </div>
                        <div class="col-md-8">
                            <input type="checkbox" class="checkbox-blue" id="select_all_client"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <?= $form->render('client_list',array('class' => 'multiselect')); ?>
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
    #description {
        resize: none;
    }
</style>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>

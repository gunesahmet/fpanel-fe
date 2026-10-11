<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <form id="create-client-repo-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
            <div class="row">
                <div class="col-md-6">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="ftp_server"> <?= __('FTP Server Host');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('ftp_server',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="ftp_user"> <?= __('FTP User');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('ftp_user',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="ftp_folder"> <?= __('FTP Folder');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">/</span>
                                <?= $form->render('ftp_folder',array('class' => 'form-control form-control-sm input-sm'));?>
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="ftp_pass"> <?= __('FTP Password');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-sm" id="input-group-password">
                                <?= $form->render("ftp_pass",array('class' => 'form-control form-control-sm')) ?>
                                <span class="input-group-append">
                            <button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button>
                        </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="status"> <?= __('Status');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('status',array('class' => 'checkbox-blue'));?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>

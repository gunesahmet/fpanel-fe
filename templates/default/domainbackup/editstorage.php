<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-md-6">
                <form id="create-clientbackup-storage-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="storages"> <?= __('Storage Type');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('storages',array('class' => 'form-control form-control-sm input-sm select-picker'));?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="storage_name"> <?= __('Storage Name');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('storage_name',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group row" id="dropbox">
                        <div class="col-md-4">
                            <label for="access_token"> <?= __('Access Token');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('access_token',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="folder"> <?= __('Folder');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">/</span>
                                <?= $form->render('folder',array('class' => 'form-control form-control-sm input-sm'));?>
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
                </form>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
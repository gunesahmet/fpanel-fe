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

                    <div class="form-group row dropbox">
                        <div class="col-md-4">
                            <label for="access_token"> <?= __('Access Token');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('access_token',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>
                    <!-- GOOGLE FORM BEGIN -->
                    <div class="form-group row gdrive" style="display: none;">
                        <div class="col-md-4">
                            <label for="project_id"> <?= __('Project ID');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('project_id',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group row gdrive" style="display: none;">
                        <div class="col-md-4">
                            <label for="private_key"> <?= __('Private Key');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('private_key',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group row gdrive" style="display: none;">
                        <div class="col-md-4">
                            <label for="private_key_id"> <?= __('Private Key ID');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('private_key_id',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group row gdrive" style="display: none;">
                        <div class="col-md-4">
                            <label for="client_email"> <?= __('Client Email');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('client_email',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group row gdrive" style="display: none;">
                        <div class="col-md-4">
                            <label for="client_id"> <?= __('Client ID');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('client_id',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>

                    <div class="form-group row gdrive" style="display: none;">
                        <div class="col-md-4">
                            <label for="client_cert_url"> <?= __('Client Certificate URL');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('client_cert_url',array('class' => 'form-control form-control-sm input-sm'));?>
                        </div>
                    </div>
                    <div class="divider gdrive" style="display: none;"><span></span><span> <?= __("OR")?> </span><span></span></div>

                    <div class="form-group row gdrive" style="display: none;">
                        <div class="col-md-4">
                            <label for="json_data"> <?= __('JSON Data');?> <br />(<b class="text-danger"><?= __("Paste here your service account credential json file");?></b>)</label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('json_data',array('class' => 'form-control form-control-sm input-sm', "style" => "resize: vertical;", "rows" => "10"));?>
                        </div>
                    </div>
                    <!-- GOOGLE FORM END -->

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
            <div class="col-md-5">
                <div class="row margin-top-10 dropbox">
                    <p>
                        <?= __('If you want upload to dropbox storage when backup complete you need create an app from').' <a href="https://www.dropbox.com/developers/apps?_tk=pilot_lp&_ad=topbar4&_camp=myapps" target="_blank">'.__('here').'</a>';?>
                        <br />
                        <strong class="text-danger"><?= __('Important Note: ');?></strong> <?= __('After create app you need generate new access token');?>
                    </p>

                </div>
                <div class="row margin-top-10 gdrive" style="display: none">
                    <p>
                        <?= __('If you want upload to dropbox storage when backup complete you need create a service account from').' <a href="https://console.cloud.google.com/projectselector2/iam-admin/serviceaccounts?supportedpurview=project" target="_blank">'.__('here').'</a>';?>
                        <br />
                        <strong class="text-danger"><?= __('Important Note: ');?></strong> <?= __('You need create a project before creating service account.');?> <?= __('This will give a new google drive account.');?> <?= __('If you want upload backup on your personal drive, you have to create a directory on your personal drive and share with this service account (When you sharing, you must use your service account email).');?>

                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
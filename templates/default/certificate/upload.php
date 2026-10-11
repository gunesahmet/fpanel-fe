<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
            <form id="ssl-install-form" method="post" class="form-horizontal" enctype="multipart/form-data" action="<?= $post_url;?>">
                <div class="row">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="crt_name_text"> <strong><?= __('SSL Name');?></strong></label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('crt_name_text',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="pri_key"> <strong><?= __('Private Key');?></strong></label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('pri_key',array('class' => 'form-control form-control-sm', "rows" => "10")); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="crt"> <strong><?= __('Certificate');?></strong></label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('crt',array('class' => 'form-control form-control-sm', "rows" => "10")); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="ca_crt"> <strong><?= __('CA Certificate');?></strong></label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('ca_crt',array('class' => 'form-control form-control-sm', "rows" => "10")); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="float-right">
                                    <button type="reset" class="btn btn-default btn-sm"> <?= __('Cancel');?> </button>
                                    <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="crt_name_file"> <strong><?= __('SSL Name');?></strong></label>
                            </div>
                            <div class="col-md-8">
                                <?= $form->render('crt_name_file',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="crt"> <strong><?= __('Private Key');?></strong></label>
                            </div>
                            <div class="col-md-8">
                                <input type="file" class="file" data-show-preview="false" name="privateKeyFile" id="privateKeyFile">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="crt"> <strong><?= __('Certificate');?></strong></label>
                            </div>
                            <div class="col-md-8">
                                <input type="file" class="file" data-show-preview="false" name="crtFile" id="crtFile">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="crt"> <strong><?= __('CA Certificate');?></strong></label>
                            </div>
                            <div class="col-md-8">
                                <input type="file" class="file" data-show-preview="false" name="cacrtFile" id="cacrtFile">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="float-right">
                                    <button type="reset" class="btn btn-default btn-sm"> <?= __('Cancel');?> </button>
                                    <button class="btn btn-primary btn-sm" type="button" onclick="core.ajaxFilePost(this)"><i class="fa fa-upload"></i> <?= __('Send'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    </div>
</div>
<style>
    textarea {
        resize: vertical; /* user can resize vertically, but width is fixed */
    }
</style>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>

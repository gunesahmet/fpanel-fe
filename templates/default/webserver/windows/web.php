<form id="web-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="form-group row">
        <div class="col-md-2">
            <label for="dynamic_compression"> <strong><?= __('Dynamic Compression');?></strong></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('dynamic_compression',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2">
            <label for="static_compression"> <strong><?= __('Static Compression');?></strong></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('static_compression',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2">
            <label for="compress_file_size"> <strong><?= __('Only compress files larger than this size');?></strong></label>
        </div>
        <div class="col-md-3">
            <div class="input-group input-group-sm" data-autoclose="true">
                <?= $form->render('compress_file_size',array('class' => 'form-control form-control-sm input-sm')); ?>
                <span class="input-group-addon">MB</span>
            </div>
        </div>
    </div>


    <div class="form-group row">
        <div class="col-md-2">
            <label for="per_application_pool_status"> <strong><?= __('Use disk space Per Application Pool');?></strong></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('per_application_pool_status',array('class' => 'checkbox-blue', "data-sub-elements" => "per_application_pool")); ?>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2">
            <label for="per_application_pool"> <strong><?= __('Per Application Pool Disk Space Limit (MB)');?></strong></label>
        </div>
        <div class="col-md-3">
            <div class="input-group input-group-sm" data-autoclose="true">
                <?= $form->render('per_application_pool',array('class' => 'form-control form-control-sm input-sm')); ?>
                <span class="input-group-addon">MB</span>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-5">
            <div class=" float-right">
                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>

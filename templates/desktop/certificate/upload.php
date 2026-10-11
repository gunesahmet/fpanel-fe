<form id="ssl-install-form" method="post" class="form-horizontal" enctype="multipart/form-data" action="<? echo $post_url;?>">
    <div class="col-sm-6">
        <div class="form-group">
            <div class="col-md-4">
                <label for="crt_name_text"> <strong><? echo __('SSL Name');?></strong></label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('crt_name_text',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label for="pri_key"> <strong><? echo __('Private Key');?></strong></label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('pri_key',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label for="crt"> <strong><? echo __('Certificate');?></strong></label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('crt',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label for="ca_crt"> <strong><? echo __('CA Certificate');?></strong></label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('ca_crt',array('class' => 'form-control input-sm')); ?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <div class="pull-right">
                    <button type="reset" class="btn btn-default btn-sm"> <? echo __('Cancel');?> </button>
                    <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="col-md-4">
                <label for="crt_name_file"> <strong><? echo __('SSL Name');?></strong></label>
            </div>
            <div class="col-md-8">
                <? echo $form->render('crt_name_file',array('class' => 'form-control input-sm')); ?>
                <p id="error_crt_name" class="error help-block error-block"><span class="label label-danger"></span></p>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label for="crt"> <strong><? echo __('Private Key');?></strong></label>
            </div>
            <div class="col-md-8">
                <input type="file" class="btn btn-primary btn-sm" name="privateKeyFile" id="privateKeyFile">
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label for="crt"> <strong><? echo __('Certificate');?></strong></label>
            </div>
            <div class="col-md-8">
                <input type="file" class="btn btn-primary btn-sm" name="crtFile" id="crtFile">
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label for="crt"> <strong><? echo __('CA Certificate');?></strong></label>
            </div>
            <div class="col-md-8">
                <input type="file" class="btn btn-primary btn-sm" name="cacrtFile" id="cacrtFile">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <div class="pull-right">
                    <button type="reset" class="btn btn-default btn-sm"> <? echo __('Cancel');?> </button>
                    <button class="btn btn-primary btn-sm" type="button" onclick="core.ajaxFilePost(this)"><i class="fa fa-upload"></i> <? echo __('Send'); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="system-notify-form" class="form-horizontal" method="post" enctype="multipart/form-data" action="<? echo $this->uri->url_encrypt('system/certificate')?>">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-6">
                    <label for="activate_certificate"><? echo __('Activate Uploaded Certificate');?></label>
                </div>
                <div class="col-md-6">
                    <? echo $form->render('activate_certificate',array('class' => 'checkbox-blue')); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6">
                    <label for="hostname"><? echo __('Hostname');?></label>
                </div>
                <div class="col-md-6">
                    <? echo $form->render('hostname',array('class' => 'form-control input-sm')); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6">
                    <label for="pri_key"><? echo __('Private Key');?></label>
                </div>
                <div class="col-md-6">
                    <? echo $form->render('pri_key',array('id' => 'file_input_csr')); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6">
                    <label for="crt"><? echo __('Certificate');?></label>
                </div>
                <div class="col-md-6">
                    <? echo $form->render('crt',array('id' => 'file_input_crt')); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6">
                    <label for="ca_crt"><? echo __('CA Bundle Certificate');?></label>
                </div>
                <div class="col-md-6">
                    <? echo $form->render('ca_crt',array('id' => 'file_input_ca_crt')); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <div class="pull-right">
                        <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                        <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <? if($uploaded_certificate != false) { ?>
                <h2><? echo __('Uploaded Certificate');?></h2>
                <div class="well"><? echo $uploaded_certificate['pri_key'];?></div>
                <div class="well"><? echo $uploaded_certificate['crt'];?></div>
                <div class="well"><? echo $uploaded_certificate['ca_crt'];?></div>
            <? } ?>
        </div>
    </div>
</form>
<script>
    core.inputInits();
</script>
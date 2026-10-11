<form id="webserver-settings-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="file_extension"> <? echo __('Mime Type File Extension');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('file_extension',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="mimetype_octet"> <? echo __('Mime Type Application Type');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('mimetype_octet',array('class' => 'form-control input-sm'));?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            <div class=" pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
<script>
    core.inputInits();
</script>
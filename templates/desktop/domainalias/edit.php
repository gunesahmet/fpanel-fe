<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="alias_name" class="strong"><? echo __('Domain Alias Name');?></label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('alias_name',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="activate_mail_service" class="strong"><? echo __('Activate Mail Service');?></label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('activate_mail_service',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="activate_web_service" class="strong"><? echo __('Activate Web Service');?></label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('activate_web_service',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
<script>
    core.inputInits();
</script>

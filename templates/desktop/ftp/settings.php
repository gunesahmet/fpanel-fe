<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="timeout_idle"> <strong><? echo __('Timeout Idle');?></strong></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('timeout_idle',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="timeout_no_transfer"> <strong><? echo __('Timeout No Transfer');?></strong></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('timeout_no_transfer',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="maxinstances"> <strong><? echo __('Max Instances');?></strong></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('maxinstances',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="max_clients"> <strong><? echo __('Max Clients');?></strong></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('max_clients',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="welcome_message"> <strong><? echo __('Welcome Message');?></strong></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('welcome_message',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
<script>
    core.inputInits();
</script>
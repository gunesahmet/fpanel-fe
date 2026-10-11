<form id="web-settings-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-2">
            <label for="keep_alive"> <? echo __('Keep Alive');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('keep_alive',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="keepalive_timeout"> <? echo __('Keep Alive Timeout');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('keepalive_timeout',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="timeout"> <? echo __('Server Timeout');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('timeout',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="max_keepalive_req"> <? echo __('Max. Keep Alive Requests');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('max_keepalive_req',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="start_server"> <? echo __('Start Server');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('start_server',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="min_spare_server"> <? echo __('Min. Spare Server');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('min_spare_server',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="max_client"> <? echo __('Max. Client');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('max_client',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="max_req_per_child"> <? echo __('Max. Requests Per Child');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('max_req_per_child',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-5">
            <div class=" pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
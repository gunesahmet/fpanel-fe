<div class="form-group row">
    <div class="col-md-2">
        <label for="keep_alive"> <?= __('Keep Alive');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('keep_alive',array('class' => 'checkbox-blue')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="keepalive_timeout"> <?= __('Keep Alive Timeout');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('keepalive_timeout',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="timeout"> <?= __('Server Timeout');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('timeout',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="max_keepalive_req"> <?= __('Max. Keep Alive Requests');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('max_keepalive_req',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="start_server"> <?= __('Start Server');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('start_server',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="min_spare_server"> <?= __('Min. Spare Server');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('min_spare_server',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="max_client"> <?= __('Max. Client');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('max_client',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="max_req_per_child"> <?= __('Max. Requests Per Child');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('max_req_per_child',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>

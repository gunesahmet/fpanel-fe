<h4 class="heading-arrow"><?= __('Session Settings');?></h4>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('Authentication Mode');?></span>
    </div>
    <div class="col-md-7">
        <?= $form->render('auth_mode',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('Session Timeout');?></span>
    </div>
    <div class="col-md-7">
        <?= $form->render('sessionTimeOut',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
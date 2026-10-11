<h4 class="heading-arrow"><?= __('Compilation and Debugging');?></h4>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('Default Language');?></span>
    </div>
    <div class="col-md-2">
        <?= $form->render('defaultLang',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('Switch Debugging');?></span>
    </div>
    <div class="col-md-4">
        <?= $form->render('switchDebug',array('class' => 'checkbox-blue'));?>
    </div>
</div>

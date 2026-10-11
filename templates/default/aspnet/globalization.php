<h4 class="heading-arrow"><?= __('Globalization Settings');?></h4>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('Request Encoding');?></span>
    </div>
    <div class="col-md-7">
        <?= $form->render('requestEncoding',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('Response Encoding');?></span>
    </div>
    <div class="col-md-7">
        <?= $form->render('responseEncoding',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('File Encoding');?></span>
    </div>
    <div class="col-md-7">
        <?= $form->render('fileEncoding',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('Culture');?></span>
    </div>
    <div class="col-md-7">
        <?= $form->render('culture',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('UI Culture');?></span>
    </div>
    <div class="col-md-7">
        <?= $form->render('ui_culture',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>

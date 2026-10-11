<h4 class="heading-arrow"><?= __('Code Access Security');?></h4>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('.NET Trust Level');?></span>
    </div>
    <div class="col-md-7">
        <?= $form->render('trust_level',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>

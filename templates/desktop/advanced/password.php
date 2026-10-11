<form id="system-notify-form" class="form-horizontal" method="post" action="<? echo $this->uri->url_encrypt('system/passwordsettings')?>">
    <div class="form-group">
        <div class="col-md-6">
            <label for="alpha_chk"><? echo __('Alphabetic Characters');?></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('alpha_chk',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-6">
            <label for="numeric_chk"><? echo __('Numeric Characters');?></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('numeric_chk',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            <label for="special_chk"><? echo __('Special Characters');?></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('special_chk',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            <label for="minimum_len"><? echo __('Minimum Length');?></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('minimum_len',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            <label for="maximum_len"><? echo __('Maximum Length');?></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('maximum_len',array('class' => 'form-control input-sm')); ?>
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
</form>
<script>
    core.inputInits();
</script>

<form id="webserver-settings-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-2">
            <label for="ssl_support"> <? echo __('SSL Support');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('ssl_support',array('class' => 'checkbox-blue')); ?>
            <p id="error_ssl_support" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="php_support"> <? echo __('PHP Support');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('php_support',array('class' => 'checkbox-blue')); ?>
            <p id="error_php_support" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="php_extension"> <? echo __('PHP Extension');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('php_extension',array('class' => 'form-control input-sm')); ?>
            <p id="error_php_extension" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="perl_support"> <? echo __('Perl Support');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('perl_support',array('class' => 'checkbox-blue')); ?>
            <p id="error_perl_support" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="perl_extension"> <? echo __('Perl Extension');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('perl_extension',array('class' => 'form-control input-sm')); ?>
            <p id="error_perl_extension" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="python_support"> <? echo __('Python Support');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('python_support',array('class' => 'checkbox-blue')); ?>
            <p id="error_python_support" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="python_extension"> <? echo __('Python Extension');?></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('python_extension',array('class' => 'form-control input-sm')); ?>
            <p id="error_python_extension" class="error help-block error-block"><span class="label label-danger"></span></p>
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
<script>
    core.inputInits();
</script>
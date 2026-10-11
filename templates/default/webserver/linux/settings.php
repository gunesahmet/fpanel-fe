<form id="webserver-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="form-group">
        <div class="col-md-2">
            <label for="ssl_support"> <?= __('SSL Support');?></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('ssl_support',array('class' => 'checkbox-blue')); ?>
            <p id="error_ssl_support" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="php_support"> <?= __('PHP Support');?></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('php_support',array('class' => 'checkbox-blue')); ?>
            <p id="error_php_support" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="php_extension"> <?= __('PHP Extension');?></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('php_extension',array('class' => 'form-control form-control-sm input-sm')); ?>
            <p id="error_php_extension" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="perl_support"> <?= __('Perl Support');?></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('perl_support',array('class' => 'checkbox-blue')); ?>
            <p id="error_perl_support" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="perl_extension"> <?= __('Perl Extension');?></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('perl_extension',array('class' => 'form-control form-control-sm input-sm')); ?>
            <p id="error_perl_extension" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="python_support"> <?= __('Python Support');?></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('python_support',array('class' => 'checkbox-blue')); ?>
            <p id="error_python_support" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>

    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="python_extension"> <?= __('Python Extension');?></label>
        </div>
        <div class="col-md-3">
            <?= $form->render('python_extension',array('class' => 'form-control form-control-sm input-sm')); ?>
            <p id="error_python_extension" class="error help-block error-block"><span class="label label-danger"></span></p>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-5">
            <div class=" float-right">
                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
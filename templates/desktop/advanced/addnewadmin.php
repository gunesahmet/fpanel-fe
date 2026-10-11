<form id="system-notify-form" class="form-horizontal" method="post" action="<? echo $this->uri->url_encrypt('system/addnewadmin')?>">
    <div class="form-group">
        <div class="col-md-6">
            <label for="username"><? echo __('Username');?></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('username',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            <label for="email"><? echo __('Email Address');?></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('email',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            <label for="password"> <? echo __('Password');?> </label>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-sm" id="input-group-password">
                <? echo $form->render("password",array('class' => 'form-control')) ?>
                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-6">
            <label for="confirm-password"> <? echo __('Confirm Password'); ?> </label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-6">
            <label for="site_secret"><? echo __('Fullname');?></label>
        </div>
        <div class="col-md-6">
            <? echo $form->render('fullname',array('class' => 'form-control input-sm')); ?>
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
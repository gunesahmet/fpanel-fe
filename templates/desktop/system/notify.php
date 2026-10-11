<form id="system-notify-form" class="form-horizontal" method="post" action="<? echo $this->uri->url_encrypt('system/notify')?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="host"> <? echo __('Mail Server Host');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('host',array('class' => 'form-control input-sm'));?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="port"> <? echo __('Mail Server Port');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('port',array('class' => 'form-control input-sm'));?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="from_fullname"> <? echo __('From Name');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('from_fullname',array('class' => 'form-control input-sm'));?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="email"> <? echo __('Email Address');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('email',array('class' => 'form-control input-sm'));?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="password"> <? echo __('Password');?> </label>
        </div>
        <div class="col-md-8">
            <div class="input-group input-group-sm" id="input-group-password">
                <? echo $form->render("password",array('class' => 'form-control')) ?>
                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="email"> <? echo __('Confirm Password');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render("password",array('id' => 'confirm-password' , 'name' => 'confirm-password','class' => 'form-control input-sm')) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
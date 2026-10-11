<form id="create-subdomain-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="support_status"> <? echo __('Support Status'); ?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('support_status',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="disable_logins"> <? echo __('Disable Client Logins'); ?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('disable_logins',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="request_user_email"> <? echo __('Email Address for Contact'); ?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('request_user_email',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="username"> <? echo __('Support Username'); ?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('username',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="password"> <? echo __('Support User Password'); ?> </label>
        </div>
        <div class="col-md-8">
            <div class="input-group input-group-sm" id="input-group-password">
                <? echo $form->render('password',array('class' => 'form-control')); ?>
                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="title"> <? echo __('Support Message Title'); ?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('title',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="support_message"> <? echo __('Support Message'); ?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('support_message',array('class' => 'form-control','style' => 'resize:none')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>

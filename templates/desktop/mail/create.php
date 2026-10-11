<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="mail_account"> <? echo __('Mail Address');?> </label>
        </div>
        <div class="col-md-8">
            <div class="input-group input-group-sm" id="input-group-mail_account">
                <? echo $form->render('mail_account',array('class' => 'form-control')); ?>
                <span class="input-group-addon">@<? echo $domain_name; ?></span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="password"> <? echo __('Mail Password');?> </label>
        </div>
        <div class="col-md-8">
            <div class="input-group input-group-sm" id="input-group-mail_password">
                <? echo $form->render("mail_password",array('class' => 'form-control')) ?>
                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="username"> <? echo __('Confirm Password'); ?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('mail_password',array('id' => 'confirm-password', 'name' => 'confirm-password','class' => 'form-control input-sm')); ?>
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-4">
            <label for="unlimited_quota"> <? echo __('Unlimited');?> </label>
        </div>
        <div class="col-md-8">
            <div class="uniformjs">
                <? echo $form->render('unlimited_quota',array('class' => 'checkbox-blue','checked' => 'checked')); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="mail_quota"> <? echo __('Mail Quota');?> (MB)</label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('mail_quota',array('class' => 'form-control input-sm','disabled' => 'disabled'));?>
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
    $('input[name="unlimited_quota"]').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('#mail_quota').prop('disabled', true);
        }
        else {
            $('#mail_quota').prop('disabled', false);
        }
    });
</script>
<form id="server-log-settings-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="periodic_del"> <? echo __('Periodically Delete Logs');?></label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('periodic_del',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="store_time"> <? echo __('Keep Logs for (days)');?></label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('store_time',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="log_compress"> <? echo __('Log Compression');?></label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('log_compress',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="send_information"> <? echo __('Send Notification with Email');?></label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('send_information',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="email_address"> <? echo __('Email Address');?></label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('email_address',array('class' => 'form-control input-sm')); ?>
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
<script>
    core.inputInits();
    $('#send_information').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('#email_address').prop('disabled', false);
        }
        else {
            $('#email_address').prop('disabled', true);
        }
    });
</script>
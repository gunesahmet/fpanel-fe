<form id="server-time-settings-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="update_time"><? echo __('Update System Time');?> </label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('update_time',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="system_date"><? echo __('Date (YYYY/MM/DD)');?> </label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('system_date',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="system_time"><? echo __('Time (HH:MM:SS)');?> </label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('system_time',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <hr />

    <div class="form-group">
        <div class="col-md-4">
            <label for="time_zone"><? echo __('Your Timezone');?> </label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('time_zone',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <hr />
    <div class="form-group">
        <div class="col-md-4">
            <label for="sync_time"><? echo __('Synchronize System Time');?> </label>
        </div>
        <div class="col-md-8">
            <? echo $form->render('sync_time',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <label for="timezone_addr"><? echo __('Domain Name or IP Address');?> </label>
        </div>

        <div class="col-md-8">
            <? echo $form->render('timezone_addr',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>
    <hr />
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
    $('#sync_time').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('#timezone_addr').prop('disabled', false);
        }
        else {
            $('#timezone_addr').prop('disabled', true);
        }
    });
</script>
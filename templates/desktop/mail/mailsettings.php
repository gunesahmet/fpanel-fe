<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-6">
            <label for="forward_to_address"><? echo __('Forward to Address');?></label>
        </div>
        <div class="col-md-6">
            <label> <? echo $form->render('forward_to_address',array('class' => 'checkbox-blue')); ?> </label>
            <div class="input-group input-group-sm" id="input-group-mail_address">
                <? echo $form->render('mail_address',array('class' => 'form-control input-sm')); ?>
                <span class="input-group-addon">@<? echo $domain_name;?></span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            <label for="reject_incoming"><? echo __('Reject');?></label>
        </div>
        <div class="col-md-6">
            <?
            echo '<label>';
            echo $form->render('reject_incoming',array('class' => 'checkbox-blue'));
            echo '</label>';
            ?>
        </div>
    </div>
    <div class="col-md-12">
        <div class="pull-right">
            <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
            <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
        </div>
    </div>
</form>

<script>
    core.inputInits();
    $('input[id="forward_to_address"]').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('#mail_address').prop('disabled', false);
        }
        else {
            $('#mail_address').prop('disabled', true);
        }
    });
</script>
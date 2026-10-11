<form id="mailserver-general-settings-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <? $elements = array(
        'hostname'	            => array('type' => 'text', 'display_name' => __('Hostname')),
        'max_message_size'	    => array('type' => 'text', 'display_name' => __('Maximum Message Size')),
        'queue_life_time'	    => array('type' => 'text', 'display_name' => __('Queue Life-Time')),
        'concurrency_in_que'	=> array('type' => 'text', 'display_name' => __('Concurrency Connections')),
    //								'concurrency_out_que'	=> array('type' => 'text', 'display_name' => __('Concurrency Outgoing Queue')),
        'max_recipients'	    => array('type' => 'text', 'display_name' => __('Maximum Recipients')),
        'submission_port'	    => array('type' => 'checkbox', 'display_name' => __('Enable Submission Port (587)')),
        'delete_double_msg'	    => array('type' => 'checkbox', 'display_name' => __('Delete DoubleBounce Messages')),
    //								'imap_with_smtp'	    => array('type' => 'checkbox', 'display_name' => __('Enable POP3/IMAP Authentication with SMTP Auth')),
    //								'smtp_auth'	            => array('type' => 'checkbox', 'display_name' => __('Enable SMTP Authentication')),
        'spf_protection'	    => array('type' => 'checkbox','display_name' => __('Enable SPF Spam Protection')),

        'spf_check_mode'	    => array('type' => 'selectbox','display_name' => __('SPF Checking Mode')),

        'spf_exp'	            => array('type' => 'text','display_name' => __('SPF Explanation Text')),
        'rbl_status'	        => array('type' => 'checkbox', 'display_name' => __('RBL Status')),
        'add_rbl_list'	        => array('type' => 'text', 'display_name' => __('Add RBL list')),
        'rbl_list'	            => array('type' => 'selectbox', 'display_name' => __('RBL list')),
    );
    foreach($elements as $key => $value) { ?>
        <div class="form-group">
            <div class="col-md-3">
                <label for="<? echo $key;?>">  <? echo $value['display_name'];?></label>
            </div>
            <div class="col-md-4">
                <? if($value['type'] == "checkbox") {?>
                    <? echo $form->render($key,array('class' => 'checkbox-blue')); ?>
                <? } else { ?>
                    <? echo $form->render($key,array('class' => 'form-control input-sm')); ?>
                <? } ?>
            </div>
            <? if($key == "add_rbl_list") { ?>
                <div class="col-md-1">
                    <a id="add_to_rbl_list" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i></a>
                    <a id="remove_from_rbl_list" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>
                </div>
            <? }?>
        </div>
    <? } ?>
    <br />
    <br />
    <div class="form-group">
        <div class="col-md-7">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>

<script>
    core.inputInits();
    if($('#rbl_status').is(':checked')) {
        $('#add_rbl_list').prop('disabled',false);
        $('#rbl_list').prop('disabled',false);
    }
    else {
        $('#add_rbl_list').prop('disabled',true);
        $('#rbl_list').prop('disabled',true);
    }
    $('#rbl_status').on('click', function () {
        if($('#rbl_status').is(':checked')) {
            $('#add_rbl_list').prop('disabled',false);
            $('#rbl_list').prop('disabled',false);
        }
        else {
            $('#add_rbl_list').prop('disabled',true);
            $('#rbl_list').prop('disabled',true);
        }
    });
    $('#add_to_rbl_list').click(function(){
        var allowed_ip = $('#add_rbl_list').val();
        $('#rbl_list').append("<option value='"+ allowed_ip +"'>"+ allowed_ip +"</option>");
        $('#add_rbl_list').val('');
    });
    $('#remove_from_rbl_list').click(function(){
        $('#rbl_list option:selected').each( function() {
            $(this).remove();
        });
    });
    function selectAllList(){
        for (var i = 0; i < document.getElementById("rbl_list").options.length; i++) {
            document.getElementById("rbl_list").options[i].selected = true;
        }
    }
</script>




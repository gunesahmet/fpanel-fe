<div class="row" id="server-php-settings-panel" data-width="40%">
    <form id="edit-phpsettings-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <?
        $elements = array(
            'date_timezone'	        => array('display_name'	=> __('Date Timezone')),
            'memory_limit'	        => array('display_name'	=> __('Memory Limit')),
            'max_execution_time'	=> array('display_name'	=> __('Max Execution Time')),
            'max_input_time'	    => array('display_name'	=> __('Max Input Time')),
            'post_max_size'	        => array('display_name'	=> __('Post Max. File Size')),
            'upload_max_filesize'	=> array('display_name'	=> __('Upload Max File Size')),
            'file_uploads'	        => array('display_name'	=> __('File Upload')),
            'register_globals'	    => array('display_name'	=> __('Register Global')),
            'magic_quotes_gpc'	    => array('display_name'	=> __('Magic Quotes GPC')),
            'display_errors'	    => array('display_name'	=> __('Display Errors')),
            'log_errors'	        => array('display_name'	=> __('Log Errors')),
            'allow_url_fopen'	    => array('display_name'	=> __('Allow Url Fopen')),
            'short_open_tag'	    => array('display_name'	=> __('Shot Open Tag')),
            'session_save_path'	    => array('display_name'	=> __('Session Save Path')),
        );
        foreach ($elements as $key => $value) {
            ?>
            <div class="form-group">
                <div class="col-md-4 col-xs-12">
                    <label for="<? echo $key; ?>"> <? echo $value['display_name']; ?></label>
                </div>
                <div class="col-md-8 col-xs-12">
                    <div class="col-md-6 col-xs-6">
                        <? echo $form->render($key,array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <label><? echo $form->render($key.'_chk',array('class' => 'checkbox-blue')); ?> <? echo __('Allowed');?></label>
                    </div>
                </div>
            </div>
        <? } ?>
        <div class="form-group">
            <div class="col-md-8">
                <div class="pull-right">
                    <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                    <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    core.setWidth({
        target: 'server-php-settings-panel'
    });
    core.inputInits();
</script>
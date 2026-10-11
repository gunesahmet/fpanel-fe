<form id="web-settings-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-2">
            <label for="ftp_messages"> <strong><? echo __('FTP Messages');?></strong></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('ftp_messages',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-2">
            <label for="add_hidden_segments"> <strong><? echo __('Add Hidden Segments');?></strong></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('add_hidden_segments',array('class' => 'form-control input-sm')); ?>
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary btn-xs" type="button" id="segment-add"><i class="fa fa-plus-circle"></i></button>
            <button class="btn btn-danger btn-xs" type="button" id="segment-remove"><i class="fa fa-minus-circle"></i></button>
        </div>

    </div>

    <div class="form-group">
        <div class="col-md-2">
            <label for="hidden_segments"> <strong><? echo __('Hidden Segments');?></strong></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('hidden_segments',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-2">
            <label for="dynamic_compression"> <strong><? echo __('Dynamic Compression');?></strong></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('dynamic_compression',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-2">
            <label for="static_compression"> <strong><? echo __('Static Compression');?></strong></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('static_compression',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label for="compress_file_size"> <strong><? echo __('Static Compression Size');?></strong></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('compress_file_size',array('class' => 'form-control input-sm','disabled' => 'disabled')); ?>
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-2">
            <label for="per_application_pool_status"> <strong><? echo __('Use disk space Per Application Pool');?></strong></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('per_application_pool_status',array('class' => 'checkbox-blue')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-2">
            <label for="per_application_pool"> <strong><? echo __('Per Application Pool Disk Space Limit (MB)');?></strong></label>
        </div>
        <div class="col-md-3">
            <? echo $form->render('per_application_pool',array('class' => 'form-control input-sm')); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-5">
            <div class=" pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>

<script>
    core.inputInits();
    function selectAllSegments(){
        for (var i = 0; i < document.getElementById("hidden_segments").options.length; i++) {
            document.getElementById("hidden_segments").options[i].selected = true;
        }
    }

    $('#static_compression').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('#compress_file_size').prop('disabled', false);
        }
        else {
            $('#compress_file_size').prop('disabled', true);
        }
    });

    $('#per_application_pool_status').on('ifChecked ifUnchecked', function(event) {
        if (event.type == 'ifChecked') {
            $('#per_application_pool').prop('disabled', false);
        }
        else {
            $('#per_application_pool').prop('disabled', true);
        }
    });

</script>
<form method="post" autocomplete="off" class="form-horizontal">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><? echo __('Database Server Settings');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" >
                                <div class="col-md-4">
                                    <label class="control-label" for="allow_remote_connection"> <? echo __('Allow Remote Connection');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('allow_remote_connection',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-4">
                                    <label class="control-label" for="buffer_pool_size"> <? echo __('Buffer Pool Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('buffer_pool_size',array('class' => 'form-control')); ?>
                                </div>
                            </div>

                            <div class="form-group" >
                                <div class="col-md-4">
                                    <label class="control-label" for="log_file_size"> <? echo __('Log File Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('log_file_size',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-4">
                                    <label class="control-label" for="file_per_table"> <? echo __('File Per Table');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('file_per_table',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-4">
                                    <label class="control-label" for="flush_method"> <? echo __('Flush Method');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('flush_method',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-4">
                                    <label class="control-label" for="log_error"> <? echo __('Error Log File');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('log_error',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-4">
                                    <label class="control-label" for="slow_query_log"> <? echo __('Slow Query Log');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('slow_query_log',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="control-label" for="slow_query_log_file"> <? echo __('Slow Query Log File');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('slow_query_log_file',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" >
                                <div class="col-md-3">
                                    <label class="control-label" for="tmp_table_size"> <? echo __('TMP Table Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('tmp_table_size',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-3">
                                    <label class="control-label" for="max_heap_table_size"> <? echo __('Max Heap Table Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('max_heap_table_size',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-3">
                                    <label class="control-label" for="query_cache_type"> <? echo __('Query Cache Type');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('query_cache_type',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-3">
                                    <label class="control-label" for="query_cache_size"> <? echo __('Query Cache Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('query_cache_size',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-3">
                                    <label class="control-label" for="max_connections"> <? echo __('Max Connections');?> (<? echo  __('Integer');?>)</label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('max_connections',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-3">
                                    <label class="control-label" for="thread_cache_size"> <? echo __('Thread Cache Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('thread_cache_size',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-3">
                                    <label class="control-label" for="table_open_cache"> <? echo __('Table Open Cache');?> (<? echo  __('Integer');?>) </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('table_open_cache',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group" >
                                <div class="col-md-3">
                                    <label class="control-label" for="open_files_limit"> <? echo __('Open Files Limit');?> (<? echo  __('Integer');?>) </label>
                                </div>
                                <div class="col-md-8">
                                    <? echo $form->render('open_files_limit',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
    if($('#slow_query_log').val() == "on") {
        $('#slow_query_log_file').prop('disabled',false);
    }
    else {
        $('#slow_query_log_file').prop('disabled',true);
    }
    $('#slow_query_log').on('click', function () {
        if($('#slow_query_log').val() == "on") {
            $('#slow_query_log_file').prop('disabled',false);
        }
        else {
            $('#slow_query_log_file').prop('disabled',true);
        }
    });
</script>
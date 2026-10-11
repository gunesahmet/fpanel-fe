<form method="post" autocomplete="off" class="form-horizontal">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Database Server Settings');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="control-label" for="allow_remote_connection"> <?= __('Allow Remote Connection');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('allow_remote_connection',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="control-label" for="buffer_pool_size"> <?= __('Buffer Pool Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm" data-autoclose="true">
                                        <?= $form->render('buffer_pool_size',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="control-label" for="log_file_size"> <?= __('Log File Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm" data-autoclose="true">
                                        <?= $form->render('log_file_size',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="control-label" for="file_per_table"> <?= __('File Per Table');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('file_per_table',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="control-label" for="flush_method"> <?= __('Flush Method');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('flush_method',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="control-label" for="log_error"> <?= __('Error Log File');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('log_error',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="control-label" for="slow_query_log"> <?= __('Slow Query Log');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('slow_query_log',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="control-label" for="slow_query_log_file"> <?= __('Slow Query Log File');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('slow_query_log_file',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="control-label" for="tmp_table_size"> <?= __('TMP Table Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm" data-autoclose="true">
                                        <?= $form->render('tmp_table_size',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="control-label" for="max_heap_table_size"> <?= __('Max Heap Table Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm" data-autoclose="true">
                                        <?= $form->render('max_heap_table_size',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="control-label" for="query_cache_type"> <?= __('Query Cache Type');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('query_cache_type',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="control-label" for="query_cache_size"> <?= __('Query Cache Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm" data-autoclose="true">
                                        <?= $form->render('query_cache_size',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="control-label" for="max_connections"> <?= __('Max Connections');?> (<?=  __('Integer');?>)</label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('max_connections',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="control-label" for="thread_cache_size"> <?= __('Thread Cache Size');?> (MB) </label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm" data-autoclose="true">
                                        <?= $form->render('thread_cache_size',array('class' => 'form-control form-control-sm')); ?>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="control-label" for="table_open_cache"> <?= __('Table Open Cache');?> (<?=  __('Integer');?>) </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('table_open_cache',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="control-label" for="open_files_limit"> <?= __('Open Files Limit');?> (<?=  __('Integer');?>) </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('open_files_limit',array('class' => 'form-control form-control-sm')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
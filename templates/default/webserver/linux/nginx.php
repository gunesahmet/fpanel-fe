<div class="form-group row">
    <div class="col-md-2">
        <label for="worker_processes"> <?= __('Worker Process');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('worker_processes',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="worker_connections"> <?= __('Worker Connections');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('worker_connections',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="multi_accept"> <?= __('Multi Accept');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('multi_accept',array('class' => 'make-switch','data-size' => 'mini')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="sendfile"> <?= __('Send File');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('sendfile',array('class' => 'make-switch','data-size' => 'mini')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="tcp_nopush"> <?= __('TCP No Push');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('tcp_nopush',array('class' => 'make-switch','data-size' => 'mini')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="tcp_nodelay"> <?= __('TCP No Delay');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('tcp_nodelay',array('class' => 'make-switch','data-size' => 'mini')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="keepalive_timeout"> <?= __('Keep Alive Timeout');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('keepalive_timeout',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="types_hash_max_size"> <?= __('Types Hash Max. Size');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('types_hash_max_size',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="server_tokens"> <?= __('Server Tokens');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('server_tokens',array('class' => 'make-switch','data-size' => 'mini')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="server_names_hash_bucket_size"> <?= __('Server Names hash Bucket Size');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('server_names_hash_bucket_size',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="server_name_in_redirect"> <?= __('Server Name In Redirect');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('server_name_in_redirect',array('class' => 'make-switch','data-size' => 'mini')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="gzip"> <?= __('Gzip Support');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('gzip',array('class' => 'make-switch','data-size' => 'mini')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="gzip_disable"> <?= __('Gzip Disabled For');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('gzip_disable',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="gzip_vary"> <?= __('Gzip Vary');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('gzip_vary',array('class' => 'make-switch','data-size' => 'mini')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="gzip_proxied"> <?= __('Gzip Proxied');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('gzip_proxied',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="gzip_comp_level"> <?= __('Gzip Compression Level');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('gzip_comp_level',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="gzip_buffers"> <?= __('Gzip Buffers');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('gzip_buffers',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="gzip_http_version"> <?= __('Gzip HTTP Version');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('gzip_http_version',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-2">
        <label for="gzip_types"> <?= __('Gzip Types');?></label>
    </div>
    <div class="col-md-3">
        <?= $form->render('gzip_types',array('class' => 'form-control form-control-sm input-sm')); ?>
    </div>
</div>
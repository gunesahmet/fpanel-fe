<div class="row">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $this->uri->url_encrypt('blacklist/addftpuser');?>" type="button"><i class="fa fa-plus-circle"></i> <? echo __('Add Ftp Users'); ?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="blacklist-ftpuser-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('blacklist/ftpusers');?>">
                <thead>
                <tr>
                    <th width="60%"><? echo __('FTP Users');?></th>
                    <th width="40%"><? echo __('Actions');?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.inputInits();
    core.datatable({
        target : '#blacklist-ftpuser-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>

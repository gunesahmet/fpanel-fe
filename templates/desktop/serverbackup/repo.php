<div class="row" id="server-repo-panel" data-width="50%">
    <div class="col-md-12 margin-bottom-10">
        <a class="btn btn-sm btn-primary" data-id="nested"  href="<? echo $this->uri->url_encrypt("serverbackup/repo/create");?>" ><i class="fa fa-plus-circle"></i> <? echo __('Add New Repo'); ?></a>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="server-repo-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt("serverbackup/repo");?>">
                <thead>
                <tr>
                    <th width="15%"><? echo __('FTP Server Name'); ?></th>
                    <th width="10%"><? echo __('FTP User'); ?></th>
                    <th width="10%"><? echo __('Status'); ?></th>
                    <th width="20%"><? echo __('Actions'); ?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'server-repo-panel'
    });
    core.datatable({
        target : '#server-repo-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>

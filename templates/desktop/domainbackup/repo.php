<?
$list_url = $this->uri->url_encrypt("backup/repo");
$create_url = $this->uri->url_encrypt("backup/repo/create");
if(isset($domain_id)) {
    $list_url = $this->uri->url_encrypt("backup/repo/domain/$domain_id");
    $create_url = $this->uri->url_encrypt("backup/repo/create/domain/$domain_id");
}

?>

<div class="row" id="domain-backup-repo-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Add New Repo');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="domain-backup-repo-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
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
        target: 'domain-backup-repo-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#domain-backup-repo-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });

</script>

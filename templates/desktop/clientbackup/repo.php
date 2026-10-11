<?
$create_url = $this->uri->url_encrypt("backupclient/repo/create");
$list_url = $this->uri->url_encrypt("backupclient/repo");
if(isset($client_id)) {
    $create_url = $this->uri->url_encrypt("backupclient/repo/create/client/$client_id");
    $list_url = $this->uri->url_encrypt("backupclient/repo/client/$client_id");
}
?>

<div class="row" id="client-repo-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Add New Repo');?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="client-storage-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
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
        target: 'client-storage-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#client-storage-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>

<?
$class = "";
$ftp_repo_url = $this->uri->url_encrypt("backupclient/repo");
$create_url = $this->uri->url_encrypt("backupclient/create");
$upload_url = $this->uri->url_encrypt("backupclient/upload");
$storage_url = $this->uri->url_encrypt("backupclient/storage");
$list_url = $this->uri->url_encrypt("backupclient");
if(isset($client_id)) {
    $ftp_repo_url = $this->uri->url_encrypt("backupclient/repo/client/$client_id");
    $create_url = $this->uri->url_encrypt("backupclient/create/client/$client_id");
    $upload_url = $this->uri->url_encrypt("backupclient/upload/client/$client_id");
    $storage_url = $this->uri->url_encrypt("backupclient/storage/client/$client_id");
    $list_url = $this->uri->url_encrypt("backupclient/client/$client_id");
}

if($backup_status == true) {
    $class = " disabled";
    $upload = "#";
    $create_new = "#";
}
?>
<div class="row" id="client-backup-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary<? echo $class;?>" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create New Backup');?></a>
            <a class="btn btn-sm btn-info<? echo $class;?>" data-id="nested" href="<? echo $upload_url;?>"><i class="fa fa-upload"></i> <? echo __('Upload Backup');?></a>
            <a class="btn btn-sm btn-default" data-id="nested" href="<? echo $storage_url;?>"><i class="fa fa-cloud-upload"></i> <? echo __('Cloud Storage');?></a>
            <a class="btn btn-sm btn-default" data-id="nested" href="<? echo $ftp_repo_url;?>"><i class="fa fa-share-alt-square"></i> <? echo __('FTP Repo');?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="client-backup-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="15%"><? echo __('Backup Name'); ?></th>
                    <th width="10%"><? echo __('Description'); ?></th>
                    <th width="10%"><? echo __('Created Date'); ?></th>
                    <th width="10%"><? echo __('Status'); ?></th>
                    <th width="10%"><? echo __('Size'); ?></th>
                    <th width="10%"><? echo __('Repo'); ?></th>
                    <th width="20%"><? echo __('Actions'); ?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'client-backup-list-panel'
    });
    core.datatable({
        target : '#client-backup-table',
        select_row: false,
        buttons: true,
        checkbox: false,
        actions: false
    });

</script>

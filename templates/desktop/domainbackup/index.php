<?
$class = "";
$ftp_repo = $this->uri->url_encrypt("backup/repo");
$create_url = $this->uri->url_encrypt("backup/create");
$upload = $this->uri->url_encrypt("backup/upload");
$storage = $this->uri->url_encrypt("backup/storage");
$list_url = $this->uri->url_encrypt("backup");
if(isset($domain_id)) {
    $ftp_repo = $this->uri->url_encrypt("backup/repo/domain/$domain_id");
    $create_url = $this->uri->url_encrypt("backup/create/domain/$domain_id");
    $upload = $this->uri->url_encrypt("backup/upload/domain/$domain_id");
    $storage = $this->uri->url_encrypt("backup/storage/domain/$domain_id");
    $list_url = $this->uri->url_encrypt("backup/domain/$domain_id");
}

if($backup_status == true) {
    $class = " disabled";
    $upload = "#";
    $create_url = "#";
}
?>

<div class="row" id="domain-backup-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $ftp_repo;?>"><i class="fa fa-share-alt-square"></i> <? echo __('FTP Repos');?></a>
            <a class="btn btn-sm btn-primary <? echo $class;?>" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create New Backup');?></a>
            <a class="btn btn-sm btn-info <? echo $class;?>" data-id="nested" href="<? echo $upload;?>"><i class="fa fa-upload"></i> <? echo __('Upload Backup');?></a>
            <a class="btn btn-sm btn-info<? echo $class;?>" data-id="nested" href="<? echo $storage;?>"><i class="fa fa-cloud-download"></i> <? echo __('Cloud Storage');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="domain-backup-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
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
        target: 'domain-backup-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#domain-backup-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });

</script>


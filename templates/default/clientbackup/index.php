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
<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-md-12 margin-bottom-10">
                <a class="btn btn-sm btn-default" data-id="nested" href="<?= $ftp_repo_url;?>"><i class="fa fa-share-alt-square"></i> <?= __('FTP Repo');?></a>
                <a class="btn btn-sm btn-default" data-id="nested" href="<?= $storage_url;?>"><i class="fa fa-cloud-upload"></i> <?= __('Cloud Storage');?></a>
                <a class="btn btn-sm btn-info<?= $class;?>" data-id="nested" href="<?= $upload_url;?>"><i class="fa fa-upload"></i> <?= __('Upload Backup');?></a>
                <a class="btn btn-sm btn-primary<?= $class;?> float-right" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create New Backup');?></a>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="false" data-buttons="true" data-actions="false">
                    <thead>
                    <tr>
                        <th width="15%"><?= __('Backup Name'); ?></th>
                        <th width="10%"><?= __('Description'); ?></th>
                        <th width="10%"><?= __('Created Date'); ?></th>
                        <th width="10%"><?= __('Status'); ?></th>
                        <th width="10%"><?= __('Size'); ?></th>
                        <th width="10%"><?= __('Repo'); ?></th>
                        <th width="20%" data-orderable="false"><?= __('Actions'); ?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>

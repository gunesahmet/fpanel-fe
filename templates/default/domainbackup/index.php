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
                <a class="btn btn-sm btn-default" data-id="nested" href="<?= $ftp_repo;?>"><i class="fa fa-share-alt-square"></i> <?= __('FTP Repos');?></a>
                <a class="btn btn-sm btn-default<?= $class;?>" data-id="nested" href="<?= $storage;?>"><i class="fa fa-cloud-download"></i> <?= __('Cloud Storage');?></a>
                <a class="btn btn-sm btn-info <?= $class;?>" data-id="nested" href="<?= $upload;?>"><i class="fa fa-upload"></i> <?= __('Upload Backup');?></a>
                <a class="btn btn-sm btn-primary <?= $class;?> float-right" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create New Backup');?></a>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-buttons="true">
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
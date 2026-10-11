<? if(isset($server_id)) $server_link = "/server/$server_id"; else $server_link = "";?>
<?
$list_url = $this->uri->url_encrypt("serverbackup$server_link");
$create_url = $this->uri->url_encrypt("serverbackup/create$server_link");
$upload_url = $this->uri->url_encrypt("serverbackup/upload$server_link");
$storage_url = $this->uri->url_encrypt("serverbackup/storage$server_link");
$repo_url = $this->uri->url_encrypt("serverbackup/repo$server_link");
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
                <a class="btn btn-sm btn-default" data-id="nested" href="<?= $repo_url;?>" ><i class="fa fa-share-alt-square"></i> <?= __('FTP Repos'); ?></a>
                <a class="btn btn-sm btn-default" data-id="nested" href="<?= $storage_url;?>"><i class="fa fa-cloud-upload"></i> <?= __('Cloud Storage');?></a>
                <a class="btn btn-sm btn-info" data-id="nested" href="<?= $upload_url;?>" ><i class="fa fa-upload"></i> <?= __('Upload Backup'); ?></a>
                <a class="btn btn-sm btn-primary float-right" data-id="nested" href="<?= $create_url;?>" ><i class="fa fa-plus-circle"></i> <?= __('Create New Backup'); ?></a>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="false" data-buttons="true" data-actions="false">
                    <thead>
                    <tr>
                        <th width="15%" data-orderable="false"><?= __('Backup Name'); ?></th>
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
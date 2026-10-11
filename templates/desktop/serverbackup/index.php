<? if(isset($server_id)) $server_link = "server/$server_id"; else $server_link = "";?>
<?
$create_url = $this->uri->url_encrypt("serverbackup/create");
$upload_url = $this->uri->url_encrypt("serverbackup/upload");
$list_url = $this->uri->url_encrypt('serverbackup');
if(isset($server_id)) {
    $create_url = $this->uri->url_encrypt("serverbackup/create/server/$server_id");
    $upload_url = $this->uri->url_encrypt("serverbackup/upload/server/$server_id");
}
?>
<div class="row" id="server-backup-panel" data-width="50%">
    <div class="col-md-12 margin-bottom-10">
        <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $this->uri->url_encrypt("serverbackup/repo");?>" ><i class="fa fa-share-alt-square"></i> <? echo __('FTP Repos'); ?></a>
        <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>" ><i class="fa fa-plus-circle"></i> <? echo __('Create New Backup'); ?></a>
        <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $upload_url;?>" ><i class="fa fa-upload"></i> <? echo __('Upload Backup'); ?></a>

    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="server-backup-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
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
        target: 'server-backup-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#server-backup-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>

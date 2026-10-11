<?
$create_url = $this->uri->url_encrypt('dbserver/create');
$db_list_link = $this->uri->url_encrypt('dbserver/databases');
$list_url = $this->uri->url_encrypt('dbserver');
if(isset($server_id)) {
    $create_url = $this->uri->url_encrypt('dbserver/create/server/'.$server_id);
    $db_list_link = $this->uri->url_encrypt('dbserver/databases/server/'.$server_id);
    $list_url = $this->uri->url_encrypt('dbserver/server/'.$server_id);
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
                <div class="float-left">
                    <a class="btn btn-sm btn-primary" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Add Database Server');?></a>
                    <a class="btn btn-sm btn-default" data-id="nested" href="<?= $db_list_link;?>"><i class="fa fa-database"></i> <?= __('Databases');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="false" data-buttons="true" data-actions="false">
                    <thead>
                    <tr>
                        <th width="15%"><?= __('Host')?></th>
                        <th width="10%"><?= __('Port')?></th>
                        <th width="10%"><?= __('Type')?></th>
                        <th width="10%"><?= __('Existing Database')?></th>
                        <th width="10%"><?= __('Status')?></th>
                        <th width="15%" data-orderable="false"><?= __('Actions')?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
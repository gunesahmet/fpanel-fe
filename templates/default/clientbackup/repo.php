<?
$create_url = $this->uri->url_encrypt("backupclient/repo/create");
$list_url = $this->uri->url_encrypt("backupclient/repo");
if(isset($client_id)) {
    $create_url = $this->uri->url_encrypt("backupclient/repo/create/client/$client_id");
    $list_url = $this->uri->url_encrypt("backupclient/repo/client/$client_id");
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
        <div class="row" id="client-repo-list-panel" data-width="70%" data-height="50%">
            <div class="col-md-12 margin-bottom-10">
                <div class="float-left">
                    <a class="btn btn-sm btn-primary" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Add New Repo');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="false" data-buttons="true" data-actions="false">
                    <thead>
                    <tr>
                        <th width="15%"><?= __('FTP Server Name'); ?></th>
                        <th width="10%"><?= __('FTP User'); ?></th>
                        <th width="10%"><?= __('Status'); ?></th>
                        <th width="20%" data-orderable="false"><?= __('Actions'); ?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>

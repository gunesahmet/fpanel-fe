<?
$list_url = $this->uri->url_encrypt("backup/repo");
$create_url = $this->uri->url_encrypt("backup/repo/create");
if(isset($domain_id)) {
    $list_url = $this->uri->url_encrypt("backup/repo/domain/$domain_id");
    $create_url = $this->uri->url_encrypt("backup/repo/create/domain/$domain_id");
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
                    <a class="btn btn-sm btn-primary" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Add New Repo');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-buttons="true">
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
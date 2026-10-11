<?
$multi_url = $this->uri->url_encrypt('dnsserver/multiple');
$list_url = $this->uri->url_encrypt('dnsserver');
$create_url = $this->uri->url_encrypt('dnsserver/create');
if(isset($server_id)) {
    $multi_url = $this->uri->url_encrypt('dnsserver/multiple/server/'.$server_id);
    $list_url = $this->uri->url_encrypt('dnsserver/server/'.$server_id);
    $create_url = $this->uri->url_encrypt('dnsserver/create/server/'.$server_id);
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
                    <a class="btn btn-primary btn-sm" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create New Record');?></a>
                    <a class="btn btn-sm btn-info" data-id="confirm" data-url="<?= $this->uri->url_encrypt($restore_url);?>"><i class="fa fa-exchange"></i> <?= __('Restore Default Records');?></a>
                </div>
                <div class="float-right table-button-lists">
                    <a class="btn btn-sm btn-danger remove table-action disabled" data-single="false" data-type="multiple" data-action="delete" data-target="dns_chk" data-single="false" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                    <thead>
                    <tr>
                        <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                        <th width="25%"><?= __('Host'); ?></th>
                        <th width="10%"><?= __('Type'); ?></th>
                        <th width="35%"><?= __('Value'); ?></th>
                        <th width="20%" data-orderable="false"><?= __('Action'); ?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
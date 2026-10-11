<? if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt('dns/remove/multiple/domain/'.$domain_id);
    $list_url = $this->uri->url_encrypt('dns/domain/'.$domain_id);
    $create_url = $this->uri->url_encrypt("dns/create/domain/".$domain_id);
}
else {
    $multi_url = $this->uri->url_encrypt('dns/remove/multiple');
    $list_url = $this->uri->url_encrypt('dns');
    $create_url = $this->uri->url_encrypt("dns/create");
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
                <div class="table-button-lists">
                    <div class="float-left">
                        <a class="btn btn-primary btn-sm btn-xs-block" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create New Record');?></a>
                    </div>
                    <div class="float-right">
                        <a class="btn btn-sm btn-info btn-xs-block" data-id="confirm" data-url="<?= $restore_url;?>"><i class="fa fa-exchange"></i> <?= __('Restore Default Records');?></a>
                        <a class="btn btn-sm btn-danger remove disabled btn-xs-block table-action" data-single="false" data-type="multiple" data-action="delete" data-target="records" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                    </div>
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
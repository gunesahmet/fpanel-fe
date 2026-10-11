<? if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt('domainalias/multiple/domain/'.$domain_id);
    $list_url = $this->uri->url_encrypt('domainalias/domain/'.$domain_id);
    $create_url = $this->uri->url_encrypt("domainalias/create/domain/".$domain_id);
}
else {
    $multi_url = $this->uri->url_encrypt('domainalias/multiple');
    $list_url = $this->uri->url_encrypt('domainalias');
    $create_url = $this->uri->url_encrypt("domainalias/create");
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
        <div class="row" id="domain-alias-list-panel" data-width="70%" data-height="50%">
            <div class="col-md-12 margin-bottom-10">
                <div class="float-left">
                    <a class="btn btn-sm btn-primary <?= $active_class;?>" data-id="nested" href="<?= $create_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Create New');?></a>
                </div>
                <div class="float-right table-button-lists">
                    <a class="btn btn-sm btn-danger remove table-action disabled" data-type="multiple" data-action="delete" data-target="aliases" data-url="<?= $multi_url;?>"><i class="fa fa-plus-circle"></i> <?= __('Remove Selected');?></a>
                </div>
            </div>
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                    <thead>
                    <tr>
                        <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                        <th width="15%"><?= __('Alias Name')?></th>
                        <th width="10%"><?= __('Mail Status')?></th>
                        <th width="10%"><?= __('Web Status')?></th>
                        <th width="15%" data-orderable="false"><?= __('Actions')?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
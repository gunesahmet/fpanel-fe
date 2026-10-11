<? if(isset($client_id)) {
    $multi_url = $this->uri->url_encrypt('serviceplans/multiple/client/'.$client_id);
    $list_url = $this->uri->url_encrypt('serviceplans/client/'.$client_id);
    $url = $this->uri->url_encrypt("serviceplans/create/client/".$client_id);
}
else {
    $multi_url = $this->uri->url_encrypt('serviceplans/multiple');
    $list_url = $this->uri->url_encrypt('serviceplans');
    $url = $this->uri->url_encrypt("serviceplans/create");
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
            <div class="col-md-12">
                <div class="float-left">
                    <a data-id="nested" class="btn btn-sm btn-primary" href="<?= $url;?>" type="button"><i class="fa fa-plus-circle"></i> <?= __('Create New');?></a>
                </div>
                <div class="float-right table-button-lists">
                    <a class="btn btn-sm btn-danger remove table-action disabled" data-type="multiple" data-action="delete" data-target="serviceplans" data-url="<?= $multi_url;?>"><i class="fa fa-times"></i> <?= __('Remove Selected');?></a>
                </div>
            </div>
            <br />
            <br />
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $list_url;?>" data-select="true" data-buttons="true" data-actions="true">
                    <thead>
                    <tr>
                        <th width="5%" data-orderable="false"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                        <th width="35%"><?= __('Plan Name')?></th>
                        <th width="20%"><?= __('Author')?></th>
                        <th width="10%"><?= __('Is Default')?></th>
                        <th width="10%"><?= __('Overuse')?></th>
                        <th width="20%" data-orderable="false"><?= __('Actions')?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
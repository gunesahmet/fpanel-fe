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
<div class="row" id="named-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-primary btn-sm" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create New Record');?></a>
            <a class="btn btn-sm btn-info" data-id="confirm" data-url="<? echo $restore_url;?>"><i class="fa fa-exchange"></i> <? echo __('Restore Default Records');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-single="false" data-type="multiple" data-action="delete" data-target="records" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="named-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th width="25%"><? echo __('Host'); ?></th>
                    <th width="10%"><? echo __('Type'); ?></th>
                    <th width="35%"><? echo __('Value'); ?></th>
                    <th width="20%"><? echo __('Action'); ?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'named-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#named-table',
        select_row: true,
        buttons: true,
        checkbox: true,
        actions: true,
        action_buttons: {
            0:'remove'
        }
    });
    core.multipleProcess();
</script>

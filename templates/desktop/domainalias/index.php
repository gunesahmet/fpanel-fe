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
<div class="row" id="domain-alias-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary <? echo $active_class;?>" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create New');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="aliases" data-url="<? echo $multi_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="domain-alias-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th width="15%"><? echo __('Alias Name')?></th>
                    <th width="10%"><? echo __('Mail Status')?></th>
                    <th width="10%"><? echo __('Web Status')?></th>
                    <th width="15%"><? echo __('Actions')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'domain-alias-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#domain-alias-table',
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
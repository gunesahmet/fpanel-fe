<? if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt('database/multiple/domain/'.$domain_id);
    $list_url = $this->uri->url_encrypt('database/domain/'.$domain_id);
    $create_url = $this->uri->url_encrypt('database/create/domain/'.$domain_id);
}
else {
    $multi_url = $this->uri->url_encrypt('database/multiple');
    $list_url = $this->uri->url_encrypt('database');
    $create_url = $this->uri->url_encrypt('database/create');
}
$create_url = ($active_class == "disabled" ? '#' : $create_url);

?>
<div class="row" id="databases-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary <? echo $active_class;?>" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create Database');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="databases" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="databases-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th width="15%"><? echo __('Database Name')?></th>
                    <th width="10%"><? echo __('Database Type')?></th>
                    <th width="10%"><? echo __('Database Server')?></th>
                    <th width="10%"><? echo __('Database Username')?></th>
                    <th width="15%"><? echo __('Actions')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'databases-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#databases-table',
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

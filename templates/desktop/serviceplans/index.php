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
<div class="row" id="serviceplans-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a data-id="nested" class="btn btn-sm btn-primary" href="<? echo $url;?>" type="button"><i class="fa fa-plus-circle"></i> <? echo __('Create New');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="serviceplans" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="serviceplans-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th width="35%"><? echo __('Plan Name')?></th>
                    <th width="20%"><? echo __('Author')?></th>
                    <th width="10%"><? echo __('Is Default')?></th>
                    <th width="10%"><? echo __('Overuse')?></th>
                    <th width="20%"><? echo __('Actions')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'serviceplans-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#serviceplans-table',
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
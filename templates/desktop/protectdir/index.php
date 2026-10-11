<? if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt('protectdir/multiple/dir/domain/'.$domain_id);
    $list_url = $this->uri->url_encrypt('protectdir/domain/'.$domain_id);
    $create_url = $this->uri->url_encrypt("protectdir/create/domain/".$domain_id);
}
else {
    $multi_url = $this->uri->url_encrypt('protectdir/multiple/dir');
    $list_url = $this->uri->url_encrypt('protectdir');
    $create_url = $this->uri->url_encrypt("protectdir/create");
}
?>
<div class="row" id="pro-dir-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-primary btn-sm" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create Protect Directory');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="pro_dirs" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="pro-dir-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th width="15%"><? echo __('Directory Path')?></th>
                    <th width="10%"><? echo __('Title of Protect Area')?></th>
                    <th width="10%"><? echo __('Domain')?></th>
                    <th width="15%"><? echo __('Actions')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'pro-dir-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#pro-dir-table',
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
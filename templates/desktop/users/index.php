<? if(isset($client_id)) {
    $multi_url = $this->uri->url_encrypt('users/multiple/client/'.$client_id);
    $list_url = $this->uri->url_encrypt('users/client/'.$client_id);
    $url = $this->uri->url_encrypt("users/create/client/".$client_id);
}
else {
    $multi_url = $this->uri->url_encrypt('users/multiple');
    $list_url = $this->uri->url_encrypt('users');
    $url = $this->uri->url_encrypt("users/create");
}
if($active_class == "disabled") $url = "#";
?>
<div class="row" id="user-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a data-id="nested" class="btn btn-primary btn-sm <? echo $active_class;?>" href="<? echo $url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create New User'); ?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-primary unsuspend disabled" data-type="multiple" data-action="unsuspend" data-target="clients" data-url="<? echo $multi_url;?>"><i class="fa fa-check"></i> <? echo __('Unsuspend Selected');?></a>
            <a class="btn btn-sm btn-warning suspend disabled" data-type="multiple" data-action="suspend" data-target="clients" data-url="<? echo $multi_url;?>"><i class="fa fa-ban"></i> <? echo __('Suspend Selected');?></a>
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="clients" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="users-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th width="15%"><? echo __('Username')?></th>
                    <th width="10%"><? echo __('Parent')?></th>
                    <th width="10%"><? echo __('Expire Date')?></th>
                    <th width="10%"><? echo __('Domains')?></th>
                    <th width="5%"><? echo __('Clients')?></th>
                    <th width="10%"><? echo __('User Type')?></th>
                    <th width="10%"><? echo __('Status')?></th>
                    <th width="15%"><? echo __('Actions')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    core.setWidth({
        target: 'user-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#users-table',
        select_row: true,
        buttons: true,
        checkbox: true,
        actions: true,
        action_buttons: {
            0:'suspend',
            1:'unsuspend',
            2:'remove'
        }
    });
    core.nested();
</script>
<?
$create_url = $this->uri->url_encrypt('dbserver/create');
$db_list_link = $this->uri->url_encrypt('dbserver/databases');
$list_url = $this->uri->url_encrypt('dbserver');
if(isset($server_id)) {
    $create_url = $this->uri->url_encrypt('dbserver/create/server/'.$server_id);
    $db_list_link = $this->uri->url_encrypt('dbserver/databases/server/'.$server_id);
    $list_url = $this->uri->url_encrypt('dbserver/server/'.$server_id);
}
?>

<div class="row" id="dbserver-settings-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create Database');?></a>
            <a class="btn btn-sm btn-default" data-id="nested" href="<? echo $db_list_link;?>"><i class="fa fa-database"></i> <? echo __('Databases');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="db-server-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="15%"><? echo __('Host')?></th>
                    <th width="10%"><? echo __('Port')?></th>
                    <th width="10%"><? echo __('Type')?></th>
                    <th width="10%"><? echo __('Existing Database')?></th>
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
        target: 'dbserver-settings-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#db-server-table',
        select_row: false,
        buttons: true,
        checkbox: false,
        actions: false
    });
    core.multipleProcess();

</script>

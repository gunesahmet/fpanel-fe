<?
$list_url = $this->uri->url_encrypt('dbserver/databases');
if(isset($server_id)) {
    $list_url = $this->uri->url_encrypt('dbserver/databases/'.$server_id);
}
?>

<div class="row" id="db-server-dbs-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="db-server-dbs-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="15%"><? echo __('Database Name')?></th>
                    <th width="10%"><? echo __('Database Type')?></th>
                    <th width="10%"><? echo __('Database Server')?></th>
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
        target: 'db-server-dbs-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#db-server-dbs-table',
        select_row: false,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>


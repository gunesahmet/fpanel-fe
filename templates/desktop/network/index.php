<? $server_link = (isset($server_id) ? "/server/$server_id" : '');
$create_url = $this->uri->url_encrypt("network/create$server_link");
$reread_url = $this->uri->url_encrypt("network/reread$server_link");
$list_url = $this->uri->url_encrypt("network$server_link");
$hostname_url = $this->uri->url_encrypt("network/hostname$server_link");

?>
<div class="row" id="server-network-panel" data-width="50%">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Add New IP Address');?></a>
            <a class="btn btn-sm btn-info" data-id="nested" href="<? echo $hostname_url;?>"><i class="fa fa-edit"></i> <? echo __('Hostname');?></a>
        </div>
        <div class="pull-right">
            <a class="btn btn-sm btn-danger" data-id="confirm" data-url="<? echo $reread_url;?>"><i class="fa fa-recycle"></i> <? echo __('Reread All Interfaces');?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="server-network-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="15%"><? echo __('IP Address')?></th>
                    <th width="10%"><? echo __('Network Interface')?></th>
                    <th width="10%"><? echo __('Used Accounts')?></th>
                    <th width="10%"><? echo __('Used Domains')?></th>
                    <th width="10%"><? echo __('Used Type')?></th>
                    <th width="10%"><? echo __('Default')?></th>
                    <th width="15%"><? echo __('Actions')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'server-network-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#server-network-table',
        select_row: false,
        buttons: true,
        checkbox: false,
        actions: false
    });
    core.multipleProcess();
</script>

<?
$multi_url = $this->uri->url_encrypt('dnsserver/multiple');
$list_url = $this->uri->url_encrypt('dnsserver');
$create_url = $this->uri->url_encrypt('dnsserver/create');
if(isset($server_id)) {
    $multi_url = $this->uri->url_encrypt('dnsserver/multiple/server/'.$server_id);
    $list_url = $this->uri->url_encrypt('dnsserver/server/'.$server_id);
    $create_url = $this->uri->url_encrypt('dnsserver/create/server/'.$server_id);
}
 ?>
<div class="row" id="dnsserver-settings-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-primary btn-sm" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create New Record');?></a>
            <a class="btn btn-sm btn-info" data-id="confirm" data-url="<? echo $this->uri->url_encrypt($restore_url);?>"><i class="fa fa-exchange"></i> <? echo __('Restore Default Records');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-single="false" data-type="multiple" data-action="delete" data-target="dns_chk" data-single="false" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="dns-server-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
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
        target: 'dnsserver-settings-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#dns-server-table',
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

<? if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt('subdomain/multiple/domain/'.$domain_id);
    $list_url = $this->uri->url_encrypt('subdomain/domain/'.$domain_id);
    $create_url = $this->uri->url_encrypt("subdomain/create/domain/".$domain_id);
}
else {
    $multi_url = $this->uri->url_encrypt('subdomain/multiple');
    $list_url = $this->uri->url_encrypt('subdomain');
    $create_url = $this->uri->url_encrypt("subdomain/create");
}
$create_url = ($active_class == "disabled" ? '#' : $create_url);
?>
<div class="row" id="subdomains-list-panel" data-width="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-primary btn-sm  <? echo $active_class;?>" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create Subdomain');?></a>
        </div>
        <div id="multiple_process" class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="subdomains" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="subdomains-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" class="checkbox-aero" id="select_all" /></th>
                    <th width="15%"><? echo __('Subdomain Name')?></th>
                    <th width="10%"><? echo __('Created Date')?></th>
                    <th width="10%"><? echo __('Ftp User Type')?></th>
                    <th width="10%"><? echo __('FTP Username')?></th>
                    <th width="5%"><? echo __('Status')?></th>
                    <th width="15%"><? echo __('Actions')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<script>
    core.setWidth({
        target: 'subdomains-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#subdomains-table',
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
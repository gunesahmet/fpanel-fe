<? if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt('certificate/multiple/domain/'.$domain_id);
    $list_url = $this->uri->url_encrypt('certificate/domain/'.$domain_id);
    $create_url = $this->uri->url_encrypt("certificate/create/domain/".$domain_id);
    $upload_url = $this->uri->url_encrypt("certificate/upload/domain/".$domain_id);
}
else {
    $multi_url = $this->uri->url_encrypt('certificate/multiple');
    $list_url = $this->uri->url_encrypt('certificate');
    $create_url = $this->uri->url_encrypt("certificate/create");
    $upload_url = $this->uri->url_encrypt("certificate/upload");
}
?>
<div class="row" id="domain-certificate-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Request New CSR');?></a>
            <a class="btn btn-sm btn-info" data-id="nested" href="<? echo $upload_url;?>"><i class="fa fa-upload"></i> <? echo __('Upload SSL Certificate');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="certificates" data-url="<? echo $multi_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="domain-certificate-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th width="15%"><? echo __('Certificate Name')?></th>
                    <th width="10%"><? echo __('Use www')?></th>
                    <th width="10%"><? echo __('SSL Name')?></th>
                    <th width="15%"><? echo __('Actions')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'domain-certificate-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#domain-certificate-table',
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

<?
$multi_url = $this->uri->url_encrypt("mail/multiple/alias");
$list_url = $this->uri->url_encrypt("mail/alias/$mail_id");
$create_url = $this->uri->url_encrypt("mail/createalias/$mail_id");
if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt("mail/multiple/alias/domain/$domain_id");
    $list_url = $this->uri->url_encrypt("mail/alias/$mail_id/domain/$domain_id");
    $create_url = $this->uri->url_encrypt("mail/createalias/$mail_id/domain/".$domain_id);
}
?>

<div class="row" id="domain-mailalias-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create Mail Account');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="aliases" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="domain-mailalias-list-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th width="60%"><? echo __('Alias Name');?></th>
                    <th width="20%"><? echo __('Action');?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'domain-mailalias-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#domain-mailalias-list-table',
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

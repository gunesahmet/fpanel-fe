<?
$create_url = $this->uri->url_encrypt("backup/storage/create");
$list_url = $this->uri->url_encrypt("backup/storage");
if(isset($this->domain_id)) {
    $create_url = $this->uri->url_encrypt("backup/storage/create/domain/$domain_id");
    $list_url = $this->uri->url_encrypt("backup/storage/domain/$domain_id");
}
?>

<div class="row" id="domain-storage-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Add New Storage');?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="domain-storage-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="15%"><? echo __('Storage Name'); ?></th>
                    <th width="10%"><? echo __('Type'); ?></th>
                    <th width="10%"><? echo __('Status'); ?></th>
                    <th width="20%"><? echo __('Actions'); ?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'domain-storage-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#domain-storage-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>

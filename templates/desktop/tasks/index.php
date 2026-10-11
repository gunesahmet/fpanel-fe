<?
$multi_url = $this->uri->url_encrypt("tasks/multiple");
$list_url = $this->uri->url_encrypt("tasks");
$create_url = $this->uri->url_encrypt("tasks/create");
if(isset($domain_id)) {
    $multi_url = $this->uri->url_encrypt("tasks/multiple/domain/$domain_id");
    $list_url = $this->uri->url_encrypt("tasks/domain/$domain_id");
    $create_url = $this->uri->url_encrypt("tasks/create/domain/$domain_id");
}

?>
<div class="row" id="domain-tasks-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create Task');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="tasks" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="domain-tasks-list-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th width="40%"><? echo __('Command'); ?></th>
                    <th width="30%"><? echo __('Description'); ?></th>
                    <th width="10%"><? echo __('Status'); ?></th>
                    <th width="15%"><? echo __('Action'); ?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    core.setWidth({
        target: 'domain-tasks-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#domain-tasks-list-table',
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
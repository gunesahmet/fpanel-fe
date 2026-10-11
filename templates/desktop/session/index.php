<div class="row" id="system-messages-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $this->uri->url_encrypt('session/settings');?>"><i class="fa fa-cogs"></i> <? echo __('Session Settings'); ?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="sessions" data-url="<? echo $remove_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="sessions-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('session');?>">
                <thead>
                <tr>
                    <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                    <th><? echo __('Username'); ?></th>
                    <th><? echo __('IP Address'); ?></th>
                    <th><? echo __('Logged Time'); ?></th>
                    <th><? echo __('Idle Time'); ?></th>
                    <th><? echo __('Action'); ?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    core.setWidth({
        target: 'session-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#sessions-table',
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

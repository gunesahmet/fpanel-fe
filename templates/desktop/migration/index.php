<div class="row" id="server-migration-panel" data-width="50%">
    <div class="col-md-12">
        <? if(isset($server_id)) { ?>
            <a class="btn btn-sm btn-primary" href="<? echo $this->uri->url_encrypt('migration/create/server/'.$server_id);?>" type="button"><i class="fa fa-plus-circle"></i> <? echo __('Add Server for Migration'); ?></a>
        <? } else { ?>
            <a class="btn btn-sm btn-primary" href="<? echo $this->uri->url_encrypt('migration/create');?>" type="button"><i class="fa fa-plus-circle"></i> <? echo __('Add Server for Migration'); ?></a>
        <? } ?>
    </div>

    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="server-migration-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('migration');?>">
                <thead>
                <tr>
                    <th width="15%"><? echo __('Type'); ?></th>
                    <th width="10%"><? echo __('IP Address'); ?></th>
                    <th width="10%"><? echo __('Description'); ?></th>
                    <th width="10%"><? echo __('Created Date'); ?></th>
                    <th width="10%"><? echo __('Last Migration Date'); ?></th>
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
        target: 'server-migration-panel'
    });
    core.datatable({
        target : '#server-migration-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>

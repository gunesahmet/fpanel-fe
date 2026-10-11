<? $server_link = (isset($server_id) ? "/server/$server_id" : '');?>
<div class="row" id="server-cron-list-panel" data-width="45%" data-height="50%">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $this->uri->url_encrypt("cron/create$server_link");?>"><i class="fa fa-plus-circle"></i> <? echo __('Create Cronjob');?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="server-crons-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('cron');?>">
                <thead>
                    <tr>
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
        target: 'server-cron-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#server-crons-table',
        select_row: false,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>
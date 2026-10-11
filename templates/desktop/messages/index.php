<div class="row" id="system-messages-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12 margin-bottom-10">
        <div class="pull-left">
            <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $this->uri->url_encrypt('messages/create');?>" type="button"><i class="fa fa-plus-circle"></i> <? echo __('Add New'); ?></a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="system-messages-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('messages');?>">
                <thead>
                <tr>
                    <th width="15%"><? echo __('Title')?></th>
                    <th width="10%"><? echo __('Description')?></th>
                    <th width="10%"><? echo __('Created Date')?></th>
                    <th width="15%"><? echo __('Expire Date')?></th>
                    <th width="15%"><? echo __('Type')?></th>
                    <th width="15%"><? echo __('Status')?></th>
                    <th width="15%"><? echo __('Actions')?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    core.setWidth({
        target: 'system-messages-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#system-messages-table',
        select_row: true,
        buttons: true,
        checkbox: false,
        actions: false
    });
</script>


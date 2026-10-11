<div class="row">
    <div class="col-md-12 margin-bottom-10">
        <a data-id="confirm" class="btn btn-sm btn-primary" data-url="<? echo $update_source_url;?>" type="button"><i class="fa fa-refresh"></i> <? echo __('Update Source Tree');?></a>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="server-packages-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('packages');?>">
                <thead>
                    <tr>
                        <th width="35%"><? echo __('Package Name')?></th>
                        <th width="20%"><? echo __('Description')?></th>
                        <th width="10%"><? echo __('Version')?></th>
                        <th width="10%"><? echo __('Status')?></th>
                        <th width="20%"><? echo __('Support')?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    core.datatable({
        target : '#server-packages-table',
        select_row: false,
        buttons: false,
        checkbox: false,
        actions: false
    });
    core.inputInits();
</script>
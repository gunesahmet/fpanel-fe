<? if(isset($client_id)) {
    $multi_url = $this->uri->url_encrypt('domains/multiple/client/'.$client_id);
    $list_url = $this->uri->url_encrypt('domains/client/'.$client_id);
    $create_url = $this->uri->url_encrypt('domains/create/client/'.$client_id);
}
else {
    $multi_url = $this->uri->url_encrypt('domains/multiple');
    $list_url = $this->uri->url_encrypt('domains');
    $create_url = $this->uri->url_encrypt('domains/create');
} ?>
<div class="row" id="domain-list-panel" data-width="70%" data-height="50%">
    <div class="col-md-12">
        <div class="pull-left">
            <a class="btn btn-primary btn-sm" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create New');?></a>
        </div>
        <div class="pull-right table-button-lists">
            <a class="btn btn-sm btn-primary disabled change-parent" data-toggle="modal" id="change_parent" data-target="#parent_modal"><i class="fa fa-exchange"></i> <? echo __('Change Parent');?></a>
            <a class="btn btn-sm btn-primary suspend disabled" data-type="multiple" data-action="unsuspend" data-target="domains" data-url="<? echo $multi_url;?>"><i class="fa fa-check"></i> <? echo __('Unsuspend Selected');?></a>
            <a class="btn btn-sm btn-warning unsuspend disabled" data-type="multiple" data-action="suspend" data-target="domains" data-url="<? echo $multi_url;?>"><i class="fa fa-ban"></i> <? echo __('Suspend Selected');?></a>
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="domains" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
        </div>
    </div>
    <br />
    <br />
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="domains-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
                <thead>
                    <tr>
                        <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                        <th width="15%"><? echo __('Domain Name')?></th>
                        <th width="10%"><? echo __('Owner')?></th>
                        <th width="5%"><? echo __('Created Date')?></th>
                        <th width="5%"><? echo __('Expire Date')?></th>
                        <th width="10%"><? echo __('IP Address')?></th>
                        <th width="5%"><? echo __('Status')?></th>
                        <th width="10%"><? echo __('Disk')?></th>
                        <th width="10%"><? echo __('Traffic')?></th>
                        <th width="25%"><? echo __('Actions')?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div id="parent_modal" class="modal fade" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3><? echo __('Change Domain Parent');?> </h3>
        <small class="font-bold"><? echo __('Please select a client as parent assing to selected domains');?> </small>
    </div>
    <div class="modal-body">
        <div class="row form-horizontal">
            <div class="form-group">
                <label class="control-label col-md-3" for="parent"><? echo __('Select Parent');?></label>
                <div class="col-md-6"><select id="parent" name="parent" class="form-control input-sm"></select></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><? echo __('Close');?></button>
        <button type="button" class="btn btn-primary btn-sm" id="submit"><? echo __('Submit');?></button>
    </div>
</div>

<script>
    $('#change_parent').on('click', function () {
        $.ajax({
            type: 'POST',
            url: '<? echo $this->uri->url_encrypt('domains/getclients');?>',
            success: function (dataRules) {
                var obj = $.parseJSON(dataRules);
                $('select[name="parent"]').html("");
                for(var i in obj) {
                    var key = i;
                    var value = obj[i];
                    $('select[name="parent"]').append(new Option(value, key));
                }
            }
        });
    });
    $('#parent_modal button#submit').unbind();
    $('#parent_modal button#submit').on('click', function () {
        var parent = document.getElementById("parent").value;
        var table = $('.dataTables').dataTable();
        var ids = $('input:checkbox[name="domains[]"]:checked').map(function () {
            return this.value;
        }).get();
        for(var id=0; id<ids.length; id++) {
            var data = {};
            data['action'] = 'changeparent';
            data['id'] = ids[id];
            data['parent'] = parent;
            core.ajaxRequest({
                method: 'POST',
                url: '<? echo $multi_url;?>',
                data: data,
                table: '#domains-table',
                table_url: '<? echo $list_url;?>'
            });
        }
        $('#parent_modal').modal('hide');
    });
    core.setWidth({
        target: 'domain-list-panel'
    });
    core.inputInits();
    core.datatable({
        target : '#domains-table',
        select_row: true,
        buttons: true,
        checkbox: true,
        actions: true,
        action_buttons: {
            0:'change-parent',
            1:'suspend',
            2:'unsuspend',
            3:'remove'
        }
    });
    core.multipleProcess();

</script>


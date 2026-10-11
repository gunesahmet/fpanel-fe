<? $multi_url= (isset($server_id) == true ? $this->uri->url_encrypt('mailserver/multiple/white/server/'.$server_id) : $this->uri->url_encrypt('mailserver/multiple/white')); ?>
<? $list_url = (isset($server_id) == true ? $this->uri->url_encrypt('mailserver/whitelist/server/'.$server_id) : $this->uri->url_encrypt('mailserver/whitelist')); ?>
<? $create_url = (isset($server_id) == true ? $this->uri->url_encrypt('mailserver/whitelistcreate/server/'.$server_id) : $this->uri->url_encrypt('mailserver/whitelistcreate')); ?>
<div class="col-md-12">
    <div class="pull-left">
        <a class="btn btn-sm btn-primary" data-id="nested" href="<? echo $create_url;?>"><i class="fa fa-plus-circle"></i> <? echo __('Create White List');?></a>
    </div>
    <div class="pull-right table-button-lists">
        <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="lists" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
    </div>
</div>
<br />
<br />
<div class="col-md-12">
    <div class="table-responsive">
        <table id="mail-whitelist-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
            <thead>
            <tr>
                <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero"></th>
                <th width="75%"><? echo __('Syntax'); ?></th>
                <th width="20%"><? echo __('Action'); ?></th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    core.inputInits();
    core.datatable({
        target : '#mail-whitelist-table',
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


<?
if(isset($server_id))  {
    $multi_url = $this->uri->url_encrypt('mailserver/multiplequeue/server/'.$server_id);
    $clear_url = $this->uri->url_encrypt('mailserver/clear/server/'.$server_id);
    $flush_url = $this->uri->url_encrypt('mailserver/flush/server/'.$server_id);
    $list_url = $this->uri->url_encrypt('mailserver/smtp/server/'.$server_id);
}
else  {
    $multi_url = $this->uri->url_encrypt('mailserver/multiplequeue');
    $clear_url = $this->uri->url_encrypt('mailserver/clear');
    $flush_url = $this->uri->url_encrypt('mailserver/flush');
    $list_url = $this->uri->url_encrypt('mailserver/smtp');
}

?>
<div class="col-md-12">
    <div class="pull-left">
        <? if(PANEL_OS_TYPE == "linux") { ?>
            <a class="btn btn-primary btn-sm" onclick="confirm('<? echo $flush_url;?>','<? echo __('Are you sure?');?>');"><i class="fa fa-refresh"></i> <? echo __('Flush Queue');?></a>
        <? } ?>
        <a class="btn btn-danger btn-sm" onclick="confirm('<? echo $clear_url;?>','<? echo __('Are you sure?');?>');"><i class="fa fa-trash"></i> <? echo __('Clear Queue');?></a>
    </div>
    <div class="pull-right table-button-list">
        <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="removequeue" data-target="queue" data-url="<? echo $multi_url;?>"><i class="fa fa-times"></i> <? echo __('Remove Selected');?></a>
    </div>
</div>
<br />
<br />
<div class="col-md-12">
    <div class="table-responsive">
        <table id="mail-smtp-queue-table" class="table table-striped table-bordered table-hover" data-url="<? echo $list_url;?>">
            <thead>
            <tr>
                <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero"></th>
                <th width="10%"><? echo __('Sender'); ?></th>
                <th width="10%"><? echo __('Recipients'); ?></th>
                <th width="20%"><? echo __('Subject'); ?></th>
                <th width="10%"><? echo __('Date'); ?></th>
                <th width="10%"><? echo __('Age'); ?></th>
                <th width="10%"><? echo __('Size'); ?></th>
                <th width="20%"><? echo __('Action'); ?></th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    core.inputInits();
    core.datatable({
        target : '#mail-smtp-queue-table',
        select_row: true,
        buttons: true,
        checkbox: true,
        actions: true,
        action_buttons: {
            0:'remove'
        }
    });
</script>

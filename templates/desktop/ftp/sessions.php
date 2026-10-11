<? $server_link = (isset($server_id) ? "/server/$server_id" : ""); ?>

<div class="col-md-12">
    <div class="pull-right table-button-lists">
        <? if($server_os == "linux") { ?>
            <a class="btn btn-sm btn-danger remove disabled" data-type="multiple" data-action="delete" data-target="aliases" data-url="<? echo $this->uri->url_encrypt("ftp/multiple$server_link");?>"><i class="fa fa-plus-circle"></i> <? echo __('Remove Selected');?></a>
        <? } ?>
    </div>
</div>
<br />
<br />
<div class="col-md-12">
    <div class="table-responsive">
        <table id="ftp-sessions-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt("ftp/sessions$server_link");?>">
            <thead>
            <tr>
                <th width="5%"><input type="checkbox" id="select_all" class="checkbox-aero" /></th>
                <th width="35%"><? echo __('FTP User')?></th>
                <th width="20%"><? echo __('Logged Time')?></th>
                <th width="10%"><? echo __('Idle Time')?></th>
                <th width="20%"><? echo __('Actions')?></th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    core.inputInits();
    core.datatable({
        target : '#ftp-sessions-table',
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

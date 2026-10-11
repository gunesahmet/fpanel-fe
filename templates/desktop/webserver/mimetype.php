<? $server_link = (isset($server_id) == true ? "/server/$server_id" : ''); ?>
<div class="col-md-12">
    <a class="btn btn-primary btn-sm" data-id="nested" href="<? echo $this->uri->url_encrypt("webserver/addmime$server_link");?>"><i class="fa fa-plus-circle"></i> <? echo __('Add Mime Type');?></a>
</div>
<br />
<br />
<div class="col-md-12">
    <div class="table-responsive">
        <table id="web-server-mimetypes-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt('webserver/mimetypes'.$server_link);?>">
            <thead>
            <tr>
                <? foreach ($th as $i => $o) { ?>
                    <? if (is_numeric($i)) { ?>
                        <th><? echo $o; ?></th>
                    <? } else { ?>
                        <th><? echo $i; ?></th>
                    <? } ?>
                <? } ?>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    core.datatable({
        target : '#web-server-mimetypes-table',
        select_row: false,
        serverside: false,
        buttons: false,
        checkbox: false,
        actions: false
    });
</script>
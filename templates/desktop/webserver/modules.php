<? $server_link = (isset($server_id) == true ? "/server/$server_id" : ''); ?>
<table id="web-server-modules-table" class="table table-striped table-bordered table-hover" data-url="<? echo $this->uri->url_encrypt("webserver/modules$server_link");?>">
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
<script>
    core.datatable({
        target : '#web-server-modules-table',
        serverside: false,
        select_row: false,
        buttons: false,
        checkbox: false,
        actions: false
    });
</script>
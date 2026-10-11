<? if(isset($client_id)) { ?>
    <? $url = $this->uri->url_encrypt('backupclient/client/'.$client_id);;?>
    <? $url2 = $this->uri->url_encrypt('backupclient/progress/'. $restore_id .'/client/'.$client_id);;?>
<? } else { ?>
    <? $url = $this->uri->url_encrypt('backupclient');;?>
    <? $url2 = $this->uri->url_encrypt('backupclient/progress/'. $restore_id);;?>
<? } ?>
<div class="row">
    <div class="col-md-12">
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $progress;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $progress;?>%">
                <span class="sr-only"> <?= $progress;?>%  </span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table class="table table-striped table-condensed table-white">
                    <? if(!empty($configs['domain_list'])) { ?>
                        <? foreach($configs['domain_list'] as $domain) { ?>
                            <tr>
                                <td width="30%"><?= $domain;?></td>
                                <td id="<?= str_replace('.','_', $domain)?>"><div class="waiting"><i class="fa fa-spinner fa-spin"></i></div></td>
                            </tr>
                        <? } ?>
                    <? } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function autoInterval() {
        $.ajax({
            url: '<?= $url2;?>',
            type: 'POST',
            'success': function (result) {
                var obj;
                try
                {
                    obj = JSON.parse(result);
                }
                catch(e)
                {
                    obj = {message:'error',content:'<?= __('System Error 500');?>'};
                }
                if(obj['message'] == "logout") {
                    toastr["error"]('<?= __('Session Timeout');?>');
                    setTimeout(function(){window.location.assign('/')}, 1000);
                }
                if(obj['message'] == "505") {
                    toastr["error"]("System Error");
                    setTimeout(function(){window.location.assign('/error.php')}, 1000);
                }
                if(obj['percent'] == "100") {
                    window.location.assign('<?= $url;?>');
                }
                else {
                    progress_status(obj);
                    domain_status(obj);
                }
            }
        });
    }

    function domain_status(obj){
        var domain_status = false;

        if(obj['domainlist'] != "undefined") {
            for(var domain in obj['domainlist']) {
                if(obj['domainlist'][domain] == "1") {
                    domain_status = true;
                    var domain_id = domain.replace(".","_");
                    document.getElementById(domain_id).innerHTML = '<i class="fa fa-fw text-primary fa-check-circle"></i>';
                }
            }
        }
    }


    function progress_status(obj){
        $(".progress-bar").css("width", obj['percent'] + "%");
        $('.progress-bar').attr('aria-valuenow',obj['percent'] + "%");
        $(".sr-only").html(obj['percent'] + "%");
    }
    autoInterval();
    setInterval("autoInterval()", 5000);
</script>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>

<? if(isset($server_id)) { ?>
    <? $url = $this->uri->url_encrypt('migration/server/'.$server_id);;?>
    <? $url2 = $this->uri->url_encrypt('migration/progress/'. $id .'/server/'.$server_id);;?>
<? } else { ?>
    <? $url = $this->uri->url_encrypt('migration');;?>
    <? $url2 = $this->uri->url_encrypt('migration/progress/'. $id);;?>
<? } ?>
<div class="row">
    <div class="progress progress-striped active">
        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $progress;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $progress;?>%">
            <span class="sr-only"> <?= $progress;?>%  </span>
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
                <div class="tabbable-custom nav-justified">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active"> <a id="resellers_tab" href="#resellers" data-toggle="tab"><i class="fa fa-spinner fa-spin"></i> <?= __('Reseller Accounts');?> </a> </li>
                        <li> <a id="clients_tab" href="#clients" data-toggle="tab"> <i class="fa fa-spinner fa-spin"></i><?= __('Client Accounts');?> </a> </li>
                        <li> <a id="domains_tab" href="#domains" data-toggle="tab"> <i class="fa fa-spinner fa-spin"></i><?= __('Domains');?> </a> </li>
                    </ul>
                </div>


                <div class="tab-content">
                    <div id="resellers" class="tab-pane">
                        <table class="table table-striped table-condensed table-white">
                            <? if(!empty($configs['resellers'])) { ?>
                                <? foreach($configs['resellers'] as $reseller) { ?>
                                    <tr>
                                        <td width="30%"><?= $reseller;?></td>
                                        <td id="<?= $reseller?>"> <div class="waiting"><i class="fa fa-spinner fa-spin"></i></div> </td>
                                    </tr>
                                <? } ?>
                            <? } ?>
                        </table>
                    </div>
                    <div id="clients" class="tab-pane">
                        <table class="table table-striped table-condensed table-white">
                            <? if(!empty($configs['clients'])) { ?>

                                <? foreach($configs['clients'] as $client => $status) { ?>
                                    <tr>
                                        <td width="30%"><?= $client;?></td>
                                        <td id="<?= $client?>"><div class="waiting"><i class="fa fa-spinner fa-spin"></i></div></td>
                                    </tr>
                                <? } ?>
                            <? } ?>
                        </table>
                    </div>
                    <div id="domains" class="tab-pane">
                        <table class="table table-striped table-condensed table-white">
                            <? if(!empty($configs['domains'])) { ?>
                                <? foreach($configs['domains'] as $domain => $status) { ?>
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
                if(obj['progress'] == "100") {
                    <? $this->flashSession->success(__('Migration Completed'));?>
                    window.location.assign('<?= $url;?>');
                }
                else {
                    progress_status(obj);
                    reseller_status(obj);
                    client_status(obj);
                    domain_status(obj);
                }
            }
        });
    }

    function reseller_status(obj){
        var reseller_status = false;
        if(Object.keys(obj['resellers']).length > 0) {
            for(var reseller in obj['resellers']) {
                if(obj['resellers'][reseller] == true) {
                    reseller_status = true;
                    document.getElementById(reseller).innerHTML = '<i class="fa fa-fw text-primary fa-check-circle"></i>';
                }
            }
        }
        else {
            reseller_status = true;
        }



        if(reseller_status == true) {
            document.getElementById("resellers_tab").parentNode.className = "active";
            document.getElementById("clients_tab").parentNode.className = "";
            document.getElementById("domains_tab").parentNode.className = "";

            document.getElementById("resellers").className = "tab-pane active";
            document.getElementById("clients").className = "tab-pane";
            document.getElementById("domains").className = "tab-pane";
            var tab = document.getElementById('resellers_tab');
            tab.children[0].className = "fa fa-fw text-primary fa-check-circle";
        }

    }

    function client_status(obj){
        var client_status = false;
        if(Object.keys(obj['clients']).length > 0) {
            for(var client in obj['clients']) {
                if(obj['clients'][client] == "1") {
                    client_status = true;
                    document.getElementById(client).innerHTML = '<i class="fa fa-fw text-primary fa-check-circle"></i>';
                }
            }
        }
        else {
            client_status = true;
        }

        if(client_status == true) {
            document.getElementById("resellers_tab").parentNode.className = "";
            document.getElementById("clients_tab").parentNode.className = "active";
            document.getElementById("domains_tab").parentNode.className = "";

            document.getElementById("resellers").className = "tab-pane";
            document.getElementById("clients").className = "tab-pane active";
            document.getElementById("domains").className = "tab-pane";
            var tab = document.getElementById('clients_tab');
            tab.children[0].className = "fa fa-fw text-primary fa-check-circle";
        }
    }

    function domain_status(obj){
        var domain_status = false;
        if(Object.keys(obj['domains']).length > 0) {
            for(var domain in obj['domains']) {
                if(obj['domains'][domain] == "1") {
                    domain_status = true;
                    var domain_id = domain.replace(".","_");
                    document.getElementById(domain_id).innerHTML = '<i class="fa fa-fw text-primary fa-check-circle"></i>';
                }
            }
        }
        else {
            domain_status = true;
        }
        if(domain_status == true) {
            document.getElementById("resellers_tab").parentNode.className = "";
            document.getElementById("clients_tab").parentNode.className = "";
            document.getElementById("domains_tab").parentNode.className = "active";

            document.getElementById("resellers").className = "tab-pane";
            document.getElementById("clients").className = "tab-pane";
            document.getElementById("domains").className = "tab-pane active";
            var tab = document.getElementById('domains_tab');
            tab.children[0].className = "fa fa-fw text-primary fa-check-circle";
        }
    }

    function progress_status(obj){
        $(".progress-bar").css("width", obj['progress'] + "%");
        $('.progress-bar').attr('aria-valuenow',obj['progress'] + "%");
        $(".sr-only").html(obj['progress'] + "%");
    }
    autoInterval();
    setInterval("autoInterval()", 5000);
</script>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
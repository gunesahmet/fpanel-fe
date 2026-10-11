<? if(isset($server_id)) { ?>
    <? $url = $this->uri->url_encrypt('serverbackup/server/'.$server_id);;?>
    <? $url2 = $this->uri->url_encrypt('serverbackup/progress/'. $restore_id .'/server/'.$server_id);;?>
<? } else { ?>
    <? $url = $this->uri->url_encrypt('serverbackup');;?>
    <? $url2 = $this->uri->url_encrypt('serverbackup/progress/'. $restore_id);;?>
<? } ?>
<div class="row">
    <div class="col-md-12">
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<? echo $progress;?>" aria-valuemin="0" aria-valuemax="100" style="width: <? echo $progress;?>%">
                <span class="sr-only"> <? echo $progress;?>%  </span>
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
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="tabbable-custom nav-justified">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active"> <a id="server_settings_tab" href="#server_settings" data-toggle="tab"><i class="fa fa-spinner fa-spin"></i><? echo __('Server Settings');?> </a> </li>
                        <li> <a id="domains_tab" href="#domains" data-toggle="tab"> <i class="fa fa-spinner fa-spin"></i><? echo __('Domains');?> </a> </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div id="server_settings" class="tab-pane active">
                        <table class="table">
                            <tr>
                                <td><strong><? echo __('Restoring Cronjobs');?></strong></td>
                                <td id="restore_cronjobs"><div class="waiting"><i class="fa fa-spinner fa-spin"></i></div></td>
                            </tr>
                            <tr>
                                <td><strong><? echo __('Restoring Blacklists');?></strong></td>
                                <td id="restore_blacklists"><div class="waiting"><i class="fa fa-spinner fa-spin"></i></div></td>
                            </tr>
                            <tr>
                                <td><strong><? echo __('Restoring DNS Settings');?></strong></td>
                                <td id="restore_dns_server"><div class="waiting"><i class="fa fa-spinner fa-spin"></i></div></td>
                            </tr>
                            <tr>
                                <td><strong><? echo __('Restoring Mail Server Settings');?></strong></td>
                                <td id="restore_mail_server_settings"><div class="waiting"><i class="fa fa-spinner fa-spin"></i></div></td>
                            </tr>
                            <tr>
                                <td><strong><? echo __('Restoring Spam Settings');?></strong></td>
                                <td id="restore_spam_settings"><div class="waiting"><i class="fa fa-spinner fa-spin"></i></div></td>
                            </tr>
                            <tr>
                                <td><strong><? echo __('Restoring Spam Lists');?></strong></td>
                                <td id="restore_spam_list"><div class="waiting"><i class="fa fa-spinner fa-spin"></i></div></td>
                            </tr>
                        </table>
                    </div>
                    <div id="domains" class="tab-pane">
                        <table class="table table-striped table-condensed table-white">
                            <? if(!empty($configs['domain_list'])) { ?>
                                <? foreach($configs['domain_list'] as $domain) { ?>
                                    <tr>
                                        <td width="30%"><? echo $domain;?></td>
                                        <td id="<? echo str_replace('.','_',$domain)?>"><div class="waiting"><i class="fa fa-spinner fa-spin"></i></div></td>
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
            url: '<? echo $url2;?>',
            type: 'POST',
            'success': function (result) {
                var obj;
                try
                {
                    obj = JSON.parse(result);
                }
                catch(e)
                {
                    obj = {message:'error',content:'<? echo __('System Error 500');?>'};
                }
                if(obj['message'] == "logout") {
                    toastr["error"]('<? echo __('Session Timeout');?>');
                    setTimeout(function(){window.location.assign('/')}, 1000);
                }
                if(obj['message'] == "505") {
                    toastr["error"]("System Error");
                    setTimeout(function(){window.location.assign('/error.php')}, 1000);
                }
                if(obj['percent'] == "100") {
                    <? $this->flash->success(__('Restore Completed'));?>
                    window.location.assign('<? echo $url;?>');
                }
                else {
                    progress_status(obj);
                    server_status(obj);
                    domain_status(obj);
                }
            }
        });
    }

    function domain_status(obj){
        var domain_status = false;

        if(obj['restore_clients']['domainlist'] != "undefined") {
            for(var domain in obj['restore_clients']['domainlist']) {
                if(obj['restore_clients']['domainlist'][domain] == "1") {
                    domain_status = true;
                    var domain_id = domain.replace(".","_");
                    document.getElementById(domain_id).innerHTML = '<i class="fa fa-fw text-primary fa-check-circle"></i>';
                    //$("#"+domain_id).innerHTML = '<i class="fa fa-fw text-primary fa-check-circle"></i>';
                }
            }
        }
        else {
            domain_status = true;
        }
        if(domain_status == true) {
            document.getElementById("server_settings_tab").parentNode.className = "";
            document.getElementById("domains_tab").parentNode.className = "active";

            document.getElementById("server_settings").className = "tab-pane";
            document.getElementById("domains").className = "tab-pane active";
            var tab = document.getElementById('domains_tab');
            tab.children[0].className = "fa fa-fw text-primary fa-check-circle";
        }
    }

    function server_status(obj){
        var server_setting_status = false;
        for (var setting in obj['restore_server_settings']) {
            if(obj['restore_server_settings'][setting] == "1") {
                server_setting_status = true;
                $("#"+setting).html('<i class="fa fa-fw text-primary fa-check-circle"></i>');
            }
        }
        if(server_setting_status == true) {
            document.getElementById("server_settings_tab").parentNode.className = "active";
            document.getElementById("domains_tab").parentNode.className = "";
            document.getElementById("server_settings").className = "tab-pane active";
            document.getElementById("domains").className = "tab-pane";
            var tab = document.getElementById('server_settings_tab');
            tab.children[0].className = "fa fa-fw text-primary fa-check-circle";
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

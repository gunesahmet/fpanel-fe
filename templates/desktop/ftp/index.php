<? $server_link = (isset($server_id) ? "/server/$server_id" : "");?>

<div class="row" id="ftp-settings-panel" data-width="50%">
    <div class="tabbable-line">
        <ul class="nav nav-tabs nav-justified" id="ftp-settings-tabs">
            <li class="active">
                <a data-url="<? echo $this->uri->url_encrypt("ftp/settings$server_link");?>" href="#tab-ftp-general-settings"><? echo __('General Settings'); ?></a>
            </li>
            <li>
                <a data-url="<? echo $this->uri->url_encrypt("ftp/sessions$server_link");?>" href="#tab-active-sessions"><? echo __('Active Sessions'); ?></a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="tab-ftp-general-settings">
            </div>
            <div class="tab-pane fade" id="tab-active-sessions">
            </div>
        </div>
    </div>

</div>
<script>
    core.setWidth({
        target: 'ftp-settings-panel'
    });
    core.ajaxTabs("#ftp-settings-tabs");
</script>
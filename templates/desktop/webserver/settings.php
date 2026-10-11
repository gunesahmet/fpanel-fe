<div class="row" id="webserver-settings-panel" data-width="60%">
    <div class="tabbable-line">
        <ul class="nav nav-tabs nav-justified" id="webserver-settings-tabs">
            <li class="active">
                <a data-url="<? echo $this->uri->url_encrypt('webserver/settings');?>" href="#tab-web-general-settings"><? echo __('General Settings'); ?></a>
            </li>
            <li>
                <a data-url="<? echo $this->uri->url_encrypt('webserver/web');?>" href="#tab-webserver-settings"><? echo __('Web Server Settings'); ?></a>
            </li>
            <li>
                <a data-url="<? echo $this->uri->url_encrypt('webserver/modules');?>" href="#tab-module-settings"><? echo __('Modules'); ?></a>
            </li>
            <li>
                <a data-url="<? echo $this->uri->url_encrypt('webserver/mimetypes');?>" href="#tab-mimetype-settings"><? echo __('Mime Types'); ?></a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="tab-web-general-settings">
            </div>
            <div class="tab-pane fade" id="tab-webserver-settings">
            </div>
            <div class="tab-pane fade" id="tab-module-settings">
            </div>
            <div class="tab-pane fade" id="tab-mimetype-settings">
            </div>
        </div>
    </div>

</div>
<script>
    core.setWidth({
        target: 'webserver-settings-panel'
    });
    core.ajaxTabs("#webserver-settings-tabs");
</script>
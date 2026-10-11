<div class="row" id="advanced-settings-panel" data-width="60%">
    <div class="tabbable-line">
        <ul class="nav nav-tabs nav-justified" id="advanced-settings-tabs">
            <li class="active">
                <a data-url="<? echo $this->uri->url_encrypt('system/passwordsettings');?>" href="#tab-password-rule">
                    <? echo __('Password Rules'); ?>
                </a>
            </li>
            <li>
                <a data-url="<? echo $this->uri->url_encrypt('system/certificate');?>" href="#tab-system-certificate">
                    <? echo __('System Certificate'); ?>
                </a>
            </li>
            <li>
                <a data-url="<? echo $this->uri->url_encrypt('system/recaptchasettings');?>" href="#tab-recaptcha">
                    <? echo __('Recaptcha Settings'); ?>
                </a>
            </li>
            <li>
                <a data-url="<? echo $this->uri->url_encrypt('system/addnewadmin');?>" href="#tab-add-new-admin">
                    <? echo __('Additional System Administrator'); ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="tab-password-rule"></div>
            <div class="tab-pane fade" id="tab-system-certificate"></div>
            <div class="tab-pane fade" id="tab-recaptcha"></div>
            <div class="tab-pane fade" id="tab-add-new-admin"></div>
        </div>
    </div>

</div>
<script>
    core.setWidth({
        target: 'advanced-settings-panel'
    });
    core.ajaxTabs("#advanced-settings-tabs");
</script>

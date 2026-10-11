<div id="<? echo md5('installation_1')?>" class="abs window">
    <div class="abs window_inner">
        <div class="window_top" data-original-title="<? echo __('System Software Configure & Installation');?>">
            <span class="pull-left window-element"></span>
            <span id="window_name"><? echo __('System Software Configure & Installation');?></span>
            <span class="pull-right window-controls">
            </span>
        </div>
        <div class="abs window_content">
            <div class="window_frame">
                <div class="window_main">
                    <div id="installation-wizard" class="margin-top-10">
                        <div class="form-bootstrapWizard">
                            <ul class="bootstrapWizard form-wizard">
                                <li class="active" data-target="#step1">
                                    <a href="#tab-licence-agreement" data-url="<? echo $this->uri->url_encrypt("install/licence")?>" data-toggle="tab"> <span class="step">1</span> <span class="title"><? echo __('Software License Agreement');?></span> </a>
                                </li>
                                <li data-target="#step2">
                                    <a href="#tab-server-settings" data-url="<? echo $this->uri->url_encrypt("install/serverinfo")?>" data-toggle="tab"> <span class="step">2</span> <span class="title"><? echo __('Server Settings');?></span> </a>
                                </li>
                                <li data-target="#step3">
                                    <a href="#tab-system-settings" data-url="<? echo $this->uri->url_encrypt("install/generalinfo")?>" data-toggle="tab"> <span class="step">3</span> <span class="title"><? echo __('System Settings');?></span> </a>
                                </li>
                                <li data-target="#step4">
                                    <a href="#tab-finish-installation" data-url="<? echo $this->uri->url_encrypt("install/finish")?>" data-toggle="tab"> <span class="step">4</span> <span class="title"><? echo __('Finish');?></span> </a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active margin-bottom-10" id="tab-licence-agreement"><? $this->partial('install/licence')?></div>
                            <div class="tab-pane margin-bottom-10" id="tab-server-settings"></div>
                            <div class="tab-pane margin-bottom-10" id="tab-system-settings"></div>
                            <div class="tab-pane margin-bottom-10" id="tab-finish-installation"></div>
                            <div class="form-actions fixed-bottom nav-buttons">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pull-left">
                                            <a class="btn btn-sm btn-danger btn-logout" href="<? echo $this->uri->url_encrypt('logout');?>"> <? echo __('Logout');?> </a>
                                            <a class="btn btn-sm btn-default previous disabled"> <? echo __('Previous');?> </a>
                                        </div>
                                        <div class="pull-right">
                                            <a class="btn btn-sm btn-primary next"> <? echo __('Next');?> </a>
                                            <a class="btn btn-sm btn-primary next finish" style="display: none;"> <? echo __('Finish');?> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="abs window_bottom">
        </div>
    </div>
    <span class="abs ui-resizable-handle ui-resizable-se"></span>
</div>
<style>
    .window {
        min-width:60%;
        min-height:600px;
    }
    .window_main {
        background: #fff none repeat scroll 0 0;
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 5px 10px 0;
        position: relative;
        width: 100%;
    }
    .pager li > a, .pager li > span {
        background: #337ab7 none repeat scroll 0 0;
        color: #fff;
    }

    .pager li > a:hover,
    .pager li > a:focus {
        color: #ffffff;
        background-color: #286090;
        border-color: #122b40;
    }
    .pager li > a:hover{
        color: #ffffff;
        background-color: #286090;
        border-color: #204d74;
    }


    #installation-wizard {
        min-height: 93%;
        position: absolute;
        min-width:100%;
    }
    .tab-content {
        min-height: 87%;
        position: absolute;
        width: 98%;
        margin-top:40px;
    }

    .fixed-bottom {
        bottom: 0;
        min-width: 100%;
        position: absolute;
    }
</style>
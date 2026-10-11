<div class="ibox">
    <div class="ibox-title">
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="icon-chevron-up"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-md-12">
                <form id="aspnet-domain-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="tabbable-line">
                        <ul class="nav nav-tabs nav-justified" id="domain-aspnet-settings-tabs">
                            <li class="active"><a href="#connstr" data-toggle="tab"><?= __('Connection String Settings'); ?> </a></li>
                            <li><a href="#customErr" data-toggle="tab"><?= __('Custom Error Settings'); ?> </a></li>
                            <li><a href="#compilation" data-toggle="tab"><?= __('Compilation and Debugging'); ?> </a></li>
                            <li><a href="#globalization" data-toggle="tab"><?= __('Globalization Settings'); ?> </a></li>
                            <li><a href="#codeAccess" data-toggle="tab"><?= __('Code Access'); ?> </a></li>
                            <li><a href="#sessionSettings" data-toggle="tab"><?= __('Session Settings'); ?> </a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="connstr">
                                <div class="row">
                                    <div class="col-md-12">
                                        <? $this->partial('aspnet/connection_string'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="customErr">
                                <div class="row">
                                    <div class="col-md-12">
                                        <? $this->partial('aspnet/custom_error'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="compilation">
                                <div class="row">
                                    <div class="col-md-12">
                                        <? $this->partial('aspnet/compilation'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="globalization">
                                <div class="row">
                                    <div class="col-md-12">
                                        <? $this->partial('aspnet/globalization'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="codeAccess">
                                <div class="row">
                                    <div class="col-md-12">
                                        <? $this->partial('aspnet/codeAccess'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="sessionSettings">
                                <div class="row">
                                    <div class="col-md-12">
                                        <? $this->partial('aspnet/sessionSettings'); ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <a class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></a>
                                <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>

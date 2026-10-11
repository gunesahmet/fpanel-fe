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
                <div class="tabbable-line">
                    <ul class="nav nav-tabs nav-justified" id="mailserver-settings-tabs">
                        <li><a href="<?= $generalsettings_link;?>"><?= __('General Settings'); ?></a></li>
                        <? if($installed_mail == POSTFIX) { ?>
                            <li class="active"><a href="<?= $policyd_link;?>"><?= __('Policyd Settings'); ?></a></li>
                        <? } ?>
                        <li><a href="<?= $spamsettings_link;?>"><?= __('SpamAssassin Settings'); ?></a></li>
                        <li><a href="<?= $blacklist_link;?>"><?= __('Black Lists'); ?></a></li>
                        <li><a href="<?= $whitelist_link;?>"><?= __('White Lists'); ?></a></li>
                        <li><a href="<?= $smtp_link;?>"><?= __('SMTP Queue'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane in" id="tab-mail-general-settings">
                        </div>
                        <? if($installed_mail == POSTFIX) { ?>
                            <div class="tab-pane active" id="tab-policyd-settings">
                                <div class="col-md-8">
                                    <div class="row margin-bottom-10">
                                        <div class="col-md-12">
                                            <a class="btn btn-sm btn-primary" data-toggle="modal" data-target="#create_modal"><?= __("Add New Policy");?></a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover" id="policies-table" data-server="true" data-actions="true" data-url="<?= $policyd_link;?>">
                                                    <thead>
                                                    <tr>
                                                        <th width="10%"><?= __('Name'); ?></th>
                                                        <th width="10%"><?= __('Priority'); ?></th>
                                                        <th width="20%"><?= __('Description'); ?></th>
                                                        <th width="10%"><?= __('Status'); ?></th>
                                                        <th width="20%"><?= __('Action'); ?></th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row margin-bottom-10">
                                        <div class="col-md-12">
                                            <a class="btn btn-sm btn-primary" id="add_new_member" disabled><?= __("Add New Policy Member");?></a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover" id="policy-members-table" data-server="false" data-actions="true" >
                                                    <thead>
                                                    <tr>
                                                        <th width="10%"><?= __('Name'); ?></th>
                                                        <th width="10%"><?= __('Priority'); ?></th>
                                                        <th width="20%"><?= __('Description'); ?></th>
                                                        <th width="10%"><?= __('Status'); ?></th>
                                                        <th width="20%"><?= __('Action'); ?></th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row margin-bottom-10">
                                        <div class="col-md-12">
                                            <a class="btn btn-sm btn-primary" id="add_new_quota" disabled><?= __("Add New Policy Quotas");?></a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover" id="policy-quotas-table" data-server="false" data-actions="true" >
                                                    <thead>
                                                    <tr>
                                                        <th width="10%"><?= __('Name'); ?></th>
                                                        <th width="10%"><?= __('Priority'); ?></th>
                                                        <th width="20%"><?= __('Description'); ?></th>
                                                        <th width="10%"><?= __('Status'); ?></th>
                                                        <th width="20%"><?= __('Action'); ?></th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <form class="form-horizontal" method="post" id="policyd-settings-form" action="<?= $save_url;?>">
                                        <h2 class="text-right"><?= __("Policy Settings");?></h2>
                                        <div class="hr-line-dashed"></div>
                                        <h4 class="text-right text-navy"><?= __("Server Settings");?></h4>
                                        <hr />
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="min_servers"><?= __('Min Servers');?></label>
                                            <div class="col-md-9">
                                                <?= $form->render("min_servers", ["class" => "form-control form-control-sm"]);?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="min_spare_servers"><?= __('Min Spare Servers');?></label>
                                            <div class="col-md-9">
                                                <?= $form->render("min_spare_servers", ["class" => "form-control form-control-sm"]);?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="max_spare_servers"><?= __('Max Spare Servers');?></label>
                                            <div class="col-md-9">
                                                <?= $form->render("max_spare_servers", ["class" => "form-control form-control-sm"]);?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="max_servers"><?= __('Max Servers');?></label>
                                            <div class="col-md-9">
                                                <?= $form->render("max_servers", ["class" => "form-control form-control-sm"]);?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="max_requests"><?= __('Max Requests');?></label>
                                            <div class="col-md-9">
                                                <?= $form->render("max_requests", ["class" => "form-control form-control-sm"]);?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="timeout_busy"><?= __('Timeout Busy');?></label>
                                            <div class="col-md-9">
                                                <div class="input-group input-group-sm" data-autoclose="true">
                                                    <?= $form->render("timeout_busy", ["class" => "form-control form-control-sm"]);?>
                                                    <span class="input-group-addon"><?= __("Seconds");?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="bypass_timeout"><?= __('Bypass Timeout');?></label>
                                            <div class="col-md-9">
                                                <div class="input-group input-group-sm" data-autoclose="true">
                                                    <?= $form->render("bypass_timeout", ["class" => "form-control form-control-sm"]);?>
                                                    <span class="input-group-addon"><?= __("Seconds");?></span>
                                                </div>
                                            </div>
                                        </div>


                                        <h4 class="text-right text-navy"><?= __("Log Settings");?></h4>
                                        <hr/>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="log_level"><?= __('Log Level');?></label>
                                            <div class="col-md-9">
                                                <?= $form->render("log_level", ["class" => "form-control form-control-sm"]);?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="log_detail"><?= __('Log Detail');?></label>
                                            <div class="col-md-9">
                                                <?= $form->render("log_detail", ["class" => "form-control form-control-sm select-multiple"]);?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-9">
                                                <button type="button" class="btn btn-primary btn-sm btn-block" id="save_policy_settings"><?= __('Save');?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                            </div>
                        <? } ?>
                        <div class="tab-pane" id="tab-spam-settings"></div>
                        <div class="tab-pane" id="tab-blacklists-settings"></div>
                        <div class="tab-pane" id="tab-whitelists-settings"></div>
                        <div class="tab-pane" id="tab-smtp-settings"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal fade" id="create_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static" data-url="<?= $create_url;?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><?= __('Add New Policy');?></h4>
                <small class="font-bold"><?= __('Please enter your policy details');?></small>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="add_policy_form">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="name"><?= __('Policy Name');?></label>
                        <div class="col-md-9">
                            <input id="name" name="name" class="form-control form-control-sm input-sm" type="text"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="priority"><?= __('Priority');?></label>
                        <div class="col-md-9">
                            <input id="priority" name="priority" class="form-control form-control-sm input-sm" type="number"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="description"><?= __('Description');?></label>
                        <div class="col-md-9">
                            <textarea id="description" name="description" class="form-control form-control-sm" style="resize: vertical;"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="status"><?= __('Status');?></label>
                        <div class="col-md-9">
                            <select class="form-control form-control-sm" name="status" id="status">
                                <option value="active"><?= __("Active");?></option>
                                <option value="disable"><?= __("Disable");?></option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit_add"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal fade" id="edit_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><?= __('Edit Policy');?></h4>
                <small class="font-bold"><?= __('Please enter your policy details');?></small>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="edit_policy_form">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="name"><?= __('Policy Name');?></label>
                        <div class="col-md-9">
                            <input id="name" name="name" class="form-control form-control-sm input-sm" type="text"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="priority"><?= __('Priority');?></label>
                        <div class="col-md-9">
                            <input id="priority" name="priority" class="form-control form-control-sm input-sm" type="number"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="description"><?= __('Description');?></label>
                        <div class="col-md-9">
                            <textarea id="description" name="description" class="form-control form-control-sm" style="resize: vertical;"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="status"><?= __('Status');?></label>
                        <div class="col-md-9">
                            <select class="form-control form-control-sm" name="status" id="status">
                                <option value="enabled"><?= __("Active");?></option>
                                <option value="disabled"><?= __("Disable");?></option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit_edit"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal fade" id="create_member_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static" data-url="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><?= __('Add New Policy Member');?></h4>
                <small class="font-bold"><?= __('Please enter your policy member details');?></small>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="add_policy_form">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="source"><?= __('Source');?></label>
                        <div class="col-md-9">
                            <input id="source" name="source" class="form-control form-control-sm input-sm" type="text"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="destination"><?= __('Destination');?></label>
                        <div class="col-md-9">
                            <input id="destination" name="destination" class="form-control form-control-sm input-sm"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="status"><?= __('Status');?></label>
                        <div class="col-md-9">
                            <select class="form-control form-control-sm" name="status" id="status">
                                <option value="active"><?= __("Active");?></option>
                                <option value="disable"><?= __("Disable");?></option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit_add"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal fade" id="create_quota_modal" role="dialog"  aria-hidden="true" tabindex="-1" data-focus-on="input:first" data-keyboard="false" data-backdrop="static" data-url="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><?= __('Add New Policy Quota');?></h4>
                <small class="font-bold"><?= __('Please enter your policy quota details');?></small>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="add_policy_form">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="quota_name"><?= __('Quota Name');?></label>
                        <div class="col-md-9">
                            <input id="quota_name" name="quota_name" class="form-control form-control-sm input-sm" type="text"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="track"><?= __('Track');?></label>
                        <div class="col-md-9">
                            <input id="track" name="track" class="form-control form-control-sm input-sm"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="period"><?= __('Period');?></label>
                        <div class="col-md-9">
                            <input id="period" name="period" class="form-control form-control-sm input-sm"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="verdict"><?= __('Verdict');?></label>
                        <div class="col-md-9">
                            <input id="verdict" name="verdict" class="form-control form-control-sm input-sm"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="status"><?= __('Status');?></label>
                        <div class="col-md-9">
                            <select class="form-control form-control-sm" name="status" id="status">
                                <option value="active"><?= __("Active");?></option>
                                <option value="disable"><?= __("Disable");?></option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default btn-sm"><?= __('Close');?></button>
                <button type="button" class="btn btn-primary btn-sm" id="submit_add"><?= __('Submit');?></button>
            </div>
        </div>
    </div>
</div>

<style>
    .ms-container.single {
        width: 100%;
    }
    .ms-container.single .ms-list {
        border-radius: 0;
    }
    .ms-selected {
        background: #d9edf7 none repeat scroll 0 0;
        color: #ddd;
    }
    .ms-selectable li.ms-elem-selectable.ms-selected {
        color: #fff;
    }
    .ms-container.single .ms-list.ms-focus {
        border-color: #1ab394;
        box-shadow: inherit;
        border-radius: 0;
    }
</style>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
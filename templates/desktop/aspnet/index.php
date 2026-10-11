<div class="row" id="domain-aspnet-settings-panel" data-width="60%">
    <form id="aspnet-domain-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="tabbable-line">
            <ul class="nav nav-tabs nav-justified" id="domain-aspnet-settings-tabs">
                <li class="active"><a href="#connstr" data-toggle="tab"><? echo __('Connection String Settings'); ?> </a></li>
                <li><a href="#customErr" data-toggle="tab"><? echo __('Custom Error Settings'); ?> </a></li>
                <li><a href="#compilation" data-toggle="tab"><? echo __('Compilation and Debugging'); ?> </a></li>
                <li><a href="#globalization" data-toggle="tab"><? echo __('Globalization Settings'); ?> </a></li>
                <li><a href="#codeAccess" data-toggle="tab"><? echo __('Code Access Security'); ?> </a></li>
                <li><a href="#sessionSettings" data-toggle="tab"><? echo __('Session Settings'); ?> </a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="connstr">
                    <h4 class="heading-arrow"><? echo __('Connection String Manager');?></h4>
                    <div class="row">
                        <div class="col-md-3">
                            <span class="strong"><? echo __('Name');?></span>
                        </div>
                        <div class="col-md-7">
                            <span class="strong"><? echo __('Connection String');?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <? echo $form->render('connStrName',array('class' => 'form-control input-sm'));?>
                            </div>
                            <div class="col-md-9">
                                <? echo $form->render('connectionString',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>

                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-condensed table-striped table-primary">
                                <thead>
                                <tr>
                                    <th><? echo __('Connection Name'); ?></th>
                                    <th><? echo __('String'); ?></th>
                                    <th><? echo __('Provider'); ?></th>
                                    <th><? echo __('Action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach($conn_list as $value) { ?>
                                    <tr>
                                        <td><? echo $value['0']?></td>
                                        <td><? echo $value['1'];?></td>
                                        <td><? echo $value['2'];?></td>
                                        <td><? echo $value['3'];?></td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="customErr">
                    <h4 class="heading-arrow"><? echo __('Custom Error Settings');?></h4>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('Custom Error Mode');?></span>
                            </div>
                            <div class="col-md-7">
                                    <? echo $form->render('customErrMode',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-3">
                            <span class="strong"><? echo __('Status Code');?></span>
                        </div>
                        <div class="col-md-7">
                            <span class="strong"><? echo __('Redirect URL');?></span>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <? echo $form->render('customErrCode',array('class' => 'form-control input-sm'));?>
                            </div>
                            <div class="col-md-7">
                                <? echo $form->render('customErrUrl',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-condensed table-striped table-primary">
                                <thead>
                                <tr>
                                    <th style="width: 30% !important;"><? echo __('Status Code'); ?></th>
                                    <th style="width: 50% !important;"><? echo __('Redirect URL'); ?></th>
                                    <th style="width: 20% !important;"><? echo __('Action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach($err_list as $value) { ?>
                                    <tr>
                                        <td><? echo $value['0']?></td>
                                        <td><? echo $value['1'];?></td>
                                        <td><? echo $value['2'];?></td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="compilation">
                    <h4 class="heading-arrow"><? echo __('Compilation and Debugging');?></h4>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('Default Language');?></span>
                            </div>
                            <div class="col-md-2">
                                <? echo $form->render('defaultLang',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('Switch Debugging');?></span>
                            </div>
                            <div class="col-md-4">
                                <? echo $form->render('switchDebug',array('class' => 'checkbox-blue'));?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="globalization">
                    <h4 class="heading-arrow"><? echo __('Globalization Settings');?></h4>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('Request Encoding');?></span>
                            </div>
                            <div class="col-md-7">
                                <? echo $form->render('requestEncoding',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('Response Encoding');?></span>
                            </div>
                            <div class="col-md-7">
                                <? echo $form->render('responseEncoding',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('File Encoding');?></span>
                            </div>
                            <div class="col-md-7">
                                <? echo $form->render('fileEncoding',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('Culture');?></span>
                            </div>
                            <div class="col-md-7">
                                <? echo $form->render('culture',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('UI Culture');?></span>
                            </div>
                            <div class="col-md-7">
                                <? echo $form->render('ui_culture',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="tab-pane" id="codeAccess">
                    <h4 class="heading-arrow"><? echo __('Code Access Security');?></h4>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('.NET Trust Level');?></span>
                            </div>
                            <div class="col-md-7">
                                <? echo $form->render('trust_level',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="tab-pane" id="sessionSettings">
                    <h4 class="heading-arrow"><? echo __('Session Settings');?></h4>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('Authentication Mode');?></span>
                            </div>
                            <div class="col-md-7">
                                <? echo $form->render('auth_mode',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <span class="strong"><? echo __('Session Timeout');?></span>
                            </div>
                            <div class="col-md-7">
                                <? echo $form->render('sessionTimeOut',array('class' => 'form-control input-sm'));?>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="pull-right">
                    <a class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></a>
                    <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    core.setWidth({target:'domain-aspnet-settings-panel'});
    core.inputInits();

</script>
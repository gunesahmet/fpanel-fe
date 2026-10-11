<form method="post" autocomplete="off" class="form-horizontal">
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
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="migration_type"> <? echo __('Server Panel Type');?> </label>
                        </div>
                        <div class="col-md-3">
                            <? echo $form->render('migration_type',array('class' => 'form-control')); ?>
                        </div>
                    </div>
                    <div class="row" id="fpanel">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="system"> <? echo __('Operating System');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('system',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="ip_address"> <? echo __('Source IP Address and Port');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('ip_address',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="username"> <? echo __('Server Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('username',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="password"> <? echo __('Server Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <? echo $form->render('password',array('class' => 'form-control')); ?>
                                        <span class="input-group-btn"><button type="button" data-placement="top" title="<? echo __('Show Password');?>" data-toggle="tooltip" id="show-passwd" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="panel_uname"> <? echo __('Panel Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('panel_uname',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="panel_upass"> <? echo __('Panel Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <? echo $form->render('panel_upass',array('class' => 'form-control')); ?>
                                        <span class="input-group-btn"><button type="button" data-placement="top" title="<? echo __('Show Password');?>" data-toggle="tooltip" id="show-passwd" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="description"> <? echo __('Description');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('description',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="cpanel">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="ip_address"> <? echo __('Source IP Address and Port');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('ip_address',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="username"> <? echo __('Server Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('username',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="password"> <? echo __('Server Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <? echo $form->render('password',array('class' => 'form-control')); ?>
                                        <span class="input-group-btn"><button type="button" data-placement="top" title="<? echo __('Show Password');?>" data-toggle="tooltip" id="show-passwd" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="description"> <? echo __('Description');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('description',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="plesk">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="system"> <? echo __('Operating System');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('system',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="ip_address"> <? echo __('Source IP Address and Port');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('ip_address',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="username"> <? echo __('Server Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('username',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="password"> <? echo __('Server Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <? echo $form->render('password',array('class' => 'form-control')); ?>
                                        <span class="input-group-btn"><button type="button" data-placement="top" title="<? echo __('Show Password');?>" data-toggle="tooltip" id="show-passwd" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="panel_uname"> <? echo __('Panel Login Username');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('panel_uname',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="panel_upass"> <? echo __('Panel Login Password');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <? echo $form->render('panel_upass',array('class' => 'form-control')); ?>
                                        <span class="input-group-btn"><button type="button" data-placement="top" title="<? echo __('Show Password');?>" data-toggle="tooltip" id="show-passwd" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="description"> <? echo __('Description');?> </label>
                                </div>
                                <div class="col-md-3">
                                    <? echo $form->render('description',array('class' => 'form-control')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="pull-right">
                                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<style>
    #fpanel {
        display: none;
    }
    #plesk {
        display: none;
    }
    #cpanel {
        display: none;
    }
</style>
<script>
    switch ($('#migration_type').find("option:selected").val()) {
        case "fpanel":
            $("#fpanel").show();
            $("#plesk").hide();
            $("#cpanel").hide();
            disableAll("#fpanel",false);
            disableAll("#plesk",true);
            disableAll("#cpanel",true);
            break;
        case "plesk":
            $("#fpanel").hide();
            $("#plesk").show();
            $("#cpanel").hide();
            disableAll("#fpanel",true);
            disableAll("#plesk",false);
            disableAll("#cpanel",true);
            break;
        case "cpanel":
            $("#fpanel").hide();
            $("#plesk").hide();
            $("#cpanel").show();
            disableAll("#fpanel",true);
            disableAll("#plesk",true);
            disableAll("#cpanel",false);
            break;
    }

    $("#migration_type").change(function(){
        switch ($('#migration_type').find("option:selected").val()) {
            case "fpanel":
                $("#fpanel").show();
                $("#plesk").hide();
                $("#cpanel").hide();
                disableAll("#fpanel",false);
                disableAll("#plesk",true);
                disableAll("#cpanel",true);
                break;
            case "plesk":
                $("#fpanel").hide();
                $("#plesk").show();
                $("#cpanel").hide();
                disableAll("#fpanel",true);
                disableAll("#plesk",false);
                disableAll("#cpanel",true);
                break;
            case "cpanel":
                $("#fpanel").hide();
                $("#plesk").hide();
                $("#cpanel").show();
                disableAll("#fpanel",true);
                disableAll("#plesk",true);
                disableAll("#cpanel",false);
                break;
        }
    });

    function disableAll(element,state) {
        $.each($(element).find("input"), function() {
            $(this).prop("disabled",state);
        });
        $.each($(element).find("select"), function() {
            $(this).prop("disabled",state);
        });
        $.each($(element).find("textarea"), function() {
            $(this).prop("disabled",state);
        });
    }

</script>
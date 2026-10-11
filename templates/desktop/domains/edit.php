<div class="row" id="domain-edit-panel" data-width="60%">
    <form id="edit-domain-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo $client_data['user']['info']['username'];?></div>
                <div class="panel-body">
                    <span class="text-muted small pull-right"><? echo __('Last Login');?> <i class="fa fa-clock-o"></i> <? echo $this->env->convertDate($client_data['user']['info']['last_login']);?></span>
                    <br />
                    <div>
                        <div class="row">
                            <div class="col-md-3">
                                <strong><? echo __('Change Password');?></strong>
                                <p class="muted"><? echo __('You can change your account password from here');?></p>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fullname"><? echo __('Fullname');?></label>
                                            <? echo $form->render('fullname',array('class' => 'form-control input-sm')); ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="username"><? echo __('Username');?></label>
                                            <? echo $form->render('username',array('class' => 'form-control input-sm')); ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="parents"><? echo __('Parent');?></label>
                                            <? echo $form->render('parents',array('class' => 'form-control input-sm')); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password"><? echo __('New Password');?></label>
                                            <div class="input-group input-group-sm" id="input-group-password">
                                                <? echo $form->render("password",array('class' => 'form-control')) ?>
                                                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Show Password');?>" data-toggle="tooltip" data-type="show-passwd" class="btn btn-default"><i class="fa fa-eye"></i></button></span>
                                                <span class="input-group-btn"><button type="button" data-placement="top" data-original-title="<? echo __('Generate Password');?>" data-toggle="tooltip" data-type="password-generate" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm-password"><? echo __('Confirm Password');?> *</label>
                                            <? echo $form->render("password",array('id' => 'confirm-password','name' => 'confirm-password','class' => 'form-control input-sm')) ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="templates"><? echo __('Apply new Limits from Templates');?></label>
                                            <? echo $form->render('templates',array('class' => 'form-control input-sm')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-3">
                                <strong><? echo __('Contact details');?></strong>
                                <p class="muted"><? echo __('You can change contact informations from here');?></p>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label for="telephone"><? echo __('Phone');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                    <? echo $form->render('telephone',array('class' => 'form-control input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="email_addr"><? echo __('E-Mail');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                                    <? echo $form->render('email_addr',array('class' => 'form-control input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="fax"><? echo __('Fax');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-fax"></i></span>
                                                    <? echo $form->render('fax',array('class' => 'form-control input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="company"><? echo __('Company');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-tags"></i></span>
                                                    <? echo $form->render('company',array('class' => 'form-control input-sm')); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label for="address"><? echo __('Address');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-university"></i></span>
                                                    <? echo $form->render('address',array('class' => 'form-control input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="city"><? echo __('City');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                                    <? echo $form->render('city',array('class' => 'form-control input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="region"><? echo __('Region');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-road"></i></span>
                                                    <? echo $form->render('region',array('class' => 'form-control input-sm')); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="country"><? echo __('Country');?></label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                                                    <? echo $form->render('country',array('class' => 'form-control')); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><? echo __('Features');?></div>
                <div class="panel-body">
                    <div class="tab-content">
                        <div id="contact-1" class="tab-pane active">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="reset" class="btn btn-default btn-sm btn-block"> <? echo __('Cancel');?> </button>
                                    <button class="btn btn-sm btn-primary btn-block" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                                </div>

                            </div>
                            <div class="client-detail">
                                <div class="">
                                    <strong><? echo __('About User');?></strong>
                                    <ul class="list-group clear-list">
                                        <li class="list-item fist-item">
                                            <span class="pull-right"> <? echo __('Domain');?> </span>
                                            <? echo __('User Type');?>
                                        </li>
                                        <li class="list-item">
                                            <span class="pull-right"> <? echo $this->env->convert($client_data['user']['statistics']['used_total_disk']);?> </span>
                                            <? echo __('Disk Used');?>
                                        </li>
                                        <li class="list-item">
                                            <span class="pull-right"> <? echo $this->env->convert($client_data['user']['statistics']['used_total_traffic']);?> </span>
                                            <? echo __('Traffic Used');?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $('#parents').on('change', function () {
        bootbox.confirm('<? echo __('Are you sure?');?>', function (result) {
            var data = {};
            data['parent'] = $('#parents').val();
            if (result == true) {
                $.ajax({
                    type: 'POST',
                    url: '<? echo $this->uri->url_encrypt('domains/changeparent/'.$client_data['user']['domain_info']['domain_id']);?>',
                    data: data,
                    beforeSend:function() { $.blockUI({ message: $('#domMessage'),css:  { border:'none',background:'none',width:'30%'} });  },
                    complete: function() { $.unblockUI(); },
                    success: function (dataRules) {
                        var obj;
                        try {
                            obj = $.parseJSON(dataRules);
                        }
                        catch(e) {
                            obj = {message:'error',content:'<? echo __('Session Timeout');?>'};
                        }
                        if(obj['message'] == "505") {
                            toastr["error"]("System Error");
                            setTimeout(function(){window.location.assign('/error.php')}, 1000);
                        }
                        if(obj['message'] == "logout") {
                            toastr["error"](variables['session_timeout']);
                            setTimeout(function(){window.location.assign('/')}, 1000);
                        }
                        switch (obj['message']) {
                            case "success":
                                toastr["success"](obj['content'],obj['title']);
                                break;
                            case "info":
                                toastr["info"](obj['content'],obj['title']);
                                break;
                            default:
                                toastr["error"](obj['content'],obj['title']);
                                break;
                        }
                    }
                });
            }
        });
    });

    $('#templates').on('change', function () {
        var template_id = $('#templates').val();
        if(template_id != "0") {
            bootbox.confirm('<? echo __('Are you sure?');?>', function (result) {
                var data = {};
                data['serviceplan'] = template_id;
                if (result == true) {
                    $.ajax({
                        type: 'POST',
                        url: '<? echo $this->uri->url_encrypt('domains/changelimits/'.$client_data['user']['domain_info']['domain_id']);?>',
                        data: data,
                        beforeSend:function() { $.blockUI({ message: $('#domMessage'),css:  { border:'none',background:'none',width:'30%'} });  },
                        complete: function() { $.unblockUI(); },
                        success: function (dataRules) {
                            var obj;
                            try {
                                obj = $.parseJSON(dataRules);
                            }
                            catch(e) {
                                obj = {message:'error',content:'<? echo __('Session Timeout');?>'};
                            }
                            if(obj['message'] == "505") {
                                toastr["error"]("System Error");
                                setTimeout(function(){window.location.assign('/error.php')}, 1000);
                            }
                            if(obj['message'] == "logout") {
                                toastr["error"](variables['session_timeout']);
                                setTimeout(function(){window.location.assign('/')}, 1000);
                            }
                            switch (obj['message']) {
                                case "success":
                                    toastr["success"](obj['content'],obj['title']);
                                    break;
                                case "info":
                                    toastr["info"](obj['content'],obj['title']);
                                    break;
                                default:
                                    toastr["error"](obj['content'],obj['title']);
                                    break;
                            }
                        }
                    });
                }
            });
        }
    });
    core.setWidth({target: 'domain-edit-panel'});
    core.inputInits();
</script>
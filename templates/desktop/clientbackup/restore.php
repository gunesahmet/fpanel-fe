<form id="form" method="post" class="form-horizontal">
    <div class="row">
        <div class="col-lg-7">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><? echo __('Restore Options');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_all"> <strong><? echo __('Select All');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_all',array('class' => 'checkbox')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_domain_ftproot"> <strong><? echo __('Restore Domains FTP Root');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_domain_ftproot',array('class' => 'checkbox')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_subdomains"> <strong><? echo __('Restore Domain Subdomains');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_subdomains',array('class' => 'checkbox')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_databases"> <strong><? echo __('Restore Domain Databases');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_databases',array('class' => 'checkbox')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="restore_dns"> <strong><? echo __('Restore Domain DNS Records');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $form->render('restore_dns',array('class' => 'checkbox')); ?>
                        </div>
                    </div>

                    <hr />
                    <div class="form-group tabbable-custom">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="active" data-toggle="tab"><a href="#users" data-toggle="tab"><i></i><span class="strong"><? echo __('Users');?> &amp; <? echo __('Domains');?></span></a></li>
                        </ul>

                        <div class="tab-content">

                            <div class="tab-pane active" id="users">
                                <div class="form-group">
                                    <div class="col-md-9">
                                        <? echo $form->render('domain_list',array('class' => 'searchable','data-selectable' => __('Available'),'data-selected' => __('Selected'))); ?>
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-11">
                            <div class="pull-right">
                                <button type="reset" class="btn btn-default btn-sm"> <? echo __('Cancel');?> </button>

                                <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><? echo __('Overview');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><? echo __('Backup Name');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $config['backup_data']['name']; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><? echo __('Description');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $config['backup_data']['description']; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><? echo __('Created Date');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $this->env->convertDate($config['backup_data']['created_date']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><? echo __('Backup Size');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <? echo $this->env->convert($backup_content['size']['backup_size']); ?>
                        </div>
                    </div>



                    <hr class="separator" />

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="backup_name"> <strong><? echo __('Backup Content');?></strong></label>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <th><? echo __('Content');?></th>
                                    <th><? echo __('Status');?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><? echo __('Client Accounts');?></td>
                                    <td><? echo "<span class='label label-success'>".__('Included')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('Domains');?></td>
                                    <td><? if(!empty($config['selected_domains'])) echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('Domain FTP Root Directories');?></td>
                                    <td><? if($config['backup_data']['content']['www_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('Subdomains');?></td>
                                    <td><? if($config['backup_data']['content']['subdomain_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('Databases');?></td>
                                    <td><? if($config['backup_data']['content']['database_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('DNS Settings');?></td>
                                    <td><? if($config['backup_data']['content']['dns_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                <tr>
                                    <td><? echo __('Mail Accounts');?></td>
                                    <td><? if($config['backup_data']['content']['mail_content'] == "true") echo "<span class='label label-success'>".__('Included')."</span>"; else echo "<span class='label label-danger'>".__('Excluded')."</span>";?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('input[id="restore_all"]').unbind();
    $('input[id="restore_all"]').bind('click', function(){
        if ($(this).is(':checked')) {
            $('input[id="domain_content"]').prop('checked', true);
            $('input[id="restore_sp"]').prop('checked', true);
            $.uniform.update('input[type="checkbox"]');
            $('.searchable').multiSelect('select_all');
        }
        else {
            $('input[id="domain_content"]').prop('checked', false);
            $('input[id="restore_sp"]').prop('checked', false);
            $.uniform.update('input[type="checkbox"]');
            $('.searchable').multiSelect('deselect_all');
        }

    });

</script>

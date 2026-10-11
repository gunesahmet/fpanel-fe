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
            <div class="col-md-6">
                <form id="server-time-settings-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="system_date"><?= __('Date (YYYY/MM/DD)');?> </label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('system_date',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="system_time"><?= __('Time (HH:MM:SS)');?> </label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('system_time',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <hr />

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="time_zone"><?= __('Your Timezone');?> </label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('time_zone',array('class' => 'form-control form-control-sm input-sm select-picker', "data-live-search" => "true", "data-size" => "10")); ?>
                        </div>
                    </div>
                    <hr />
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="sync_time"><?= __('Synchronize System Time');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('sync_time',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="timezone_addr"><?= __('Domain Name or IP Address');?> </label>
                        </div>

                        <div class="col-md-8">
                            <?= $form->render('ntp_address',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <hr />
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
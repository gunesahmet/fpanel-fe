<div class="row">
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <span class="label label-primary float-right"><?= __("Telegram Notification");?></span>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-6">
                        <form id="system-telegram-form" class="form-horizontal" method="post" action="<?= $this->uri->url_encrypt('telegram')?>">
                            <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="api_key"> <?= __('Api Key');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render("api_key",["class" => "form-control form-control-sm input-sm", 'placeholder' => __('Enter Api Key')]);?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="telegram_status"> <?= __('Telegram Status');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('telegram_status',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>
                            <div class="divider"><span></span><span> <?= __('Alerts');?> </span><span></span></div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="service_status"> <?= __('Service Status');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('service_status',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="backup_restore"> <?= __('Backup/Restore Status');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('backup_restore',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="domain_create"> <?= __('Domain Create');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('domain_create',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="network_changes"> <?= __('Network Changes');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('network_changes',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="system_updates"> <?= __('Updates');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('system_updates',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="load_average"> <?= __('Load Average');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('load_average',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="load_average_values"> <?= __("Minimum Load Averages");?> </label>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm" id="input-group-webook-url">
                                        <?= $form->render("load_average_1min", ["class" => "form-control form-control-sm input-sm"]);?>
                                        <span class="input-group-addon">-</span>
                                        <?= $form->render("load_average_5min", ["class" => "form-control form-control-sm input-sm"]);?>
                                        <span class="input-group-addon">-</span>
                                        <?= $form->render("load_average_15min", ["class" => "form-control form-control-sm input-sm"]);?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="disk_status"> <?= __('Disk Status');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('disk_status',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="disk_status_value"> <?= __('Warn n GB left before disk is full');?> </label>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm" id="input-group-webook-url">
                                        <?= $form->render("disk_status_value", ["class" => "form-control form-control-sm input-sm"]);?>
                                        <span class="input-group-addon">GB</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="ddos_attack"> <?= __('Possible DDOS Attacks');?> </label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('ddos_attack',array('class' => 'checkbox-blue')); ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                        <button class="btn btn-sm btn-primary" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <span class="text-danger"><?= __("Important");?></span>: <?= __("All alerts will send one time every day.");?>
                            <?= __("All Telegram bot communication is one-way.");?>
                            <?= __("If you don't know how to create a telegram bot, please follow instructions below.");?>
                        </p>

                        <img src="/templates/default/assets/images/telegram-1.jpg" alt="Start with Botfather" class="img-fluid img-thumbnail thumb">
                        <img src="/templates/default/assets/images/telegram-2.jpg" alt="Botfather options" class="img-fluid img-thumbnail thumb">
                        <img src="/templates/default/assets/images/telegram-3.jpg" alt="Botfather Create New Bot" class="img-fluid img-thumbnail thumb">
                        <img src="/templates/default/assets/images/telegram-4.jpg" alt="Botfather Bot Success" class="img-fluid img-thumbnail thumb">
                        <img src="/templates/default/assets/images/telegram-5.jpg" alt="Setting Bot Name" class="img-fluid img-thumbnail thumb">
                        <img src="/templates/default/assets/images/telegram-6.jpg" alt="Setting Bot Username" class="img-fluid img-thumbnail thumb">
                        <img src="/templates/default/assets/images/telegram-7.jpg" alt="Bot created and getting api key" class="img-fluid img-thumbnail thumb">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
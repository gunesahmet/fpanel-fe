<div class="row">
    <div class="col-lg-8">
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('Update Lists');?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-hover dynamic-table" data-url="<?= $this->uri->url_encrypt('update');?>" data-select="false" data-buttons="true" data-actions="false">
                            <thead>
                            <tr>
                                <th width="20%"><?= __('Package');?></th>
                                <th width="20%"><?= __('Version');?></th>
                                <th width="20%"><?= __('Updated Date');?></th>
                                <th width="20%"><?= __('Description');?></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('Update Settings');?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <form method="post" autocomplete="off" id="system-update-form" class="form-horizontal" action="<?= $this->uri->url_encrypt('update/settings')?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="auto_update"><?= __('Auto Update');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('auto_update',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="random_time"><?= __('Random Time');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('random_time',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="update_time"><?= __('Update Time');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('update_time',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>
                    <hr />
                    <h5><?= __("Statistics Settings");?></h5>
                    <hr />
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="statistics_random"><?= __('Random Time');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('statistics_random',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="statistics_time"><?= __('Running Time');?></label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('statistics_time',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
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
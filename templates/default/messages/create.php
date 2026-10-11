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
            <div class="col-md-7">
                <form id="create-sys-message-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="status"> <?= __('Status');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('status',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="title"> <?= __('Message Title');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('title',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="description"> <?= __('Description');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('description',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="expire_date"> <?= __('Expire After Day');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('expire_date',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="type"> <?= __('Message Type');?> </label>
                        </div>
                        <div class="col-md-8">
                            <?= $form->render('type',array('class' => 'form-control form-control-sm input-sm')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="message_content"> <?= __('Message');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="widget-body">
                                <?= $form->render('message_content',array('class' => 'form-control form-control-sm input-sm')); ?>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
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
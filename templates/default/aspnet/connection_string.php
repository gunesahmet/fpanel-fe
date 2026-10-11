<h4 class="heading-arrow"><?= __('Connection String Manager');?></h4>
<div class="row">
    <div class="col-md-3">
        <span class="strong"><?= __('Name');?></span>
    </div>
    <div class="col-md-5">
        <span class="strong"><?= __('Connection String');?></span>
    </div>
    <div class="col-md-4">
        <span class="strong"><?= __('Connection String Provider');?></span>
    </div>
</div>
<div class="form-group">
    <div class="col-md-3">
        <?= $form->render('connStrName',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
    <div class="col-md-5">
        <?= $form->render('connectionString',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
    <div class="col-md-4">
        <?= $form->render('connectionStringProvider',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
<br />
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-striped table-primary">
                <thead>
                <tr>
                    <th><?= __('Connection Name'); ?></th>
                    <th><?= __('String'); ?></th>
                    <th><?= __('Provider'); ?></th>
                    <th><?= __('Action'); ?></th>
                </tr>
                </thead>
                <tbody>
                <? foreach($conn_list as $value) { ?>
                    <tr>
                        <td><?= $value['0']?></td>
                        <td><?= $value['1'];?></td>
                        <td><?= $value['2'];?></td>
                        <td><?= $value['3'];?></td>
                    </tr>
                <? } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

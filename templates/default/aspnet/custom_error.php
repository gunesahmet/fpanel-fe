<h4 class="heading-arrow"><?= __('Custom Error Settings');?></h4>
<div class="form-group">
    <div class="col-md-3">
        <span class="strong"><?= __('Custom Error Mode');?></span>
    </div>
    <div class="col-md-7">
        <?= $form->render('customErrMode',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
<br />
<div class="col-md-3">
    <span class="strong"><?= __('Status Code');?></span>
</div>
<div class="col-md-7">
    <span class="strong"><?= __('Redirect URL');?></span>
</div>
<br />
<div class="form-group">
    <div class="col-md-3">
        <?= $form->render('customErrCode',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
    <div class="col-md-7">
        <?= $form->render('customErrUrl',array('class' => 'form-control form-control-sm input-sm'));?>
    </div>
</div>
<table class="table table-bordered table-condensed table-striped table-primary">
    <thead>
    <tr>
        <th style="width: 30% !important;"><?= __('Status Code'); ?></th>
        <th style="width: 50% !important;"><?= __('Redirect URL'); ?></th>
        <th style="width: 20% !important;"><?= __('Action'); ?></th>
    </tr>
    </thead>
    <tbody>
    <? foreach($err_list as $value) { ?>
        <tr>
            <td><?= $value['0']?></td>
            <td><?= $value['1'];?></td>
            <td><?= $value['2'];?></td>
        </tr>
    <? } ?>
    </tbody>
</table>

<form id="create-database-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="form-group">
        <div class="col-md-4">
            <label for="task_status"> <? echo __('Cronjob Status');?> </label>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    <? echo $form->render('task_status',array('class' => 'checkbox-blue'));?>
                </div>
            </div>
        </div>
    </div>

    <!-- Minute -->
    <div class="form-group">
        <div class="col-md-4">
            <label for="minute"> <? echo __('Minute');?> </label>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <? echo $form->render('minute',array('class' => 'form-control input-sm'));?>
                </div>
                <div class="col-md-8">
                    <select id="minute_select" name="minute_select" class="form-control input-sm">
                        <option value="*/5" selected="selected"><? echo __('Every %s% Minutes',['s' => '5']). '(*/5)';?></option>
                        <option value="*/10"><? echo __('Every %s% Minutes',['s' => '10']). '(*/10)';?></option>
                        <option value="*/15"><? echo __('Every %s% Minutes',['s' => '15']). '(*/15)';?></option>
                        <option value="*/30"><? echo __('Every %s% Minutes',['s' => '30']). '(*/30)';?></option>
                        <? for($i = 0; $i<=59; $i++) {?>
                            <option value="<? echo $i;?>"><? echo $i;?></option>
                        <? } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Hour -->
    <div class="form-group">
        <div class="col-md-4">
            <label for="hour"> <? echo __('Hour');?> </label>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <? echo $form->render('hour',array('class' => 'form-control input-sm'));?>
                </div>
                <div class="col-md-8">
                    <? $hour_array = array(
                        '*/2'   => __('Every %s% Hours',['s' => '2']). '(*/2)',
                        '*/3'   => __('Every %s% Hours',['s' => '3']). '(*/3)',
                        '*/4'   => __('Every %s% Hours',['s' => '4']). '(*/4)',
                        '*/5'   => __('Every %s% Hours',['s' => '5']). '(*/5)',
                        '*/6'   => __('Every %s% Hours',['s' => '6']). '(*/6)',
                        '*/12'   => __('Every %s% Hours',['s' => '12']). '(*/12)',
                        '0'     => __('Once at').' 00:00 (0)',
                        '1'     => __('Once at').' 01:00 (1)',
                        '2'     => __('Once at').' 02:00 (2)',
                        '3'     => __('Once at').' 03:00 (3)',
                        '4'     => __('Once at').' 04:00 (4)',
                        '5'     => __('Once at').' 05:00 (5)',
                        '6'     => __('Once at').' 06:00 (6)',
                        '7'     => __('Once at').' 07:00 (7)',
                        '8'     => __('Once at').' 08:00 (8)',
                        '9'     => __('Once at').' 09:00 (9)',
                        '10'    => __('Once at').' 10:00 (10)',
                        '11'    => __('Once at').' 11:00 (11)',
                        '12'    => __('Once at').' 12:00 (12)',
                        '13'    => __('Once at').' 13:00 (13)',
                        '14'    => __('Once at').' 14:00 (14)',
                        '15'    => __('Once at').' 15:00 (15)',
                        '16'    => __('Once at').' 16:00 (16)',
                        '17'    => __('Once at').' 17:00 (17)',
                        '18'    => __('Once at').' 18:00 (18)',
                        '19'    => __('Once at').' 19:00 (19)',
                        '20'    => __('Once at').' 20:00 (20)',
                        '21'    => __('Once at').' 21:00 (21)',
                        '22'    => __('Once at').' 22:00 (22)',
                        '23'    => __('Once at').' 23:00 (23)',
                    );?>
                    <select id="hour_select" name="hour_select" class="form-control input-sm">
                        <option value="*"><? echo __('Every Hour'). ' (*) ';?></option>
                        <? foreach($hour_array as $key => $value) { ?>
                            <option value="<? echo $key; ?>"><? echo $value;?></option>
                        <? } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Day -->
    <div class="form-group">
        <div class="col-md-4">
            <label for="day"> <? echo __('Day');?> </label>
        </div>
        <div class="col-md-8">
           <div class="row">
               <div class="col-md-4">
                   <? echo $form->render('day',array('class' => 'form-control input-sm'));?>
               </div>
               <div class="col-md-8">
                   <? echo $form->render('day_select',array('class' => 'form-control input-sm'));?>
               </div>
           </div>
        </div>
    </div>

    <!-- Week -->
    <div class="form-group">
        <div class="col-md-4">
            <label for="week"> <? echo __('Week');?> </label>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <? echo $form->render('week',array('class' => 'form-control input-sm'));?>
                </div>
                <div class="col-md-8">
                    <? echo $form->render('week_select',array('class' => 'form-control input-sm'));?>
                </div>
            </div>
        </div>
    </div>

    <!-- Month -->
    <div class="form-group">
        <div class="col-md-4">
            <label for="month"> <? echo __('Month');?> </label>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <? echo $form->render('month',array('class' => 'form-control input-sm'));?>
                </div>
                <div class="col-md-8">
                    <? echo $form->render('month_select',array('class' => 'form-control input-sm'));?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="description"> <? echo __('Cronjob Description');?> </label>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    <? echo $form->render('description',array('class' => 'form-control input-sm'));?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-4">
            <label for="command"> <? echo __('Cronjob Command');?> </label>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    <? echo $form->render('command',array('class' => 'form-control input-sm'));?>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-right">
                        <button class="btn btn-sm btn-default" type="reset"> <? echo __('Cancel'); ?></button>
                        <button class="btn btn-primary btn-sm" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    core.inputInits();
</script>
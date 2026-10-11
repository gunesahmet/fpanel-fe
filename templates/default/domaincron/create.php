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
                <form id="create-sch-task-form" method="post" class="form-horizontal" action="<?= $post_url;?>">
                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="task_status"> <?= __('PHP Script');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $form->render('php_script',array('class' => 'checkbox-blue'));?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="task_status"> <?= __('Cronjob Status');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $form->render('task_status',array('class' => 'checkbox-blue'));?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Minute -->
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="minute"> <?= __('Minute');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $form->render('minute',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                                <div class="col-md-8">
                                    <select id="minute_select" name="minute_select" class="form-control form-control-sm input-sm">
                                        <option value="*/5" selected="selected"><?= __('Every').' 5 '.__('Minutes'). '(*/5)';?></option>
                                        <option value="*/10"><?= __('Every').' 10 '.__('Minutes'). '(*/10)';?></option>
                                        <option value="*/15"><?= __('Every').' 15 '.__('Minutes'). '(*/15)';?></option>
                                        <option value="*/30"><?= __('Every').' 30 '.__('Minutes'). '(*/30)';?></option>
                                        <? for($i = 0; $i<=59; $i++) {?>
                                            <option value="<?= $i;?>"><?= $i;?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hour -->
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="hour"> <?= __('Hour');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $form->render('hour',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                                <div class="col-md-8">
                                    <? $hour_array = array(
                                        '*/2'   => __('Every').' 2 ' .__('Hours'). '(*/2)',
                                        '*/3'   => __('Every').' 3 ' .__('Hours'). '(*/3)',
                                        '*/4'   => __('Every').' 4 ' .__('Hours'). '(*/4)',
                                        '*/5'   => __('Every').' 5 ' .__('Hours'). '(*/5)',
                                        '*/6'   => __('Every').' 6 ' .__('Hours'). '(*/6)',
                                        '*/12'  => __('Every').' 12 '.__('Hours'). '(*/12)',
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
                                    <select id="hour_select" name="hour_select" class="form-control form-control-sm input-sm">
                                        <option value="*"><?= __('Every Hour'). ' (*) ';?></option>
                                        <? foreach($hour_array as $key => $value) { ?>
                                            <option value="<?= $key; ?>"><?= $value;?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Day -->
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="day"> <?= __('Day');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $form->render('day',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('day_select',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Week -->
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="week"> <?= __('Week');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $form->render('week',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('week_select',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Month -->
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="month"> <?= __('Month');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $form->render('month',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->render('month_select',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="description"> <?= __('Cronjob Description');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $form->render('description',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row" id="php-scripting">
                        <div class="col-md-4">
                            <label for="command"> <?= __('Cronjob Command');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group input-group-sm" id="input-group-domain_name">
                                        <span class="input-group-addon">php -q </span>
                                        <?= $form->render('php_command',array('class' => 'form-control form-control-sm input-sm'));?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row" id="sys-command" style="display: none">
                        <div class="col-md-4">
                            <label for="command"> <?= __('Cronjob Command');?> </label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $form->render('command',array('class' => 'form-control form-control-sm input-sm'));?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <button class="btn btn-sm btn-default" type="reset"> <?= __('Cancel'); ?></button>
                                        <button class="btn btn-primary btn-sm" type="button" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Save'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <pre class="well" style="white-space: unset;
    text-overflow: unset;
    word-break: break-all !important;
    word-wrap: break-word !important;">
                    <p>
                        <b class="text-danger">*</b>
                        <?php
/*
 * fpanel — a web-based hosting control panel.
 * Copyright (C) 2026  Ahmet Refik Gunes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */                        echo __("You can run php scripts with the <b class='text-danger'>Php Script</b> option. No jail system will be applied for this.")
                        ?>
                    </p>
                    <br />
                    <p>
                        <b class="text-danger">*</b>
                        <?php
/*
 * fpanel — a web-based hosting control panel.
 * Copyright (C) 2026  Ahmet Refik Gunes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */                        if($server_type == WINDOWS) {
                            $vhost = "c:\\vhosts\\" . $domain_name;
                        }
                        else {
                            $vhost = "/var/www/" . $domain_name;
                        }
                        echo __("If you don't select the <b class='text-danger'>Php Script</b> option, you can run a <b class='text-danger'>Shell Script</b> or <b class='text-danger'>System Commands</b> directly. However in this case, your main directory will be <b class='text-danger'>%vhost_dir%</b>. It is very important to consider this when editing your command.",["vhost_dir" => $vhost])
                        ?>
                    </p>
                </pre>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
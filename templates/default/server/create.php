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
            <div class="col-md-12">
                <div class="wizard-big">
                    <h1><?= __("Connection");?></h1>
                    <fieldset>
                        <div class="row">
                            <div class="col-lg-8">
                                <form id="server-connect" action="#" class="wizard-big form-horizontal" data-url="<?= $this->uri->encode("server/create");?>">
                                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="status"> <strong><?= __('Installed Agent?');?></strong></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('agent_status',array('class' => 'make-switch', 'data-size' => 'mini', "data-on" => __("Yes"), "data-off" => __("No"))); ?>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="server_os"> <strong><?= __('Operating System');?></strong> <b class="text-danger">*</b></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('server_os',array('class' => 'form-control form-control-sm select-picker')); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="ip_address"> <strong><?= __('Hostname');?></strong> <b class="text-danger">*</b></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('hostname',array('class' => 'form-control form-control-sm')); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="ip_address"> <strong><?= __('IP Address');?></strong> <b class="text-danger">*</b></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('ip_address',array('class' => 'form-control form-control-sm')); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="username"> <strong><?= __('Username');?></strong> <b class="text-danger">*</b></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('username',array('class' => 'form-control form-control-sm')); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="password"> <strong><?= __('Password');?></strong> <b class="text-danger">*</b></label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="input-group" id="input-group-password">
                                                <?= $form->render("password",array('class' => 'form-control form-control-sm')) ?>
                                                <span class="input-group-append"><button type="button" data-type="show-passwd"  tabindex="-1" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button></span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <div style="margin-top: 20px">
                                        <i class="fa fa-sign-in" style="font-size: 180px;color: #e5e5e5 "></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <h1><?= __("Info") ?></h1>
                    <fieldset>
                        <h2><?= __("Server Info") ?></h2>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group clear-list m-t">
                                    <li class="list-group-item">
                                    <span class="float-right" id="hostname">
                                        [HOSTNAME]
                                    </span>
                                        <i class="fp fp-local-net"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span><span class="path35"></span><span class="path36"></span><span class="path37"></span><span class="path38"></span><span class="path39"></span><span class="path40"></span><span class="path41"></span><span class="path42"></span><span class="path43"></span><span class="path44"></span><span class="path45"></span><span class="path46"></span><span class="path47"></span><span class="path48"></span><span class="path49"></span><span class="path50"></span><span class="path51"></span><span class="path52"></span><span class="path53"></span><span class="path54"></span><span class="path55"></span><span class="path56"></span><span class="path57"></span><span class="path58"></span><span class="path59"></span><span class="path60"></span><span class="path61"></span><span class="path62"></span><span class="path63"></span><span class="path64"></span><span class="path65"></span><span class="path66"></span><span class="path67"></span><span class="path68"></span><span class="path69"></span><span class="path70"></span><span class="path71"></span><span class="path72"></span><span class="path73"></span></i>
                                        <span class="text-primary"><?= __('Hostname');?></span>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="float-right" id="ip_address">
                                        [IP_ADDRESS]
                                    </span>
                                        <i class="fp fp-net-port"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span></i>
                                        <span class="text-primary"><?= __('Default IP Address');?></span>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="float-right" id="operating_system">
                                        [OPERATING_SYSTEM]
                                    </span>
                                        <i id="os_icon"></i>
                                        <span class="text-primary"><?= __('Operating System');?></span>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="float-right" id="cpu_brand">
                                        [CPU_BRAND]
                                    </span>
                                        <i class="fas fa-microchip"></i>
                                        <span class="text-primary "><?= __('CPU Type');?></span>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="float-right" id="total_memory">
                                        [TOTAL_MEMORY]
                                    </span>
                                        <i class="fas fa-memory"></i>
                                        <span class="text-primary"><?= __('Memory');?></span>
                                    </li>
                                    <li class="list-group-item">
                                    <span class="float-right" id="total_disk">
                                        [TOTAL_DISK]
                                    </span>
                                        <i class="fas fa-hdd"></i>
                                        <span class="text-primary"><?= __('Disk');?></span>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </fieldset>
                    <h1><?= __("Installation") ?></h1>
                    <fieldset>
                        <h2><?= __("Select Packages") ?></h2>
                        <div class="row">
                            <div class="col-md-8">
                                <form id="select-services" action="#" class="wizard-big form-horizontal" data-url="<?= $this->uri->encode("server/installation");?>">
                                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                    <div class="row">
                                        <? foreach ($server_pkgs as $title => $pkgs) { ?>
                                            <div class="col-lg-3">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <?= $title; ?>
                                                    </div>
                                                    <div class="panel-body">
                                                        <? foreach ($pkgs as $pkg => $label) { ?>
                                                            <div class="form-group" data-id="<?= $pkg;?>">
                                                                <div class="i-checks"><label> <?= $form->render($pkg,array('class' => 'checkbox-blue')); ?> <i></i> <?= $label;?> </label></div>
                                                            </div>
                                                        <? } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <? } ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </fieldset>
                    <h1><?= __("Save") ?></h1>
                    <fieldset>
                        <h2><?= __("Save Server") ?></h2>
                        <div class="row">
                            <div class="col-md-8">
                                <form id="save-server" action="#" class="wizard-big form-horizontal" data-url="<?= $this->uri->encode("server/save");?>">
                                    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
                                    <div class="form-group row" id="sql-pass">
                                        <div class="col-md-4">
                                            <label for="sql_password"> <strong><?= __('SQL Password');?></strong></label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="input-group clipboard">
                                                <div class="p-xs b-r-xs sql-pass" id="clipboard">[SQL_PASSWORD]</div>
                                                <span class="input-group-append"><a class="btn btn-white clipboard" data-clipboard-target="#clipboard"><i class="fa fa-copy"></i></a></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="server_name"> <strong><?= __('Server Name');?></strong> <b class="text-danger">*</b></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('server_name',array('class' => 'form-control form-control-sm')); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="domain_limit"> <strong><?= __('Domain Limit');?></strong> <b class="text-danger">*</b></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('domain_limit',array('class' => 'form-control form-control-sm')); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="database_limit"> <strong><?= __('Database Limit');?></strong> <b class="text-danger">*</b></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('database_limit',array('class' => 'form-control form-control-sm')); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="mail_limit"> <strong><?= __('Mail Limit');?></strong> <b class="text-danger">*</b></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('mail_limit',array('class' => 'form-control form-control-sm')); ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="description"> <strong><?= __('Description');?></strong></label>
                                        </div>
                                        <div class="col-md-8">
                                            <?= $form->render('description',array('class' => 'form-control form-control-sm')); ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
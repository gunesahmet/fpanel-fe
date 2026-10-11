<form method="post" id="form" class="form-horizontal">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-lg-5">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Settings');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="col-md-3"><label for="select_all_mig_resellers"> <strong><?= __('Select All Resellers');?></strong></label></div>
                        <div class="col-md-9"><input type="checkbox" class="checkbox-blue" checked="checked" id="select_all_mig_resellers" /></div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3"><label for="select_all_mig_clients"> <strong><?= __('Select All Clients');?></strong></label></div>
                        <div class="col-md-9"><input type="checkbox" class="checkbox-blue" checked="checked" id="select_all_mig_clients" /></div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3"><label for="select_all_mig_domains"> <strong><?= __('Select All Domains');?></strong></label></div>
                        <div class="col-md-9"><input type="checkbox" class="checkbox-blue" checked="checked" id="select_all_mig_domains" /></div>
                    </div>
                    <input type="hidden" name="remote_clients" id="remote_clients" value="" />
                    <input type="hidden" name="remote_domains" id="remote_domains" value="" />
                    <input type="hidden" name="remote_resellers" id="remote_resellers" value="" />


                    <hr />

                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="skip_global_settings"> <strong><?= __('Skip Global Settings');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('skip_global_settings',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="skip_domain_datas"> <strong><?= __('Skip Domain Datas');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('skip_domain_datas',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="replace_exists_accounts"> <strong><?= __('Replace Exists Users');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('replace_exists_accounts',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="replace_exists_domains"> <strong><?= __('Replace Exists Domains');?></strong></label>
                        </div>
                        <div class="col-md-6">
                            <?= $form->render('replace_exists_domains',array('class' => 'checkbox-blue')); ?>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-sm btn-default" type="reset"><?= __('Cancel'); ?></button>
                                <a class="btn btn-primary btn-sm" id="submit-form"><i class="fa fa-check-circle"></i> <?= __('Start'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?= __('Users & Domains');?></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div id="tree_remote">
                            <ul>
                                <li class="jstree-open"><?= __('Remote Server');?>
                                    <ul>
                                        <li class="jstree-open"><?= __('Resellers');?>
                                            <ul>
                                                <? if(!empty($remote_data['resellers'])) {
                                                    foreach($remote_data['resellers'] as $reseller_name => $reseller_clients) {?>
                                                        <li class="jstree-open" data-jstree='{"type":"reseller"}' data-id="reseller" id="<?= $reseller_name;?>"> <?= $reseller_name;?> <? $this->env->checkUser($reseller_name);?>
                                                            <ul>
                                                                <? if(!empty($reseller_clients['clients'])) {
                                                                    foreach($reseller_clients['clients'] as $reseller_client_name => $reseller_client) { ?>
                                                                        <li class="jstree-open" data-jstree='{"type":"client"}' data-id="client" id="<?= $reseller_client_name;?>"> <?= $reseller_client_name;?> <? $this->env->checkUser($reseller_client_name);?>
                                                                            <ul>
                                                                                <? if(!empty($reseller_client['domains']))foreach($reseller_client['domains'] as $domain) {?>
                                                                                    <li class="jstree-open" data-jstree='{"type":"domain"}' data-id="domain" id="<?= $domain;?>">
                                                                                        <?= $domain;?> <? $this->env->checkDomain($domain);?>
                                                                                    </li>
                                                                                <? } ?>
                                                                            </ul>
                                                                        </li>
                                                                    <? } ?>
                                                                    <?
                                                                    if(!empty($reseller_clients['domains'])) {
                                                                        foreach($reseller_clients['domains'] as $reseller_domain_name) { ?>
                                                                            <li class="jstree-open" data-jstree='{"type":"domain"}' data-id="domain" id="<?= $reseller_domain_name;?>">
                                                                                <?= $reseller_domain_name;?> <? $this->env->checkDomain($reseller_domain_name);?>
                                                                            </li>
                                                                        <? } ?>
                                                                    <? } ?>
                                                                <? } ?>
                                                            </ul>
                                                        </li>
                                                    <? } ?>
                                                <? } ?>
                                            </ul>
                                        </li>
                                        <li class="jstree-open"><?= __('Clients');?>
                                            <ul>
                                                <? if(!empty($remote_data['clients'])) {
                                                    foreach($remote_data['clients'] as $client_name => $client_domains) { ?>
                                                        <li class="jstree-open" data-jstree='{"type":"client"}' data-id="client" id="<?= $client_name;?>"> <?= $client_name;?> <? $this->env->checkUser($client_name);?>
                                                            <ul>
                                                                <? if(!empty($client_domains['domains'])) {
                                                                    foreach($client_domains['domains'] as $client_domain_name) { ?>
                                                                        <li class="jstree-open" data-jstree='{"type":"domain"}' data-id="domain" id="<?= $client_domain_name;?>">
                                                                            <?= $client_domain_name;?> <? $this->env->checkDomain($client_domain_name);?>
                                                                        </li>
                                                                    <? } ?>
                                                                <? } ?>
                                                            </ul>
                                                        </li>
                                                    <? } ?>
                                                <? } ?>
                                            </ul>
                                        </li>
                                        <li class="jstree-open"><?= __('Domains');?>
                                            <ul>
                                                <? if(!empty($remote_data)) { foreach($remote_data['domains'] as $domain) {?>
                                                    <li class="jstree-open" data-jstree='{"type":"domain"}' data-id="domain" id="<?= $domain;?>">
                                                        <?= $domain;?> <? $this->env->checkDomain($domain);?>
                                                    </li>
                                                <? } ?>
                                                <? } ?>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
<form class="form-horizontal" method="post">
    <input name="<?= $this->auth->getCsrfKey();?>" value="<?= $this->auth->getCsrfToken();?>" type="hidden">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="col-md-6">
                        <ul class="list-group clear-list m-t">

                            <li class="list-group-item fist-item">
                                   <span class="float-right">
                                        <?= F_VERSION;?>
                                   </span>
                                <?= __('Panel Version');?>
                            </li>
                            <li class="list-group-item">
                                   <span class="float-right">
                                        <?= ucfirst($this->session->get('server_init_data')['panel_type']);?>
                                   </span>
                                <?= __('System Type');?>
                            </li>
                            <li class="list-group-item">
                                   <span class="float-right">
                                        <?= $this->session->get('server_init_data')['username'];?>
                                   </span>
                                <?= __('Username');?>
                            </li>
                            <li class="list-group-item">
                                   <span class="float-right">
                                        <?= $this->session->get('server_init_data')['email'];?>
                                   </span>
                                <?= __('Email Address');?>
                            </li>

                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group clear-list m-t">
                            <li class="list-group-item fist-item">
                                   <span class="float-right">
                                        <?= ucfirst($this->session->get('server_init_data')['server_os']);?>
                                   </span>
                                <?= __('Server Operating System');?>
                            </li>
                            <li class="list-group-item fist-item">
                                   <span class="float-right">
                                        <?= $this->session->get('server_init_data')['hostname'];?>
                                   </span>
                                <?= __('Hostname');?>
                            </li>
                            <li class="list-group-item fist-item">
                                   <span class="float-right">
                                        <? foreach ($this->session->get('server_init_data')['ip_list'] as $ip) { ?>
                                            <?= $ip['2'];?><br />
                                        <? } ?>
                                   </span>
                                <?= __('IP Addresses');?>
                            </li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr />
                            <div class="float-right">
                                <a class="btn btn-sm btn-danger" href="<?= $this->uri->encode('install/generalinfo');?>"><i class="fa fa-backward"></i> <?= __('Go Back'); ?></a>
                                <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-check-circle"></i> <?= __('Finish'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

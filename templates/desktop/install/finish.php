<div class="col-md-6">
    <ul class="list-group clear-list m-t">

        <li class="list-item fist-item">
            <span class="pull-right">
                <? echo $this->env->readVersion();?>
            </span>
            <? echo __('Panel Version');?>
        </li>
        <li class="list-item">
            <span class="pull-right">
                <? echo ucfirst($this->session->get('server_init_data')['panel_type']);?>
            </span>
            <? echo __('Panel Type');?>
        </li>
        <li class="list-item">
            <span class="pull-right">
                <? echo $this->session->get('server_init_data')['username'];?>
            </span>
            <? echo __('Username');?>
        </li>
        <li class="list-item">
            <span class="pull-right">
                <? echo $this->session->get('server_init_data')['email'];?>
            </span>
            <? echo __('Email Address');?>
        </li>

    </ul>
</div>
<div class="col-md-6">
    <ul class="list-group clear-list m-t">
        <li class="list-item fist-item">
            <span class="pull-right">
                <? echo ucfirst($this->session->get('server_init_data')['server_os']);?>
            </span>
            <? echo __('Server Operating System');?>
        </li>
        <li class="list-item fist-item">
            <span class="pull-right">
                <? echo $this->session->get('server_init_data')['hostname'];?>
            </span>
            <? echo __('Hostname');?>
        </li>
        <li class="list-item fist-item">
            <span class="pull-right">
                <? foreach ($this->session->get('server_init_data')['ip_list'] as $ip) { ?>
                    <? echo $ip['2'];?><br />
                <? } ?>
            </span>
            <? echo __('IP Addresses');?>
        </li>
    </ul>
</div>
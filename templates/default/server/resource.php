<div class="row">
    <div class="col-lg-8">
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('General');?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <?= __('CPU Type');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['cpu_type'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Fpanel Version');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['version'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Operating System');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['os'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Server Uptime');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['uptime'];?>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('Clients');?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <?= __('Total');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['total_users'];?> <a href="<?= $this->uri->url_encrypt("users");?>"><?= __('Show');?></a>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Active');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['active_users'];?>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Passive');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['passive_users'];?>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Expired');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['expired_users'];?>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('Domains');?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <?= __('Total');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['total_domains'];?> <a href="<?= $this->uri->url_encrypt("domains");?>"><?= __('Show');?></a>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Active');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['active_domains'];?>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Passive');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['passive_domains'];?>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Expired');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['expired_domains'];?>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">


    <? if(PANEL_OS_TYPE == LINUX) { ?>
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('CPU Usage');?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <?= __('Last 1 Min');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['min1'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Last 5 Min');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['min5'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Last 15 Min');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['min15'];?>
                        </td>
                    </tr>


                    </tbody>
                </table>
            </div>
        </div>
    <? } ?>
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('RAM Usage');?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <?= __('Total');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['ram_total'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Used');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['ram_used'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Free');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['ram_free'];?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-title">
                <h5><?= __('Disk Usage');?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="icon-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <?= __('Total');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['disk_total'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Used');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['disk_used'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <?= __('Free');?>
                        </td>
                        <td style="width:50%">
                            <?= $resources['disk_free'];?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
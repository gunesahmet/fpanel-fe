<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('General');/*Genel*/?></div>
            <div class="panel-body">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <? echo __('CPU Type');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['cpu_type'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Fpanel Version');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['version'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Operating System');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['os'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Server Uptime');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['uptime'];?>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Clients');/*Müşteriler*/?></div>
            <div class="panel-body">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Total');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['total_users'];?> <a href="<? echo $this->uri->url_encrypt("domains");?>"><? echo __('Show');?></a>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Active');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['active_users'];?>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Passive');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['passive_users'];?>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Expired');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['expired_users'];?>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Domains');/*Domainler*/?></div>
            <div class="panel-body">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Total');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['total_domains'];?> <a href="<? echo $this->uri->url_encrypt("users");?>"><? echo __('Show');?></a>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Active');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['active_domains'];?>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Passive');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['passive_domains'];?>
                        </td>

                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Expired');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['expired_domains'];?>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">


    <? if(PANEL_OS_TYPE == "linux") { ?>
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('CPU Usage');/*CPU Kullanımı*/?></div>
            <div class="panel-body">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Last 1 Min');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['min1'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Last 5 Min');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['min5'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Last 15 Min');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['min15'];?>
                        </td>
                    </tr>


                    </tbody>
                </table>
            </div>
        </div>
    <? } ?>
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('RAM Usage');/*RAM Kullanımı*/?></div>
            <div class="panel-body">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Total');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['ram_total'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Used');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['ram_used'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Free');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['ram_free'];?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Disk Usage');/*Disk Kullanımı*/?></div>
            <div class="panel-body">
                <table id="user" class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Total');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['disk_total'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Used');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['disk_used'];?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:15%">
                            <? echo __('Free');?>
                        </td>
                        <td style="width:50%">
                            <? echo $resources['disk_free'];?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?
$general_items = $system_items['user']['info'];
if(($general_items['user_type'] > "0") && ($general_items['user_type'] < "6")) {
    $limit_items = $system_items['user']['limits'];
    $perm_items = $system_items['user']['permission'];
}
$statistics_items = $system_items['user']['usage'];
$last_login_date = "-";
if (!empty($general_items['last_login']) ) {
    $last_login_date = $general_items['last_login'];
} ?>
<ul class="gridster connectedLists" id="desktop">

</ul>
<div class="system-monitoring widget-panel widget-panel-default">
    <div class="widget-panel-heading"> System Monitoring </div>
    <ul class="list-group">
        <li class="list-group-item no-bg">
            <div class="row">
                <div class="col-md-12">
                    <h5><? echo __('CPU Type');?> </h5>
                    <p><? echo $cpu_type;?></p>
                    <br />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">CPU <span class="text-white"> <? echo $cpu_used?>%</span></div>
                <div class="col-sm-6">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="<? echo $cpu_used?>" aria-valuemin="0" aria-valuemax="100" style="width: <? echo $cpu_used?>%">
                            <span class="sr-only"><? echo $cpu_used?>% Used</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6"><? echo __('Memory');?> <span class="text-white"> <? echo $memory_used?>%</span></div>
                <div class="col-sm-6">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="<? echo $memory_used?>" aria-valuemin="0" aria-valuemax="100" style="width: <? echo $memory_used?>%">
                            <span class="sr-only"><? echo $memory_used?>% Used</span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li class="list-group-item no-bg">
            <h5><? echo __('Disk Usage');?></h5>
            <div class="media">
                <div class="media-left">
                    <p class="data-attributes media-object">
                        <span class="disk_usage" data-peity='{ "fill": ["#2D99DC", "#222D33"],    "innerRadius": 50, "radius": 69 }'><? echo $disk_used;?>/100</span>
                    </p>
                </div>
                <div class="media-body media-top">
                    <table class="table">
                        <tr>
                            <td><? echo __('Last Login Time');?>:</td>
                            <td><? echo $last_login_date;?></td>
                        </tr>
                        <tr>
                            <td><? echo __('Last Login IP');?>:</td>
                            <td><? echo $general_items['last_login_ip'];?></td>
                        </tr>
                        <tr>
                            <td><? echo __('Last Login IP');?>:</td>
                            <td><? echo $general_items['last_login_ip'];?></td>
                        </tr>
                        <tr>
                            <td><? echo __('Resellers');?>:</td>
                            <td><? echo $system_items['user']['usage']['reseller_count'];?></td>
                        </tr>
                        <tr>
                            <td><? echo __('Clients');?>:</td>
                            <td><? echo $system_items['user']['usage']['client_count'];?></td>
                        </tr>
                        <tr>
                            <td><? echo __('Domain');?>:</td>
                            <td><? echo $system_items['user']['usage']['domains'];?></td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <small><i class="fa fa-fw fa-circle"></i> <? echo __('Used');?></small>
                        <h5><? echo $this->env->convert($system_items['system_info']['disk']['used']);?></h5>
                    </div>
                    <div class="col-sm-4">
                        <small><i class="fa fa-fw fa-circle"></i> <? echo __('Free');?></small>
                        <h5><? echo $this->env->convert($system_items['system_info']['disk']['free']);?></h5>
                    </div>
                    <div class="col-sm-4">
                        <small><i class="fa fa-fw fa-circle"></i> <? echo __('Total');?></small>
                        <h5><? echo $this->env->convert($system_items['system_info']['disk']['total']);?></h5>
                    </div>
                </div>
            </div>
        </li>
        <li class="list-group-item no-bg">
            <h5><? echo __('Build')?></h5>
            <p><? echo 'Fpanel ' . $system_version;?> <i class="fa fa-fw fa-info-circle"></i></p>
        </li>
    </ul>
</div>

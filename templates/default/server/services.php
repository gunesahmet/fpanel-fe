<?
$server_link = (isset($server_id) == true ? "/server/$server_id" : '');
$service_running = '<span data-toggle="tooltip" data-placement="right" title="'. __('Service is running') .'"><i class="fa text-info fa-check-circle"></i></span>';
$service_stopped = '<span data-toggle="tooltip" data-placement="right" title="'. __('Service is not running') .'"><i class="fa text-danger fa-check-circle"></i></span>';
$service_not_installed = '<span data-toggle="tooltip" data-placement="right" title="'. __('Service is not installed') .'"><i class="fa text-danger fa-times"></i></span>';
$i = 1;
?>
<div class="row">
    <div class="col-md-12">
        <div class="row">
        <? foreach($services as $key => $service_list) { ?>
            <div class="col-md-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                            <span class="label label-primary"> <?= str_replace("_", " ", $key);?></span>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="fm-table col-md-12 noselect">
                                <div class="fm-table-body">
                                    <? foreach($service_list as $service) { ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <? if($service['service_state'] == "installed") {
                                                if($service['service_status'] == "on")
                                                    echo $service_running;
                                                else
                                                    echo $service_stopped;
                                            } else
                                                echo $service_not_installed;?>
                                            <?= $service['service_desc'];?>
                                        </div>
                                        <div class="col-md-6">
                                            <?
                                            $start_url = "#";
                                            $stop_url = '#';
                                            $restart_url = '#';
                                            $class = "disabled";

                                            $start_class = "";
                                            $stop_class = "";
                                            $restart_class = "";
                                            if($service['service_state'] == "installed") {
                                                $stop_url = $this->uri->url_encrypt("services/stop/".$this->crypto->encryptSession($service["service_name"]).$server_link);
                                                $start_url = $this->uri->url_encrypt("services/start/".$this->crypto->encryptSession($service["service_name"]) .$server_link);
                                                $restart_url = $this->uri->url_encrypt("services/restart/".$this->crypto->encryptSession($service["service_name"]).$server_link);
                                                if($service['service_status'] == "off") {
                                                    $start_class = "";
                                                    $restart_class = "disabled";
                                                    $stop_class = "disabled";
                                                }
                                                else {
                                                    $stop_class = "";
                                                    $start_class = "disabled";
                                                    $restart_class = "";
                                                }
                                                $class = "";
                                            } ;?>
                                            <a data-id="confirm" data-url="<?= $stop_url;?>" data-sid="services" class="btn btn-xs btn-default btn-outline btn-sm-block btn-xs-block btn-w-m <?= $class.$stop_class;?>"><i class="fa fa-power-off"></i> <?= __('Stop');?></a>
                                            <a data-id="confirm" data-url="<?= $restart_url;?>" data-sid="services" class="btn btn-xs btn btn-success btn-sm-block btn-xs-block btn-w-m <?= $class.$restart_class;?>"><i class="fa fa-sync-alt"></i> <?= __('Restart');?></a>
                                            <a data-id="confirm" data-url="<?= $start_url;?>" data-sid="services" class="btn btn-xs btn-primary btn-sm-block btn-xs-block btn-w-m <?= $class.$start_class;?>"><i class="fa fa-play-circle"></i> <?= __('Start');?></a>
                                        </div>
                                    </div>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
        </div>
    </div>
</div>

<script>breadcrumbs = <?= json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES);?>;var lvar = <?= $script_vars;?>;</script>
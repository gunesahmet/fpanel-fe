<?
$server_link = (isset($server_id) == true ? "/server/$server_id" : '');
$service_running = '<span title="'. __('Service is running') .'"><i class="fa text-info fa-check-circle"></i></span>';
$service_stopped = '<span title="'. __('Service is not running') .'"><i class="fa text-danger fa-check-circle"></i></span>';
$service_not_installed = '<span title="'. __('Service is not installed') .'"><i class="fa text-danger fa-times"></i></span>';
?>
<div class="row">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <tbody>
            <? foreach($services as $key => $service_list) { ?>
                <? foreach($service_list as $service) { ?>
                    <tr>
                        <td width="2%">
                            <? if($service['service_state'] == "installed") {
                                if($service['service_status'] == "on")
                                    echo $service_running;
                                else
                                    echo $service_stopped;
                            } else
                                echo $service_not_installed;?>
                        </td>
                        <td width="50%">
                            <? echo $service['service_desc'];?>
                        </td>
                        <td width="48%">
                            <div class="pull-right">
                                <a href="<? echo $this->uri->url_encrypt("services/stop/$key".$server_link);?>" title="<? echo __('Stop');?>" data-message="<? echo __('Are you sure?') ?>" id="services" class="btn btn-xs btn-danger"><i class="fa fa-power-off"></i></a>
                                <a href="<? echo $this->uri->url_encrypt("services/restart/$key".$server_link);?>" title="<? echo __('Restart');?>" data-message="<? echo __('Are you sure?') ?>" id="services" class="btn btn-xs btn-warning"><i class="fa fa-refresh"></i></a>
                                <a href="<? echo $this->uri->url_encrypt("services/start/$key".$server_link);?>" title="<? echo __('Start');?>" data-message="<? echo __('Are you sure?') ?>" id="services" class="btn btn-xs btn-primary"><i class="fa fa-play-circle"></i></a>
                            </div>
                        </td>
                    </tr>
                <? } ?>
            <? } ?>
            </tbody>
        </table>
    </div>
</div>

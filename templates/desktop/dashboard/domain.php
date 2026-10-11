<?
if(isset($client_data)) {
    $user_data = $client_data;
}
$limits = $user_data['user']['limits'];

$disk_limit = $this->env->convert($limits['disk_limit']);
$traffic_limit = $this->env->convert($limits['traffic_limit']);
$used_disk = $this->env->convert($user_data['user']['statistics']['used_total_disk']);
$used_traffic = $this->env->convert($user_data['user']['statistics']['used_total_traffic']);
$remain_disk = $this->env->convert($user_data['user']['statistics']['remain_total_disk']);
$remain_traffic = $this->env->convert($user_data['user']['statistics']['remain_total_traffic']);

$percent_disk = $user_data['user']['statistics']['percent_disk'];
$percent_traffic = $user_data['user']['statistics']['percent_traffic'];
$last_login_date = "-";
if (!empty($user_data['user']['info']['last_login']) ) {
    $last_login_date = date('d/m/Y h:s', $user_data['user']['info']['last_login']);
}
?>
<?
if($nested == true) {
$buttons = $system_items['dashboard']['buttons'];
$button_lists = array(
    array(
        'key' => __('Web Hosting Settings'),
        'values' => array(
            md5('webhosting_general_settings'),
            md5('webhosting_aspnet_settings'),
//            md5('webhosting_php_settings'),
            md5('subdomains'),
            md5('domain_databases'),
            md5('domain_dns'),
            md5('domain_alias'),
            md5('domain_certificate'),
            md5('site_preview'),
        )
    ),
    array(
        'key' => __('File & Backups Management'),
        'values' => array(
            md5('virtual-dir-settings'),
            md5('protect_dir'),
            md5('file_manager'),
//            md5('domain_backup'),
        )
    ),
    array(
        'key' => __('Mail Management'),
        'values' => array(
            md5('domain_mail_settings'),
            md5('mail_accounts'),
            md5('webmail'),
        )
    ),
    array(
        'key' => __('Advanced Settings'),
        'values' => array(
            md5('domain_tasks'),
            md5('domain_resource'),
            md5('domain_statistics'),
        )
    )
);
?>

<div class="row" id="domain-dashboard-panel" data-width="60%">
    <div class="col-lg-4 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Resource Usages') . '(' . $user_data['user']['domain_info']['domain_name'] .')';?></div>
            <div class="panel-body">
                <div class="row text-center">
                    <div class="col-md-6 col-xs-6">
                        <canvas id="disk_usage" width="125" height="125"></canvas>
                        <h5 ><? echo __('Disk Usage'); ?></h5>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <canvas id="traffic_usage" width="125" height="125"></canvas>
                        <h5 ><? echo __('Traffic Usage'); ?></h5>
                    </div>
                </div>

            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Overview');?></div>
            <div class="panel-body">
                <ul class="list-group clear-list m-t">
                    <li class="list-item">
                        <span class="pull-right"><? echo $last_login_date;?></span>
                        <span class="label label-info"><i class="fa fa-info"></i></span> <? echo __('Last Login Time');?>
                    </li>
                    <li class="list-item">
                        <span class="pull-right">
                            <? echo $user_data['user']['info']['last_login_ip'];?>
                        </span>
                        <span class="label label-info"><i class="fa fa-info"></i></span> <? echo __('Last Login IP');?>
                    </li>
                    <li class="list-item">
                        <span class="pull-right">
                            <? echo $user_data['user']['info']['email'];?>
                        </span>
                        <span class="label label-info"><i class="fa fa-info"></i></span> <? echo __('Email Address');?>
                    </li>
                    <li class="list-item">
                        <span class="pull-right">
                            <? echo $this->env->convertDate($user_data['user']['info']['create_date'], __('Unknown'));?>
                        </span>
                        <span class="label label-info"><i class="fa fa-info"></i></span> <? echo __('Created Date');?>
                    </li>
                    <li class="list-item">
                        <span class="pull-right">
                            <? echo $this->env->convertDate($user_data['user']['info']['expire_date'], __('Unlimited'));?>
                        </span>
                        <span class="label label-info"><i class="fa fa-info"></i></span> <? echo __('Expire Date');?>
                    </li>
                </ul>
            </div>
        </div>
        <? if(isset($domain_id)) { ?>
        <div class="panel panel-default">
            <div class="panel-heading"><? echo __('Services');?></div>
            <div class="panel-body">
                <div class="row">
                    <? $dns = ""; $mail = ""; ?>
                    <? if($user_data['user']['domain_info']['dns_service'] == "1") $dns = "checked"; ?>
                    <? if($user_data['user']['domain_info']['mail_service'] == "1") $mail = "checked"; ?>
                    <div class="col-md-4"><? echo __('Status');?></div>
                    <div class="col-md-4"><? echo __('DNS Service');?></div>
                    <div class="col-md-4"><? echo __('Mail Service');?></div>
                    <div class="col-md-4">
                        <? if(isset($domain_id)) { ?>
                            <? if($user_data['user']['info']['status'] == "1") { ?>
                                <input id="client_status" type="checkbox" checked class="make-switch" data-size="mini" data-url="<? echo $this->uri->url_encrypt("domains/suspend/". $user_data['user']['domain_info']['domain_id'] ."/suspend")?>" data-on-text="<? echo __('On'); ?>" data-off-text="<? echo __('Off'); ?>">
                            <? } else { ?>
                                <input id="client_status" type="checkbox" class="make-switch" data-size="mini" data-url="<? echo $this->uri->url_encrypt("domains/suspend/". $user_data['user']['domain_info']['domain_id'] ."/unsuspend")?>" data-on-text="<? echo __('On'); ?>" data-off-text="<? echo __('Off'); ?>">
                            <? } ?>


                        <? } else { ?>
                            <? if($user_data['user']['info']['status'] == "1") {?>
                                <strong><span class="label label-success" data-toggle="tooltip" data-original-title="<? echo $user_data['user']['info']['status_msg'];?>"> <i class="fa fa-info-circle"></i> <? echo __('Active')?> </span></strong>
                            <? } else { ?>
                                <strong><span class="label label-danger" data-toggle="tooltip" data-original-title="<? echo $user_data['user']['info']['status_msg'];?>"> <i class="fa fa-info-circle"></i> <? echo __('Passive')?> </span> </strong>
                            <? } ?>
                        <? } ?>
                    </div>
                    <div class="col-md-4">
                        <input name="enable_dns" type="checkbox" data-on-color="primary" data-off-color="danger"  <? echo $dns;?> class="make-switch enable_service" data-size="mini" data-url="<? echo $this->uri->url_encrypt("domains/service/". $user_data['user']['domain_info']['domain_id'])?>" data-on-text="<? echo __('On'); ?>" data-off-text="<? echo __('Off'); ?>">
                    </div>
                    <div class="col-md-4">
                        <input name="enable_mail" type="checkbox" data-on-color="primary" data-off-color="danger" <? echo $mail;?> class="make-switch enable_service" data-size="mini" data-url="<? echo $this->uri->url_encrypt("domains/service/". $user_data['user']['domain_info']['domain_id'])?>" data-on-text="<? echo __('On'); ?>" data-off-text="<? echo __('Off'); ?>">
                    </div>
                </div>

                </div>
            </div>
        <? } ?>

    </div>
    <div class="col-lg-8 col-md-12 col-xs-12">
        <? foreach($button_lists as $button) { ?>
        <div class="panel panel-default">
            <div class="panel-heading"><? echo $button['key'];?></div>
            <div class="panel-body">
                <ul>
                    <? foreach($button['values'] as $value) { ?>
                    <li class="icon col-md-1 col-xs-4 gs-w">
                        <? echo $buttons[$value]['icon']; ?>
                        <a data-trigger="focus" data-placement="bottom" data-toggle="tooltip"
                           data-id="<? echo $buttons[$value]['id']; ?>"
                           data-child="false"
                           data-target="<? echo $buttons[$value]['target']; ?>"
                           title="<? echo $buttons[$value]['name']; ?>: <? echo $buttons[$value]['description']; ?>"
                           data-url="<? echo $buttons[$value]['url']; ?>">
                            <? echo $buttons[$value]['name']; ?>
                        </a>
                    </li>
                    <? } ?>
                </ul>
            </div>
        </div>
        <? } ?>
    </div>
</div>
<script>
    var traffic_usage = [
        {
            value: + '<? echo $percent_disk;?>',
            color: "#dedede",
            highlight: "#222D33",
            label: lvar['used']
        },
        {
            value: 100 - + '<? echo $percent_traffic;?>',
            color: "#2d99dc",
            highlight: "#1ab394",
            label: lvar['free']
        }
    ];
    var disk_usage = [
        {
            value: +'<? echo $percent_disk;?>',
            color: "#dedede",
            highlight: "#222D33",
            label: lvar['used']
        },
        {
            value: 100 - +'<? echo $percent_traffic;?>',
            color: "#2d99dc",
            highlight: "#222D33",
            label: lvar['free']
        }
    ];
    var doughnutOptions = {
        segmentShowStroke: true,
        segmentStrokeColor: "#fff",
        segmentStrokeWidth: 2,
        percentageInnerCutout: 50,
        animationSteps: 100,
        animationEasing: "easeOutBounce",
        animateRotate: true,
        animateScale: false
    };

    var traffic = document.getElementById("traffic_usage").getContext("2d");
    var disk = document.getElementById("disk_usage").getContext("2d");
    new Chart(traffic).Doughnut(traffic_usage, doughnutOptions);
    new Chart(disk).Doughnut(disk_usage, doughnutOptions);

    core.inputInits();
    core.setWidth({
        target: 'domain-dashboard-panel'
    });


    var proc = false;
    $('#client_status').on('switchChange.bootstrapSwitch', function(event, state) {

        if ( proc == true ) {
            return;
        } else {
            proc = true;
        }

        var url = $(this).attr('data-url');

        bootbox.dialog({
            closeButton: false,
            message: '<div class="row">' +
            '<div class="col-md-12">' +
            '<div class="form-group">' +
            '<label class="col-md-4 control-label" for="description">'+ lvar['description'] +'</label>' +
            '<div class="col-md-8">' +
            '<input id="description" name="description" type="text" placeholder="'+ lvar['description'] +'" class="form-control">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>',
            title: lvar['are_you_sure'],
            "backdrop" : "static",
            "keyboard" : false,
            "show"     : true,
            buttons: {
                cancel: {
                    label: lvar['cancel'],
                    className: "btn btn-default",
                    callback: function () {
                        if($('#client_status').bootstrapSwitch('state') == true) {
                            $('#client_status').bootstrapSwitch('state', false);
                        }
                        else {
                            $('#client_status').bootstrapSwitch('state', true);
                        }
                        proc = false;
                    }
                },
                success: {
                    label: lvar['submit'],
                    className: "btn btn-primary btn-sm",
                    callback: function () {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            beforeSend:function() {$.blockUI(); },
                            complete: function() { $.unblockUI(); },
                            data: 'message='+ $('#description').val(),
                            success: function (dataRules) {
                                var obj = $.parseJSON(dataRules);
                                if (obj['message'] == 'success') {
                                    toastr["success"](obj['content'],obj['title']);
                                    setTimeout("location.reload(true);",500);
                                }
                                else {
                                    toastr["error"](obj['content'],obj['title']);
                                }
                            }
                        });
                    }
                }
            }
        });
    });

    var service_proc = false;
    $('.enable_service').on('switchChange.bootstrapSwitch', function(event, state) {
        if ( service_proc == true ) {
            return;
        } else {
            service_proc = true;
        }
        var url = $(this).attr('data-url');
        var checkbox = $(this).attr('name');
        var data = {};
        var status = "off";
        if($(this).is(":checked"))
            status = "on";
        data[checkbox] = status;
        bootbox.confirm(lvar['are_you_sure'], function (result) {
            if (result == true) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function (dataRules) {
                        var obj;
                        try {
                            obj = $.parseJSON(dataRules);
                        }
                        catch(e) {
                            obj = {message:'error',content:illegal_process};
                        }

                        if(obj['message'] == "505") {
                            toastr["error"]("System Error");
                            //setTimeout(function(){window.location.assign('/error.php')}, 1000);
                            //@TODO Reload yazılacak
                        }
                        if(obj['message'] == "logout") {
                            toastr["error"](session_timeout);
                            //setTimeout(function(){window.location.assign('/')}, 1000);
                            //@TODO Reload yazılacak
                        }

                        if (obj['message'] == 'success') {
                            toastr["success"](obj['content'],obj['title']);
                            //setTimeout("location.reload(true);",500);
                            //@TODO Reload yazılacak
                        }
                        else {
                            toastr["error"](obj['content'],obj['title']);
                            if($('input[name="'+ checkbox +'"]').bootstrapSwitch('state') == true) {
                                $('input[name="'+ checkbox +'"]').bootstrapSwitch('state', false);
                            }
                            else {
                                $('input[name="'+ checkbox +'"]').bootstrapSwitch('state', true);
                            }
                            service_proc = false;
                            return;
                        }

                    }
                });
            }
            else {
                if($('input[name="'+ checkbox +'"]').bootstrapSwitch('state') == true) {
                    $('input[name="'+ checkbox +'"]').bootstrapSwitch('state', false);
                }
                else {
                    $('input[name="'+ checkbox +'"]').bootstrapSwitch('state', true);
                }
                service_proc = false;
            }
            return;
        });
    });
</script>
<? } else { ?>
    <ul class="gridster connectedLists" id="desktop">

    </ul>
    <div class="system-monitoring widget-panel widget-panel-default hidden-xs">
        <div class="widget-panel-heading"> System Monitoring </div>
        <ul class="list-group">
            <li class="list-group-item no-bg">
                <div class="media">
                    <h5><? echo __('Disk Usage');?></h5>
                    <div class="media-left">
                        <p class="data-attributes media-object">
                            <span class="disk_usage" data-peity='{ "fill": ["#2D99DC", "#222D33"],    "innerRadius": 20, "radius": 39 }'><? echo $percent_disk;?>/100</span>
                        </p>
                        <h5><? echo __('Traffic Usage');?></h5>
                        <p class="data-attributes media-object">
                            <span class="traffic_usage" data-peity='{ "fill": ["#2D99DC", "#222D33"],    "innerRadius": 20, "radius": 39 }'><? echo $percent_traffic;?>/100</span>
                        </p>
                    </div>
                    <div class="media-body media-top">
                        <table class="table">
                            <tr>
                                <td><? echo __('Status');?>:</td>
                                <td>
                                    <? if($user_data['user']['info']['status'] == "1") {?>
                                        <strong><span class="label label-success" data-toggle="tooltip" data-original-title="<? echo $user_data['user']['info']['status_msg'];?>"> <i class="fa fa-info-circle"></i> <? echo __('Active')?> </span></strong>
                                    <? } else { ?>
                                        <strong><span class="label label-danger" data-toggle="tooltip" data-original-title="<? echo $user_data['user']['info']['status_msg'];?>"> <i class="fa fa-info-circle"></i> <? echo __('Passive')?> </span> </strong>
                                    <? } ?>
                                </td>
                            </tr>
                            <tr>
                                <td><? echo __('Last Login Time');?>:</td>
                                <td><? echo $last_login_date;?></td>
                            </tr>
                            <tr>
                                <td><? echo __('Last Login IP');?>:</td>
                                <td><? echo $user_data['user']['info']['last_login_ip'];?></td>
                            </tr>
                            <tr>
                                <td><? echo __('Email Address');?>:</td>
                                <td><? echo $user_data['user']['info']['email'];?></td>
                            </tr>
                            <tr>
                                <td><? echo __('Created Date');?>:</td>
                                <td><? echo $this->env->convertDate($user_data['user']['info']['create_date'], __('Unknown'));?></td>
                            </tr>
                            <tr>
                                <td><? echo __('Expire Date');?>:</td>
                                <td><? echo $this->env->convertDate($user_data['user']['info']['expire_date'], __('Unlimited'));?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li class="list-group-item no-bg">
                <h5><? echo __('Build')?></h5>
                <p><? echo 'Fpanel ' . $system_version;?> <i class="fa fa-fw fa-info-circle"></i></p>
            </li>
        </ul>
    </div>
    <style>
        .icon-web-hostings-settings-2 .path4::before {
            color: rgb(252, 98, 32);
        }
        .icon-web-hosting-settings .path2::before, .icon-web-hosting-settings .path3::before, .icon-web-hosting-settings .path4::before  {
            color: #fff;
        }
        .icon-mail-server .path1::before {
            color: #fff;
        }
        .icon-mail-server .path2::before, .icon-mail-server .path3::before, .icon-mail-server .path4::before {
            color: rgb(252, 98, 32);
        }
        .icon-mail-accounts .path2::before, .icon-mail-accounts .path3::before {
            color: rgb(252, 98, 32);
        }
    </style>

<? } ?>

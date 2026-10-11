<div class="row" id="domain-statistics-panel" data-width="70%">
    <div class="col-md-8">
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Monthly Report');?></div>
                    <div class="panel-body">
                        <div class="row" style="overflow-x:auto !important">
                            <table style="margin-left:10px;font-size:smaller;color:#545454">
                                <tbody>
                                <tr>
                                    <td class="legendColorBox">
                                        <div style="border:1px solid #000000;padding:1px">
                                            <div style="width:4px;height:0;border:5px solid #0078d7;overflow:hidden"></div>
                                        </div>
                                    </td>
                                    <td class="legendLabel"><? echo __('Requests');?></td>
                                </tr>
                                <tr>
                                    <td class="legendColorBox">
                                        <div style="border:1px solid #000000;padding:1px">
                                            <div style="width:4px;height:0;border:5px solid #dcdcdc;overflow:hidden"></div>
                                        </div>
                                    </td>
                                    <td class="legendLabel"><? echo __('Failed Requests');?></td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <canvas id="monthChart" height="140" width="450"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Last 7 Days Report');?></div>
                    <div class="panel-body">
                        <div class="row" style="overflow-x:auto !important">
                            <table style="margin-left:10px;font-size:smaller;color:#545454">
                                <tbody>
                                <tr>
                                    <td class="legendColorBox">
                                        <div style="border:1px solid #000000;padding:1px">
                                            <div style="width:4px;height:0;border:5px solid #0078d7;overflow:hidden"></div>
                                        </div>
                                    </td>
                                    <td class="legendLabel"><? echo __('Requests');?></td>
                                </tr>
                                <tr>
                                    <td class="legendColorBox">
                                        <div style="border:1px solid #000000;padding:1px">
                                            <div style="width:4px;height:0;border:5px solid #dcdcdc;overflow:hidden"></div>
                                        </div>
                                    </td>
                                    <td class="legendLabel"><? echo __('Failed Requests');?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <canvas id="dayChart" height="140" width="450"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Host Report');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flot-chart">
                                    <div class="flot-chart-pie-content" id="host-report"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend_host legend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Host Redirection Report');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flot-chart">
                                    <div class="flot-chart-pie-content" id="host-redirection-report"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend_host_redirection legend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Host Failure Report');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flot-chart">
                                    <div class="flot-chart-pie-content" id="host-failure-report"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend_host_fail legend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Redirected Referrer Report');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flot-chart">
                                    <div class="flot-chart-pie-content" id="redirected-referrer"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend_red_ref legend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Failed Referrer Report');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flot-chart">
                                    <div class="flot-chart-pie-content" id="failed-referrer"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend_failed_ref legend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Referring Site Report');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flot-chart">
                                    <div class="flot-chart-pie-content" id="referring-site"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend_ref_site legend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('General Overview');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-4">
                                <small class="stats-label"><? echo __('Success Requests');?></small>
                                <h4><? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[0]['col'][1]);?> (<? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[0]['col'][2]);?>)</h4>
                            </div>

                            <div class="col-xs-4">
                                <small class="stats-label"><? echo __('Average Requests');?></small>
                                <h4><? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[1]['col'][1]);?> (<? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[1]['col'][2]);?>)</h4>
                            </div>
                            <div class="col-xs-4">
                                <small class="stats-label"><? echo __('Requests for Page');?></small>
                                <h4><? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[2]['col'][1]);?> (<? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[2]['col'][2]);?>)</h4>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-4">
                                <small class="stats-label"><? echo __('Requests for pages per day');?></small>
                                <h4><? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[3]['col'][1]);?> (<? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[3]['col'][2]);?>)</h4>
                            </div>

                            <div class="col-xs-4">
                                <small class="stats-label"><? echo __('Failed Requests');?></small>
                                <h4><? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[4]['col'][1]);?> (<? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[4]['col'][2]);?>)</h4>
                            </div>
                            <div class="col-xs-4">
                                <small class="stats-label"><? echo __('Redirected Requests');?></small>
                                <h4><? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[5]['col'][1]);?> (<? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[5]['col'][2]);?>)</h4>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-4">
                                <small class="stats-label"><? echo __('Distinct files requested');?></small>
                                <h4><? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[6]['col'][1]);?> (<? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[6]['col'][2]);?>)</h4>
                            </div>

                            <div class="col-xs-4">
                                <small class="stats-label"><? echo __('Distinct hosts served');?></small>
                                <h4><? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[7]['col'][1]);?> (<? echo preg_replace('/^([^.]*).*$/', '$1', $general_report[7]['col'][2]);?>)</h4>
                            </div>
                            <div class="col-xs-4">
                                <small class="stats-label"><? echo __('Avg. Data transfer per day');?></small>
                                <h4><? echo $this->env->convert($general_report[10]['col'][1]);?> (<? echo $this->env->convert($general_report[10]['col'][2]);?>)</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Referrer Report');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flot-chart">
                                    <div class="flot-chart-pie-content" id="referrers"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend_referrer legend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Browser Report');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flot-chart">
                                    <div class="flot-chart-pie-content" id="browsers"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend_browsers legend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><? echo __('Status Code Report');?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="flot-chart">
                                    <div class="flot-chart-pie-content" id="status_codes"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="legend_status_codes legend"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?

$month_data_req = array(
    0 => '0',
    1 => '0',
    2 => '0',
    3 => '0',
    4 => '0',
    5 => '0',
    6 => '0',
    7 => '0',
    8 => '0',
    9 => '0',
    10 => '0',
    11 => '0'
);
$month_data_pages = array(
    0 => '0',
    1 => '0',
    2 => '0',
    3 => '0',
    4 => '0',
    5 => '0',
    6 => '0',
    7 => '0',
    8 => '0',
    9 => '0',
    10 => '0',
    11 => '0'
);
$day_data_req = array(
    0 => '0',
    1 => '0',
    2 => '0',
    3 => '0',
    4 => '0',
    5 => '0',
    6 => '0',
);
$day_data_pages = array(
    0 => '0',
    1 => '0',
    2 => '0',
    3 => '0',
    4 => '0',
    5 => '0',
    6 => '0',
);

if(!empty($monthly_report['row'])) {
    if(count($monthly_report['row']) > 0) {
        for ($i=0; $i<count($monthly_report['row']); $i++) {
            $month = date('m', strtotime($monthly_report['row'][$i]['col'][6]['timespan']['@attributes']['from']));
            $month = abs(ltrim($month,"0") -1);
            $month_data_req[$month]= $monthly_report['row'][$i]['col'][0];
            $month_data_pages[$month]= $monthly_report['row'][$i]['col'][2];
        }
    }
    else {
        $month = date('m', strtotime($monthly_report['row']['col'][6]['timespan']['@attributes']['from']));
        $month = abs(ltrim($month,"0") -1);
        $month_data_req[$month] = $monthly_report['row']['col'][0];
        $month_data_pages[$month] = $monthly_report['row']['col'][2];
    }
}

if(!empty($daily_report)) {
    if (count($daily_report) > 0) {
        for ($i = 0; $i < count($daily_report); $i++) {
            $day_data_req[$i] = $daily_report[$i]['col'][0];
            $day_data_pages[$i] = $daily_report[$i]['col'][2];
        }
    } else {
        $day_data_req[$i] = $daily_report['col'][0];
        $day_data_pages[$i] = $daily_report['col'][2];
    }
}

?>
<script>

    var monthData = {
        labels: ["<? echo __('Jan');?>", "<? echo __('Feb');?>", "<? echo __('Mar');?>", "<? echo __('Apr');?>", "<? echo __('May');?>", "<? echo __('Jun');?>", "<? echo __('Jul');?>", "<? echo __('Aug');?>", "<? echo __('Sep');?>", "<? echo __('Oct');?>", "<? echo __('Nov');?>", "<? echo __('Dec');?>"],
        datasets: [
            {
                label: "<? echo __('Requests')?>",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.7)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(26,179,148,1)",
                data: <? echo json_encode($month_data_req);?>
            },
            {
                label: "<? echo __('Pages')?>",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: <? echo json_encode($month_data_pages);?>
            }
        ]
    };

    var dayData = {
        labels: ["7","6","5","4","3","2","1"],
        datasets: [
            {
                label: "<? echo __('Requests')?>",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.7)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(26,179,148,1)",
                data: <? echo json_encode($day_data_req);?>
            },
            {
                label: "<? echo __('Pages')?>",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: <? echo json_encode($day_data_pages);?>
            }
        ]
    };
    host_data = null;
    host_redirection_data = null;
    host_failure_data = null;
    redirected_referrer_data = null;
    failed_referrer_data = null;
    refererring_site_data = null;
    referrers_data = null;
    browser_data = null;
    status_codes_data = null;
    <? if(!empty($host_report)) { ?>
    var host_data = [
        <? $i = 0; foreach($host_report as $host) { ?>
        <?
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        ?>
        <? if($i != 0) echo ',';?>
        {
            label: "<? echo $host['ip'];?>",
            data: <? echo $host['request'];?>,
            color: "<? echo $color;?>"
        }
        <? $i++; } ?>
    ];
    <? } ?>
    <? if(!empty($host_redirection_report)) { ?>
    var host_redirection_data = [
        <? $i = 0; foreach($host_redirection_report as $host) { ?>
        <?
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        ?>
        <? if($i != 0) echo ',';?>
        {
            label: "<? echo $host['host'];?>",
            data: <? echo $host['request'];?>,
            color: "<? echo $color;?>"
        }
        <? $i++; } ?>
    ];
    <? } ?>
    <? if(!empty($host_failure_report)) { ?>
    var host_failure_data = [
        <? $i = 0; foreach($host_failure_report as $host) { ?>
        <?
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        ?>
        <? if($i != 0) echo ',';?>
        {
            label: "<? echo $host['host'];?>",
            data: <? echo $host['request'];?>,
            color: "<? echo $color;?>"
        }
        <? $i++; } ?>
    ];
    <? } ?>
    <? if(!empty($redirected_referrer_report)) { ?>
    var redirected_referrer_data = [
        <? $i = 0; foreach($redirected_referrer_report as $page) { ?>
        <?
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        ?>
        <? if($i != 0) echo ',';?>
        {
            label: "<? echo $page['page'];?>",
            data: <? echo $page['request'];?>,
            color: "<? echo $color;?>"
        }
        <? $i++; } ?>
    ];
    <? } ?>
    <? if(!empty($failed_referrer_report)) { ?>
    var failed_referrer_data = [
        <? $i = 0; foreach($failed_referrer_report as $page) { ?>
        <?
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        ?>
        <? if($i != 0) echo ',';?>
        {
            label: "<? echo $page['page'];?>",
            data: <? echo $page['request'];?>,
            color: "<? echo $color;?>"
        }
        <? $i++; } ?>
    ];
    <? } ?>
    <? if(!empty($referring_site_report)) { ?>
    var refererring_site_data = [
        <? $i = 0; foreach($referring_site_report as $page) { ?>
        <?
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        ?>
        <? if($i != 0) echo ',';?>
        {
            label: "<? echo $page['page'];?>",
            data: <? echo $page['request'];?>,
            color: "<? echo $color;?>"
        }
        <? $i++; } ?>
    ];
    <? } ?>
    <? if(!empty($referrer_report)) { ?>
    var referrers_data = [
        <? $i = 0; foreach($referrer_report as $page) { ?>
        <?
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        ?>
        <? if($i != 0) echo ',';?>
        {
            label: "<? echo $page['page'];?>",
            data: <? echo $page['request'];?>,
            color: "<? echo $color;?>"
        }
        <? $i++; } ?>
    ];
    <? } ?>
    <? if(!empty($browser_report)) { ?>
    var browser_data = [
        <? $i = 0; foreach($browser_report as $browser => $request) { ?>
        <?
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        ?>
        <? if($i != 0) echo ',';?>
        {
            label: "<? echo $browser;?>",
            data: <? echo $request;?>,
            color: "<? echo $color;?>"
        }
        <? $i++; } ?>
    ];
    <? } ?>
    <? if(!empty($browser_report)) { ?>
    var status_codes_data = [
        <? $i = 0; foreach($status_report as $code) { ?>
        <?
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        ?>
        <? if($i != 0) echo ',';?>
        {
            label: "<? echo $code['code'];?>",
            data: <? echo $code['request'];?>,
            color: "<? echo $color;?>"
        }
        <? $i++; } ?>
    ];
    <? } ?>
    var options = {
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        bezierCurve: true,
        bezierCurveTension: 0.4,
        pointDot: true,
        pointDotRadius: 4,
        pointDotStrokeWidth: 1,
        pointHitDetectionRadius: 20,
        datasetStroke: true,
        datasetStrokeWidth: 2,
        datasetFill: true,
        responsive: true
    };
    var ctx = document.getElementById("monthChart").getContext("2d");
    var newChart = new Chart(ctx).Line(monthData, options);
    var ctx2 = document.getElementById("dayChart").getContext("2d");
    var newChart2 = new Chart(ctx2).Line(dayData, options);
    if(host_data != null) {
        var hostReport = $.plot($("#host-report"), host_data, {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            legend:{
                container:$(".legend_host"),
                labelFormatter: legendFormatter
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s",
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: true
            }
        });
    }
    if(host_redirection_data != null) {
        var hostRedirectionReport = $.plot($("#host-redirection-report"), host_redirection_data, {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            legend:{
                container:$(".legend_host_redirection"),
                labelFormatter: legendFormatter
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s",
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }
        });
    }
    if(host_failure_data != null) {
        var hostfailureReport = $.plot($("#host-failure-report"), host_failure_data, {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            legend:{
                container:$(".legend_host_fail"),
                labelFormatter: legendFormatter
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s",
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }
        });
    }
    if(redirected_referrer_data != null) {
        var redirectedReferrer = $.plot($("#redirected-referrer"), redirected_referrer_data, {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            legend:{
                container:$(".legend_red_ref"),
                labelFormatter: legendFormatter
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s",
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }
        });
    }
    if(failed_referrer_data != null) {
        var failedReferrer = $.plot($("#failed-referrer"), failed_referrer_data, {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            legend:{
                container:$(".legend_failed_ref"),
                labelFormatter: legendFormatter
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s",
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }
        });
    }
    if(refererring_site_data != null) {
        var refererringSite = $.plot($("#referring-site"), refererring_site_data, {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            legend:{
                container:$(".legend_ref_site"),
                labelFormatter: legendFormatter
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s",
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }
        });
    }
    if(referrers_data != null) {
        var referrers = $.plot($("#referrers"), referrers_data, {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            legend: {
                container: $(".legend_referrer"),
                labelFormatter: legendFormatter
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s",
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }
        });
    }
    if(browser_data != null) {
        var browsers = $.plot($("#browsers"), browser_data, {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            legend: {
                container: $(".legend_browsers"),
                labelFormatter: legendFormatter
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s",
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }
        });
    }
    if(status_codes_data != null) {
        var status_codes = $.plot($("#status_codes"), status_codes_data, {
            series: {
                pie: {
                    show: true
                }
            },
            grid: {
                hoverable: true
            },
            legend: {
                container: $(".legend_status_codes"),
                labelFormatter: legendFormatter
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s",
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false
            }

        });
    }
    function legendFormatter(label, series) {
        return '<div ' +
            'style="font-size:8pt;text-align:center;padding:2px;">' +
            label + ' (' + Math.round(series.data[0][1])+')</div>';
    };
    core.setWidth({
        target: 'domain-statistics-panel'
    });
</script>

<style>

    .legend {
        position: relative;
        width: 100%;
    }
    .legend table {
        background-color: #fff;
        padding: 0px;
        margin-bottom: 8px;
        border-radius: 3px 3px 3px 3px;
        border: 1px solid #E6E6E6;
        display: inline-block;
        margin: 0px;
    }
</style>
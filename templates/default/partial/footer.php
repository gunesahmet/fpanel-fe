<div id="countdown-app"></div>
<div class="footer fixed">
    <div class="row">
        <div class="col-md-8">
            &copy; 2021 - <?= date("Y"); ?> - <a href="http://www.fpanel.net" target="_blank">Fpanel</a> - <?= __("All Rights Reserved.");?></a>
            Current version: <?= F_VERSION;?>
        </div>
        <div class="col-md-4">
            <div class="float-right">
              <?= __("Load Time: %time% ms", ["time" => "<span class='load-time'>".number_format($time_load*1000, 0)]) . "</span>" ;?>
                <?= __("Memory Usage: %memory%", ["memory" => '<span class="memory-usage">'. $this->env->convert(memory_get_usage(true))]) . "</span>";?>
            </div>
        </div>
    </div>
</div>

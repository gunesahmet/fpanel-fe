<?php
/*
 * fpanel — a web-based hosting control panel.
 * Copyright (C) 2026  Ahmet Refik Gunes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */namespace FPanel\Log;

use Phalcon\Mvc\User\Component;

class Debugger extends Component
{
    public function debug($data, $pretty = true)
    {
        if(PRODUCTION_STATUS == "DEBUG" || PRODUCTION_STATUS == "PRODUCTION" ) {
            if($pretty == true) {
                echo '<link href="/templates/default/assets/css/bootstrap.css" rel="stylesheet" />';
                echo '<link href="/templates/default/assets/css/jquery.jsonbrowser.css" rel="stylesheet" />';
                echo '<style>.jsonbrowser { margin : 15px auto 20px auto;padding : 20px;color:#777;}</style>';
                echo '<script src="/templates/default/assets/js/jquery.min.js"></script>';
                echo '<script src="/templates/default/assets/js/bootstrap.min.js"></script>';
                echo '<script src="/templates/default/assets/js/jquery.jsonbrowser.js"></script>';
                echo '<body>';
                echo '<nav class="navbar navbar-inverse navbar-fixed-top">';

                echo '<div class="row" style="padding: 10px">';

                echo '<div class="col-md-2 float-left">';
                echo '<a class="btn btn-sm btn-primary" style="margin-right:5px" id="collapse-all">Collapse All</a>';
                echo '<a class="btn btn-sm btn-default" id="expand-all">Expand All</a>';
                echo '</div>';
                echo '<div class="col-md-10 float-right"><input type="text" id="search" class="form-control form-control-sm input-sm" placeholder="Search ..."></div>';

                echo '</div>';
                echo '</nav>';
                echo '<div class="container" style="margin-top: 70px">';
                echo '<div class="col-md-12"><div id="debug" class="jsonbrowser well"></div></div>';
                echo '</div>';
                echo '</body>';

                echo '<script>
                $(\'#debug\').jsonbrowser(' . json_encode($data) . ');

                $(\'#collapse-all\').on(\'click\', function(e) {
                    e.preventDefault();
                    $.jsonbrowser.collapseAll(\'#debug\');
                });

                $(\'#expand-all\').on(\'click\', function(e) {
                    e.preventDefault();
                    $.jsonbrowser.expandAll(\'#debug\');
                });

                $(\'#search\').on(\'keyup\', function(e) {
                    e.preventDefault();
                    $.jsonbrowser.search(\'#debug\', $(this).val());
                });
                $(\'#search\').focus().trigger(\'keyUp\');
                </script>
                <style>
                    * {
                    border-radius: 0 !important}
                </style>';
            }
            else {
                echo "<pre>";
//                if(is_array($data))
//                    echo print_r(json_encode($data, JSON_PRETTY_PRINT), true);
//                else
                    echo print_r($data, true);
            }
            exit(0);
        }
    }

    public function firebug($data, $log_type = "info")
    {
        if(PRODUCTION_STATUS != "DEBUG") {
            if(is_array($data)) {
                $data = json_encode($data);
            }
            switch($log_type) {
                case "log":
                    $this->fb->log($data, FPHP::LOG);
                    break;
                case "warning":
                    $this->fb->warn($data, FPHP::WARN);
                    break;
                case "error":
                    $this->fb->error($data, FPHP::ERROR);
                    break;
                case "debug":
                case "info":
                    $this->fb->info($data, FPHP::INFO);
            }
        }
    }
}
<? if($controller_name != "install") {
    if(($system_items['user']['info']['user_type'] == ROOT_USER) || ($system_items['user']['info']['user_type'] == ADMIN_USER) || ($system_items['user']['info']['user_type'] == SUPPORT_USER)) {
        if($panel_system == CLOUD && $system_items["system_data"]["server_id"] != "1") {
            if($domain_limit_exceeded == true) {
                echo '<div class="alert alert-warning" style="margin-top:5px"> <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i> <strong>'. __('Warning').'</strong> </a>'. __('Domain limit exceeded!') .'</div>';
            }
            if($client_limit_exceeded == true) {
                echo '<div class="alert alert-warning" style="margin-top:5px"> <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i> <strong>'. __('Warning').'</strong> </a>'. __('Client limit exceeded!') .'</div>';
            }
        }
        else if ($panel_system == STANDALONE){
            if($domain_limit_exceeded == true) {
                echo '<div class="alert alert-warning" style="margin-top:5px"> <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i> <strong>'. __('Warning').'</strong> </a>'. __('Domain limit exceeded!') .'</div>';
            }
            if($client_limit_exceeded == true) {
                echo '<div class="alert alert-warning" style="margin-top:5px"> <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i> <strong>'. __('Warning').'</strong> </a>'. __('Client limit exceeded!') .'</div>';
            }
        }


        if(demo == true) {
            echo '<div class="alert alert-info" style="margin-top:5px"> <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-info-circle"></i> <strong>'. __('Information').'</strong> </a>'. __('Fpanel working as demo mode. Some features will not work!') .'</div>';
        }
    }

    if($support_status == true) {
        echo '<div class="alert alert-warning" style="margin-top:5px"> <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa fa-exclamation-circle"></i> <strong>'. __('Information').'</strong> </a>'. __('Support user logged in') .'</div>';
    }

    if((isset($database_server_status)) && ($database_server_status == false)) {
        echo '<div class="alert alert-danger" style="margin-top:5px"> <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa fa-exclamation-circle"></i> <strong>'. __('Warning').'</strong> </a>'. $database_server_message .'</div>';
    }

    if((isset($changed_data)) && ($changed_data != false)) {
        echo $changed_data;
    }
}

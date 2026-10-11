<?

if($server_type == "standalone") {
    echo $this->partial('system/standalone');
}
else {
    echo $this->partial('system/cloud');
}
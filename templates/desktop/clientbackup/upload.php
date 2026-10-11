<? if(isset($client_id)) $append_link = "/client/$client_id"; else $append_link = "";
$upload_url = $this->uri->url_encrypt("backupclient/upload$append_link");
?>
<form id="client-backup-upload" method="POST" enctype="multipart/form-data" action="<? echo $upload_url;?>">
    <div class="row fileupload-buttonbar">
        <div class="col-lg-7">
            <span class="btn btn-sm btn-primary fileinput-button"><i class="fa fa-plus"></i> <span><? echo __('Add files');?>... </span><input type="file" name="files[]" multiple=""></span>
            <button type="submit" class="btn btn-sm btn-primary start"><i class="fa fa-upload"></i> <span><? echo __('Start Upload');?> </span></button>
            <button type="reset" class="btn btn-sm btn-warning cancel"><i class="fa fa-ban"></i> <span><? echo __('Cancel Upload');?> </span></button>
            <span class="fileupload-process"></span>
        </div>
        <div class="col-lg-5 fileupload-progress fade">
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-success" style="width:0%;">
                </div>
            </div>
            <div class="progress-extended">
                &nbsp;
            </div>
        </div>
    </div>
    <table role="presentation" class="table table-striped clearfix">
        <tbody class="files">
        </tbody>
    </table>
</form>

<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger label label-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-sm btn-primary start" disabled>
                    <i class="fa fa-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-sm btn-danger cancel">
                    <i class="fa fa-ban"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
                <td>
                    <p class="name">
                        <span>{%=file.name%}</span>

                    </p>
                    {% if (file.error) { %}
                        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                    {% } %}
                </td>
                <td>
                    <span class="size">{%=o.formatFileSize(file.size)%}</span>
                </td>
                <td>
                    {% if (!file.deleteUrl) { %}
                        <button class="btn-danger cancel btn-sm">
                            <i class="fa fa-ban"></i>
                            <span>Cancel</span>
                        </button>

                    {% } %}
                </td>
            </tr>
        {% } %}
    </script>
<script>
    $("#client-backup-upload").fileupload({maxChunkSize:5E5, disableImageResize:!1, autoUpload:!1, maxRetries:100, retryTimeout:500, maxFileSize:2E9, acceptFileTypes:/(\.|\/)(backup)$/i, fail:function(a, b) {
        $.ajax({url:"<? echo $upload_url;?>", dataType:"json", data:{file:b.files[0].name}, type:"DELETE"});
    }});
    $("#client-backup-upload").addClass("fileupload-processing");
</script>
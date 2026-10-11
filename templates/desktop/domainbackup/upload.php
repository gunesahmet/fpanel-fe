<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">

                <form id="fileupload" method="POST" enctype="multipart/form-data">
                    <div class="row fileupload-buttonbar">
                        <div class="col-lg-7">
                            <span class="btn btn-primary fileinput-button"><i class="fa fa-plus"></i><span><? echo __('Add files');?>... </span><input type="file" name="files[]" multiple=""></span>
                            <button type="submit" class="btn btn-primary start"><i class="fa fa-upload"></i><span><? echo __('Start Upload');?> </span></button>
                            <button type="reset" class="btn btn-warning cancel"><i class="fa fa-ban-circle"></i><span><? echo __('Cancel Upload');?> </span></button>

								<span class="fileupload-process">
								</span>
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

            </div>
        </div>
    </div>
</div>

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
                <button class="btn btn-primary start" disabled>
                    <i class="fa fa-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-danger cancel">
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
                        <button class="btn yellow cancel btn-sm">
                            <i class="fa fa-ban"></i>
                            <span>Cancel</span>
                        </button>

                    {% } %}
                </td>
            </tr>
        {% } %}
    </script>
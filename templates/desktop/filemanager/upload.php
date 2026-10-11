<div class="modal inmodal fade" id="upload_files" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><? echo __('File Upload');?></h4>
                <small class="font-bold"><? echo __('You can upload files from here');?></small>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div style="overflow: auto; max-height: 500px">
                            <div id="upload_folder_lists">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <form id="fileupload" method="POST" enctype="multipart/form-data" action="<? echo $upload_url;?>">
                            <input type="hidden" name="upload_folder" id="upload_folder" value="">
                            <div class="row fileupload-buttonbar">
                                <div class="col-lg-12">
                                    <span class="btn btn-primary fileinput-button"><i class="fa fa-plus"></i><span><? echo __('Add files');?>... </span><input type="file" name="files[]" multiple=""></span>
                                    <button type="submit" class="btn btn-primary start"><i class="fa fa-upload"></i><span><? echo __('Start Upload');?> </span></button>
                                    <button type="reset" class="btn btn-warning cancel"><i class="fa fa-ban-circle"></i><span><? echo __('Cancel Upload');?> </span></button>
                                    <label><input type="checkbox" name="unzip" class="checkbox"> <? echo __('Unzip after upload');?></label>
								<span class="fileupload-process">
								</span>
                                </div>
                            </div>
                            <table role="presentation" class="table table-striped clearfix">
                                <tbody class="files">
                                </tbody>
                            </table>
                        </form>

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
            </div>

            <div class="modal-footer">
                <button id="close" type="button" class="btn btn-white" data-dismiss="modal"><? echo __('Close');?></button>
            </div>
        </div>
    </div>
</div>


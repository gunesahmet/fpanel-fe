<form id="create-vdir-form" method="post" class="form-horizontal" action="<? echo $post_url;?>">
    <div class="tabbable-line">
        <ul class="nav nav-tabs nav-justified">
            <li class="active"><a href="#vdir-index" data-toggle="tab"><i></i><span class="strong"><? echo __('Virtual Directory');?></span></a></li>
            <li><a href="#vdir-applications" data-toggle="tab"><i></i><span class="strong"><? echo __('Application Settings');?></span></a></li>
            <li><a href="#vdir-documents" data-toggle="tab"><i></i><span class="strong"><? echo __('Default Documents Settings');?></span></a></li>
            <li><a href="#vdir-directorysec" data-toggle="tab"><i></i><span class="strong"><? echo __('Directory Security');?></span></a></li>
            <li><a href="#vdir-aspsettings" data-toggle="tab"><i></i><span class="strong"><? echo __('ASP Settings');?></span></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="vdir-index">
                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Path');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('directoryPath',array('class' => 'form-control input-sm'));?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Access Policy');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('accessPolicy',array('class' => 'multiple-select'));?>
                    </div>
                </div>

                <hr />

                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Directory Browsing');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('directory_browse',array('class' => 'checkbox-blue'));?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Http Logging');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('logging',array('class' => 'checkbox-blue'));?>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="vdir-applications">
                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Create Application');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('createapplication',array('class' => 'checkbox-blue'));?>
                    </div>
                </div>
                <br />
                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Allow application execurion in MTA (Multi Threaded Apartment) Mode');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('execute_in_mta',array('class' => 'checkbox-blue'));?>

                    </div>
                </div>
            </div>
            <div class="tab-pane" id="vdir-documents">
                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Use Default Documents');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('use_default_document',array('class' => 'checkbox-blue'));?>

                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Add New Document');?>
                    </div>
                    <div class="col-md-7">
                        <? echo $form->render('default_document_add',array('class' => 'form-control input-sm'));?>
                    </div>
                    <div class="col-md-1">
                        <a class="btn btn-primary btn-xs" id="btn-add"><i class="fa fa-plus"></i></a>
                        <a class="btn btn-danger btn-xs" id="btn-remove"><i class="fa fa-times"></i></a>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Order Default Documents');?>
                    </div>
                    <div class="col-md-7">
                        <? echo $form->render('default_document_order',array('class' => 'multiple-select'));?>
                    </div>
                    <div class="col-md-1">
                        <a class="btn btn-default btn-xs" id="btn-up"><i class="fa fa-arrow-up"></i></a>
                        <a class="btn btn-default btn-xs" id="btn-down"><i class="fa fa-arrow-down"></i></a>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="vdir-directorysec">
                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Allow Anonymous Access');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('allow_anonymous',array('class' => 'checkbox-blue'));?>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="vdir-aspsettings">
                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Allow to use Parent Path');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('parent_path',array('class' => 'checkbox-blue'));?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Enable ASP Server Side Script Debugging');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('server_debugging',array('class' => 'checkbox-blue'));?>

                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Enable ASP Client Side Script Debugging');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('client_debugging',array('class' => 'checkbox-blue'));?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Max. Size of ASP Buffer (Byte)');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('buffer_limit',array('class' => 'form-control input-sm'));?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <? echo __('Max. Size of Entity Body of on ASP (Byte)');?>
                    </div>
                    <div class="col-md-8">
                        <? echo $form->render('entity_size',array('class' => 'form-control input-sm'));?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-sm btn-default" type="reset"><? echo __('Cancel'); ?></button>
                <button class="btn btn-sm btn-primary" type="button" onclick="core.submitForm(this)"><i class="fa fa-check-circle"></i> <? echo __('Save'); ?></button>
            </div>
        </div>
    </div>
</form>
<script>
    $('#default_document_order').selectMultiple();
    $('#accessPolicy').selectMultiple();
    $('input.checkbox-blue').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });

    $('#btn-up').unbind();
    $('#btn-up').on('click', function () {
        $('#default_document_order option:selected').each(function () {
            var newPos = $('#default_document_order option').index(this) - 1;
            if (newPos > -1) {
                $('#default_document_order option').eq(newPos).before("<option value='" + $(this).val() + "' selected='selected'>" + $(this).text() + "</option>");
                $(this).remove();
            }
        });
        $('#default_document_order').selectMultiple('refresh');

    });

    $('#btn-down').unbind();
    $('#btn-down').on('click', function () {
        var countOptions = $('#default_document_order option').size();
        $('#default_document_order option:selected').each(function () {
            var newPos = $('#default_document_order option').index(this) + 1;
            if (newPos < countOptions) {
                $('#default_document_order option').eq(newPos).after("<option value='" + $(this).val() + "' selected='selected'>" + $(this).text() + "</option>");
                $(this).remove();
            }
        });
        $('#default_document_order').selectMultiple('refresh');

    });

    $('#btn-add').unbind();
    $('#btn-add').on('click',function(){
        var defaultDocument = $('#default_document_add').val();
        $('#default_document_order').append("<option>"+ defaultDocument +"</option>");
        $('#default_document_order').selectMultiple('refresh');
        $('#default_document_add').val("");
    });

    $('#btn-remove').unbind();
    $('#btn-remove').on('click',function(){
        $('#default_document_order option:selected').each( function() {
            $(this).remove();
        });
        $('#default_document_order').selectMultiple('refresh');
    });
</script>
var backupUploader = function () {
    return {
        init: function () {
            $('.fpb-progress .progress-resume-link').unbind();
            $('.fpb-progress .progress-resume-link').on('click',function () {
                r.upload(); return(false);
            });
            $('.fpb-progress .progress-pause-link').unbind();
            $('.fpb-progress .progress-pause-link').on('click',function () {
                r.pause(); return(false);
            });
            $('.fpb-progress .progress-cancel-link').unbind();
            $('.fpb-progress .progress-cancel-link').on('click',function () {
                r.cancel(); return(false);
            });
        },
        upload: function (url,query) {
            var r = new Flow({
                target: url,
                method: 'POST',
                testMethod: 'POST',
                uploadMethod: 'POST',
                chunkSize: 1024*1024,
                testChunks: false,
                query : query
            });
            if (!r.support) {
                $('.fpb-error').show();
                return ;
            }
            $('.fpb-drop').show();
            r.assignDrop($('.fpb-drop')[0]);
            r.assignBrowse($('.fpb-browse')[0]);
            r.assignBrowse($('.fpb-browse-folder')[0], true);
            r.assignBrowse($('.fpb-browse-file')[0], false, false, {accept: '*'});
            // Handle file add event
            r.on('fileAdded', function(file){
                // Show progress bar
                $('.fpb-progress, .uploading-file-list').show();
                // Add the file to the list
                $('.uploading-backup-file-list').append(
                    '<li class="fpb-file fpb-file-'+file.uniqueIdentifier+'">' +
                    '<div class="col-md-8 margin-top-10">' +
                    lvar['uploading'] + ' <span class="fpb-file-name"></span> ' +
                    '<span class="fpb-file-size"></span> ' +
                    '<span class="fpb-file-progress"></span> ' +
                    '</div>' +
                    '<div class="col-md-4 margin-top-5">' +
                    '<a class="fpb-file-pause btn btn-sm btn-warning" style="display:none">' + lvar['pause_upload'] + '</a> ' +
                    '<a class="fpb-file-resume btn btn-sm btn-primary" style="display:none">' + lvar['resume_upload'] + '</a> ' +
                    '<a class="fpb-file-cancel btn btn-sm btn-danger">' + lvar['cancel'] + '</a>' +
                    '</div>'
                );
                var $self = $('.fpb-file-'+file.uniqueIdentifier);
                $self.find('.fpb-file-name').text(file.name);
                $self.find('.fpb-file-size').text(core.readablizeBytes(file.size));
                $self.find('.fpb-file-pause').on('click', function () {
                    file.pause();
                    if($(".uploading-backup-file-list li .fpb-file-pause:visible").length == 1) {
                        $(".progress-pause-link").hide();
                        $(".progress-resume-link").css("display", "inline");
                    }
                    $self.find('.fpb-file-pause').hide();
                    $self.find('.fpb-file-resume').show();
                });
                $self.find('.fpb-file-resume').on('click', function () {
                    file.resume();
                    $self.find('.fpb-file-pause').show();
                    $self.find('.fpb-file-resume').hide();
                });
                $self.find('.fpb-file-cancel').on('click', function () {
                    $(".progress-bar").css("width","0");
                    if($(".uploading-backup-file-list li").length == 1) {
                        $(".fpb-progress").hide();
                    }
                    file.cancel();
                    $self.remove();
                });
            });
            $('.fpb-progress .progress-start-link').unbind();
            $('.fpb-progress .progress-start-link').on('click',function () {
                $('.fpb-file-pause').show();
                r.upload();
            });
            r.on('complete', function(){
                // Hide pause/resume when the upload has completed
                $('.fpb-progress .progress-resume-link, .fpb-progress .progress-pause-link').hide();
            });
            r.on('fileSuccess', function(file,message){
                var $self = $('.fpb-file-'+file.uniqueIdentifier);
                if(message.length > 0) {
                    if(typeof message != "undefined") {
                        var error_message = '<b class="text-danger">(file could not be uploaded)</b>';
                        var obj = $.parseJSON(message);
                        if(!obj.status) {
                            error_message = "<b class=\"text-danger\">(" + obj.message + ")</b>";
                            $('.fpb-file-'+file.uniqueIdentifier+' .fpb-file-progress').html(error_message);
                        }
                    }
                }
                else {
                    // Reflect that the file upload has completed
                    $self.find('.fpb-file-progress').html('<strong class="navy">(completed)</strong>');
                }

                $self.find('.fpb-file-pause, .fpb-file-resume').remove();
                $self.find('.fpb-file-pause, .fpb-file-cancel').remove();
                // $self.find('.fpb-file-download').attr('href', '/download/' + file.uniqueIdentifier).show();
            });
            r.on('fileError', function(file, message){
                // Reflect that the file upload has resulted in error
                if(typeof message != "undefined") {
                    var error_message = '<b class="text-danger">(file could not be uploaded)</b>';
                    var obj = $.parseJSON(message);
                    if(!obj.status) {
                        error_message = "<b class=\"text-danger\">(" + obj.message + ")</b>";
                    }
                }

                $('.fpb-file-'+file.uniqueIdentifier+' .fpb-file-progress').html(error_message);
                $('.fpb-file-'+file.uniqueIdentifier+' .fpb-file-pause').hide();
                $('.fpb-file-'+file.uniqueIdentifier+' .fpb-file-resume').hide();
            });
            r.on('fileProgress', function(file){
                // Handle progress for both the file and the overall upload
                $('.fpb-file-'+file.uniqueIdentifier+' .fpb-file-progress')
                    .html(Math.floor(file.progress()*100) + '% '
                        + core.readablizeBytes(file.averageSpeed) + '/s '
                        + secondsToStr(file.timeRemaining()) + ' remaining') ;
                $('.fpb-progress .progress-bar').css({width:Math.floor(r.progress()*100) + '%'});
            });
            r.on('uploadStart', function(){
                // Show pause, hide resume
                $('.fpb-progress .progress-start-link').hide();
                $('.fpb-progress .progress-resume-link').hide();
                $('.fpb-progress .progress-pause-link').css("display","inline");
            });

            window.r = {
                pause: function () {
                    r.pause();
                    // Show resume, hide pause
                    $('.fpb-file-resume').show();
                    $('.fpb-file-pause').hide();
                    $('.fpb-progress .progress-resume-link').show();
                    $('.fpb-progress .progress-pause-link').hide();
                },
                cancel: function() {
                    $('.fpb-progress .progress-start-link').show();
                    $('.fpb-progress .progress-resume-link').hide();
                    r.cancel();
                    $('.fpb-file').remove();
                    $('.fpb-progress').hide();
                },
                upload: function() {
                    $('.fpb-file-pause').show();
                    $('.fpb-file-resume').hide();
                    r.resume();
                },
                Flow: r
            };
        },
        clear: function () {
            r.cancel();
        }
    }
}();
var fileUploader = function () {
    return {
        selected_folder: null,
        target_url: null,
        cancel_url: null,
        r: null,
        windowR: null,
        init: function () {
            window.r = null;
            fileUploader.windowR = null;
            fileUploader.r = new Flow({
                method: 'POST',
                testMethod: 'POST',
                uploadMethod: 'POST',
                chunkSize: 1024*1024,
                testChunks: false,
                allowDuplicateUploads: false,
                target: function(fileObj) {
                    return fileObj.target_url;
                },
                query: function (fileObj) {
                    return fileObj.query;
                }
            });
            if (!fileUploader.r.support) {
                $('.fp-error').show();
                return ;
            }
            $('.fp-drop').show();
            fileUploader.r.assignDrop($('.fp-drop')[0]);
            fileUploader.r.assignBrowse($('.fp-browse')[0]);
            fileUploader.r.assignBrowse($('.fp-browse-folder')[0], true);
            fileUploader.r.assignBrowse($('.fp-browse-file')[0], false, false, {accept: '*'});
            window.r = {
                pause: function () {
                    fileUploader.r.pause();
                    // Show resume, hide pause
                    $('.fp-file-resume').show();
                    $('.fp-file-pause').hide();
                    $('.fp-progress .progress-resume-link').show();
                    $('.fp-progress .progress-pause-link').hide();
                },
                cancel: function(e) {
                    // $('.fp-progress .progress-start-link').show();
                    // $('.fp-progress .progress-resume-link').hide();
                    $('.fp-progress .progress-start-link').show();
                    $('.fp-progress .progress-resume-link').hide();
                    // $(".fp-progress").hide();
                    // $(".progress-pause-link").hide();
                    // $(".fp-progress").hide();
                    $(".progress-pause-link").hide();
                    fileUploader.r.cancel();
                    // $(".progress-bar").css("width","0");
                    // $('.fp-file').remove();
                    // $('.fp-progress').hide();
                    $(".progress-bar").css("width","0");
                    $('.fp-file').remove();
                    $('.fp-progress').hide();
                    // e.preventDefault()
                },
                upload: function() {
                    $('.fp-file-pause').show();
                    $('.fp-file-resume').hide();
                    fileUploader.r.resume();
                },
                Flow: fileUploader.r
            };
            fileUploader.windowR = window.r;
            fileUploader.events();
        },
        upload: function (target_url, cancel_url, selected_folder) {
            target_url = core.replaceAll(target_url, "#", "");
            cancel_url = core.replaceAll(cancel_url, "#", "");
            fileUploader.selected_folder = selected_folder;
            fileUploader.target_url = target_url;
            fileUploader.cancel_url = cancel_url;
        },
        events: function () {
            // Handle file add event
            fileUploader.r.on('fileAdded', function(file){
                // Show progress bar
                file.upload_started = false;
                file.upload_completed = false;
                file.target_url = fileUploader.target_url;
                file.cancel_url = fileUploader.cancel_url;
                file.query = {
                    "upload_folder" : fileUploader.selected_folder,
                };
                file.query[lvar.stk] = lvar.stc;

                $('.fp-progress, .uploading-file-list').show();
                $('.fp-progress .progress-start-link').css("display","inline");
                // Add the file to the list
                var inhtml = '';
                inhtml = '<li class="fp-file fp-file-'+file.uniqueIdentifier+'">' +
                    '<div class="col-md-8">' +
                    lvar['uploading'] + ' <span class="fp-file-name"></span> ' +
                    '<span class="fp-file-size"></span> ' +
                    '<span class="fp-file-progress"></span> ' +
                    '</div>' +
                    '<div class="col-md-4">' +
                    '<a class="fp-file-pause btn btn-sm btn-block btn-warning" style="display:none">' + lvar['pause_upload'] + '</a>' +
                    '<a class="fp-file-resume btn btn-sm btn-block btn-primary" style="display:none">' + lvar['resume_upload'] + '</a>' +
                    '<a class="fp-file-cancel btn btn-sm btn-block btn-danger">' + lvar['cancel'] + '</a>';

                inhtml += '</div>';
                $('.uploading-file-list').append(inhtml);
                var $self = $('.fp-file-'+file.uniqueIdentifier);
                $self.find('.fp-file-name').text(file.name);
                $self.find('.fp-file-size').text(core.readablizeBytes(file.size));

                $self.find('.fp-file-pause').on('click', function () {
                    file.pause();
                    $self.find('.fp-file-pause').hide();
                    $self.find('.fp-file-resume').show();
                });

                $self.find('.fp-file-resume').on('click', function () {
                    file.resume();
                    $self.find('.fp-file-pause').show();
                    $self.find('.fp-file-resume').hide();
                });

                $self.find('.fp-file-cancel').on('click', function () {
                    $(".progress-bar").css("width","0");
                    if($(".uploading-file-list li").length == 1) {
                        $(".fp-progress").hide();
                    }
                    $(".progress-resume-link").hide();
                    $(".progress-pause-link").hide();
                    file.cancel();
                    $self.hide();
                    $self.remove();
                });
            });
            $('.fp-progress .progress-start-link').unbind();
            $('.fp-progress .progress-start-link').on('click',function () {
                $('.fp-file-pause').show();
                fileUploader.r.upload();
            });
            fileUploader.r.on('complete', function(file){
                // Hide pause/resume when the upload has completed
                $('.fp-progress .progress-start-link').hide();
                $('.fp-progress .progress-resume-link').hide();
                $('.fp-progress .progress-pause-link').hide();
                filemanager.filelist(fileUploader.selected_folder);
            });
            fileUploader.r.on('fileSuccess', function(file, message, chunk){
                var $self = $('.fp-file-' + file.uniqueIdentifier);
                file.upload_completed = true;
                var json_data;
                try {
                    json_data = $.parseJSON(chunk.xhr.response);
                } catch (e) {

                }
                core.showToast("success", "fa-check", json_data['title'], json_data["messages"]);

                // Reflect that the file upload has completed
                $self.find('.fp-file-progress').text('(completed)');
                $self.find('.fp-file-pause, .fp-file-resume, .fp-file-cancel').remove();
            });
            fileUploader.r.on('fileError', function(file, message){
                var json_data;
                try {
                    json_data = $.parseJSON(message);
                    $('.fp-file-'+file.uniqueIdentifier+' .fp-file-progress').html('(file could not be uploaded: <b class="text-danger">'+json_data["messages"]+'</b>)');
                } catch (e) {
                    $('.fp-file-'+file.uniqueIdentifier+' .fp-file-progress').html('(file could not be uploaded: '+message+')');
                }
            });
            fileUploader.r.on('fileProgress', function(file, chunk){
                var json_data;
                var file_name;
                try {
                    json_data = $.parseJSON(chunk.xhr.response);
                    file_name = json_data.file_name;
                    file.chunked_name = file_name;
                } catch (e) {
                }
                file.upload_started = true;
                $('.fp-file-'+file.uniqueIdentifier+' .fp-file-progress')
                    .html(Math.floor(file.progress()*100) + '% '
                        + core.readablizeBytes(file.averageSpeed) + '/s '
                        + secondsToStr(file.timeRemaining()) + ' remaining') ;
                $('.fp-progress .progress-bar').css({width:Math.floor(fileUploader.r.progress()*100) + '%'});
            });
            fileUploader.r.on('uploadStart', function(){
                // Show pause, hide resume
                $('.fp-progress .progress-start-link').hide();
                $('.fp-progress .progress-resume-link').hide();
                $('.fp-progress .progress-pause-link').css("display","inline");
            });
            fileUploader.r.on('fileRemoved', function(file) {
                if(file.upload_started && (file.upload_completed == false)) {
                    var query = {
                        "file_name" : file.chunked_name,
                        "upload_folder" : file.query.upload_folder,
                    };
                    query[lvar.stk] = lvar.stc;
                    core.ajaxRequest({
                        method: 'POST',
                        url: file.cancel_url,
                        data: query,
                        before: function (){},
                        complete: function (){},
                        success: function (){},
                        error: function (){},
                    },true);
                }
            });

            $('.fp-progress .progress-resume-link').on('click',function (e) {
                $(this).hide();
                $('.fp-progress .progress-pause-link').css("display","inline");
                fileUploader.r.resume();
                e.preventDefault();
                return(false);
            });
            $('.fp-progress .progress-pause-link').on('click',function (e) {
                $(this).hide();
                $('.fp-progress .progress-resume-link').css("display","inline");
                fileUploader.r.pause();
                e.preventDefault();
                return(false);
            });
            $('.fp-progress .progress-cancel-link').on('click',function (e) {
                fileUploader.windowR.cancel()
                e.preventDefault();
                return(false);
            });
        },
        clear: function () {
            if(fileUploader.r != null) {
                fileUploader.r.cancel();
            }
            if(fileUploader.windowR != null) {
                fileUploader.windowR.cancel();
            }
        }
    }
}();
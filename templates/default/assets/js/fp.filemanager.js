var permission_manager;
var filemanager = function () {
    return {
        permission_url: "",
        selectedFolder: "",
        selectedUploadFolder: "",
        server_type: "",
        file_list_datas: {},
        init: function () {
            filemanager.folderAndFileActions();
            $('#folder_lists').on('click', function (e) {
                var id = $('#folder_lists').jstree(true).get_selected();
                var data_folder = $('#' + id[0]).attr('data-url');
                $('.fileList').attr('onclick', 'getFileLists("' + data_folder + '");');
                filemanager.filelist(data_folder);
                e.preventDefault();
            });
            filemanager.search();
            if (filemanager.server_type === "linux")
                permission_manager = linuxfm;
            else
                permission_manager = windowsfm;
            filemanager.uploadInit();
            $('#file-lists .frow').on('contextmenu', function () {
                if (!$(this).hasClass('noselect')) {
                    filemanager.changeClass(this);
                }
            });
            filemanager.context();
        },
        initFiles: function () {
            var table_id;
            table_id = $('.fm-table').attr('id');
            filemanager.filelist(table_id);
        },
        folderlist: function (target) {
            filemanager.initJstree(target);
            var data = {};
            data[lvar.stk] = lvar.stc;
            $.ajax({
                type: "POST",
                cache: false,
                url: "/" + index_url.trimLeft("#"),
                data: data,
                success: function (res) {
                    $(target).jstree(true).settings.core.data = res;
                    $(target).jstree(true).refresh();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                }
            });
        },
        initJstree: function (target) {
            $(target).jstree({
                "core": {
                    "themes": {
                        "responsive": true
                    },
                    'check_callback': true,
                    'data': ''
                },
                "types": {
                    "default": {
                        "icon": "fa fa-folder text-primary"
                    },
                    "file": {
                        "icon": "fa fa-file text-primary"
                    }
                },
                'plugins': ['state', 'dnd', 'sort', 'types', 'wholerow']
            });
        },
        refresh: function () {
            var id = $('#folder_lists').jstree(true).get_selected();
            var data_folder = $('#' + id[0]).attr('data-url');
            filemanager.filelist(data_folder);
        },
        fileListTable: function () {
            var output = '<div class="row">';
            output += '<div class="fm-table col-md-12 noselect" id="' + filemanager.file_list_datas.data_folder + '">';
            output += '<div class="row fm-table-header">' +
                '<div class="col-md-6" id="file-name">' + lvar['name'] + '<i class="fas fa-sort"></i></div>' +
                '<div class="col-md-2" id="file-date">' + lvar['modified'] + '<i class="fas fa-sort"></i></div>' +
                '<div class="col-md-3" id="file-type">' + lvar['type'] + '<i class="fas fa-sort"></i></div>' +
                '<div class="col-md-1" id="file-size">' + lvar['size'] + '<i class="fas fa-sort"></i></div>' +
                '</div>';

            var files = filemanager.file_list_datas.obj['files'];
            var table_body = '<div class="fm-table-body">';

            var table_body_elements = "";
            var arrayLength = files.length;
            var data_type;
            var download_link = '';
            if (files.length == 0) {
                table_body += '<div class="middle-box text-center"><h1>Empty</h1></div>';
            } else {
                for (var i = 0; i < arrayLength; i++) {
                    data_type = "folder";
                    if (files[i]['mime'] != "folder") {
                        data_type = "file";
                        download_link = ' data-download="' + files[i]['data-download'] + '"';
                    }
                    table_body_elements += '<div class="row frow destination" data-name="' + files[i]['path'] + '" id="' + i + '" data-clear-name="' + files[i]['clear-name'] + '"' + download_link + ' data-id="' + data_type + '">' +
                        '<div class="col-md-6"><i class="fa fa-' + files[i]['mime'] + ' text-primary margin-right-10" style="font-size: 1.2rem"></i> <span class="fname" title="' + files[i]['id'] + '" data-toggle="tooltip">' + files[i]['file_name'] + '</span></div>' +
                        '<div class="col-md-2"><span class="fdate">' + files[i]['modified_date'] + '</span></div>' +
                        '<div class="col-md-3"><span class="ftype">' + files[i]['type'] + '</span></div>' +
                        '<div class="col-md-1"><span>' + files[i]['size'] + '</span><span class="fsize text-hide">' + files[i]['rsize'] + '</span></div>';
                    table_body_elements += '</div>';
                }
            }

            table_body += table_body_elements + "</div>";
            output += table_body + '</div>' + '</div>';

            output += '<div class="row margin-top-10">' +
                '<div class="col-md-12">' +
                '<div class="page_navigation"></div>' +
                '</div>' +
                '</div>';

            filemanager.file_list_datas.el.html(output);
            $('#file-name').data("sortKey", "span.fname");
            $('#file-date').data("sortKey", "span.fdate");
            $('#file-type').data("sortKey", "span.ftype");
            $('#file-size').data("sortKey", "span.fsize");

            $(".fm-table-header #file-name").click(function (e) {
                $(".fm-table-header #file-date i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-size i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-type i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-size").removeClass("selected");
                $(".fm-table-header #file-date").removeClass("selected");
                $(".fm-table-header #file-type").removeClass("selected");
                filemanager.initSort(this, false);
                $(this).addClass("selected");
                e.preventDefault();
            });
            $(".fm-table-header #file-date").click(function (e) {
                $(".fm-table-header #file-name i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-size i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-type i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-size").removeClass("selected");
                $(".fm-table-header #file-name").removeClass("selected");
                $(".fm-table-header #file-type").removeClass("selected");
                filemanager.initSort(this, false);
                $(this).addClass("selected");
                e.preventDefault();
            });
            $(".fm-table-header #file-size").click(function (e) {
                $(".fm-table-header #file-name i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-date i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-type i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-name").removeClass("selected");
                $(".fm-table-header #file-date").removeClass("selected");
                $(".fm-table-header #file-type").removeClass("selected");
                filemanager.initSort(this, true);
                $(this).addClass("selected");
                e.preventDefault();
            });
            $(".fm-table-header #file-type").click(function (e) {
                $(".fm-table-header #file-name i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-date i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-size i").attr("class", "fas fa-sort");
                $(".fm-table-header #file-name").removeClass("selected");
                $(".fm-table-header #file-date").removeClass("selected");
                $(".fm-table-header #file-size").removeClass("selected");
                filemanager.initSort(this, false);
                $(this).addClass("selected");
                e.preventDefault();
            });
            core.multipleSelect(".fm-table-body", ".frow");
            core.paginate(".fm-table-body", ".frow");
            filemanager.disableEnableButtons();
        },
        initSort: function (elem, isInt) {
            var icon_elem = $(elem).find("i");
            if (icon_elem.hasClass("fa-sort")) {
                icon_elem.removeClass("fa-sort");
                icon_elem.addClass("fas fa-sort-down");
                filemanager.sortList($('.fm-table-body'), ".frow", $(elem).data("sortKey"), true, isInt);
            } else if (icon_elem.hasClass("fa-sort-down")) {
                icon_elem.removeClass("fa-sort-down");
                icon_elem.addClass("fas fa-sort-up");
                filemanager.sortList($('.fm-table-body'), ".frow", $(elem).data("sortKey"), false, isInt);
            } else if (icon_elem.hasClass("fa-sort-up")) {
                icon_elem.removeClass("fa-sort-up");
                icon_elem.addClass("fas fa-sort-down");
                filemanager.sortList($('.fm-table-body'), ".frow", $(elem).data("sortKey"), true, isInt);
            }
            $(".frow").removeClass("hidden");
            core.paginate(".fm-table-body", ".frow");
        },
        sortList: function (parent, childSelector, keySelector, reverse, isInt) {
            var items;
            if (reverse) {
                items = parent.children(childSelector).sort(function (a, b) {
                    var vA = $(keySelector, a).text();
                    var vB = $(keySelector, b).text();
                    if (!isInt)
                        return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
                    else
                        return (parseInt(vA) < parseInt(vB)) ? -1 : (parseInt(vA) > parseInt(vB)) ? 1 : 0;
                });
            } else {
                items = parent.children(childSelector).sort(function (a, b) {
                    var vA = $(keySelector, a).text();
                    var vB = $(keySelector, b).text();
                    if (!isInt)
                        return (vA > vB) ? -1 : (vA < vB) ? 1 : 0;
                    else
                        return (parseInt(vA) > parseInt(vB)) ? -1 : (parseInt(vA) < parseInt(vB)) ? 1 : 0;
                });
            }
            parent.append(items);
        },
        fileListIcons: function () {
            var output = '<div class="row">';
            output += '<div class="fm-table col-md-12 noselect" id="' + filemanager.file_list_datas.data_folder + '">';
            output += '<div class="fm-table-body row">';
            var files = filemanager.file_list_datas.obj['files'];

            var data_type;
            var download_link = '';
            if (files.length == 0) {
                output += '<div class="middle-box text-center"><h1>Empty</h1></div>';
            } else {
                for (var i = 0; i < files.length; i++) {
                    data_type = "folder";
                    if (files[i]['mime'] != "folder") {
                        data_type = "file";
                        download_link = ' data-download="' + files[i]['data-download'] + '"';
                    }
                    output += '<div class="frow col-lg-2 col-md-3 col-sm-6" data-name="' + files[i]['path'] + '" data-clear-name="' + files[i]['clear-name'] + '" ' + download_link + ' data-id="' + data_type + '">' +
                        '<div class="fm-card">' +
                        '<div class="fm-file">' +
                        '<a href="javascript:void(0);">' +
                        '<div class="hover">' +
                        '</div>' +
                        '<div class="icon">' +
                        '<i class="fa fa-' + files[i]['mime'] + ' text-info fa-3x"></i>' +
                        '</div>' +
                        '<div class="fm-file-name" title="' + files[i]['id'] + '" data-toggle="tooltip">' +
                        '<p class="m-b-5 text-muted">' + files[i]['file_name'] + '</p>' +
                        '<small>Size: ' + files[i]['size'] + ' <span class="date text-muted">' + files[i]['modified_date'] + '</span></small>' +
                        '</div>' +
                        '</a>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                }
            }
            output += '</div></div></div>';
            output += '<div class="row margin-top-10">' +
                '<div class="col-md-12">' +
                '<div class="page_navigation"></div>' +
                '</div>' +
                '</div>';

            filemanager.file_list_datas.el.html(output);
            core.multipleSelect(".fm-table-body", ".frow");
            core.paginate(".fm-table", ".frow");
            filemanager.disableEnableButtons();
        },
        filelist: function (data_folder) {
            if (typeof data_folder != "undefined") {
                if (data_folder.length > 0) {
                    if (data_folder != filemanager.selectedFolder) {
                        filemanager.selectedFolder = data_folder;
                    }
                }
            }
            var el = $('#file-lists');
            el.html('<div class="folder-loading"><i class="fa fa-spinner"></i><br>' + lvar["loading"] + '...</div>');
            var data = {};
            data['folder'] = filemanager.selectedFolder;
            data[lvar.stk] = lvar.stc;
            var options = {
                method: 'POST',
                url: "/" + file_manager_url.trimLeft("#"),
                data: data,
            };

            options.before = function () {
            };
            options.success = function (obj) {
                // core.is_async_step = true;
                filemanager.initFileList(obj, data_folder, el)
            };
            options.error = function () {
                return false;
            };
            options.complete = function () {
            };
            var obj = core.ajaxRequest(options);
        },
        disableEnableButtons: function () {
            var forbidden_paths = [
                "/",
                "/dev",
                "/subdomains",
            ];
            if ($("input#folder-path").val().length == 0 || forbidden_paths.includes($("input#folder-path").val())) {
                $('[data-id="fl-back"]').addClass("disabled");
                $('[data-id="fl-back"]').attr("disabled", "disabled");
                $('[data-id="fl-create-file"]').addClass("disabled");
                $('[data-id="fl-create-file"]').attr("disabled", "disabled");
                $('[data-id="fl-create-folder"]').addClass("disabled");
                $('[data-id="fl-create-folder"]').attr("disabled", "disabled");
                $('[data-id="fl-delete-selected"]').addClass("disabled");
                $('[data-id="fl-delete-selected"]').attr("disabled", "disabled");
                $('[data-id="fl-upload"]').addClass("disabled");
                $('[data-id="fl-upload"]').attr("disabled", "disabled");
                $('[data-id="fl-upload-files"]').addClass("disabled");
                $('[data-id="fl-fl-upload-files"]').attr("disabled", "disabled");
            } else {
                $('[data-id="fl-back"]').removeClass("disabled");
                $('[data-id="fl-back"]').removeAttr("disabled");
                $('[data-id="fl-create-file"]').removeClass("disabled");
                $('[data-id="fl-create-file"]').removeAttr("disabled", "disabled");
                $('[data-id="fl-create-folder"]').removeClass("disabled");
                $('[data-id="fl-create-folder"]').removeAttr("disabled", "disabled");
                $('[data-id="fl-delete-selected"]').removeClass("disabled");
                $('[data-id="fl-delete-selected"]').removeAttr("disabled", "disabled");
                $('[data-id="fl-upload"]').removeClass("disabled");
                $('[data-id="fl-upload"]').removeAttr("disabled", "disabled");
                $('[data-id="fl-upload-files"]').removeClass("disabled");
                $('[data-id="fl-upload-files"]').removeAttr("disabled", "disabled");
            }
        },
        search: function () {
            $("#search-file").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $(".fm-table .fm-table-body .frow").filter(function () {
                    if ($(this).attr("data-clear-name").toLowerCase().indexOf(value) > -1) {
                        $(this).removeClass("d-none");
                    } else {
                        $(this).addClass("d-none");
                    }
                    core.paginate(".fm-table", ".frow");
                });
            });
        },
        initFileList: function (obj, data_folder, el) {
            var jstree_path = obj["path"];

            var selecting_id = $('#folder_lists').find('[data-name="' + jstree_path + '"]').attr("id");
            $('#folder_lists').jstree(true).deselect_all();
            $('#folder_lists').jstree(true).open_node(selecting_id);
            $('#folder_lists').jstree(true).select_node(selecting_id);

            var path = obj['path'];
            if (path.length == 0) {
                path = "/";
            }
            $("#file-list-panel .ibox-title #folder-path").val(path);
            filemanager.file_list_datas = {
                data_folder: data_folder,
                obj: obj,
                el: el,
            };

            if (typeof window.localStorage.file_manager_icons != "undefined") {
                if (window.localStorage.file_manager_icons == "on") {
                    filemanager.fileListIcons();
                } else {
                    filemanager.fileListTable();
                }
            } else {
                filemanager.fileListTable();
            }
            $('#file-lists .frow').on('dblclick', function () {
                if ($(this).attr('data-id') == "folder") {
                    filemanager.filelist($(this).attr('data-name'));
                }
            });

            $('[data-toggle="tooltip"]').tooltip();
            $('.tooltip').remove();
        },
        folderAndFileActions: function () {
            $('[data-id="fl-refresh"]').click(function (e) {
                filemanager.refresh();
                e.preventDefault();
            });
            $('[data-id="fl-create-file"]').click(function (e) {
                filemanager.createFile();
                e.preventDefault();
            });
            $('[data-id="fl-create-folder"]').click(function (e) {
                filemanager.createFolder();
                e.preventDefault();
            });
            $('[data-id="fl-show-icons"]').click(function (e) {
                window.localStorage.setItem('file_manager_icons', "on");
                window.localStorage.setItem('file_manager_list', "off");
                filemanager.fileListIcons();
                $('#file-lists .frow').on('dblclick', function () {
                    if ($(this).attr('data-id') == "folder") {
                        filemanager.filelist($(this).attr('data-name'));
                    }
                });
                e.preventDefault();
            });
            $('[data-id="fl-show-list"]').click(function (e) {
                window.localStorage.setItem('file_manager_icons', "off");
                window.localStorage.setItem('file_manager_list', "on");
                filemanager.fileListTable();
                $('#file-lists .frow').on('dblclick', function () {
                    if ($(this).attr('data-id') == "folder") {
                        filemanager.filelist($(this).attr('data-name'));
                    }
                });
                e.preventDefault();
            });
            $('[data-id="fl-delete-selected"]').click(function (e) {
                var selecteds = $(".fm-table-body").multiSelector("get");
                var items = [];
                for (var i = 0; i < selecteds.length; ++i) {
                    items.push({
                        name: $(selecteds[i]).attr("data-name"),
                        type: $(selecteds[i]).attr("data-id"),
                        clear_name: $(selecteds[i]).attr("data-clear-name"),
                    })
                }
                filemanager.delete(items);
                e.preventDefault();
            });
            $('[data-id="fl-back"]').click(function (e) {
                filemanager.filelist(filemanager.file_list_datas.obj.parent);
                e.preventDefault();
            });
            $('[data-id="fl-upload"]').click(function (e) {
                fileUploader.upload($('form#fileupload').attr('action'), $('form#fileupload').attr('data-cancel'), filemanager.file_list_datas.obj["current_path"]);
                $('#upload_files').modal("show");
                e.preventDefault();
            });
            $('[data-id="fl-upload-files"]').click(function (e) {
                fileUploader.upload($('form#fileupload').attr('action'), $('form#fileupload').attr('data-cancel'), filemanager.file_list_datas.obj["current_path"]);
                $('#upload_files').modal("show");
                e.preventDefault();
            });
        },
        getFileContent: function (url, data) {
            var obj = core.ajaxRequest({
                method: 'POST',
                url: "/" + url.trimLeft("#"),
                data: data
            });
            if (obj['type'] == 'success') {
                var content = obj.content;
                content["content"] = core.replaceAll(content["content"], '&nbsp;', ' ');
                $('#file_editor_textarea').val('');
                $('#file_editor_textarea').val(content['content']);
                var ext = data["clear_name"].split(".")[data["clear_name"].split(".").length - 1];

                filemanager.editor(content['type'], data['file'], ext, data["clear_name"]);
                $('#file_edit_modal').modal('show');

            } else {
                core.showToast("error", "fa-exclamation", obj['title'], obj["content"]);
            }
        },
        editor: function (type, name, ext, clear_name) {
            var mode = "text/html";
            var foldOption;
            if (ext == "php") {
                mode = "application/x-httpd-php";
            }
            if (ext == "css" || ext == "less" || ext == "scss") {
                mode = "text/css";
            }
            if (ext == "sh" || ext == "bash" || ext == "ksh" || ext == "zsh" || ext == "") {
                mode = "text/x-sh";
            }
            if (ext == "pl") {
                mode = "text/x-perl";
            }
            if (ext == "py") {
                mode = "text/x-python";
            }
            if (ext == "sql") {
                mode = "text/x-sql";
            }
            if (ext == "json") {
                mode = {name: "javascript", json: true};
                foldOption = {
                    widget: (from, to) => {
                        var count = undefined;

                        // Get open / close token
                        var startToken = '{', endToken = '}';
                        var prevLine = editor.getLine(from.line);
                        if (prevLine.lastIndexOf('[') > prevLine.lastIndexOf('{')) {
                            startToken = '[', endToken = ']';
                        }

                        // Get json content
                        var internal = editor.getRange(from, to);
                        var toParse = startToken + internal + endToken;

                        // Get key count
                        try {
                            var parsed = JSON.parse(toParse);
                            count = Object.keys(parsed).length;
                        } catch (e) {
                        }

                        return count ? `\u21A4${count}\u21A6` : '\u2194';
                    }
                };
            }
            if (ext == "js") {
                mode = {name: "javascript", json: true};
            }
            if (ext == "csv") {
                mode = "csv";
            }
            if (ext == "yml" || ext == "yaml") {
                mode = "text/x-yaml";
            }

            var editor = CodeMirror.fromTextArea(document.getElementById("file_editor_textarea"), {
                lineNumbers: true,
                tabSize: 4,
                styleActiveLine: true,
                autoCloseBrackets: true,
                lineWrapping: false,
                autoCloseTags: true,
                mode: mode,
                fold: true,
                extraKeys: {
                    "Ctrl-Space": "autocomplete",
                    "Ctrl-Y": cm => CodeMirror.commands.foldAll(cm),
                    "Ctrl-I": cm => CodeMirror.commands.unfoldAll(cm),
                    "Ctrl-J": "toMatchingTag"
                },
                matchTags: {
                    bothTags: true
                },
                highlightSelectionMatches: {
                    showToken: /\w/, annotateScrollbar: true
                },
                keyMap: "sublime",
                matchBrackets: true,
                foldGutter: true,
                theme: "monokai",
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                foldOptions: foldOption,
            });
            // var modalContentHeight = $('#file_edit_modal').find('.modal-body').attr("data-size");
            // console.log ('modalContentHeight = ' + modalContentHeight);
            editor.setSize("100%", "900px");

            $('#file_edit_modal').on('shown.bs.modal', function (e) {
                editor.refresh();
                $('#submit_save_file').unbind();
                $('#submit_save_file').on('click', function () {
                    var data = {};
                    data['content'] = editor.getValue();
                    data['name'] = name;
                    data[lvar.stk] = lvar.stc;
                    var result = core.ajaxRequest({
                        method: 'POST',
                        url: "/" + save_file_url,
                        data: data
                    }, true);
                    if (result['type'] == "success") {
                        $('#file_edit_modal').modal('hide');
                    } else {
                        core.showToast("error", "fa-exclamation", result['title'], result["content"]);
                    }
                });

                // $('#file_edit_modal').on('hidden.bs.modal', function (e) {
                //     $('.file_edit').html('');
                //     editor.clearHistory();
                //     $('.file_edit').html('<textarea id="file_editor_textarea"></textarea>');
                //     e.preventDefault();
                // });
                e.preventDefault();
            });
            $('#file_edit_modal').on('hidden.bs.modal', function (e) {
                $('.file_edit').html('');
                editor.setValue("");
                editor.clearHistory();
                editor.clearGutter("gutterId");
                $('.file_edit').html('<textarea id="file_editor_textarea"></textarea>');
                e.preventDefault();
            });

        },
        createFile: function () {
            sweetAlert({
                title: lvar['create_file'],
                text: lvar['enter_file_name'],
                type: "input",
                showCancelButton: true,
                input_group: true,
                input_group_addon: filemanager.file_list_datas.obj.path === "/" ? "/" : filemanager.file_list_datas.obj.path + "/",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: lvar["submit"],
                closeOnConfirm: false,
                buttonsStyling: false,
            }, function (inputValue) {
                var post_data = {};
                post_data[lvar["stk"]] = lvar["src"];
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("You need to write something!");
                    return false
                }
                var table_id;
                table_id = $('#file-lists .fm-table').attr('id');

                post_data['name'] = inputValue;
                post_data['current_folder'] = table_id;
                var options = {
                    async: true,
                    method: 'POST',
                    url: "/" + create_file_url,
                    data: post_data
                };
                options.before = function () {
                    $(".sa-button-container .cancel").attr("disabled", "disabled");
                    $(".sa-button-container .confirm").attr("disabled", "disabled");
                };
                options.success = function (data) {
                    $(".sa-button-container .cancel").removeAttr("disabled");
                    $(".sa-button-container .confirm").removeAttr("disabled");
                    // core.is_async_step = true;
                    if (data['type'] == "success") {
                        $(".sweet-alert").find(".input-group").addClass("d-none");
                        swal("Nice!", "File create: " + inputValue, "success");
                        filemanager.folderlist('#folder_lists');
                        filemanager.initFiles();
                    } else {
                        swal.showInputError(data.messages);
                    }
                };
                options.error = function () {
                    $(".sa-button-container .confirm").removeAttr("disabled");
                    return false;
                };
                options.complete = function () {
                    $(".sa-button-container .cancel").removeAttr("disabled");
                    $(".sa-button-container .confirm").removeAttr("disabled");
                };
                core.ajaxRequest(options, false);
            });
        },
        createFolder: function () {
            swal({
                title: lvar['create_folder'],
                text: lvar['enter_folder_name'],
                type: "input",
                showCancelButton: true,
                input_group: true,
                input_group_addon: filemanager.file_list_datas.obj.path === "/" ? "/" : filemanager.file_list_datas.obj.path + "/",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: lvar["submit"],
                closeOnConfirm: false,
                buttonsStyling: false,
            }, function (inputValue) {
                var post_data = {};
                post_data[lvar["stk"]] = lvar["src"];
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("You need to write something!");
                    return false
                }
                var table_id;
                table_id = $('#file-lists .fm-table').attr('id');

                post_data['name'] = inputValue;
                post_data['current_folder'] = table_id;

                var options = {
                    async: true,
                    method: 'POST',
                    url: "/" + create_folder_url,
                    data: post_data
                };
                options.before = function () {
                    $(".sa-button-container .cancel").attr("disabled", "disabled");
                    $(".sa-button-container .confirm").attr("disabled", "disabled");
                };
                options.success = function (data) {
                    $(".sa-button-container .cancel").removeAttr("disabled");
                    $(".sa-button-container .confirm").removeAttr("disabled");
                    // core.is_async_step = true;
                    if (data['type'] == "success") {
                        $(".sweet-alert").find(".input-group").addClass("d-none");
                        swal("Nice!", "Folder create: " + inputValue, "success");
                        filemanager.folderlist('#folder_lists');
                        filemanager.initFiles();
                    } else {
                        swal.showInputError(data.messages);
                    }
                };
                options.error = function () {
                    $(".sa-button-container .confirm").removeAttr("disabled");
                    return false;
                };
                options.complete = function () {
                    $(".sa-button-container .cancel").removeAttr("disabled");
                    $(".sa-button-container .confirm").removeAttr("disabled");
                };
                core.ajaxRequest(options, false);
            });
        },
        edit: function (name, clear_name) {
            var data = {};
            data['file'] = name;
            data['clear_name'] = clear_name;
            filemanager.getFileContent(edit_file_url, data);
        },
        rename: function (clear_name, file_name, element, type) {
            swal({
                title: lvar["rename"],
                text: lvar['are_you_sure'],
                type: "input",
                inputPlaceholder: clear_name,
                input_group: true,
                input_group_addon: filemanager.file_list_datas.obj.path === "/" ? "/" : filemanager.file_list_datas.obj.path + "/",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: lvar["submit"],
                closeOnConfirm: false,
                buttonsStyling: false,
            }, function (inputValue) {
                var post_data = {};
                post_data[lvar["stk"]] = lvar["src"];
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("You need to write something!");
                    return false
                }

                post_data['new_name'] = inputValue;
                post_data['name'] = file_name;
                post_data['type'] = type;

                var options = {
                    async: true,
                    method: 'POST',
                    url: "/" + rename_url,
                    data: post_data
                };
                options.before = function () {
                    $(".sa-button-container .cancel").attr("disabled", "disabled");
                    $(".sa-button-container .confirm").attr("disabled", "disabled");
                };
                options.success = function (data) {
                    $(".sa-button-container .cancel").removeAttr("disabled");
                    $(".sa-button-container .confirm").removeAttr("disabled");
                    // core.is_async_step = true;
                    if (data['type'] == "success") {
                        $(".sweet-alert").find(".input-group").addClass("d-none");
                        swal("Nice!", "File rename: " + inputValue, "success");
                        filemanager.folderlist('#folder_lists');
                        filemanager.initFiles();
                    } else {
                        swal.showInputError(data.messages);
                    }
                };
                options.error = function () {
                    $(".sa-button-container .confirm").removeAttr("disabled");
                    return false;
                };
                options.complete = function () {
                    $(".sa-button-container .cancel").removeAttr("disabled");
                    $(".sa-button-container .confirm").removeAttr("disabled");
                };
                core.ajaxRequest(options, false);
            });
        },
        resetInput: function () {
            var old_element_parent = $('#rename_old_item').parent();
            var old_element_div = $('#rename_old_item').html();
            $(old_element_parent).html(old_element_div);
        },
        move: function (clear_name, name) {
            $('#folder_lists_modal .modal-header h5').text(lvar["select_target"] + ": " + clear_name);
            $('#folder_lists_modal').modal();
            filemanager.folderlist('#move_folders');
            $("#submit_move").unbind("click");
            $('#submit_move').bind('click', function (e) {

                var id = $('#move_folders').jstree(true).get_selected();
                var data_url = $('#' + id[0]).attr('data-url');
                var data = {};
                data['target'] = data_url;
                data['source'] = name;
                var result = core.ajaxRequest({
                    method: 'POST',
                    url: "/" + move_url,
                    data: data
                }, true);

                if (result['type'] == "success") {
                    filemanager.initFiles();
                    $('#folder_lists_modal').modal("hide");
                }
            });
        },
        copy: function (name) {
            $.cookie('copied_file', name, {expires: 3600});
            core.showToast("success", "fa-check", lvar["success"], "File copied temporarily");
        },
        paste: function (name) {
            swal({
                title: lvar["confirm"],
                text: lvar['are_you_sure'],
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: lvar["submit"],
                closeOnConfirm: false
            }, function (confirmed) {
                if (confirmed) {
                    var copied_file = $.cookie("copied_file");
                    var data = {};
                    data['target'] = name;
                    data['source'] = copied_file;
                    core.ajaxRequest({
                        method: 'POST',
                        url: "/" + paste_url,
                        data: data
                    });
                    filemanager.initFiles();
                }
                swal.close();
            });
        },
        delete: function (items) {
            if(items.length === 0) {
                swal({
                    title: "Failed",
                    text: "Please select at least one file or folder",
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: lvar["submit"],
                    closeOnConfirm: false
                });
                return;
            }
            var item_html = '<div class="modal-table-responsive"><table class="table table-bordered table-hover">';
            item_html += '<thead>';
            item_html += '<tr>';
            item_html += '<th>' + lvar["name"];
            item_html += '</th>';
            item_html += '<th>' + lvar["type"];
            item_html += '</th>';
            item_html += '</tr>';
            item_html += '</thead>';
            item_html += '<tbody>';
            for (var i = 0; i < items.length; ++i) {
                item_html += "<tr>";
                item_html += "<td class='truncate'>" + items[i].clear_name;
                item_html += "</td>";
                item_html += "<td>" + items[i].type;
                item_html += "</td>";
                item_html += "</tr>";
            }
            item_html += "</table></div>";
            swal({
                title: lvar['are_you_sure'],
                text: item_html,
                type: "warning",
                html: true,
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: lvar["submit"],
                closeOnConfirm: false
            }, function (confirmed) {
                if (confirmed) {
                    var data = {};
                    data['selecteds'] = items;
                    core.ajaxRequest({
                        method: 'POST',
                        url: "/" + delete_url,
                        data: data
                    }, true);
                    filemanager.initFiles();
                    filemanager.folderlist('#folder_lists');
                }
                swal.close();
            });
        },
        download: function (url) {
            window.open(url, '_blank');
        },
        getPermissions: function (name, url) {
            var data = {};
            data['name'] = name;
            return core.ajaxRequest({
                method: 'POST',
                url: "/" + url.trimLeft("#"),
                data: data
            });
        },
        permissionFile: function (name, clean_name) {
            var permissions = filemanager.getPermissions(name, filemanager.get_permissions_url);
            permission_manager.init(permissions, name);
            $('#permissions_modal h5').text(lvar["permission"] + ": " + clean_name);
            $('#permissions_modal').modal();
        },
        changeClass: function (element) {
            if ($(element).hasClass("frow")) {
                $(element).closest(".frow").siblings().removeClass("ms-selected");
                $(element).closest(".frow").siblings().removeClass("ms-last");
                $(element).addClass("ms-selected ms-last");
            }
        },
        context: function () {
            $.contextMenu( 'destroy' );
            $.contextMenu({
                selector: '.fm-table-body [data-id="folder"]',
                callback: function (key, options) {
                    var forbidden_paths = [
                        "/",
                        "/dev",
                        "/subdomains",
                    ];
                    var disabled = false;
                    if ($("input#folder-path").val().length == 0 || forbidden_paths.includes($("input#folder-path").val())) {
                        disabled = true;
                    }
                    filemanager.resetInput();
                    var element = options["$trigger"][0];
                    var clear_name = element['dataset']['clearName'];
                    var file_name = element['dataset']['name'];
                    var type = element['dataset']['id'];
                    switch (key) {
                        case "rename":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.rename(clear_name, file_name, element, type);
                            break;
                        case "move":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.move(clear_name, file_name);
                            break;
                        case "delete":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.delete([{name: file_name, type: type, clear_name: clear_name}]);
                            break;
                        case "createfolder":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.createFolder(file_name);
                            break;
                        case "createfile":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.createFile(file_name);
                            break;
                        case "permission":
                            filemanager.permissionFile(file_name, clear_name);
                            break;
                        case "upload":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            fileUploader.upload($('form#fileupload').attr('action'), $('form#fileupload').attr('data-cancel'), filemanager.file_list_datas.obj["current_path"]);

                            $('#upload_files').modal("show");
                            break;
                    }
                },
                items: {
                    "rename": {name: "Rename Folder", icon: "fas fa-edit"},
                    "move": {name: "Move", icon: "fas fa-arrows-alt"},
                    "delete": {name: "Delete", icon: "fas fa-times"},
                    "sep1": "---------",
                    "createfile": {name: "Create File", icon: "fas fa-file-medical"},
                    "createfolder": {name: "Create Folder", icon: "fas fa-folder-plus"},
                    "upload": {name: "Upload", icon: "fas fa-upload"},
                    "permission": {name: "Permissions", icon: "fas fa-shield-alt"},
                }
            });
            $.contextMenu({
                className: 'css-title',
                selector: '.fm-table-body [data-id="file"]',
                callback: function (key, options) {
                    filemanager.resetInput();
                    var element = options["$trigger"][0];
                    var clear_name = element['dataset']['clearName'];
                    var file_name = element['dataset']['name'];
                    var url = element['dataset']['download'];
                    var type = element['dataset']['id'];
                    var disabled = false;
                    var forbidden_paths = [
                        "/",
                        "/dev",
                        "/subdomains",
                    ];
                    if ($("input#folder-path").val().length == 0 || forbidden_paths.includes($("input#folder-path").val())) {
                        disabled = true;
                    }
                    switch (key) {
                        case "rename":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.rename(clear_name, file_name, element, type);
                            break;
                        case "move":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.move(clear_name, file_name);
                            break;
                        case "edit":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.edit(file_name, clear_name);
                            break;
                        case "delete":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.delete([{name: file_name, type: type, clear_name: clear_name}]);
                            break;
                        case "permission":
                            filemanager.permissionFile(file_name, clear_name);
                            break;
                        case "createfolder":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.createFolder(file_name);
                            break;
                        case "createfile":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            filemanager.createFile(file_name);
                            break;
                        case "upload":
                            if (disabled) {
                                filemanager.forbiddenMessage();
                                return;
                            }
                            fileUploader.upload($('form#fileupload').attr('action'), $('form#fileupload').attr('data-cancel'), filemanager.file_list_datas.obj["current_path"]);
                            $('#upload_files').modal("show");
                            break;
                    }
                },
                items: {
                    "rename": {name: "Rename File", icon: "fas fa-edit"},
                    "move": {name: "Move", icon: "fas fa-arrows-alt"},
                    "edit": {name: "Edit File", icon: "fas fa-pencil-alt"},
                    "delete": {name: "Delete", icon: "fas fa-times"},
                    "sep1": "---------",
                    "createfile": {name: "Create File", icon: "fas fa-file-medical"},
                    "createfolder": {name: "Create Folder", icon: "fas fa-folder-plus"},
                    "upload": {name: "Upload", icon: "fas fa-upload"},
                    "permission": {name: "Permissions", icon: "fas fa-shield-alt"},
                }
            });
        },
        forbiddenMessage: function () {
            core.showToast("error", "fa-exclamation", "Failed", "You cannot do this in this directory!");
        },
        uploadInit: function () {
            fileUploader.clear();
            fileUploader.init();
            $('#upload_files').on('hidden.bs.modal', function (e) {
                fileUploader.clear();
                e.preventDefault();
            });
            $('#upload_files').on('shown.bs.modal', function (e) {
                $("#upload_files .modal-title").text($("#folder-path").val());
                e.preventDefault();
            });
        }
    };
}();
var linuxfm = function () {
    return {
        init: function (permissions, file_name) {
            $('input[name="t_total"]').val(permissions);
            linuxfm.octalchange();
            $('input[name^="group"]').on('ifChecked ifUnchecked', function(event) {
                linuxfm.calcChMod();
            });
            $('input[name^="owner"]').on('ifChecked ifUnchecked', function(event) {
                linuxfm.calcChMod();
            });
            $('input[name^="other"]').on('ifChecked ifUnchecked', function(event) {
                linuxfm.calcChMod();
            });

            $('[name="t_total"]').on('keyup', function () {
                linuxfm.octalchange();
            });
            $("#submit_permission").unbind("click");
            $('#submit_permission').on('click', function () {
                var data = {};
                data['permission'] = $('input[name="t_total"]').val();
                data['name'] = file_name;
                core.ajaxRequest({
                    method: 'POST',
                    url: "/" + filemanager.permissions_url.trimLeft("#"),
                    data: data
                })
                filemanager.initFiles();
                $('#permissions_modal').modal('hide');
                //e.preventDefault();
                return false;
            });
        },
        octalchange: function () {
            var val = document.chmod.t_total.value;
            var ownerbin = parseInt(val.charAt(0)).toString(2);
            while (ownerbin.length < 3) {
                ownerbin = "0" + ownerbin;
            }
            var groupbin = parseInt(val.charAt(1)).toString(2);
            while (groupbin.length < 3) {
                groupbin = "0" + groupbin;
            }
            var otherbin = parseInt(val.charAt(2)).toString(2);
            while (otherbin.length < 3) {
                otherbin = "0" + otherbin;
            }
            document.chmod.owner4.checked = parseInt(ownerbin.charAt(0));
            document.chmod.owner2.checked = parseInt(ownerbin.charAt(1));
            document.chmod.owner1.checked = parseInt(ownerbin.charAt(2));
            document.chmod.group4.checked = parseInt(groupbin.charAt(0));
            document.chmod.group2.checked = parseInt(groupbin.charAt(1));
            document.chmod.group1.checked = parseInt(groupbin.charAt(2));
            document.chmod.other4.checked = parseInt(otherbin.charAt(0));
            document.chmod.other2.checked = parseInt(otherbin.charAt(1));
            document.chmod.other1.checked = parseInt(otherbin.charAt(2));
            $('#permissions_modal .checkbox-blue').iCheck("update");
            linuxfm.calcChMod(1);
        },
        calcChMod: function (nototals) {
            var users = ["owner", "group", "other"];
            var totals = ["", "", ""];
            var syms = ["", "", ""];

            for (var i = 0; i < users.length; i++) {
                var user = users[i];
                var field4 = user + "4";
                var field2 = user + "2";
                var field1 = user + "1";
                //var total = "t_" + user;
                var symbolic = "sym_" + user;
                var number = 0;
                var sym_string = "";

                if($(document.chmod[field4]).is(':checked')) {
                    number += 4;
                }
                if($(document.chmod[field2]).is(':checked')) {
                    number += 2;
                }
                if($(document.chmod[field1]).is(':checked')) {
                    number += 1;
                }

                if($(document.chmod[field4]).is(':checked')) {
                    sym_string += "r";
                }
                else {
                    sym_string += "-";
                }
                if($(document.chmod[field2]).is(':checked')) {
                    sym_string += "w";
                }
                else {
                    sym_string += "-";
                }
                if($(document.chmod[field1]).is(':checked')) {
                    sym_string += "x";
                }
                else {
                    sym_string += "-";
                }
                // if (document.chmod[field4].checked == true) {
                //     number += 4;
                // }
                // if (document.chmod[field2].checked == true) {
                //     number += 2;
                // }
                // if (document.chmod[field1].checked == true) {
                //     number += 1;
                // }

                // if (document.chmod[field4].checked == true) {
                //     sym_string += "r";
                // } else {
                //     sym_string += "-";
                // }
                // if (document.chmod[field2].checked == true) {
                //     sym_string += "w";
                // } else {
                //     sym_string += "-";
                // }
                // if (document.chmod[field1].checked == true) {
                //     sym_string += "x";
                // } else {
                //     sym_string += "-";
                // }

                //if (number == 0) { number = ""; }
                //document.chmod[total].value =
                totals[i] = totals[i] + number;
                syms[i] = syms[i] + sym_string;

            }
            if (!nototals)
                document.chmod.t_total.value = totals[0] + totals[1] + totals[2];
            document.chmod.sym_total.value = "-" + syms[0] + syms[1] + syms[2];
        }
    };
}();
var windowsfm = function () {
    return {
        init: function (permissions, file_name) {
            var obj = permissions;
            var table_users = '<table class="table table-bordered" id="user_lists"><thead class="thead-dark"><tr><th>Users</th></tr></thead><tbody>';
            for (var i = 0; i < obj.length; i++) {
                var html = '<tr id="user_' + i + '"><td>' + obj[i]['name'] + '</td></tr>';
                table_users = table_users + html;
            }
            table_users = table_users + '</tbody></table>';
            $('.user-lists').html(table_users);
            $('div[id="user_permissions"]').html('');

            for (var k = 0; k < obj.length; k++) {
                $('div[id="user_permissions"]').append(windowsfm.createTable(k, obj[k]['name']));
                windowsfm.checkPermissions(k, obj);
            }
            $('div[id="user_permissions"] .checkbox-blue').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
            });
            windowsfm.checkedStatus();
            $('div[id="' + $('#user_lists tbody tr:first-child').attr('id') + '"]').css('display', "block");
            $('tr[id="' + $('#user_lists tbody tr:first-child').attr('id') + '"]').addClass("active");

            $('#user_lists tbody tr').on('click', function () {
                var user_id = $(this).attr('id');
                for (var i = 0; i < obj.length; i++) {
                    $('div[id="user_' + i + '"]').css('display', "none");
                    $('tr[id="user_' + i + '"]').removeClass("active");
                }
                $('div[id="' + user_id + '"]').css('display', "block");
                $('tr[id="' + user_id + '"]').addClass("active");
            });
            $("#submit_permission").unbind("click");
            $('#submit_permission').on('click', function (e) {
                var data = {};
                data['name'] = file_name;
                data['permissions'] = windowsfm.getAllUsers();
                core.ajaxRequest({
                    method: 'POST',
                    url: "/" + filemanager.permissions_url.trimLeft("#"),
                    data: data
                }, true);
                $('#permissions_modal').modal('hide');
                $('input').iCheck("uncheck");
                $('div[id="user_permissions"] .checkbox-blue').iCheck("update");

                //e.preventDefault();
                return false;
            });
        },
        checkPermissions: function (user_id, obj) {
            var allowed_permissions = obj[user_id]['allowed'];
            var denied_permissions = obj[user_id]['denied'];
            if (obj[user_id]['disabled'] == true) {
                $('input[name$="' + user_id + '"]').prop('disabled', true);
            }

            if (core.isExists(allowed_permissions, "FullControl") == true) {
                $('input[value="allow"][name$="' + user_id + '"]').iCheck("check");
                $('input[value="deny"][name$="' + user_id + '"]').iCheck("uncheck");
            } else {
                if (core.isExists(denied_permissions, "FullControl") == true) {
                    $('input[value="deny"][name$="' + user_id + '"]').iCheck("check");
                    $('input[value="allow"][name$="' + user_id + '"]').iCheck("uncheck");
                } else {
                    if (core.isExists(allowed_permissions, "Write") == true) {
                        $('input[value="allow"][name="Write' + user_id + '"]').iCheck("check");
                        $('input[value="deny"][name="Read' + user_id + '"]').iCheck("uncheck");
                    }
                    if (core.isExists(denied_permissions, "Write") == true) {
                        $('input[value="deny"][name="Write' + user_id + '"]').iCheck("check");
                        $('input[value="allow"][name="Write' + user_id + '"]').iCheck("uncheck");
                    }
                    if (core.isExists(allowed_permissions, "Read") == true) {
                        $('input[value="allow"][name="Read' + user_id + '"]').iCheck("check");
                        $('input[value="deny"][name="Read' + user_id + '"]').iCheck("uncheck");
                    }
                    if (core.isExists(denied_permissions, "Read") == true) {
                        $('input[value="deny"][name="Read' + user_id + '"]').iCheck("check");
                        $('input[value="allow"][name="Read' + user_id + '"]').iCheck("uncheck");
                    }

                    if (core.isExists(allowed_permissions, "ReadAndExecute") == true) {
                        $('input[value="allow"][name="ReadAndExecute' + user_id + '"]').iCheck("check");
                        $('input[value="allow"][name="ListFolderContents' + user_id + '"]').iCheck("check");
                        $('input[value="allow"][name="Read' + user_id + '"]').iCheck("check");

                        $('input[value="deny"][name="ReadAndExecute' + user_id + '"]').iCheck("uncheck");
                        $('input[value="deny"][name="ListFolderContents' + user_id + '"]').iCheck("uncheck");
                        $('input[value="deny"][name="Read' + user_id + '"]').iCheck("uncheck");
                    }
                    if (core.isExists(denied_permissions, "ReadAndExecute") == true) {
                        $('input[value="deny"][name="ReadAndExecute' + user_id + '"]').iCheck("check");
                        $('input[value="deny"][name="ListFolderContents' + user_id + '"]').iCheck("check");
                        $('input[value="deny"][name="Read' + user_id + '"]').iCheck("check");

                        $('input[value="allow"][name="ReadAndExecute' + user_id + '"]').iCheck("uncheck");
                        $('input[value="allow"][name="ListFolderContents' + user_id + '"]').iCheck("uncheck");
                        $('input[value="allow"][name="Read' + user_id + '"]').iCheck("uncheck");
                    }

                    if (core.isExists(allowed_permissions, "Modify") == true) {
                        $('input[value="allow"][name="FullControl' + user_id + '"]').iCheck("uncheck");
                        $('input[value="allow"][name="Modify' + user_id + '"]').iCheck("check");
                        $('input[value="allow"][name="ReadAndExecute' + user_id + '"]').iCheck("check");
                        $('input[value="allow"][name="ListFolderContents' + user_id + '"]').iCheck("check");
                        $('input[value="allow"][name="Write' + user_id + '"]').iCheck("check");
                        $('input[value="allow"][name="Read' + user_id + '"]').iCheck("check");

                        $('input[value="deny"][name="FullControl' + user_id + '"]').iCheck("uncheck");
                        $('input[value="deny"][name="Modify' + user_id + '"]').iCheck("uncheck");
                        $('input[value="deny"][name="ReadAndExecute' + user_id + '"]').iCheck("uncheck");
                        $('input[value="deny"][name="ListFolderContents' + user_id + '"]').iCheck("uncheck");
                        $('input[value="deny"][name="Write' + user_id + '"]').iCheck("uncheck");
                        $('input[value="deny"][name="Read' + user_id + '"]').iCheck("uncheck");
                    }
                    if (core.isExists(denied_permissions, "Modify") == true) {
                        $('input[value="deny"][name="FullControl' + user_id + '"]').iCheck("uncheck");
                        $('input[value="deny"][name="Modify' + user_id + '"]').iCheck("check");
                        $('input[value="deny"][name="ReadAndExecute' + user_id + '"]').iCheck("check");
                        $('input[value="deny"][name="ListFolderContents' + user_id + '"]').iCheck("check");
                        $('input[value="deny"][name="Write' + user_id + '"]').iCheck("check");
                        $('input[value="deny"][name="Read' + user_id + '"]').iCheck("check");

                        $('input[value="allow"][name="FullControl' + user_id + '"]').iCheck("uncheck");
                        $('input[value="allow"][name="Modify' + user_id + '"]').iCheck("uncheck");
                        $('input[value="allow"][name="ReadAndExecute' + user_id + '"]').iCheck("uncheck");
                        $('input[value="allow"][name="ListFolderContents' + user_id + '"]').iCheck("uncheck");
                        $('input[value="allow"][name="Write' + user_id + '"]').iCheck("uncheck");
                        $('input[value="allow"][name="Read' + user_id + '"]').iCheck("uncheck");
                    }
                }
            }

        },
        checkedStatus: function () {
            $('input[name^="FullControl"][value="allow"]').on('ifClicked', function (ev) {
                $('input[name^="FullControl"][value="allow"]').on('ifChecked ifUnchecked', function(event) {
                    if (event.type == 'ifChecked') {
                        var user_id = $(this).attr('name').replace('FullControl', '');
                        // $('input[name$="' + user_id + '"][value="deny"]').iCheck("uncheck");
                        var checks = {
                            1: 'Modify',
                            2: 'ReadAndExecute',
                            3: 'Read',
                            4: 'Write',
                            5: 'ListFolderContents'
                        };
                        windowsfm.check(user_id, checks, 'allow');
                        checks[0] = 'FullControl';
                        windowsfm.uncheck(user_id, checks, 'deny');
                        // $('input[name="' + user_id + '"][value="allow"]').iCheck("check");
                    }
                });
            });
            $('input[name^="FullControl"][value="deny"]').on('ifClicked', function (ev) {
                $('input[name^="FullControl"][value="deny"]').on('ifChecked ifUnchecked', function(event) {
                    if (event.type == 'ifChecked') {
                        var user_id = $(this).attr('name').replace('FullControl', '');
                        var checks = {
                            1: 'Modify',
                            2: 'ReadAndExecute',
                            3: 'Read',
                            4: 'Write',
                            5: 'ListFolderContents'
                        };
                        windowsfm.check(user_id, checks, 'deny');
                        checks[0] = 'FullControl';
                        windowsfm.uncheck(user_id, checks, 'allow');
                        // $('input[name$="' + user_id + '"][value="deny"]').iCheck("check");
                        // $('input[name$="' + user_id + '"][value="allow"]').iCheck("uncheck");
                    }
                });
            });

            $('input[name^="Modify"][value="allow"]').on('ifClicked', function (ev) {
                $('input[name^="Modify"][value="allow"]').on('ifChecked ifUnchecked', function(event) {
                    var user_id = $(this).attr('name').replace('Modify', '');
                    var checks;
                    if (event.type == 'ifChecked') {
                        checks = {
                            0: 'FullControl',
                            1: 'Modify',
                            2: 'ReadAndExecute',
                            3: 'Read',
                            4: 'Write',
                            5: 'ListFolderContents'
                        };
                        windowsfm.uncheck(user_id, checks, 'deny');
                        checks = {0: 'Modify', 1: 'ReadAndExecute', 2: 'Read', 3: 'Write', 4: 'ListFolderContents'};
                        windowsfm.check(user_id, checks, 'allow');
                    } else {
                        checks = {0: 'FullControl', 1: 'Modify'};
                        windowsfm.uncheck(user_id, checks, 'allow');
                    }
                });
            });
            $('input[name^="Modify"][value="deny"]').on('ifClicked', function (ev) {
                $('input[name^="Modify"][value="deny"]').on('ifChecked ifUnchecked', function(event) {
                    var checks;
                    var user_id = $(this).attr('name').replace('Modify', '');
                    if (event.type == 'ifChecked') {
                        checks = {
                            0: 'FullControl',
                            1: 'Modify',
                            2: 'ReadAndExecute',
                            3: 'Read',
                            4: 'Write',
                            5: 'ListFolderContents'
                        };
                        windowsfm.uncheck(user_id, checks, 'allow');
                        checks = {0: 'Modify', 1: 'ReadAndExecute', 2: 'Read', 3: 'Write', 4: 'ListFolderContents'};
                        windowsfm.check(user_id, checks, 'deny');
                    } else {
                        checks = {0: 'FullControl', 1: 'Modify'};
                        windowsfm.uncheck(user_id, checks, 'deny');
                    }
                });
            });

            $('input[name^="ReadAndExecute"][value="allow"]').on('ifClicked', function (ev) {
                $('input[name^="ReadAndExecute"][value="allow"]').on('ifChecked ifUnchecked', function(event) {
                    var checks;
                    var user_id = $(this).attr('name').replace('ReadAndExecute', '');
                    if (event.type == 'ifChecked') {
                        checks = {0: 'FullControl', 1: 'Modify', 2: 'ReadAndExecute', 3: 'Read', 4: 'ListFolderContents'};
                        windowsfm.uncheck(user_id, checks, 'deny');
                        checks = {0: 'ReadAndExecute', 1: 'Read', 2: 'ListFolderContents'};
                        windowsfm.check(user_id, checks, 'allow');
                    } else {
                        checks = {0: 'ReadAndExecute', 1: 'Modify', 2: 'FullControl'};
                        windowsfm.uncheck(user_id, checks, 'allow');
                    }
                });
            });
            $('input[name^="ReadAndExecute"][value="deny"]').on('ifClicked', function (ev) {
                $('input[name^="ReadAndExecute"][value="deny"]').on('ifChecked ifUnchecked', function(event) {
                    var checks;
                    var user_id = $(this).attr('name').replace('ReadAndExecute', '');
                    if (event.type == 'ifChecked') {
                        checks = {0: 'FullControl', 1: 'Modify', 2: 'ReadAndExecute', 3: 'Read', 5: 'ListFolderContents'};
                        windowsfm.uncheck(user_id, checks, 'allow');
                        checks = {0: 'ReadAndExecute', 1: 'Read', 2: 'ListFolderContents'};
                        windowsfm.check(user_id, checks, 'deny');
                    } else {
                        checks = {0: 'ReadAndExecute', 1: 'Modify', 2: 'FullControl'};
                        windowsfm.uncheck(user_id, checks, 'deny');
                    }
                });
            });

            $('input[name^="Read"][value="allow"]').on('ifClicked', function (ev) {
                $('input[name^="Read"][value="allow"]').on('ifChecked ifUnchecked', function(event) {
                    var checks;
                    var user_id = $(this).attr('name').replace('Read', '');
                    if (event.type == 'ifChecked') {
                        checks = {1: 'ReadAndExecute', 2: 'ListFolderContents', 3: 'Read'};
                        windowsfm.uncheck(user_id, checks, 'deny');
                        checks = {1: 'Read'};
                        windowsfm.check(user_id, checks, 'allow');
                    } else {
                        checks = {1: 'Read'};
                        windowsfm.uncheck(user_id, checks, 'allow');
                    }
                });
            });
            $('input[name^="Read"][value="deny"]').on('ifClicked', function (ev) {
                $('input[name^="Read"][value="deny"]').on('ifChecked ifUnchecked', function(event) {
                    var checks;
                    var user_id = $(this).attr('name').replace('Read', '');
                    if (event.type == 'ifChecked') {
                        checks = {1: 'ReadAndExecute', 2: 'ListFolderContents', 3: 'Read'};
                        windowsfm.uncheck(user_id, checks, 'allow');
                        checks = {1: 'Read'};
                        windowsfm.check(user_id, checks, 'deny');
                    } else {
                        checks = {1: 'Read', 2: 'ReadAndExecute'};
                        windowsfm.uncheck(user_id, checks, 'deny');
                    }
                });
            });

            $('input[name^="ListFolderContents"][value="allow"]').on('ifClicked', function (ev) {
                $('input[name^="ListFolderContents"][value="allow"]').on('ifChecked ifUnchecked', function(event) {
                    var checks;
                    var user_id = $(this).attr('name').replace('ListFolderContents', '');
                    if (event.type == 'ifChecked') {
                        checks = {0: 'FullControl', 1: 'Modify', 2: 'ReadAndExecute', 3: 'Read', 5: 'ListFolderContents'};
                        windowsfm.uncheck(user_id, checks, 'deny');
                        checks = {0: 'ListFolderContents'};
                        windowsfm.check(user_id, checks, 'allow');
                    } else {
                        checks = {0: 'ListFolderContents', 1: 'ReadAndExecute'};
                        windowsfm.uncheck(user_id, checks, 'allow');
                    }
                });
            });
            $('input[name^="ListFolderContents"][value="deny"]').on('ifClicked', function (ev) {
                $('input[name^="ListFolderContents"][value="deny"]').on('ifChecked ifUnchecked', function(event) {
                    var checks;
                    var user_id = $(this).attr('name').replace('ListFolderContents', '');
                    if (event.type == 'ifChecked') {
                        checks = {0: 'FullControl', 1: 'Modify', 2: 'ReadAndExecute', 3: 'Read', 5: 'ListFolderContents'};
                        windowsfm.uncheck(user_id, checks, 'allow');
                        checks = {0: 'ListFolderContents'};
                        windowsfm.check(user_id, checks, 'deny');
                    } else {
                        checks = {0: 'ListFolderContents', 1: 'ReadAndExecute'};
                        windowsfm.uncheck(user_id, checks, 'deny');
                    }
                });
            });

            $('input[name^="Write"][value="allow"]').on('ifClicked', function (ev) {
                $('input[name^="Write"][value="allow"]').on('ifChecked ifUnchecked', function(event) {
                    var checks;
                    var user_id = $(this).attr('name').replace('Write', '');
                    if (event.type == 'ifChecked') {
                        checks = {0: 'FullControl', 1: 'Modify', 2: 'Write'};
                        windowsfm.uncheck(user_id, checks, 'deny');
                        checks = {0: 'Write'};
                        windowsfm.check(user_id, checks, 'allow');
                    } else {
                        checks = {0: 'FullControl', 1: 'Modify', 2: 'Write'};
                        windowsfm.uncheck(user_id, checks, 'deny');
                    }
                });
            });
            $('input[name^="Write"][value="deny"]').on('ifClicked', function (ev) {
                $('input[name^="Write"][value="deny"]').on('ifChecked ifUnchecked', function(event) {
                    var checks;
                    var user_id = $(this).attr('name').replace('Write', '');
                    if (event.type == 'ifChecked') {
                        checks = {0: 'FullControl', 1: 'Modify', 2: 'Write'};
                        windowsfm.uncheck(user_id, checks, 'allow');
                        checks = {0: 'Write'};
                        windowsfm.check(user_id, checks, 'deny');
                    } else {
                        checks = {0: 'FullControl', 1: 'Modify', 2: 'Write'};
                        windowsfm.uncheck(user_id, checks, 'allow');
                    }
                });
            });
        },
        createTable: function (user_id, username) {
            return '<div style="display:none" id="user_' + user_id + '"><table class="table table-bordered">' +
                '<thead class="thead-dark">' +
                '<tr>' +
                '<th width="80%" id="perm_title">Permissions for ' + username + '</th>' +
                '<th class="text-center">Allow</th>' +
                '<th class="text-center">Deny</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>' +
                '<tr>' +
                '<td>Full Control</td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="allow" name="FullControl' + user_id + '" class="checkbox" ></td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="deny" name="FullControl' + user_id + '" class="checkbox" ></td>' +
                '</tr>' +
                '<tr>' +
                '<td>Modify</td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="allow" name="Modify' + user_id + '" class="checkbox" ></td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="deny" name="Modify' + user_id + '" class="checkbox" ></td>' +
                '</tr>' +
                '<tr>' +
                '<td>Read &amp; Execute</td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="allow" name="ReadAndExecute' + user_id + '" class="checkbox" ></td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="deny" name="ReadAndExecute' + user_id + '" class="checkbox" ></td>' +
                '</tr>' +
                '<tr>' +
                '<td>Read</td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="allow" name="Read' + user_id + '" class="checkbox" ></td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="deny" name="Read' + user_id + '" class="checkbox" ></td>' +
                '</tr>' +
                '<tr>' +
                '<td>Write</td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="allow" name="Write' + user_id + '" class="checkbox" ></td>' +
                '<td align="center"><input type="checkbox" class="checkbox-blue" value="deny" name="Write' + user_id + '" class="checkbox" ></td>' +
                '</tr>' +
                '</tbody>' +
                '</table></div>';
        },
        uncheck: function (id, inputs, type) {
            for (var i in inputs) {
                $('input[name="' + inputs[i] + id + '"][value="' + type + '"]').iCheck("uncheck");
            }
        },
        check: function (id, inputs, type) {
            for (var i in inputs) {
                $('input[name="' + inputs[i] + id + '"][value="' + type + '"]').iCheck("check");
            }
        },
        getAllUsers: function () {
            var allUserElements = $('tr[id^="user_"]');
            var idu;
            var allIds = {};
            for (var i = 0, n = allUserElements.length; i < n; ++i) {
                var elu = allUserElements[i];
                idu = $(elu)[0].innerHTML;
                var user = $(idu + '> td');
                var name = $(user)[0].innerHTML;
                var data_perm = {};

                $('#user_' + i + ' input:checked').each(function () {
                    data_perm[$(this).attr('name').replace(i, '')] = $(this).val();
                });
                allIds[i] = {name: name, permission: data_perm};
            }

            return allIds;
        }
    };
}();
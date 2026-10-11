var permission_manager;
var filemanager = function() {
    return {
        init: function () {
            $('#upload_folder_lists').click(function (e) {
                var id = $('#upload_folder_lists').jstree(true).get_selected();
                var data_url = $('#' + id[0]).attr('data-url');
                $('#upload_folder').val(data_url);
                e.preventDefault();
            });
            $('#folder_lists').on('click',function (e) {
                var id = $('#folder_lists').jstree(true).get_selected();
                var data_url = $('#' + id[0]).attr('data-url');
                $('.fileList').attr('onclick','getFileLists("'+ data_url +'");');
                filemanager.filelist(data_url);
                e.preventDefault();

            });
            filemanager.context();
            if(server_type == "linux")
                permission_manager = linuxfm;
            else
                permission_manager = windowsfm;
        },
        initFiles: function () {
            var table_id;
            table_id = $('.table-responsive table').attr('id');
            filemanager.filelist(table_id);
        },
        folderlist: function (target) {
            $(target).jstree({
                "core" : {
                    "themes" : {
                        "responsive": true
                    },
                    'check_callback' : true,
                    'data' : ''

                },
                "types" : {
                    "default" : {
                        "icon" : "fa fa-folder fa-2x text-primary"
                    },
                    "file" : {
                        "icon" : "fa fa-file fa-2x text-primary"
                    }
                },
                'plugins' : ['state','dnd','sort','types','wholerow']
            });

            $.ajax({
                type: "POST",
                cache: false,
                url: index_url,
                success: function(res) {
                    $(target).jstree(true).settings.core.data = JSON.parse(res);
                    $(target).jstree(true).refresh();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                }
            });
        },
        filelist: function (data_folder) {
            var el = $('#file-lists');
            el.html('<div class="folder-loading"><i class="fa fa-spin fa-spinner"></i><br>Loading...</div>');
            var data = {};
            data['folder'] = data_folder;
            var obj = core.ajaxRequest({
                method: 'POST',
                url: file_manager_url,
                data:data
            });
            var output = '<div class="table-responsive"><table id="'+ data_folder +'" class="table" style="cursor: pointer">';
            var thead = '<thead><tr>'+
                '<th>'+ lvar['name'] +'</th>'+
                '<th>'+ lvar['modified'] +'</th>'+
                '<th>'+ lvar['size'] +'</th>'+
                '</tr></thead><tbody>';
            output = output + thead;
            var files = obj['files'];

            var first_row = '<tr data-name="' + obj['parent'] + '" id="folder"><td class="unselectable" id="folder" data-type="file"><i class="fa fa-2x fa-reply text-primary"></i> ..</td><td></td><td></td></tr>';

            output = output + first_row;
            var arrayLength = files.length;
            var data_type;
            var download_link = '';
            for (var i = 0; i < arrayLength; i++) {
                data_type = "folder";
                if(files[i]['mime'] != "folder") {
                    data_type = "file";
                    download_link = ' data-download="'+ files[i]['data-download'] +'"';
                }

                output += '<tr data-name="'+ files[i]['path'] +'" data-clear-name="'+ files[i]['clear-name'] +'"' +
                    download_link + 'id="'+ data_type + '">' +
                    '<td data-title="'+ files[i]['id'] +'"><i class="fa fa-2x fa-'+ files[i]['mime'] +' text-primary"></i> ' + files[i]['file_name'] + '</td>' +
                    '<td>' + files[i]['modified_date'] + '</td>' +
                    '<td>' + files[i]['size'] + '</td>';
            }
            output += '</tbody></table></div>';
            el.html(output);
            $('#file-lists table tbody tr').on('contextmenu', function() {
                if(!$(this).hasClass('unselectable')) {
                    filemanager.changeClass(this);
                }
            });
            $('#file-lists table tbody').on('dblclick','tr', function() {
                if($(this).attr('id') == "folder") {
                    filemanager.filelist($(this).attr('data-name'));
                }
            });

            $('#file-lists table tbody').on('click', 'tr', function() {
                filemanager.changeClass(this);
            });


            $('.tooltip').remove();
        },
        getFileContent: function (url,data) {
            var obj = core.ajaxRequest({
                method: 'POST',
                url: url,
                data: data
            });

            if (obj['type'] == 'success') {
                var content = obj.content;
                $('#file_editor_textarea').val('');
                $('#file_editor_textarea').val(content['content']);
                $('#mode option:contains(' + content['type'] + ')').prop({selected: true});
                filemanager.editor(content['type'],data['file']);
                $('#file_edit_modal').modal('show');
            }
            else {
                toastr["error"](obj['content'],obj['title']);
            }
        },
        createFile: function () {
            var table_id;
            table_id = $('#file-lists table').attr('id');


            $('#file_folder_modal').modal();
            $('#file_folder_modal .modal-header h3').text(lvar['create_file']);
            $('#file_folder_modal .modal-body label').text(lvar['new_name']);

            $("#file_folder_modal #submit_edit_file_folder").unbind("click");
            $('#file_folder_modal #submit_edit_file_folder').bind('click',function (e) {
                var data = {};
                data['name'] = $('#new_name').val();
                data['current_folder'] = table_id;
                var result = core.ajaxRequest({
                    method: 'POST',
                    url: create_file_url,
                    data: data
                },true);

                if(result['type'] == "success") {
                    filemanager.folderlist('#folder_lists');
                    filemanager.initFiles();
                    $('#file_folder_modal').modal("hide");
                }
                $('#file_folder_modal .modal-header h3').text("[title]");
                $('#file_folder_modal .modal-body label').text("[label]");
            });
        },
        createFolder: function () {
            var table_id;
            table_id = $('#file-lists table').attr('id');
            $('#file_folder_modal .modal-header h3').text(lvar['create_folder']);
            $('#file_folder_modal .modal-body label').text(lvar['new_name']);
            $('#file_folder_modal').modal();

            $("#file_folder_modal #submit_edit_file_folder").unbind("click");
            $('#file_folder_modal #submit_edit_file_folder').bind('click',function (e) {
                var data = {};
                data['name'] = $('#new_name').val();
                data['current_folder'] = table_id;
                var result = core.ajaxRequest({
                    method: 'POST',
                    url: create_folder_url,
                    data: data
                },true);

                if(result['type'] == "success") {
                    filemanager.folderlist('#folder_lists');
                    filemanager.initFiles();
                    $('#file_folder_modal').modal("hide");
                    $('#file_folder_modal .modal-header h3').text("[title]");
                    $('#file_folder_modal .modal-body label').text("[label]");
                }
            });

        },
        edit: function (name) {
            var data = {};
            data['file'] = name;
            filemanager.initFiles();
            filemanager.getFileContent(edit_file_url,data);
            var window_height = $(window).height();
            //$('.CodeMirror').css('min-height',window_height - 200);
            $('#file_edit_modal').on('hidden.bs.modal', function () {
                $('.file_edit').html('<textarea id="file_editor_textarea"></textarea>')
            });
        },
        rename: function (clear_name,file_name) {
            $('#file_folder_modal .modal-header h3').text(lvar['rename_file']);
            $('#file_folder_modal .modal-body label').text(lvar['new_name']);

            $('#file_folder_modal').modal();
            $('#file_folder_modal input#new_name').val(clear_name);

            $("#file_folder_modal #submit_edit_file_folder").unbind("click");
            $('#file_folder_modal #submit_edit_file_folder').bind('click',function (e) {
                var data = {};
                data['new_name'] = $('#new_name').val();
                data['name'] = file_name;
                var result = core.ajaxRequest({
                    method: 'POST',
                    url: rename_url,
                    data: data
                },true);

                if(result['type'] == "success") {
                    filemanager.folderlist('#folder_lists');
                    filemanager.initFiles();
                    $('#file_folder_modal').modal("hide");
                    $('#file_folder_modal .modal-header h3').text("[title]");
                    $('#file_folder_modal .modal-body label').text("[label]");
                }
            });

        },
        move: function (name) {
            $('#folder_lists_modal').modal();
            filemanager.folderlist('#move_folders');
            $("#submit_move").unbind("click");
            $('#submit_move').bind('click',function (e) {

                var id = $('#move_folders').jstree(true).get_selected();
                var data_url = $('#' + id[0]).attr('data-url');
                var data = {};
                data['target'] = data_url;
                data['source'] = name;
                var result = core.ajaxRequest({
                    method: 'POST',
                    url: move_url,
                    data: data
                },true);

                if(result['type'] == "success") {
                    filemanager.initFiles();
                    $('#folder_lists_modal').modal("hide");
                }
            });
        },
        copy: function (name) {
            $.cookie('copied_file', name, { expires: 86400 * 365 });
            //toastr["success"]('File copied temporarily','Success');
        },
        paste: function (name) {
            bootbox.dialog({
                title: lvar['confirm'],
                message: lvar['are_you_sure'],
                buttons: {
                    cancel: {
                        label: lvar['cancel'],
                        className: "btn btn-default btn-sm",
                        callback: function() {
                        }
                    },
                    submit: {
                        label: lvar['submit'],
                        className: "btn btn-primary btn-sm",
                        callback: function() {
                            var copied_file = $.cookie("copied_file");
                            var data = {};
                            data['target'] = name;
                            data['source'] = copied_file;
                            core.ajaxRequest({
                                method: 'POST',
                                url: paste_url,
                                data: data
                            });
                            filemanager.initFiles();
                        }
                    }
                }
            });
        },
        delete: function (name) {
            bootbox.dialog({
                title: lvar['confirm'],
                message: lvar['are_you_sure'],
                buttons: {
                    cancel: {
                        label: lvar['cancel'],
                        className: "btn btn-default btn-sm",
                        callback: function() {
                        }
                    },
                    submit: {
                        label: lvar['submit'],
                        className: "btn btn-primary btn-sm",
                        callback: function() {
                            var data = {};
                            data['file_name'] = name;
                            core.ajaxRequest({
                                method: 'POST',
                                url: delete_url,
                                data: data
                            },true);
                            filemanager.initFiles();
                            filemanager.folderlist('#folder_lists');
                        }
                    }
                }
            });
        },
        editor: function (type,name) {
            $('#file_edit_modal').on('shown.bs.modal', function() {
                var editor = CodeMirror.fromTextArea(document.getElementById("file_editor_textarea"), {
                    lineNumbers: true,
                    styleActiveLine: true,
                    mode: "text/html",
                    theme: "monokai"

                });
                $('#submit_save_file').unbind();
                $('#submit_save_file').bind('click', function () {
                    var data = {};
                    data['content'] = editor.getValue();
                    data['name'] = name;
                    core.ajaxRequest({
                        method: 'POST',
                        url: save_file_url,
                        data: data
                    },true);
                });
            });

        },
        download: function (url) {
            window.open(url,'_blank');
        },
        getPermissions: function (name,url) {
            var data = {};
            data['name'] = name;
            return core.ajaxRequest({
                method: 'POST',
                url: url,
                data: data
            });
        },
        permissionFile: function (name) {
            var permissions = filemanager.getPermissions(name,get_permissions_url);
            permission_manager.init(permissions,name);
            $('#permissions_modal').modal();

        },
        changeClass: function (element) {
            $(element).closest("tr").siblings().removeClass("active");
            $(element).toggleClass("active");
        },
        context: function () {
            $.contextMenu({
                selector: '#folder',
                callback: function(key, options) {
                    var element = options["$trigger"][0];
                    var clear_name = element['dataset']['clearName'];
                    var file_name = element['dataset']['name'];
                    //var url = element['dataset']['download'];
                    switch (key) {
                        case "rename":
                            filemanager.rename(clear_name,file_name);
                            break;
                        case "move":
                            filemanager.move(file_name);
                            break;
                        case "copy":
                            filemanager.copy(file_name);
                            break;
                        case "paste":
                            filemanager.paste(file_name);
                            break;
                        case "delete":
                            filemanager.delete(file_name);
                            break;
                        case "createfolder":
                            filemanager.createFolder(file_name);
                            break;
                        case "createfile":
                            filemanager.createFile(file_name);
                            break;
                        case "permission":
                            filemanager.permissionFile(file_name);
                            break;
                    }
                },
                items: {
                    "rename": {name: "Rename Folder", icon: "rename"},
                    "move": {name: "Cut", icon: "cut"},
                    "copy": {name: "Copy", icon: "copy"},
                    "paste": {name: "Paste", icon: "paste"},
                    "delete": {name: "Delete", icon: "delete"},
                    "createfolder": {name: "Create Folder", icon: "create-folder"},
                    "createfile": {name: "Create File", icon: "create-file"},
                    "sep1": "---------",
                    "permission": {name: "Permissions", icon: "shield"}
                }
            });

            $.contextMenu({
                className: 'css-title',
                selector: '#file',
                callback: function(key, options) {
                    var element = options["$trigger"][0];

                    var clear_name = element['dataset']['clearName'];
                    var file_name = element['dataset']['name'];
                    var url = element['dataset']['download'];
                    switch (key) {
                        case "rename":
                            filemanager.rename(clear_name,file_name);
                            break;
                        case "move":
                            filemanager.move(file_name);
                            break;
                        case "edit":
                            filemanager.edit(file_name);
                            break;
                        case "copy":
                            filemanager.copy(file_name);
                            break;
                        case "delete":
                            filemanager.delete(file_name);
                            break;
                        case "createfolder":
                            filemanager.createFolder(file_name);
                            break;
                        case "createfile":
                            filemanager.createFile(file_name);
                            break;
                        case "download":
                            filemanager.download(url);
                            break;
                        case "permission":
                            filemanager.permissionFile(file_name);
                            break;
                    }
                },
                items: {
                    "rename": {name: "Rename File", icon: "rename"},
                    "move": {name: "Move", icon: "move"},
                    "copy": {name: "Copy", icon: "copy"},
                    "paste": {name: "Paste", icon: "paste"},
                    "edit": {name: "Edit File", icon: "edit"},
                    "delete": {name: "Delete", icon: "delete"},
                    "createfile": {name: "Create File", icon: "create-file"},
                    "createfolder": {name: "Create Folder", icon: "create-folder"},
                    "download": {name: "Download", icon: "download"},
                    "sep1": "---------",
                    "permission": {name: "Permissions", icon: "permissions"}
                }
            });
        }
    };
}();


var linuxfm = function () {
    return {
        init: function (permissions,file_name) {
            $('input[name="t_total"]').val(permissions);
            linuxfm.octalchange();

            $("#submit_permission").unbind("click");
            $('#submit_permission').on('click',function () {
                var data = {};
                data['permission'] = $('input[name="t_total"]').val();
                data['name'] = file_name;
                ajaxPost(permissions_url,data);
                filemanager.initFiles();
                $('#permissions_modal').modal('hide');
                //e.preventDefault();
                return false;
            });
            window.onload=octalchange;
        },
        octalchange: function () {
            var val = document.chmod.t_total.value;
            var ownerbin = parseInt(val.charAt(0)).toString(2);
            while (ownerbin.length<3) { ownerbin="0"+ownerbin; }
            var groupbin = parseInt(val.charAt(1)).toString(2);
            while (groupbin.length<3) { groupbin="0"+groupbin; }
            var otherbin = parseInt(val.charAt(2)).toString(2);
            while (otherbin.length<3) { otherbin="0"+otherbin; }
            document.chmod.owner4.checked = parseInt(ownerbin.charAt(0));
            document.chmod.owner2.checked = parseInt(ownerbin.charAt(1));
            document.chmod.owner1.checked = parseInt(ownerbin.charAt(2));
            document.chmod.group4.checked = parseInt(groupbin.charAt(0));
            document.chmod.group2.checked = parseInt(groupbin.charAt(1));
            document.chmod.group1.checked = parseInt(groupbin.charAt(2));
            document.chmod.other4.checked = parseInt(otherbin.charAt(0));
            document.chmod.other2.checked = parseInt(otherbin.charAt(1));
            document.chmod.other1.checked = parseInt(otherbin.charAt(2));
            linuxfm.calcChMod(1);
        },
        calcChMod: function (nototals) {
            var users = new Array("owner", "group", "other");
            var totals = new Array("","","");
            var syms = new Array("","","");

            for (var i=0; i<users.length; i++)
            {
                var user=users[i];
                var field4 = user + "4";
                var field2 = user + "2";
                var field1 = user + "1";
                //var total = "t_" + user;
                var symbolic = "sym_" + user;
                var number = 0;
                var sym_string = "";

                if (document.chmod[field4].checked == true) { number += 4; }
                if (document.chmod[field2].checked == true) { number += 2; }
                if (document.chmod[field1].checked == true) { number += 1; }

                if (document.chmod[field4].checked == true) {
                    sym_string += "r";
                } else {
                    sym_string += "-";
                }
                if (document.chmod[field2].checked == true) {
                    sym_string += "w";
                } else {
                    sym_string += "-";
                }
                if (document.chmod[field1].checked == true) {
                    sym_string += "x";
                } else {
                    sym_string += "-";
                }

                //if (number == 0) { number = ""; }
                //document.chmod[total].value =
                totals[i] = totals[i]+number;
                syms[i] =  syms[i]+sym_string;

            }
            if (!nototals) document.chmod.t_total.value = totals[0] + totals[1] + totals[2];
            document.chmod.sym_total.value = "-" + syms[0] + syms[1] + syms[2];
        }

    };
}();

var windowsfm = function () {
    return {
        init: function (permissions,file_name) {
            var obj = permissions;
            var table_users = '<thead><tr><th>Users</th></tr></thead><tbody>';
            for (var i = 0; i < obj.length; i++) {
                var html = '<tr id="user_'+ i +'"><td>'+ obj[i]['name'] +'</td></tr>';
                table_users = table_users + html;
            }
            table_users = table_users + '</tbody>';
            $('#user_lists').html(table_users);
            $('div[id="user_permissions"]').html('');

            for (var k = 0; k < obj.length; k++) {
                $('div[id="user_permissions"]').append(windowsfm.createTable(k,obj[k]['name']));
                windowsfm.checkPermissions(k,obj);
            }
            windowsfm.checkedStatus();
            $('div[id="'+ $('#user_lists tbody tr:first-child').attr('id') +'"]').css('display',"block");
            $('tr[id="'+ $('#user_lists tbody tr:first-child').attr('id') +'"]').addClass("active");

            $('#user_lists tbody tr').on('click', function () {
                var user_id = $(this).attr('id');
                for (var i = 0; i < obj.length; i++) {
                    $('div[id="user_' + i +'"]').css('display',"none");
                    $('tr[id="user_' + i +'"]').removeClass("active");
                }
                $('div[id="'+ user_id +'"]').css('display',"block");
                $('tr[id="'+ user_id +'"]').addClass("active");
            });

            $("#submit_permission").unbind("click");
            $('#submit_permission').on('click',function (e) {
                var data = {};
                data['name'] = file_name;
                data['permissions'] = windowsfm.getAllUsers();
                core.ajaxRequest({
                    method: 'POST',
                    url: permissions_url,
                    data: data
                },true);
                $('#permissions_modal').modal('hide');
                $('input').prop('checked',false);
                //e.preventDefault();
                return false;
            });
        },
        checkPermissions: function (user_id,obj) {
            var allowed_permissions = obj[user_id]['allowed'];
            var denied_permissions = obj[user_id]['denied'];
            if(obj[user_id]['disabled'] == true) {
                $('input[name$="'+ user_id +'"]').prop('disabled',true);
            }

            if(core.isExists(allowed_permissions,"FullControl") == true) {
                $('input[value="allow"][name$="'+ user_id +'"]').prop('checked',true);
                $('input[value="deny"][name$="'+ user_id +'"]').prop('checked',false);
            }
            else {
                if(core.isExists(denied_permissions,"FullControl") == true) {
                    $('input[value="deny"][name$="'+ user_id +'"]').prop('checked',true);
                    $('input[value="allow"][name$="'+ user_id +'"]').prop('checked',false);
                }
                else {
                    if(core.isExists(allowed_permissions,"Write") == true) {
                        $('input[value="allow"][name="Write'+ user_id +'"]').prop('checked',true);
                        $('input[value="deny"][name="Read'+ user_id +'"]').prop('checked',false);
                    }
                    if(core.isExists(denied_permissions,"Write") == true) {
                        $('input[value="deny"][name="Write'+ user_id +'"]').prop('checked',true);
                        $('input[value="allow"][name="Write'+ user_id +'"]').prop('checked',false);
                    }
                    if(core.isExists(allowed_permissions,"Read") == true) {
                        $('input[value="allow"][name="Read'+ user_id +'"]').prop('checked',true);
                        $('input[value="deny"][name="Read'+ user_id +'"]').prop('checked',false);
                    }
                    if(core.isExists(denied_permissions,"Read") == true) {
                        $('input[value="deny"][name="Read'+ user_id +'"]').prop('checked',true);
                        $('input[value="allow"][name="Read'+ user_id +'"]').prop('checked',false);
                    }

                    if(core.isExists(allowed_permissions,"ReadAndExecute") == true) {
                        $('input[value="allow"][name="ReadAndExecute'+ user_id +'"]').prop('checked',true);
                        $('input[value="allow"][name="ListFolderContents'+ user_id +'"]').prop('checked',true);
                        $('input[value="allow"][name="Read'+ user_id +'"]').prop('checked',true);

                        $('input[value="deny"][name="ReadAndExecute'+ user_id +'"]').prop('checked',false);
                        $('input[value="deny"][name="ListFolderContents'+ user_id +'"]').prop('checked',false);
                        $('input[value="deny"][name="Read'+ user_id +'"]').prop('checked',false);
                    }
                    if(core.isExists(denied_permissions,"ReadAndExecute") == true) {
                        $('input[value="deny"][name="ReadAndExecute'+ user_id +'"]').prop('checked',true);
                        $('input[value="deny"][name="ListFolderContents'+ user_id +'"]').prop('checked',true);
                        $('input[value="deny"][name="Read'+ user_id +'"]').prop('checked',true);

                        $('input[value="allow"][name="ReadAndExecute'+ user_id +'"]').prop('checked',false);
                        $('input[value="allow"][name="ListFolderContents'+ user_id +'"]').prop('checked',false);
                        $('input[value="allow"][name="Read'+ user_id +'"]').prop('checked',false);
                    }

                    if(core.isExists(allowed_permissions,"Modify") == true) {
                        $('input[value="allow"][name="FullControl'+ user_id +'"]').prop('checked',false);
                        $('input[value="allow"][name="Modify'+ user_id +'"]').prop('checked',true);
                        $('input[value="allow"][name="ReadAndExecute'+ user_id +'"]').prop('checked',true);
                        $('input[value="allow"][name="ListFolderContents'+ user_id +'"]').prop('checked',true);
                        $('input[value="allow"][name="Write'+ user_id +'"]').prop('checked',true);
                        $('input[value="allow"][name="Read'+ user_id +'"]').prop('checked',true);

                        $('input[value="deny"][name="FullControl'+ user_id +'"]').prop('checked',false);
                        $('input[value="deny"][name="Modify'+ user_id +'"]').prop('checked',false);
                        $('input[value="deny"][name="ReadAndExecute'+ user_id +'"]').prop('checked',false);
                        $('input[value="deny"][name="ListFolderContents'+ user_id +'"]').prop('checked',false);
                        $('input[value="deny"][name="Write'+ user_id +'"]').prop('checked',false);
                        $('input[value="deny"][name="Read'+ user_id +'"]').prop('checked',false);
                    }
                    if(core.isExists(denied_permissions,"Modify") == true) {
                        $('input[value="deny"][name="FullControl'+ user_id +'"]').prop('checked',false);
                        $('input[value="deny"][name="Modify'+ user_id +'"]').prop('checked',true);
                        $('input[value="deny"][name="ReadAndExecute'+ user_id +'"]').prop('checked',true);
                        $('input[value="deny"][name="ListFolderContents'+ user_id +'"]').prop('checked',true);
                        $('input[value="deny"][name="Write'+ user_id +'"]').prop('checked',true);
                        $('input[value="deny"][name="Read'+ user_id +'"]').prop('checked',true);

                        $('input[value="allow"][name="FullControl'+ user_id +'"]').prop('checked',false);
                        $('input[value="allow"][name="Modify'+ user_id +'"]').prop('checked',false);
                        $('input[value="allow"][name="ReadAndExecute'+ user_id +'"]').prop('checked',false);
                        $('input[value="allow"][name="ListFolderContents'+ user_id +'"]').prop('checked',false);
                        $('input[value="allow"][name="Write'+ user_id +'"]').prop('checked',false);
                        $('input[value="allow"][name="Read'+ user_id +'"]').prop('checked',false);
                    }
                }

            }
        },
        checkedStatus: function () {
            $('input[name^="FullControl"][value="allow"]').bind('click', function () {
                if ($(this).is(':checked')) {
                    var user_id = $(this).attr('name').replace('FullControl','');
                    $('input[name$="'+ user_id +'"][value="deny"]').prop('checked',false);
                    $('input[name$="'+ user_id +'"][value="allow"]').prop('checked',true);
                }
            });
            $('input[name^="FullControl"][value="deny"]').bind('click', function () {
                if ($(this).is(':checked')) {
                    var user_id = $(this).attr('name').replace('FullControl','');
                    $('input[name$="'+ user_id +'"][value="deny"]').prop('checked',true);
                    $('input[name$="'+ user_id +'"][value="allow"]').prop('checked',false);
                }
            });

            $('input[name^="Modify"][value="allow"]').bind('click', function () {
                var user_id = $(this).attr('name').replace('Modify','');
                var checks;
                if ($(this).is(':checked')) {
                    checks = {0:'FullControl',1:'Modify',2:'ReadAndExecute',3:'Read',4:'Write',5:'ListFolderContents'};
                    windowsfm.uncheck(user_id,checks,'deny');
                    checks = {0:'Modify',1:'ReadAndExecute',2:'Read',3:'Write',4:'ListFolderContents'};
                    windowsfm.check(user_id,checks,'allow');
                }
                else {
                    checks = {0:'FullControl',1:'Modify'};
                    windowsfm.uncheck(user_id,checks,'allow');
                }
            });
            $('input[name^="Modify"][value="deny"]').bind('click', function () {
                var checks;
                var user_id = $(this).attr('name').replace('Modify','');
                if ($(this).is(':checked')) {
                    checks = {0:'FullControl',1:'Modify',2:'ReadAndExecute',3:'Read',4:'Write',5:'ListFolderContents'};
                    windowsfm.uncheck(user_id,checks,'allow');
                    checks = {0:'Modify',1:'ReadAndExecute',2:'Read',3:'Write',4:'ListFolderContents'};
                    windowsfm.check(user_id,checks,'deny');
                }
                else {
                    checks = {0:'FullControl',1:'Modify'};
                    windowsfm.uncheck(user_id,checks,'deny');
                }
            });

            $('input[name^="ReadAndExecute"][value="allow"]').bind('click', function () {
                var checks;
                var user_id = $(this).attr('name').replace('ReadAndExecute','');
                if ($(this).is(':checked')) {
                    checks = {0:'FullControl',1:'Modify',2:'ReadAndExecute',3:'Read',4:'ListFolderContents'};
                    windowsfm.uncheck(user_id,checks,'deny');
                    checks = {0:'ReadAndExecute',1:'Read',2:'ListFolderContents'};
                    windowsfm.check(user_id,checks,'allow');
                }
                else {
                    checks = {0:'ReadAndExecute',1:'Modify',2:'FullControl'};
                    windowsfm.uncheck(user_id,checks,'allow');
                }
            });
            $('input[name^="ReadAndExecute"][value="deny"]').bind('click', function () {
                var checks;
                var user_id = $(this).attr('name').replace('ReadAndExecute','');
                if ($(this).is(':checked')) {
                    checks = {0:'FullControl',1:'Modify',2:'ReadAndExecute',3:'Read',5:'ListFolderContents'};
                    windowsfm.uncheck(user_id,checks,'allow');
                    checks = {0:'ReadAndExecute',1:'Read',2:'ListFolderContents'};
                    windowsfm.check(user_id,checks,'deny');
                }
                else {
                    checks = {0:'ReadAndExecute',1:'Modify',2:'FullControl'};
                    windowsfm.uncheck(user_id,checks,'deny');
                }
            });

            $('input[name^="Read"][value="allow"]').bind('click', function () {
                var checks;
                var user_id = $(this).attr('name').replace('Read','');
                if ($(this).is(':checked')) {
                    checks = {1:'ReadAndExecute',2:'ListFolderContents',3:'Read'};
                    windowsfm.uncheck(user_id,checks,'deny');
                    checks = {1:'Read'};
                    windowsfm.check(user_id,checks,'allow');
                }
                else {
                    checks = {1:'Read'};
                    windowsfm.uncheck(user_id,checks,'allow');
                }
            });
            $('input[name^="Read"][value="deny"]').bind('click', function () {
                var checks;
                var user_id = $(this).attr('name').replace('Read','');
                if ($(this).is(':checked')) {
                    checks = {1:'ReadAndExecute',2:'ListFolderContents',3:'Read'};
                    windowsfm.uncheck(user_id,checks,'allow');
                    checks = {1:'Read'};
                    windowsfm.check(user_id,checks,'deny');
                }
                else {
                    checks = {1:'Read',2:'ReadAndExecute'};
                    windowsfm.uncheck(user_id,checks,'deny');
                }
            });

            $('input[name^="ListFolderContents"][value="allow"]').bind('click', function () {
                var checks;
                var user_id = $(this).attr('name').replace('ListFolderContents','');
                if ($(this).is(':checked')) {
                    checks = {0:'FullControl',1:'Modify',2:'ReadAndExecute',3:'Read',5:'ListFolderContents'};
                    windowsfm.uncheck(user_id,checks,'deny');
                    checks = {0:'ListFolderContents'};
                    windowsfm.check(user_id,checks,'allow');
                }
                else {
                    checks = {0:'ListFolderContents',1:'ReadAndExecute'};
                    windowsfm.uncheck(user_id,checks,'allow');
                }
            });
            $('input[name^="ListFolderContents"][value="deny"]').bind('click', function () {
                var checks;
                var user_id = $(this).attr('name').replace('ListFolderContents','');
                if ($(this).is(':checked')) {
                    checks = {0:'FullControl',1:'Modify',2:'ReadAndExecute',3:'Read',5:'ListFolderContents'};
                    windowsfm.uncheck(user_id,checks,'allow');
                    checks = {0:'ListFolderContents'};
                    windowsfm.check(user_id,checks,'deny');
                }
                else {
                    checks = {0:'ListFolderContents',1:'ReadAndExecute'};
                    windowsfm.uncheck(user_id,checks,'deny');
                }
            });

            $('input[name^="Write"][value="allow"]').bind('click', function () {
                var checks;
                var user_id = $(this).attr('name').replace('Write','');
                if ($(this).is(':checked')) {
                    checks = {0:'FullControl',1:'Modify',2:'Write'};
                    windowsfm.uncheck(user_id,checks,'deny');
                    checks = {0:'Write'};
                    windowsfm.check(user_id,checks,'allow');
                }
                else {
                    checks = {0:'FullControl',1:'Modify',2:'Write'};
                    windowsfm.uncheck(user_id,checks,'deny');
                }
            });
            $('input[name^="Write"][value="deny"]').bind('click', function () {
                var checks;
                var user_id = $(this).attr('name').replace('Write','');
                if ($(this).is(':checked')) {
                    checks = {0:'FullControl',1:'Modify',2:'Write'};
                    windowsfm.uncheck(user_id,checks,'allow');
                    checks = {0:'Write'};
                    windowsfm.check(user_id,checks,'deny');
                }
                else {
                    checks = {0:'FullControl',1:'Modify',2:'Write'};
                    windowsfm.uncheck(user_id,checks,'allow');
                }
            });
        },
        createTable: function (user_id,username) {
            return '<div style="display:none" id="user_'+ user_id +'"><table class="table table-bordered">' +
                '<thead>' +
                '<tr>' +
                '<th width="80%" id="perm_title">Permissions for '+ username +'</th>' +
                '<th>Allow</th>' +
                '<th>Deny</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>' +
                '<tr>' +
                '<td>Full Control</td>'+
                '<td align="center"><input type="checkbox" value="allow" name="FullControl'+ user_id +'" class="checkbox" ></td>'+
                '<td align="center"><input type="checkbox" value="deny" name="FullControl'+ user_id +'" class="checkbox" ></td>'+
                '</tr>'+
                '<tr>'+
                '<td>Modify</td>'+
                '<td align="center"><input type="checkbox" value="allow" name="Modify'+ user_id +'" class="checkbox" ></td>'+
                '<td align="center"><input type="checkbox" value="deny" name="Modify'+ user_id +'" class="checkbox" ></td>'+
                '</tr>'+
                '<tr>'+
                '<td>Read &amp; Execute</td>'+
                '<td align="center"><input type="checkbox" value="allow" name="ReadAndExecute'+ user_id +'" class="checkbox" ></td>'+
                '<td align="center"><input type="checkbox" value="deny" name="ReadAndExecute'+ user_id +'" class="checkbox" ></td>'+
                '</tr>'+
                '<tr>'+
                '<td>List Folder Contents</td>'+
                '<td align="center"><input type="checkbox" value="allow" name="ListFolderContents'+ user_id +'" class="checkbox" ></td>'+
                '<td align="center"><input type="checkbox" value="deny" name="ListFolderContents'+ user_id +'" class="checkbox" ></td>'+
                '</tr>'+
                '<tr>'+
                '<td>Read</td>'+
                '<td align="center"><input type="checkbox" value="allow" name="Read'+ user_id +'" class="checkbox" ></td>'+
                '<td align="center"><input type="checkbox" value="deny" name="Read'+ user_id +'" class="checkbox" ></td>'+
                '</tr>'+
                '<tr>'+
                '<td>Write</td>'+
                '<td align="center"><input type="checkbox" value="allow" name="Write'+ user_id +'" class="checkbox" ></td>'+
                '<td align="center"><input type="checkbox" value="deny" name="Write'+ user_id +'" class="checkbox" ></td>'+
                '</tr>' +
                '</tbody>' +
                '</table></div>';
        },
        uncheck: function (id,inputs,type) {
            for(var i in inputs) {
                $('input[name="'+inputs[i]+ id +'"][value="'+ type +'"]').prop('checked',false)
            }
        },
        check: function (id,inputs,type) {
            for(var i in inputs) {
                $('input[name="'+inputs[i]+ id +'"][value="'+ type +'"]').prop('checked',true);
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

                $('#user_'+ i +' input:checked').each(function() {
                    data_perm[$(this).attr('name').replace(i,'')] = $(this).val();
                });
                allIds[i] = {name:name,permission:data_perm};
            }

            return allIds;
        }
    };
}();
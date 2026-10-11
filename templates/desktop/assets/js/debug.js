var selected_data = {};
$('[aria-controls="modules"]').click(function () {
    $('#module').change(function () {
        var selected = $(this).val();
        if(selected != "") {
            $('#module_name').val();
            $('#module_description').val();
            $('#module_icon').val();
            $('#module_url').val();

            core.removeAllOptions({
                target: '#parent'
            });

            selected_data = core.select_result[selected];
            $('#module_name').val(selected_data['text']);
            $('#module_description').val(selected_data['description']);
            $('#module_icon').val(selected_data['icon']);
            $('#module_url').val(selected_data['url']);
            core.addOption({
                target: '#parent',
                value: selected_data['parent_id'],
                text: selected_data['parent_name'],
                selected: selected_data['parent_id']
            });


        }
    });

    $('button#save_module').click(function () {
        var selected = $('#module').val();
        core.ajaxRequest({
            method: 'POST',
            url : "/components/save",
            data: {
                id: selected,
                name: $('#module_name').val(),
                parent: $('#parent').val(),
                url: $('#module_url').val(),
                description: $('#module_description').val(),
                icon: $('#module_icon').val()
            }
        },true);
    });
});
$('[aria-controls="users"]').click(function () {
    core.datatable({
        target : '#users-table',
        url: '/users/sessions',
        select_row: true,
        destroy: true,
        buttons: false,
        serverside: false,
        actions: true,
        action_buttons: {
            0: 'add',
            1: 'edit',
            2: 'delete'
        }
    });
    $('#user_details').click(function () {

    });
    $('#user_sess_delete').click(function () {
        $( "#dialog-confirm" ).dialog({
            resizable: false,
            height:140,
            modal: true,
            buttons: {
                'Delete': function() {
                    //@TODO User session delete tamamlanacak
                    var selected = $('#users-table tbody tr.selected td:first-child span').text();
                    core.ajaxRequest({
                        method: 'GET',
                        url: '/users/sessions/delete/'+ selected,
                        data: ''
                    },true);

                    $( this ).dialog( "close" );
                },
                'Cancel': function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });

});

$('.icons a[data-toggle="logs"]').unbind();
$('.icons a[data-toggle="logs"]').on("click", function () {
    if($('div#logs').is(':visible')) {
        $('div#logs').hide();
    }
    else {
        $('div#logs').show();
    }
    return false;
});

$('#init_lang').unbind();
$('#init_lang').on('click', function (e) {
    $('div#languages .modal-body').load('/system/initlang');
    $('div#languages').modal('show');
    JQD.closeStartMenu();
    e.preventDefault();
});




$('div#languages').on('hidden.bs.modal', function () {
    $('div#languages .modal-body').html('');
});
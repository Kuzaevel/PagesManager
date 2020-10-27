$('.permission-file').click(function(){
    var table = $(this).closest('div').find('.permission-page');
    var list = $(this).closest('div').find('.permission-table').first().find('.list-permission');
    var list_button = $(this).closest('div').find('.permission-table').first().find('.list-permission_button');

    if(table.is(":hidden")){
        table.fadeIn("slow");

        getPagePermission(list_button,list);
        setTimeout(function() { list.fadeOut("slow")}, 1800);

    } else {
        table.fadeOut();
    }
});

$('.button-add_page').click(function(){
    var but = $(this);
    var select_group  = but.closest('.permission-table').find('.select-groups');
    var select_access = but.closest('.permission-table').find('.select-access');
    var but_permission = but.closest('.permission-table').find('.button-permission');
    var but_list = but.closest('.permission-table').find('.list-permission_button');

    var new_block = but.closest('.permission-page').find('.permission-add_block');
    var but_addpage = but.closest('.permission-page').find('.button-add_object');

    $.post('ajax/permissions.php', {
        "action"      : "addPage",
        "object-name" : but.data('name')
    },function (data) {
        if (data['success']===true){
            but.remove();
            select_group.show();
            select_group.data('id',data['id']);
            select_access.show();
            but_permission.data('id',data['id']);
            but_permission.show();

            but_list.data('id',data['id']);
            but_list.show();

            new_block.show();
            but_addpage.data('parent',data['id']);
        }
    });
});

$('.button-add_object').click(function() {
    var but = $(this);
    var parent_id = $(this).data('parent');
    var input_obj = $(this).closest('.permission-add_block').find('.input-object_name');
    var object_name = input_obj.val();

    if(object_name.length>0 && object_name!='' && object_name.trim()!='') {
        $.post('ajax/permissions.php', {
            "action": "addObject",
            "object-name": object_name,
            "parent-id": parent_id
        }, function (data) {
            if (data['success']) {
                if(data['id']!=0) {
                    but.closest('.permission-page').find('.permission-blocks').append(data['template']);

                    var new_block = but.closest('.permission-page').find('.permission-blocks').find('.permission-table-new');
                    var but_new = new_block.find('.button-permission');
                    var select_groups = new_block.find('.select-groups');
                    var select_access = new_block.find('.select-access');
                    var but_list = new_block.find('.list-permission_button');
                    var but_remove = new_block.find('.permission-remove_block');

                    initButton(but_new);
                    initSelectGroups(select_groups);
                    initSelectAccess(select_access);
                    initListButton(but_list);
                    initRmoveBlockButton(but_remove);

                    new_block.removeClass('permission-table-new');
                    input_obj.val('');
                }
            } else {
                var notify = but.closest('.permission-add_block').find('.permission-notify');
                notify.html(data['template']);
                notify.show('slow');
                setTimeout(function() { notify.hide('slow'); }, 1200);
            }
        });
    }
});

function initRmoveBlockButton(but){
    but.click(function () {
        $.post('ajax/permissions.php', {
            "action": "removeObject",
            "object-id": but.data('id')
        }, function (data) {
            if (data['success']) {
                but.closest('.permission-table').hide();
            }
        });
    });
}

$('.input-object_name').change(function(){
    if ($(this).val() && $(this).val().length>0 && $(this).val()!='') {
        $(this).closest('.permission-add_block').find('.button-add_object').attr('disabled', false);
    }
});

function initButton(but){
    var list = but.closest('.permission-table').find('.list-permission');

    but.click(function(){
        if(!list.is(":hidden")) list.hide();
        var arr = {
            "action"    : but.data('action'),
            "object-id" : but.data('id'),
            "group-id"  : but.data('group-id'),
            "access-id" : but.data('access-id')
        };
        if (but.data('id')>0 && but.data('group-id')>0 && but.data('access-id')>0 ) {
            $.post('ajax/permissions.php', arr, function (data) {
                if(data['success']) {
                    but.closest('.permission-table').find('.select-access').val(0);
                    but.closest('.permission-table').find('.select-groups').val(0);
                    but.closest('.permission-table').find('.button-permission').data('group-id',0);
                    but.closest('.permission-table').find('.button-permission').data('access-id', 0);

                    //NOTIFICATION
                    var notify = but.closest('.permission-table').find('.permission-notify');
                    notify.html('Доступ изменен');
                    notify.fadeIn();
                    setTimeout(function() { notify.fadeOut("slow")}, 1200);

                } else {
                    alert(data['error'])
                };
            });
        }
    });
}

function initSelectGroups(select_group) {
    select_group.change(function () {
        var select_access = select_group.closest('.permission-table').find('.select-access');
        var but = select_group.closest('.permission-table').find('.button-permission');
        but.data('group-id',select_group.val());
        but.data('access-id',0);

        $.post('ajax/permissions.php', {
            "action"    : "getGroupAccess",
            "object-id" : select_group.data('id'),
            "group-id"  : select_group.val()
        }, function (data) {
            but.data('group-id', select_group.val());
            if(data['id']!=null) {
                select_access.val(data['id']);
                but.data('access-id',data['id']);
            } else {
                select_access.val(0);
            }
        });
    });
}

function initSelectAccess(select_access) {
    select_access.change(function () {
        var but = select_access.closest('.permission-table').find('.button-permission');
        but.data('access-id', $(this).val());
    });
}

function initListButton(but) {
    but.click(function(){
        var list = but.closest('.permission-table').find('.list-permission');
        getPagePermission(but,list);

    })
}

function getPagePermission(but,list) {
    $.post('ajax/permissions.php', {
        "action"    : "getObjectPermission",
        "object-id" : but.data('id')
    }, function (data) {
        if(data['success']) {
            list.html(data.html);

            if (list.is(":hidden")) {
                //list.html(data.html);
                list.fadeIn("slow");
            } else {
                list.fadeOut("slow");
            }
        } else alert(data['error']);
    });
}

$(document).ready(function(){
    $('.button-permission').each(function () {
        initButton($(this));
    });
    $('.select-groups').each(function () {
        initSelectGroups($(this));
    });
    $('.select-access').each(function () {
        initSelectAccess($(this));
    });
    $('.list-permission_button').each(function() {
        initListButton($(this));
    });

    $('.permission-remove_block').each(function() {
       initRmoveBlockButton($(this));
    })
});
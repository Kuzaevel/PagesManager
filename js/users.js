$(document).ready(function(){

    $('.editUser').click(function(){
        $('#myModalChange').data('user_id', $(this).data('user_id'));
        $('#myModalChange').data('type', 'change');
        $('#myModalChange').modal('show');
    });

    $('#addUser').click(function(){
        $('#myModalChange').modal('show');
    });

    $('#myModalChange').on('show.bs.modal',function(){
        $("#editForm input").val("");
        if($('#myModalChange').data('type')=='change') {
            $.post('', {
                "action" : "getUser",
                "id": $(this).data('user_id')
            }, function (data) {
                if (data) {
                    var data = $.parseJSON(data);
                    if(data['success']) {
                        var user = data["user"];
                        for(key in user) {
                            var el = "[name="+key+"]";
                            $(el).val(user[key]);
                            $(el).data('default', user[key])
                        }
                    } else {
                        alert('Ошибка при получении данных пользователя!');
                        console.log(data['error'])
                    }
                }
            });
        }
    });

    $('#editForm').submit(function (e) {
        e.preventDefault();
        if ($('#myModalChange').data('type') == 'change') {
            var user_id = $('#myModalChange').data('user_id');
            var out = "action=edit&id=" + user_id + "&" + $('#editForm').serialize();

            $.post('', out, function (data) {
                if (data) {
                    var data = $.parseJSON(data);
                    if (data['success']) {
                        alert('Данные пользователя сохранены!');
                        $('#myModalChange').modal('hide');
                    } else {
                        alert('Данные не сохранены, проверьте правильность заполнения всех полей!');
                        console.log(data['error'])
                    }
                }
            });
        } else {
            var out = "action=addUser&" + $('#addForm').serialize();
            $.post('', out, function (data) {
                if (data) {
                    var data = $.parseJSON(data);
                    if (data['success']) {
                        alert('Новый пользователь добавлен!');
                        $('#myModalChange').modal('hide');
                        // location.reload();
                    } else {
                        alert('Данные не сохранены, проверьте правильность заполнения всех полей!');
                    }
                }
            });
        }
    });

    $('.removeUser').click(function (){
        var id = $(this).data('user_id');
        $.post('', {action: 'delete', id: id}, function (data) {
            if (data) {
                var data = $.parseJSON(data);
                if (data['success']) {
                    alert('Пользователь удален!');
                    $('#myModalChange').modal('hide');
                    // location.reload();
                } else {
                    alert('Данные не сохранены!');
                }
            }
        });

    })
});

$(document).ready(function(){

    $('.table-users tr').dblclick(function(){
        $('#myModalChange').data('user_id', $(this).data('user_id'));
        $('#myModalChange').data('type', 'change');
        $('#password_block').hide();
        $('#myModalChange').modal('show');
    });

    $('#addUser').click(function(){
        $('#password_block').show();
        $('#myModalChange').modal('show');
    });

    $('#myModalChange').on('show.bs.modal',function(){
        $("#editForm input").val("");
        if($('#myModalChange').data('type')=='change') {
            $('#password_block').hide();
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

        if (!$(this).valid()) {
            return false;
        }

        if ($('#myModalChange').data('type') == 'change') {
            var user_id = $('#myModalChange').data('user_id');
            var out = "action=edit&id=" + user_id + "&" + $('#editForm').serialize();

            $.post('', out, function (data) {
                if (data) {
                    var data = $.parseJSON(data);
                    if (data['success']) {
                        alert('Данные пользователя сохранены!');
                        location.reload();
                    } else {
                        alert('Данные не сохранены, проверьте правильность заполнения всех полей!');
                        console.log(data['error'])
                    }
                }
            });
        } else {
            var out = "action=addUser&" + $('#editForm').serialize();
            $.post('', out, function (data) {
                if (data) {
                    var data = $.parseJSON(data);
                    if (data['success']) {
                        alert('Новый пользователь добавлен!');
                         location.reload();
                    } else {
                        alert('Данные не сохранены, проверьте правильность заполнения всех полей!');
                    }
                }
            });
        }
    });

    $('.removeUser').click(function () {
        var id = $(this).data('user_id');
        $.post('', {action: 'delete', id: id}, function (data) {
            if (data) {
                var data = $.parseJSON(data);
                if (data['success']) {
                    alert('Пользователь удален!');
                    $('#myModalChange').modal('hide');
                     location.reload();
                } else {
                    alert('Данные не сохранены!');
                }
            }
        });
    })

    $('#myModalChange').on('hide.bs.modal',function()
    {
        $('#myModalChange').data('type', '');
        $("#editForm input").val("");
        $("#editForm select").val(-1);
    });

    $('#editForm').validate({
        lang: 'ru',
        rules: {
            username: {
                required:true,
                username_double: true
            }
        },
        messages: {
            username: {
                required: "Это поле обязательно для заполнения",
                username_double: "Данный login уже зарегистрирован"
            }
        }
    });

    jQuery.validator.addMethod("username_double", function(value, element) {
        return !is_double_name(value);
    });


    function is_double_name(username) {
        var isDouble = false;
        $.ajax({
            type: "POST",
            url: "",
            data: {action: 'validate', username: username},
            async: false,
            success:
                function(data) {
                    var data = $.parseJSON(data);
                    isDouble = data['is_double'] === "double" ? true : false;
                }
        });
        return isDouble;
    }

});

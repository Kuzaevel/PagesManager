$(document).ready(function(){

    $('.table-users tr').dblclick(function(){
        $('#myModalChange').data('user_id', $(this).data('user_id'));
        $("#editForm input").val("");
        $('.err_form:not(input)').remove();
        $('#myModalChange').modal('show');
    });

    // $('#addUser').click(function(){
    //     $('#myModalAdd').modal('show');
    // });

    $('#myModalAdd').on('show.bs.modal',function(){
        $("#addForm input").val("");
    });

    $('#myModalChange').on('show.bs.modal',function(){
        $("#editForm input").val("");
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
    });

    $('#editForm').submit(function (e) {
        e.preventDefault();

        if (!$(this).valid()) {
            return false;
        }
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
    });

    $('#addForm').submit(function (e) {
        e.preventDefault();
        if (!$(this).valid()) {
            return false;
        }
        var out = "action=addUser&" + $('#addForm').serialize();
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
        $("#editForm input").val("");
        $("#editForm select").val(-1);
        $('.err_form:not(input)').remove();
    });

    $('#myModalAdd').on('hide.bs.modal',function()
    {
        $("#addForm input").val("");
        $("#addForm select").val(-1);
        $('.err_form:not(input)').remove();
    });

    $('#editForm').validate({
        errorClass:'error err_form',
        lang: 'ru',
        rules: {
            username: {
                required:true,
                minlength: 4,
                maxlength: 16,
            },
            contact_name: "required",
            contact_mail: {
                required: true,
                email: true
            }
        },
        messages: {
            username: {
                required: "Это поле обязательно для заполнения"
            },
            password: "Это поле обязательно для заполнения",
            contact_name: "Это поле обязательно для заполнения",
            contact_mail: {
                required: "Это поле обязательно для заполнения",
                email: "Заполните в формате name@domain.com"
            }
        }
    });

    $('#addForm').validate({
        errorClass:'error err_form',
        lang: 'ru',
        rules: {
            username: {
                required:true,
                username_double: true,
                minlength: 4,
                maxlength: 16,
            },
            contact_name: "required",
            contact_mail: {
                required: true,
                email: true
            },
            password:{
                required: true,
                minlength: 6,
                maxlength: 16
            },
        },
        messages: {
            username: {
                required: "Это поле обязательно для заполнения",
                username_double: "Данный login уже зарегистрирован",
            },
            password: "Это поле обязательно для заполнения",
            contact_name: "Это поле обязательно для заполнения",
            contact_mail: {
                required: "Это поле обязательно для заполнения",
                email: "Заполните в формате name@domain.com"
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

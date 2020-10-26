$(document).ready(function(){

    $('#addUser').click(function(){
        $('#myModalChange').modal('show');
    });

    $('#addForm').submit(function (e) {
        e.preventDefault();
        var out = "action=addUser&" + $('#addForm').serialize();
        $.post('', out, function (data) {
            if (data) {
                var data = $.parseJSON(data);
                if (data['success']) {
                    alert('Новый пользователль добавлен!');
                    $('#myModalChange').modal('hide');
                    // location.reload();
                } else {
                    alert('Данные не сохранены, проверьте правильность заполнения всех полей!');
                }
            }
        });
    });
});

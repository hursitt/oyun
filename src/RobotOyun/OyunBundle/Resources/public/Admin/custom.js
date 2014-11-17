/**
 * Created by hursit on 11/15/14.
 */

$(function(){
    $('#dataTables').dataTable();

    $('.removeGame').on('click', function () {
        var href = $(this).attr('href');
        if(confirm('Silmek İstediğinizden Emin Misiniz')){
            return true;
        }
        return false;
    });


    var isActiveSwitch = $('.isActive-switch');
    isActiveSwitch.bootstrapSwitch();
    isActiveSwitch.on('switchChange.bootstrapSwitch', function(event, state) {
        var status = state == true ? 1 : 0; // true | false
        var slug = $(this).attr('id'); // true | false


        $.ajax({
            url :  Routing.generate('admin_reverse_game_status', {'gameSlug': slug, 'status': status }),
            type:  'GET',
            data : null,
            success: function(data, textStatus, jqXHR)
            {
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert("Hata Oluştu");
            }
        });
    });

});


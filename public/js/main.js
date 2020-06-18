var url = 'http://www.proyecto-laravel.local:8888';

window.addEventListener("load", function() {
    $('.btn-like').css('cursor', 'pointer');
    $('.btn-dislike').css('cursor', 'pointer');

    //Boton de likes
    function like() {
        $('.btn-like').unbind('click').click(function() {
            console.log('Like');
            $(this).addClass('btn-dislike').removeClass('btn-like');
            $(this).attr('src', url+'/img/heart-red-64.png');

            $.ajax({
                url: url+'/like/'+$(this).data('id'),
                type: 'GET',
                success: function(response) {
                    if(response.like) {
                    console.log('Has dado Like a la publicaión');
                    } else {
                        console.log('Error al dar Like');
                    }
                }
            });

            dislike();

        });
    }
    like();

    //Boton de dislike
    function dislike() {
        $('.btn-dislike').unbind('click').click(function() {
            console.log('Dislike');
            $(this).addClass('btn-like').removeClass('btn-dislike');
            $(this).attr('src', url+'/img/heart-black-64.png');

            $.ajax({
                url: url+'/dislike/'+$(this).data('id'),
                type: 'GET',
                success: function(response) {
                    if(response.like) {
                        console.log('Has dado Dislike a la publicación');
                    } else {
                        console.log('Error al dar a Dislike');
                    }
                }
            });

            like();
        });
    }
    dislike();

    //BUSCADOR
    //Capturar los estilos del formulario
    $('#buscador').submit(function(e) {
        $(this).attr('action', url+'/gente/'+$('#buscador #search').val());
    });

});

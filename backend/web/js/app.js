$(function() {

    var $b = $('body');

    //scroll view
    $b.on('click', 'a[href="#left-grid-view_smart__table-container"]', function(e){
        e.preventDefault();
        var $cont = $('.grid-view_smart__table-container');
        $cont.animate({scrollLeft: '-=300'}, 300);
    });
    $b.on('click', 'a[href="#right-grid-view_smart__table-container"]', function(e){
        e.preventDefault();
        var $cont = $('.grid-view_smart__table-container');
        $cont.animate({scrollLeft: '+=300'}, 300);
    })

});

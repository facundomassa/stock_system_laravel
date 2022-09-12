$(document).ready(function () {
    let $framepdf = $('#framepdf');
    let $pfdbuttom = $('#pfdbuttom');
    let $filter = $('form').serialize();

    $pfdbuttom.click( function(){
    //     $.get('http://stocksystem.com/stock/pdf', $filter, function(htmlexterno){
    //         console.log($filter);
    //         $framepdf.html(htmlexterno);
    // }, 'html');
        console.log('http://stocksystem.com/stock/pdf?' + $filter);
        $framepdf.attr("src", 'http://stocksystem.com/stock/pdf?' + $filter);
    })
})
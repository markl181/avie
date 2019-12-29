window.onload=function() {

    var clipboard = new ClipboardJS('.btn');

    clipboard.on('success', function(e) {

        e.clearSelection();
    });

};

$(document).ready(function(){
    $("button#toggle").click(function(){
        $(".history").toggle();
    });
});
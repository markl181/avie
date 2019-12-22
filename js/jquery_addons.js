jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.document.location = $(this).data("href");
    });
});

jQuery(document).ready(function($) {
    $(".clickable-row-new").click(function() {
        var url = $(this).data('href');
        window.open(url);

    });
});
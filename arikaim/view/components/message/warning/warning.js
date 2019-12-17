$(document).ready(function() {
    $('.warning .close').on('click', function() {
        $(this).closest('.message').transition('fade');
    });
});
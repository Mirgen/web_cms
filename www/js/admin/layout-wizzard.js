
$( ".selectLayout" ).click(function( event ) {
    event.preventDefault();
    $('input[name="layout"]').val($(this).attr('id'));
    $('input[name="layoutVisible"]').val($(this).attr('title'));
});

$( "img.thumbImage" ).click(function( event ) {
    var thumbImage = $(this);
    var newMainImage = thumbImage.attr("src");
    var images = thumbImage.closest( ".images" );
    var mainImage = images.find(".mainImage");

    thumbImage.attr("src", mainImage.attr("src"));
    mainImage.attr("src", newMainImage);
});

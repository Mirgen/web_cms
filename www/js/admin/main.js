$(function(){

    /* minimalize or mazimalize module in list of module in Administration */
    $( ".minimalize-maximalize" ).click(function() {
        $(this).find(".minimalize").toggle();
        $(this).find(".maximalize").toggle();


        var module = $(this).closest(".module");
        module.children(".panel-body").toggle(200);
        module.children(".panel-footer").toggle(700);
        console.log(module.attr('id'));

        // save module ID for future, to leave this module opened for a user
        var cookieValue = $.cookie(module.attr('id'));
        if(cookieValue){
            $.removeCookie(module.attr('id'));
        } else {
            // expires after 1 day
            $.cookie(module.attr('id'), 1, { expires : 1 });
        }

    });

    $( ".sortable" ).sortable({
      // configuration
      delay: 100,
      items: 'li',
      update: function(event, ui) {
        $.ajax({
            type: "POST",
            url: $(this).attr( "data-url" ),//"/admin/module-image-galery/update-order/",
            data: $(this).sortable("serialize")
          });
      }
    });
    $( ".sortable-images" ).disableSelection();

    $('.confirmation').on('click', function () {
        return confirm('Opravdu to chcete udělat?');
    });

    tinymce.init({
            selector: ".tinymce_insert_code",
            plugins: [
                    "code"
            ],

            toolbar1: "code",

            menubar: false,
            toolbar_items_size: 'small',
    });

    tinymce.init({
            selector: ".tinymce_text_editor",
            plugins: [
                    "advlist autolink autosave link image jbimages lists charmap print preview hr anchor pagebreak spellchecker code",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
            ],

            toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect code",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image jbimages media code | insertdatetime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

            menubar: false,
            toolbar_items_size: 'small',

            style_formats: [
                    {title: 'Bold text', inline: 'b'},
                    {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                    {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                    {title: 'Example 1', inline: 'span', classes: 'example1'},
                    {title: 'Example 2', inline: 'span', classes: 'example2'},
                    {title: 'Table styles'},
                    {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
            ],

            templates: [
                    {title: 'Test template 1', content: 'Test 1'},
                    {title: 'Test template 2', content: 'Test 2'}
            ]
    });
});



jQuery(function () {
    jQuery("#sortable").sortable({
        update: function (event, ui) {
            var data = jQuery("#sortable").sortable("serialize");
            var data2 = jQuery('#sortable').sortable('toArray').toString();
            //alert(data);
            //alert(data2);
        }
    }).disableSelection();
    supportInput();

    function supportInput() {
        jQuery('#shipping #sortable input[type="text"],select,button').mousedown(function (event) {
            event.stopPropagation();
        });
    }

    function clickEventBindButtonRemove() {
        jQuery("#shipping #sortable li button.remove").unbind('click');

        jQuery("#shipping #sortable li button.remove").click(function () {
            var t = jQuery(this);
            //jQuery(this).parents('li').find('input[type="text"]').val("");
            jQuery(this).parents('li').slideUp('fast', function () {
                t.parents('li').find('input[type="text"]').val("");
                jQuery(this).parents('li').remove();
                return false;
            });
            return false;
        });
    }
    clickEventBindButtonRemove();

    jQuery("#shipping button.add_row").click(function () {
        //var li = jQuery("#shipping #sortable li").clone().last();
        var li = jQuery("#shipping .li_clone").html();
        //alert(li);
        jQuery(li).appendTo("#shipping #sortable");
        supportInput();
        clickEventBindButtonRemove();
        //clickEventBindButtonAddRow();
        return false;
    });
    

    jQuery(".cfshoppingcart.welcome-panel a.welcome-panel-close.button").click(function () {
        jQuery.cookie("cfshoppingcart-welcome-panel", "hidden", {expires: 30});//30days
        jQuery("#welcome-panel.cfshoppingcart").addClass("hidden");
        return false;
    });

});


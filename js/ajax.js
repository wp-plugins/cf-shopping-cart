
var cfshoppingcart = {version: '2.0'};

cfshoppingcart.notify = function (title, text, error) {
    if (error) classes = 'cfshoppingcart error';
    else classes = 'cfshoppingcart';
    var notice = new PNotify({
        title: title,
        text: text,
        opacity: 1.0,
        animation: 'fade',
        addclass: classes,
        delay: 2000,
        after_init: function (e) {
        },
        nonblock: {
            nonblock: true
        },
    });
    notice.get().click(function () {
        notice.remove();
    });
};

cfshoppingcart.message = function (title, text, error) {
    if (error) {
        jQuery('div.cfshoppingcart_message_wrap').addClass('error');
    } else {
        jQuery('div.cfshoppingcart_message_wrap').removeClass('error');
    }
    if (text) {
        jQuery('div.cfshoppingcart_message_wrap').addClass('message');
    } else {
        jQuery('div.cfshoppingcart_message_wrap').removeClass('message');
    }
    jQuery('div.cfshoppingcart_message_wrap > div').text(text);
};

cfshoppingcart.buttonClick = function () {
    var submitSelector = 'button.cfshoppingcart[type="submit"]';
    jQuery(submitSelector).unbind();
    jQuery(submitSelector).click(function () {
        //jQuery(submitSelector).unbind();
        //event.preventDefault();

        var buttonName = jQuery(this).attr('name');
        var buttonValue = jQuery(this).val();
        var quantity = jQuery(this).parent().find('.quantity').text();
        //console.log('quantity = ' + quantity);
        var product = jQuery(this).parent();
        //console.log(buttonName);

        var formSelector = 'form[name="cfshoppingcart"]';
        jQuery(formSelector).submit(function (event) {
            jQuery(formSelector).off();
            // HTMLでの送信をキャンセル
            event.preventDefault();

            // 操作対象のフォーム要素を取得
            var form = jQuery(this);

            //console.log($form.serialize());
            var sendData = form.serialize() + '&' + buttonName + '=' + buttonValue;
            console.log(sendData);
            // 送信
            jQuery.ajax({
                url: cfshoppingcart_ajaxurl,
                type: "POST",
                dataType: "json",
                //type: $form.attr('method'),
                data: sendData,
                // 通信成功時の処理
                success: function (response) {
                    //console.log(response);
                    cfshoppingcart.notify(response.message.title, response.message.text, response.message.error);
                    cfshoppingcart.message(response.message.title, response.message.text, response.message.error);
                    //console.log('success');

                    var selector = response.product.selector;
                    var content = response.product.content;
                    //console.log(selector);
                    //console.log(content);
                    jQuery(selector).replaceWith(content);

                    if (buttonName == 'cmd' && buttonValue == 'quantity_minus' && quantity == 1) {
                        jQuery(product).slideUp('fast', function () {
                            //console.log('slideUp');
                            jQuery.each(response.shortcodes, function (key, data) {
                                var selector = data.selector;
                                var content = data.content;
                                jQuery(selector).replaceWith(content);
                            });
                            cfshoppingcart.buttonClick();
                            //return false;
                        });
                    } else {
                        jQuery.each(response.shortcodes, function (key, data) {
                            //console.log('key = ' + key);
                            var selector = data.selector;
                            var content = data.content;
                            //console.log(selector + ' = ' + content);
                            jQuery(selector).replaceWith(content);
                        });
                        cfshoppingcart.buttonClick();
                        //return false;
                    }
                    //cfshoppingcart.buttonClick();
                },
                error: function (xhr, textStatus, error) {
                    console.log('Error: xhr: ' + xhr + "\ntextStatus: " + textStatus + "\nerror: " + error);
                }
            });
            return false;
        });
        //return false;
    });
    return false;
};


jQuery(document).ready(function () {
    cfshoppingcart.buttonClick();
});


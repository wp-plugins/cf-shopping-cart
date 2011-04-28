// postbox.js
//-*- Encoding: utf8n -*-


jQuery(document).ready(function(){
    //jQuery('.cfshoppingcart_postbox h3').prepend('<a class="togbox">+</a> ');
    jQuery('.cfshoppingcart_postbox div.handlediv').click(function() {
        jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
    });
    jQuery('.cfshoppingcart_postbox h3').click(function() {
        jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
    });
    jQuery('.cfshoppingcart_postbox.close-me').each(function(){
        jQuery(this).addClass("closed");
    });

    cfshoppingcart_postbox_cookie('.cfshoppingcart_postbox div.handlediv');
    cfshoppingcart_postbox_cookie('.cfshoppingcart_postbox h3');
    function cfshoppingcart_postbox_cookie(sel) {
        //var sel = '.cfshoppingcart_postbox div.handlediv';
        var coo = 'cfshoppingcart_admin_postbox';
        //ddの数を変数に代入
        var ddlen = jQuery(sel).length;
        
        //初期設定部分  
        for (i = 0; i < ddlen; i++) {
            if (jQuery.cookie(coo + i)) {
                //既にcookieがあれば表示、cookie名は23行目と連動
                //jQuery(sel).eq(i).show();
                //alert('add');
                jQuery(jQuery(sel).eq(i).parent().get(0)).addClass('closed');
            } else {//cookieが無ければ非表示
                //alert('remove');
                //jQuery(sel).eq(i).removeClass('closed');
                jQuery(jQuery(sel).eq(i).parent().get(0)).removeClass('closed');
            }
        }
        
        //クリック時のfunction設定
        jQuery(sel).click(function() {
            //alert('i');
            //何番目のdtなのかを変数に代入
            var index = jQuery(sel).index(this);
            //alert(index);
            //クリックされたら対応するselにイベントを割り当てる
            //jQuery(sel).eq(index).slideToggle("fast");
            
            //cookieの名前となる変数を作成
            var name = coo + index;
            
            if (jQuery.cookie(name)) {//既に対応するcookieを持っていたら
                jQuery.cookie(name, '', { expires: -1 });//cookieを削除
            } else {
                jQuery.cookie(name,1,{expires:7});//cookieをセットする
            }
        }).css("cursor","pointer");
    }
});


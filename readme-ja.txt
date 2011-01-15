=== Cf Shopping Cart ===
Contributors: AI.Takeuchi
Donate link: http://takeai.silverpigeon.jp/
Tags: shopping, content, widget, plugin, custom field, wordpress, Exec-PHP, Contact Form 7, Custom Field Template, QF-GetThumb
Requires at least: 2.6
Tested up to: 3.0.4
Stable tag: 0.2.13

Cfショッピングカートはシンプルなショッピングカート機能です。


== Description ==

Cfショッピングカートはシンプルなショッピングカート機能です。(同じ)

== Installation ==

1. 追加プラグインのインストール。次のプラグインをインストールし利用可能にして下さい。Exec-PHP, Contact Form 7, Custom Field Template, QF-GetThumb(任意).
2. CFショッピングカートのインストール。cf-shopping-cart フォルダを '/wp-content/plugins/' ディレクトリーへアップロードします。
3. CFショッピングカートプラグイン2個を有効にして下さい。送料機能を使用する場合はファイル 'shipping.php を '/wp-content/plugins/cf-shopping-cart/extention/' から '/wp-content/cfshoppingcart/' へコピーし、それを編集して下さい。
4. サイドバーウィジットに CFショッピングカートウィジットを配置してください。
5. Contact Form 7 のフォームを作成します。フォームの内容に '[cfshoppingcart* cartdata class:cfshoppingcart7]' を追加します。また、送信メールの内容に '[cartdata]' を追加します。追加設定に 'on_sent_ok: "cfshoppingcart_empty_cart();"' を追加します。そして新しいページ(例:オーダー送信)を作成しフォームのショートコードを書きます。このページのURLを覚えて置いてください。
6. 新しいページ(例:ショッピングカート)を作成し、内容に次のショートコードを書きます '[cfshoppingcart_cart 1]'。このページのURLを覚えておいてください。
7. カテゴリーを追加します(例:商品)。
8. プラグイン Custom Field Template の設定を行います。新しいカスタムフィールドのテンプレートを作成します。フィールドの例 '商品名', '価格'... このフィールド名を覚えておいてください。
9. CFショッピングカートの設定を行います。
10. 項目9で「手動」を選んだ場合はテーマファイルの編集作業が必要です。archive.php と single.php (必要であれば home.php, page.php なども) を編集し PHPコードを追加します。'<?php cfshoppingcart(); ?>'。書き込む場所は記事出力ループ内です。
11. 商品ページの作成。新しい記事の追加をし、商品の内容を書きます。カテゴリーを設定し、カスタムフィールドを入力します。
12. 項目11を繰り返し商品を追加します。


== More plugins. Thank you! ==

Name: Exec-PHP
URL: http://wordpress.org/extend/plugins/exec-php/

Name: Contact Form 7
URL: http://wordpress.org/extend/plugins/contact-form-7/

Name: Custom Field Template
URL: http://wordpress.org/extend/plugins/custom-field-template/

Name: QF-GetThumb
URL: http://wordpress.org/extend/plugins/qf-getthumb/


== Others ==

#I can not speak english very well.
#I would like you to tell me mistake my English, code and others.
#thanks.
Cf Shopping Cart Website: http://cfshoppingcart.silverpigeon.jp/
Blog: http://takeai.silverpigeon.jp/
AI.Takeuchi <takeai@silverpigeon.jp>



=== Cf Shopping Cart ===
Contributors: AI.Takeuchi
Tags: shopping, content, widget, plugin, custom field, Exec-PHP, Contact Form 7, Custom Field Template, QF-GetThumb
Requires at least: 2.6
Tested up to: 3.0.4
Stable tag: 0.6.0

Cfショッピングカートはシンプルなショッピングカート機能です。


== Description ==

同上


== Installation ==

1. プラグインのインストールと有効化。次のプラグインをインストールし利用可能にして下さい。Cf Shopping Cart, Exec-PHP, Contact Form 7, Custom Field Template, QF-GetThumb(任意).
2. サイドバーウィジットに CFショッピングカートウィジットを配置してください。
3. Contact Form 7 のフォームを作成します。フォームの内容に '[cfshoppingcart* cartdata class:cfshoppingcart7]' を追加します。送信メールの内容に '[cartdata]' を追加します。追加設定に 'on_sent_ok: "cfshoppingcart_empty_cart();"' を追加します。そして新しいページ(例:オーダー送信)を作成しフォームのショートコードを書きます。このページのURLを覚えて置いてください。
4. 新しいページ(例:ショッピングカート)を作成し、内容に次のショートコードを書きます '[cfshoppingcart_cart 1]'。このページのURLを覚えておいてください。
5. プラグイン Custom Field Template の設定を行います。カスタムフィールドのテンプレートを設定します。フィールドの例 '商品名', '価格'... このフィールド名を覚えておいてください。
6. CFショッピングカートの設定を行います。
("手動"を選んだ場合はテーマファイルの編集作業が必要です。archive.php と single.php (必要であれば home.php, page.php なども) を編集し PHPコードを追加します。'<?php cfshoppingcart(); ?>'。書き込む場所は記事出力ループ内です)
7. 商品ページの作成。新しい記事の追加をし、商品の内容を書きます。カスタムフィールドを入力します。
8. 項目7を繰り返し商品を追加します。


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



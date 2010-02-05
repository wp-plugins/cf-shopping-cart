=== Cf Shopping Cart ===
Contributors: AI.Takeuchi
Donate link: http://takeai.silverpigeon.jp/
Tags: shopping, content, widget, plugin, custom field, wordpress, Exec-PHP, Contact Form 7, Custom Field Template, QF-GetThumb
Requires at least: 2.6
Tested up to: 2.9.1
Stable tag: 0.1.3

Cfショッピングカートはシンプルなショッピングカート機能です。

Demonstration site is here!!
http://takeai.silverpigeon.jp/donate


== Description ==

Cfショッピングカートはシンプルなショッピングカート機能です。(同じ)

== Installation ==

1. 追加プラグインのインストール。次のプラグインをインストールし利用可能にして下さい。Exec-PHP, Contact Form 7, Custom Field Template, QF-GetThumb(任意).
2. CFショッピングカートのインストール。cfshoppingcart フォルダを `/wp-content/plugins/` ディレクトリーへアップロードします。
3. Contact Form 7 プラグイン用の拡張モジュールをインストールします。拡張モジュールファイル 'cfshoppingcart.php' を '/wp-content/plugins/cfshoppingcart/contact-form-7-module/' フォルダから '/wp-content/plugins/contact-form-7/module/' フォルダへコピーします。
4. CFショッピングカートプラグイン2個を有効にして下さい。送料機能を使用する場合はファイル '/wp-content/plugins/cfshoppingcart/extention/shipping.php' を編集して下さい。
5. サイドバーウィジットに CFショッピングカートウィジットを配置してください。
6. Contact Form 7 のフォームを作成します。フォームの内容に '[cfshoppingcart* cartdata class:cfshoppingcart7]' を追加します。また、送信メールの内容に '[cartdata]' を追加します。そして新しいページ(例:オーダー送信)を作成しフォームのショートコードを書きます。このページのURLを覚えて置いてください。
7. 新しいページ(例:ショッピングカート)を作成し、内容に次のPHPコードを書きます '<?php cfshoppingcart_cart(); ?>' (HTML mode)。このページのURLを覚えておいてください。
8. カテゴリーを追加します(例:商品)。
9. プラグイン Custom Field Template の設定を行います。新しいカスタムフィールドのテンプレートを作成します。フィールドの例 '商品名', '価格'... このフィールド名を覚えておいてください。
10. archive.php と single.php ファイルを編集し次のPHPコードを追加します。'<?php cfshoppingcart(get_post_custom()); ?>'。書き込む場所は次のコードの上辺りです '<?php comments_template(); // Get wp-comments.php template ?>'。
11. CFショッピングカートの設定を行います。
12. 商品ページの作成。新しい記事の追加をし、商品の内容を書きます。カテゴリーを設定し、カスタムフィールドを追加します。


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

# If you notice my mistakes(Program, English...), Please tell me.
Web site: http://takeai.silverpigeon.jp/
AI.Takeuchi <takeai@silverpigeon.jp>



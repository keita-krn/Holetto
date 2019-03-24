# Holetto
会員登録制の掲示板 / Webアプリケーション  
ユーザーは自分が興味のあるテーマ（カテゴリー）の中でスレッド（掲示板）を作成し、  
書き込みを行う事ができる。カテゴリーは自分で作成することができる。
## トップ画面
![Holetto](https://user-images.githubusercontent.com/46701811/54836908-88b2b100-4d08-11e9-9dcc-16e64cb33d53.png)
## テストユーザー
- ユーザー名:guest
- パスワード:guest123
## 作成しようと思ったきっかけ
- 作品を作りながら学習を進めたいと思った為。
- スクールで掲示板の作成方法を教わり、個人でも作ってみたいと思った為。
- 掲示板サイトにおいて重要な「スレッドの新規作成機能」の作り方が分からず悩んでいた所、DBのリレーションを上手く活用すれば実現できるのではないか、と考えた為。
## 実装機能
- ユーザー登録機能
- ログイン機能
- 画像アップデート機能
   - Cloudinary(画像をクラウド上で管理できるサービス)を使用
- カテゴリー作成機能
- スレッド一覧表示機能
  - ページネーション機能
  - スレッド作成機能
- カテゴリー・スレッド検索機能
- コメント一覧表示機能
   - いいね機能
   - コメント投稿機能
   - コメント削除機能
- マイページ表示機能
   - コメント投稿履歴表示機能
   - 退会機能
- ログアウト機能
## 開発環境
- 使用言語・ＤＢ
  - HTML
  - CSS
  - PHP
  - JavaScript
  - MySQL(ClearDB)
- 使用ツール
  - Visual Studio Code
  - XAMPP
  - MySQL Workbench
- ライブラリなど
  - jQuery
  - Font Awesome
  - Ajax
- インフラ
  - Heroku

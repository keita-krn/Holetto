# Holetto
会員登録制の掲示板（コミュニティサイト） / Webアプリケーション  
ユーザーは自分が興味のあるテーマ（カテゴリー）の中でスレッド（掲示板）を作成し、  
書き込みを行う事ができる。カテゴリーは自分で作成することができる。  
制作期間：2/10～3/31（約一か月半）  
URL：https://holetto.herokuapp.com  
テストユーザー：ユーザー名：guest　パスワード：guest123
## トップ画面
![holetto_800](https://user-images.githubusercontent.com/46701811/55288106-bc1ecb00-53ed-11e9-9d3d-4320d75f37e3.png)
## スレッド画面
![thread_800](https://user-images.githubusercontent.com/46701811/55288157-51ba5a80-53ee-11e9-92e9-94e0130d183a.png)
## 作成しようと思ったきっかけ
- 作品を作りながら学習を進めたいと思った為。
- スクールで掲示板の作成方法を教わり、個人でも作ってみたいと思った為。
- 掲示板サイトにおいて重要な「スレッドの新規作成機能」の作り方が分からず悩んでいた所、DBのリレーションを上手く活用すれば実現できるのではないか、と考えた為。 
## 実装機能
- ユーザー登録機能
- ログイン機能
- 画像アップロード機能
- カテゴリー作成機能
- スレッド一覧表示機能
  - ページネーション機能
- スレッド作成機能
- カテゴリー・スレッド検索機能
- コメント一覧表示機能
- いいね機能
- コメント投稿機能
- コメント返信機能
- コメント削除機能
- マイページ表示機能
   - ユーザー画像更新機能
- コメント投稿履歴表示機能
- ユーザー情報削除機能
- ログアウト機能
## 技術内容
- 使用言語・ＤＢ
  - HTML
  - CSS
  - PHP
  - JavaScript
  - MySQL（ClearDB）
- 使用ツール
  - Visual Studio Code
  - XAMPP
  - MySQL Workbench
- ライブラリなど
  - jQuery
  - Font Awesome
  - Ajax
- インフラなど
  - Heroku（PaaS）
  - Cloudinary（画像管理サービス）
## 評価点
- 当初の目的通り、アプリ開発を通して基本構文や組み込み関数、DB連携などについて学ぶ事が出来た。
- 会員制の掲示板に必要な機能を一から開発することでその仕組みを学び、実装する力がついた。
- 機能実装～デプロイする上で分からない事に直面した際に様々な手段で解決する力が身についた。
  - エラーメッセージやログを読む、Googleで検索する、teratailで質問するなど
## 反省点とその改善策
- 初期段階でテーブル定義書、設計書を作成したが必要な部分が欠けており、実装段階で欠けていた部分の仕様について改めて考える事になってしまった。
  - 次にアプリ開発する際はより詳細な設計書を作成するよう心がける。

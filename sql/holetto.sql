set names utf8;
set foreign_key_checks=0;

create table category_table(
id int primary key not null auto_increment comment "カテゴリーID",
category_name varchar(100) not null comment "カテゴリー名",
category_image varchar(255) not null comment "カテゴリー画像",
create_category_user_id int(20) not null comment "カテゴリーを作ったユーザーのID",
insert_date date not null comment "登録日"
);

create table thread_table(
id int primary key not null auto_increment comment "スレッドID",
title varchar(100) not null comment "スレッドタイトル",
first_comment varchar(255) not null comment "１コメ",
thread_image varchar(255) not null comment "スレッド画像",
create_thread_user_id int(20) not null comment "スレッドを作ったユーザーのID",
category_id int(20) not null comment "カテゴリーのID",
insert_date datetime not null comment "投稿日時"
);

create table comment_table(
id int primary key not null auto_increment comment "コメントID",
comment varchar(255) not null comment "コメント内容",
thread_id int(20) not null comment "書き込んだスレッドのID",
user_id int(20) not null comment "書き込んだユーザーのID",
comment_image varchar(255) not null comment "コメント画像",
reply_comment_id int(20) not null comment "返信先のコメントのID",
insert_date datetime not null comment "投稿日時"
);

create table user_table(
id int primary key not null auto_increment comment "ユーザーID",
user_name varchar(32) not null comment "ユーザー名",
email varchar(32) not null comment "メールアドレス",
password varchar(255) not null comment "パスワード",
user_image varchar(255) not null comment "ユーザー画像",
insert_date date not null comment "登録日"
);

create table good_table(
id int primary key not null auto_increment comment "いいねID",
comment_id int(20) not null comment "コメントID",
user_id int(20) not null comment "ユーザーID",
delete_flg tinyint(1) not null comment "削除フラグ",
insert_date datetime not null comment"登録日時",
update_date timestamp not null comment "更新日時"
);
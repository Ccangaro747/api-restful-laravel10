CREATE DATABASE IF NOT EXISTS api_rest_laravel;
USE api_rest_laravel;

CREATE TABLE users(
id int(255) auto_increment NOT NULL,
name varchar(50) NOT NULL,
surname varchar(50) NOT NULL,
role varchar(20),
email varchar(100) NOT NULL,
password varchar(255) NOT NULL,
description text,
image varchar(255),
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
remember_token varchar(100),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDB;

CREATE TABLE categories(
id int(255) auto_increment NOT NULL,
name varchar(50) NOT NULL,
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
CONSTRAINT pk_categories PRIMARY KEY(id)
)ENGINE=InnoDB;

CREATE TABLE posts(
id int(255) auto_increment NOT NULL,
user_id int(255) NOT NULL,
category_id int(255) NOT NULL,
title varchar(255) NOT NULL,
content text NOT NULL,
image varchar(255),
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
CONSTRAINT pk_posts PRIMARY KEY(id),
CONSTRAINT fk_posts_users FOREIGN KEY(user_id) REFERENCES users(id),
CONSTRAINT fk_posts_categories FOREIGN KEY(category_id) REFERENCES categories(id)
)ENGINE=InnoDB;
CREATE DATABASE IF NOT EXISTS apiLaravel;
USE apiLaravel;

CREATE TABLE users(

    id              int(255) auto_increment not null,
    role            varchar(20),
    name            varchar(255),
    surname         varchar(255),
    password        varchar(255),
    created_at      datetime DEFAULT null,
    updated_at      datetime DEFAULT null,
    remember_token  varchar(255),

    CONSTRAINT pk_users PRIMARY KEY(id)

)ENGINE=InnoDb;


CREATE TABLE cars(

    id              int(255) auto_increment not null,
    user_id         int(255) not null,
    title           varchar(20),
    description     text,
    price           varchar(30),
    status          varchar(30),
    created_at      datetime DEFAULT null,
    updated_at      datetime DEFAULT null,
    remember_token  varchar(255),

    CONSTRAINT pk_cars PRIMARY KEY(id),
    CONSTRAINT fk_cars_users  FOREIGN KEY(user_id) REFERENCES users(id)
 
)ENGINE=InnoDb;
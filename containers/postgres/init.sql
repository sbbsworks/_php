CREATE DATABASE _php
    WITH
    OWNER = postgres
    ENCODING = 'UTF8'
    LOCALE_PROVIDER = 'libc'
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False;

\c _php;

CREATE TABLE IF NOT EXISTS public.users
(
    id character varying(100) NOT NULL,
    name character varying(100) NOT NULL,
    email character varying(200) DEFAULT null UNIQUE,
    telegram character varying(200) DEFAULT null UNIQUE,
    PRIMARY KEY (id)
);

ALTER TABLE IF EXISTS public.users
    OWNER to postgres;
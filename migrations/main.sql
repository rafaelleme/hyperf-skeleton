create table users
(
    id         serial
        constraint users_pk
            primary key,
    name       varchar(255)              not null,
    document   varchar(20)               not null,
    email      varchar(100)              not null,
    created_at timestamp   default now() not null,
    updated_at timestamp   default now() not null,
    deleted_at timestamp,
    type       varchar(30) default 'consumer'::character varying,
    roles      json        default '[]'::json,
    password   varchar(100)              not null
);

comment on column users.type is 'consumer or seller';

alter table users
    owner to admin;

create index users_deleted_at_index
    on users (deleted_at);

create index users_document_email_index
    on users (document, email);

create table wallet
(
    id         serial
        constraint wallet_pk
            primary key,
    user_id    integer                        not null
        constraint wallet_users_id_fk
            references users,
    balance    double precision default 0.0,
    created_at timestamp        default now() not null,
    updated_at timestamp        default now() not null,
    deleted_at integer
);

alter table wallet
    owner to admin;

create table transactions
(
    id         serial
        constraint transactions_pk
            primary key,
    payer_id   integer                                          not null
        constraint transactions_users_id_fk
            references users,
    payee_id   integer                                          not null
        constraint transactions_users_id_fk2
            references users,
    value      double precision                                 not null,
    status     varchar(20) default 'PENDING'::character varying not null,
    created_at timestamp   default now()                        not null,
    updated_at timestamp   default now()                        not null,
    deleted_at timestamp,
    notified   boolean     default false                        not null
);

alter table transactions
    owner to admin;


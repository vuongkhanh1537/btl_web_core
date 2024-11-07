CREATE SCHEMA `assign_db` ;


CREATE table  assign_db.customer(
	customer_id int  NOT NULL AUTO_INCREMENT primary key,
    name_ varchar(255) NOT NULL,
    password_ varchar(255) NOT NULL,
    username varchar(255) NOT NULL unique,
    gender char(1) NOT NULL,
    birthday DATE NOT NULL,
    email varchar(255) NOT NULL unique
);

CREATE table  assign_db.manager(
	empolyeeID int  NOT NULL AUTO_INCREMENT primary key,
    name_ varchar(255) NOT NULL,
    password_ varchar(255) NOT NULL,
    username varchar(255) NOT NULL unique,
    gender char(1) NOT NULL,
    birthday DATE NOT NULL,
    email varchar(255) NOT NULL unique
);

CREATE table  assign_db.product(
	product_id int  NOT NULL AUTO_INCREMENT primary key,
    name_ varchar(255) NOT NULL,
    price int NOT NULL,
    color varchar(255) NOT NULL,
    descriptioin mediumtext NOT NULL,
    weight int NOT NULL,
    size int NOT NULL,
    quantity int NOT NULL,
    category enum ('Shoes', 'Stocks','Sneaker')
);


CREATE table  assign_db.collection_(
    collection_id int  NOT NULL AUTO_INCREMENT primary key,
    name_ varchar(255) NOT NULL,
);


CREATE table  assign_db.compriesof(
    collection_id int  NOT NULL AUTO_INCREMENT primary key,
    product_id int  NOT NULL AUTO_INCREMENT primary key,
    FOREIGN KEY (customer_id) references customer(customer_id) On update restrict on delete restrict,
    FOREIGN KEY (customer_id) references customer(customer_id) On update restrict on delete restrict,
    CONSTRAINT pk_create PRIMARY KEY (cart_id, customer_id)

);



create table  assign_db.order_(
	order_id int NOT NULL primary key auto_increment,
    order_time datetime NOT NULL,
    shipment_time datetime ,
    ship_fee double not null,
    payment_method varchar(255) not null,
    payment_time datetime,
    status_ ENUM('Completed', 'Shipping', 'Cancelled') not null,
    address varchar(255) not null,
    customer_id int not null,
    FOREIGN KEY (customer_id) references customer(customer_id) On update restrict on delete restrict
);

create table  assign_db.promotion_code(
	code_id int NOT NULL primary key,
    name_ varchar(255) NOT NULL,
    start_date datetime NOT NULL, 
    end_date datetime NOT NULL,
    min_order int NOT NULL,
    maximum_promo int NOT NULL,
    promo_value double NOT NULL,
    init_quantity int NOT NULL
);

create table  assign_db.cart(
	cart_id int NOT NULL primary key
   
);

create table assign_db.create_(
	cart_id int not NULL,
    customer_id int not NULL,
    foreign key (customer_id) references customer(customer_id) On update restrict on delete restrict,
    foreign key (cart_id) references cart(cart_id) On update cascade on delete cascade,
    CONSTRAINT pk_create PRIMARY KEY (cart_id, customer_id)
);

create table  assign_db.make(
	order_id int not NULL primary key,
    customer_id int not NULL,
    foreign key (customer_id) references customer(customer_id) On update restrict on delete restrict,
    foreign key (order_id) references order_(order_id) On update cascade on delete cascade
);

create table  assign_db.apply_for(
	order_id int not NULL primary key,
    promotion_code_id int not NULL,
    foreign key (order_id) references order_(order_id) On update cascade on delete cascade,
    foreign key (promotion_code_id) references promotion_code(code_id) On update restrict on delete restrict
);

create table  assign_db.contain(
	order_id int not NULL,
    product_id int not NULL,
    quantity int not NULL,
    foreign key (order_id) references order_(order_id) On update cascade on delete cascade,
    foreign key (product_id) references product(product_id) On update restrict on delete restrict,
    CONSTRAINT pk_contain PRIMARY KEY (order_id, product_id)
);

create table  assign_db.consisted(
	cart_id int not NULL ,
    product_id int not NULL,
    quantity int not NULL,
    foreign key (product_id) references product(product_id) On update cascade on delete cascade,
    foreign key (cart_id) references cart(cart_id) On update restrict on delete restrict,
	 CONSTRAINT pk_consisted PRIMARY KEY (cart_id, product_id)
);



CREATE table  assign_db.rate(
	customer_id int  NOT NULL,
    product_id int NOT NULL,
    score int NOT NULL,
    CONSTRAINT pk_rate PRIMARY KEY (customer_id, product_id),
    foreign key (customer_id) references customer(customer_id) On update restrict on delete restrict,
    foreign key (product_id) references product (product_id) On update restrict on delete restrict
);


CREATE table  assign_db.own (
	customer_id int  NOT NULL,
    promotion_code_id int NOT NULL,
    CONSTRAINT pk_own PRIMARY KEY (customer_id, promotion_code_id),
    foreign key (customer_id) references customer (customer_id) On update restrict on delete restrict,
    foreign key (promotion_code_id) references promotion_code (code_id) On update restrict on delete restrict
);


Create table assign_db.review(
	product_id int NOT NULL,
    ordinal_number int NOT NULL,
    content varchar(255) not null,
    time_ datetime not null,
    reviewer_id  int not null,
    constraint pk_review Primary key (product_id, ordinal_number),
	foreign key (product_id) references product (product_id) On update cascade on delete cascade,
    foreign key (reviewer_id) references customer(customer_id) On update restrict on delete restrict
);


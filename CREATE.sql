CREATE SCHEMA `assign_db` ;


CREATE table  user(
	user_id int  NOT NULL AUTO_INCREMENT primary key,
    name_ varchar(255) NOT NULL,
    password_ varchar(255) NOT NULL,
    role_ ENUM('customer', 'manager') NOT NULL,
    gender char(1) NOT NULL,
    birthday DATE NOT NULL,
    email varchar(255) NOT NULL unique
);


CREATE table  collection_(
    collection_id int  NOT NULL AUTO_INCREMENT primary key,
    name_ varchar(255) NOT NULL
);

CREATE table  product(
	product_id int  NOT NULL AUTO_INCREMENT primary key,
    name_ varchar(255) NOT NULL,
    price int NOT NULL,
    color varchar(255) NOT NULL,
    brand varchar(255) NOT NULL,
    description_ text NOT NULL,
    image_path text NOT NULL,
    weight_ int NOT NULL,
    size_ int NOT NULL,
    quantity int NOT NULL,
    category enum ('Shoes', 'Stocks','Sneaker'),
    collection_id int  NOT NULL,
    FOREIGN KEY (collection_id) references collection_(collection_id) On update restrict on delete restrict
);

create table  promotion_code(
	code_id int NOT NULL primary key,
    name_ varchar(255) NOT NULL,
    start_date datetime NOT NULL, 
    end_date datetime NOT NULL,
    min_order int NOT NULL,
    maximum_promo int NOT NULL,
    promo_value double NOT NULL,
    init_quantity int NOT NULL
);


create table  order_(
	order_id int NOT NULL primary key auto_increment,
    order_time datetime NOT NULL,
    shipment_time datetime ,
    ship_fee double not null,
    payment_status ENUM('Completed', 'Not Completed', 'Cancelled') not null,
    total_payment int not null,
    payment_method varchar(255) not null,
    payment_time datetime,
    status_ ENUM('Completed', 'Shipping', 'Cancelled') not null,
    address_ varchar(255) not null,
    user_id int not null,
    promotion_code_id int,
    discount int,
    FOREIGN KEY (user_id) references user(user_id) On update restrict on delete restrict
);


create table  cart(
	cart_id int NOT NULL AUTO_INCREMENT primary key,
    user_id int not NULL,
    foreign key (user_id) references user(user_id) On update restrict on delete restrict
);


create table  contain(
	order_id int not NULL,
    product_id int not NULL,
    quantity int not NULL,
    price int not NULL,
    foreign key (order_id) references order_(order_id) On update cascade on delete cascade,
    foreign key (product_id) references product(product_id) On update restrict on delete restrict,
    CONSTRAINT pk_contain PRIMARY KEY (order_id, product_id)
);

create table  consisted(
	cart_id int not NULL ,
    product_id int not NULL,
    quantity int not NULL,
    foreign key (product_id) references product(product_id) On update cascade on delete cascade,
    foreign key (cart_id) references cart(cart_id) On update restrict on delete restrict,
	 CONSTRAINT pk_consisted PRIMARY KEY (cart_id, product_id)
);


CREATE table  own (
	user_id int  NOT NULL,
    promotion_code_id int NOT NULL,
    CONSTRAINT pk_own PRIMARY KEY (user_id, promotion_code_id),
    foreign key (user_id) references user (user_id) On update restrict on delete restrict,
    foreign key (promotion_code_id) references promotion_code (code_id) On update restrict on delete restrict
);


Create table review(
	product_id int NOT NULL,
    ordinal_number int NOT NULL,
    content varchar(255) not null,
    time_ datetime not null,
    reviewer_id  int not null,
    score int NOT NULL,
    constraint pk_review Primary key (product_id, ordinal_number),
	foreign key (product_id) references product (product_id) On update cascade on delete cascade,
    foreign key (reviewer_id) references user(user_id) On update restrict on delete restrict
);


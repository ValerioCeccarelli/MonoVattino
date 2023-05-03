DROP TABLE users


-- this file is used to save the sql statements for the project

CREATE TABLE users (
	username VARCHAR(20) NOT NULL,
	email VARCHAR(50) PRIMARY KEY,
	password VARCHAR(64) NOT NULL, -- password is hashed with sha256 so it is 64 characters long
	salt VARCHAR(10) NOT NULL -- salt is used to hash the password and in the algorithm is a 10 character string
);

INSERT INTO users (username, email, password, salt) VALUES ('test', 'test@test', 'test', 'test');
-- Could not execute the query: ERROR: duplicate key value violates unique constraint "users_pkey" DETAIL: Key (email)=(test@test) already exists.


CREATE TABLE companies (
	id SERIAL PRIMARY KEY,
	name VARCHAR(50) NOT NULL,
	email VARCHAR(50) NOT NULL,
	phone_number VARCHAR(20) NOT NULL,
	website VARCHAR(100) NOT NULL,
	color VARCHAR(6) NOT NULL,

	cost_per_km INTEGER NOT NULL,
	fixed_cost INTEGER NOT NULL
);

INSERT INTO companies (name, email, phone_number, website, color, cost_per_km, fixed_cost) VALUES ('mirko_scuscu', 'contanct@mirko.com', '123456789', 'www.mirko_scuscu.com', 'FF0000', 0.5, 1);
INSERT INTO companies (name, email, phone_number, website, color, cost_per_km, fixed_cost) VALUES ('vale_brumbrum', 'info@vale.it', '123456789', 'www.vale.brumbrum.com', '00FF00', 0.7, 0.9);
INSERT INTO companies (name, email, phone_number, website, color, cost_per_km, fixed_cost) VALUES ('ergrande', 'noreplay@ergrande.en', '123456789', 'www.ergrande.com', '0000FF', 0.3, 1.1);

CREATE TABLE scooters (
	id SERIAL PRIMARY KEY,
	latitude FLOAT NOT NULL,
	longitude FLOAT NOT NULL,
	battery_level FLOAT NOT NULL,
	company INTEGER NOT NULL,

	FOREIGN KEY (company) REFERENCES companies(id)
);

INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41, 12, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (42, 12, 10, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41, 13, 50, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (42, 13, 20, 2);

-- insert scooter with latitude between 41 and 42 and longitude between 12 and 13
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41 + random() * (42 - 41), 12 + random() * (13 - 12), 100, 1);

-- insert scooter with latitude between 41 and 42 and longitude between 12 and 13
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41 + random() * (42 - 41), 12 + random() * (13 - 12), 100, 2);


CREATE TABLE trips (
	start_time TIMESTAMP NOT NULL,
	scooter_id INTEGER PRIMARY KEY,
	user_email VARCHAR(50) NOT NULL,

	FOREIGN KEY (scooter_id) REFERENCES scooters(id),
	FOREIGN KEY (user_email) REFERENCES users(email)
);


------------------------------------------------------------------------------

CREATE TABLE my_points (
	id SERIAL PRIMARY KEY,
	latitude NUMERIC(10, 8) NOT NULL,
	longitude NUMERIC(11, 8) NOT NULL
);

INSERT INTO my_points (latitude, longitude) VALUES (1, 1);

CREATE EXTENSION postgis;

SELECT * FROM my_points WHERE ST_DWithin(ST_MakePoint(0, 1), ST_MakePoint(latitude, longitude), 0.5);

SELECT ST_Distance(
    'SRID=4326;POINT(-72.1235 42.3521)'::geometry,
    'SRID=4326;LINESTRING(-72.1260 42.45, -72.123 42.1546)'::geometry );

SELECT ST_Distance(
    ST_MakePoint(0, 1),
    ST_MakePoint(1, 0));
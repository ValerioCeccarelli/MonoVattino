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

	cost_per_km NUMERIC(5, 2) NOT NULL,
	fixed_cost NUMERIC(5, 2) NOT NULL,
);

INSERT INTO companies (name, email, phone_number, website, cost_per_km, fixed_cost) VALUES ('test_company', 'test@test', '123456789', 'test.com', 0.5, 1);
INSERT INTO companies (name, email, phone_number, website, cost_per_km, fixed_cost) VALUES ('test_company_2', 'test2@test2', '123456789', 'test2.com', 0.7, 0.9);


CREATE TABLE scooters (
	id SERIAL PRIMARY KEY,
	latitude NUMERIC(10, 8) NOT NULL,
	longitude NUMERIC(11, 8) NOT NULL,
	battery_level NUMERIC(3, 2) NOT NULL,
	is_available BOOLEAN NOT NULL,
	company INTEGER NOT NULL,

	owner_email VARCHAR(50), -- can be null if the scooter is not owned by anyone

	FOREIGN KEY (company) REFERENCES companies(company_id),
	FOREIGN KEY (owner_email) REFERENCES users(email)
);

INSERT INTO scooters (latitude, longitude, battery_level, is_available, company, owner_email) VALUES (1, 1, 100, true, 1, NULL);
INSERT INTO scooters (latitude, longitude, battery_level, is_available, company, owner_email) VALUES (2, 1, 10, true, 1, NULL);
INSERT INTO scooters (latitude, longitude, battery_level, is_available, company, owner_email) VALUES (1, 2, 50, true, 2, NULL);
INSERT INTO scooters (latitude, longitude, battery_level, is_available, company, owner_email) VALUES (2, 2, 20, true, 2, NULL);
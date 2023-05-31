DROP TABLE IF EXISTS trips;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS issues;
DROP TABLE IF EXISTS scooters;
DROP TABLE IF EXISTS companies;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS payment_methods;

CREATE TABLE payment_methods (
	id SERIAL PRIMARY KEY,
	card_number VARCHAR(16) NOT NULL,
	month INTEGER NOT NULL,
	year INTEGER NOT NULL,
	cvv INTEGER NOT NULL,
	owner VARCHAR(50) NOT NULL
);

INSERT INTO payment_methods (card_number, month, year, cvv, owner) VALUES ('1234567890123456', 1, 25, 123, 'Utente Uno'); -- id=1
INSERT INTO payment_methods (card_number, month, year, cvv, owner) VALUES ('1234567890123456', 12, 26, 456, 'Admin Uno'); -- id=2
INSERT INTO payment_methods (card_number, month, year, cvv, owner) VALUES ('1234567890123456', 12, 26, 789, 'Utente Due'); -- id=3

CREATE TABLE users (
	username VARCHAR(20) NOT NULL,
	email VARCHAR(50) PRIMARY KEY,
	password VARCHAR(64) NOT NULL,
	salt VARCHAR(10) NOT NULL,

	privacy_policy_accepted BOOLEAN NOT NULL,
	terms_and_conditions_accepted BOOLEAN NOT NULL,

	payment_method INTEGER,

	name VARCHAR(50) NOT NULL,
	surname VARCHAR(50) NOT NULL,
	date_of_birth VARCHAR(20) NOT NULL,
	phone_number VARCHAR(20) NOT NULL,

	map_theme VARCHAR(10) NOT NULL,
	html_theme VARCHAR(10) NOT NULL,

	is_admin BOOLEAN NOT NULL,
	language VARCHAR(10) NOT NULL,

	FOREIGN KEY (payment_method) REFERENCES payment_methods(id) ON DELETE SET NULL
);

INSERT INTO users (username, email, password, salt, privacy_policy_accepted, terms_and_conditions_accepted, payment_method, name, surname, date_of_birth, phone_number, map_theme, html_theme, is_admin, language) 
VALUES ('User1', 'user1@mail.com', 'df11b78fa15baaad27580ac89635af7b4643705a9724f9359ed7a6e4c6227932', 'gHi5afIvAu', true, true, 1, 'Name1', 
'Surname1', '2001-01-01', '1234567890', 'default', 'light', false, 'en'); -- password=Aa123456!
INSERT INTO users (username, email, password, salt, privacy_policy_accepted, terms_and_conditions_accepted, payment_method, name, surname, date_of_birth, phone_number, map_theme, html_theme, is_admin, language) 
VALUES ('Admin1', 'admin1@mail.com', 'df11b78fa15baaad27580ac89635af7b4643705a9724f9359ed7a6e4c6227932', 'gHi5afIvAu', true, true, 2, 'Admin', 
'Surname', '2001-01-01', '1234567890', 'default', 'light', true, 'en'); -- password=Aa123456!
INSERT INTO users (username, email, password, salt, privacy_policy_accepted, terms_and_conditions_accepted, payment_method, name, surname, date_of_birth, phone_number, map_theme, html_theme, is_admin, language) 
VALUES ('User1', 'user2@mail.com', 'df11b78fa15baaad27580ac89635af7b4643705a9724f9359ed7a6e4c6227932', 'gHi5afIvAu', true, true, 3, 'Name2', 
'Surname2', '2001-01-01', '1234567890', 'default', 'light', false, 'en'); -- password=Aa123456!

CREATE TABLE companies (
	id SERIAL PRIMARY KEY,
	name VARCHAR(50) NOT NULL,
	email VARCHAR(50) NOT NULL,
	phone_number VARCHAR(20) NOT NULL,
	website VARCHAR(100) NOT NULL,
	color VARCHAR(6) NOT NULL,

	cost_per_minute FLOAT NOT NULL,
	fixed_cost FLOAT NOT NULL
);

INSERT INTO companies (name, email, phone_number, website, color, cost_per_minute, fixed_cost) VALUES ('Beam', 'beam@beam.com', '123456789', 'www.beam.com', '2876dd', 0.4, 1);
INSERT INTO companies (name, email, phone_number, website, color, cost_per_minute, fixed_cost) VALUES ('Dott', 'dott@dott.com', '123456789', 'www.dott.com', 'ffb300', 0.2, 1.1);
INSERT INTO companies (name, email, phone_number, website, color, cost_per_minute, fixed_cost) VALUES ('Lime', 'lime@lime.com', '123456789', 'www.lime.com', '87bc24', 0.3, 0.9);
INSERT INTO companies (name, email, phone_number, website, color, cost_per_minute, fixed_cost) VALUES ('Lyft', 'lift@lift.com', '123456789', 'www.lift.com', 'd81900', 0.4, 1.2);
INSERT INTO companies (name, email, phone_number, website, color, cost_per_minute, fixed_cost) VALUES ('Spin', 'spin@spin.com', '123456789', 'www.spin.com', '76c798', 0.3, 0.8);
INSERT INTO companies (name, email, phone_number, website, color, cost_per_minute, fixed_cost) VALUES ('Bird', 'bird@bird.com', '123456789', 'www.bird.com', 'c69c6d', 0.2, 1.3);

CREATE TABLE scooters (
	id SERIAL PRIMARY KEY,
	latitude FLOAT NOT NULL,
	longitude FLOAT NOT NULL,
	battery_level FLOAT NOT NULL,
	company INTEGER NOT NULL,

	FOREIGN KEY (company) REFERENCES companies(id)
);

INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.87866923533086, 12.57117482817291, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.79329220325277, 12.358079640200907, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.87134129480741, 12.294765884673856, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.78461051955588, 12.33746083452197, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.894572972941646, 12.370511436089876, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.84029539679121, 12.366118304431355, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.968491687952735, 12.49981263188479, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.95072645809013, 12.355341184477568, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.925201409171294, 12.333131540488893, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.846965448003594, 12.486864767746505, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.940367177661265, 12.41086676383526, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.801219788048066, 12.572581393610761, 100, 4);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.79357116425402, 12.420407218450672, 100, 5);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.853616173950165, 12.584597757144758, 100, 6);

CREATE TABLE issues (
	id SERIAL PRIMARY KEY,
	scooter_id INTEGER NOT NULL,
	user_email VARCHAR(50) NOT NULL,
	title VARCHAR(50) NOT NULL,
	description VARCHAR(500) NOT NULL,
	status VARCHAR(20) NOT NULL,
	created_at TIMESTAMP NOT NULL,

	FOREIGN KEY (scooter_id) REFERENCES scooters(id),
	FOREIGN KEY (user_email) REFERENCES users(email)
);

INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (1, 'user1@mail.com', 'Title1', 'Description1', 'open', '2023-05-29 12:01:26.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (2, 'user1@mail.com', 'Title2', 'Description2', 'accepted', '2023-05-28 12:01:26.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (3, 'user1@mail.com', 'Title3', 'Description3', 'open', '2023-05-26 12:01:26.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (4, 'user1@mail.com', 'Title4', 'Description4', 'accepted', '2023-05-24 12:01:26.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (5, 'user1@mail.com', 'Title5', 'Description5', 'accepted', '2023-05-22 12:01:26.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (6, 'user1@mail.com', 'Title6', 'Description6', 'open', '2023-05-29 10:01:26.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (7, 'user1@mail.com', 'Title7', 'Description7', 'open', '2023-05-29 12:02:26.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (8, 'user1@mail.com', 'Title8', 'Description8', 'open', '2023-05-29 12:01:26.214972');

INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (9, 'user2@mail.com', 'Title10', 'Description10', 'open', '2023-05-30 12:01:26.314972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (10, 'user2@mail.com', 'Title20', 'Description20', 'open', '2023-05-29 12:02:26.314972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (11, 'user2@mail.com', 'Title30', 'Description30', 'accepted', '2023-05-27 12:01:36.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (12, 'user2@mail.com', 'Title40', 'Description40', 'accepted', '2023-05-25 12:01:36.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (13, 'user2@mail.com', 'Title50', 'Description50', 'open', '2023-05-23 12:01:36.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (10, 'user2@mail.com', 'Title60', 'Description60', 'open', '2023-05-21 12:01:36.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (11, 'user2@mail.com', 'Title70', 'Description70', 'accepted', '2023-05-19 12:01:36.214972');
INSERT INTO issues (scooter_id, user_email, title, description, status, created_at) VALUES (12, 'user2@mail.com', 'Title80', 'Description80', 'open', '2023-05-17 12:01:36.214972');


CREATE TABLE reservations (
	start_time TIMESTAMP NOT NULL,
	scooter_id INTEGER PRIMARY KEY,
	user_email VARCHAR(50) NOT NULL,

	FOREIGN KEY (scooter_id) REFERENCES scooters(id),
	FOREIGN KEY (user_email) REFERENCES users(email)
);

-- INSERT INTO reservations(start_time, scooter_id, user_email) VALUES ('2023-05-29 12:01:26.214972', 1, 'user1@mail.com');
-- INSERT INTO reservations(start_time, scooter_id, user_email) VALUES ('2023-05-29 12:01:26.214972', 1, 'user1@mail.com');
-- INSERT INTO reservations(start_time, scooter_id, user_email) VALUES ('2023-05-29 12:01:26.214972', 1, 'user1@mail.com');

CREATE TABLE trips (
	id SERIAL PRIMARY KEY,
	trip_time INTEGER NOT NULL,
	scooter_id INTEGER NOT NULL,
	user_email VARCHAR(50) NOT NULL,
	date TIMESTAMP NOT NULL,

	FOREIGN KEY (scooter_id) REFERENCES scooters(id),
	FOREIGN KEY (user_email) REFERENCES users(email)
);

INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 1, 'user1@mail.com', '2023-05-29 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (401, 2, 'user1@mail.com', '2023-05-01 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (11111, 12, 'user1@mail.com', '2023-05-02 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (12042, 4, 'user1@mail.com', '2023-05-03 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (130423, 13, 'user1@mail.com', '2023-05-04 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (42069, 14, 'user1@mail.com', '2023-05-05 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (1006, 7, 'user1@mail.com', '2023-05-06 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (33333, 8, 'user1@mail.com', '2001-05-21 12:00:00.000000');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (1000, 8, 'user1@mail.com', '2001-12-12 12:00:00.000000');

INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 7, 'user2@mail.com', '2023-05-29 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (401, 6, 'user2@mail.com', '2023-05-01 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (11111, 12, 'user2@mail.com', '2023-05-02 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (12042, 13, 'user2@mail.com', '2023-05-03 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (130423, 3, 'user2@mail.com', '2023-05-04 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (42069, 14, 'user2@mail.com', '2023-05-05 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (1006, 1, 'user2@mail.com', '2023-05-06 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (33333, 8, 'user2@mail.com', '2001-05-21 12:00:00.000000');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (1000, 8, 'user2@mail.com', '2001-12-12 12:00:00.000000');

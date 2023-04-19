-- this file is used to save the sql statements for the project

CREATE TABLE users (
	username VARCHAR(20) NOT NULL,
	email VARCHAR(50) PRIMARY KEY,
	password VARCHAR(64) NOT NULL, -- password is hashed with sha256 so it is 64 characters long
	salt VARCHAR(10) NOT NULL -- salt is used to hash the password and in the algorithm is a 10 character string
);

INSERT INTO users (username, email, password, salt) VALUES ('test', 'test@test', 'test', 'test');
-- Could not execute the query: ERROR: duplicate key value violates unique constraint "users_pkey" DETAIL: Key (email)=(test@test) already exists.
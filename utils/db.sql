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
VALUES ('Utente1', 'utente1@mail.com', 'a0357a29d984018382037f49b65b63b7f3a8df2e9cc1768f6959fbf0107e124d', '6HZFd7Supf', true, true, 1, 'Utente', 
'Utente1', '2001-01-01', '1234567890', 'default', 'light', false, 'en'); -- password=Aa123456
INSERT INTO users (username, email, password, salt, privacy_policy_accepted, terms_and_conditions_accepted, payment_method, name, surname, date_of_birth, phone_number, map_theme, html_theme, is_admin, language) 
VALUES ('Admin1', 'admin1@mail.com', 'd0fbb73db6f36b3bd7dbc8718739b14a429deb8d9af5bf5d1e4935b7d67f93df', 'AElYuBzCtP', true, true, 2, 'Admin', 
'Cognome', '2001-01-01', '1234567890', 'default', 'light', true, 'en'); -- password=Aa123456
INSERT INTO users (username, email, password, salt, privacy_policy_accepted, terms_and_conditions_accepted, payment_method, name, surname, date_of_birth, phone_number, map_theme, html_theme, is_admin, language) 
VALUES ('Utente2', 'utente2@mail.com', 'ebfd8cd9e833727adca6ef2e3758ef331ffa57b91f884d0b4a2822f980cd19eb', 'bkKCbSoFtD', true, true, 3, 'Utente', 
'Utente2', '2001-01-01', '1234567890', 'default', 'light', false, 'en'); -- password=Aa123456

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

INSERT INTO companies (name, email, phone_number, website, color, cost_per_minute, fixed_cost) VALUES ('mirko_scuscu', 'contanct@mirko.com', '123456789', 'www.mirko_scuscu.com', 'FF0000', 0.5, 1);
INSERT INTO companies (name, email, phone_number, website, color, cost_per_minute, fixed_cost) VALUES ('vale_brumbrum', 'info@vale.it', '123456789', 'www.vale.brumbrum.com', '00FF00', 0.7, 0.9);
INSERT INTO companies (name, email, phone_number, website, color, cost_per_minute, fixed_cost) VALUES ('ergrande', 'noreplay@ergrande.en', '123456789', 'www.ergrande.com', '0000FF', 0.3, 1.1);

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
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.801219788048066, 12.572581393610761, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.79357116425402, 12.420407218450672, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.853616173950165, 12.584597757144758, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.91113594218642, 12.591354449511066, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.829710806949265, 12.303953240058554, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.778365058081725, 12.3083164054336, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.91046027969479, 12.60953023549555, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.9038261865233, 12.40534878509933, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.83925347952679, 12.309208231686231, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.937698910410845, 12.343863948495107, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.82289245192223, 12.295919229849247, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.82245094203747, 12.406498022969368, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.789552590003524, 12.327090097197178, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.793474406340565, 12.503999494977075, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.91312369250972, 12.296484513121088, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.949921273733835, 12.440507399684872, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.85107764654462, 12.59952793383126, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.82211912674968, 12.432816085585038, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.897975168685576, 12.650927220013754, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.966298156897835, 12.40007198111476, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.86299926121976, 12.415291125765817, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.92267642382452, 12.517773148106677, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.79978344304197, 12.549331012355019, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.81539743442314, 12.401738015720012, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.98947022382919, 12.47451241143457, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.88813265915425, 12.430195149514836, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.886780669219114, 12.329596079089605, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.94972006041768, 12.59649664446051, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.95828712606894, 12.596880352985496, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.813603736590984, 12.379605336004204, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (42.007736387318715, 12.47679248067379, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.90245343116377, 12.32353843573915, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.97915860139535, 12.523826072408623, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.931662691079666, 12.32587933227572, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.830852832595994, 12.444007083149963, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.96875087630538, 12.425333023866317, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.95313751367014, 12.427080011725007, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.92532625718663, 12.63951123193771, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.97210604374567, 12.602865844434776, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.89383435021235, 12.286073378761413, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.94175161876493, 12.377965734319016, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.89164042416, 12.48645325369049, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.83798413892619, 12.461697644409057, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.908577818421804, 12.488036379289161, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.83899523536424, 12.450818927287798, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.78260594030076, 12.318043028945544, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.93715102812462, 12.56363210324287, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.9530828074916, 12.436169349532568, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.87084287447369, 12.458490722310687, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.998869997536545, 12.494522118845591, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.80664743050078, 12.528095386788024, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.94466012811955, 12.38292948121342, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.84388267259984, 12.60969222508962, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.93650681244391, 12.58010363720527, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.96526911737446, 12.38634493191402, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.78520135254394, 12.622088396372696, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.937540596180675, 12.549764518524308, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.806115847763195, 12.314610520949826, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (42.00372205285525, 12.379798609937016, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.954440270707934, 12.29090031964862, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.89296888614064, 12.492666187965629, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.88360721628934, 12.611054573123003, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.93305217832327, 12.337023384128893, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.898671418703046, 12.637097291906151, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.85342196923101, 12.38203881025919, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.84097156818935, 12.340279575140606, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.88013827411031, 12.5387363349748, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.77976470135376, 12.377834557771003, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.80625186620533, 12.623686427659598, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.7786997169936, 12.628062434539896, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.96106977467316, 12.477172472165883, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.79803942789407, 12.325128108424288, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.958535179784924, 12.611703878944123, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.90821267689843, 12.335149495163153, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.81073069672248, 12.487394331614299, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.971958917302146, 12.535554168726586, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.940842235771214, 12.430880757034501, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.85919265558328, 12.531329948439783, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.78518513427772, 12.41924125215754, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.98042797034344, 12.64700849918128, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.972432774333065, 12.597702817001306, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.89913341392911, 12.576941726989732, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (42.01034195742784, 12.290739453603473, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.823535277721234, 12.489200641656318, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.832369590672705, 12.361109148140187, 100, 1);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.82150124094103, 12.348476229822422, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.83943943888112, 12.460035397993927, 100, 3);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.91059073562172, 12.55949614282705, 100, 2);
INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES (41.81337129790749, 12.432355659015847, 100, 3);

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
	trip_time INTEGER NOT NULL,
	scooter_id INTEGER PRIMARY KEY,
	user_email VARCHAR(50) NOT NULL,
	date TIMESTAMP NOT NULL,

	FOREIGN KEY (scooter_id) REFERENCES scooters(id),
	FOREIGN KEY (user_email) REFERENCES users(email)
);

INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 1, 'user1@mail.com', '2023-05-29 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 2, 'user1@mail.com', '2023-05-01 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 3, 'user1@mail.com', '2023-05-02 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 4, 'user1@mail.com', '2023-05-03 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 5, 'user1@mail.com', '2023-05-04 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 6, 'user1@mail.com', '2023-05-05 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 7, 'user1@mail.com', '2023-05-06 12:01:26.214972');

INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 7, 'user2@mail.com', '2023-05-29 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 6, 'user2@mail.com', '2023-05-01 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 5, 'user2@mail.com', '2023-05-02 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 4, 'user2@mail.com', '2023-05-03 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 3, 'user2@mail.com', '2023-05-04 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 2, 'user2@mail.com', '2023-05-05 12:01:26.214972');
INSERT INTO trips (trip_time, scooter_id, user_email, date) VALUES (100, 1, 'user2@mail.com', '2023-05-06 12:01:26.214972');

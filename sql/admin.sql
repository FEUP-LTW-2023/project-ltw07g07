DROP TABLE user IF EXISTS;
CREATE TABLE user (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  status TEXT NOT NULL,
  name TEXT NOT NULL,
  username TEXT UNIQUE NOT NULL,
  password TEXT NOT NULL,
  email TEXT NOT NULL
);

DROP TABLE department IF EXISTS
CREATE TABLE department (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE
);

DROP TABLE ticket IF EXISTS
CREATE TABLE ticket (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id INTEGER NOT NULL,
  department_id INTEGER NOT NULL,
  hashtags VARCHAR NOT NULL,
  message TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'Open',
  priority TEXT NOT NULL DEFAULT 'Low',
  assigned_to INTEGER,
  FOREIGN KEY (client_id) REFERENCES users(id),
  FOREIGN KEY (assigned_to) REFERENCES users(id),
  FOREIGN KEY (department_id) REFERENCES departments(id)
);

DROP TABLE faq IF EXISTS
CREATE TABLE faq (
  id INTEGER PRIMARY KEY,
  client_id INTEGER NOT NULL,
  message TEXT NOT NULL,
  FOREIGN KEY (client_id) REFERENCES users(id)
);



INSERT INTO user VALUES (1, 'Admin', 'Goncalo', 'Guca', 'ltw2023', 'goncalo');
INSERT INTO user VALUES (2, 'Client', 'Rodrigo', 'Roger', 'ltw1', 'rodrigo');
INSERT INTO department VALUES (1, 'Propinas');
INSERT INTO ticket VALUES (1, 2, 1, 'Faculdade', 'Ja paguei', 'Open', 'Low', 1);



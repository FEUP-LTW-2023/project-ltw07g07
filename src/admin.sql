DROP TABLE IF EXISTS user;
CREATE TABLE user (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  status TEXT NOT NULL,
  name TEXT NOT NULL,
  username TEXT UNIQUE NOT NULL,
  password TEXT NOT NULL,
  email TEXT NOT NULL,
  department TEXT,
  closed INTEGER DEFAULT 0
);

DROP TABLE IF EXISTS department;
CREATE TABLE department (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE
);

DROP TABLE IF EXISTS ticket;
CREATE TABLE ticket (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id INTEGER NOT NULL,
  department TEXT,
  hashtags VARCHAR,
  message TEXT NOT NULL,
  status TEXT DEFAULT 'Open',
  priority TEXT DEFAULT 'Low',
  assigned_to INTEGER,
  FOREIGN KEY (client_id) REFERENCES users(id),
  FOREIGN KEY (assigned_to) REFERENCES users(id)
);

DROP TABLE IF EXISTS faq;
CREATE TABLE faq (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  question TEXT NOT NULL,
  answer TEXT NOT NULL
);

DROP TABLE IF EXISTS reply;
CREATE TABLE reply (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id INTEGER NOT NULL,
  message TEXT NOT NULL,
  ticket_id INTEGER NOT NULL,
  FOREIGN KEY (client_id) REFERENCES users(id),
  FOREIGN KEY (ticket_id) REFERENCES tickets(id)
);

DROP TABLE IF EXISTS hashtag;
CREATE TABLE hashtag (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL
);
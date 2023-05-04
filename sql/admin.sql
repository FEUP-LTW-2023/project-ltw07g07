CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  status TEXT NOT NULL,
  name TEXT NOT NULL,
  username TEXT UNIQUE NOT NULL,
  password TEXT NOT NULL,
  email TEXT NOT NULL
);

CREATE TABLE departments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE
);

CREATE TABLE statuses (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE
);

CREATE TABLE tickets (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  client_id INTEGER NOT NULL,
  department TEXT NOT NULL,
  subject TEXT NOT NULL,
  message TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'Open',
  priority TEXT NOT NULL DEFAULT 'Low',
  assigned_to INTEGER,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES users(id),
  FOREIGN KEY (assigned_to) REFERENCES users(id),
  FOREIGN KEY (department) REFERENCES departments(id),
  FOREIGN KEY (status) REFERENCES statuses(id)
);

CREATE TABLE faq (
);

CREATE TABLE hashtags (
);

CREATE PROCEDURE assign_ticket_to_agent(
  IN ticket_id INTEGER,
  IN agent_id INTEGER
)

CREATE PROCEDURE assign_department_to_agent(
  IN department_id INTEGER,
  IN agent_id INTEGER
)





BEGIN
  INSERT INTO users (name, username, password, email)
  VALUES (user_name, user_username, user_password, user_email);
  
  /*SET @user_id = LAST_INSERT_ID();*/
  
  UPDATE tickets SET assigned_to = @user_id WHERE client_id = client_id;
  UPDATE tickets SET assigned_to = user_id WHERE id = ticket_id;
  

  INSERT INTO department_agents (department_id, agent_id)
  VALUES (department_id, @user_id);
  
  DELETE FROM users WHERE id = client_id;
END;




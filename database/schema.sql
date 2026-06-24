CREATE TABLE users (
  id INT AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('guest', 'user', 'admin') NOT NULL DEFAULT 'guest',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_email (email)
);

CREATE TABLE projects (
  id INT AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_name (name)
);

CREATE TABLE accounts (
  id INT AUTO_INCREMENT,
  project_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_project_id (project_id),
  FOREIGN KEY (project_id) REFERENCES projects(id)
);

CREATE TABLE expenses (
  id INT AUTO_INCREMENT,
  account_id INT NOT NULL,
  date DATE NOT NULL,
  description TEXT,
  amount DECIMAL(10, 2) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_account_id (account_id),
  FOREIGN KEY (account_id) REFERENCES accounts(id)
);

CREATE TABLE incomes (
  id INT AUTO_INCREMENT,
  account_id INT NOT NULL,
  date DATE NOT NULL,
  description TEXT,
  amount DECIMAL(10, 2) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_account_id (account_id),
  FOREIGN KEY (account_id) REFERENCES accounts(id)
);

INSERT INTO users (username, email, password, role)
VALUES ('admin', 'admin@example.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'admin');

INSERT INTO projects (name)
VALUES ('Project 1'), ('Project 2'), ('Project 3');

INSERT INTO accounts (project_id, name)
VALUES (1, 'Account 1'), (1, 'Account 2'), (2, 'Account 3');

INSERT INTO expenses (account_id, date, description, amount)
VALUES (1, '2022-01-01', 'Expense 1', 100.00), (2, '2022-01-02', 'Expense 2', 200.00);

INSERT INTO incomes (account_id, date, description, amount)
VALUES (1, '2022-01-01', 'Income 1', 500.00), (3, '2022-01-02', 'Income 2', 300.00);
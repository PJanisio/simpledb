CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL, -- Change 'name' to 'username'
    email VARCHAR(255) NOT NULL
);

INSERT INTO users (username, email) VALUES ('Alice', 'alice@example.com');
INSERT INTO users (username, email) VALUES ('Bob', 'bob@example.com');

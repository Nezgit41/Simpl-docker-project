-- init.sql
CREATE TABLE IF NOT EXISTS feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    phone_number VARCHAR(20),
    email VARCHAR(255),
    text TEXT,
    is_viewed BOOLEAN,
    created_at DATETIME
);

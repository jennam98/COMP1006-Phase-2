CREATE TABLE resumes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    position VARCHAR(100),
    skills TEXT,
    email VARCHAR(100),
    phone VARCHAR(20),
    bio TEXT
);
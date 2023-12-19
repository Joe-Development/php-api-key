CREATE TABLE licenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_key VARCHAR(255) NOT NULL,
    expiration_date TIMESTAMP NOT NULL,
    username VARCHAR(255) NOT NULL
);

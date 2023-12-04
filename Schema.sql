DROP DATABASE twitter;
CREATE DATABASE twitter;
USE twitter;

CREATE TABLE Users (
    u_id VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL,
    name VARCHAR(30) NOT NULL,
    created_at DATETIME NOT NULL,
    birth_date DATETIME,
    PRIMARY KEY (u_id)
);

CREATE TABLE Article (
    a_id VARCHAR(50) NOT NULL UNIQUE,
    content VARCHAR(1500) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    like_count INT,
    writer VARCHAR(30) NOT NULL,
    PRIMARY KEY (a_id),
    FOREIGN KEY (writer) REFERENCES Users(u_id) ON DELETE CASCADE
);

CREATE TABLE Comment (
    c_id VARCHAR(50) NOT NULL UNIQUE,
    u_id VARCHAR(30) NOT NULL,
    a_id VARCHAR(30) NOT NULL,
    content VARCHAR(500) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    like_count INT,
    PRIMARY KEY (c_id),
    FOREIGN KEY (u_id) REFERENCES Users(u_id) ON DELETE CASCADE,
    FOREIGN KEY (a_id) REFERENCES Article(a_id) ON DELETE CASCADE
);

CREATE TABLE Following (
    u_id VARCHAR(30) NOT NULL,
    following_id VARCHAR(30) NOT NULL,
    FOREIGN KEY (u_id) REFERENCES Users(u_id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES Users(u_id) ON DELETE CASCADE,
    PRIMARY KEY (u_id, following_id)
);
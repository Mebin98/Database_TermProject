DROP DATABASE twitter;
CREATE DATABASE twitter;
USE twitter;

CREATE TABLE Users (
    u_id VARCHAR(30) UNIQUE NOT NULL,
    password VARCHAR(50) NOT NULL,
    name VARCHAR(30) NOT NULL,
    created_at DATETIME,
    birth_date DATETIME,
    PRIMARY KEY (u_id)
);

CREATE TABLE Article (
    a_id INT AUTO_INCREMENT NOT NULL,
    content VARCHAR(1500),
    created_at DATETIME,
    updated_at DATETIME,
    writer VARCHAR(30),
    PRIMARY KEY (a_id),
    FOREIGN KEY (writer) REFERENCES Users(u_id) ON DELETE CASCADE
);

CREATE TABLE Article_like (
    a_id INT,
    u_id VARCHAR(30),
    PRIMARY KEY (a_id, u_id),
    FOREIGN KEY (a_id) REFERENCES Article(a_id) ON DELETE CASCADE,
    FOREIGN KEY (u_id) REFERENCES Users(u_id) ON DELETE CASCADE
);

CREATE TABLE Article_Tag (
    a_id INT,
    tag VARCHAR(50) NOT NULL,
    PRIMARY KEY (a_id, tag),
    FOREIGN KEY (a_id) REFERENCES Article(a_id) ON DELETE CASCADE,
);

CREATE TABLE Follow (
    u_id VARCHAR(30),
    following_id VARCHAR(30),
    PRIMARY KEY (u_id, following_id),
    FOREIGN KEY (u_id) REFERENCES Users(u_id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES Users(u_id) ON DELETE CASCADE
);

CREATE TABLE Comment (
    c_id INT AUTO_INCREMENT NOT NULL,
    u_id VARCHAR(30),
    a_id INT,
    content VARCHAR(500) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    PRIMARY KEY (c_id),
    FOREIGN KEY (u_id) REFERENCES Users(u_id) ON DELETE CASCADE,
    FOREIGN KEY (a_id) REFERENCES Article(a_id) ON DELETE CASCADE
);

CREATE TABLE Comment_like (
    c_id INT,
    u_id VARCHAR(30),
    PRIMARY KEY (c_id, u_id),
    FOREIGN KEY (c_id) REFERENCES Comment(c_id) ON DELETE CASCADE,
    FOREIGN KEY (u_id) REFERENCES Users(u_id) ON DELETE CASCADE,
);

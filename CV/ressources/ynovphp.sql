-- Create database
CREATE DATABASE ynovphp_db;

-- Use the database
USE ynovphp_db;

CREATE TABLE
    Users (
        Id VARCHAR(36) PRIMARY KEY,
        First_name VARCHAR(25),
        Last_name VARCHAR(25),
        Email VARCHAR(50),
        Password VARCHAR(50),
        UserRole VARCHAR(50),
        UserText varchar(255),
        PhoneNB varchar(20)
    );

CREATE TABLE
    Skills (
        Skill_ID VARCHAR(36) PRIMARY KEY,
        Title VARCHAR(25),
        Description VARCHAR(250),
        YearsXP INT
    );

CREATE TABLE
    ExpExternal (
        ExpExt_ID VARCHAR(36) PRIMARY KEY,
        Title VARCHAR(25),
        Start_Date DATE,
        End_Date DATE
    );

CREATE Table
    EducationExt (
        EducationExt_ID VARCHAR(36) PRIMARY KEY,
        School VARCHAR(25),
        Start_Date DATE,
        End_Date DATE
    );

CREATE TABLE
    CV (
        CV_ID VARCHAR(36) PRIMARY KEY,
        Title VARCHAR(25),
        Description VARCHAR(250),
        Skill_ID VARCHAR(36),
        EducationExt_ID VARCHAR(36),
        ExpExt_ID VARCHAR(36),
        FOREIGN Key (Skill_ID) REFERENCES Skills (Skill_ID),
        FOREIGN Key (ExpExt_ID) REFERENCES ExpExternal (ExpExt_ID),
        FOREIGN Key (EducationExt_ID) REFERENCES EducationExt (EducationExt_ID)
    );

CREATE TABLE
    Projects (
        Project_Id varchar(36),
        User_ID varchar(36),
        Title varchar(50),
        Subjects varchar(50),
        Description varchar(100),
        LinkImage varchar(50),
        FOREIGN Key (User_ID) REFERENCES Users(Id),
    );

INSERT INTO
    Users (
        Id,
        First_name,
        Last_name,
        Email,
        Password,
        UserRole
    )
Values
    (
        "aa70d94d-6b21-4b48-8b69-b5ecb66e3ab9",
        'John',
        'Doe',
        'john.doe@test.com',
        'testAccountPsw',
        'Admin'
    );

INSERT INTO
    Users (
        Id,
        First_name,
        Last_name,
        Email,
        Password,
        UserRole,
        UserText,
        PhoneNB
    )
Values
    (
        "dc8963d0-267f-4dc5-b582-38ba0ea287af",
        'Kyrian',
        'Delaplace',
        'kyrian.delaplace09@gmail.com',
        '$2y$10$v9EKKe4cKF1dRI2SzSGYvewUOEKWPzykmhNwtz3zUXrDbNpzvJnIa',
        'SimpleUser',
        'Je suis un jeune developpeur qui cherche un stage/stage alterné',
        '+33780049696'
    );

INSERT INTO
    EducationExt (EducationExt_ID, School, Start_Date, End_Date)
Values
    (
        "264187c9-9782-40c3-8b01-1d95cbc378d2",
        'ynov',
        '2024-03-18',
        '2025-08-19'
    );

INSERT INTO
    ExpExternal (ExpExt_ID, Title, Start_Date, End_Date)
Values
    (
        "09991ddd-6cb7-402c-8be8-64cebe5a3aa8",
        'test2',
        '2024-04-18',
        '2025-08-20'
    );

INSERT INTO
    Skills (Skill_ID, Title, Description, YearsXP,)
Values
    (
        "98873601-a51d-49d4-a09e-632e90171503",
        'test',
        'de',
        '2',
    );

INSERT INTO
    CV (
        CV_ID,
        User_ID,
        Title,
        Description,
        Skill_ID,
        EducationExt_ID,
        ExpExt_ID
    )
Values
    (
        "a5f7e686-2af4-4911-b0bb-e626d9d22cb8",
        'dc8963d0-267f-4dc5-b582-38ba0ea287af',
        'Master Info',
        'Je passe mon master, je cherche un boulot',
        '98873601-a51d-49d4-a09e-632e90171503',
        '264187c9-9782-40c3-8b01-1d95cbc378d2',
        '09991ddd-6cb7-402c-8be8-64cebe5a3aa8'
    );

INSERT INTO
    Projects (
        Project_Id,
        User_ID,
        Title,
        Subjects,
        Description,
    )
Values
    (
        "6c94e2e5-02db-4e03-85ec-0df5700b4bb1",
        'dc8963d0-267f-4dc5-b582-38ba0ea287af',
        'WebSite PhP for Ynov',
        'WebSite',
        'Here is my class project of making a website using html, php, css, js and docker',
    );
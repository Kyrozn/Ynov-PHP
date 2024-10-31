-- Use the database
USE ynovphp_db;

CREATE TABLE Users (
    Id VARCHAR(36) PRIMARY KEY,
    First_name VARCHAR(25),
    Last_name VARCHAR(25),
    Email VARCHAR(50),
    Password VARCHAR(100),
    UserRole VARCHAR(50),
    UserText text,
    PhoneNB varchar(20),
    PP_User text,
    BackgroundUser text
);

CREATE TABLE CV (
    CV_ID VARCHAR(36) PRIMARY KEY,
    User_ID VARCHAR(36),
    Title VARCHAR(25),
    Description VARCHAR(250)
);

CREATE TABLE Skills (
    Skill_ID VARCHAR(36) PRIMARY KEY,
    Title VARCHAR(50),
    Description text,
    YearsXP INT,
    CV_ID VARCHAR(36),
    FOREIGN KEY (CV_ID) REFERENCES CV (CV_ID)
);

CREATE TABLE ExpExternal (
    ExpExt_ID VARCHAR(36) PRIMARY KEY,
    Title VARCHAR(50),
    Description text,
    Start_Date DATE,
    End_Date DATE,
    CV_ID VARCHAR(36),
    FOREIGN KEY (CV_ID) REFERENCES CV (CV_ID)
);

CREATE TABLE EducationExt (
    EducationExt_ID VARCHAR(36) PRIMARY KEY,
    School VARCHAR(50),
    Start_Date DATE,
    End_Date DATE,
    CV_ID VARCHAR(36),
    FOREIGN KEY (CV_ID) REFERENCES CV (CV_ID)
);

CREATE TABLE Projects (
    Project_Id varchar(36) PRIMARY KEY,
    Title varchar(50),
    Subjects varchar(50),
    Description varchar(100),
    LinkImage varchar(50),
    Validate int(1)
);

CREATE TABLE ProjectsUsers (
    Project_Id varchar(36),
    User_ID varchar(36),
    FOREIGN KEY (User_Id) REFERENCES Users (Id),
    FOREIGN KEY (Project_Id) REFERENCES Projects (Project_Id)
);

-- Insertion de données
INSERT INTO Users (
    Id,
    First_name,
    Last_name,
    Email,
    Password,
    UserRole,
    UserText,
    PhoneNB
)
VALUES (
    "dc8963d0-267f-4dc5-b582-38ba0ea287af",
    'Kyrian',
    'Delaplace',
    'kyrian.delaplace09@gmail.com',
    '$2y$10$v9EKKe4cKF1dRI2SzSGYvewUOEKWPzykmhNwtz3zUXrDbNpzvJnIa',
    'Admin',
    'Je suis un jeune developpeur qui cherche un stage/stage alterné',
    '+33780049696'
);

INSERT INTO CV (
    CV_ID,
    User_ID,
    Title,
    Description
)
VALUES (
    "a5f7e686-2af4-4911-b0bb-e626d9d22cb8",
    'dc8963d0-267f-4dc5-b582-38ba0ea287af',
    'Master Info',
    'Je passe mon master, je cherche un boulot'
);

INSERT INTO EducationExt (
    EducationExt_ID,
    CV_ID,
    School,
    Start_Date,
    End_Date
)
VALUES (
    "264187c9-9782-40c3-8b01-1d95cbc378d2",
    "a5f7e686-2af4-4911-b0bb-e626d9d22cb8",
    'Ynov',
    '2024-03-18',
    '2025-08-19'
);

INSERT INTO ExpExternal (
    ExpExt_ID,
    CV_ID,
    Title,
    Start_Date,
    End_Date
)
VALUES (
    "09991ddd-6cb7-402c-8be8-64cebe5a3aa8",
    "a5f7e686-2af4-4911-b0bb-e626d9d22cb8",
    'Stage en entreprise',
    '2024-04-18',
    '2025-08-20'
);

INSERT INTO Skills (
    Skill_ID,
    CV_ID,
    Title,
    Description,
    YearsXP
)
VALUES (
    "98873601-a51d-49d4-a09e-632e90171503",
    "a5f7e686-2af4-4911-b0bb-e626d9d22cb8",
    'PHP',
    'Site style CV Portefolio',
    2
);

INSERT INTO Projects (
    Project_Id,
    Title,
    Subjects,
    Description,
    Validate
)
VALUES (
    "6c94e2e5-02db-4e03-85ec-0df5700b4bb1",
    'WebSite PhP for Ynov',
    'WebSite',
    'Here is my class project of making a website using html, php, css, js and docker',
    1
);

INSERT INTO ProjectsUsers (
    Project_Id,
    User_ID
)
VALUES (
    "6c94e2e5-02db-4e03-85ec-0df5700b4bb1",
    'dc8963d0-267f-4dc5-b582-38ba0ea287af'
);

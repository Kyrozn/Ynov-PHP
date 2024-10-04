-- Create database
CREATE DATABASE ynovphp_db;

-- Use the database
USE ynovphp_db;

CREATE TABLE Users(
    Id VARCHAR(36) PRIMARY KEY,
    First_name VARCHAR(25),
    Last_name VARCHAR(25),
    Email VARCHAR(50),
    Password VARCHAR(50),
    UserRole VARCHAR(50)
);

CREATE TABLE Skills(
    Skill_ID VARCHAR(36) PRIMARY KEY,
    Title VARCHAR(25),
    Description VARCHAR(250),
    YearsXP INT
);

CREATE TABLE ExpExternal(
    ExpExt_ID VARCHAR(36) PRIMARY KEY,
    Title VARCHAR(25),
    Start_Date DATE,
    End_Date DATE
);

CREATE Table EducationExt(
    EducationExt_ID VARCHAR(36) PRIMARY KEY,
    School VARCHAR(25),
    Start_Date DATE,
    End_Date DATE
);

CREATE TABLE CV(
    CV_ID VARCHAR(36) PRIMARY KEY,
    Title VARCHAR(25),
    Description VARCHAR(250),
    Skill_ID VARCHAR(36),
    EducationExt_ID VARCHAR(36),
    ExpExt_ID VARCHAR(36),
    FOREIGN Key (Skill_ID) REFERENCES Skills(Skill_ID),
    FOREIGN Key (ExpExt_ID) REFERENCES ExpExternal(ExpExt_ID),
    FOREIGN Key (EducationExt_ID) REFERENCES EducationExt(EducationExt_ID)
);

INSERT INTO Users (Id, First_name,Last_name, Email, Password, UserRole)
Values  ("aa70d94d-6b21-4b48-8b69-b5ecb66e3ab9", 'John', 'Doe', 'jouhn.doe@test.com', 'testAccontPSw', 'Admin');
DROP DATABASE IF EXISTS zbbivp;
CREATE DATABASE zbbivp;
USE zbbivp;

CREATE OR REPLACE TABLE `user` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` varchar(250) NOT NULL,
    `password` varchar(250) NOT NULL,
    `firstName` VARCHAR(100) NOT NULL,
    `surname` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO user (username, password, surname, firstName) VALUES
    ('admin', '$2y$10$P6.Hp/NcWeLWuV53NsV9X.BLbJQ2w1IxVulDJ2pI8cKCi.OgErNz.', 'User', 'Administrator');

CREATE OR REPLACE TABLE `securitytoken` (
    `id` bigint unsigned AUTO_INCREMENT,
    `userId` bigint unsigned NOT NULL,
    `identifier` varchar(256) NOT NULL,
    `token` varchar(256) NOT NULL,
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`userId`) REFERENCES `user` (`id`)
) ENGINE=InnoDB;

CREATE OR REPLACE TABLE `trainingCourse` (
    `id` bigint unsigned AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE OR REPLACE TABLE `interested` (
    `id` bigint unsigned AUTO_INCREMENT,
    `trainingCourse1Id` bigint unsigned NOT NULL,
    `trainingCourse2Id` bigint unsigned,
    `firstName` VARCHAR(200) NOT NULL,
    `surname` VARCHAR(200) NOT NULL,
    `birthDate` DATE NOT NULL,
    `birthLocation` VARCHAR(200),
    `maritalStatus` VARCHAR(100) NOT NULL,
    `hasChilds` BOOLEAN DEFAULT false NOT NULL,
    `address` VARCHAR(500) NOT NULL,
    `nationality` VARCHAR(100) NOT NULL,
    `phonePrivate` VARCHAR(100),
    `phoneMobile` VARCHAR(100) NOT NULL,
    `email` VARCHAR(200) NOT NULL,
    `legalRepresentative` VARCHAR(400),
    `emailLegalRepresentative` VARCHAR(200),
    `phoneLegalRepresentative` VARCHAR(100),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`trainingCourse1Id`) REFERENCES `trainingCourse` (`id`),
    FOREIGN KEY (`trainingCourse2Id`) REFERENCES `trainingCourse` (`id`)
) ENGINE=InnoDB;
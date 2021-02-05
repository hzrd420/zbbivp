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
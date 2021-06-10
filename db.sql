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

CREATE OR REPLACE TABLE `securityToken` (
    `id` bigint unsigned AUTO_INCREMENT,
    `userId` bigint unsigned NOT NULL,
    `identifier` varchar(256) NOT NULL,
    `token` varchar(256) NOT NULL,
    `created` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE OR REPLACE TABLE `trainingCourse` (
    `id` bigint unsigned AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE OR REPLACE TABLE `interested` (
    `id` bigint unsigned AUTO_INCREMENT,
    `gender` CHAR(1) NOT NULL,
    `firstName` VARCHAR(200) NOT NULL,
    `surname` VARCHAR(200) NOT NULL,
    `birthDate` DATE,
    `birthLocation` VARCHAR(200),
    `maritalStatus` VARCHAR(100),
    `hasChilds` BOOLEAN DEFAULT false NOT NULL,
    `street` VARCHAR(500),
    `postCode` CHAR(5),
    `location` VARCHAR(500),
    `nationality` VARCHAR(100),
    `phonePrivate` VARCHAR(100),
    `phoneMobile` VARCHAR(100) NOT NULL,
    `email` VARCHAR(200),
    `legalRepresentative` VARCHAR(400),
    `emailLegalRepresentative` VARCHAR(200),
    `phoneLegalRepresentative` VARCHAR(100),
    `lastGraduation` VARCHAR(200),
    `graduationYear` INT,
    `lastSchool` VARCHAR(200),
    `schoolFrom` DATE,
    `schoolTo` DATE,
    `hasBoardingSchoolExperience` BOOLEAN DEFAULT false NOT NULL,
    `germanLevel` VARCHAR(2) NOT NULL,
    `degreeOfVisualImpairment` CHAR(1),
    `otherDisability` CHAR(1),
    `requiredAccessibilityTools` LONGTEXT,
    `handicappedIdAvailable` BOOLEAN DEFAULT false NOT NULL,
    `medicalRemarks` LONGTEXT,
    `pensionInsuranceNumber` CHAR(12),
    `taxID` CHAR(11),
    `taxClass` CHAR(2),
    `denomination` VARCHAR(50),
    `healthInsuranceName` VARCHAR(200),
    `healthInsuranceNumber` VARCHAR(30),
    `membershipCertificateForHealthInsuranceAvailable` BOOLEAN DEFAULT false NOT NULL,
    `paymentOfSVContributions` VARCHAR(50),
    `retraining` BOOLEAN NOT NULL DEFAULT false,
    `trainingCourse1Id` bigint unsigned,
    `trainingCourse2Id` bigint unsigned,
    `electives` LONGTEXT,
    `orientationWeekInterest` BOOLEAN NOT NULL DEFAULT false,
    `orientationWeekFrom` DATE,
    `orientationWeekTo` DATE,
    `orientationWeekAccommodationRequired` BOOLEAN NOT NULL DEFAULT false,
    `orientationWeekCostCommitmentRequested` BOOLEAN NOT NULL DEFAULT false,
    `orientationWeekPayer` VARCHAR(200),
    `orientationWeekRemarks` LONGTEXT,
    `orientationWeekCostCommitmentReceived` BOOLEAN NOT NULL DEFAULT false,

    `trainingFrom` DATE,
    `trainingTo` DATE,
    `trainingContract` VARCHAR(20) NOT NULL,


    `payerName` VARCHAR(200),
    `payerAddress` VARCHAR(500),
    `payerContactPerson` VARCHAR(200),
    `payerPhone` VARCHAR(100),
    `payerCustomerNumber` VARCHAR(100),
    `payerCostCommitment` VARCHAR(20),
    `payerRemarks` LONGTEXT,
    `accommodation` VARCHAR(20),
    `youthProtectionExaminationReceived` BOOLEAN NOT NULL DEFAULT false,
    `created` timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`trainingCourse1Id`) REFERENCES `trainingCourse` (`id`)  ON DELETE CASCADE,
    FOREIGN KEY (`trainingCourse2Id`) REFERENCES `trainingCourse` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE OR REPLACE TABLE `stepType` (
    `id` bigint unsigned AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `description` LONGTEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE OR REPLACE TABLE `step` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `interestedId` BIGINT UNSIGNED NOT NULL,
  `stepTypeId` BIGINT UNSIGNED NOT NULL,
  `comment` LONGTEXT,
  `due` DATE,
  `created` timestamp NULL DEFAULT NULL,
  FOREIGN KEY(`interestedId`) REFERENCES `interested`(`id`) ON DELETE CASCADE,
  FOREIGN KEY(`stepTypeId`) REFERENCES `stepType`(`id`) ON DELETE CASCADE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
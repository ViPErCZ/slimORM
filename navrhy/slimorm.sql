-- MySQL Script generated by MySQL Workbench
-- Čt 7. leden 2016, 16:05:47 CET
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema slimorm
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema slimorm
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `slimorm` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `slimorm` ;

-- -----------------------------------------------------
-- Table `slimorm`.`library`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`library` (
  `libraryID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  PRIMARY KEY (`libraryID`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`book`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`book` (
  `bookID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `libraryID` INT NOT NULL COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  `create` DATETIME NULL COMMENT '',
  PRIMARY KEY (`bookID`)  COMMENT '',
  INDEX `fk_book_library_idx` (`libraryID` ASC)  COMMENT '',
  CONSTRAINT `fk_book_library`
    FOREIGN KEY (`libraryID`)
    REFERENCES `slimorm`.`library` (`libraryID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`language`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`language` (
  `languageID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `lang` VARCHAR(2) NOT NULL COMMENT '',
  PRIMARY KEY (`languageID`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`rel2`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`rel2` (
  `rel2ID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  PRIMARY KEY (`rel2ID`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`rel1`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`rel1` (
  `rel1ID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  `rel2ID` INT NOT NULL COMMENT '',
  PRIMARY KEY (`rel1ID`)  COMMENT '',
  INDEX `fk_rel1_rel21_idx` (`rel2ID` ASC)  COMMENT '',
  CONSTRAINT `fk_rel1_rel21`
    FOREIGN KEY (`rel2ID`)
    REFERENCES `slimorm`.`rel2` (`rel2ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`contact`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`contact` (
  `contactID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `rel1ID` INT NOT NULL COMMENT '',
  `address` VARCHAR(45) NULL COMMENT '',
  PRIMARY KEY (`contactID`)  COMMENT '',
  INDEX `fk_contact_rel11_idx` (`rel1ID` ASC)  COMMENT '',
  CONSTRAINT `fk_contact_rel11`
    FOREIGN KEY (`rel1ID`)
    REFERENCES `slimorm`.`rel1` (`rel1ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`author`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`author` (
  `authorID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `bookID` INT NOT NULL COMMENT '',
  `languageID` INT NOT NULL COMMENT '',
  `contactID` INT NULL COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  PRIMARY KEY (`authorID`)  COMMENT '',
  INDEX `fk_author_book1_idx` (`bookID` ASC)  COMMENT '',
  INDEX `fk_author_language1_idx` (`languageID` ASC)  COMMENT '',
  INDEX `fk_author_contact1_idx` (`contactID` ASC)  COMMENT '',
  CONSTRAINT `fk_author_book1`
    FOREIGN KEY (`bookID`)
    REFERENCES `slimorm`.`book` (`bookID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_author_language1`
    FOREIGN KEY (`languageID`)
    REFERENCES `slimorm`.`language` (`languageID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_author_contact1`
    FOREIGN KEY (`contactID`)
    REFERENCES `slimorm`.`contact` (`contactID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`librarian`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`librarian` (
  `librarianID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `libraryID` INT NOT NULL COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  PRIMARY KEY (`librarianID`)  COMMENT '',
  INDEX `fk_librarian_library1_idx` (`libraryID` ASC)  COMMENT '',
  CONSTRAINT `fk_librarian_library1`
    FOREIGN KEY (`libraryID`)
    REFERENCES `slimorm`.`library` (`libraryID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`phone`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`phone` (
  `phoneID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `contactID` INT NOT NULL COMMENT '',
  `number` VARCHAR(45) NULL COMMENT '',
  PRIMARY KEY (`phoneID`)  COMMENT '',
  INDEX `fk_phone_contact1_idx` (`contactID` ASC)  COMMENT '',
  CONSTRAINT `fk_phone_contact1`
    FOREIGN KEY (`contactID`)
    REFERENCES `slimorm`.`contact` (`contactID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`attachment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`attachment` (
  `attachmentID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  PRIMARY KEY (`attachmentID`)  COMMENT '')
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`book_has_attachment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`book_has_attachment` (
  `book_has_attachmentID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `bookID` INT NOT NULL COMMENT '',
  `attachmentID` INT NOT NULL COMMENT '',
  PRIMARY KEY (`book_has_attachmentID`)  COMMENT '',
  INDEX `fk_book_has_attachment_attachment1_idx` (`attachmentID` ASC)  COMMENT '',
  INDEX `fk_book_has_attachment_book1_idx` (`bookID` ASC)  COMMENT '',
  CONSTRAINT `fk_book_has_attachment_book1`
    FOREIGN KEY (`bookID`)
    REFERENCES `slimorm`.`book` (`bookID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_book_has_attachment_attachment1`
    FOREIGN KEY (`attachmentID`)
    REFERENCES `slimorm`.`attachment` (`attachmentID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `slimorm`.`myself`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `slimorm`.`myself` (
  `myselfID` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `childID` INT NULL COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  PRIMARY KEY (`myselfID`)  COMMENT '',
  INDEX `fk_myself_myself1_idx` (`childID` ASC)  COMMENT '',
  CONSTRAINT `fk_myself_myself1`
    FOREIGN KEY (`childID`)
    REFERENCES `slimorm`.`myself` (`myselfID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

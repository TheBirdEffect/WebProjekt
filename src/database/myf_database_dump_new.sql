-- MySQL Script generated by MySQL Workbench
-- Tue Dec 29 22:54:08 2020
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema maskyourface
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema maskyourface
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `maskyourface` DEFAULT CHARACTER SET utf8 ;
USE `maskyourface` ;

-- -----------------------------------------------------
-- Table `maskyourface`.`addresses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`addresses` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `street` VARCHAR(255) NOT NULL,
  `streetNumber` VARCHAR(10) NOT NULL,
  `city` VARCHAR(60) NOT NULL,
  `zipCode` VARCHAR(12) NOT NULL,
  PRIMARY KEY (`id`));


-- -----------------------------------------------------
-- Table `maskyourface`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `firstName` VARCHAR(50) NOT NULL,
  `lastName` VARCHAR(50) NOT NULL,
  `secondName` VARCHAR(50) NULL,
  `gender` ENUM('u', 'm', 'f') NULL,
  `phone` VARCHAR(26) NULL,
  `addressID` INT NOT NULL,
  `birthDate` DATE NOT NULL,
  `role` ENUM('admin', 'user') NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_address_idx` (`addressID` ASC),
  CONSTRAINT `fk_user_address`
    FOREIGN KEY (`addressID`)
    REFERENCES `maskyourface`.`addresses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `maskyourface`.`logins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`logins` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `validated` TINYINT NOT NULL,
  `enabled` TINYINT NOT NULL,
  `email` VARCHAR(320) NOT NULL,
  `lastActive` TIMESTAMP NULL,
  `lastLogin` TIMESTAMP NULL,
  `failedLoginCount` TINYINT NOT NULL,
  `passwordHash` VARCHAR(255) NOT NULL,
  `passwordResetHash` VARCHAR(60) NULL,
  `passwordResetCreatedAt` TIMESTAMP NULL,
  `userID` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_login_user1_idx` (`userID` ASC),
  UNIQUE INDEX `username_UNIQUE` (`email` ASC),
  CONSTRAINT `fk_login_user1`
    FOREIGN KEY (`userID`)
    REFERENCES `maskyourface`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `maskyourface`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `categoryName` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `categoryName_UNIQUE` (`categoryName` ASC));


-- -----------------------------------------------------
-- Table `maskyourface`.`vendors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`vendors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `vendorName` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`));


-- -----------------------------------------------------
-- Table `maskyourface`.`products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`products` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `productName` VARCHAR(120) NOT NULL,
  `catchPhrase` VARCHAR(150) NULL,
  `productDescription` VARCHAR(5000) NOT NULL,
  `standardPrice` DECIMAL(7,2) NOT NULL,
  `categoryID` INT NOT NULL,
  `vendorID` INT NOT NULL,
  `isHidden` TINYINT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `productName_UNIQUE` (`productName` ASC),
  INDEX `fk_products_categories1_idx` (`categoryID` ASC),
  INDEX `fk_products_vendor1_idx` (`vendorID` ASC),
  CONSTRAINT `fk_products_categories1`
    FOREIGN KEY (`categoryID`)
    REFERENCES `maskyourface`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_vendor1`
    FOREIGN KEY (`vendorID`)
    REFERENCES `maskyourface`.`vendors` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `maskyourface`.`images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `imageName` VARCHAR(150) NULL,
  `imageURL` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`));


-- -----------------------------------------------------
-- Table `maskyourface`.`productImages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`productImages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `imagesID` INT NOT NULL,
  `productsID` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_productImages_Images1_idx` (`imagesID` ASC),
  INDEX `fk_productImages_products1_idx` (`productsID` ASC),
  CONSTRAINT `fk_productImages_Images1`
    FOREIGN KEY (`imagesID`)
    REFERENCES `maskyourface`.`images` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_productImages_products1`
    FOREIGN KEY (`productsID`)
    REFERENCES `maskyourface`.`products` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `maskyourface`.`orders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `shippingDate` TIMESTAMP NULL,
  `cancellationDate` TIMESTAMP NULL,
  `loginID` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_orders_login1_idx` (`loginID` ASC),
  CONSTRAINT `fk_orders_login1`
    FOREIGN KEY (`loginID`)
    REFERENCES `maskyourface`.`logins` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `maskyourface`.`orderItems`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `maskyourface`.`orderItems` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `productsID` INT NOT NULL,
  `ordersID` INT NOT NULL,
  `quantity` INT NULL DEFAULT 1,
  `actualPrice` DECIMAL(8,2) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_orderItems_products1_idx` (`productsID` ASC),
  INDEX `fk_orderItems_orders1_idx` (`ordersID` ASC),
  CONSTRAINT `fk_orderItems_products1`
    FOREIGN KEY (`productsID`)
    REFERENCES `maskyourface`.`products` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orderItems_orders1`
    FOREIGN KEY (`ordersID`)
    REFERENCES `maskyourface`.`orders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

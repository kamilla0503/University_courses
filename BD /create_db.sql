-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`Population`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Population` (
  `Population_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`Population_name`),
  UNIQUE INDEX `Population_name_UNIQUE` (`Population_name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Sample`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Sample` (
  `id` INT NOT NULL,
  `Method` VARCHAR(10) NOT NULL,
  `Population_Population_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`, `Population_Population_name`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_Sample_Population1_idx` (`Population_Population_name` ASC),
  CONSTRAINT `fk_Sample_Population1`
    FOREIGN KEY (`Population_Population_name`)
    REFERENCES `mydb`.`Population` (`Population_name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Variant`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Variant` (
  `idVariant` VARCHAR(15) NOT NULL,
  `Chromosome` VARCHAR(2) NOT NULL,
  `Posiion` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idVariant`),
  UNIQUE INDEX `idVariant_UNIQUE` (`idVariant` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Phenotype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Phenotype` (
  `Disease_name` VARCHAR(45) NOT NULL,
  `Invalid_status` VARCHAR(2) NULL,
  PRIMARY KEY (`Disease_name`),
  UNIQUE INDEX `Disease_name_UNIQUE` (`Disease_name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Phenotype_Variant`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Phenotype_Variant` (
  `Frequency` FLOAT NULL,
  `Phenotype_Disease_name` VARCHAR(45) NOT NULL,
  `Variant_idVariant` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`Phenotype_Disease_name`, `Variant_idVariant`),
  INDEX `fk_Phenotype_Variant_Variant1_idx` (`Variant_idVariant` ASC),
  CONSTRAINT `fk_Phenotype_Variant_Phenotype1`
    FOREIGN KEY (`Phenotype_Disease_name`)
    REFERENCES `mydb`.`Phenotype` (`Disease_name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Phenotype_Variant_Variant1`
    FOREIGN KEY (`Variant_idVariant`)
    REFERENCES `mydb`.`Variant` (`idVariant`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Sample_variant`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Sample_variant` (
  `DP` INT NULL,
  `GENOTYPE` VARCHAR(45) NULL,
  `Sample_id` INT NOT NULL,
  `Variant_idVariant` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`Sample_id`, `Variant_idVariant`),
  INDEX `fk_Sample_variant_Variant1_idx` (`Variant_idVariant` ASC),
  CONSTRAINT `fk_Sample_variant_Sample1`
    FOREIGN KEY (`Sample_id`)
    REFERENCES `mydb`.`Sample` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Sample_variant_Variant1`
    FOREIGN KEY (`Variant_idVariant`)
    REFERENCES `mydb`.`Variant` (`idVariant`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Population_variant`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Population_variant` (
  `Frequency` FLOAT NULL,
  `Population_Population_name` VARCHAR(45) NOT NULL,
  `Variant_idVariant` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`Population_Population_name`, `Variant_idVariant`),
  INDEX `fk_Population_variant_Variant1_idx` (`Variant_idVariant` ASC),
  CONSTRAINT `fk_Population_variant_Population1`
    FOREIGN KEY (`Population_Population_name`)
    REFERENCES `mydb`.`Population` (`Population_name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Population_variant_Variant1`
    FOREIGN KEY (`Variant_idVariant`)
    REFERENCES `mydb`.`Variant` (`idVariant`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

CREATE TABLE IF NOT EXISTS `#__steemit_config` (`id` INT(10) NOT NULL AUTO_INCREMENT, `config` LONGTEXT NULL, PRIMARY KEY (`id`));
INSERT INTO #__steemit_config (config) values ("{}");
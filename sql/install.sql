CREATE TABLE IF NOT EXISTS `PREFIX_homepageboxes` (
	`id_box` INT NOT NULL AUTO_INCREMENT ,
	`title` VARCHAR(255) NOT NULL , 
	`background_image` VARCHAR(255) NOT NULL , 
	`link` VARCHAR(255) NOT NULL , 
	PRIMARY KEY (`id_box`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=UTF8;
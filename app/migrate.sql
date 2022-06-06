DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `name` VARCHAR(255) NOT NULL COMMENT 'Название',
    `href` VARCHAR(255) NOT NULL COMMENT 'Ссылка',
    `src` VARCHAR(255) NOT NULL COMMENT 'Изображение',
    `price` DECIMAL(10, 2) NOT NULL DEFAULT '0.00' COMMENT 'Цена',
    PRIMARY KEY (`ID`),
    CONSTRAINT `ukHref` UNIQUE KEY (`href`),
    INDEX `ixPrice` (`price`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COMMENT = 'Товары' AUTO_INCREMENT = 1;
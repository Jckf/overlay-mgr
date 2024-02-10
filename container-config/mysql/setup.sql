CREATE USER 'overlay-mgr'@'%' IDENTIFIED BY 'overlay-mgr';

GRANT ALL PRIVILEGES ON `overlay-mgr`.* TO 'overlay-mgr'@'%';

CREATE DATABASE `overlay-mgr`;

USE `overlay-mgr`;

CREATE TABLE `items` (
    `id` int NOT NULL AUTO_INCREMENT,
    `key` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `image` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `bids` (
    `id` int NOT NULL AUTO_INCREMENT,
    `recipient` varchar(255) NOT NULL,
    `sender` varchar(255) NOT NULL,
    `original_message` text NOT NULL,
    `item_id` int,
    `amount` int,
    `timestamp` BIGINT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`item_id`) REFERENCES `items`(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `items` (`key`, `title`, `description`, `image`) VALUES ('vase', 'Ming Vase', 'A beautiful Ming Vase.', '/static/img/vase.jpg');
INSERT INTO `items` (`key`, `title`, `description`, `image`) VALUES ('candles', 'Ancient Candles', 'An ancient bundle of candles.', '/static/img/candles.jpg');
INSERT INTO `items` (`key`, `title`, `description`, `image`) VALUES ('f40', 'Ferrari F40', 'The Ferrari of Ferraris.', '/static/img/ferrari.jpg');

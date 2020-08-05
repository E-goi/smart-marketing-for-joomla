CREATE TABLE IF NOT EXISTS `#__egoi` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `apikey` VARCHAR(255)  NOT NULL ,
    `addsubscribe_myaccounts` BOOLEAN NOT NULL ,
    `sync` INT(1) DEFAULT '0' ,
    `te` INT(1) DEFAULT '0' ,
    `list` INT(1) DEFAULT '0' ,
    `groups` VARCHAR(255),
    `tag` INT(1) DEFAULT '0' ,
    `client_id` INT(1) DEFAULT '0' ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__egoi` (
    `apikey`,
    `addsubscribe_myaccounts`,
    `sync`,
    `te`,
    `list`,
    `groups`,
    `tag`,
    `client_id`
) VALUES (
    '',
    0,
    0,
    0,
    0,
    '',
    0,
    0
);

CREATE TABLE IF NOT EXISTS `#__egoi_forms` (
    `id` int(11) NOT NULL,
    `enable` int(1) NOT NULL,
    `form_title` varchar(255) NOT NULL,
    `show_title` INT(1) NOT NULL,
    `content` longtext NOT NULL,
    `hide` int(1) NOT NULL,
    `style_w` int(5) NOT NULL,
    `style_h` int(5) NOT NULL,
    `form_type` varchar(255) NOT NULL,
    `list` INT(11) NOT NULL,
    `area` VARCHAR(255) NOT NULL,
    `estado` int(1) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__egoi_map_fields` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `jm` varchar(255) NOT NULL,
    `jm_name` varchar(255) NOT NULL,
    `egoi` varchar(255) NOT NULL,
    `egoi_name` varchar(255) NOT NULL,
    `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__virtuemart_egoi` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `apikey` varchar(255) NOT NULL,
    `subscribetext` varchar(255) NOT NULL,
    `mailinglistnum` varchar(255) NOT NULL,
    `singleoptinID` varchar(255) NOT NULL,
    `content` longtext NOT NULL,
    `state` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__extensions` (
    `name`,
    `type`,
    `element`,
    `folder`,
    `client_id`,
    `enabled`,
    `access`,
    `protected`,
    `manifest_cache`,
    `params`,
    `custom_data`,
    `system_data`,
    `checked_out`,
    `checked_out_time`,
    `ordering`,
    `state`
) VALUES (
    'EgoiForms',
    'plugin',
    'egoi',
    'content',
    0,
    1,
    1,
    0,
    '{"name":"EgoiForms","type":"plugin","creationDate":"July 2020","author":"E-goi","copyright":"Copyright (C) 2020 E_goi. All rights reserved.","authorEmail":"","authorUrl":"http:\\/\\/www.e-goi.com","version":"1.0.1","description":"EgoiForms; form inputs frontoffice","group":"","filename":"specification"}',
    '',
    '',
    '',
    0,
    '0000-00-00 00:00:00',
    0,
    0
);

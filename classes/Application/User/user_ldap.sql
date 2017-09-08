CREATE TABLE `user_ldap` (
  `user_id` int(11) unsigned NOT NULL,
  `cn` varchar(255) NOT NULL,
  `dn` varchar(255) NOT NULL,
  `employeeid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `program` varchar(255) DEFAULT NULL,
  `mail` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `unique_user_id` (`user_id`),
  UNIQUE KEY `unique_cn` (`cn`),
  UNIQUE KEY `unique_dn` (`dn`),
  UNIQUE KEY `unique_uid` (`uid`),
  UNIQUE KEY `unique_mail` (`mail`),
  KEY `index_employeeid` (`employeeid`),
  KEY `index_program` (`program`),
  KEY `index_updated_at8` (`updated_at`),
  KEY `index_created_at9` (`created_at`),
  KEY `index_title` (`title`),
  CONSTRAINT `lnk_user_user_ldap` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8

INSERT INTO `role`(
    `id`,
    `alias`,
    `description`,
    `parent_role_id`,
    `created_at`,
    `updated_at`,
    `foreign_role`
) VALUES (
    11,
    'student',
    'Φοιτητής',
    1,
    '2017-02-03 09:24:00',
    NULL,
    NULL
 );


CREATE TABLE `ldap_title_role` (
  `ldap_title` varchar(255) NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`ldap_title`),
  UNIQUE KEY `role_id` (`role_id`),
  UNIQUE KEY `ldap_title` (`ldap_title`),
  KEY `index_role_id4` (`role_id`),
  KEY `index_ldap_title` (`ldap_title`),
  CONSTRAINT `lnk_role_ldap_title_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ldap_title_role`(
    `ldap_title`,
    `role_id`
) VALUES (
    'S',
    11
 );

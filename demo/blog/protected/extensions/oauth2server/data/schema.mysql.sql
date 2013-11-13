CREATE TABLE IF NOT EXISTS `tbl_oauth2server_clients` (
  `id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_approve` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `tbl_oauth2server_clients` (`id`, `secret`, `name`, `auto_approve`) VALUES
('I6Lh72kTItE6y29Ig607N74M7i21oyTo', 'dswREHV2YJjF7iL5Zr5ETEFBwGwDQYjQ', 'Test application', 0);


CREATE TABLE IF NOT EXISTS `tbl_oauth2server_client_endpoints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `tbl_oauth2server_scopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scope` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `tbl_oauth2server_scopes` (`id`, `scope`, `name`, `description`) VALUES
(1, 'user.basic', 'User profile info', 'User profile info');


CREATE TABLE IF NOT EXISTS `tbl_oauth2server_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_type` varchar(6) COLLATE utf8_unicode_ci DEFAULT 'user',
  `owner_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `refresh_token` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token_expires` int(11) DEFAULT NULL,
  `stage` varchar(9) COLLATE utf8_unicode_ci DEFAULT 'requested',
  `first_requested` int(11) DEFAULT NULL,
  `last_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `tbl_oauth2server_session_scopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `scope_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


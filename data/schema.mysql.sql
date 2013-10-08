CREATE TABLE oauth2server_scopes
(
  id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  scope VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  description VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE oauth2server_client_endpoints
(
  id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  client_id VARCHAR(40) NOT NULL,
  redirect_uri VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE oauth2server_clients
(
  id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  secret VARCHAR(40) NOT NULL,
  name VARCHAR(255) NOT NULL,
  auto_approve integer DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE oauth2server_session_scopes
(
  id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  session_id integer NOT NULL,
  scope_id integer NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE oauth2server_sessions
(
  id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  client_id VARCHAR(40) NOT NULL,
  redirect_uri VARCHAR(255),
  owner_type VARCHAR(6) DEFAULT 'user',
  owner_id VARCHAR(255),
  auth_code VARCHAR(40),
  access_token VARCHAR(40),
  refresh_token VARCHAR(40),
  access_token_expires integer,
  stage VARCHAR(9) DEFAULT 'requested',
  first_requested integer,
  last_updated integer
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

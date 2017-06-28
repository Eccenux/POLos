CREATE DATABASE polos_db;
GRANT ALL ON polos_db.* 
	TO polos_user@localhost IDENTIFIED BY 'some password for MySQL user';
/*
SET PASSWORD FOR
	polos_user@localhost = OLD_PASSWORD('some password for MySQL user');
*/
USE polos_db

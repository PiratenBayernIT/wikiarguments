DROP TRIGGER IF EXISTS `sync_users_insert`;
DELIMITER $$
 
CREATE TRIGGER `sync_users_insert` AFTER INSERT ON `users` 
FOR EACH ROW 
BEGIN
    INSERT INTO $DEST.users (
        userId, 
        userName, 
        email, 
        `group`, 
        password, 
        salt, 
        dateAdded, 
        user_last_action, 
        scoreQuestions, 
        scoreArguments) 
    VALUES(
        NEW.userId, 
        NEW.userName, 
        NEW.email, 
        NEW.`group`, 
        NEW.password, 
        NEW.salt,
        NEW.dateAdded,
        0, 
        0, 
        0);
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `sync_users_update`;
DELIMITER $$
 
CREATE TRIGGER `sync_users_update` AFTER UPDATE ON `users` 
FOR EACH ROW 
BEGIN
    IF OLD.userName != NEW.userName 
    OR OLD.email != NEW.email 
    OR OLD.group != NEW.group 
    OR OLD.password != NEW.password 
    OR OLD.salt != NEW.salt THEN
        UPDATE $DEST.users 
        SET
            userName = NEW.userName, 
            email = NEW.email, 
            `group` = NEW.`group`, 
            password = NEW.password, 
            salt = NEW.salt
        WHERE userId = NEW.userId;
    END IF;
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS `sync_users_delete`;
DELIMITER $$
 
CREATE TRIGGER `sync_users_delete` AFTER DELETE ON `users` 
FOR EACH ROW 
BEGIN
    DELETE FROM $DEST.users WHERE userId = OLD.userId;
END$$
DELIMITER ;



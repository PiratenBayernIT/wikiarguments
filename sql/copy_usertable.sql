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
SELECT 
    userId, 
    userName, 
    email, 
    `group`, 
    password, 
    salt,
    dateAdded,
    0, 
    0, 
    0
FROM $SRC.users;

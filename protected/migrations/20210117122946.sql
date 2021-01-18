ALTER TABLE `users` ADD `email_verification_hash_expires` INT NULL
AFTER `password_reset_verification_hash`; 
ALTER TABLE `users`
ADD `email_verification_hash_expires` INT NULL
AFTER `password_reset_verification_hash`,
ADD `password_reset_verification_hash_expires` INT NULL
AFTER `email_verification_hash_expires`;
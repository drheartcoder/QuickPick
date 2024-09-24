// PRIYANKA

ALTER TABLE `admin_commission` ADD `company_percentage` FLOAT NOT NULL AFTER `percentage`;

ALTER TABLE `admin_commission` CHANGE `percentage` `driver_percentage` FLOAT NOT NULL;
------------------------------------------------------------------------------------------

ALTER TABLE `users` ADD `account_status` ENUM('approved','unapproved') NOT NULL AFTER `otp_type`;
UPDATE `email_template` SET `template_variables` = '##FIRST_NAME##~##PROJECT_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##~##OTP##', `deleted_at` = NULL WHERE `email_template`.`id` = 10;

ALTER TABLE `users` ADD `account_status` ENUM('approved','unapproved') NOT NULL AFTER `otp_type`;
UPDATE `email_template` SET `template_variables` = '##FIRST_NAME##~##PROJECT_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##~##OTP##', `deleted_at` = NULL WHERE `email_template`.`id` = 11;

ALTER TABLE `users` CHANGE `lattitude` `latitude` FLOAT NOT NULL;

ALTER TABLE `review` ADD `ride_unique_id` INT NOT NULL AFTER `to_user_id`;

ALTER TABLE `review` ADD `rating_tag_id` INT NOT NULL AFTER `rating`;


// MAYUR

ALTER TABLE `vehicle` ADD `driver_id` INT(20) NOT NULL AFTER `company_id`;

31/01/2018

ALTER TABLE `users` ADD `availability_status` ENUM('ONLINE','OFFLINE') NOT NULL DEFAULT 'OFFLINE' AFTER `account_status`;

01/02/2018

ALTER TABLE `deposit_money` ADD `transaction_id` VARCHAR(50) NOT NULL AFTER `id`;

UPDATE `email_template` SET `template_variables` = '##FIRST_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##~##PROJECT_NAME##~##AMOUNT_PAID##~##TRANSACTION_ID##' WHERE `email_template`.`id` = 15;

UPDATE `email_template_translation` SET `template_html` = '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! Your Payment Successfull by \"##PROJECT_NAME##\" and Transactions ID is ##TRANSACTION_ID##.</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##FIRST_NAME## ##LAST_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Paid Amount : &nbsp;##AMOUNT_PAID##&nbsp;</p>' WHERE `email_template_translation`.`id` = 15;

UPDATE `email_template` SET `template_variables` = '##COMPANY_NAME##~##EMAIL##~##PROJECT_NAME##~##AMOUNT_PAID##~##TRANSACTION_ID##' WHERE `email_template`.`id` = 16;

UPDATE `email_template_translation` SET `template_html` = '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##COMPANY_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! Your Payment Successfully Done by&nbsp; \"##PROJECT_NAME##\" and Transactions ID is ##TRANSACTION_ID##.</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##COMPANY_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Paid Amount : &nbsp;##AMOUNT_PAID##&nbsp;</p>' WHERE `email_template_translation`.`id` = 16;

INSERT INTO `modules` (`id`, `title`, `slug`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'Notification', 'notification', '1', CURRENT_TIMESTAMP, NULL, NULL);

12/02/2018

ALTER TABLE `vehicle` ADD `vehicle_length` DECIMAL(12,2) NOT NULL AFTER `vehicle_number`, ADD `vehicle_height` DECIMAL(12,2) NOT NULL AFTER `vehicle_length`, ADD `vehicle_breadth` DECIMAL(12,2) NOT NULL AFTER `vehicle_height`, ADD `vehicle_weight` DECIMAL(12,3) NOT NULL AFTER `vehicle_breadth`, ADD `vehicle_volume` DECIMAL(12,3) NOT NULL AFTER `vehicle_weight`;	





ALTER TABLE `vehicle_type` ADD `vehicle_min_length` DECIMAL(12,2) NOT NULL AFTER `vehicle_type`, ADD `vehicle_min_height` DECIMAL(12,2) NOT NULL AFTER `vehicle_min_length`, ADD `vehicle_min_breadth` DECIMAL(12,2) NOT NULL AFTER `vehicle_min_height`, ADD `vehicle_min_weight` DECIMAL(12,3) NOT NULL AFTER `vehicle_min_breadth`, ADD `vehicle_min_volume` DECIMAL(12,3) NOT NULL AFTER `vehicle_min_weight`;


ALTER TABLE `vehicle_type` ADD `vehicle_max_length` DECIMAL(12.2) NOT NULL AFTER `vehicle_min_length`;

ALTER TABLE `vehicle_type` ADD `vehicle_max_height` DECIMAL(12.2) NOT NULL AFTER `vehicle_min_height`;

ALTER TABLE `vehicle_type` ADD `vehicle_max_breadth` DECIMAL(12.2) NOT NULL AFTER `vehicle_min_breadth`;

ALTER TABLE `vehicle_type` ADD `vehicle_max_weight` DECIMAL(12.2) NOT NULL AFTER `vehicle_min_weight`;

ALTER TABLE `vehicle_type` ADD `vehicle_max_volume` DECIMAL(12.2) NOT NULL AFTER `vehicle_min_volume`;

ALTER TABLE `vehicle_type` CHANGE `vehicle_max_length` `vehicle_max_length` DECIMAL(12,2) NOT NULL;

ALTER TABLE `vehicle_type` CHANGE `vehicle_max_height` `vehicle_max_height` DECIMAL(12,2) NOT NULL;

ALTER TABLE `vehicle_type` CHANGE `vehicle_max_breadth` `vehicle_max_breadth` DECIMAL(12,2) NOT NULL;

ALTER TABLE `vehicle_type` CHANGE `vehicle_max_weight` `vehicle_max_weight` DECIMAL(12,3) NOT NULL;

ALTER TABLE `vehicle_type` CHANGE `vehicle_max_volume` `vehicle_max_volume` DECIMAL(12,2) NOT NULL;

ALTER TABLE `vehicle_type` CHANGE `vehicle_max_volume` `vehicle_max_volume` DECIMAL(12,3) NOT NULL;



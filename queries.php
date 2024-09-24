22/01/2018
##FIRST_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##~##PROJECT_NAME##~##AMOUNT_PAID##


New Email Template Add FOr payment:
 ID: 15
INSERT INTO `email_template_translation` (`id`, `email_template_id`, `template_subject`, `template_html`, `locale`, `deleted_at`, `created_at`, `updated_at`) VALUES (NULL, '15', 'Quickpick: Driver Payment', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! Your Payment Successfull Done \"##PROJECT_NAME##\".</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##FIRST_NAME## ##LAST_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Paid Amount : &nbsp;##AMOUNT_PAID##&nbsp;</p>', 'en', NULL, NULL, NULL);


UPDATE `email_template_translation` SET `deleted_at` = NULL, `created_at` = CURRENT_TIME(), `updated_at` = CURRENT_TIME() WHERE `email_template_translation`.`id` = 15;


Email Template

INSERT INTO `email_template` (`id`, `template_name`, `template_from`, `template_from_mail`, `template_variables`, `deleted_at`, `created_at`, `updated_at`) VALUES (NULL, 'Admin Driver Payment', 'SUPER-ADMIN', 'info@quickpick.com', '##FIRST_NAME##~##LAST_NAME##~##EMAIL##~##PROJECT_NAME##~##AMOUNT_PAID##', NULL, CURRENT_TIME(), CURRENT_TIME());


UPDATE `email_template` SET `template_variables` = '##FIRST_NAME##~##LAST_NAME##~##EMAIL##~##PROJECT_NAME##~##AMOUNT_PAID##', `deleted_at` = NULL WHERE `email_template`.`id` = 15;



23/01/2018

INSERT INTO `modules` (`id`, `title`, `slug`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES (NULL, 'My Earning', 'my_earning', '1', CURRENT_TIMESTAMP, NULL, NULL);


INSERT INTO `email_template` (`id`, `template_name`, `template_from`, `template_from_mail`, `template_variables`, `deleted_at`, `created_at`, `updated_at`) VALUES (NULL, 'Admin Company Payment', 'SUPER-ADMIN', 'info@quickpick.com', '##COMPANY_NAME##~##EMAIL##~##PROJECT_NAME##~##AMOUNT_PAID##', NULL, '2018-01-22 19:34:14', '2018-01-22 19:34:14');

INSERT INTO `email_template_translation` (`id`, `email_template_id`, `template_subject`, `template_html`, `locale`, `deleted_at`, `created_at`, `updated_at`) VALUES (NULL, '15', 'Quickpick: Company Payment', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##COMPANY_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! Your Payment Successfull Done \"##PROJECT_NAME##\".</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##COMPANY_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Paid Amount : &nbsp;##AMOUNT_PAID##&nbsp;</p>', 'en', NULL, '2018-01-22 19:32:02', '2018-01-22 19:32:02');

{"account_settings.list":true,"account_settings.create":true,"account_settings.update":true,"account_settings.delete":true,"admin_bonus.list":true,"admin_bonus.create":true,"admin_bonus.update":true,"admin_bonus.delete":true,"admin_commission.list":true,"admin_commission.create":true,"admin_commission.update":true,"admin_commission.delete":true,"advertisement.list":true,"advertisement.create":true,"advertisement.update":true,"advertisement.delete":true,"assigned_area.list":true,"assigned_area.create":true,"assigned_area.update":true,"assigned_area.delete":true,"booking_summary.list":true,"booking_summary.create":true,"booking_summary.update":true,"booking_summary.delete":true,"change_password.list":true,"change_password.create":true,"change_password.update":true,"change_password.delete":true,"company.list":true,"company.create":true,"company.update":true,"company.delete":true,"contact_enquiry.list":true,"contact_enquiry.create":true,"contact_enquiry.update":true,"contact_enquiry.delete":true,"driver.list":true,"driver.create":true,"driver.update":true,"driver.delete":true,"driver.approve":true,"driver_cars.list":true,"driver_cars.create":true,"driver_cars.update":true,"driver_cars.delete":true,"email_template.list":true,"email_template.create":true,"email_template.update":true,"email_template.delete":true,"promo_offer.list":true,"promo_offer.create":true,"promo_offer.update":true,"promo_offer.delete":true,"rider.list":true,"rider.create":true,"rider.update":true,"rider.delete":true,"services.list":true,"services.create":true,"services.update":true,"services.delete":true,"site_settings.list":true,"site_settings.create":true,"site_settings.update":true,"site_settings.delete":true,"static_pages.list":true,"static_pages.create":true,"static_pages.update":true,"static_pages.delete":true,"sub_admin.list":true,"sub_admin.create":true,"sub_admin.update":true,"sub_admin.delete":true,"track_booking.list":true,"track_booking.create":true,"track_booking.update":true,"track_booking.delete":true,"vehicle.list":true,"vehicle.create":true,"vehicle.update":true,"vehicle.delete":true,"review_tag.list":true,"review_tag.create":true,"review_tag.update":true,"my_earning.update":true,"review_tag.delete":true}



24-01-2018

ALTER TABLE `deposit_money` ADD `status` ENUM('UNAPPROVED','APPROVED') NOT NULL DEFAULT 'UNAPPROVED' AFTER `receipt_image`;


ALTER TABLE `deposit_money` CHANGE `status` `status` ENUM('PENDING','UNAPPROVED','APPROVED') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'PENDING';






24-10-2018
ALTER TABLE `load_post_request` CHANGE `request_status` `request_status` ENUM('NEW_REQUEST','USER_REQUEST','ACCEPT_BY_USER','REJECT_BY_USER','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER','TIMEOUT','CANCEL_BY_USER','CANCEL_BY_ADMIN') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;


ALTER TABLE `load_post_request_history` CHANGE `status` `status` ENUM('NEW_REQUEST','USER_REQUEST','ACCEPT_BY_USER','REJECT_BY_USER','ACCEPT_BY_DRIVER','REJECT_BY_DRIVER','TIMEOUT','REPOST','CANCEL_BY_USER','CANCEL_BY_ADMIN') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;


25-10-2018
ALTER TABLE `vehicle_type` CHANGE `slug` `vehicle_type_slug` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;


ALTER TABLE `load_post_request` ADD `is_request_process` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `is_future_request`; 



ALTER TABLE `load_post_request` ADD `request_time` TIME NULL DEFAULT NULL AFTER `date`;


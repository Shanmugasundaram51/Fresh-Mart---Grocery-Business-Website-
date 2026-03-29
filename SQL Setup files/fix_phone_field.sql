-- Quick fix for phone field issue
-- Run this in phpMyAdmin SQL tab

USE onlinesale;

-- Make phone field optional
ALTER TABLE `users` 
MODIFY COLUMN `phone` varchar(15) DEFAULT NULL;

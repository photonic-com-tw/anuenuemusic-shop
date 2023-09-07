-- 2021-10-20 修改email寄送對象設定
	ALTER TABLE `newsletter_send_time` CHANGE 
	`send_target` `send_target` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '寄送對象';

-- 2021-10-20 修改成各館可獨立控制兌換點數比率
	UPDATE `excel` SET `value1` = 0, `value2` = '無用誤刪' WHERE `excel`.`id` in (1, 4);

-- 2021-11-11 修改會員資料預設值
	ALTER TABLE `account` CHANGE `birthday` `birthday` VARCHAR(11) NULL DEFAULT NULL;

-- 2021-12-17 再行銷系統加入範本功能
	CREATE TABLE `format` (
		`id` int(20) NOT NULL,
		`name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
		`data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
		`creat_time` int(255) DEFAULT NULL,
		`update_time` int(255) DEFAULT NULL,
		`status` int(11) DEFAULT '1'
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;
	ALTER TABLE `format`
		ADD PRIMARY KEY (`id`),
		ADD UNIQUE KEY `id` (`id`);
	ALTER TABLE `format`
		MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

-- 2022-02-08 會員加入推薦功能
    ALTER TABLE `account` 
    ADD `upline_user` INT(11) NOT NULL DEFAULT '0' COMMENT '上線會員id' AFTER `export`, 
    ADD `recommend_content` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '推廣文字' AFTER `upline_user`,
    ADD `share_pic` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '分享圖片' AFTER `recommend_content`,
    ADD `share_title` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '分享標題' AFTER `share_pic`,
    ADD `share_text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '分享說明' AFTER `share_title`;

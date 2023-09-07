-- 更正需求php版本
    UPDATE `system_intro` SET `php_version` = '7.2' WHERE `system_intro`.`system_id` = 1;

-- 加入商品詢問信
    ALTER TABLE `system_email` ADD `product_qa` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '商品詢問信' AFTER `order_cancel`;


-- 2021-07-06 新增商品品項對應商品圖片
    ALTER TABLE `productinfo_type` ADD `pic_index` INT NOT NULL DEFAULT '1' COMMENT '對應商品圖片第幾張' AFTER `title`;
-- 2021-07-06 商品新增第五塊說明
    ALTER TABLE `productinfo` CHANGE `updatetime` `updatetime` TIMESTAMP NULL DEFAULT NULL;
    ALTER TABLE `productinfo` ADD `text5` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL AFTER `text4_online`, 
    ADD `text5_online` TINYINT(4) NOT NULL DEFAULT '1' AFTER `text5`;
    ALTER TABLE `default_content` ADD `text5` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL AFTER `text4`;
-- 2021-07-06 推薦商品修正改為無限量
    ALTER TABLE `productinfo` ADD `pushitem` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '推薦商品(品號 ,分隔)' AFTER `updatetime`;
    ALTER TABLE `productinfo`
      DROP `pushitem1`,
      DROP `pushitem2`,
      DROP `pushitem3`,
      DROP `pushitem4`;
-- 2021-07-08 商品加入人氣功能
    CREATE TABLE `product_love` ( `id` INT(11) NOT NULL , `user_id` INT(11) NOT NULL COMMENT '使用者id' , `product_id` INT(11) NOT NULL COMMENT '商品id' , `datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '時間' ) ENGINE = InnoDB;
    ALTER TABLE `product_love` ADD PRIMARY KEY( `id`);
    ALTER TABLE `product_love` CHANGE `id` `id` int(11) AUTO_INCREMENT;
-- 2021-07-09 商品加入收藏功能
    CREATE TABLE `product_store` (
      `id` int(11) NOT NULL,
      `user_id` int(11) NOT NULL COMMENT '使用者id',
      `product_id` int(11) NOT NULL COMMENT '商品id',
      `datetime` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '時間'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ALTER TABLE `product_store`
      ADD PRIMARY KEY (`id`);
    ALTER TABLE `product_store`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


-- 2021-07-26 加入我要找貨功能
    CREATE TABLE `contact_find_prod` ( 
      `id` INT NOT NULL , 
      `user_id` INT NOT NULL , 
      `ask` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '詢問內容{name, unit, num, img, note}',
      `createdate` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '建立時間' , 
      `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '狀態 0.未處理 1.以處理' , 
      `resp` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '回覆內容' , 
      `respdate` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '回覆時間'
    ) ENGINE = MyISAM;
    ALTER TABLE `contact_find_prod` ADD PRIMARY KEY( `id`);
    ALTER TABLE `contact_find_prod` CHANGE id id int(11) AUTO_INCREMENT;

    ALTER TABLE `contact_find_prod` 
    ADD `user_name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名' AFTER `user_id`, 
    ADD `user_phone` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '手機' AFTER `user_name`, 
    ADD `user_email` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '信箱' AFTER `user_phone`;


    INSERT INTO `backstage_menu_second` (`id`, `name`, `show_name`, `url`, `Front_desk`, `count_id`, `sort`, `backstage_menu_id`, `important`, `class`, `target`) 
    VALUES (NULL, '找貨回函', '找貨回函', '/admin/Findorder/findorder', 'index/findorder/findorder', NULL, '2', '4', '0', 'findorder_findorder', '_parent');
    UPDATE `backstage_menu_second` SET `sort` = '3' WHERE `backstage_menu_second`.`name` = '常見問題';
    UPDATE `backstage_menu_second` SET `sort` = '4' WHERE `backstage_menu_second`.`name` = '經銷據點';

    INSERT INTO `frontend_menu_name` (`id`, `name`, `en_name`, `controller`, `second_menu`) VALUES ('8', '幫我找貨', 'Findorder', 'findorder', NULL);


-- 2021-08-11 首頁管理關閉分館廣告不關閉分館
    ALTER TABLE `product` ADD `ad_online` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '顯示分館廣告 0.否 1.是' AFTER `online`;


-- 2021-09-01 優惠券、直接輸入型優惠券改名稱
    UPDATE `backstage_menu_second` SET `show_name` = '會員優惠券' WHERE `backstage_menu_second`.`id` = 9;
    UPDATE `backstage_menu_second` SET `show_name` = '活動優惠券' WHERE `backstage_menu_second`.`id` = 68;
-- 2021-09-10 後台可自行修改favicon
    ALTER TABLE `admin_info` ADD `favicon` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '分頁縮圖favicon.ico' AFTER `id`;

-- 2021-09-17 可自行設定成功/錯誤提示訊息圖片
    ALTER TABLE `admin_info` ADD 
    `success_logo` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '成功提示訊息圖片' AFTER `marketing_logo`, 
    ADD `error_logo` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '失敗提示訊息圖片' AFTER `success_logo`;

-- 2021-09-24 活動優惠券做資料庫防呆(user_code不可重複)
    ALTER TABLE `coupon_direct` CHANGE `user_code` `user_code` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
    ALTER TABLE `coupon_direct` CHANGE `content` `content` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL;
    ALTER TABLE `coupon_direct` ADD UNIQUE( `user_code`);
-- 2021-09-27 拔除angular
    UPDATE `system_intro` set `front_end` = 'bootstrap\r\nvue.js', `back_end` = 'bootstrap\r\nvue.js';


-- 2021-09-27 開發報名功能
    INSERT INTO `backstage_menu_second` (`id`, `name`, `show_name`, `url`, `Front_desk`, `count_id`, `sort`, `backstage_menu_id`, `important`, `class`, `target`) VALUES 
    (NULL, '常用欄位管理', '常用欄位管理', '/admin/fields/fields_set', NULL, NULL, '16', '8', '0', 'fields_fields_set', '_parent'),
    (NULL, '常用註記詞管理', '常用註記詞管理', '/admin/fields/comments_set', NULL, NULL, '16', '8', '0', 'fields_comments_set', '_parent');
    -- 常用註記詞管理
        CREATE TABLE `comments_set` (
          `id` int(11) NOT NULL,
          `title` text COLLATE utf8_unicode_ci NOT NULL COMMENT '標題',
          `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '內容',
          `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
          `online` tinyint(1) NOT NULL DEFAULT 1 COMMENT '狀態 0.停用 1.啟用'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ALTER TABLE `comments_set`
          ADD PRIMARY KEY (`id`);
        ALTER TABLE `comments_set`
          MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    -- 常用欄位管理
        CREATE TABLE `fields_set` ( 
        `id` INT NOT NULL , 
        `title` TEXT NOT NULL COMMENT '標題' , 
        `type` TEXT NOT NULL COMMENT '輸入方式' , 
        `required` TINYINT NOT NULL DEFAULT '0' COMMENT '必填 0.否 1.是' , 
        `special` TINYINT NOT NULL DEFAULT '0' COMMENT '特殊欄位 0.否 1.是' , 
        `limit` TEXT NULL DEFAULT NULL COMMENT '限定格式' , 
        `discription` TEXT NOT NULL DEFAULT '' COMMENT '說明' , 
        `options` TEXT NULL DEFAULT NULL COMMENT '選項(json格式)' , 
        `order_id` INT(11) NOT NULL COMMENT '排序' , 
        `online` TINYINT NOT NULL DEFAULT '1' COMMENT '狀態 0.停用 1.啟用' ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ALTER TABLE `fields_set`
          ADD PRIMARY KEY (`id`);
        ALTER TABLE `fields_set`
          MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        INSERT INTO `fields_set` (`id`, `title`, `type`, `required`, `special`, `limit`, `discription`, `options`, `order_id`, `online`) VALUES
        (1, '姓名', 'text', 1, 0, '.+', '<br />', '[\"選項內容1\",\"選項內容2\",\"選項內容3\",\"選項內容4\"]', -100, 1),
        (2, 'Email', 'text', 1, 0, '[^@ \\t\\r\\n]+@[^@ \\t\\r\\n]+\\.[^@ \\t\\r\\n]+', '', '[]', -99, 1);
    -- 商品設定獨立報名欄位
        CREATE TABLE `productinfo_register_fields` ( 
        `id` INT NOT NULL , 
        `prod_id` INT(11) NOT NULL COMMENT '商品id', 
        `title` TEXT NOT NULL COMMENT '標題' , 
        `type` TEXT NOT NULL COMMENT '輸入方式' , 
        `required` TINYINT NOT NULL DEFAULT '0' COMMENT '必填 0.否 1.是' , 
        `special` TINYINT NOT NULL DEFAULT '0' COMMENT '特殊欄位 0.否 1.是' , 
        `limit` TEXT NULL DEFAULT NULL COMMENT '限定格式' , 
        `discription` TEXT NOT NULL DEFAULT '' COMMENT '說明' , 
        `options` TEXT NULL DEFAULT NULL COMMENT '選項(json格式)' , 
        `order_id` INT(11) NOT NULL COMMENT '排序' , 
        `online` TINYINT NOT NULL DEFAULT '1' COMMENT '狀態 0.停用 1.啟用' ) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ALTER TABLE `productinfo_register_fields`
            ADD PRIMARY KEY (`id`);
        ALTER TABLE `productinfo_register_fields`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ALTER TABLE `productinfo_register_fields` ADD `fields_set_id` INT(11) NOT NULL DEFAULT '0' COMMENT '常用欄位id' AFTER `id`;
    -- 調整報名資料表欄位
        ALTER TABLE `examinee_info`
            DROP `parents_name`,
            DROP `parents_phone`,
            DROP `parents_mail`,
            DROP `parents_tel`,
            DROP `parents_add`,
            DROP `examinee_school`,
            DROP `examinee_class`,
            DROP `examinee_name`,
            DROP `examinee_birthday`,
            DROP `examinee_id`,
            DROP `examinee_note`,
            DROP `exam_school`,
            DROP `examinee_room`,
            DROP `examinee_site`,
            DROP `examinee_ticket`,
            DROP `grade`,
            DROP `grade_point`,
            DROP `grade_note`,
            DROP `grade_show`;
        ALTER TABLE `examinee_info` 
        ADD `session_id` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'session_id' AFTER `user_id`, 
        ADD `register_data` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '報名資料' AFTER `session_id`;
        ALTER TABLE `examinee_info` CHANGE `order_id` `order_id` INT(11) NOT NULL COMMENT '0.報名未成立';
        ALTER TABLE `examinee_info` ADD `email` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '報名者信箱(考生)' AFTER `user_id`;
        ALTER TABLE `examinee_info` ADD `cancel` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '取消 0.未取消 1.取消' AFTER `register_data`;
        ALTER TABLE `examinee_info` 
        ADD `roll_call` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '點名 0.無 1.未到 2.到' AFTER `register_data`, 
        ADD `roll_call_time` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '點名時間' AFTER `roll_call`;
        ALTER TABLE `examinee_info` ADD `datetime` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '建立日期' AFTER `user_id`;
        ALTER TABLE `examinee_info` ADD `name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '姓名' AFTER `datetime`;
    -- 調整商品上架(品項)
        ALTER TABLE `productinfo_type` ADD 
        `start_time` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '報名開始時間' AFTER `online`, 
        ADD `end_time` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '報名結束時間' AFTER `start_time`, 
        ADD `act_time` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '活動日期' AFTER `end_time`, 
        ADD `closed` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '停止開課 0.否 1.是' AFTER `act_time`, 
        ADD `closed_date` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '取消課程日期' AFTER `closed`;
        ALTER TABLE `productinfo_type` 
        CHANGE `start_time` `start_time` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '報名開始時間', 
        CHANGE `end_time` `end_time` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '報名結束時間', 
        CHANGE `act_time` `act_time` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '活動提醒時間';
    -- 商品加入點名判斷
        ALTER TABLE `productinfo` ADD `is_roll_call` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否需點名 0.否 1.是' AFTER `is_registrable`;
        ALTER TABLE `productinfo` ADD `remind_msg` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '活動提醒訊息' AFTER `expire_date`;
    -- 活動提醒信
        ALTER TABLE `system_email` ADD `act_remind` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '活動提醒信' AFTER `product_qa`;
        ALTER TABLE `system_email` ADD `act_cancel` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '活動取消信' AFTER `act_remind`;
        UPDATE `system_email` SET 
        `act_remind` = '<p class=\"MsoNormal\"> 若有任何疑問，<br />歡迎聯繫傳訊光測試商城shopfull001a客服：<span>XXXXXXXXXX&nbsp;</span>或 XXXXXXXXXX</p><p class=\"MsoNormal\"> <span style=\"color:#E53333;\">【營業時間】</span>週一至週五XX:XX-XX:XX</p><p class=\"MsoNormal\"> <span style=\"color:#E53333;\">【電話】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【傳真】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【地址】</span>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p><p class=\"MsoNormal\"> <span style=\"color:#E53333;\">≡ 此信件為系統自動發送，請勿直接回覆</span><span style=\"color:#E53333;\">&nbsp;≡</span></p>',
        `act_cancel` = '<p class=\"MsoNormal\"> 若有任何疑問，<br />歡迎聯繫傳訊光測試商城shopfull001a客服：<span>XXXXXXXXXX&nbsp;</span>或 XXXXXXXXXX</p><p class=\"MsoNormal\"> <span style=\"color:#E53333;\">【營業時間】</span>週一至週五XX:XX-XX:XX</p><p class=\"MsoNormal\"> <span style=\"color:#E53333;\">【電話】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【傳真】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【地址】</span>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p><p class=\"MsoNormal\"> <span style=\"color:#E53333;\">≡ 此信件為系統自動發送，請勿直接回覆</span><span style=\"color:#E53333;\">&nbsp;≡</span></p>' 
        WHERE `system_email`.`id` = 1;
    -- 調整商品、品項、報名關係
        ALTER TABLE `productinfo_type` 
        CHANGE `act_time` `act_time` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '活動開始時間';
        ALTER TABLE `productinfo_type` 
        ADD `act_time_end` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '活動結束時間' AFTER `act_time`, 
        ADD `act_remind_time` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '活動提醒時間' AFTER `act_time_end`;
    -- 刪除不必要的時間欄位
        ALTER TABLE `productinfo`
        DROP `examinee_date`,
        DROP `expire_date`;

-- 2021-10-20 修改成各館可獨立控制兌換點數比率
    INSERT INTO `points_setting` (`id`, `value`, `note`) VALUES ('3', '1000', '多少元換一點');

-- 2021-11-03 累積消費兌換、單次消費抽抽樂
    INSERT INTO `backstage_menu_second` (`id`, `name`, `show_name`, `url`, `Front_desk`, `count_id`, `sort`, `backstage_menu_id`, `important`, `class`, `target`) VALUES 
    (NULL, '消費累積兌換', '消費累積兌換', '/admin/Consumption/exchange', 'index/consumption/exchange', NULL, '7', '3', '0', 'consumption_exchange', '_parent'), 
    (NULL, '消費抽抽樂', '消費抽抽樂', '/admin/Consumption/draw', 'index/consumption/draw', NULL, '8', '3', '0', 'consumption_draw', '_parent');
    CREATE TABLE `consumption_exchange` (
      `id` int(11) NOT NULL,
      `pic` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '圖片',
      `name` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '商品名稱',
      `price` int(11) DEFAULT NULL COMMENT '兌換累積金額',
      `online` tinyint(1) NOT NULL DEFAULT 1 COMMENT '狀態0.停用 1.啟用'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ALTER TABLE `consumption_exchange`
      ADD PRIMARY KEY (`id`);
    ALTER TABLE `consumption_exchange`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    CREATE TABLE `consumption_draw` (
      `id` int(11) NOT NULL,
      `pic` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '圖片',
      `name` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '商品名稱',
      `ratio` int(11) DEFAULT NULL COMMENT '佔比',
      `online` tinyint(1) NOT NULL DEFAULT 1 COMMENT '狀態0.停用 1.啟用'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ALTER TABLE `consumption_draw`
      ADD PRIMARY KEY (`id`);
    ALTER TABLE `consumption_draw`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    ALTER TABLE `consumption_draw` CHANGE `ratio` `ratio` INT(11) NULL DEFAULT '1' COMMENT '佔比';
    CREATE TABLE `consumption_draw_limit` (
      `id` int(11) NOT NULL,
      `price` int(11) DEFAULT NULL COMMENT '滿多少元抽一次'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ALTER TABLE `consumption_draw_limit`
      ADD PRIMARY KEY (`id`);
    ALTER TABLE `consumption_draw_limit`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    INSERT INTO `consumption_draw_limit` (`id`, `price`) VALUES ('1', '1000');
    INSERT INTO `frontend_menu_name` (`id`, `name`, `en_name`, `controller`, `second_menu`) VALUES (9, '消費功能', 'Consumption', 'consumption', NULL);
    UPDATE `frontend_menu_name` SET `second_menu` = '{\"gift\":{\"name\":\"消費累積兌換\", \"en_name\":\"Gift\"},\"luckdraw\":{\"name\":\"消費抽抽樂\", \"en_name\":\"Lucky Draw\"},\"createpay\":{\"name\":\"新增付款\", \"en_name\":\"Pay\"}}' 
    WHERE `frontend_menu_name`.`id` = 9;
    CREATE TABLE `consumption_pay_record` ( 
      `id` INT(11) NOT NULL , 
      `user_id` INT(11) NOT NULL COMMENT '會員id' , 
      `price` INT(11) NOT NULL COMMENT '金額' , 
      `datetime` TEXT NULL DEFAULT NULL COMMENT '付款時間' , 
      `audit` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '審核狀態 0.未通過 1.通過' 
    ) ENGINE = InnoDB;
    ALTER TABLE `consumption_pay_record` ADD PRIMARY KEY( `id`);
    ALTER TABLE `consumption_pay_record` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    INSERT INTO `backstage_menu_second` (`id`, `name`, `show_name`, `url`, `Front_desk`, `count_id`, `sort`, `backstage_menu_id`, `important`, `class`, `target`) VALUES 
    (NULL, '掃碼付款管理', '掃碼付款管理', '/admin/consumption/pay_list', 'index/consumption/create_pay', NULL, '3', '5', '0', 'consumption_pay_list', '_parent');
    CREATE TABLE `consumption_draw_record` ( 
      `id` INT(11) NOT NULL , 
      `user_id` INT(11) NOT NULL COMMENT '會員id' , 
      `gift_pic` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '贈品圖片' , 
      `gift_name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '贈品名稱' , 
      `createdate` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '建立日期' , 
      `show` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '刮刮樂狀態 0.未刮 1.刮完' , 
      `ex_date` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '兌換日期(無則表示未兌換)' 
    ) ENGINE = InnoDB;
    ALTER TABLE `consumption_draw_record` ADD PRIMARY KEY( `id`);
    ALTER TABLE `consumption_draw_record` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    ALTER TABLE `consumption_draw_record` ADD `order_id` INT NOT NULL COMMENT '訂單id' AFTER `user_id`;
    ALTER TABLE `consumption_draw_record` ADD `pay_record_id` INT NOT NULL COMMENT '對應付款紀錄id(非0表掃碼付款) ' AFTER `user_id`;
    ALTER TABLE `consumption_draw_record` CHANGE `ex_date` `ex_date` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '兌換日期(無則表示未兌換)';
    ALTER TABLE `consumption_draw_record` ADD `draw_id` INT(11) NOT NULL COMMENT '對應刮刮樂贈品id' AFTER `order_id`;
    CREATE TABLE `consumption_exchange_record` ( 
      `id` INT(11) NOT NULL , 
      `user_id` INT(11) NOT NULL COMMENT '會員id' , 
      `exchange_id` INT(11) NOT NULL COMMENT '兌換商品id' , 
      `ex_date` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '兌換時間' 
    ) ENGINE = InnoDB;
    ALTER TABLE `consumption_exchange_record` ADD PRIMARY KEY( `id`);
    ALTER TABLE `consumption_exchange_record` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    ALTER TABLE `consumption_exchange_record` 
    ADD `pic` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '贈品圖片' AFTER `exchange_id`, 
    ADD `name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '贈品名稱' AFTER `pic`;

-- 2021-11-25 首頁大圖輪播可停用
    ALTER TABLE `slideshow` ADD `online` TINYINT NOT NULL DEFAULT '1' COMMENT '狀態 0.隱藏 1.顯示' AFTER `link`;

-- 2021-12-08 修正資料庫格式
    ALTER TABLE `consumption_draw_record` 
    CHANGE `gift_pic` `gift_pic` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '贈品圖片', 
    CHANGE `gift_name` `gift_name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '贈品名稱';
    UPDATE `frontend_menu_name` 
    SET `second_menu` = '{\"gift\":{\"name\":\"消費累積兌換\", \"en_name\":\"Gift\"},\"luckdraw\":{\"name\":\"消費刮刮樂\", \"en_name\":\"Lucky Draw\"},\"createpay\":{\"name\":\"新增付款\", \"en_name\":\"Pay\"}}' WHERE `frontend_menu_name`.`id` = 9;
    UPDATE `backstage_menu_second` SET `show_name` = '消費刮刮樂' WHERE `backstage_menu_second`.`id` = 73;
    ALTER TABLE `examinee_info` CHANGE `type_id` `type_id` TEXT NOT NULL;
    ALTER TABLE `examinee_info` CHANGE `roll_call_time` `roll_call_time` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '點名時間';
    ALTER TABLE `productinfo` CHANGE `display` `display` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL;

-- 2022-01-17 更新隱私政策(加入資料刪除)
    UPDATE `consent` SET `privacy` = '<p> 非常歡迎您光臨<span>「傳訊光科技測試購物</span><span>網站</span><span>」</span>（以下簡稱本網站），為了讓您能夠安心的使用本網站的各項服務與資訊，特此向您說明本網站的隱私權保護政策，以保障您的權益，請您詳閱下列內容：</p><h3> <a id=\"user-content-一隱私權保護政策的適用範圍-1\" class=\"anchor\" href=\"#一隱私權保護政策的適用範圍-1\"></a>一、隱私權保護政策的適用範圍</h3><p>  隱私權保護政策內容，包括本網站如何處理在您使用網站服務時收集到的個人識別資料。隱私權保護政策不適用於本網站以外的相關連結網站，也不適用於非本網站所委託或參與管理的人員。</p><h3>  <a id=\"user-content-二個人資料的蒐集處理及利用方式-1\" class=\"anchor\" href=\"#二個人資料的蒐集處理及利用方式-1\"></a>二、個人資料的蒐集、處理及利用方式與資料刪除</h3><ul> <li>    當您造訪本網站或使用本網站所提供之功能服務時，我們將視該服務功能性質，請您提供必要的個人資料，並在該特定目的範圍內處理及利用您的個人資料；非經您書面同意，本網站不會將個人資料用於其他用途。  </li> <li>    本網站在您使用服務信箱、問卷調查等互動性功能時，會保留您所提供的姓名、電子郵件地址、聯絡方式及使用時間等。 </li> <li>    於一般瀏覽時，伺服器會自行記錄相關行徑，包括您使用連線設備的IP位址、使用時間、使用的瀏覽器、瀏覽及點選資料記錄等，做為我們增進網站服務的參考依據，此記錄為內部應用，決不對外公佈。  </li> <li>    為提供精確的服務，我們會將收集的問卷調查內容進行統計與分析，分析結果之統計數據或說明文字呈現，除供內部研究外，我們會視需要公佈統計數據及說明文字，但不涉及特定個人之資料。 </li><li>如希望刪除本網站對個人蒐集之資料，請填寫聯絡我們回函表，並附上相關帳戶資訊，我們在驗證過後將刪除相關資料。</li></ul><h3>  <a id=\"user-content-三資料之保護-1\" class=\"anchor\" href=\"#三資料之保護-1\"></a>三、資料之保護</h3><ul>  <li>    本網站主機均設有防火牆、防毒系統等相關的各項資訊安全設備及必要的安全防護措施，加以保護網站及您的個人資料採用嚴格的保護措施，只由經過授權的人員才能接觸您的個人資料，相關處理人員皆簽有保密合約，如有違反保密義務者，將會受到相關的法律處分。  </li> <li>    如因業務需要有必要委託其他單位提供服務時，本網站亦會嚴格要求其遵守保密義務，並且採取必要檢查程序以確定其將確實遵守。  </li></ul><h3>  <a id=\"user-content-四網站對外的相關連結-1\" class=\"anchor\" href=\"#四網站對外的相關連結-1\"></a>四、網站對外的相關連結</h3><p> 本網站的網頁提供其他網站的網路連結，您也可經由本網站所提供的連結，點選進入其他網站。但該連結網站不適用本網站的隱私權保護政策，您必須參考該連結網站中的隱私權保護政策。</p><h3> <a id=\"user-content-五與第三人共用個人資料之政策-1\" class=\"anchor\" href=\"#五與第三人共用個人資料之政策-1\"></a>五、與第三人共用個人資料之政策</h3><p> 本網站絕不會提供、交換、出租或出售任何您的個人資料給其他個人、團體、私人企業或公務機關，但有法律依據或合約義務者，不在此限。</p><p> 前項但書之情形包括不限於：</p><ul> <li>    經由您書面同意。  </li> <li>    法律明文規定。 </li> <li>    為免除您生命、身體、自由或財產上之危險。  </li> <li>    與公務機關或學術研究機構合作，基於公共利益為統計或學術研究而有必要，且資料經過提供者處理或蒐集者依其揭露方式無從識別特定之當事人。 </li> <li>    當您在網站的行為，違反服務條款或可能損害或妨礙網站與其他使用者權益或導致任何人遭受損害時，經網站管理單位研析揭露您的個人資料是為了辨識、聯絡或採取法律行動所必要者。  </li> <li>    有利於您的權益。  </li> <li>    本網站委託廠商協助蒐集、處理或利用您的個人資料時，將對委外廠商或個人善盡監督管理之責。 </li></ul><h3>  <a id=\"user-content-六cookie之使用-1\" class=\"anchor\" href=\"#六cookie之使用-1\"></a>六、Cookie之使用</h3><p> 為了提供您最佳的服務，本網站會在您的電腦中放置並取用我們的Cookie，若您不願接受Cookie的寫入，您可在您使用的瀏覽器功能項中設定隱私權等級為高，即可拒絕Cookie的寫入，但可能會導致網站某些功能無法正常執行 。</p><h3>  <a id=\"user-content-七隱私權保護政策之修正-1\" class=\"anchor\" href=\"#七隱私權保護政策之修正-1\"></a>七、隱私權保護政策之修正</h3><p>  本網站隱私權保護政策將因應需求隨時進行修正，修正後的條款將刊登於網站上。</p>' WHERE `consent`.`id` = 1;

-- 2022-01-18 運費標籤功能
    CREATE TABLE `shipping_fee_tag` ( 
      `id` INT NOT NULL , 
      `name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '運費金額名稱' , 
      `price` INT NOT NULL COMMENT '運費標籤金額' 
    ) ENGINE = InnoDB;
    ALTER TABLE `shipping_fee_tag` ADD PRIMARY KEY( `id`);
    ALTER TABLE `shipping_fee_tag` CHANGE `id` `id` int(11) AUTO_INCREMENT;
    ALTER TABLE `shipping_fee_tag` ADD `order_id` INT NOT NULL DEFAULT '0' COMMENT '排序' AFTER `price`;
    ALTER TABLE `productinfo` ADD `shipping_fee_tag` INT NOT NULL DEFAULT '0' COMMENT '套運運費標籤' AFTER `shipping_type`;
    INSERT INTO `backstage_menu_second` 
    (`id`, `name`, `show_name`, `url`, `Front_desk`, `count_id`, `sort`, `backstage_menu_id`, `important`, `class`, `target`) VALUES 
    (NULL, '運費標籤管理', '運費標籤管理', '/admin/shippingfeetag/index', NULL, NULL, '11', '8', '0', 'shippingfeetag_index', '_parent');
    UPDATE `backstage_menu_second` SET `show_name` = '運法管理' WHERE `backstage_menu_second`.`id` = 66;

-- 2022-02-07 名稱更換
    UPDATE `backstage_menu_second` SET `show_name` = '立馬省優惠' WHERE `backstage_menu_second`.`id` = 7;

-- 2022-03-10 紀錄申請wanpay金流
    CREATE TABLE `wanpay_data` ( 
        `id` INT NOT NULL , 
        `pay_way` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '無金流付款方式' , 
        `wanpay_way` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '旺沛金流付款方式' , 
        `shop_no` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '特店代號' , 
        `wanpay_key` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '特店金鑰' , 
        `regist_time` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '申請旺沛時間，吾則是未申請' 
    ) ENGINE = InnoDB;
    ALTER TABLE `wanpay_data` ADD PRIMARY KEY( `id`);
    ALTER TABLE `wanpay_data` CHANGE id id int AUTO_INCREMENT;
    INSERT INTO `wanpay_data` (`id`, `pay_way`, `wanpay_way`, `shop_no`, `wanpay_key`, `regist_time`) VALUES ('1', '[\r\n {\"show_name\":\"現場付款\", \"channel\":\"現場付款\"},\r\n {\"show_name\":\"ATM付款\", \"channel\":\"ATM付款\"}\r\n]', '[]', NULL, NULL, NULL);
    ALTER TABLE `wanpay_data` ADD `wanpay_content` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '金流填寫內容' AFTER `wanpay_key`;

-- 2022-03-11 首頁廣 三幅廣告-1 改 無限上傳
    CREATE TABLE `index_ad` (
      `id` int(11) NOT NULL,
      `pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
      `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
      `content` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
      `url` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
      `time` timestamp NOT NULL DEFAULT current_timestamp(),
      `online` tinyint(1) NOT NULL DEFAULT 1,
      `orders` int(11) NOT NULL DEFAULT 0 COMMENT '排序'
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
    ALTER TABLE `index_ad`
      ADD PRIMARY KEY (`id`);
    ALTER TABLE `index_ad`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- 2022-04-08 新增首頁管理功能
    ALTER TABLE `index_online` 
    ADD `block_iframe` TINYINT(4) NOT NULL DEFAULT '1' COMMENT '嵌入區開關' AFTER `block_edm`, 
    ADD `block_news` TINYINT(4) NOT NULL DEFAULT '1' COMMENT '最新消息開關' AFTER `block_iframe`;
    INSERT INTO `index_excel` (`id`, `data1`, `data2`, `data3`) VALUES (38, 'block_iframe', '', '');
-- 2022-04-08 最新消息加圖、小說明
    ALTER TABLE `news` ADD `pic` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL AFTER `id`;
    ALTER TABLE `news` ADD `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT '小說明' AFTER `title`;
-- 2022-04-08 展示頁面可上傳banner圖
    ALTER TABLE `frontend_menu_name`
    ADD `text_color` VARCHAR(12) NULL DEFAULT NULL COMMENT '文字色(色碼)' AFTER `second_menu`, 
    ADD `pic` TEXT NULL DEFAULT NULL COMMENT '背景圖片' AFTER `text_color`;
    INSERT INTO `backstage_menu_second`
    (`id`, `name`, `show_name`, `url`, `Front_desk`, `count_id`, `sort`, `backstage_menu_id`, `important`, `class`, `target`) VALUES
    (NULL, 'banner管理', 'banner管理', '/admin/banner/index', '', NULL, '-1', '6', '0', 'banner_index', '_parent');
-- 2022-04-11 校正有感體驗、活動專區資料流
    UPDATE `frontend_menu_name` SET `controller` = 'experience' WHERE `frontend_menu_name`.`id` = 3;
    UPDATE `frontend_menu_name` SET `controller` = 'activity' WHERE `frontend_menu_name`.`id` = 4;
    ALTER TABLE `experience` RENAME TO `experience1`;
    ALTER TABLE `activity` RENAME TO `experience`;
    ALTER TABLE `experience1` RENAME TO `activity`;
    UPDATE `backstage_menu_second` SET `Front_desk` = 'index/activity/activity' WHERE `backstage_menu_second`.`id` = 18;
    UPDATE `backstage_menu_second` SET `url` = '/admin/activity/index' WHERE `backstage_menu_second`.`id` = 18;
    UPDATE `backstage_menu_second` SET `class` = 'activity_index' WHERE `backstage_menu_second`.`id` = 18;
    UPDATE `backstage_menu_second` SET `Front_desk` = 'index/experience/experience' WHERE `backstage_menu_second`.`id` = 16;
    UPDATE `backstage_menu_second` SET `url` = '/admin/experience/index' WHERE `backstage_menu_second`.`id` = 16;
    UPDATE `backstage_menu_second` SET `class` = 'experience_index' WHERE `backstage_menu_second`.`id` = 16;
-- 2022-04-12 桃園改市
    UPDATE `city` SET `Name` = '桃園市' WHERE `city`.`AutoNo` = 7;
    UPDATE `town` SET `Name` = '中壢區' WHERE `town`.`AutoNo` = 76;
    UPDATE `town` SET `Name` = '平鎮區' WHERE `town`.`AutoNo` = 77;
    UPDATE `town` SET `Name` = '龍潭區' WHERE `town`.`AutoNo` = 78;
    UPDATE `town` SET `Name` = '楊梅區' WHERE `town`.`AutoNo` = 79;
    UPDATE `town` SET `Name` = '新屋區' WHERE `town`.`AutoNo` = 80;
    UPDATE `town` SET `Name` = '觀音區' WHERE `town`.`AutoNo` = 81;
    UPDATE `town` SET `Name` = '桃園區' WHERE `town`.`AutoNo` = 82;
    UPDATE `town` SET `Name` = '龜山區' WHERE `town`.`AutoNo` = 83;
    UPDATE `town` SET `Name` = '八德區' WHERE `town`.`AutoNo` = 84;
    UPDATE `town` SET `Name` = '大溪區' WHERE `town`.`AutoNo` = 85;
    UPDATE `town` SET `Name` = '復興區' WHERE `town`.`AutoNo` = 86;
    UPDATE `town` SET `Name` = '大園區' WHERE `town`.`AutoNo` = 87;
    UPDATE `town` SET `Name` = '蘆竹區' WHERE `town`.`AutoNo` = 88;
-- 2022-04-12 分館可顯示於選單及推薦商品
    ALTER TABLE `product` 
    ADD `recommend` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '推建商品(填入商品id逗點分隔)' AFTER `webtype_description`, 
    ADD `show_on_nav` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '顯示於選單' AFTER `recommend`;
-- 2022-04-13 報名資料添加修改截止日設定
    ALTER TABLE `productinfo` ADD `register_data_change_limit` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '報名資料修改截止日' AFTER `shipping_fee_tag`;
-- 2022-04-14 可開關商品總分類選單
    ALTER TABLE `index_online` ADD `product_nav_total` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '總商品選單' AFTER `block_news`;

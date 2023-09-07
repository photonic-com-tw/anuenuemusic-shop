-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 
-- 伺服器版本： 10.1.38-MariaDB
-- PHP 版本： 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `shop_test`
--

-- --------------------------------------------------------

--
-- 資料表結構 `about_story`
--

CREATE TABLE `about_story` (
  `id` int(11) NOT NULL,
  `image_left_top` varchar(128) NOT NULL,
  `image_right_top` varchar(128) NOT NULL,
  `image_right_bottom` varchar(128) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `mapurl` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `about_story`
--

INSERT INTO `about_story` (`id`, `image_left_top`, `image_right_top`, `image_right_bottom`, `content`, `mapurl`) VALUES
(1, '/upload/about_image_left_top.png?1256862870', '/upload/about_image_right_top.JPG?2032905688', '/upload/about_image_right_bottom.JPG?903510519', '<p class=\"MsoNormal\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<b><span style=\"font-size:24px;\">關於我們</span></b><b></b> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoNormal\">\r\n	 傳訊光科技成立於<span>2007</span>年台北大安區，我們是一家極注重「策略品質」與「行銷成果」的全方位數位媒體行銷顧問團隊，不只是提供專業性的媒體執行，我們更重視「對的策略、好的創意製作、優質的媒體成效」，結合市場、產品與消費者的洞察，更以顧問式的數位解決方案一站滿足客戶所有需求，依據各產業數位行銷任務打造點、線、面，縱深的品牌廣告綜效，協助客戶創造銷售！<span></span> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoNormal\">\r\n	本事業體分為：<br />\r\n※【創意製作-傳訊光科技】 網頁設計│平台系統開發│CRM內控系統│購物車系統開發<br />\r\n※【數位媒體整合行銷-和承國際】網路活動企劃│網路媒體宣傳│網紅直播策劃│社群口碑操作\r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoNormal\">\r\n	主要商品<span> / </span>服務項目：<span></span> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	我們提供：<span> (1)</span>網路短片製作拍攝<span> (2)</span>論壇口碑話題操作<span> (3)</span>網路新聞議題報導<span> (4)</span>網紅直播活動策劃<span> (5)</span>粉專經營代操服務<span> (6)</span>數位媒體廣告投放<span> (7)</span>網頁設計<span>/</span>購物車系統<span>/CRM/SEO</span>等服務。<span></span> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoNormal\">\r\n	公司官網 <span>https://www.photonic.com.tw/</span> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	購物車介紹 <span>https://www.photonic.com.tw/shopping_system.html</span> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	網頁設計案例 <span>https://www.photonic.com.tw/case.html</span> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<b>經營理念<span></span></b> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	我們堅信以人為本，誠信至上首重就是品德，也因為員工有良好的品德才可以提供給客戶最佳的服務及提昇技術，在我們企業體中就會有共同的信念及使命而企業及個人都可以共創出最高利益。<span></span> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<br />\r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<b>傳訊光科技股份有限公司<span></span></b> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	<b>PHOTONIC COMMUNICATION CORP.</b><b></b> \r\n</p>\r\n<p class=\"MsoNormal\">\r\n	電話<span> :02-2738-6266    </span>傳真<span> :02-2738-6255</span><br />\r\n臺北市信義區基隆路<span>2</span>段<span>189</span>號<span>16</span>樓之<span>8</span> \r\n</p>\r\n<p>\r\n	<br />\r\n</p>', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3615.2743343213997!2d121.55117601484169!3d25.02476248397663!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3442a975cb6c9925%3A0x61c46c671ff7e776!2z5YKz6KiK5YWJ56eR5oqALUNSTS3ntrLpoIHoqK3oqIgt57ay6Lev6KGM6Yq3!5e0!3m2!1szh-TW!2stw!4v1623382283060!5m2!1szh-TW!2stw\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>');

-- --------------------------------------------------------

--
-- 資料表結構 `act`
--

CREATE TABLE `act` (
  `id` int(11) NOT NULL,
  `number` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `img` text COMMENT '圖片',
  `ps` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `content` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `location` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `online1` tinyint(4) DEFAULT NULL,
  `condition1` int(11) DEFAULT NULL,
  `discount1` float DEFAULT NULL,
  `online2` int(11) DEFAULT NULL,
  `condition2` int(11) DEFAULT NULL,
  `discount2` float DEFAULT NULL,
  `online3` int(11) DEFAULT NULL,
  `condition3` int(11) DEFAULT NULL,
  `discount3` float DEFAULT NULL,
  `online` tinyint(4) NOT NULL DEFAULT '0',
  `act_type` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `activity`
--

CREATE TABLE `activity` (
  `id` int(11) NOT NULL,
  `pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `online` tinyint(1) NOT NULL DEFAULT '1',
  `orders` int(11) NOT NULL DEFAULT '0' COMMENT '排序'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `act_product`
--

CREATE TABLE `act_product` (
  `act_prod_id` int(11) NOT NULL,
  `act_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `addprice`
--

CREATE TABLE `addprice` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '加價購名稱',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '加價購說明',
  `discount` float NOT NULL DEFAULT '1' COMMENT '打幾折',
  `start_time` datetime NOT NULL COMMENT '開始時間',
  `end_time` datetime NOT NULL COMMENT '結束時間',
  `online` tinyint(1) NOT NULL DEFAULT '1' COMMENT '狀態 0.停用 1.啟用'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `addprice_product`
--

CREATE TABLE `addprice_product` (
  `id` int(11) NOT NULL,
  `addprice_id` int(11) NOT NULL COMMENT '加價購id',
  `product_type_id` int(11) NOT NULL COMMENT '商品品項id',
  `limit_num` int(11) NOT NULL DEFAULT '1' COMMENT '加價購上限'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `addprice_rule`
--

CREATE TABLE `addprice_rule` (
  `id` int(11) NOT NULL,
  `addprice_id` int(11) NOT NULL COMMENT '加價購id',
  `product_type_id` int(11) NOT NULL COMMENT '商品品項id'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `admin`
--

CREATE TABLE `admin` (
  `id` int(64) NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `account` text COLLATE utf8_unicode_ci NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `originalPassword` text COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `purview` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `admin`
--

INSERT INTO `admin` (`id`, `name`, `account`, `password`, `originalPassword`, `email`, `permission`, `purview`) VALUES
(1, 'photonic管理員', 'photonic', '48913f026dabd50a7dbe3b4621e84a8d', 'photo3599', NULL, 'all', NULL),
(2, '管理員', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'client@photonic.com.tw', 'current', '{\"8\":[\"27\",\"64\"]}'),
(3, '黃金管理員', 'GoldenAdmin', '81dc9bdb52d04dc20036dbd8313ed055', '1234', 'andyplanner@photonic.com.tw', 'no', '\"\"'),
(9, '陳叙佑', 'walkup@wellcare.com.tw', '81dc9bdb52d04dc20036dbd8313ed055', '1234', 'walkup@wellcare.com.tw', 'no', '\"\"');

-- --------------------------------------------------------

--
-- 資料表結構 `admin_info`
--

CREATE TABLE `admin_info` (
  `id` int(11) NOT NULL,
  `customer_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `system_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `marketing_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `url` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `tel` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `email` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `customer_logo` text CHARACTER SET utf32 COLLATE utf32_unicode_ci,
  `system_logo` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `marketing_logo` text CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `admin_info`
--

INSERT INTO `admin_info` (`id`, `customer_name`, `system_name`, `marketing_name`, `url`, `tel`, `email`, `address`, `customer_logo`, `system_logo`, `marketing_logo`) VALUES
(1, '購物車完整版', '傳訊光科技股份有限公司', '和承國際整合行銷', 'http://bigwell.com.tw/', '02-2738-6266', 'service@photonic.com.tw', '臺北市信義區基隆路２段189號16樓之8', NULL, '/upload/06/c8f380eb5d209a9c213e03380c0212.png', '');

-- --------------------------------------------------------

--
-- 資料表結構 `backstage_menu`
--

CREATE TABLE `backstage_menu` (
  `id` int(10) NOT NULL,
  `name` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `sort` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `backstage_menu`
--

INSERT INTO `backstage_menu` (`id`, `name`, `sort`) VALUES
(1, '首頁管理區', 1),
(2, '商品管理區', 2),
(3, '行銷管理區', 3),
(4, '客戶服務區', 4),
(5, '售後服務區', 5),
(6, '網頁編輯區', 6),
(7, '其它管理區', 7),
(8, '參數設定區', 8);

-- --------------------------------------------------------

--
-- 資料表結構 `backstage_menu_second`
--

CREATE TABLE `backstage_menu_second` (
  `id` int(11) NOT NULL,
  `name` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `show_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '顯示名稱',
  `url` varchar(100) DEFAULT NULL,
  `Front_desk` varchar(50) DEFAULT NULL,
  `count_id` varchar(20) DEFAULT NULL,
  `sort` int(10) DEFAULT NULL,
  `backstage_menu_id` int(10) DEFAULT NULL,
  `important` int(2) NOT NULL DEFAULT '0' COMMENT '醒目標示',
  `class` varchar(128) DEFAULT NULL COMMENT 'active用',
  `target` varchar(10) NOT NULL DEFAULT '_parent' COMMENT '開啟模式'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `backstage_menu_second`
--

INSERT INTO `backstage_menu_second` (`id`, `name`, `show_name`, `url`, `Front_desk`, `count_id`, `sort`, `backstage_menu_id`, `important`, `class`, `target`) VALUES
(27, '自動登出設定', '自動登出設定', '/admin/admin/maxlifetime_set', NULL, NULL, 13, 8, 0, 'admin_maxlifetime_set', '_parent'),
(25, '存放位置管理', '存放位置管理', '/admin/Position/index', NULL, NULL, 9, 8, 0, 'position_index', '_parent'),
(26, '點數設定', '點數設定', '/admin/admin/point_set', 'index/points/points', NULL, 6, 8, 0, 'admin_point_set', '_parent'),
(24, 'SEO設定', 'SEO設定', '/admin/seo/edit', NULL, NULL, 2, 8, 0, 'seo_edit', '_parent'),
(22, '同意書設定', '同意書設定', '/admin/consent/index', NULL, NULL, 1, 8, 0, 'consent_index', '_parent'),
(23, '帳號管理', '帳號管理', '/admin/admin/edit', NULL, NULL, 0, 8, 0, 'admin_edit', '_parent'),
(21, '會員管理後台', '會員管理後台', '/order/index/index.html', NULL, NULL, 0, 7, 0, NULL, '_blank'),
(62, 'LOGO管理', 'LOGO管理', '/admin/admin/admin_info', NULL, NULL, 20, 8, 0, 'admin_admin_info', '_parent'),
(19, '查看前台', '查看前台', '/index/index/index', NULL, NULL, 0, 7, 0, NULL, '_blank'),
(18, '活動專區', '活動專區', '/admin/experience/index', 'index/experience/experience', NULL, 0, 6, 0, 'experience_index', '_parent'),
(12, '經銷據點', '經銷據點', NULL, 'index/distribution/distribution', NULL, 0, 4, 0, NULL, '_parent'),
(13, '機身碼管理', '機身碼管理', '/admin/excel/index', NULL, NULL, 0, 5, 0, 'excel_index', '_parent'),
(14, '註冊商品回函', '註冊商品回函', '/admin/excel/reply', 'index/member/reg_product', NULL, 0, 5, 0, 'excel_reply', '_parent'),
(15, '最新消息', '最新消息', '/admin/news/index', 'index/news/news_c', NULL, 0, 6, 0, 'news_index', '_parent'),
(9, '優惠券專區', '優惠券專區', '/admin/Coupon/index', 'index/coupon/coupon', NULL, 0, 3, 0, 'coupon_index', '_parent'),
(10, '客戶來函', '客戶來函', '/admin/Contact/index', 'index/about/about_contact', NULL, 0, 4, 0, 'contact_index', '_parent'),
(17, '關於我們', '關於我們', '/admin/about/index', 'index/about/about_story', NULL, 0, 6, 0, 'about_index', '_parent'),
(16, '有感體驗', '有感體驗', '/admin/activity/index', 'index/activity/activity', NULL, 0, 6, 0, 'activity_index', '_parent'),
(11, '常見問題', '常見問題', '/admin/qa/index', 'index/qa/qa', NULL, 0, 4, 0, 'qa_index', '_parent'),
(8, '價格組合設定', '價格組合設定', '/admin/Disset/index', NULL, NULL, 7, 8, 0, 'disset_index', '_parent'),
(6, '活動優惠', '活動優惠', '/admin/Act/index', NULL, 'ActCount', 0, 3, 0, 'act_index', '_parent'),
(7, '折扣優惠', '折扣優惠', '/admin/discount/index', 'index/product/activity', 'DisCount', 0, 3, 0, 'discount_index', '_parent'),
(4, '庫存警示', '庫存警示', '/admin/limit/index', NULL, 'limitCount', 3, 2, 0, 'limit_index', '_parent'),
(3, '商品銷售', '實體銷存', '/admin/sell/index', NULL, NULL, 2, 2, 0, 'sell_index', '_parent'),
(2, '新增商品', '新增商品', '/admin/all/index', NULL, NULL, 1, 2, 0, 'all_index', '_parent'),
(1, '首頁編輯', '首頁編輯', '/admin/index/index', 'index/index/index', NULL, 0, 1, 0, 'index_index', '_parent'),
(28, '商品描述設定', '商品描述設定', '/admin/ProDesc/index', '', NULL, 8, 8, 0, 'prodesc_index', '_parent'),
(56, 'SEO行銷/發布設定', 'SEO行銷/發布設定', '/admin/seo/edit_social', NULL, NULL, 3, 8, 0, 'seo_edit_social', '_parent'),
(57, '進階SEO設定', '進階SEO設定', '/admin/seo/edit_advance', NULL, NULL, 4, 8, 0, 'seo_edit_advance', '_parent'),
(58, '標籤設定', '標籤設定', '/admin/tag/tag', NULL, NULL, 5, 8, 0, 'tag_tag', '_parent'),
(59, 'EDM管理後台', 'EDM管理後台', 'http://shop-edm.sprlight.net/admin', NULL, NULL, 0, 7, 0, NULL, '_blank'),
(60, '簡訊/寄信管理後台', '簡訊/寄信管理後台', 'http://shop-email.sprlight.net/', NULL, NULL, 0, 7, 0, NULL, '_blank'),
(61, '館/分類樹', '館/分類樹', '/admin/layertree/tree', 'index/product/product', NULL, 0, 2, 0, 'layertree_tree', '_parent'),
(63, '系統信管理', '系統信管理', '/admin/admin/system_email', NULL, NULL, 15, 8, 0, 'admin_system_email', '_parent'),
(65, '網紅列表', '網紅列表', '/admin/Kol/index', NULL, NULL, 10, 8, 0, 'kol_index', '_parent'),
(64, '版本說明', '版本說明', '/admin/System/index', NULL, NULL, 30, 8, 0, 'system_index', '_parent'),
(66, '運費管理', '運費管理', '/admin/shippingfee/index', NULL, NULL, 11, 8, 0, 'shippingfee_index', '_parent'),
(67, '加價購設定', '加價購設定', '/admin/Addprice/index', '', NULL, 2, 3, 0, 'addprice_index', '_parent'),
(68, '直接輸入型優惠券', '直接輸入型優惠券', '/admin/CouponDirect/index', '', NULL, 1, 3, 0, 'coupondirect_index', '_parent');

-- --------------------------------------------------------

--
-- 資料表結構 `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `data` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '[]'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `city`
--

CREATE TABLE `city` (
  `Name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `Order` bigint(20) NOT NULL,
  `State` smallint(6) NOT NULL,
  `AutoNo` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `city`
--

INSERT INTO `city` (`Name`, `Order`, `State`, `AutoNo`) VALUES
('台北市', 0, 0, 1),
('基隆市', 0, 0, 2),
('新北市', 0, 0, 3),
('宜蘭縣', 0, 0, 4),
('新竹市', 0, 0, 5),
('新竹縣', 0, 0, 6),
('桃園縣', 0, 0, 7),
('苗栗縣', 0, 0, 8),
('台中市', 0, 0, 9),
('彰化縣', 0, 0, 10),
('南投縣', 0, 0, 11),
('雲林縣', 0, 0, 12),
('嘉義市', 0, 0, 13),
('嘉義縣', 0, 0, 14),
('台南市', 0, 0, 15),
('高雄市', 0, 0, 16),
('南海諸島', 0, 0, 17),
('澎湖縣', 0, 0, 18),
('屏東縣', 0, 0, 19),
('台東縣', 0, 0, 20),
('花蓮縣', 0, 0, 21),
('金門縣', 0, 0, 22),
('連江縣', 0, 0, 23);

-- --------------------------------------------------------

--
-- 資料表結構 `consent`
--

CREATE TABLE `consent` (
  `id` int(11) NOT NULL,
  `member` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `point` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `coupon` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shopping` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '購物條款',
  `other` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '其他資訊',
  `privacy` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '隱私政策',
  `examination` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '報名流程說明',
  `g_process` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '分數查詢說明',
  `examinee` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '考生資料注意事項'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `consent`
--

INSERT INTO `consent` (`id`, `member`, `point`, `coupon`, `shopping`, `other`, `privacy`, `examination`, `g_process`, `examinee`) VALUES
(1, '<p class=\"MsoNormal\" style=\"font-family:&quot;font-size:medium;\">	<span style=\"font-family:&quot;\">會員條款</span></p><p class=\"MsoNormal\">	為保障您的權益，請您在同意註冊為<span>「傳訊光科技測試購物</span><span>網站</span><span>」</span>(以下稱本網站)會員前，詳細閱讀本同意書。本同意書內容詳述東城數位科技有限公司(以下簡稱本公司)所提供的服務，本同意書之目的，乃在於盡能保護會員在使用本網站服務時的權益，同時確認本公司會員之間的權利義務關係。請您在同意註冊成為會員前，詳細閱讀，並按下同意鍵，即可完成註冊動作。<br />本公司有權於任何時間基於需要而修訂或變更本同意書內容，並取代先前的內容，修改後的同意書內容將公佈在本服務『最新消息』的網站上，本公司將不會個別通知會員。您使用本網站時，應隨時注意相關內容的修改與變更。您於任何修改或變更之後繼續使用本服務，將視為您已經閱讀、瞭解且同意接受已完成的相關修改與變更，並以您同意接受的相關修改與變更作為雙方的契約更新內容。若您不同意上述的本同意書修訂或更新方式，或不接受本同意書的其他任一約定，您應立即停止使用本服務。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	一、 個人資料保護政策的適用範圍</p><p class=\"MsoNormal\">	1. 本公司遵守個人資料保護相關法令的規定。本公司對於您所登錄或留存的個人資料，在未獲得您的同意以前，絕不對非相關業務合作夥伴(含相關履行輔助人及代理人，以下合稱「策略夥伴」)以外之人揭露您的姓名、地址、電子郵件地址及其他依法受保護的個人資料。<br />2. 如果涉及司法調查、法律訴訟及主管機關來函要求時，不在此適用範圍內。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	二、 個人資料之收集及使用</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	1. 您同意本公司蒐集您的個人資料，以便在本公司經營範圍內執行會員事務(如會員管理、行銷、內部統計調查與分析、銷售產品及配運等)，您並同意在會員期間屆滿後本公司仍得在經營管理所需之法定期間內保存您的資料。您享有個人資料保護法第三條相關權利如下，但本公司因執行職務或業務所必須者，得拒絕之: (1)查詢或請求閱覽。(2)請求製給複製本。(3)請求補充或更正。(4)請求停止蒐集、處理及利用。(5)請求刪除。</p><p class=\"MsoNormal\">	2. 本公司可利用您這些資料寄送公司相關資料或服務，包括新產品及最近促銷活動。<br />3. 本公司會將您的個人資料做成會員統計資料，在不揭露您個人資料之前提下，可進行合法的使用以及公開。本公司也可能提供整體的統計資料予相關廣告商分析，但不會將您個人資料個別揭露。本公司不會銷售或租借其本公司之顧客名單給任何第三人。<br />4. 在下列的情況下，本公司有可能會提供您的個人資料給相關機關，或主張其權利受侵害並提示司法機關正式文件之第三人：<br />(1)基於法律之規定、或受司法機關與其他有權機關基於法定程序之要求；<br />(2)在緊急情況下為維護其他會員或第三人之合法權益；<br />(3)為維護會員服務系統的正常運作；<br />(4)會員透過本服務購物、兌換贈品，因而產生的金流、物流必要資訊；<br />(5)使用者有任何違反政府法令或本使用條款之情形。<br />關於您所登錄或留存之個人資料及其他特定資料（例如交易資料），悉依本公司「隱私權保護政策」受到保護與規範。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	三、 關於會員資料</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	1. 您於本網站註冊會員資料時，本網站為交易上安全，將以您的E-Mail為會員帳號。此外，您必須自行設定一組密碼做為個人辨識使用，以保障您的交易安全。</p><p class=\"MsoNormal\">	2. 您註冊會員時必須填寫確實資料，若發現有不實之登錄，或原所登錄資料已不符合真實未更新，本網站保留隨時暫停或終止您網路會員資格即使用各項服務資格之權益。若您冒用他人名義，並造成其他會員或本站之損失，本網站將追究相關法律責任，並提供資料配合司法機關調查。<br />3. 就本網站而言，各該使用者帳號及密碼的組合即代表使用者個人，其於使用本網站服務上之行為，均視為使用者的本人行為。您必須妥善設定、維護及保管您的帳號及密碼，包含但不限使用本網站服務結束時的適時登出手續。如果您洩漏自己的個人資料、會員密碼或付款資料，並使得第三人有使用的機會時，您必須就第三人的行為負全部責任。<br />4. 若未滿法定成人年齡欲加入會員時，在瀏覽網頁及執行購物行為時，請由監護人陪同在旁指導，若發生不當之購物行為，監護人應出面代表協調處理事宜。<br />5. 您如果選擇以信用卡方式購物時，必須填寫確實的信用卡資料。若發現有不實登錄或任何未經持卡人許可而盜刷其信用卡的情形時，本網站得以暫停或終止其會員資格，若違反相關法律，亦將依法追究。本網站為保護會員的隱私及權益，並不會留存會員在網站上登錄的信用卡號等金融資料。<br />6. 您的帳號若有被冒用時，應立即通知本公司。本公司於知悉您的帳號密碼被冒用時，將立即暫停該帳號所生交易的處理及後續利用。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	四、 關於網站經營</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	1. 本公司有權不經通知隨時停止或是更改各項服務內容。</p><p class=\"MsoNormal\">	2. 本網站所有資料包含文字、軟體、圖片、影片、音樂等，均受中華民國著作權法、商標法及國際智慧財產權等相關規定之保護，禁止未經授權的修改、抄襲、出租、外借、出售等行為，請充分尊重智慧財產權、著作權、專利權等相關權利，違者依法處置。<br />3. 本網站內部份刊登的文章及資訊引用專家學者的言論，僅供參考，不代表本網站的立場，您對於內容若有任何疑問，宜求教於專業人員，依個人狀況進行諮詢。<br />4. 本公司有權視情況採取電子郵件勸誡、減除某些會員權益、取消您的會員資格等措施，或是交由執法機關或主管單位處理。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	五、 資料保全</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	1. 為保障您的個人資料及使用安全，您在本網站的個人資料會用密碼保護。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	2. 承諾保護您個人資訊的安全，保護個人資料以防止未經授權的資料存取、使用或公開。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	六、停止或中斷提供服務</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	本公司確保以符合目前一般可合理期待安全性之方式及技術，維護本網站的正常運作。但在下列情況之下，本公司將有權暫停、中斷或拒絕提供本服務的全部或一部份，且不負責事先通知您的義務，本公司對使用者任何直接或間接的損害，均不負任何賠償或補償的義務：</p><p class=\"MsoNormal\">	1.對本服務相關軟硬體設備進行搬遷、更換、升級、保養或維修時；<br />2.使用者有任何違反政府法令或本同意書的情形；<br />3.天災或其他不可抗力因素所導致的服務停止或中斷；<br />4.其他不可歸責於本公司事由所致的服務停止或中斷；<br />5.非本公司事由而致本服務資訊顯示不正確、或遭偽造、竄改、刪除或擷取、或致系統中斷或不能正常運作時。<br />6.使用本公司簡訊發送或其他電子訊息傳輸服務時，出現誹謗、攻擊或傷害性文字，或其他違背公序良俗的內容，本公司有權拒絕您使用本服務。<br />7.其他本公司認為有需要停止或中斷服務的情形。<br />8.本公司針對可預知的軟硬體維護計畫，有可能導致系統中斷或是暫停時，將盡可能地於該狀況發生前，以適當方式於本網站公告。<br />9.本網站僅是依提供服務當時的既有項目、功能及現狀提供您使用，本公司並不負任何的擔保、保證或損害賠償責任。<br />七、退換貨須知</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	本網站提供所有消費者收受商品後七天猶豫期之權利，自您收訖商品後起算七天內，如您不願買受所收受之商品，請您退回商品並以書面或E-mail通知本網站，本網站將立即為您處理退貨事宜，並請您注意以下事項：</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	1.在您收到貨品後，請儘速確認您所訂購的商品，如有非人為因素所致之商品損毀、刮傷、或運輸過程造成包裝破損不完整的情形，請您儘速通知本公司客服人員，我們會進行商品瑕疵或損壞鑑定，並儘速為您更換新品。<br />2.如您需辦理退換貨時，請E-mail或來電至本公司，並提供以下資訊：</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(1)訂單號碼；<br />(2)姓名及聯絡電話，以及收件地址；<br />(3)退、換貨原因(非必填)</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	3.當您欲退貨時，依民法規定，您與本店鋪應互負交易解除回復原狀之責，因此：</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(1)請您維持商品之全新狀態，並請確保所有商品、使用手冊、註冊回函、周邊零件、發票、相關配件等均無任何遺漏，連同原來的包裝一併退回，以便本網站為您處裡退款事宜。</p><p class=\"MsoNormal\">	(2)若商品發生因不當使用、拆卸等人為因素所致之故障、損毀、磨損、擦傷、刮傷、髒汙、包裝破損不完整之情況，導致無法完整退回本網站時，本網站將向您酌收回復原狀之費用，或依商品之保存狀況按比例向您請求商品之價額。<br />(3)如需退換貨，請洽本公司客服專線：02-22687777；本網站「聯絡我們」留言。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	八、產品保固</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	1.本產品之保固期：</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(1)按保固書所載之保固到期日為保固期限。惟自您購買本產品之日起至保固到期日為止不滿一年，或保固書未記載保固到期日，保固期則以您購買本產品之日起算一年。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(2)若保固書未記載保固到期日或經塗改，或有前項不滿一年之情形，而您又無相關購買憑證證明購買日期，保固期則以本產品之進口日起算壹年。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(3)本公司係依您購買本產品之相關購買憑證（如：發票、收據）及產品保 固書認定保固期，請您務必妥善保存相關購買憑證，以確保您的權益。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(4)為確保您的權益，以利本公司提供完善之保固服務，請您在購買本產品 後30天內至本公司網站 <a href=\"https://dongcheng.tw/\">https://dongcheng.tw</a>&nbsp;完整填載您個人與產 品相關資料，完成上網登錄註冊。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	2.本產品保固範圍為保固期內，且正常使用本產品，如有下述情形者，則不在本產品保固服務範圍內：</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(1) 未遵循本產品之規格、使用手冊內容之方式操作或不當使用本產品者。&nbsp;</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(2) 自行拆裝、修理、添加附件、產品機身遭塗改、更新非本公司軟體、或修改、調整本產品之電路、機械結構者。&nbsp;</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(3) 自然耗損或屬消耗品之零件、附件及配件，例如：電池、充電器/座、相機帶、皮套、傳輸線及電源變壓器等。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	3.產品在保固期內，依使用手冊正常使用產品、新品不良，本公司提供免費維修服務，但如遇下列情況者，本公司得酌收材料與人工費用：</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(1)產品之保固資料不完整或不實者。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(2)遭遇不可抗拒之天災、地變與人禍而導致產品損壞者。&nbsp;</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(3)使用非本公司進口、製造或銷售之原廠耗材、配件或與型號不符之耗材 而導致產品損壞者。&nbsp;</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(4)非屬產品保固範圍內之調整、保養、維修，或非本產品本身問題而導致產品損壞者(本公司得酌收檢修工時之費用)。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	(5)因您未正常使用本產品而導致產品損壞者。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	4.於保固期內而屬產品保固範圍內之維修，若遇有零件停產之情事，本公司得以其他機種之相容性零件代之，或以同等級之機種提供換機服務。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	5.當產品需要送修、保養或調整時，請您與本公司客服聯絡(請勿自行拆卸任何零件以免造成其他損壞)，就近送至各授權維修廠商。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	6.若無產品保固書將會影響您應有之權益，請您妥善保存您的產品保固書(即使保固期已過)，以便享有更完整之售後服務。如有遺失．破損，恕不補發。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	九、到貨時間</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	訂購之商品將於訂單成立、付款完成後，2~5工作天內到貨;若未收到貨，請與本公司客服人員聯繫。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	十、活動優惠規則</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	1.本網站之各項活動優惠不得合併使用。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	2.除本公司免運活動外，任一折扣方式皆不得抵扣運費。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	十一、相關資訊</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	1.客服專線 TEL:（02）2268-7777。</p><p class=\"MsoNormal\">	2.銀行代號:808(玉山銀行)銀行帳號:1207940023880 戶名:東城數位科技有限公司<br />3.轉帳完成後，麻煩請至會員後台→會員專區填寫帳號後五碼及相關資訊或mail至 east.city9328002@gmail.com。</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	十二、購物條款&nbsp;</p><p class=\"MsoNormal\" style=\"margin-left:0cm;\">	1.本商城商品數量眾多，多少會產生缺貨或停售之情況！如遇商品缺貨或停售之情況，我們將會以電話或email與您聯繫，確認出貨事宜。</p>2.下標成功或匯款成功不代表實質交易完成。我們保有保留接受訂單與否的權利！&nbsp;<br />3.每台電腦因螢幕解析度不同，多少會有些許色差，物品皆以實品顏色為標準，若您是對顏色標準相當嚴格之買家，請不要下標購買。&nbsp;<br />4.下標前請慎選款式及顏色並仔細詳閱商品、價格、運費等相關事宜。<br />5.依動產交易法第三條之規定，買賣同意在商品未出貨前，貨品之所有權仍屬於賣方。<p>	<br /></p>', '<p>	<br /></p>', '<p>	<br /></p>', '<span>1.請注意！本商城商品數量眾多，多少會產生缺貨或停售之情況！</span><br /><span>2.下標成功或匯款成功不代表實質交易完成。我們保有保留接受訂單與否的權利！</span><br /><span>3.沒出貨前，您也有決定是否購買的權利。</span><br /><span>4.每台電腦因螢幕解析度不同，多少會有些許色差，物品皆以實品顏色為標準，若您是對顏色標準相當嚴格之買家，請不要下標購買。</span><br /><span>5.下標前請慎選款式及顏色並仔細詳閱商品、價格、運費等相關事宜。</span><br /><span>6.依動產交易法第三條之規定，買賣同意在商品未出貨前，貨品之所有權仍屬於賣方。</span><br /><span>7.網站內無標記之商品皆為藥品，為配合政府法令， 本網站不得提供中藥材或藥品線上銷售及詢價，且不得配合任何折扣優惠。</span>', '<span>1.客服專線 TEL: 02-7730-2973</span><br /><span>2.銀行代號:XXX (XX銀行) 銀行帳號:XXXXXXXXXX 戶名：傳訊光科技股份有限公司</span><br /><span>3.轉帳完成後，麻煩請將帳號後五碼於訂單查詢回報</span>', '<p>	非常歡迎您光臨<span>「傳訊光科技測試購物</span><span>網站</span><span>」</span>（以下簡稱本網站），為了讓您能夠安心的使用本網站的各項服務與資訊，特此向您說明本網站的隱私權保護政策，以保障您的權益，請您詳閱下列內容：</p><h3>	<a id=\"user-content-一隱私權保護政策的適用範圍-1\" class=\"anchor\" href=\"#一隱私權保護政策的適用範圍-1\"></a>一、隱私權保護政策的適用範圍</h3><p>	隱私權保護政策內容，包括本網站如何處理在您使用網站服務時收集到的個人識別資料。隱私權保護政策不適用於本網站以外的相關連結網站，也不適用於非本網站所委託或參與管理的人員。</p><h3>	<a id=\"user-content-二個人資料的蒐集處理及利用方式-1\" class=\"anchor\" href=\"#二個人資料的蒐集處理及利用方式-1\"></a>二、個人資料的蒐集、處理及利用方式</h3><ul>	<li>		當您造訪本網站或使用本網站所提供之功能服務時，我們將視該服務功能性質，請您提供必要的個人資料，並在該特定目的範圍內處理及利用您的個人資料；非經您書面同意，本網站不會將個人資料用於其他用途。	</li>	<li>		本網站在您使用服務信箱、問卷調查等互動性功能時，會保留您所提供的姓名、電子郵件地址、聯絡方式及使用時間等。	</li>	<li>		於一般瀏覽時，伺服器會自行記錄相關行徑，包括您使用連線設備的IP位址、使用時間、使用的瀏覽器、瀏覽及點選資料記錄等，做為我們增進網站服務的參考依據，此記錄為內部應用，決不對外公佈。	</li>	<li>		為提供精確的服務，我們會將收集的問卷調查內容進行統計與分析，分析結果之統計數據或說明文字呈現，除供內部研究外，我們會視需要公佈統計數據及說明文字，但不涉及特定個人之資料。	</li></ul><h3>	<a id=\"user-content-三資料之保護-1\" class=\"anchor\" href=\"#三資料之保護-1\"></a>三、資料之保護</h3><ul>	<li>		本網站主機均設有防火牆、防毒系統等相關的各項資訊安全設備及必要的安全防護措施，加以保護網站及您的個人資料採用嚴格的保護措施，只由經過授權的人員才能接觸您的個人資料，相關處理人員皆簽有保密合約，如有違反保密義務者，將會受到相關的法律處分。	</li>	<li>		如因業務需要有必要委託其他單位提供服務時，本網站亦會嚴格要求其遵守保密義務，並且採取必要檢查程序以確定其將確實遵守。	</li></ul><h3>	<a id=\"user-content-四網站對外的相關連結-1\" class=\"anchor\" href=\"#四網站對外的相關連結-1\"></a>四、網站對外的相關連結</h3><p>	本網站的網頁提供其他網站的網路連結，您也可經由本網站所提供的連結，點選進入其他網站。但該連結網站不適用本網站的隱私權保護政策，您必須參考該連結網站中的隱私權保護政策。</p><h3>	<a id=\"user-content-五與第三人共用個人資料之政策-1\" class=\"anchor\" href=\"#五與第三人共用個人資料之政策-1\"></a>五、與第三人共用個人資料之政策</h3><p>	本網站絕不會提供、交換、出租或出售任何您的個人資料給其他個人、團體、私人企業或公務機關，但有法律依據或合約義務者，不在此限。</p><p>	前項但書之情形包括不限於：</p><ul>	<li>		經由您書面同意。	</li>	<li>		法律明文規定。	</li>	<li>		為免除您生命、身體、自由或財產上之危險。	</li>	<li>		與公務機關或學術研究機構合作，基於公共利益為統計或學術研究而有必要，且資料經過提供者處理或蒐集者依其揭露方式無從識別特定之當事人。	</li>	<li>		當您在網站的行為，違反服務條款或可能損害或妨礙網站與其他使用者權益或導致任何人遭受損害時，經網站管理單位研析揭露您的個人資料是為了辨識、聯絡或採取法律行動所必要者。	</li>	<li>		有利於您的權益。	</li>	<li>		本網站委託廠商協助蒐集、處理或利用您的個人資料時，將對委外廠商或個人善盡監督管理之責。	</li></ul><h3>	<a id=\"user-content-六cookie之使用-1\" class=\"anchor\" href=\"#六cookie之使用-1\"></a>六、Cookie之使用</h3><p>	為了提供您最佳的服務，本網站會在您的電腦中放置並取用我們的Cookie，若您不願接受Cookie的寫入，您可在您使用的瀏覽器功能項中設定隱私權等級為高，即可拒絕Cookie的寫入，但可能會導致網站某些功能無法正常執行 。</p><h3>	<a id=\"user-content-七隱私權保護政策之修正-1\" class=\"anchor\" href=\"#七隱私權保護政策之修正-1\"></a>七、隱私權保護政策之修正</h3><p>	本網站隱私權保護政策將因應需求隨時進行修正，修正後的條款將刊登於網站上。</p>', '<p>	步驟1.【考場地區】選擇考場區域位置，切確考場位置會陸續公告<br />步驟2.【數量】選擇報考學生人數<br />步驟3.【加入購物車】可以繼續報名或購買其他，或【直接購物】直接下一步驟<br /><br />步驟4.【填寫考生資料】於購物車填寫考試人員資料，依報考學生人數依次填寫，填完1人按「確認」後再按一次填寫下一位，直到全部考生資料填寫完畢；若為購物就可不需填寫<br /><br />步驟5.【選擇付款方式】<br />步驟6.【配送方式】若單純只是報名，按「同會員資料」即可<br /><br />步驟7.【送出】完成報名<br /><br />※其他注意事項※<br />1.需登入會員才可訂購<br />2.送出訂單並完成匯款後,才視為完成訂單</p><p>	&nbsp; &nbsp;(須回報匯出帳號後5碼,以利核對)</p><p>	3.完成繳費後不得退費<br />4.准考證號碼與考試地點座位等資訊會於官網公佈，請密切注意</p><p style=\"margin-left:0in;text-align:left;\">	<br /></p>', '<p>	目前成績尚未公佈，請耐心等候</p>', '(一)作弊者一律取消比賽資格，並通知就讀學校。<br />(二)比賽當天恕不接受現場報名。<br /><span>(三)</span>不得攜帶字典、電子字典、手機、電子錶、字表(小抄)或相似功能的物品。<br />(四)如遇天災或不可抗力因素，經發布停止上班上課時，另訂比賽日期。<br /><p>	(五)現場學生若發生緊急事故致中斷比賽，該次成績不列入計算，並視為自動棄權不得重新比賽。</p><p>	<br /></p>※<span>為維護考生權益，</span>考場選擇於準考證編發後則不接受更改，如有不便請見諒<br />');

-- --------------------------------------------------------

--
-- 資料表結構 `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `contact_type` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `contact`
--

INSERT INTO `contact` (`id`, `contact_type`) VALUES
(1, '未收到貨,更改收貨資料,查詢出貨進度,商品換貨問題,商品缺件或不符,買家問題,取消訂單,行銷活動');

-- --------------------------------------------------------

--
-- 資料表結構 `contact_log`
--

CREATE TABLE `contact_log` (
  `id` int(11) NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `homephone` int(11) DEFAULT NULL,
  `email` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `order_number` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '訂單編號',
  `freeTime` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '皆可',
  `content` varchar(4096) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `remessage` text CHARACTER SET utf8 COLLATE utf8_bin,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `coupon`
--

CREATE TABLE `coupon` (
  `id` int(11) NOT NULL,
  `pic` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '',
  `title` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `number` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `limit_num` int(11) DEFAULT '5' COMMENT '領取上限',
  `type` tinyint(1) NOT NULL COMMENT '1 => 實體卷 0 => 虛擬卷',
  `ps` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '',
  `content` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '',
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `coupon_condition` int(11) NOT NULL,
  `transfer` tinyint(4) NOT NULL DEFAULT '0',
  `num` int(11) NOT NULL,
  `area` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `text1_online` tinyint(4) DEFAULT '0',
  `text1` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `text2_online` tinyint(4) DEFAULT '0',
  `text2` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `text3_online` tinyint(4) DEFAULT '0',
  `text3` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `text4_online` tinyint(4) DEFAULT '0',
  `text4` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `online` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `coupon_direct`
--

CREATE TABLE `coupon_direct` (
  `id` int(11) NOT NULL,
  `number` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `img` text COMMENT '圖片',
  `user_code` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `content` varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `online1` tinyint(4) DEFAULT NULL,
  `condition1` int(11) DEFAULT NULL,
  `discount1` float DEFAULT NULL,
  `online2` int(11) DEFAULT NULL,
  `condition2` int(11) DEFAULT NULL,
  `discount2` float DEFAULT NULL,
  `online3` int(11) DEFAULT NULL,
  `condition3` int(11) DEFAULT NULL,
  `discount3` float DEFAULT NULL,
  `online` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `coupon_direct_product`
--

CREATE TABLE `coupon_direct_product` (
  `coupon_prod_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `coupon_direct_record`
--

CREATE TABLE `coupon_direct_record` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '會員id',
  `coupon_id` int(11) NOT NULL COMMENT '使用優惠券id',
  `order_id` int(11) NOT NULL COMMENT '訂單id',
  `datetime` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '使用時間',
  `total` int(11) NOT NULL COMMENT '購物總金額',
  `discount` int(11) NOT NULL COMMENT '優惠金額'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `coupon_pool`
--

CREATE TABLE `coupon_pool` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `number` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `login_time` int(11) DEFAULT NULL,
  `owner` int(11) DEFAULT NULL,
  `use_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `default_content`
--

CREATE TABLE `default_content` (
  `id` int(11) NOT NULL,
  `text1` text CHARACTER SET utf8 COLLATE utf8_bin,
  `text2` text CHARACTER SET utf8 COLLATE utf8_bin,
  `text3` text CHARACTER SET utf8 COLLATE utf8_bin,
  `text4` text CHARACTER SET utf8 COLLATE utf8_bin,
  `note` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '說明'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `default_content`
--

INSERT INTO `default_content` (`id`, `text1`, `text2`, `text3`, `text4`, `note`) VALUES
(1, '<div style=\"text-align:center;\">\n	<p style=\"text-align:center;\">\n		12312321\n	</p>\n</div>', '21321323', '', '', '商品的預設內容'),
(2, '', '', '', NULL, '優惠券的預設內容');

-- --------------------------------------------------------

--
-- 資料表結構 `discount`
--

CREATE TABLE `discount` (
  `id` int(10) NOT NULL,
  `name` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `discount`
--

INSERT INTO `discount` (`id`, `name`, `number`) VALUES
(1, '1折', '0.1'),
(2, '2折', '0.2'),
(3, '3折', '0.3'),
(4, '5折', '0.5'),
(5, '7折', '0.7'),
(6, '9折', '0.9'),
(12, '1折:商品狀況良好', '0.1');

-- --------------------------------------------------------

--
-- 資料表結構 `examinee_info`
--

CREATE TABLE `examinee_info` (
  `id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parents_name` varchar(20) COLLATE utf8_bin NOT NULL,
  `parents_phone` varchar(12) COLLATE utf8_bin NOT NULL,
  `parents_mail` varchar(50) COLLATE utf8_bin NOT NULL,
  `parents_tel` varchar(15) COLLATE utf8_bin NOT NULL,
  `parents_add` varchar(50) COLLATE utf8_bin NOT NULL,
  `examinee_school` varchar(100) COLLATE utf8_bin NOT NULL,
  `examinee_class` varchar(20) COLLATE utf8_bin NOT NULL,
  `examinee_name` varchar(20) COLLATE utf8_bin NOT NULL,
  `examinee_birthday` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `examinee_id` varchar(10) COLLATE utf8_bin NOT NULL,
  `examinee_note` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `exam_school` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `examinee_room` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  `examinee_site` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  `examinee_ticket` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `grade` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  `grade_point` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `grade_note` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `grade_show` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `excel`
--

CREATE TABLE `excel` (
  `id` int(20) NOT NULL,
  `product_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_brand` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pic` text COLLATE utf8_unicode_ci,
  `status` int(1) DEFAULT '0',
  `account_number` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createtime` date NOT NULL,
  `regtime` date DEFAULT NULL,
  `tax_ID_number` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buytime` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `excel_reply`
--

CREATE TABLE `excel_reply` (
  `id` int(20) NOT NULL,
  `pro_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_brand` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pic` text COLLATE utf8_unicode_ci,
  `status` int(1) DEFAULT '0',
  `account_number` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createtime` date NOT NULL,
  `regtime` date DEFAULT NULL,
  `tax_ID_number` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buytime` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `experience`
--

CREATE TABLE `experience` (
  `id` int(11) NOT NULL,
  `pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `url` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `online` tinyint(1) NOT NULL DEFAULT '1',
  `orders` int(11) NOT NULL DEFAULT '0' COMMENT '排序'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `expiring_product`
--

CREATE TABLE `expiring_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `expiring_product`
--

INSERT INTO `expiring_product` (`id`, `product_id`) VALUES
(1, 0),
(2, 0),
(3, 0),
(4, 0),
(5, 0),
(6, 0),
(7, 0),
(8, 0),
(9, 0),
(10, 0);

-- --------------------------------------------------------

--
-- 資料表結構 `frontend_data_name`
--

CREATE TABLE `frontend_data_name` (
  `id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `url` text CHARACTER SET utf32 COLLATE utf32_unicode_ci,
  `show_type` text COMMENT '後台顯示分類'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `frontend_data_name`
--

INSERT INTO `frontend_data_name` (`id`, `name`, `url`, `show_type`) VALUES
(1, '人氣商品', NULL, 'tag'),
(2, '店長推薦', NULL, 'tag'),
(3, '即期良品', NULL, 'tag'),
(4, '特價商品', NULL, 'tag');

-- --------------------------------------------------------

--
-- 資料表結構 `frontend_menu_name`
--

CREATE TABLE `frontend_menu_name` (
  `id` int(11) NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '名稱',
  `en_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '英文名字',
  `controller` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '控制器名稱',
  `second_menu` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '子目錄'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `frontend_menu_name`
--

INSERT INTO `frontend_menu_name` (`id`, `name`, `en_name`, `controller`, `second_menu`) VALUES
(1, '關於我們', 'ABOUT US', 'about', '{\"about_story\":{\"name\":\"歷史沿革\"},\"about_map\":{\"name\":\"地圖資訊\"},\"about_contact\":{\"name\":\"聯絡我們\"}}'),
(2, '產品資訊', 'PRODUCT', 'product', '{\"activity\":{\"name\":\"優惠專區\", \"en_name\":\"OFFER\"}}'),
(3, '有感體驗', 'EXPERIENCE', 'activity', NULL),
(4, '活動專區', 'ACTIVITY', 'experience', NULL),
(5, '常見問題', 'Question', 'qa', NULL),
(6, '經銷據點', 'DISTRIBUTORS', 'distribution', NULL),
(7, '最新消息', 'NEWS', 'news', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `hot_product`
--

CREATE TABLE `hot_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `hot_product`
--

INSERT INTO `hot_product` (`id`, `product_id`) VALUES
(1, 0),
(2, 0),
(3, 0),
(4, 0),
(5, 0),
(6, 0),
(7, 0),
(8, 0),
(9, 0),
(10, 0);

-- --------------------------------------------------------

--
-- 資料表結構 `index_excel`
--

CREATE TABLE `index_excel` (
  `id` int(11) NOT NULL,
  `data1` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '一般存圖',
  `data2` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '一般URL',
  `data3` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '一般文字'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `index_excel`
--

INSERT INTO `index_excel` (`id`, `data1`, `data2`, `data3`) VALUES
(1, '/upload/46/d73ca09707c45c3117b48fbc55a0db.jpg', '/', NULL),
(2, '/upload/03/b70cbca094a4d955b5b801bb4aad54.png', 'javascript:closeBox()', NULL),
(28, NULL, '聯絡我們', '<p style=\"font-size:14px;vertical-align:baseline;color:font-family:微軟正黑體, Arial, Helvetica, sans-serif;\">	電話:XX-XXXX-XXXX</p><p style=\"font-size:14px;vertical-align:baseline;color:font-family:微軟正黑體, Arial, Helvetica, sans-serif;\">	fax:XX-XXXX-XXXX</p><p style=\"font-size:14px;vertical-align:baseline;color:font-family:微軟正黑體, Arial, Helvetica, sans-serif;\">	fax:<span style=\"font-size:14px;white-space:normal;\">XX-XXXX-XXXX</span></p><p style=\"font-size:14px;vertical-align:baseline;color:font-family:微軟正黑體, Arial, Helvetica, sans-serif;\">	地址：XXXXXXXXXXXXXXXXXXXXXXX</p><p style=\"font-size:14px;vertical-align:baseline;color:font-family:微軟正黑體, Arial, Helvetica, sans-serif;\">	客服信箱：XXXXXXX@XXXXX.XXX</p><p class=\"p1\" style=\"font-size:14px;vertical-align:baseline;color:font-family:微軟正黑體, Arial, Helvetica, sans-serif;\">	<span class=\"s1\" style=\"font-size:15px;vertical-align:baseline;\"></span><span style=\"font-size:14px;vertical-align:baseline;color:#808080;\"><a href=\"mailto:service360@drdc.com.tw\"></a></span> </p>'),
(3, NULL, '123', NULL),
(4, NULL, '', NULL),
(5, NULL, '', NULL),
(6, NULL, '', NULL),
(7, '/upload/1e/a7cb3c8468c27b77b5f85fb52fe36d.jpg', '/', NULL),
(8, '/upload/47/3f1c871d9b4bf49ad255d8ce424cdf.jpg', '', NULL),
(9, '/upload/e5/143b6161c50c27a9872b963f3c60e0.jpg', 'https://s.insta360.com/p/a4c366344b96cb7fabf1245c27d8ee94?e=true&locale=en-us', NULL),
(10, '/upload/e5/143b6161c50c27a9872b963f3c60e0.jpg', 'https://s.insta360.com/p/933102d790a76af21cf0e1872a195472?e=true&locale=en-us', NULL),
(11, '/upload/e5/143b6161c50c27a9872b963f3c60e0.jpg', 'https://s.insta360.com/p/ee4dece53bb6359696eee5a57741e995?e=true&locale=en-us', NULL),
(12, '/upload/6c/ce07ffb81b9a2e9ea86034067ace41.jpg', '', ''),
(13, '/upload/2a/d5d5b0afe186a87b2b675db4600b59.jpg', '/', NULL),
(14, NULL, NULL, '圖文廣告標題'),
(15, NULL, NULL, '圖文廣告說明圖文廣告說明圖文廣告說明圖文廣告說明圖文廣告說明圖文廣告說明圖文廣告說明圖文廣告說明圖文廣告說明'),
(16, NULL, NULL, '0'),
(17, NULL, NULL, '0'),
(18, NULL, NULL, '獨家留下可見光'),
(19, NULL, NULL, '一般布料，布料較厚較密者可阻擋陽光穿透，阻隔紫外線之同時也阻隔了有益的可見光；布料較薄較鬆者陽光可穿透，穿透了可見光之同時卻也穿透了有殺傷力的紫外線。然而，先進光學布，在完美抵擋紫外線之同時，有效率地留下了天然的有益可見光。'),
(20, '/upload/80/4899c3ccf41dc8fb73e773f661e580.jpg', 'http://hoii.de-ane.com/index/activity/activity_c.html?id=25', NULL),
(21, NULL, 'http://hoii.de-ane.com/index/activity/activity_c.html?id=25', NULL),
(22, '/upload/index_excel22.jpg?458032863', '#55566', NULL),
(23, '/upload/index_excel23.jpg?1404121665', 'http://hoii.de-ane.com/index/about/about_story.html', NULL),
(24, NULL, NULL, '新標題2455566'),
(25, NULL, NULL, '內容_2555566<br>內容_25內容_2555566<br>內容_25內容_25內容_2555566'),
(26, NULL, NULL, '德安藥行'),
(27, NULL, NULL, '※ 網路客服：週一至週六 09：30 ~ 21：00 / 週日公休<br>※ 門市地址：台北市中山區錦州街244、246號<br>   (捷運行天宮四號出口可見店面招牌)<br>※ 客服專線：(02)2503-3689'),
(29, NULL, '經銷據點', '<p>	標籤1<br /><span style=\"white-space:normal;\">標籤2<br /></span><span style=\"white-space:normal;\">標籤3<br /></span><span style=\"white-space:normal;\">標籤4<br /></span><span style=\"white-space:normal;\">標籤5</span></p>'),
(30, NULL, '粉絲專頁', '<p>	<span style=\"font-size:16px;\">關鍵字1<br /><span style=\"font-size:16px;white-space:normal;\">關鍵字2<br /></span><span style=\"font-size:16px;white-space:normal;\">關鍵字3<br /></span><span style=\"font-size:16px;white-space:normal;\">關鍵字4<br /></span><span style=\"font-size:16px;white-space:normal;\">關鍵字5</span><br /></span></p>'),
(31, NULL, '諮詢服務', '<p>	XXXXXXXXXXXXXXXXXXXXXXXX<br />XXXXXXXXXXXXXXXXXX<br /><span style=\"white-space:normal;\">XXXXXXXXXXXXXXXXXX<br /></span><span style=\"white-space:normal;\">XXXXXXXXXXXXXXXXXX</span><br /><span style=\"display:none;\" id=\"__kindeditor_bookmark_start_37__\"></span></p>'),
(32, NULL, '1549209600', '1612281600'),
(33, '/upload/61/1b5d9fb7af9a618893894212b7585d.jpg', '/', ''),
(34, '/upload/99/31f0a24767c130b5bf8a2f3ff00119.jpg', '', NULL),
(35, '/upload/99/31f0a24767c130b5bf8a2f3ff00119.jpg', '', NULL),
(36, '/upload/99/31f0a24767c130b5bf8a2f3ff00119.jpg', '', NULL),
(37, 'edm_id', '', '');

-- --------------------------------------------------------

--
-- 資料表結構 `index_online`
--

CREATE TABLE `index_online` (
  `id` tinyint(4) NOT NULL,
  `block1` tinyint(4) NOT NULL,
  `block2` tinyint(4) NOT NULL,
  `block3` tinyint(4) NOT NULL,
  `block4` tinyint(4) NOT NULL,
  `block5` tinyint(4) NOT NULL,
  `block6` tinyint(4) NOT NULL,
  `block7` tinyint(4) NOT NULL,
  `block8` tinyint(4) NOT NULL,
  `block9` tinyint(4) NOT NULL,
  `block10` tinyint(4) NOT NULL,
  `block11` tinyint(4) NOT NULL,
  `block12` tinyint(4) NOT NULL DEFAULT '1',
  `block13` int(11) NOT NULL,
  `block7_new` tinyint(4) NOT NULL,
  `block10_new` tinyint(4) NOT NULL,
  `block_spe_price` tinyint(4) NOT NULL,
  `block_edm` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'EDM'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `index_online`
--

INSERT INTO `index_online` (`id`, `block1`, `block2`, `block3`, `block4`, `block5`, `block6`, `block7`, `block8`, `block9`, `block10`, `block11`, `block12`, `block13`, `block7_new`, `block10_new`, `block_spe_price`, `block_edm`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `kol`
--

CREATE TABLE `kol` (
  `id` int(10) NOT NULL,
  `email` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '信箱',
  `password` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '密碼',
  `kol_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '網紅名',
  `real_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '真實姓名',
  `english_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '英文姓名',
  `category` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '分類',
  `phone` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '電話',
  `mobile` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '手機',
  `address` text CHARACTER SET utf32 COLLATE utf32_unicode_ci COMMENT '地址',
  `address_memo` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '地址備註',
  `bank_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '匯款銀行',
  `bank_account` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '匯款帳號',
  `id_no` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '身份証',
  `memo` text CHARACTER SET utf32 COLLATE utf32_unicode_ci COMMENT '備註',
  `start_date` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '起計日',
  `count_days` int(11) NOT NULL COMMENT '結算日周期',
  `creatdate` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '建立時間'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `kol_confirm_sale`
--

CREATE TABLE `kol_confirm_sale` (
  `id` int(10) NOT NULL,
  `kol_id` int(11) NOT NULL COMMENT '網紅id',
  `create_time` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '確認時間',
  `period` int(11) NOT NULL COMMENT '期數',
  `s_time` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '該期開始時間'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `kol_productinfo`
--

CREATE TABLE `kol_productinfo` (
  `id` int(10) NOT NULL,
  `kol_id` int(11) NOT NULL,
  `productinfo_id` int(11) NOT NULL,
  `time` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `is_using` tinyint(1) NOT NULL DEFAULT '1' COMMENT '使用中 0.過期 1.使用中'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `news`
--

CREATE TABLE `news` (
  `id` int(10) NOT NULL,
  `title` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `online` int(10) NOT NULL DEFAULT '1',
  `orders` int(11) NOT NULL DEFAULT '0' COMMENT '排序'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `points_setting`
--

CREATE TABLE `points_setting` (
  `id` int(11) NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '設定值',
  `note` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '說明'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `points_setting`
--

INSERT INTO `points_setting` (`id`, `value`, `note`) VALUES
(1, '', '點數適用分館'),
(2, '', '點數適用分類');

-- --------------------------------------------------------

--
-- 資料表結構 `position`
--

CREATE TABLE `position` (
  `id` int(10) NOT NULL,
  `name` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `max` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0.有限  1.無限'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `position`
--

INSERT INTO `position` (`id`, `name`, `number`, `max`) VALUES
(1, 'F', '0', 1),
(7, 'A', '10', 0),
(8, 'B', '10', 0),
(9, 'C', '10', 0),
(10, 'D', '5', 0),
(16, 'g', '10', 0),
(17, 'g', '5', 0),
(18, 'aaww', '122', 0);

-- --------------------------------------------------------

--
-- 資料表結構 `position_portion`
--

CREATE TABLE `position_portion` (
  `id` int(10) NOT NULL,
  `position_id` int(10) NOT NULL,
  `position_number` int(10) NOT NULL COMMENT '編碼號碼',
  `num` int(10) NOT NULL COMMENT '實際商品數量',
  `product_id` int(11) DEFAULT '0',
  `productinfo_type` int(11) DEFAULT '0',
  `radio` int(2) DEFAULT NULL COMMENT '編碼方式 0.不重複 1.重複',
  `datetime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `prodesc`
--

CREATE TABLE `prodesc` (
  `id` int(10) NOT NULL,
  `name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `number` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `prodesc`
--

INSERT INTO `prodesc` (`id`, `name`, `number`) VALUES
(30, '台灣人最愛的書', '0'),
(28, '新書', '0'),
(29, '==未輸入==', '0');

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `pic_icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `index_adv01_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv01_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv02_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv02_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv03_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv03_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv04_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv04_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv05_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv05_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv06_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv06_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv07_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv07_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `inner_adv01_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `inner_adv01_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `inner_adv02_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `inner_adv02_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `order_id` int(11) DEFAULT '999',
  `online` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0.隱藏 1.開啟 2.關閉',
  `webtype_keywords` text CHARACTER SET utf8 COLLATE utf8_bin,
  `webtype_description` text CHARACTER SET utf8 COLLATE utf8_bin
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`id`, `title`, `pic`, `pic_icon`, `index_adv01_pic`, `index_adv01_link`, `index_adv02_pic`, `index_adv02_link`, `index_adv03_pic`, `index_adv03_link`, `index_adv04_pic`, `index_adv04_link`, `index_adv05_pic`, `index_adv05_link`, `index_adv06_pic`, `index_adv06_link`, `index_adv07_pic`, `index_adv07_link`, `inner_adv01_pic`, `inner_adv01_link`, `inner_adv02_pic`, `inner_adv02_link`, `content`, `order_id`, `online`, `webtype_keywords`, `webtype_description`) VALUES
(1, '測試分館', '/upload/fb/6a42b3af03aaf7b378ac427d7c3711.jpg', '/upload/9c/421e36107ae48a4f0616ee0e6ae37f.jpg', '/upload/6a/175a6be514101f3f052a50d2412d38.jpg', '', '/upload/e2/4f6e6423dcb197c4b286345c7d3e7a.jpg', '', '/upload/e2/4f6e6423dcb197c4b286345c7d3e7a.jpg', '', '/upload/e2/4f6e6423dcb197c4b286345c7d3e7a.jpg', '', '/upload/e2/4f6e6423dcb197c4b286345c7d3e7a.jpg', '', '/upload/e2/4f6e6423dcb197c4b286345c7d3e7a.jpg', '', '/upload/e2/4f6e6423dcb197c4b286345c7d3e7a.jpg', '', '/upload/07/c11bdc495b5572e6d374f48a639e5d.jpg', '', '/upload/ba/5d4b4f528f66e4c6495a52b3ec656a.jpg', '', '<span style=\"white-space:normal;\">推薦分館廣告說明</span><span style=\"white-space:normal;\">推薦分館廣告說明</span><span style=\"white-space:normal;\">推薦分館廣告說明<br />\r\n<span style=\"white-space:normal;\">推薦分館廣告說明</span><span style=\"white-space:normal;\">推薦分館廣告說明<br />\r\n</span><span style=\"white-space:normal;\">推薦分館廣告說明</span><span style=\"white-space:normal;\">推薦分館廣告說明</span><br />\r\n</span>', 1, 1, '', ''),
(2, '測試分館2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `productinfo`
--

CREATE TABLE `productinfo` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `final_array` text COMMENT '顯示位置',
  `ISBN` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '條碼',
  `version` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `out_ID` int(5) DEFAULT NULL COMMENT '外串EDM',
  `product_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_bin,
  `r_repeat` int(1) DEFAULT '1' COMMENT '庫存編碼方式：0.不重複 1.重複',
  `Author` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '文字1',
  `house` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '文字2',
  `house_date` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '文字3',
  `pic` text CHARACTER SET utf8 COLLATE utf8_bin,
  `pic2` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `pic3` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `pic4` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `keywords` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `display` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `has_price` tinyint(4) NOT NULL DEFAULT '0',
  `text1` text CHARACTER SET utf8 COLLATE utf8_bin,
  `text1_online` tinyint(4) NOT NULL DEFAULT '1',
  `text2` text CHARACTER SET utf8 COLLATE utf8_bin,
  `text2_online` tinyint(4) NOT NULL DEFAULT '1',
  `text3` text CHARACTER SET utf8 COLLATE utf8_bin,
  `text3_online` tinyint(4) NOT NULL DEFAULT '1',
  `text4` text CHARACTER SET utf8 COLLATE utf8_bin,
  `text4_online` tinyint(4) NOT NULL DEFAULT '1',
  `online` tinyint(4) NOT NULL DEFAULT '1' COMMENT '狀態 0.隱藏 1.開啟 2.關閉',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `pushitem1` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pushitem2` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pushitem3` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pushitem4` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `prodesc` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '商品說明(下拉選控制)',
  `card_pay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否可刷卡',
  `is_registrable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否需填報名資料 0.否 1.是',
  `examinee_date` int(11) DEFAULT '-28800' COMMENT '考試/活動日期',
  `expire_date` int(11) DEFAULT '-28800' COMMENT '截止日期',
  `shipping_type` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '限用運法'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `productinfo_orders`
--

CREATE TABLE `productinfo_orders` (
  `id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL COMMENT '商品id',
  `prev_id` int(11) NOT NULL COMMENT '分館id',
  `branch_id` int(11) NOT NULL COMMENT '分類id',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '排序'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `productinfo_type`
--

CREATE TABLE `productinfo_type` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `num` int(11) NOT NULL COMMENT '線上可購買量',
  `limit_num` int(11) NOT NULL DEFAULT '10' COMMENT '庫存警示量',
  `price` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `position` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `discount_id` int(10) DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `online` tinyint(1) NOT NULL DEFAULT '1' COMMENT '狀態 1.啟用 0.假刪除'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `qa`
--

CREATE TABLE `qa` (
  `id` int(10) NOT NULL,
  `q` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `a` text CHARACTER SET utf8 COLLATE utf8_bin,
  `category` text COLLATE utf8_unicode_ci COMMENT '分類',
  `order_id` int(11) NOT NULL DEFAULT '999',
  `online` int(10) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `recommend_product`
--

CREATE TABLE `recommend_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `recommend_product`
--

INSERT INTO `recommend_product` (`id`, `product_id`) VALUES
(1, 0),
(2, 0),
(3, 0),
(4, 0),
(5, 0),
(6, 0),
(7, 0),
(8, 0),
(9, 0),
(10, 0);

-- --------------------------------------------------------

--
-- 資料表結構 `seo`
--

CREATE TABLE `seo` (
  `id` int(11) NOT NULL,
  `title` varchar(64) COLLATE utf8_bin NOT NULL,
  `seokey` varchar(256) COLLATE utf8_bin NOT NULL,
  `descr` text COLLATE utf8_bin NOT NULL,
  `fb_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `fb_title` varchar(64) COLLATE utf8_bin NOT NULL,
  `fb_descr` text COLLATE utf8_bin NOT NULL,
  `fb_img` varchar(128) COLLATE utf8_bin NOT NULL,
  `twitter_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `twitter_title` varchar(64) COLLATE utf8_bin NOT NULL,
  `twitter_descr` text COLLATE utf8_bin NOT NULL,
  `verification` varchar(128) COLLATE utf8_bin NOT NULL,
  `trackgoogle` text COLLATE utf8_bin NOT NULL,
  `marketgoogle` text COLLATE utf8_bin NOT NULL,
  `marketyahoo` text COLLATE utf8_bin NOT NULL,
  `display` text COLLATE utf8_bin NOT NULL,
  `map` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 傾印資料表的資料 `seo`
--

INSERT INTO `seo` (`id`, `title`, `seokey`, `descr`, `fb_name`, `fb_title`, `fb_descr`, `fb_img`, `twitter_name`, `twitter_title`, `twitter_descr`, `verification`, `trackgoogle`, `marketgoogle`, `marketyahoo`, `display`, `map`) VALUES
(1, '傳訊光測試商城shopfull001a', '傳訊光測試商城', '傳訊光測試商城', '傳訊光測試商城', '傳訊光測試商城', '傳訊光測試商城', '/upload/seo_fb_img.png?1619888314', '傳訊光測試商城', '傳訊光測試商城', '傳訊光測試商城', '', '', '', '', '', 'sitemap.jpg');

-- --------------------------------------------------------

--
-- 資料表結構 `shipping_fee`
--

CREATE TABLE `shipping_fee` (
  `id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '運費名稱',
  `price` int(11) NOT NULL COMMENT '運費金額',
  `free_rule` int(11) NOT NULL DEFAULT '999999999' COMMENT '滿額免運',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `online` tinyint(1) NOT NULL DEFAULT '1' COMMENT '狀態 0.停用 1.啟用'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `shipping_fee`
--

INSERT INTO `shipping_fee` (`id`, `name`, `price`, `free_rule`, `order_id`, `online`) VALUES
(1, '宅配', 100, 298, 4, 1),
(7, '萊爾富取貨', 90, 999999999, 3, 1),
(8, '7-11取貨', 60, 1000, 2, 1),
(9, '到店取貨', 0, 999999999, 1, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `slideshow`
--

CREATE TABLE `slideshow` (
  `id` int(10) NOT NULL,
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `slideshow`
--

INSERT INTO `slideshow` (`id`, `title`, `pic`, `link`) VALUES
(1, '輪播1', '/upload/slideshow1.jpg?1055136376', '/'),
(2, '輪播2', '/upload/slideshow2.jpg?1624424720', '/'),
(4, '輪播4', '/upload/slideshow4.jpg?1596847261', '/'),
(5, '輪播5', '/upload/slideshow5.jpg?1032674404', '/'),
(3, '輪播3', '/upload/slideshow3.jpg?1005488963', '/');

-- --------------------------------------------------------

--
-- 資料表結構 `spe_price_product`
--

CREATE TABLE `spe_price_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `orders` int(11) NOT NULL DEFAULT '0' COMMENT '排序'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `stronghold`
--

CREATE TABLE `stronghold` (
  `id` int(11) NOT NULL,
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `pic_icon` varchar(255) DEFAULT NULL,
  `pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv01_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv01_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv02_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv02_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv03_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv03_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv04_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv04_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv05_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv05_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv06_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv06_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv07_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `index_adv07_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `inner_adv01_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `inner_adv01_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `inner_adv02_pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `inner_adv02_link` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `order_id` int(11) DEFAULT '999',
  `online` tinyint(4) NOT NULL DEFAULT '0',
  `webtype_keywords` text CHARACTER SET utf8 COLLATE utf8_bin,
  `webtype_description` text CHARACTER SET utf8 COLLATE utf8_bin
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `system_email`
--

CREATE TABLE `system_email` (
  `id` int(11) NOT NULL,
  `signup_complete` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '註冊成功信',
  `contact_complete` text CHARACTER SET utf32 COLLATE utf32_unicode_ci COMMENT '回函成功信',
  `order_complete` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '訂購成功信',
  `forget_password` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '忘記密碼信',
  `order_cancel` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '取消訂單信'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `system_email`
--

INSERT INTO `system_email` (`id`, `signup_complete`, `contact_complete`, `order_complete`, `forget_password`, `order_cancel`) VALUES
(1, '<span>若有任何疑問，</span><br /><span>歡迎聯繫傳訊光測試商城shopfull001a客服：&nbsp;</span><a href=\"mailto:admin@sprlight.net\" target=\"_blank\">admin@sprlight.net</a><span>&nbsp;或&nbsp;</span><a href=\"tel:02-7730-2973\" target=\"_blank\">02-7730-2973</a><p>	<span style=\"color:#E53333;\">【營業時間】</span>週一至週五XX:XX-XX:XX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">【電話】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【傳真】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【地址】</span>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">≡ 此信件為系統自動發送，請勿直接回覆</span><span style=\"color:#E53333;\">&nbsp;≡</span></p>', '<span>若您在 1 ~ 2 個工作天後仍未收到我們的回覆，</span><br /><span></span><span>請您重新確認您的垃圾郵件匣及電子郵件相關設定，</span><br /><span></span><span>並再次利用回函表與我們聯絡。</span><br /><span></span><span>感謝您的來信！</span><br /><span></span><span>若有任何疑問，</span><br /><span>歡迎聯繫傳訊光測試商城</span><span>shopfull001a</span><span>客服：&nbsp;</span><span><a href=\"mailto:admin@sprlight.net\" target=\"_blank\">admin@sprlight.net</a>&nbsp;</span><span>或&nbsp;</span><span><a href=\"tel:02-7730-2973\" target=\"_blank\">02-7730-2973</a></span><span></span><p>	<span style=\"color:#E53333;\">【營業時間】</span>週一至週五XX:XX-XX:XX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">【電話】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【傳真】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【地址】</span>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">≡ 此信件為系統自動發送，請勿直接回覆</span><span style=\"color:#E53333;\">&nbsp;≡</span></p><p>	<br /></p>', '<p class=\"MsoNormal\">	1.客服專線 TEL: 02-7730-2973<br />2.銀行代號:XXX (XX銀行) 銀行帳號:XXXXXXXXXX 戶名：傳訊光科技股份有限公司<br />3.轉帳完成後，麻煩請將帳號後五碼於訂單查詢回報</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">【營業時間】</span>週一至週五XX:XX-XX:XX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">【電話】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【傳真】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【地址】</span>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">≡ 此信件為系統自動發送，請勿直接回覆</span><span style=\"color:#E53333;\">&nbsp;≡</span></p>', '<p class=\"MsoNormal\">	若無法操作，請來電通知客服人員<br />若有任何疑問，<br />歡迎聯繫傳訊光測試商城shopfull001a客服：&nbsp;<a href=\"mailto:admin@sprlight.net\" target=\"_blank\">admin@sprlight.net</a>&nbsp;或&nbsp;<a href=\"tel:02-7730-2973\" target=\"_blank\">02-7730-2973</a></p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">【營業時間】</span>週一至週五XX:XX-XX:XX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">【電話】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【傳真】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【地址】</span>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">≡ 此信件為系統自動發送，請勿直接回覆</span><span style=\"color:#E53333;\">&nbsp;≡</span></p>', '<p class=\"MsoNormal\">	若有任何疑問，<br />歡迎聯繫傳訊光測試商城shopfull001a客服：<span>XXXXXXXXXX&nbsp;</span>或 XXXXXXXXXX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">【營業時間】</span>週一至週五XX:XX-XX:XX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">【電話】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【傳真】</span>XXXXXXXXXX<br /><span style=\"color:#E53333;\">【地址】</span>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p><p class=\"MsoNormal\">	<span style=\"color:#E53333;\">≡ 此信件為系統自動發送，請勿直接回覆</span><span style=\"color:#E53333;\">&nbsp;≡</span></p>');

-- --------------------------------------------------------

--
-- 資料表結構 `system_intro`
--

CREATE TABLE `system_intro` (
  `system_id` int(10) UNSIGNED NOT NULL,
  `system_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '系統名稱',
  `system_version` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '系統版本',
  `system_note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '系統備註',
  `front_end` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT ' 前台開發程式',
  `back_end` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT ' 後台開發程式',
  `php_version` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'php版本',
  `sql_version` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'sql版本',
  `closing_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT ' 結案時間',
  `note` text COLLATE utf8_unicode_ci COMMENT '備註',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `system_intro`
--

INSERT INTO `system_intro` (`system_id`, `system_title`, `system_version`, `system_note`, `front_end`, `back_end`, `php_version`, `sql_version`, `closing_date`, `note`, `created_at`, `updated_at`) VALUES
(1, 'thinkphp', '5', '', 'bootstrap\r\nvue.js、angular.js', 'bootstrap\r\nvue.js、angular.js', '7.1', '5.6.43', '', '註冊商品\r\n商品無限階層\r\n綠界、金物流\r\n商品圖可傳影片、可限制數量\r\n庫存編號(一碼一物、一碼多物)、現場銷售功能\r\n優惠券可加入購物車\r\n\r\n後台帳號有權限管理(可調控使用那些功能)\r\n\r\n前台選單文字紀錄於資料庫\r\n共用變數紀錄於application/common.php\r\n\r\n2020-08-19:網紅追蹤銷售(一商品限一網紅)\r\n2020-09-04:會員/訂單列表可直接寄信、訂單加入賣家備註(消費者可查看)\r\n2020-09-15:加入報名功能\r\n2020-09-25:修改庫存編碼、商品銷售、訂單撿貨、多運費管理、運費關聯商品\r\n2020-09-26:可開關金流(不使用、綠界(一次付清、分期)、台新(限一次付清)擇一)、可開關物流(綠界或自行查詢店鋪)\r\n2020-11-24:點數結構調整\r\n2020-12-01:有感體驗模組化、驗證碼自動更新\r\n\r\n2020-12-14:商品品項可修改庫存位置(整批修改)\r\n2020-12-18:拔除number_id資料表(用於產生系統編碼的，ex:訂單流水號、商品品號...)、後台可新增會員\r\n2020-12-20:可單獨使用庫存管理(可扣除數量)\r\n2020-12-28:加價購設定\r\n\r\n2021-01-14:搜尋商品品項整合\r\n2021-01-15:訊息推播功能\r\n\r\n2021-01-29:分館分類無商品則自動隱藏、合併前台product,typeinfo頁面\r\n2021-02-01:前台可依標籤搜尋商品\r\n2021-02-01:套用前端驗證碼(maintpl, about_contact, signup, logincontroller, kolcontroller, aboutcontroller)\r\n2021-02-02:前台商品的優惠券、活動折扣、顯示價格功能統整\r\n\r\n2021-04-06:設定分館適用點數\r\n2021-04-21:消費者可自行取消訂單(未出貨前)、商品註冊Debug\r\n2021-04-23:直接輸入型優惠券\r\n2021-04-26:會員首購優惠、會員VIP優惠、 Qa文字分類\r\n2021-04-27:經銷據點多小圖、首頁大圖輪播等比縮放、選擇優惠多「領取優惠券」按鈕', '2020-04-07 08:11:38', '2020-04-07 08:11:38');

-- --------------------------------------------------------

--
-- 資料表結構 `temp_order_data`
--

CREATE TABLE `temp_order_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '會員id',
  `order_data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '訂單內容',
  `cart_data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '購物車資料',
  `randomkey` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '隨機碼',
  `time` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '時間',
  `over` tinyint(1) NOT NULL DEFAULT '0' COMMENT '狀態 0.未使用 1.已使用'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `time_product`
--

CREATE TABLE `time_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `time_product`
--

INSERT INTO `time_product` (`id`, `product_id`) VALUES
(1, 0),
(2, 0),
(3, 0),
(4, 0),
(5, 0),
(6, 0),
(7, 0),
(8, 0),
(9, 0),
(10, 0);

-- --------------------------------------------------------

--
-- 資料表結構 `town`
--

CREATE TABLE `town` (
  `CNo` bigint(20) NOT NULL,
  `Name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `Post` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `State` smallint(6) NOT NULL,
  `AutoNo` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `town`
--

INSERT INTO `town` (`CNo`, `Name`, `Post`, `State`, `AutoNo`) VALUES
(1, '中正區', '100', 0, 1),
(1, '大同區', '103', 0, 2),
(1, '中山區', '104', 0, 3),
(1, '松山區', '105', 0, 4),
(1, '大安區', '106', 0, 5),
(1, '萬華區', '108', 0, 6),
(1, '信義區', '110', 0, 7),
(1, '士林區', '111', 0, 8),
(1, '北投區', '112', 0, 9),
(1, '內湖區', '114', 0, 10),
(1, '南港區', '115', 0, 11),
(1, '文山區', '116', 0, 12),
(2, '仁愛區', '200', 0, 13),
(2, '信義區', '201', 0, 14),
(2, '中正區', '202', 0, 15),
(2, '中山區', '203', 0, 16),
(2, '安樂區', '204', 0, 17),
(2, '暖暖區', '205', 0, 18),
(2, '七堵區', '206', 0, 19),
(3, '萬里區', '207', 0, 20),
(3, '金山區', '208', 0, 21),
(3, '板橋區', '220', 0, 22),
(3, '汐止區', '221', 0, 23),
(3, '深坑區', '222', 0, 24),
(3, '石碇區', '223', 0, 25),
(3, '瑞芳區', '224', 0, 26),
(3, '平溪區', '226', 0, 27),
(3, '雙溪區', '227', 0, 28),
(3, '貢寮區', '228', 0, 29),
(3, '新店區', '231', 0, 30),
(3, '坪林區', '232', 0, 31),
(3, '烏來區', '233', 0, 32),
(3, '永和區', '234', 0, 33),
(3, '中和區', '235', 0, 34),
(3, '土城區', '236', 0, 35),
(3, '三峽區', '237', 0, 36),
(3, '樹林區', '238', 0, 37),
(3, '鶯歌區', '239', 0, 38),
(3, '三重區', '241', 0, 39),
(3, '新莊區', '242', 0, 40),
(3, '泰山區', '243', 0, 41),
(3, '林口區', '244', 0, 42),
(3, '蘆洲區', '247', 0, 43),
(3, '五股區', '248', 0, 44),
(3, '八里區', '249', 0, 45),
(3, '淡水區', '251', 0, 46),
(3, '三芝區', '252', 0, 47),
(3, '石門區', '253', 0, 48),
(4, '宜蘭市', '260', 0, 49),
(4, '頭城鎮', '261', 0, 50),
(4, '礁溪鄉', '262', 0, 51),
(4, '壯圍鄉', '263', 0, 52),
(4, '員山鄉', '264', 0, 53),
(4, '羅東鎮', '265', 0, 54),
(4, '三星鄉', '266', 0, 55),
(4, '大同鄉', '267', 0, 56),
(4, '五結鄉', '268', 0, 57),
(4, '冬山鄉', '269', 0, 58),
(4, '蘇澳鎮', '270', 0, 59),
(4, '南澳鄉', '272', 0, 60),
(4, '釣魚台列嶼', '290', 0, 61),
(5, '新竹市', '300', 0, 62),
(6, '竹北市', '302', 0, 63),
(6, '湖口鄉', '303', 0, 64),
(6, '新豐鄉', '304', 0, 65),
(6, '新埔鎮', '305', 0, 66),
(6, '關西鎮', '306', 0, 67),
(6, '芎林鄉', '307', 0, 68),
(6, '寶山鄉', '308', 0, 69),
(6, '竹東鎮', '310', 0, 70),
(6, '五峰鄉', '311', 0, 71),
(6, '橫山鄉', '312', 0, 72),
(6, '尖石鄉', '313', 0, 73),
(6, '北埔鄉', '314', 0, 74),
(6, '峨眉鄉', '315', 0, 75),
(7, '中壢市', '320', 0, 76),
(7, '平鎮市', '324', 0, 77),
(7, '龍潭鄉', '325', 0, 78),
(7, '楊梅市', '326', 0, 79),
(7, '新屋鄉', '327', 0, 80),
(7, '觀音鄉', '328', 0, 81),
(7, '桃園市', '330', 0, 82),
(7, '龜山鄉', '333', 0, 83),
(7, '八德市', '334', 0, 84),
(7, '大溪鎮', '335', 0, 85),
(7, '復興鄉', '336', 0, 86),
(7, '大園鄉', '337', 0, 87),
(7, '蘆竹鄉', '338', 0, 88),
(8, '竹南鎮', '350', 0, 89),
(8, '頭份鎮', '351', 0, 90),
(8, '三灣鄉', '352', 0, 91),
(8, '南庄鄉', '353', 0, 92),
(8, '獅潭鄉', '354', 0, 93),
(8, '後龍鎮', '356', 0, 94),
(8, '通霄鎮', '357', 0, 95),
(8, '苑裡鎮', '358', 0, 96),
(8, '苗栗市', '360', 0, 97),
(8, '造橋鄉', '361', 0, 98),
(8, '頭屋鄉', '362', 0, 99),
(8, '公館鄉', '363', 0, 100),
(8, '大湖鄉', '364', 0, 101),
(8, '泰安鄉', '365', 0, 102),
(8, '銅鑼鄉', '366', 0, 103),
(8, '三義鄉', '367', 0, 104),
(8, '西湖鄉', '368', 0, 105),
(8, '卓蘭鎮', '369', 0, 106),
(9, '中區', '400', 0, 107),
(9, '東區', '401', 0, 108),
(9, '南區', '402', 0, 109),
(9, '西區', '403', 0, 110),
(9, '北區', '404', 0, 111),
(9, '北屯區', '406', 0, 112),
(9, '西屯區', '407', 0, 113),
(9, '南屯區', '408', 0, 114),
(9, '太平區', '411', 0, 115),
(9, '大里區', '412', 0, 116),
(9, '霧峰區', '413', 0, 117),
(9, '烏日區', '414', 0, 118),
(9, '豐原區', '420', 0, 119),
(9, '后里區', '421', 0, 120),
(9, '石岡區', '422', 0, 121),
(9, '東勢區', '423', 0, 122),
(9, '和平區', '424', 0, 123),
(9, '新社區', '426', 0, 124),
(9, '潭子區', '427', 0, 125),
(9, '大雅區', '428', 0, 126),
(9, '神岡區', '429', 0, 127),
(9, '大肚區', '432', 0, 128),
(9, '沙鹿區', '433', 0, 129),
(9, '龍井區', '434', 0, 130),
(9, '梧棲區', '435', 0, 131),
(9, '清水區', '436', 0, 132),
(9, '大甲區', '437', 0, 133),
(9, '外埔區', '438', 0, 134),
(9, '大安區', '439', 0, 135),
(10, '彰化市', '500', 0, 136),
(10, '芬園鄉', '502', 0, 137),
(10, '花壇鄉', '503', 0, 138),
(10, '秀水鄉', '504', 0, 139),
(10, '鹿港鎮', '505', 0, 140),
(10, '福興鄉', '506', 0, 141),
(10, '線西鄉', '507', 0, 142),
(10, '和美鎮', '508', 0, 143),
(10, '伸港鄉', '509', 0, 144),
(10, '員林鎮', '510', 0, 145),
(10, '社頭鄉', '511', 0, 146),
(10, '永靖鄉', '512', 0, 147),
(10, '埔心鄉', '513', 0, 148),
(10, '溪湖鎮', '514', 0, 149),
(10, '大村鄉', '515', 0, 150),
(10, '埔鹽鄉', '516', 0, 151),
(10, '田中鎮', '520', 0, 152),
(10, '北斗鎮', '521', 0, 153),
(10, '田尾鄉', '522', 0, 154),
(10, '埤頭鄉', '523', 0, 155),
(10, '溪州鄉', '524', 0, 156),
(10, '竹塘鄉', '525', 0, 157),
(10, '二林鎮', '526', 0, 158),
(10, '大城鄉', '527', 0, 159),
(10, '芳苑鄉', '528', 0, 160),
(10, '二水鄉', '530', 0, 161),
(11, '南投市', '540', 0, 162),
(11, '中寮鄉', '541', 0, 163),
(11, '草屯鎮', '542', 0, 164),
(11, '國姓鄉', '544', 0, 165),
(11, '埔里鎮', '545', 0, 166),
(11, '仁愛鄉', '546', 0, 167),
(11, '名間鄉', '551', 0, 168),
(11, '集集鎮', '552', 0, 169),
(11, '水里鄉', '553', 0, 170),
(11, '魚池鄉', '555', 0, 171),
(11, '信義鄉', '556', 0, 172),
(11, '竹山鎮', '557', 0, 173),
(11, '鹿谷鄉', '558', 0, 174),
(12, '斗南鎮', '630', 0, 175),
(12, '大埤鄉', '631', 0, 176),
(12, '虎尾鎮', '632', 0, 177),
(12, '土庫鎮', '633', 0, 178),
(12, '褒忠鄉', '634', 0, 179),
(12, '東勢鄉', '635', 0, 180),
(12, '臺西鄉', '636', 0, 181),
(12, '崙背鄉', '637', 0, 182),
(12, '麥寮鄉', '638', 0, 183),
(12, '斗六市', '640', 0, 184),
(12, '林內鄉', '643', 0, 185),
(12, '古坑鄉', '646', 0, 186),
(12, '莿桐鄉', '647', 0, 187),
(12, '西螺鎮', '648', 0, 188),
(12, '二崙鄉', '649', 0, 189),
(12, '北港鎮', '651', 0, 190),
(12, '水林鄉', '652', 0, 191),
(12, '口湖鄉', '653', 0, 192),
(12, '四湖鄉', '654', 0, 193),
(12, '元長鄉', '655', 0, 194),
(13, '嘉義市', '600', 0, 195),
(14, '番路鄉', '602', 0, 196),
(14, '梅山鄉', '603', 0, 197),
(14, '竹崎鄉', '604', 0, 198),
(14, '阿里山鄉', '605', 0, 199),
(14, '中埔鄉', '606', 0, 200),
(14, '大埔鄉', '607', 0, 201),
(14, '水上鄉', '608', 0, 202),
(14, '鹿草鄉', '611', 0, 203),
(14, '太保市', '612', 0, 204),
(14, '朴子市', '613', 0, 205),
(14, '東石鄉', '614', 0, 206),
(14, '六腳鄉', '615', 0, 207),
(14, '新港鄉', '616', 0, 208),
(14, '民雄鄉', '621', 0, 209),
(14, '大林鎮', '622', 0, 210),
(14, '溪口鄉', '623', 0, 211),
(14, '義竹鄉', '624', 0, 212),
(14, '布袋鎮', '625', 0, 213),
(15, '中西區', '700', 0, 214),
(15, '東區', '701', 0, 215),
(15, '南區', '702', 0, 216),
(15, '北區', '704', 0, 217),
(15, '安平區', '708', 0, 218),
(15, '安南區', '709', 0, 219),
(15, '永康區', '710', 0, 220),
(15, '歸仁區', '711', 0, 221),
(15, '新化區', '712', 0, 222),
(15, '左鎮區', '713', 0, 223),
(15, '玉井區', '714', 0, 224),
(15, '楠西區', '715', 0, 225),
(15, '南化區', '716', 0, 226),
(15, '仁德區', '717', 0, 227),
(15, '關廟區', '718', 0, 228),
(15, '龍崎區', '719', 0, 229),
(15, '官田區', '720', 0, 230),
(15, '麻豆區', '721', 0, 231),
(15, '佳里區', '722', 0, 232),
(15, '西港區', '723', 0, 233),
(15, '七股區', '724', 0, 234),
(15, '將軍區', '725', 0, 235),
(15, '學甲區', '726', 0, 236),
(15, '北門區', '727', 0, 237),
(15, '新營區', '730', 0, 238),
(15, '後壁區', '731', 0, 239),
(15, '白河區', '732', 0, 240),
(15, '東山區', '733', 0, 241),
(15, '六甲區', '734', 0, 242),
(15, '下營區', '735', 0, 243),
(15, '柳營區', '736', 0, 244),
(15, '鹽水區', '737', 0, 245),
(15, '善化區', '741', 0, 246),
(15, '大內區', '742', 0, 247),
(15, '山上區', '743', 0, 248),
(15, '新市區', '744', 0, 249),
(15, '安定區', '745', 0, 250),
(16, '新興區', '800', 0, 251),
(16, '前金區', '801', 0, 252),
(16, '苓雅區', '802', 0, 253),
(16, '鹽埕區', '803', 0, 254),
(16, '鼓山區', '804', 0, 255),
(16, '旗津區', '805', 0, 256),
(16, '前鎮區', '806', 0, 257),
(16, '三民區', '807', 0, 258),
(16, '楠梓區', '811', 0, 259),
(16, '小港區', '812', 0, 260),
(16, '左營區', '813', 0, 261),
(16, '仁武區', '814', 0, 262),
(16, '大社區', '815', 0, 263),
(16, '岡山區', '820', 0, 264),
(16, '路竹區', '821', 0, 265),
(16, '阿蓮區', '822', 0, 266),
(16, '田寮區', '823', 0, 267),
(16, '燕巢區', '824', 0, 268),
(16, '橋頭區', '825', 0, 269),
(16, '梓官區', '826', 0, 270),
(16, '彌陀區', '827', 0, 271),
(16, '永安區', '828', 0, 272),
(16, '湖內區', '829', 0, 273),
(16, '鳳山區', '830', 0, 274),
(16, '大寮區', '831', 0, 275),
(16, '林園區', '832', 0, 276),
(16, '鳥松區', '833', 0, 277),
(16, '大樹區', '840', 0, 278),
(16, '旗山區', '842', 0, 279),
(16, '美濃區', '843', 0, 280),
(16, '六龜區', '844', 0, 281),
(16, '內門區', '845', 0, 282),
(16, '杉林區', '846', 0, 283),
(16, '甲仙區', '847', 0, 284),
(16, '桃源區', '848', 0, 285),
(16, '那瑪夏區', '849', 0, 286),
(16, '茂林區', '851', 0, 287),
(16, '茄萣區', '852', 0, 288),
(17, '東沙', '817', 0, 289),
(17, '南沙', '819', 0, 290),
(18, '馬公市', '880', 0, 291),
(18, '西嶼鄉', '881', 0, 292),
(18, '望安鄉', '882', 0, 293),
(18, '七美鄉', '883', 0, 294),
(18, '白沙鄉', '884', 0, 295),
(18, '湖西鄉', '885', 0, 296),
(19, '屏東市', '900', 0, 297),
(19, '三地門鄉', '901', 0, 298),
(19, '霧臺鄉', '902', 0, 299),
(19, '瑪家鄉', '903', 0, 300),
(19, '九如鄉', '904', 0, 301),
(19, '里港鄉', '905', 0, 302),
(19, '高樹鄉', '906', 0, 303),
(19, '鹽埔鄉', '907', 0, 304),
(19, '長治鄉', '908', 0, 305),
(19, '麟洛鄉', '909', 0, 306),
(19, '竹田鄉', '911', 0, 307),
(19, '內埔鄉', '912', 0, 308),
(19, '萬丹鄉', '913', 0, 309),
(19, '潮州鎮', '920', 0, 310),
(19, '泰武鄉', '921', 0, 311),
(19, '來義鄉', '922', 0, 312),
(19, '萬巒鄉', '923', 0, 313),
(19, '崁頂鄉', '924', 0, 314),
(19, '新埤鄉', '925', 0, 315),
(19, '南州鄉', '926', 0, 316),
(19, '林邊鄉', '927', 0, 317),
(19, '東港鄉', '928', 0, 318),
(19, '琉球鄉', '929', 0, 319),
(19, '佳冬鄉', '931', 0, 320),
(19, '新園鄉', '932', 0, 321),
(19, '枋寮鄉', '940', 0, 322),
(19, '枋山鄉', '941', 0, 323),
(19, '春日鄉', '942', 0, 324),
(19, '獅子鄉', '943', 0, 325),
(19, '車城鄉', '944', 0, 326),
(19, '牡丹鄉', '945', 0, 327),
(19, '恆春鄉', '946', 0, 328),
(19, '滿州鄉', '947', 0, 329),
(20, '臺東市', '950', 0, 330),
(20, '綠島鄉', '951', 0, 331),
(20, '蘭嶼鄉', '952', 0, 332),
(20, '延平鄉', '953', 0, 333),
(20, '卑南鄉', '954', 0, 334),
(20, '鹿野鄉', '955', 0, 335),
(20, '關山鎮', '956', 0, 336),
(20, '海端鄉', '957', 0, 337),
(20, '池上鄉', '958', 0, 338),
(20, '東河鄉', '959', 0, 339),
(20, '成功鎮', '961', 0, 340),
(20, '長濱鄉', '962', 0, 341),
(20, '太麻里鄉', '963', 0, 342),
(20, '金峰鄉', '964', 0, 343),
(20, '大武鄉', '965', 0, 344),
(20, '達仁鄉', '966', 0, 345),
(21, '花蓮市', '970', 0, 346),
(21, '新城鄉', '971', 0, 347),
(21, '秀林鄉', '972', 0, 348),
(21, '吉安鄉', '973', 0, 349),
(21, '壽豐鄉', '974', 0, 350),
(21, '鳳林鎮', '975', 0, 351),
(21, '光復鄉', '976', 0, 352),
(21, '豐濱鄉', '977', 0, 353),
(21, '瑞穗鄉', '978', 0, 354),
(21, '萬榮鄉', '979', 0, 355),
(21, '玉里鎮', '981', 0, 356),
(21, '卓溪鄉', '982', 0, 357),
(21, '富里鄉', '983', 0, 358),
(22, '金沙鎮', '890', 0, 359),
(22, '金湖鎮', '891', 0, 360),
(22, '金寧鄉', '892', 0, 361),
(22, '金城鎮', '893', 0, 362),
(22, '烈嶼鄉', '894', 0, 363),
(22, '烏坵鄉', '896', 0, 364),
(23, '南竿鄉', '209', 0, 365),
(23, '北竿鄉', '210', 0, 366),
(23, '莒光鄉', '211', 0, 367),
(23, '東引鄉', '212', 0, 368);

-- --------------------------------------------------------

--
-- 資料表結構 `typeinfo`
--

CREATE TABLE `typeinfo` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL COMMENT '下掛分館id',
  `branch_id` int(11) DEFAULT '0' COMMENT '下掛分類id,0則代表下掛於分館',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `start` int(11) DEFAULT '0',
  `end` int(11) DEFAULT '0',
  `limit_num` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL,
  `webtype_keywords` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `webtype_description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `online` tinyint(4) NOT NULL DEFAULT '1' COMMENT '狀態 0.隱藏 1.開啟 2.關閉'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `typeinfo_str`
--

CREATE TABLE `typeinfo_str` (
  `id` int(11) NOT NULL,
  `pic` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `sub_pics` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '附圖',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `online` tinyint(1) NOT NULL DEFAULT '1',
  `parent_id` varchar(10) DEFAULT NULL,
  `orders` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `about_story`
--
ALTER TABLE `about_story`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `act`
--
ALTER TABLE `act`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `act_product`
--
ALTER TABLE `act_product`
  ADD PRIMARY KEY (`act_prod_id`);

--
-- 資料表索引 `addprice`
--
ALTER TABLE `addprice`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `addprice_product`
--
ALTER TABLE `addprice_product`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `addprice_rule`
--
ALTER TABLE `addprice_rule`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `admin_info`
--
ALTER TABLE `admin_info`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `backstage_menu`
--
ALTER TABLE `backstage_menu`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `backstage_menu_second`
--
ALTER TABLE `backstage_menu_second`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`AutoNo`);

--
-- 資料表索引 `consent`
--
ALTER TABLE `consent`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `contact_log`
--
ALTER TABLE `contact_log`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `coupon`
--
ALTER TABLE `coupon`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `coupon_direct`
--
ALTER TABLE `coupon_direct`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `coupon_direct_product`
--
ALTER TABLE `coupon_direct_product`
  ADD PRIMARY KEY (`coupon_prod_id`);

--
-- 資料表索引 `coupon_direct_record`
--
ALTER TABLE `coupon_direct_record`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `coupon_pool`
--
ALTER TABLE `coupon_pool`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `default_content`
--
ALTER TABLE `default_content`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `discount`
--
ALTER TABLE `discount`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `examinee_info`
--
ALTER TABLE `examinee_info`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `excel`
--
ALTER TABLE `excel`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `excel_reply`
--
ALTER TABLE `excel_reply`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `experience`
--
ALTER TABLE `experience`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `expiring_product`
--
ALTER TABLE `expiring_product`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `frontend_data_name`
--
ALTER TABLE `frontend_data_name`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `frontend_menu_name`
--
ALTER TABLE `frontend_menu_name`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `hot_product`
--
ALTER TABLE `hot_product`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `index_excel`
--
ALTER TABLE `index_excel`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `index_online`
--
ALTER TABLE `index_online`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `kol`
--
ALTER TABLE `kol`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `kol_confirm_sale`
--
ALTER TABLE `kol_confirm_sale`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `kol_productinfo`
--
ALTER TABLE `kol_productinfo`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `points_setting`
--
ALTER TABLE `points_setting`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `position_portion`
--
ALTER TABLE `position_portion`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `prodesc`
--
ALTER TABLE `prodesc`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `productinfo`
--
ALTER TABLE `productinfo`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `productinfo_orders`
--
ALTER TABLE `productinfo_orders`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `productinfo_type`
--
ALTER TABLE `productinfo_type`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `qa`
--
ALTER TABLE `qa`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `recommend_product`
--
ALTER TABLE `recommend_product`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `seo`
--
ALTER TABLE `seo`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `shipping_fee`
--
ALTER TABLE `shipping_fee`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `slideshow`
--
ALTER TABLE `slideshow`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `spe_price_product`
--
ALTER TABLE `spe_price_product`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `stronghold`
--
ALTER TABLE `stronghold`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `system_email`
--
ALTER TABLE `system_email`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `system_intro`
--
ALTER TABLE `system_intro`
  ADD PRIMARY KEY (`system_id`);

--
-- 資料表索引 `temp_order_data`
--
ALTER TABLE `temp_order_data`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `time_product`
--
ALTER TABLE `time_product`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `town`
--
ALTER TABLE `town`
  ADD PRIMARY KEY (`AutoNo`);

--
-- 資料表索引 `typeinfo`
--
ALTER TABLE `typeinfo`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `typeinfo_str`
--
ALTER TABLE `typeinfo_str`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動增長(AUTO_INCREMENT)
--

--
-- 使用資料表自動增長(AUTO_INCREMENT) `about_story`
--
ALTER TABLE `about_story`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `act`
--
ALTER TABLE `act`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `activity`
--
ALTER TABLE `activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `act_product`
--
ALTER TABLE `act_product`
  MODIFY `act_prod_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `addprice`
--
ALTER TABLE `addprice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `addprice_product`
--
ALTER TABLE `addprice_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `addprice_rule`
--
ALTER TABLE `addprice_rule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `backstage_menu`
--
ALTER TABLE `backstage_menu`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `backstage_menu_second`
--
ALTER TABLE `backstage_menu_second`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `city`
--
ALTER TABLE `city`
  MODIFY `AutoNo` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `consent`
--
ALTER TABLE `consent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `contact_log`
--
ALTER TABLE `contact_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `coupon`
--
ALTER TABLE `coupon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `coupon_direct`
--
ALTER TABLE `coupon_direct`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `coupon_direct_product`
--
ALTER TABLE `coupon_direct_product`
  MODIFY `coupon_prod_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `coupon_direct_record`
--
ALTER TABLE `coupon_direct_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `coupon_pool`
--
ALTER TABLE `coupon_pool`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `default_content`
--
ALTER TABLE `default_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `discount`
--
ALTER TABLE `discount`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `examinee_info`
--
ALTER TABLE `examinee_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `excel`
--
ALTER TABLE `excel`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `excel_reply`
--
ALTER TABLE `excel_reply`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `experience`
--
ALTER TABLE `experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `expiring_product`
--
ALTER TABLE `expiring_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `frontend_data_name`
--
ALTER TABLE `frontend_data_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `hot_product`
--
ALTER TABLE `hot_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `index_excel`
--
ALTER TABLE `index_excel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `index_online`
--
ALTER TABLE `index_online`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `kol`
--
ALTER TABLE `kol`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `kol_confirm_sale`
--
ALTER TABLE `kol_confirm_sale`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `kol_productinfo`
--
ALTER TABLE `kol_productinfo`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `news`
--
ALTER TABLE `news`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `points_setting`
--
ALTER TABLE `points_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `position`
--
ALTER TABLE `position`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `position_portion`
--
ALTER TABLE `position_portion`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `prodesc`
--
ALTER TABLE `prodesc`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `productinfo`
--
ALTER TABLE `productinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `productinfo_orders`
--
ALTER TABLE `productinfo_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `productinfo_type`
--
ALTER TABLE `productinfo_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `qa`
--
ALTER TABLE `qa`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `recommend_product`
--
ALTER TABLE `recommend_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `seo`
--
ALTER TABLE `seo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `shipping_fee`
--
ALTER TABLE `shipping_fee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `slideshow`
--
ALTER TABLE `slideshow`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `spe_price_product`
--
ALTER TABLE `spe_price_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `stronghold`
--
ALTER TABLE `stronghold`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `system_intro`
--
ALTER TABLE `system_intro`
  MODIFY `system_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `temp_order_data`
--
ALTER TABLE `temp_order_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `time_product`
--
ALTER TABLE `time_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `town`
--
ALTER TABLE `town`
  MODIFY `AutoNo` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=371;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `typeinfo`
--
ALTER TABLE `typeinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動增長(AUTO_INCREMENT) `typeinfo_str`
--
ALTER TABLE `typeinfo_str`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

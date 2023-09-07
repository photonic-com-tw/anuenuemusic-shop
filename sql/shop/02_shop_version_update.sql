-- 2021-06-16
UPDATE `system_intro` set note = CONCAT(note, '\n\n2021-06-16 合併訂單、商品後台');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-06-16 修正資料庫大小寫問題');


-- 2021-07-06 新增商品品項對應商品圖片
UPDATE `system_intro` set note = CONCAT(note, '\n\n2021-07-06 新增商品品項對應商品圖片');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-07-06 商品新增第五塊說明');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-07-06 推薦商品修正改為無限量');
-- 2021-07-07 品項可拆分多個、且點擊會更換圖片前台串接
UPDATE `system_intro` set note = CONCAT(note, '\n2021-07-07 品項可拆分多個、且點擊會更換圖片前台串接');
-- 2021-07-08 商品加入我的人氣功能
UPDATE `system_intro` set note = CONCAT(note, '\n2021-07-08 商品加入我的人氣功能');
-- 2021-07-09 商品加入我的收藏功能
UPDATE `system_intro` set note = CONCAT(note, '\n2021-07-08 商品加入我的收藏功能');

-- 2021-07-26 商品加入我的收藏功能
UPDATE `system_intro` set note = CONCAT(note, '\n2021-07-26 加入我要找貨功能');


-- 2021-08-11 樣式調整模組化
UPDATE `system_intro` set note = CONCAT(note, '\n\n2021-08-11 樣式調整模組化');
-- 2021-08-11 首頁管理關閉分館廣告不關閉分館
UPDATE `system_intro` set note = CONCAT(note, '\n2021-08-11 首頁管理關閉分館廣告不關閉分館');
-- 2021-08-12 首頁管理關閉分館廣告不關閉分館
UPDATE `system_intro` set note = CONCAT(note, '\n2021-08-12 調整樣式修改、物流功能修改、填訂單可以下拉選縣市區');

-- 2021-09-01 優惠券、直接輸入型優惠券改名稱
UPDATE `system_intro` set note = CONCAT(note, '\n\n2021-09-01 優惠券、直接輸入型優惠券改名稱');
-- 2021-09-01 可控以common.php控制社群登入使用
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-01 可控以common.php控制社群登入使用');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-10 商品新增/編輯前台頁面整合');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-10 後台可自行修改favicon、商品圖片上傳修改(可多圖)');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-11 直接輸入型優惠券可重複設定商品、修正活動優惠金額計算條件為0時的bug');

UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-13 修改主選單fixed');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-14 修改商品麵包屑，且可點擊進入分類/分館');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-15 首頁館告圖改用img不用bg');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-16 前台程式碼整理、全站可使用set_price_sets_and_add_cart(prod_id)將商品加入購物車');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-16 套用最新有特殊模板的CMS作為EDM');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-16 可於庫存警示快速修改庫存量');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-17 可自行設定成功/錯誤提示訊息圖片');

UPDATE `system_intro` set note = CONCAT(note, '\n2021-09-27 拔除angular、加入成立訂單前的加價購可購買數量判斷');

UPDATE `system_intro` set note = CONCAT(note, '\n\n2021-10-07 報名功能修改、匯入/編輯會員、商品左側選單修改');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-10-19 報名功能修改、可搭配email系統寄送報名者');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-10-20 各館(前台商城)可獨立控制兌換點數比率');

UPDATE `system_intro` set note = CONCAT(note, '\n\n2021-11-09 單筆消費刮刮樂、累積消費兌換功能開發(待補刮刮樂樣式)、可隱藏數到期功能(年設99)');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-11-11 會員資料處理功能整合');

UPDATE `system_intro` set note = CONCAT(note, '\n2021-11-17 獨立會員登入頁、修正社群帳號運作');

UPDATE `system_intro` set note = CONCAT(note, '\n2021-11-25 首頁大圖輪播可停用');

UPDATE `system_intro` set note = CONCAT(note, '\n2021-12-08 修正資料庫格式 & 拔除無用資料表');
UPDATE `system_intro` set note = CONCAT(note, '\n2021-12-17 再行銷系統加入範本功能');

UPDATE `system_intro` set note = CONCAT(note, '\n2022-01-18 添加運費標籤功能，運費金額為各別商品設定*數量之加總');

UPDATE `system_intro` set note = CONCAT(note, '\n2022-02-07 修正報名資料、匯入機身碼及名稱更換');
UPDATE `system_intro` set note = CONCAT(note, '\n2022-02-07 添加列印條碼功能');
UPDATE `system_intro` set note = CONCAT(note, '\n2022-02-08 會員加入推薦功能');
UPDATE `system_intro` set note = CONCAT(note, '\n2022-02-08 聯絡我們回函串接google機器人驗證');
UPDATE `system_intro` set note = CONCAT(note, '\n2022-02-08 修改購物車，預留串接平台的程式');
UPDATE `system_intro` set note = CONCAT(note, '\n2022-02-14 加入一頁式商品樣式');
UPDATE `system_intro` set note = CONCAT(note, '\n2022-03-11 首頁廣 三幅廣告-1 改 無限上傳');

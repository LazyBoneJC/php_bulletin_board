# php_bulletin_board
This is a bulletin board website using php as backend.

## 設計理念＆應用場景
可以說是社群軟體的前身，提供使用者一個自由開放的討論空間。

## 作品細節
* 後端技術使用 PHP、MariaDB、Apache
* 功能包括：
  * 留言的 CRUD、分頁 Paginator
  * 會員系統 - 註冊、登入、登出
  * 2 隻 API - 查詢所有留言、新增留言
* 發現並補強數個資安漏洞，包括：
  * cookie 的偽造（Session）
  * 明文密碼（Hash function）
  * XSS、SQL Injection
  * 權限管理

## 畫面呈現
![GITHUB](https://github.com/LazyBoneJC/php_bulletin_board/blob/master/pic/bulletin_board_final.png)

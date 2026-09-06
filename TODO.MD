# 開發待辦

## Login User

- [ ] 設計登入 API：`POST /api/2026-09/auth/login`
- [ ] 建立 Login Request，驗證 email/username 與 password
- [ ] 使用 `Hash::check()` 驗證密碼，禁止明文比對
- [ ] 登入成功後建立 Session 或回傳 API Token
- [ ] 建立登出 API：`POST /api/2026-09/auth/logout`
- [ ] 建立目前使用者 API：`GET /api/2026-09/me`
- [ ] 未登入請求統一回傳 `401 Unauthenticated`
- [ ] 加入登入失敗次數限制與錯誤記錄
- [ ] 測試登入成功、密碼錯誤、帳號不存在及停用帳號

## User 與權限

- [ ] 確認 `users` 欄位包含角色及啟用狀態
- [ ] 建立 `admin` 與 `user` 角色權限
- [ ] 為需要登入的 API 加上 authentication middleware
- [ ] 確認使用者只能存取自己有權限的文件及資料夾
- [ ] 禁止透過修改 file ID 讀取其他使用者文件
- [ ] 測試未登入、一般使用者及管理員的權限差異

## File Upload

- [ ] upload API 只接受已登入使用者
- [ ] 使用者身份由 authentication 取得，不接受前端傳入 `user_id`
- [ ] 驗證 `file` 必須存在且為有效檔案
- [ ] 設定檔案大小上限及允許的 MIME Type
- [ ] 檢查檔案名稱，拒絕 `..`、絕對路徑及 Null byte
- [ ] 使用唯一檔案 ID 或 UUID 儲存實體檔案
- [ ] 確認檔案寫入成功後才建立 Metadata
- [ ] 上傳失敗時清理暫存檔案並回傳明確錯誤
- [ ] 回傳檔案 ID、原始檔名、大小、MIME Type 及上傳時間

## File API

- [ ] 完成文件列表 API
- [ ] 完成文件下載 API，檢查使用者權限
- [ ] 完成文件預覽 API，檢查使用者權限
- [ ] 完成文件重新命名及移動
- [ ] 完成文件刪除及回收機制
- [ ] 所有檔案 API 加入 Feature Test

## Postman 驗收

- [ ] 先呼叫 login API 取得 Session 或 Token
- [ ] 將 Session Cookie 或 `Authorization: Bearer <token>` 帶入後續請求
- [ ] 使用 `POST /api/2026-09/files` 測試 multipart/form-data
- [ ] Body 使用 `form-data`，欄位名稱為 `file`，類型為 `File`
- [ ] 測試未登入時回傳 `401`
- [ ] 測試沒有檔案時回傳 `422`
- [ ] 測試合法檔案上傳成功並產生資料庫記錄
- [ ] 測試不允許的檔案類型及超過大小限制的檔案

## 安全與部署

- [ ] 正式環境啟用 HTTPS
- [ ] 設定 HttpOnly、Secure 及 SameSite Cookie
- [ ] 加入 CSRF 保護或確認 Token API 的安全設定
- [ ] 不在 API 錯誤回應暴露 Stack Trace 或實體儲存路徑
- [ ] 設定 NAS 掛載路徑及最小讀寫權限
- [ ] 建立資料庫與 NAS 備份及還原測試

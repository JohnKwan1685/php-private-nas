# 手機文件上載及 NAS 存取方案

## 1. 目標

建立一個適合手機使用的私人文件系統，讓授權使用者可以：

- 以手機瀏覽器或 PWA 登入系統。
- 上載單一或多個文件至 NAS。
- 瀏覽資料夾、搜尋文件、下載文件。
- 建立資料夾、重新命名、移動及刪除文件。
- 查看上載進度、檔案大小、類型、上載者及時間。
- 由管理員管理使用者、權限及系統活動記錄。

## 2. 建議整體架構

```text
手機瀏覽器 / PWA
	|
	| HTTPS
	v
反向代理 (Caddy 或 Nginx)
	|
	v
PHP Web 應用程式
  - 登入與 Session
  - 權限檢查
  - 檔案及資料夾 API
  - 上載驗證與暫存
  - 稽核記錄
	|                 \
	| PDO               \ SMB/NFS 掛載
	v                  \
MariaDB / SQLite           NAS 儲存區
 (帳戶、Metadata、Log)      (實際文件內容)
```

### 元件責任

| 元件 | 責任 |
|---|---|
| 手機 Web/PWA | 響應式介面、選擇文件、上載進度、文件瀏覽及下載 |
| Caddy/Nginx | HTTPS、反向代理、請求大小限制及靜態資源 |
| PHP 應用程式 | 身份驗證、授權、檔案操作、Metadata 及錯誤處理 |
| 資料庫 | 使用者、資料夾、文件 Metadata、Refresh Token、稽核記錄 |
| NAS | 儲存文件內容；不直接對互聯網公開 |
| 備份服務 | 備份資料庫及 NAS 重要資料，並定期驗證可還原性 |

## 3. NAS 連接方式

優先採用「NAS 只掛載到 PHP 伺服器」的模式：

1. 在 NAS 建立專用帳戶，例如 `php_file_service`。
2. 只授予該帳戶文件根目錄的必要讀寫權限。
3. 在伺服器以 SMB 3 或 NFSv4 掛載到固定路徑，例如 `/srv/nas-files`。
4. PHP 使用應用程式設定的根路徑，不接受用戶輸入的實體路徑。
5. 前端永遠不能直接連接 NAS；所有操作均經 PHP 權限檢查。

不要把 NAS 管理介面、SMB、NFS 或 NAS 共享連接埠直接暴露到互聯網。

## 4. 文件資料流程

### 上載

1. 使用者登入並取得 Session/CSRF Token。
2. 前端先建立上載請求，傳送目標資料夾、檔名及大小。
3. PHP 驗證使用者權限、檔案大小、檔案名稱及剩餘空間。
4. 文件先寫入伺服器暫存目錄，避免未完成文件直接出現在 NAS 正式目錄。
5. 上載完成後，PHP 計算 SHA-256，重新確認檔案大小與類型。
6. 使用唯一內部 ID 搬移至 NAS；資料庫以交易方式寫入 Metadata。
7. 寫入成功後才回覆前端完成；失敗時刪除暫存檔並記錄原因。

大文件應使用分段上載（chunk upload），支援中斷後續傳；小文件可先使用一般 `multipart/form-data`。

### 下載

1. PHP 檢查文件是否存在及使用者是否有讀取權限。
2. PHP 以串流方式輸出文件，避免把整個文件載入記憶體。
3. 回應使用安全的下載檔名及正確 MIME Type。
4. 可選擇加入短期有效的下載 Token，但 Token 必須綁定文件、使用者及有效期限。

## 5. 權限模型

第一階段採用簡單且容易維護的模型：

- `admin`：管理使用者、所有文件及系統設定。
- `user`：只能存取自己的根目錄及被分享的資料夾。
- 資料夾權限：`read`、`write`、`delete`。
- 預設拒絕所有權限，明確授權後才可操作。
- 每次 API 請求在服務端重新檢查權限，不能只相信前端傳入的路徑或角色。

## 6. 建議資料表

### `users`

`id`、`username`、`password_hash`、`role`、`is_active`、`created_at`、`updated_at`

### `folders`

`id`、`parent_id`、`owner_id`、`name`、`storage_key`、`created_at`、`updated_at`

### `files`

`id`、`folder_id`、`owner_id`、`original_name`、`storage_key`、`mime_type`、`size_bytes`、`sha256`、`created_at`、`updated_at`

### `folder_permissions`

`folder_id`、`user_id`、`can_read`、`can_write`、`can_delete`

### `audit_logs`

`id`、`user_id`、`action`、`target_type`、`target_id`、`ip_address`、`user_agent`、`created_at`

檔案在 NAS 的實際名稱建議使用內部 ID 或 UUID，原始檔名只存於資料庫，以避免檔名衝突及路徑穿越問題。

## 7. API 草案

所有 API 使用 `/api` 前綴並回傳 JSON；文件內容例外使用串流回應。

| 方法 | 路徑 | 用途 |
|---|---|---|
| `POST` | `/api/auth/login` | 登入 |
| `POST` | `/api/auth/logout` | 登出 |
| `GET` | `/api/me` | 取得目前使用者 |
| `GET` | `/api/folders/{id}/items` | 列出資料夾內容 |
| `POST` | `/api/folders` | 建立資料夾 |
| `POST` | `/api/uploads` | 建立上載工作 |
| `PUT` | `/api/uploads/{id}/chunks/{part}` | 上載分段 |
| `POST` | `/api/uploads/{id}/complete` | 完成及驗證上載 |
| `GET` | `/api/files/{id}/download` | 下載文件 |
| `PATCH` | `/api/files/{id}` | 重新命名或移動 |
| `DELETE` | `/api/files/{id}` | 刪除文件 |

## 8. 安全要求

- 全程使用 HTTPS；內網也建議使用有效憑證。
- 密碼只使用 `password_hash()` 儲存，禁止明文或自行加密。
- 使用 HttpOnly、Secure、SameSite Cookie；修改資料的請求加入 CSRF 保護。
- 登入及上載 API 加入速率限制，並記錄失敗登入。
- 檔案名稱只允許安全字元，正規化 Unicode，拒絕 `..`、絕對路徑及 Null byte。
- 限制單檔大小、總上載大小及允許的檔案類型；不可只依賴副檔名判斷 MIME Type。
- PHP、依賴套件、作業系統及 NAS 韌體定期更新。
- PHP 程序只能讀寫文件根目錄及暫存目錄，不能使用 root 權限。
- 刪除先移至 NAS 垃圾桶或保留區，確認備份後才永久清除。
- 不在錯誤回應中暴露 NAS 路徑、資料庫資訊或 Stack Trace。

## 9. 備份與故障處理

- 資料庫每日備份，保留多個版本。
- NAS 重要文件使用 NAS Snapshot 或異地備份。
- 備份必須至少每月進行一次還原測試。
- NAS 暫時離線時，API 回覆明確的服務暫不可用狀態，不刪除資料庫 Metadata。
- 上載使用暫存檔及清理工作，定期清除逾時未完成的上載。
- 監控磁碟空間、NAS 掛載狀態、PHP 錯誤率及備份結果。

## 10. 分階段實作

### Phase 1：可用核心

- 建立 PHP 專案、資料庫連線及設定管理。
- 完成登入、登出及單一使用者權限。
- 完成資料夾列表、文件列表、一般上載及下載。
- NAS 以本機掛載路徑接入，加入路徑安全檢查。

### Phase 2：手機體驗

- 加入響應式介面及 PWA manifest。
- 加入多文件選擇、上載進度、取消及失敗重試。
- 加入搜尋、重新命名、建立資料夾及垃圾桶。

### Phase 3：多人及可靠性

- 加入使用者管理、資料夾分享及細緻權限。
- 加入分段上載及中斷續傳。
- 加入稽核記錄、速率限制、監控及備份還原流程。

### Phase 4：部署優化

- 以 Docker Compose 或正式服務方式部署 PHP、反向代理及資料庫。
- 啟用 HTTPS、自動更新流程及健康檢查。
- 進行安全測試、權限測試、大文件測試及 NAS 離線測試。

## 11. 驗收條件

- 未登入使用者不能查看、上載或下載任何文件。
- 使用者不能透過 `../`、編碼路徑或修改 ID 讀取其他文件。
- 上載中斷後不會產生可見的半成品文件。
- 重新整理或重新登入後，已完成文件仍可正確列出及下載。
- NAS 暫停服務時，系統不會誤刪資料庫記錄或回報上載成功。
- 手機尺寸下可完成登入、上載、瀏覽及下載主要流程。
- 備份可以在全新環境還原資料庫及至少一份文件。
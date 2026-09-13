# PHP Home NAS API

以 Laravel 12 與 Laravel Sanctum 建立的個人檔案儲存 API。此專案只負責後端 API，
前端應由獨立專案維護。API 使用版本化路徑，目前版本為 `2026-09`。

## 快速開始

### 環境需求

- PHP 8.2 或以上
- Composer

### 安裝與啟動

```bash
composer run setup
php artisan serve
```

預設服務位置為 `http://127.0.0.1:8000`。API Base URL：

```text
http://127.0.0.1:8000/api/2026-09
```

若只需要安裝 PHP 依賴，也可以執行：

```bash
composer install
php artisan migrate
```

本後端不包含前端原始碼、Node.js、npm、Vite 或 Tailwind。若需要部署由前端專案
建置出的靜態檔案，可將產出檔放在 `public/`；前端開發與建置應在獨立的前端專案完成。

## API 共通規則

- JSON 請求與 JSON 回應使用 `Content-Type: application/json`；檔案上傳使用 `multipart/form-data`。
- 需要登入的端點使用 `Authorization: Bearer <access_token>`。
- `access_token` 有效期限為 1 小時，`refresh_token` 有效期限為 30 天。
- 檔案上傳使用 `multipart/form-data`，欄位名稱為 `file`。
- `message` 在 `APP_DEBUG=false` 時會是 `null`；驗證錯誤明細也只在 debug 模式提供。

成功回應格式：

```json
{
    "code": "LOGIN_SUCCESS",
    "data": {},
    "message": null,
    "nextPageCursor": ""
}
```

## 認證 API

### 註冊

`POST /register`

請求 Body：

```json
{
    "account": "demo@example.com",
    "username": "Demo User",
    "password": "password",
    "password_confirmation": "password"
}
```

回應 `200`：`code` 為 `REGISTER_SUCCESS`，`data` 內含 `user` 與 `tokens`。

### 登入

`POST /login`

請求 Body：

```json
{
    "account": "demo@example.com",
    "password": "password"
}
```

回應 `200`：`code` 為 `LOGIN_SUCCESS`，`data` 內含 `user` 與 `tokens`。

### 更新 Access Token

`POST /refresh`

請求 Body：

```json
{
    "refresh_token": "<refresh_token>"
}
```

成功回應 `200` 的 `code` 為 `TOKEN_REFRESHED`。Refresh token 使用後會失效，
請使用回應中的新 token 組合取代舊值。

Token 回應格式：

```json
{
    "token_type": "Bearer",
    "access_token": "<access_token>",
    "refresh_token": "<refresh_token>",
    "expires_at": "2026-09-13T12:00:00.000000Z"
}
```

## 檔案 API

以下端點都需要 `Authorization: Bearer <access_token>`。

### 上傳檔案

`POST /files`

```bash
curl -X POST http://127.0.0.1:8000/api/2026-09/files \
	-H "Authorization: Bearer <access_token>" \
	-F "file=@/path/to/example.pdf"
```

請求欄位：`file`（必填，檔案）。成功回應 `200` 的 `code` 為 `FILE_UPLOADED`。

檔案資料格式：

```json
{
    "id": 1,
    "name": "example.pdf",
    "created_at": "2026-09-13T12:00:00.000000Z",
    "updated_at": "2026-09-13T12:00:00.000000Z"
}
```

### 檢視檔案

`GET /files/{file}`

回傳檔案內容，並依檔案 MIME type 設定 `Content-Type`。

```bash
curl http://127.0.0.1:8000/api/2026-09/files/1 \
	-H "Authorization: Bearer <access_token>" \
	--output example.pdf
```

### 下載檔案

`GET /files/{file}/download`

回傳檔案下載回應，檔名使用上傳時的檔名。

```bash
curl http://127.0.0.1:8000/api/2026-09/files/1/download \
	-H "Authorization: Bearer <access_token>" \
	--output example.pdf
```

### 刪除檔案

`DELETE /files/{file}`

```bash
curl -X DELETE http://127.0.0.1:8000/api/2026-09/files/1 \
	-H "Authorization: Bearer <access_token>"
```

成功回應 `200` 的 `code` 為 `FILE_DELETED`，`data` 為空陣列。

## 錯誤回應

錯誤回應都使用相同結構：

```json
{
    "code": "VALIDATION_FAILED",
    "data": null,
    "message": null,
    "nextPageCursor": ""
}
```

常見 HTTP 狀態與 `code`：

| HTTP 狀態 | code                      | 說明                       |
| --------- | ------------------------- | -------------------------- |
| `401`     | `AUTHENTICATION_REQUIRED` | 缺少或無效的 Bearer token  |
| `401`     | `INVALID_CREDENTIALS`     | 帳號或密碼錯誤             |
| `401`     | `INVALID_REFRESH_TOKEN`   | Refresh token 無效或已過期 |
| `404`     | `RESOURCE_NOT_FOUND`      | 找不到指定檔案             |
| `422`     | `VALIDATION_FAILED`       | 請求欄位驗證失敗           |
| `500`     | `INTERNAL_ERROR`          | 伺服器內部錯誤             |

## 開發指令

```bash
# 執行測試
composer test

# 查看 API 路由
php artisan route:list --path=api

# 啟動 API 開發伺服器
composer run dev
```

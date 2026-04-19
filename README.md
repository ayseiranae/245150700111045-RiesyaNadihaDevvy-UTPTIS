# API Toko Pastry Sederhana — UTP TIS

Backend API sederhana untuk toko pastry menggunakan **Laravel 12** dengan penyimpanan **mock data JSON** (tanpa database).

| Item | Detail |
|------|--------|
| **Nama** | Riesya Nadiha Devvy |
| **NIM** | 245150700111045 |
| **Mata Kuliah** | Teknologi Integrasi Sistem (TIS) |
| **Framework** | Laravel 12 |
| **Bahasa** | PHP 8.2+ |

---

## Cara Menjalankan

```bash
# 1. Clone repository
git clone <repository-url>
cd 245150700111045-RiesyaNadihaDevvy-UTPTIS

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Generate Swagger documentation
php artisan l5-swagger:generate

# 5. Jalankan server
php artisan serve
```

Server berjalan di `http://localhost:8000`

---

## Daftar Endpoint API

Base URL: `http://localhost:8000/api`

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/items` | Menampilkan seluruh data item |
| `GET` | `/api/items/{id}` | Menampilkan item berdasarkan ID |
| `POST` | `/api/items` | Membuat data item baru |
| `PUT` | `/api/items/{id}` | Mengedit seluruh data item (full update) |
| `PATCH` | `/api/items/{id}` | Mengedit sebagian data item (partial update) |
| `DELETE` | `/api/items/{id}` | Menghapus data item |

### Query Parameters (GET /api/items)

| Parameter | Tipe | Deskripsi |
|-----------|------|-----------|
| `category` | string | Filter item berdasarkan kategori (contoh: `Pastry`, `Sweet Bread`) |
| `search` | string | Cari item berdasarkan nama (case-insensitive) |

---

## Contoh Request & Response

### GET /api/items — Menampilkan Semua Item

```
GET http://localhost:8000/api/items
```

Response (200):
```json
{
    "status": "success",
    "message": "Items retrieved successfully",
    "data": [
        {
            "id": 2,
            "name": "Pain au Chocolat",
            "category": "Pastry",
            "price": 20000,
            "stock": 20,
            "description": "Pastry filled with premium chocolate"
        },
        {
            "id": 3,
            "name": "Strawberry Danish",
            "category": "Pastry",
            "price": 22000,
            "stock": 15,
            "description": "Pastry topped with fresh strawberries and custard"
        }
    ]
}
```

### GET /api/items?category=Pastry — Filter by Category

```
GET http://localhost:8000/api/items?category=Pastry
```

### GET /api/items?search=Croissant — Search by Name

```
GET http://localhost:8000/api/items?search=Croissant
```

### GET /api/items/{id} — Menampilkan Item by ID

```
GET http://localhost:8000/api/items/2
```

Response (200):
```json
{
    "status": "success",
    "message": "Item found",
    "data": {
        "id": 2,
        "name": "Pain au Chocolat",
        "category": "Pastry",
        "price": 20000,
        "stock": 20,
        "description": "Pastry filled with premium chocolate"
    }
}
```

Response Error (404):
```json
{
    "status": "error",
    "message": "Item with ID 99 not found",
    "data": null
}
```

### POST /api/items — Membuat Item Baru

```
POST http://localhost:8000/api/items
Content-Type: application/json
```

Body (semua field wajib):
```json
{
    "name": "Matcha Croissant",
    "category": "Pastry",
    "price": 27000,
    "stock": 10,
    "description": "Croissant with matcha cream filling and white chocolate drizzle"
}
```

Response (200):
```json
{
    "status": "success",
    "message": "Item created successfully",
    "data": {
        "id": 7,
        "name": "Matcha Croissant",
        "category": "Pastry",
        "price": 27000,
        "stock": 10,
        "description": "Croissant with matcha cream filling and white chocolate drizzle"
    }
}
```

### PUT /api/items/{id} — Full Update

```
PUT http://localhost:8000/api/items/4
Content-Type: application/json
```

Body (semua field wajib):
```json
{
    "name": "Cinnamon Roll Special",
    "category": "Sweet Bread",
    "price": 22000,
    "stock": 10,
    "description": "Special cinnamon roll with extra cream cheese frosting"
}
```

Response (200):
```json
{
    "status": "success",
    "message": "Item updated successfully",
    "data": {
        "id": 4,
        "name": "Cinnamon Roll Special",
        "category": "Sweet Bread",
        "price": 22000,
        "stock": 10,
        "description": "Special cinnamon roll with extra cream cheese frosting"
    }
}
```

### PATCH /api/items/{id} — Partial Update

```
PATCH http://localhost:8000/api/items/3
Content-Type: application/json
```

Body (minimal satu field):
```json
{
    "price": 28000,
    "stock": 8
}
```

Response (200):
```json
{
    "status": "success",
    "message": "Item partially updated",
    "data": {
        "id": 3,
        "name": "Strawberry Danish",
        "category": "Pastry",
        "price": 28000,
        "stock": 8,
        "description": "Pastry topped with fresh strawberries and custard"
    }
}
```

### DELETE /api/items/{id} — Hapus Item

```
DELETE http://localhost:8000/api/items/6
```

Response (200):
```json
{
    "status": "success",
    "message": "Item deleted successfully",
    "data": null
}
```

Response Error (404):
```json
{
    "status": "error",
    "message": "Item with ID 99 not found",
    "data": null
}
```

---

## Validation Rules

| Field | Rule |
|-------|------|
| `name` | Wajib diisi |
| `category` | Wajib diisi |
| `price` | Wajib diisi, numerik, minimal 1000 |
| `stock` | Wajib diisi, numerik, minimal 0 |
| `description` | Wajib diisi |

---

## Error Handling

| HTTP Code | Deskripsi |
|-----------|-----------|
| `200` | Request berhasil |
| `404` | Resource tidak ditemukan |
| `422` | Validasi gagal |

Format response konsisten:
```json
// Sukses
{ "status": "success", "message": "...", "data": { } }

// Error
{ "status": "error", "message": "...", "data": null }
```

---

## Dokumentasi Swagger

Swagger UI dapat diakses di: `http://localhost:8000/api/documentation`

Swagger didukung oleh package:
- `darkaonline/l5-swagger` ^11.0
- `zircote/swagger-php` ^6.1

Untuk re-generate dokumentasi Swagger:
```bash
php artisan l5-swagger:generate
```

---

## Struktur Project

```
245150700111045-RiesyaNadihaDevvy-UTPTIS/
├── app/Http/Controllers/
│   └── ItemController.php          # Controller CRUD + Swagger annotations
├── bootstrap/
│   └── app.php                     # Registrasi route API
├── routes/
│   └── api.php                     # Definisi endpoint API
├── storage/app/
│   └── items.json                  # Mock data JSON (non-database)
├── config/
│   └── l5-swagger.php              # Konfigurasi Swagger
└── README.md                       # File ini
```

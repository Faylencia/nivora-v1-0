# NIVORA Promo API

Implementasi promo mengikuti ERD internal terbaru yang diberikan: `promos`, `promo_products`, dan `sale_promos`.

Kontrak OpenAPI untuk alur web (auth, katalog, stok, promo, dan POS) tersedia di [`openapi.yaml`](openapi.yaml).

## Aturan MVP

- `promos.type`: `product` atau `transaction`.
- Karena ERD hanya menyediakan satu `discount_value` tanpa kolom `discount_type`, implementasi MVP menafsirkan `discount_value` sebagai persentase `0..100`.
- Promo harus berstatus `active` dan berada dalam periode `start_date..end_date` saat dipakai pada POS.
- Product promo diterapkan hanya ke produk yang terhubung melalui `promo_products`.
- Transaction promo diterapkan ketika subtotal memenuhi `min_transaction` (jika diisi).
- Nilai diskon dihitung ulang di server dan disimpan sebagai nominal pada `sale_promos.discount_amount`.
- Promo yang pernah dipakai transaksi tidak dihapus permanen; DELETE mengubah status menjadi `inactive` agar histori transaksi tetap aman.

## Endpoint

`GET /api/v1/promotions`

`GET /api/v1/promotions/{id}`

`POST /api/v1/promotions`

`PUT /api/v1/promotions/{id}`

`DELETE /api/v1/promotions/{id}`

`POST /api/v1/sales` menerima `promo_ids[]`.

Semua endpoint yang membutuhkan konteks toko menggunakan Bearer Token dan business context.

## Contoh product promo

```json
{
  "name": "Diskon Kopi 20%",
  "description": "Promo kopi bulan ini",
  "type": "product",
  "discount_value": 20,
  "min_transaction": null,
  "start_date": "2026-09-28 00:00:00",
  "end_date": "2026-10-31 23:59:59",
  "status": "active",
  "product_ids": [1, 2]
}
```

## Contoh transaction promo

```json
{
  "name": "Hemat 10%",
  "description": "Diskon untuk transaksi minimal Rp100.000",
  "type": "transaction",
  "discount_value": 10,
  "min_transaction": 100000,
  "start_date": "2026-09-28 00:00:00",
  "end_date": "2026-10-31 23:59:59",
  "status": "active"
}
```

## POS dengan promo

```json
{
  "outlet_id": 1,
  "customer_id": 10,
  "items": [
    {"product_id": 1, "quantity": 2},
    {"product_id": 2, "quantity": 1}
  ],
  "promo_ids": [1, 2],
  "discount": 0,
  "tax": 0,
  "payment_method": "cash",
  "paid": 20000
}
```

## Setup database lokal

Gunakan migration Laravel untuk membuat seluruh tabel, termasuk `promos`, `promo_products`, dan `sale_promos`. Jangan membuat tabel atau menjalankan SQL `CREATE TABLE` secara manual.

Checkout ini saat ini memakai SQLite melalui `.env` (`DB_CONNECTION=sqlite`) dan file `database/database.sqlite`, jadi tidak perlu membuat tabel atau database di Laragon. Jalankan:

```bash
php artisan optimize:clear
php artisan migrate --seed
```

Jika memilih MySQL Laragon, ubah `DB_CONNECTION` ke `mysql`, arahkan `.env` ke database kosong `nivora`, lalu tetap jalankan command migration di atas. MySQL memerlukan database tujuan, tetapi Laravel migration yang membuat tabel-tabelnya.

Setelah migration:

1. Build asset web dengan `npm run build`, lalu buka `/register` dan buat akun owner. Form registrasi juga membuat bisnis dan outlet pertama.
2. Setelah diarahkan ke halaman produk, buat kategori, produk, dan stok awal. Akun yang sudah ada dapat masuk lewat `/login`.
3. Buka `/owner/promotions` untuk membuat promo produk atau transaksi.
4. Buka `/cashier/pos`, pilih produk dan promo yang tersedia, lalu simpan transaksi.
5. Backend menghitung ulang diskon, memperbarui stok, dan mencatat pemakaian pada `sale_promos` serta nominalnya di `sales.discount`.
6. Periksa status migration dengan `php artisan migrate:status` bila perlu.

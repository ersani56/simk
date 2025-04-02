-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 01 Apr 2025 pada 19.20
-- Versi server: 10.11.11-MariaDB-cll-lve
-- Versi PHP: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u1135200_dbk56App`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bahanbakus`
--

CREATE TABLE `bahanbakus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_bbaku` varchar(255) NOT NULL,
  `nama_bbaku` varchar(255) NOT NULL,
  `satuan` enum('Kg','Mtr','Yrd','Pcs') NOT NULL,
  `harga` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bahanbakus`
--

INSERT INTO `bahanbakus` (`id`, `kode_bbaku`, `nama_bbaku`, `satuan`, `harga`, `created_at`, `updated_at`) VALUES
(1, 'PE000010', 'PE Hitam', 'Kg', 60000.00, '2025-03-17 00:47:53', '2025-03-17 00:47:53'),
(2, 'PE000011', 'PE Merah cabe', 'Kg', 60000.00, '2025-03-17 07:07:28', '2025-03-17 07:07:28'),
(3, 'PE000007', 'PE Orange', 'Kg', 60000.00, '2025-03-31 08:42:17', '2025-03-31 08:42:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bahanjadis`
--

CREATE TABLE `bahanjadis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_bjadi` varchar(8) NOT NULL,
  `nama_bjadi` varchar(100) NOT NULL,
  `kategori` enum('Kaos','Trening','Batik','Celana','Lainnya') NOT NULL,
  `satuan` varchar(255) DEFAULT NULL,
  `upah` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `gambar1` varchar(255) DEFAULT NULL,
  `gambar2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bahanjadis`
--

INSERT INTO `bahanjadis` (`id`, `kode_bjadi`, `nama_bjadi`, `kategori`, `satuan`, `upah`, `created_at`, `updated_at`, `gambar1`, `gambar2`) VALUES
(1, 'K1000010', 'Kaos TK V Ungu muda tangan strip 2 ungu tua', 'Kaos', 'Pcs', 2800, '2025-03-17 00:49:39', '2025-03-17 00:49:39', 'products/01JPHH8V2H8KX8GHDWKR6WMFBS.jpeg', 'products/01JPHH8V2K5C9HC67ST4EQWJDZ.jpeg'),
(2, 'K5000001', 'Kaos DW PE KKK Dongker - Orange', 'Kaos', 'Pcs', 65000, '2025-03-19 22:49:23', '2025-03-19 22:49:23', 'products/01JPS1JS19064E2GE0GVCK8A0K.jpeg', 'products/01JPS1JS1CJ63HBFN53H1KY9GY.jpeg'),
(3, 'K3000001', 'Kaos SMP orange-krem tangan strip 2 orange', 'Kaos', 'Pcs', 3200, '2025-03-31 08:48:26', '2025-03-31 08:48:26', 'products/01JQPE7JRH3RPAZP2JD46WJKJC.jpeg', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_03_07_165200_create_bahanbakus_table', 1),
(6, '2025_03_10_074440_create_stoks_table', 1),
(7, '2025_03_11_033950_add_nama_bbaku_to_stoks', 1),
(8, '2025_03_11_035404_change_column_type_in_bahan_bakus', 1),
(9, '2025_03_12_014147_remove_nama_bbaku_from_stoks', 1),
(10, '2025_03_12_014502_remove_kode_stok_from_stoks', 1),
(11, '2025_03_12_081137_create_bahanjadis_table', 1),
(12, '2025_03_13_054100_remove_gambar1_from_bahanjadis', 1),
(13, '2025_03_13_054451_remove_gambar2_from_bahanjadis', 1),
(14, '2025_03_13_054641_add_gambar1_to_bahanjadis', 1),
(15, '2025_03_13_055146_add_gambar2_to_bahanjadis', 1),
(16, '2025_03_16_151814_add_satuan_to_bahanjadis', 1),
(17, '2025_03_18_071902_create_pelanggans_table', 2),
(18, '2025_03_18_122935_create_pesanans_table', 2),
(19, '2025_03_18_224108_rename_kode_pelanggan_to_kode_plg_in_pelanggans_table', 2),
(20, '2025_03_18_224347_rename_nama_pelanggan_to_nama_plg_in_pelanggans_table', 2),
(21, '2025_03_23_223152_create_pesanans_table', 3),
(22, '2025_03_23_223429_create_pesanan_details_table', 3),
(23, '2025_03_24_014322_rename_kode_barang_to_kode_bjadi_in_pesanan_details_table', 3),
(24, '2025_03_25_072226_change_column_type_in_pesanan_details_table', 4),
(25, '2025_03_27_065515_add_role_to_users_table', 4),
(26, '2025_03_27_073038_add_is_active_to_users_table', 4),
(27, '2025_03_28_042734_create_permission_tables', 4),
(28, '2025_03_28_092247_add_columns_to_pesanan_details_table', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(3, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggans`
--

CREATE TABLE `pelanggans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_plg` varchar(8) NOT NULL,
  `nama_plg` varchar(60) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `telepon` varchar(18) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pelanggans`
--

INSERT INTO `pelanggans` (`id`, `kode_plg`, `nama_plg`, `alamat`, `telepon`, `created_at`, `updated_at`) VALUES
(1, 'PLG00001', 'Ny. Sunarti', 'Mulya Asri', '0853', '2025-03-18 15:51:41', '2025-03-18 15:51:41'),
(3, 'PLG00002', 'Ny. Sri Mustika', 'Daya Asri', '0812', '2025-03-31 08:50:06', '2025-03-31 08:50:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanans`
--

CREATE TABLE `pesanans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_faktur` varchar(12) NOT NULL,
  `kode_plg` varchar(8) NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pesanans`
--

INSERT INTO `pesanans` (`id`, `no_faktur`, `kode_plg`, `tanggal`, `created_at`, `updated_at`) VALUES
(1, 'INV240325001', 'PLG00001', '2025-03-24', '2025-03-24 04:11:08', '2025-03-24 04:11:08'),
(2, 'INV300325001', 'PLG00001', '2025-03-30', '2025-03-29 21:12:10', '2025-03-29 21:12:10'),
(3, 'INV310325001', 'PLG00002', '2025-03-31', '2025-03-31 08:52:07', '2025-03-31 08:52:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan_details`
--

CREATE TABLE `pesanan_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_faktur` varchar(12) NOT NULL,
  `kode_bjadi` varchar(8) NOT NULL,
  `ukuran` varchar(10) NOT NULL,
  `harga` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pemotong` varchar(25) DEFAULT NULL,
  `penjahit` varchar(25) DEFAULT NULL,
  `penyablon` varchar(25) DEFAULT NULL,
  `ket` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pesanan_details`
--

INSERT INTO `pesanan_details` (`id`, `no_faktur`, `kode_bjadi`, `ukuran`, `harga`, `jumlah`, `status`, `created_at`, `updated_at`, `pemotong`, `penjahit`, `penyablon`, `ket`) VALUES
(1, 'INV240325001', 'K1000010', 'L', 85000, 30, 'selesai di sablon', '2025-03-24 04:11:08', '2025-03-31 08:53:41', 'Ersani', 'Ersani', 'Ersani', NULL),
(2, 'INV240325001', 'K1000010', 'XL', 85000, 20, 'selesai', '2025-03-24 04:11:08', '2025-03-24 04:11:08', NULL, NULL, NULL, NULL),
(3, 'INV300325001', 'K5000001', 'XL', 85000, 20, 'selesai dijahit', '2025-03-29 21:12:10', '2025-03-30 05:21:22', 'Ersani', 'Ersani', NULL, NULL),
(4, 'INV310325001', 'K3000001', 'M', 95000, 70, 'antrian', '2025-03-31 08:52:07', '2025-03-31 08:52:07', NULL, NULL, NULL, NULL),
(5, 'INV310325001', 'K3000001', 'L', 95000, 130, 'antrian', '2025-03-31 08:52:07', '2025-03-31 08:52:07', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(3, 'admin', 'web', '2025-03-29 22:57:47', '2025-03-29 22:57:47'),
(4, 'user', 'web', '2025-03-29 22:57:47', '2025-03-29 22:57:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stoks`
--

CREATE TABLE `stoks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_bbaku` varchar(255) NOT NULL,
  `jml_stok` int(11) NOT NULL,
  `lokasi` enum('Rumah','Ruko','Sri Agung','SOhari','BUde Imah','Mb Hani') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stoks`
--

INSERT INTO `stoks` (`id`, `kode_bbaku`, `jml_stok`, `lokasi`, `created_at`, `updated_at`) VALUES
(1, 'PE000010', 26, 'Rumah', '2025-03-18 15:51:59', '2025-03-18 15:51:59'),
(2, 'PE000007', 26, 'Rumah', '2025-03-31 09:07:17', '2025-03-31 09:07:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `is_active`) VALUES
(1, 'Ersani', 'mr.ersani@gmail.com', NULL, '$2y$12$A2QsSMiy91N8la/2OD3.v.HFl1Lz5fU8hP53dQjbb.WLpID2mbCaC', 'UlurYtUxwnZnmeGAEL0jGC85RSmPjd5Uw6PgaHKn0HboU2kyr3B6mPnRDs1A', '2025-03-16 23:25:17', '2025-03-16 23:25:17', 'admin', 1),
(2, 'sani', 'er_sani@yahoo.com', NULL, '$2y$12$pluy/ghB2krsnmg2jxz1ruFStQRJn23OY935qRSMADyYVGSBo0U8O', NULL, '2025-03-16 23:43:08', '2025-03-16 23:43:08', 'user', 1),
(3, 'Sari', 'sari@gmail.com', NULL, '$2y$12$HyWJX9s9UTujIFt2L8zz4eDlQKlp87KuDfSFmOpo04caKo4QFMPM2', NULL, '2025-03-29 22:59:53', '2025-03-29 22:59:53', 'user', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bahanbakus`
--
ALTER TABLE `bahanbakus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bahanbakus_kode_bbaku_unique` (`kode_bbaku`),
  ADD UNIQUE KEY `bahanbakus_nama_bbaku_unique` (`nama_bbaku`);

--
-- Indeks untuk tabel `bahanjadis`
--
ALTER TABLE `bahanjadis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bahanjadis_kode_bjadi_unique` (`kode_bjadi`),
  ADD UNIQUE KEY `bahanjadis_nama_bjadi_unique` (`nama_bjadi`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `pelanggans`
--
ALTER TABLE `pelanggans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pelanggans_kode_pelanggan_unique` (`kode_plg`),
  ADD UNIQUE KEY `pelanggans_nama_pelanggan_unique` (`nama_plg`),
  ADD UNIQUE KEY `pelanggans_alamat_unique` (`alamat`),
  ADD UNIQUE KEY `pelanggans_telepon_unique` (`telepon`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `pesanans`
--
ALTER TABLE `pesanans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pesanans_no_faktur_unique` (`no_faktur`);

--
-- Indeks untuk tabel `pesanan_details`
--
ALTER TABLE `pesanan_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_details_no_faktur_foreign` (`no_faktur`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `stoks`
--
ALTER TABLE `stoks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bahanbakus`
--
ALTER TABLE `bahanbakus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `bahanjadis`
--
ALTER TABLE `bahanjadis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `pelanggans`
--
ALTER TABLE `pelanggans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pesanans`
--
ALTER TABLE `pesanans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pesanan_details`
--
ALTER TABLE `pesanan_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `stoks`
--
ALTER TABLE `stoks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesanan_details`
--
ALTER TABLE `pesanan_details`
  ADD CONSTRAINT `pesanan_details_no_faktur_foreign` FOREIGN KEY (`no_faktur`) REFERENCES `pesanans` (`no_faktur`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

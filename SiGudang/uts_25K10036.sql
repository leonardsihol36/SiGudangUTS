-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: uts_25K10036
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `uts_25K10036`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `uts_25K10036` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `uts_25K10036`;

--
-- Table structure for table `t036_audit_logs`
--

DROP TABLE IF EXISTS `t036_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t036_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `t036_audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `t036_audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `t036_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036_audit_logs`
--

LOCK TABLES `t036_audit_logs` WRITE;
/*!40000 ALTER TABLE `t036_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `t036_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t036_barang`
--

DROP TABLE IF EXISTS `t036_barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t036_barang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `stok_minimum` int NOT NULL DEFAULT '10',
  `lokasi_penyimpanan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `t036_barang_kode_barang_unique` (`kode_barang`),
  KEY `t036_barang_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `t036_barang_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `t036_supplier` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036_barang`
--

LOCK TABLES `t036_barang` WRITE;
/*!40000 ALTER TABLE `t036_barang` DISABLE KEYS */;
INSERT INTO `t036_barang` VALUES (1,'BRG-001','Barcode Scanner Wireless 2D','Elektronik & Perangkat','Unit',45,10,'Rak A-01',3,'Scanner barcode nirkabel frekuensi 2.4GHz dengan jangkauan 50 meter.','2026-10-18 05:32:17','2026-10-18 05:32:17'),(2,'BRG-002','Kardus Packing Double Wall 40x30x30','Packaging','Pcs',250,50,'Zona B-Pallet 04',1,'Kardus corrugated karton tebal double wall untuk pengiriman kargo berat.','2026-10-18 05:32:17','2026-10-18 05:32:17'),(3,'BRG-003','Hand Pallet Truck 3 Ton','Alat Berat & Handling','Unit',8,5,'Area Docking C',2,'Hand pallet hidrolik kapasitas angkat 3000 kg roda polyurethane.','2026-10-18 05:32:17','2026-10-18 05:32:17'),(4,'BRG-004','Thermal Label Sticker 100x150 mm','Packaging','Roll',8,15,'Rak A-03',1,'Kertas label barcode direct thermal isi 500 pcs per roll.','2026-10-18 05:32:17','2026-10-18 05:32:17'),(5,'BRG-005','Stretch Film 50cm x 300m 17 Micron','Packaging','Roll',60,20,'Zona B-Pallet 01',2,'Plastik wrapping pembungkus pallet elastis kualitas industri.','2026-10-18 05:32:17','2026-10-18 05:32:17');
/*!40000 ALTER TABLE `t036_barang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t036_migrations`
--

DROP TABLE IF EXISTS `t036_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t036_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036_migrations`
--

LOCK TABLES `t036_migrations` WRITE;
/*!40000 ALTER TABLE `t036_migrations` DISABLE KEYS */;
INSERT INTO `t036_migrations` VALUES (1,'2024_01_01_000001_create_t036_roles_table',1),(2,'2024_01_01_000002_create_t036_users_table',1),(3,'2024_01_01_000003_create_t036_supplier_table',1),(4,'2024_01_01_000004_create_t036_barang_table',1),(5,'2024_01_01_000005_create_t036_mutasi_stok_table',1),(6,'2024_01_01_000006_create_t036_audit_logs_table',1),(7,'2024_01_01_000007_create_t036_personal_access_tokens_table',1),(8,'2024_01_01_000008_create_t036_password_reset_tokens_table',1);
/*!40000 ALTER TABLE `t036_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t036_mutasi_stok`
--

DROP TABLE IF EXISTS `t036_mutasi_stok`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t036_mutasi_stok` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `barang_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `tipe_mutasi` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL,
  `stok_sebelum` int NOT NULL,
  `stok_sesudah` int NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `tanggal_mutasi` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `t036_mutasi_stok_barang_id_foreign` (`barang_id`),
  KEY `t036_mutasi_stok_user_id_foreign` (`user_id`),
  CONSTRAINT `t036_mutasi_stok_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `t036_barang` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `t036_mutasi_stok_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `t036_users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036_mutasi_stok`
--

LOCK TABLES `t036_mutasi_stok` WRITE;
/*!40000 ALTER TABLE `t036_mutasi_stok` DISABLE KEYS */;
INSERT INTO `t036_mutasi_stok` VALUES (1,1,1,'masuk',20,25,45,'Penerimaan stok baru dari PT Sumber Elektronik Mandiri PO-2026-001','2026-03-10 09:30:00','2026-10-18 05:32:17','2026-10-18 05:32:17'),(2,4,2,'keluar',12,20,8,'Pengeluaran label shipping untuk pemenuhan pesanan batch pagi','2026-03-12 14:15:00','2026-10-18 05:32:17','2026-10-18 05:32:17');
/*!40000 ALTER TABLE `t036_mutasi_stok` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t036_password_reset_tokens`
--

DROP TABLE IF EXISTS `t036_password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t036_password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036_password_reset_tokens`
--

LOCK TABLES `t036_password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `t036_password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `t036_password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t036_personal_access_tokens`
--

DROP TABLE IF EXISTS `t036_personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t036_personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `t036_personal_access_tokens_token_unique` (`token`),
  KEY `t036_personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036_personal_access_tokens`
--

LOCK TABLES `t036_personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `t036_personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `t036_personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t036_roles`
--

DROP TABLE IF EXISTS `t036_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t036_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `t036_roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036_roles`
--

LOCK TABLES `t036_roles` WRITE;
/*!40000 ALTER TABLE `t036_roles` DISABLE KEYS */;
INSERT INTO `t036_roles` VALUES (1,'Manajer','Hak akses tertinggi: kelola pengguna, barang, supplier, mutasi, laporan, dan audit log.','2026-10-18 05:32:17','2026-10-18 05:32:17'),(2,'Staf Gudang','Operasional gudang: kelola barang, catat mutasi stok masuk dan keluar, serta lihat supplier.','2026-10-18 05:32:17','2026-10-18 05:32:17'),(3,'Auditor','Pemeriksaan sistem: hak akses peninjauan (read-only) untuk seluruh data inventaris dan log aktivitas.','2026-10-18 05:32:17','2026-10-18 05:32:17');
/*!40000 ALTER TABLE `t036_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t036_supplier`
--

DROP TABLE IF EXISTS `t036_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t036_supplier` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_supplier` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_supplier` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kontak` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `t036_supplier_kode_supplier_unique` (`kode_supplier`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036_supplier`
--

LOCK TABLES `t036_supplier` WRITE;
/*!40000 ALTER TABLE `t036_supplier` DISABLE KEYS */;
INSERT INTO `t036_supplier` VALUES (1,'SUP-001','PT Global Logistik Nusantara','Hendra Wijaya','081234567890','info@globallogistik.co.id','Jl. Industri Raya No. 45, Kawasan Industri Jababeka, Cikarang','Aktif','2026-10-18 05:32:17','2026-10-18 05:32:17'),(2,'SUP-002','CV Maju Jaya Abadi','Dewi Lestari','082198765432','sales@majujayaabadi.com','Jl. Rungkut Industri III No. 12, Surabaya','Aktif','2026-10-18 05:32:17','2026-10-18 05:32:17'),(3,'SUP-003','PT Sumber Elektronik Mandiri','Agus Pratama','085712345678','kontak@sumberelektronik.id','Jl. Gajah Mada No. 88, Jakarta Barat','Aktif','2026-10-18 05:32:17','2026-10-18 05:32:17');
/*!40000 ALTER TABLE `t036_supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t036_users`
--

DROP TABLE IF EXISTS `t036_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t036_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `t036_users_username_unique` (`username`),
  UNIQUE KEY `t036_users_email_unique` (`email`),
  KEY `t036_users_role_id_foreign` (`role_id`),
  CONSTRAINT `t036_users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `t036_roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t036_users`
--

LOCK TABLES `t036_users` WRITE;
/*!40000 ALTER TABLE `t036_users` DISABLE KEYS */;
INSERT INTO `t036_users` VALUES (1,1,'25K10036','Leonard Sihol Pardede','leonard.pardede@sigudang.test','$2y$12$0NvZdg4mw9q0zYRc.9Agbu1WOQINQXwtx1oxPKbB/L5t3HYXY2z.2','uploads/profil/leonard-25k10036.png',1,NULL,'2026-10-18 05:32:17','2026-10-18 05:32:17'),(2,2,'staf036','Budi Santoso','staf@sigudang.test','$2y$12$0NvZdg4mw9q0zYRc.9Agbu1WOQINQXwtx1oxPKbB/L5t3HYXY2z.2',NULL,1,NULL,'2026-10-18 05:32:17','2026-10-18 05:32:17'),(3,3,'auditor036','Siti Nurhaliza','auditor@sigudang.test','$2y$12$0NvZdg4mw9q0zYRc.9Agbu1WOQINQXwtx1oxPKbB/L5t3HYXY2z.2',NULL,1,NULL,'2026-10-18 05:32:17','2026-10-18 05:32:17');
/*!40000 ALTER TABLE `t036_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'uts_25K10036'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-18 19:32:31

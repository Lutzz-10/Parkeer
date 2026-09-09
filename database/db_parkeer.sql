-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 09, 2026 at 01:46 AM
-- Server version: 8.4.3
-- PHP Version: 8.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_parkeer`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_area_parkir`
--

CREATE TABLE `tb_area_parkir` (
  `id_area` int NOT NULL,
  `nama_area` varchar(50) NOT NULL,
  `kapasitas` int NOT NULL,
  `terisi` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_area_parkir`
--

INSERT INTO `tb_area_parkir` (`id_area`, `nama_area`, `kapasitas`, `terisi`) VALUES
(1, 'Skansanesia', 112, 0),
(2, 'Masjid Ash Shidiq', 250, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_kendaraan`
--

CREATE TABLE `tb_kendaraan` (
  `id_kendaraan` int NOT NULL,
  `plat_nomor` varchar(15) NOT NULL,
  `jenis_kendaraan` varchar(20) NOT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `pemilik` varchar(100) DEFAULT NULL,
  `id_user` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kendaraan`
--

INSERT INTO `tb_kendaraan` (`id_kendaraan`, `plat_nomor`, `jenis_kendaraan`, `warna`, `pemilik`, `id_user`) VALUES
(1, 'B 1782 AA', 'motor', 'aa', 'mm', 2),
(2, 'B 1121 MA', 'motor', 'Biru', 'Qodir', 2),
(3, 'B 1111 MK', 'motor', 'Biru', 'Indra', 5),
(4, 'R 1211 MJ', 'mobil', 'Jingga', 'egie', 5);

-- --------------------------------------------------------

--
-- Table structure for table `tb_log_aktivitas`
--

CREATE TABLE `tb_log_aktivitas` (
  `id_log` int NOT NULL,
  `id_user` int NOT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `waktu_aktivitas` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_log_aktivitas`
--

INSERT INTO `tb_log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu_aktivitas`) VALUES
(1, 1, 'Login ke sistem', '2026-09-09 06:22:59'),
(2, 1, 'Menambahkan user baru: lutzpi', '2026-09-09 06:25:39'),
(3, 1, 'Menambahkan tarif: motor', '2026-09-09 06:25:52'),
(4, 1, 'Menambahkan area: Skansanesia', '2026-09-09 06:26:05'),
(5, 1, 'Logout dari sistem', '2026-09-09 06:26:13'),
(6, 2, 'Login ke sistem', '2026-09-09 06:32:21'),
(7, 2, 'Mencatat kendaraan masuk (id_parkir: 1)', '2026-09-09 06:35:00'),
(8, 2, 'Memproses kendaraan keluar (id_parkir: 1)', '2026-09-09 06:38:35'),
(9, 2, 'Logout dari sistem', '2026-09-09 06:39:06'),
(10, 1, 'Login ke sistem', '2026-09-09 06:57:54'),
(11, 1, 'Menambahkan tarif: mobil', '2026-09-09 06:58:05'),
(12, 1, 'Menambahkan tarif: lainnya', '2026-09-09 06:58:13'),
(13, 1, 'Logout dari sistem', '2026-09-09 07:04:55'),
(14, 1, 'Login ke sistem', '2026-09-09 07:25:12'),
(15, 1, 'Logout dari sistem', '2026-09-09 07:27:32'),
(16, 1, 'Login ke sistem', '2026-09-09 07:29:07'),
(17, 1, 'Logout dari sistem', '2026-09-09 07:29:17'),
(18, 2, 'Login ke sistem', '2026-09-09 07:29:29'),
(19, 2, 'Logout dari sistem', '2026-09-09 07:29:36'),
(20, 1, 'Login ke sistem', '2026-09-09 07:30:12'),
(21, 1, 'Logout dari sistem', '2026-09-09 07:55:53'),
(22, 3, 'Login ke sistem', '2026-09-09 07:59:13'),
(23, 3, 'Logout dari sistem', '2026-09-09 07:59:30'),
(24, 2, 'Login ke sistem', '2026-09-09 07:59:42'),
(25, 2, 'Mencatat kendaraan masuk (id_parkir: 2)', '2026-09-09 08:00:34'),
(26, 2, 'Logout dari sistem', '2026-09-09 08:01:36'),
(27, 1, 'Login ke sistem', '2026-09-09 08:01:46'),
(28, 1, 'Logout dari sistem', '2026-09-09 08:02:59'),
(29, 1, 'Login ke sistem', '2026-09-09 08:04:07'),
(30, 1, 'Logout dari sistem', '2026-09-09 08:04:14'),
(31, 1, 'Login ke sistem', '2026-09-09 08:06:08'),
(32, 1, 'Menambahkan user baru: lutpi', '2026-09-09 08:06:32'),
(33, 1, 'Menambahkan user baru: qodir', '2026-09-09 08:08:21'),
(34, 1, 'Menambahkan area: Masjid Ash Shidiq', '2026-09-09 08:19:10'),
(35, 1, 'Logout dari sistem', '2026-09-09 08:19:22'),
(36, 5, 'Login ke sistem', '2026-09-09 08:19:26'),
(37, 5, 'Mencatat kendaraan masuk: B 1111 MK (id_parkir: 3)', '2026-09-09 08:21:29'),
(38, 5, 'Memproses kendaraan keluar (id_parkir: 3)', '2026-09-09 08:22:09'),
(39, 5, 'Memproses kendaraan keluar (id_parkir: 2)', '2026-09-09 08:23:37'),
(40, 5, 'Logout dari sistem', '2026-09-09 08:23:49'),
(41, 3, 'Login ke sistem', '2026-09-09 08:23:53'),
(42, 3, 'Logout dari sistem', '2026-09-09 08:27:30'),
(43, 5, 'Login ke sistem', '2026-09-09 08:28:38'),
(44, 5, 'Logout dari sistem', '2026-09-09 08:28:46'),
(45, 5, 'Login ke sistem', '2026-09-09 08:29:21'),
(46, 5, 'Logout dari sistem', '2026-09-09 08:31:10'),
(47, 1, 'Login ke sistem', '2026-09-09 08:31:17'),
(48, 1, 'Logout dari sistem', '2026-09-09 08:31:34'),
(49, 5, 'Login ke sistem', '2026-09-09 08:31:42'),
(50, 5, 'Mencatat kendaraan masuk: R 1211 MJ (id_parkir: 4)', '2026-09-09 08:32:09'),
(51, 5, 'Logout dari sistem', '2026-09-09 08:32:23'),
(52, 1, 'Login ke sistem', '2026-09-09 08:32:41'),
(53, 1, 'Logout dari sistem', '2026-09-09 08:33:01');

-- --------------------------------------------------------

--
-- Table structure for table `tb_tarif`
--

CREATE TABLE `tb_tarif` (
  `id_tarif` int NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya','') NOT NULL,
  `tarif_per_jam` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_tarif`
--

INSERT INTO `tb_tarif` (`id_tarif`, `jenis_kendaraan`, `tarif_per_jam`) VALUES
(1, 'motor', 2000),
(2, 'mobil', 3000),
(3, 'lainnya', 5000);

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_parkir` int NOT NULL,
  `id_kendaraan` int NOT NULL,
  `waktu_masuk` datetime NOT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `id_tarif` int NOT NULL,
  `durasi_jam` int DEFAULT NULL,
  `biaya_total` decimal(10,0) DEFAULT NULL,
  `status` enum('masuk','keluar','') NOT NULL DEFAULT 'masuk',
  `id_user` int NOT NULL,
  `id_area` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_parkir`, `id_kendaraan`, `waktu_masuk`, `waktu_keluar`, `id_tarif`, `durasi_jam`, `biaya_total`, `status`, `id_user`, `id_area`) VALUES
(1, 1, '2026-09-09 06:35:00', '2026-09-09 06:38:35', 1, 1, 2000, 'keluar', 2, 1),
(2, 2, '2026-09-09 08:00:34', '2026-09-09 08:23:37', 1, 1, 2000, 'keluar', 2, 1),
(3, 3, '2026-09-09 08:21:29', '2026-09-09 08:22:09', 1, 1, 2000, 'keluar', 5, 2),
(4, 4, '2026-09-09 08:32:09', NULL, 2, NULL, NULL, 'masuk', 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','petugas','owner','') NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status_aktif`) VALUES
(1, 'Admin', 'admin', '$2y$12$H8zAZa6fhrw8BIShlug6DuucpESJEMJFDd5zX6KGE3fSQTLHMjsaK', 'admin', 1),
(2, 'Petugas', 'petugas', '$2y$12$H8zAZa6fhrw8BIShlug6DuucpESJEMJFDd5zX6KGE3fSQTLHMjsaK', 'petugas', 1),
(3, 'Owner', 'owner', '$2y$12$H8zAZa6fhrw8BIShlug6DuucpESJEMJFDd5zX6KGE3fSQTLHMjsaK', 'owner', 1),
(4, 'lutz', 'lutzpi', '$2y$12$HG1K2s75DmUTM0XvMEZ3ru.n2.iMDKUktKORy3h2aRKLjheCnIK0G', 'petugas', 1),
(5, 'lutz', 'lutpi', '$2y$12$Y1pqtGLI3.0EaLvFRA/inOA1Zpon8L3EsYw6wFr477BhRwXFeI6MC', 'petugas', 1),
(6, 'alqodri', 'qodir', '$2y$12$25WJFpDSQBkEuVBEkAZj.u3QjqqBma34EYR9vZaSpqA2vOutgjS.O', 'petugas', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  ADD PRIMARY KEY (`id_area`);

--
-- Indexes for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD UNIQUE KEY `plat_nomor` (`plat_nomor`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `idx_waktu_aktivitas` (`waktu_aktivitas`);

--
-- Indexes for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  ADD PRIMARY KEY (`id_tarif`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_parkir`),
  ADD KEY `id_kendaraan` (`id_kendaraan`),
  ADD KEY `id_tarif` (`id_tarif`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_area` (`id_area`),
  ADD KEY `idx_status_waktu_masuk` (`status`,`waktu_masuk`),
  ADD KEY `idx_waktu_keluar` (`waktu_keluar`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  MODIFY `id_area` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  MODIFY `id_kendaraan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  MODIFY `id_tarif` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_parkir` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD CONSTRAINT `fk_kendaraan_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `fk_transaksi_area` FOREIGN KEY (`id_area`) REFERENCES `tb_area_parkir` (`id_area`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_transaksi_kendaraan` FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan` (`id_kendaraan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_transaksi_tarif` FOREIGN KEY (`id_tarif`) REFERENCES `tb_tarif` (`id_tarif`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

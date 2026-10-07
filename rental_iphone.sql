-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2026 at 10:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental_iphone`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `username`, `password`) VALUES
(1, 'Ahmad Fauzan', 'ahmad_admin', 'admin123'),
(2, 'Rina Wulandari', 'rina_admin', 'admin456');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `nik_ktp` varchar(16) NOT NULL,
  `no_wa` varchar(15) NOT NULL,
  `alamat` text NOT NULL,
  `link_sosmed` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `nik_ktp`, `no_wa`, `alamat`, `link_sosmed`) VALUES
(1, 'Bagas Prasetyo', '3201021234560001', '6281234567891', 'Jl. Kenanga No. 5, Depok, Jawa Barat', 'instagram.com/bagas_p'),
(2, 'Tiara Agustina', '3201025678900002', '6285678901234', 'Jl. Mawar Raya No. 12, Bekasi, Jawa Barat', 'instagram.com/tiara_agst'),
(3, 'Rafi Akbar', '3201029012340003', '6289012345678', 'Jl. Melati Indah No. 23, Bogor, Jawa Barat', ''),
(4, 'Devina Kusuma', '3201023456780004', '6283456789012', 'Jl. Dahlia No. 8, Tangerang, Banten', 'tiktok.com/@devina_k'),
(5, 'Yusuf Hamdani', '3201027890120005', '6287890123456', 'Jl. Anggrek No. 17, Jakarta Timur', ''),
(6, 'Nadhira Putri', '3201021234560006', '6281234560006', 'Jl. Flamboyan No. 31, Depok, Jawa Barat', 'instagram.com/nadhira_ptr');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_sewa` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `tgl_kembali_aktual` date NOT NULL,
  `kondisi_unit` enum('mulus','lecet ringan','rusak','hilang') NOT NULL,
  `denda_keterlambatan` int(11) NOT NULL DEFAULT 0,
  `denda_kerusakan` int(11) NOT NULL DEFAULT 0,
  `total_denda` int(11) NOT NULL DEFAULT 0,
  `catatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_sewa`, `id_admin`, `tgl_kembali_aktual`, `kondisi_unit`, `denda_keterlambatan`, `denda_kerusakan`, `total_denda`, `catatan`) VALUES
(1, 1, 1, '2026-06-04', 'mulus', 0, 0, 0, 'Pengembalian tepat waktu, kondisi sangat baik.'),
(2, 2, 1, '2026-06-08', 'lecet ringan', 150000, 0, 150000, 'Terlambat 1 hari (Rp 150.000). Terdapat goresan kecil pada body, tidak signifikan.'),
(3, 3, 2, '2026-06-07', 'lecet ringan', 0, 75000, 75000, 'Tepat waktu. Ada lecet ringan di sudut kiri bawah, dikenakan denda kerusakan Rp 75.000.'),
(4, 7, 1, '2026-05-13', 'mulus', 0, 0, 0, 'Pengembalian tepat waktu. Unit bersih dan lengkap.'),
(5, 8, 2, '2026-05-19', 'lecet ringan', 175000, 0, 175000, 'Terlambat 1 hari (Rp 175.000). Kondisi cukup baik.'),
(6, 9, 1, '2026-05-23', 'mulus', 0, 0, 0, 'Pengembalian tepat waktu. Unit dalam kondisi sempurna.');

-- --------------------------------------------------------

--
-- Table structure for table `stok_iphone`
--

CREATE TABLE `stok_iphone` (
  `id_iphone` int(11) NOT NULL,
  `tipe_unit` varchar(50) NOT NULL,
  `no_seri` varchar(50) NOT NULL,
  `warna` varchar(20) NOT NULL,
  `kapasitas_gb` int(11) NOT NULL,
  `harga_sewa_perhari` int(11) NOT NULL,
  `gambar` varchar(100) NOT NULL DEFAULT '',
  `status_ketersediaan` enum('tersedia','disewa','servis') NOT NULL DEFAULT 'tersedia',
  `status` enum('Tersedia','Disewa') DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stok_iphone`
--

INSERT INTO `stok_iphone` (`id_iphone`, `tipe_unit`, `no_seri`, `warna`, `kapasitas_gb`, `harga_sewa_perhari`, `gambar`, `status_ketersediaan`, `status`) VALUES
(1, 'iPhone 11', 'FVHXXXXX01', 'Black', 64, 75000, '', 'tersedia', 'Tersedia'),
(2, 'iPhone 12', 'FVHXXXXX02', 'White', 64, 100000, '', 'tersedia', 'Tersedia'),
(3, 'iPhone 12 Pro', 'FVHXXXXX03', 'Pacific Blue', 128, 150000, '', 'tersedia', 'Tersedia'),
(4, 'iPhone 13', 'FVHXXXXX04', 'Midnight', 128, 130000, '', 'disewa', 'Disewa'),
(5, 'iPhone 13 Mini', 'FVHXXXXX05', 'Starlight', 64, 110000, '', 'disewa', 'Disewa'),
(6, 'iPhone 13 Pro', 'FVHXXXXX06', 'Sierra Blue', 256, 175000, '', 'tersedia', 'Tersedia'),
(7, 'iPhone 13 Pro Max', 'FVHXXXXX07', 'Alpine Green', 256, 200000, '', 'tersedia', 'Tersedia'),
(8, 'iPhone 14', 'FVHXXXXX08', 'Blue', 128, 160000, '', 'disewa', 'Disewa'),
(9, 'iPhone 14 Pro', 'FVHXXXXX09', 'Space Black', 256, 225000, '', 'tersedia', 'Tersedia'),
(10, 'iPhone 14 Pro Max', 'FVHXXXXX10', 'Deep Purple', 256, 275000, '', 'tersedia', 'Tersedia'),
(11, 'iPhone 15', 'FVHXXXXX11', 'Pink', 128, 180000, '', 'servis', 'Tersedia'),
(12, 'iPhone 15 Pro Max', 'FVHXXXXX12', 'Natural Titanium', 512, 400000, '', 'tersedia', 'Tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_sewa`
--

CREATE TABLE `transaksi_sewa` (
  `id_sewa` int(11) NOT NULL,
  `kode_transaksi` varchar(20) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `id_iphone` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `jaminan_identitas` varchar(50) NOT NULL,
  `total_bayar` int(11) NOT NULL,
  `status_transaksi` enum('proses','berjalan','selesai','terlambat') NOT NULL DEFAULT 'berjalan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_sewa`
--

INSERT INTO `transaksi_sewa` (`id_sewa`, `kode_transaksi`, `id_pelanggan`, `id_iphone`, `id_admin`, `tgl_mulai`, `tgl_kembali`, `jaminan_identitas`, `total_bayar`, `status_transaksi`) VALUES
(1, 'TRX-202606-001', 1, 1, 1, '2026-06-01', '2026-06-04', 'KTP Asli', 225000, 'selesai'),
(2, 'TRX-202606-002', 2, 3, 1, '2026-06-03', '2026-06-07', 'KTP & Kartu Pelajar', 600000, 'selesai'),
(3, 'TRX-202606-003', 3, 7, 2, '2026-06-05', '2026-06-07', 'KTP Asli', 400000, 'selesai'),
(4, 'TRX-202606-004', 4, 4, 1, '2026-06-20', '2026-06-23', 'KTP Asli', 390000, 'berjalan'),
(5, 'TRX-202606-005', 5, 5, 2, '2026-06-22', '2026-06-24', 'KTM & KTP', 220000, 'berjalan'),
(6, 'TRX-202606-006', 6, 8, 1, '2026-06-23', '2026-06-27', 'KTP Asli', 640000, 'berjalan'),
(7, 'TRX-202605-007', 1, 2, 1, '2026-05-10', '2026-05-13', 'KTP Asli', 300000, 'selesai'),
(8, 'TRX-202605-008', 2, 6, 2, '2026-05-15', '2026-05-18', 'KTP & SIM A', 525000, 'selesai'),
(9, 'TRX-202605-009', 3, 9, 1, '2026-05-20', '2026-05-23', 'KTP Asli', 675000, 'selesai');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD KEY `id_sewa` (`id_sewa`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `stok_iphone`
--
ALTER TABLE `stok_iphone`
  ADD PRIMARY KEY (`id_iphone`);

--
-- Indexes for table `transaksi_sewa`
--
ALTER TABLE `transaksi_sewa`
  ADD PRIMARY KEY (`id_sewa`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_iphone` (`id_iphone`),
  ADD KEY `id_admin` (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stok_iphone`
--
ALTER TABLE `stok_iphone`
  MODIFY `id_iphone` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transaksi_sewa`
--
ALTER TABLE `transaksi_sewa`
  MODIFY `id_sewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `kembali_fk_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `kembali_fk_sewa` FOREIGN KEY (`id_sewa`) REFERENCES `transaksi_sewa` (`id_sewa`);

--
-- Constraints for table `transaksi_sewa`
--
ALTER TABLE `transaksi_sewa`
  ADD CONSTRAINT `trx_fk_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `trx_fk_iphone` FOREIGN KEY (`id_iphone`) REFERENCES `stok_iphone` (`id_iphone`),
  ADD CONSTRAINT `trx_fk_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

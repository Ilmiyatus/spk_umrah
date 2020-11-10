-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2020 at 02:41 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_biro_umrah`
--

-- --------------------------------------------------------

--
-- Table structure for table `biro_umroh`
--

CREATE TABLE `biro_umroh` (
  `id_biro_umroh` int(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `tanggal_input` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `biro_umroh`
--

INSERT INTO `biro_umroh` (`id_biro_umroh`, `nama`, `alamat`, `tanggal_input`) VALUES
(6, 'Lovina Tour', 'Palembang, Sumatera Selatan', '2020-04-23'),
(7, 'BMP Tour & Travel', 'Palembang, Sumatera Selatan', '2020-04-24'),
(8, 'PT Namira Angkasa Jayatama', 'Palembang, Sumatera Selatan', '2020-04-24'),
(9, 'PT Penjuru Wisata Negeri', 'Palembang, Sumatera Selatan', '2020-04-21'),
(10, 'PT Ar-Raudhah', 'Palembang, Sumatera Selatan', '2020-04-21'),
(11, 'Pakem Travel', 'Palembang, Sumatera Selatan', '0000-00-00'),
(12, 'Fairus Tour & Travel', 'Palembang, Sumatera Selatan', '0000-00-00'),
(13, 'Sriwijaya Mega Wisata', 'Palembang, Sumatera Selatan', '2020-04-29'),
(14, 'PT Muna Bina Insani', 'Palembang, Sumatera Selatan', '0000-00-00'),
(15, 'Al-Ahram Hajj', 'Palembang, Sumatera Selatan', '2020-04-29');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` int(10) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `type` enum('benefit','cost') NOT NULL,
  `bobot` float NOT NULL,
  `ada_pilihan` tinyint(1) DEFAULT NULL,
  `urutan_order` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `nama`, `type`, `bobot`, `ada_pilihan`, `urutan_order`) VALUES
(11, 'Pelayanan', 'benefit', 0.1765, 0, 0),
(12, 'Keamanan', 'benefit', 0.1691, 0, 1),
(13, 'Fasilitas', 'benefit', 0.0732, 0, 2),
(14, 'Eksistensi', 'benefit', 0.1912, 0, 3),
(15, 'Harga yang ditawarkan', 'cost', 0.1174, 0, 4),
(16, 'Paket yang ditawarkan', 'cost', 0.1101, 0, 5),
(17, 'Kepercayaan pelanggan', 'benefit', 0.1617, 0, 6);

-- --------------------------------------------------------

--
-- Table structure for table `nilai_biro_umroh`
--

CREATE TABLE `nilai_biro_umroh` (
  `id_nilai_biro_umroh` int(11) NOT NULL,
  `id_biro_umroh` int(10) NOT NULL,
  `id_kriteria` int(10) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `nilai_biro_umroh`
--

INSERT INTO `nilai_biro_umroh` (`id_nilai_biro_umroh`, `id_biro_umroh`, `id_kriteria`, `nilai`) VALUES
(25, 8, 11, 4),
(26, 8, 12, 4),
(27, 8, 13, 5),
(28, 8, 14, 5),
(30, 6, 11, 5),
(31, 6, 12, 3),
(32, 6, 13, 5),
(33, 6, 14, 2),
(35, 7, 11, 5),
(36, 7, 12, 3),
(37, 7, 13, 5),
(38, 7, 14, 3),
(43, 6, 15, 5),
(48, 7, 15, 4),
(53, 8, 15, 3),
(54, 9, 11, 3),
(55, 9, 12, 3),
(56, 9, 13, 5),
(57, 9, 14, 2),
(58, 9, 15, 2),
(59, 10, 11, 4),
(60, 10, 12, 4),
(61, 10, 13, 3),
(62, 10, 14, 4),
(63, 10, 15, 3),
(69, 6, 16, 4),
(81, 7, 16, 4),
(87, 8, 16, 5),
(93, 9, 16, 2),
(99, 10, 16, 3),
(106, 6, 17, 4),
(113, 7, 17, 5),
(127, 8, 17, 3),
(134, 9, 17, 2),
(141, 10, 17, 4),
(142, 11, 11, 4),
(143, 11, 12, 4),
(144, 11, 13, 4),
(145, 11, 14, 3),
(146, 11, 15, 5),
(147, 11, 16, 2),
(148, 11, 17, 4),
(149, 12, 11, 3),
(150, 12, 12, 3),
(151, 12, 13, 3),
(152, 12, 14, 4),
(153, 12, 15, 4),
(154, 12, 16, 3),
(155, 12, 17, 5),
(156, 13, 11, 2),
(157, 13, 12, 3),
(158, 13, 13, 3),
(159, 13, 14, 3),
(160, 13, 15, 4),
(161, 13, 16, 2),
(162, 13, 17, 4),
(163, 14, 11, 3),
(164, 14, 12, 2),
(165, 14, 13, 2),
(166, 14, 14, 2),
(167, 14, 15, 4),
(168, 14, 16, 3),
(169, 14, 17, 3),
(170, 15, 11, 3),
(171, 15, 12, 2),
(172, 15, 13, 5),
(173, 15, 14, 2),
(174, 15, 15, 2),
(175, 15, 16, 3),
(176, 15, 17, 5);

-- --------------------------------------------------------

--
-- Table structure for table `pilihan_kriteria`
--

CREATE TABLE `pilihan_kriteria` (
  `id_pil_kriteria` int(10) NOT NULL,
  `id_kriteria` int(10) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `nilai` float NOT NULL,
  `urutan_order` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(5) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nama` varchar(70) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `role` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `email`, `alamat`, `role`) VALUES
(1, 'admin', 'admin', 'Okta Chandika Salsabila', 'chandika.salsabila@gmail.com', 'Jl. Kesumba Dalam 16 Jatimulyo Lowokwaru', '1'),
(11, 'pengunjung', 'pengunjung', 'Diah Ayu Ratna', 'diahayukusuma@gmail.com', 'Malang', '2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `biro_umroh`
--
ALTER TABLE `biro_umroh`
  ADD PRIMARY KEY (`id_biro_umroh`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

--
-- Indexes for table `nilai_biro_umroh`
--
ALTER TABLE `nilai_biro_umroh`
  ADD PRIMARY KEY (`id_nilai_biro_umroh`),
  ADD UNIQUE KEY `id_kambing_2` (`id_biro_umroh`,`id_kriteria`),
  ADD KEY `id_kambing` (`id_biro_umroh`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `pilihan_kriteria`
--
ALTER TABLE `pilihan_kriteria`
  ADD PRIMARY KEY (`id_pil_kriteria`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `biro_umroh`
--
ALTER TABLE `biro_umroh`
  MODIFY `id_biro_umroh` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `nilai_biro_umroh`
--
ALTER TABLE `nilai_biro_umroh`
  MODIFY `id_nilai_biro_umroh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=654;

--
-- AUTO_INCREMENT for table `pilihan_kriteria`
--
ALTER TABLE `pilihan_kriteria`
  MODIFY `id_pil_kriteria` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nilai_biro_umroh`
--
ALTER TABLE `nilai_biro_umroh`
  ADD CONSTRAINT `nilai_biro_umroh_ibfk_1` FOREIGN KEY (`id_biro_umroh`) REFERENCES `biro_umroh` (`id_biro_umroh`),
  ADD CONSTRAINT `nilai_biro_umroh_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`);

--
-- Constraints for table `pilihan_kriteria`
--
ALTER TABLE `pilihan_kriteria`
  ADD CONSTRAINT `pilihan_kriteria_ibfk_1` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

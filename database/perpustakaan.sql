-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 09, 2025 at 07:04 AM
-- Server version: 8.0.30
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` int NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `penerbit` varchar(255) NOT NULL,
  `isbn` varchar(255) NOT NULL,
  `edisi` varchar(255) NOT NULL,
  `penulis` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `barcode`, `judul`, `penerbit`, `isbn`, `edisi`, `penulis`) VALUES
(3, '12761728', 'OFF THE RECORD', 'PT. HANINDITA GRAHA WIDYA', '9798849221', '-', 'Suryadi, A.P.'),
(4, '51590093', 'NOWHERE', 'PT. BUKUNE KREATIF CIPTA', '9786022203032', '-', 'Pinkishdelight'),
(5, '67076984', 'HUJAN', 'PT. GRAMEDIA PUSTAKA UTAMA', '9786020324784', '-', 'Tere Liye'),
(6, '91986644', 'MY TWIN\'S SECRET', 'PT. GRAMEDIA WIDIASARANA INDONESIA', '9786024523190', '-', 'Cho Park-Ha'),
(7, '52203883', '70 MIL', 'KATADEPAN', '9786236197493', '-', 'Anastasya'),
(8, '49271328', 'MEME COMIC INDONESIA', 'LOVEABLE', '9786020900209', '-', 'Widya Arifianti'),
(9, '55318076', 'PANGERAN KELAS', 'PT MELVANA MEDIA INDONESIA', '9786026940889', '-', 'Hendra Putra'),
(10, '74548411', 'I WUF U', 'PT BUMI SEMESTA MEDIA', '9786026940667', '-', 'Wulanfadi'),
(11, '77277803', 'CLAUDIA VS NADIA', 'PT MIZAN PUBLIKA', '9786020989648', '-', 'Sarah Ann'),
(12, '47411027', 'HUKUM YANG TERABAIKAN', 'PT KOMPAS MEDIA NUSANTARA', '9786024121389', '-', 'Saldi Isra'),
(13, '88989581', 'MEREKA ADA VOL. 2', 'PT SEMBILAN CAHAYA ABADI', '9786236619230', '-', 'MWV.Mystic'),
(14, '22624700', 'EINSTEIN', 'COCONUT BOOKS', '9786025508653', '-', 'Yourkidlee'),
(15, '82430786', 'RIVER MONSTERS', 'THE ORION PUBLISHING GROUP', '9781409127383', '-', 'Jeremy Wade'),
(16, '35805118', 'BUKTI-BUKTI GUS DUR ITU WALI', 'RENEBOOK', '9786021201039', '-', 'Achmadd Mukafi Niam dan Syaifullah Amin'),
(17, '27268092', 'PESAN DAN TUJUAN KAUM SUFI', 'PUTRA PELAJAR', '-', '-', 'Drs. Firmansyach M. H.'),
(18, '15549627', '33 PESAN NABI', 'PT ZAYTUNA UFUK ABADI', '9786023720934', '-', 'vbi_djenggotten'),
(19, '57613455', 'SAJADAH CINTA MAHIRA', 'DELTA PIJAR KHATULISTIWA', '9786025283550', '-', 'Siswa MAN Sidoarjo'),
(20, '30512874', 'KUMPULAN CERITA RAKYAT NUSANTARA', 'DUA MEDIA', '-', '-', 'Yudhistira Ikranegara'),
(21, '20866212', 'ANAK KOS DODOL', 'GRADIEN MEDIATAMA', '9789791550161', '-', 'Dewi Rieka'),
(22, '19464177', 'SAILOR MOON', 'PT ELEX MEDIA KOMPUTINDO', '9786020291765', '-', 'Naoko Takeuchi'),
(23, '93764109', 'HIGH-RISE INVASION VOL. 5', 'PT GRAMEDIA', '9786024801663', '-', 'Tsuina Miura'),
(24, '91531073', 'KHADIJAH', 'FATHAN PRIMA MEDIA', '9786021683415', '-', 'Sumayya Muhammad'),
(25, '20576529', 'GUK... GUK...', 'ERLANGGA', '9789790757042', '-', 'Rae Sita Patappa'),
(26, '78879965', 'KAMUS ISTILAH KOMENTATOR BOLA', 'OCTOPUS\'S GARDEN PUBLISHING', '9786029896077', '-', 'Muhammad Mice Misrad'),
(27, '77095506', 'KISAH PARA SAHABAT', 'DARUL FAJR LITTURAS', '9789792647495', '-', 'Hamid Ahmad ath-Thahir'),
(28, '68926735', 'AREKSA', 'PT AKAD MEDIA CAKRAWALA', '9786239608088', '-', 'ItaKrn'),
(29, '37895751', 'KUMPULAN 65 DONGENG TELADAN ANAK MUSLIM', 'BINTANG INDONESIA', '9786022189725', '-', 'MB. Rahimsyah, AR'),
(30, '51879919', 'FORMASI KATA KUMPULAN PUISI', 'CV. PUSTAKA MEDIAGURU', '9786232720411', '-', 'Nur Sjamsuarini Pudji Astutik dan Fanesha \'Aizatus Qorik'),
(31, '65831814', 'FIQIH TRADISIONALIS', 'PUSTAKA BAYAN MALANG', '9789793766003', '-', 'KH. Muhyiddin Abdusshomad'),
(32, '78725131', 'MISTERI SHALAT SUBUH', 'AQWAM', '9793653086', '-', 'Dr. Raghib AS-Sirjani'),
(33, '33412903', 'USIR RASA TAKUTMU SEKARANG JUGA!', 'FLASHBOOKS', '9786022556138', '-', 'Herman Susanto'),
(34, '36546579', 'USIR GELISAH DENGAN IBADAH', 'DIVA PRESS', '9786023914630', '-', 'Ustadz Syauqi Abdillah Zein'),
(35, '38443941', 'SAYYIDAH AISYAH', 'PT SERAMBI SEMESTA DISTRIBUSI', '9786025323652', '-', 'Dr. Muhammad Said Ramadhan al-Buthi'),
(36, '21890737', 'API TAUHID', 'REPUBLIKA PENERBIT', '9786028997959', '-', 'Habiburrahman El Shirazy'),
(37, '67952139', 'BELAJAR SENDIRI MICROSOFT ACCESS 2000', 'PENERBIT INDAH SURABAYA', '-', '-', 'Mico Pardosi');

-- --------------------------------------------------------

--
-- Table structure for table `daftarbuku`
--

CREATE TABLE `daftarbuku` (
  `id` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `penerbit` varchar(255) NOT NULL,
  `isbn` varchar(255) NOT NULL,
  `edisi` varchar(255) NOT NULL,
  `penulis` varchar(255) NOT NULL,
  `link_cover` varchar(255) NOT NULL,
  `link_buku` varchar(255) NOT NULL,
  `link_unduh` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `daftarbuku`
--

INSERT INTO `daftarbuku` (`id`, `judul`, `penerbit`, `isbn`, `edisi`, `penulis`, `link_cover`, `link_buku`, `link_unduh`) VALUES
(1, 'Naik-Naik Ke puncak Bukit', 'Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi', '978-602-244-937-9', '1', 'Sarah Fauzia', 'https://static.buku.kemdikbud.go.id/content/image/covernonteks/coverpusbuk/Naik_Naik_Kepuncak_Bukit_Cover.png', 'https://static.buku.kemdikbud.go.id/content/pdf/bukunonteks/pusbuk/Naik_Naik_Kepuncak_Bukit.pdf', 'https://static.buku.kemdikbud.go.id/content/pdf/bukunonteks/pusbuk/Naik_Naik_Kepuncak_Bukit.pdf'),
(3, 'Si Cemong Coak', 'Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi', '978-602-244-922-5', '1', 'Iwok Abqary', 'https://static.buku.kemdikbud.go.id/content/image/covernonteks/coverpusbuk/Si_Cemong_Coak_Cover.png', 'https://static.buku.kemdikbud.go.id/content/pdf/bukunonteks/pusbuk/Si_Cemong_Coak.pdf', 'https://static.buku.kemdikbud.go.id/content/pdf/bukunonteks/pusbuk/Si_Cemong_Coak.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  `created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password_hash`, `role`, `created_at`) VALUES
(89, 'Administrator', 'admin@gmail.com', '$2y$10$8qC0rvSA2VGGF3Z/DOreBu0aTAE5mR4sGJJYngEf1JAg3VhYiIP9y', 'admin', '2025-07-09 06:59:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daftarbuku`
--
ALTER TABLE `daftarbuku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `daftarbuku`
--
ALTER TABLE `daftarbuku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

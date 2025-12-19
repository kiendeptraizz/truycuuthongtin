-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 24, 2025 lúc 07:37 PM
-- Phiên bản máy phục vụ: 8.4.3
-- Phiên bản PHP: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `truycuuthongtin`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test Admin', 'admin@test.com', NULL, '$2y$12$O8HIKHJHyhkJfQR.QL1NNer7xWIrF0r2k6f.DTGktNI.kgvKzZysm', NULL, '2025-08-30 09:40:33', '2025-08-30 09:40:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `collaborators`
--

CREATE TABLE `collaborators` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `collaborator_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã cộng tác viên tự sinh',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên cộng tác viên',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email liên hệ',
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số điện thoại',
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Địa chỉ',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Trạng thái',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú',
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Tỷ lệ hoa hồng (%)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `collaborator_services`
--

CREATE TABLE `collaborator_services` (
  `id` bigint UNSIGNED NOT NULL,
  `collaborator_id` bigint UNSIGNED NOT NULL,
  `service_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên dịch vụ cung cấp',
  `price` decimal(15,2) NOT NULL COMMENT 'Giá dịch vụ',
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'Số lượng',
  `warranty_period` int NOT NULL DEFAULT '0' COMMENT 'Thời gian bảo hành (ngày)',
  `status` enum('active','inactive','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Trạng thái',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả dịch vụ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `collaborator_service_accounts`
--

CREATE TABLE `collaborator_service_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `collaborator_service_id` bigint UNSIGNED NOT NULL,
  `account_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Thông tin tài khoản (username, password, etc.)',
  `provided_date` date NOT NULL COMMENT 'Ngày cung cấp',
  `expiry_date` date DEFAULT NULL COMMENT 'Ngày hết hạn',
  `status` enum('active','expired','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Trạng thái tài khoản',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `content_posts`
--

CREATE TABLE `content_posts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_groups` json NOT NULL,
  `scheduled_at` timestamp NOT NULL,
  `status` enum('scheduled','posted','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notification_sent` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `conversion_logs`
--

CREATE TABLE `conversion_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `campaign_id` bigint UNSIGNED NOT NULL,
  `group_member_id` bigint UNSIGNED NOT NULL,
  `message_log_id` bigint UNSIGNED DEFAULT NULL,
  `own_group_id` bigint UNSIGNED NOT NULL,
  `joined_at` timestamp NOT NULL COMMENT 'Thời gian join nhóm',
  `days_to_convert` int DEFAULT NULL COMMENT 'Số ngày từ lúc gửi tin đến khi join',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú về khách hàng',
  `is_collaborator` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `customer_code`, `name`, `email`, `phone`, `notes`, `is_collaborator`, `created_at`, `updated_at`) VALUES
(78, 'KUN25617', 'Trần Minh Tuân', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(79, 'KUN61651', 'Le Van Vi', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(80, 'KUN46126', 'Kim Ngọc Nam', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(81, 'KUN95166', 'Phuc', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(82, 'KUN53165', 'Hoàng Minh Thái Vũ', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(83, 'KUN92311', 'Quỳnh Như', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(84, 'KUN89966', 'Tuấn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(85, 'KUN03019', 'Haminh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(86, 'KUN27980', 'Lại Hoàng Thế Vũ', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(87, 'KUN33056', 'Trần Nguyễn Minh Thiên', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(88, 'KUN04979', 'Trần Minh Tuân', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(89, 'KUN64601', 'Lâm Phương', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(90, 'KUN03331', 'trang', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(91, 'KUN25052', 'quang anh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(92, 'KUN48575', 'Tư Quý', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(93, 'KUN36340', 'Hiển cẩn thận', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(94, 'KUN93718', 'Minh Khoa', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(95, 'KUN37596', 'Tố Uyên', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(96, 'KUN22750', 'Thùy Trang', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(97, 'KUN09794', 'Harry Phan', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(98, 'KUN61336', 'Bùi Giang', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(99, 'KUN34197', 'Binn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(100, 'KUN60386', 'Tạ Ngọc Hà', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(101, 'KUN14123', 'Quang Phcn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(102, 'KUN79733', 'Việt Phương', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(103, 'KUN73976', 'Mai Hoàng Tú', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(104, 'KUN97722', 'Nguyễn Trọng Vinh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(105, 'KUN23787', 'Dương Văn Tuấn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(106, 'KUN13956', 'Long Pham', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(107, 'KUN16749', 'Quang Thịnh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(108, 'KUN32746', 'Le Hoang Chuan', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(109, 'KUN11401', 'Nguyễn Đức Dũng', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(110, 'KUN78117', 'Trí Lắk', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(111, 'KUN33590', 'Thaibinh Bdg', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(112, 'KUN54688', 'Toán', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(113, 'KUN90221', 'Đức Duy', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(114, 'KUN11394', 'Văn Anh Trần', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(115, 'KUN01445', 'Li Ti4', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(116, 'KUN11911', 'Đạt Gold', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(117, 'KUN19068', 'Đạt', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(118, 'KUN56313', 'Bùi Giang', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(119, 'KUN85970', 'Thu Hiền', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(120, 'KUN21121', 'Tiến Đức', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(121, 'KUN24694', 'Nguyễn Hữu Nuôi', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(129, 'KUN48802', 'Tiến Dũng', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(130, 'KUN64020', 'Thắng Bùi', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(131, 'KUN27989', 'Nguyễn Đức Toàn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(132, 'KUN02858', 'Hưng Ngân', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(133, 'KUN18660', 'Đặng Mạnh Đức', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(134, 'KUN32494', 'Ngoclam', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(135, 'KUN54484', 'Nguyễn Bi', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(136, 'KUN25464', 'Ivy Preparation', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(137, 'KUN68385', 'Xuân Sơn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(138, 'KUN08091', 'Lê Hồng Đức', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(139, 'KUN40433', 'Nguyễn Hồng Diễm', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(140, 'KUN00571', 'Duong Do', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(141, 'KUN62526', 'Ngọc Minh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(142, 'KUN38755', 'Đặng Mai, Đỗ Trung Kiên', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(143, 'KUN49839', 'Tranvanngoc', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(144, 'KUN75039', 'Phạm Hồng Nhung', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(145, 'KUN34686', 'Nguyễn Quang Minh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(146, 'KUN53838', 'Nguyễn Xuân Lợi', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(147, 'KUN21755', 'Vũ Lê', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(148, 'KUN36411', 'Nguyên Phương', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(149, 'KUN90943', 'Nguyen Jack', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(150, 'KUN99122', 'Mai Linh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(151, 'KUN80938', 'Xuân Mãnh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(152, 'KUN93968', 'Crazy Thina', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(153, 'KUN01125', 'Nguyễn Duy Chiến', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(154, 'KUN59567', 'Đinh Trường Lộc', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(155, 'KUN38737', 'Võ Thiện', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(156, 'KUN89413', 'Thịnh Nguyễn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(157, 'KUN13727', 'Vĩnh Qui', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(158, 'KUN98742', 'Bình Nguyễn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(159, 'KUN45158', 'Tường', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(160, 'KUN94807', 'Xuangiang Generalsvhs', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(161, 'KUN72537', 'Phan Duy Linh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(162, 'KUN68314', 'Trịnh Bảo', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(163, 'KUN98263', 'Ly', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(164, 'KUN99551', 'Nguyễn Ngọc Trường', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(165, 'KUN30371', 'Nguyễn Sơn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(166, 'KUN27332', 'Hoàng Nam', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(167, 'KUN40686', 'Bắc', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(168, 'KUN90652', 'Quang Tuan', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(169, 'KUN37908', 'Phương Anh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(170, 'KUN73369', 'Hùng Nguyễn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(171, 'KUN14676', 'Nguyễn Hải', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(172, 'KUN59883', 'Thương', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(173, 'KUN75231', 'Trang Quang Truy', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(174, 'KUN12000', 'Phạm Hồng Chơn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(175, 'KUN02426', 'Thương', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(176, 'KUN12261', 'Tao Hai Anh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(177, 'KUN49913', 'Vũ Đức Hiệu', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(178, 'KUN14908', 'Nguyễn Huy Trung', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(179, 'KUN87720', 'Lê Đoan', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(180, 'KUN41814', 'Đạt Táo', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(181, 'KUN58749', 'Thanh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(182, 'KUN59326', 'thành', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(183, 'KUN78763', 'Quynh Nguyen', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(184, 'KUN34280', 'Giáp', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(185, 'KUN84110', 'Duc Hao', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(186, 'KUN06099', 'Nguyễn Anh Tấn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(187, 'KUN25607', 'Đỗ Cường', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(188, 'KUN68716', 'Huyền Trân', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(189, 'KUN19255', 'Hằng Phan', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(190, 'KUN76900', 'Nguyễn Đức Long', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(191, 'KUN56681', 'In Ấn Hồng Phát', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(192, 'KUN98285', 'Lâm Tùng', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(193, 'KUN33767', 'Nhật', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(194, 'KUN11862', 'Kuaaaa - Nguyen Minh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(195, 'KUN95311', 'Tuấn', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(196, 'KUN06556', 'Huy', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(197, 'KUN16428', 'Trịnh Đăng Khoa', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(198, 'KUN66318', 'Bích Thủy', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(199, 'KUN01568', 'Lê Bình', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(200, 'KUN12005', 'Hưng Lê', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(201, 'KUN34389', 'Hạnh', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(202, 'KUN15405', 'Đinh Huyền Trang', NULL, NULL, NULL, 0, '2025-08-02 19:04:27', '2025-08-02 19:04:27'),
(209, 'KUN39233', 'Huy Nguyễn', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(213, 'KUN83851', 'Mr Thành Cks Và Hddt', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(214, 'KUN38916', 'Hương Ađời', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(215, 'KUN42132', 'Huỳnh Thanh Duy', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(216, 'KUN33903', 'Hoàng Tuấn', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(219, 'KUN31630', 'Trần Nghĩa', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(220, 'KUN83932', 'Vu Hiếu', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(221, 'KUN20317', 'Trúc Vy', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(223, 'KUN19823', 'Ngọc Anh', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(225, 'KUN38105', 'Hoàng Anh', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(241, 'KUN56417', 'Nguyễn Lê Hiếu Nhi', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(242, 'KUN39315', 'Phạm Mạnh Cầm', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(243, 'KUN78692', 'Bá An', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(244, 'KUN31679', 'Tony Nguyen', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(245, 'KUN54101', 'Phạm thế Quang', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(246, 'KUN01433', 'Nguyễn Hoàng Long', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(247, 'KUN51510', 'Đoàn Thanh', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(248, 'KUN50665', 'Nguyencuong', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(249, 'KUN38844', 'đăng đăng', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(250, 'KUN24987', 'Vi Vi', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(251, 'KUN14364', 'Sunny Xuyên', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(252, 'KUN39969', 'Lam Tuan Phu', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(254, 'KUN00682', 'HùNg Anh', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(256, 'KUN18892', 'Nghĩa', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(257, 'KUN58785', 'Thu', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(259, 'KUN69560', 'Lê ĐìNh Vũ', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(260, 'KUN16151', 'PhạM Alaha', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(261, 'KUN62125', 'Vũ Huyền', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(262, 'KUN68554', 'DươNg', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(263, 'KUN28548', 'MộC Lan', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(264, 'KUN80550', 'HiệP NguyễN', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(265, 'KUN54469', 'Kim Huệ', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(266, 'KUN92124', 'Dka', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(267, 'KUN53212', 'Trieu Nam Design', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(268, 'KUN81158', 'Thaiong', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(269, 'KUN42190', 'Ngựa Vằn', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(270, 'KUN84979', 'Nguyễn Nhật Nguyên', NULL, NULL, NULL, 1, '2025-07-21 05:19:36', '2025-09-29 14:31:20'),
(271, 'KUN55958', 'Duy Khánh', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(272, 'KUN48919', 'Trần Tú', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(273, 'KUN14312', 'Thành Phước', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(274, 'KUN66792', 'Tài', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(275, 'KUN45544', 'Phi', NULL, NULL, NULL, 0, '2025-07-21 05:19:36', '2025-07-21 05:19:36'),
(276, 'KUN71920', 'NguyễN ThúY', NULL, NULL, NULL, 0, '2025-07-21 06:34:02', '2025-07-21 06:34:02'),
(277, 'KUN03695', 'Phú Anh Sneaker', NULL, NULL, NULL, 0, '2025-07-21 06:34:02', '2025-07-21 06:34:02'),
(278, 'KUN16670', 'Tạ Lan Hương', NULL, NULL, NULL, 0, '2025-07-21 06:34:02', '2025-07-21 06:34:02'),
(300, 'KUN01389', 'Dương Hòa', NULL, NULL, 'Khách hàng được thêm theo yêu cầu', 0, '2025-07-21 09:50:21', '2025-07-21 09:50:21'),
(301, 'KUN50098', 'Phương', NULL, NULL, 'Khách hàng được thêm theo yêu cầu', 0, '2025-07-21 09:50:21', '2025-07-21 09:50:21'),
(302, 'KUN74612', 'Phaolo Huy', NULL, NULL, 'Khách hàng được thêm theo yêu cầu', 0, '2025-07-21 09:50:21', '2025-07-21 09:50:21'),
(303, 'KUN95076', 'Alex Nguyễn', NULL, NULL, 'Khách hàng được thêm theo yêu cầu', 0, '2025-07-21 09:50:21', '2025-07-21 09:50:21'),
(304, 'KUN12152', 'Tý', NULL, NULL, 'Khách hàng được thêm theo yêu cầu', 0, '2025-07-21 09:50:21', '2025-07-21 09:50:21'),
(305, 'KUN93758', 'Duy', NULL, NULL, NULL, 0, '2025-07-21 09:52:26', '2025-07-21 09:52:26'),
(306, 'KUN74751', 'Duy Mirae', NULL, NULL, NULL, 0, '2025-07-24 11:14:24', '2025-07-24 11:14:24'),
(307, 'KUN71988', 'TrầN TrọNg Trí', NULL, NULL, NULL, 0, '2025-08-03 17:29:04', '2025-08-03 17:29:04'),
(308, 'KUN74383', 'Trang Cao', NULL, NULL, NULL, 0, '2025-08-03 17:59:26', '2025-08-03 17:59:26'),
(309, 'KUN79195', 'Lê Minh Khánh', NULL, NULL, NULL, 0, '2025-08-03 18:09:48', '2025-08-03 18:39:23'),
(313, 'KUN22229', 'PhướC NguyễN', NULL, NULL, NULL, 0, '2025-08-08 15:56:08', '2025-08-08 15:56:08'),
(314, 'KUN96843', 'Nguyễn Hoàng Thanh Sơn', NULL, NULL, NULL, 0, '2025-08-09 18:29:10', '2025-08-09 18:29:10'),
(315, 'KUN15530', 'San San', NULL, NULL, NULL, 0, '2025-08-09 18:30:02', '2025-08-09 18:30:02'),
(316, 'KUN44925', 'Hải', NULL, NULL, NULL, 0, '2025-08-09 18:42:43', '2025-08-09 18:42:43'),
(317, 'KUN78603', 'Tú Hoa', NULL, NULL, NULL, 0, '2025-08-09 18:46:29', '2025-08-09 18:46:29'),
(318, 'KUN39021', 'Hoàng Hợi', NULL, NULL, NULL, 0, '2025-08-09 18:51:30', '2025-08-09 18:51:30'),
(319, 'KUN68927', 'An Yên', NULL, NULL, NULL, 0, '2025-08-09 18:54:14', '2025-08-09 18:54:14'),
(320, 'KUN90056', 'Trí Nguyễn', NULL, NULL, NULL, 0, '2025-08-09 19:14:09', '2025-08-09 19:14:09'),
(321, 'KUN76467', 'ĐặNg Mai', NULL, NULL, NULL, 0, '2025-08-09 19:15:43', '2025-08-09 19:15:43'),
(322, 'KUN38386', 'NgọC', NULL, NULL, NULL, 0, '2025-08-09 19:18:23', '2025-08-09 19:18:23'),
(323, 'KUN25452', 'SơN Ht', NULL, NULL, NULL, 0, '2025-08-09 19:27:33', '2025-08-09 19:27:33'),
(324, 'KUN68740', 'Dư Quang Quý Pcna', NULL, NULL, NULL, 0, '2025-08-09 19:29:07', '2025-08-09 19:29:07'),
(325, 'KUN58032', 'Apostle', NULL, NULL, NULL, 0, '2025-08-09 19:37:16', '2025-08-09 19:37:16'),
(326, 'KUN15217', 'Dung Ly', NULL, NULL, NULL, 0, '2025-08-09 19:42:32', '2025-08-09 19:42:32'),
(327, 'KUN28211', 'Hoa Trong Gio', NULL, NULL, NULL, 0, '2025-08-10 16:45:18', '2025-08-10 16:45:18'),
(329, 'KUN52045', 'Milo', NULL, NULL, NULL, 0, '2025-08-10 16:54:53', '2025-08-10 16:54:53'),
(330, 'KUN37425', 'Nguyen Thuy Anh', NULL, NULL, NULL, 0, '2025-08-10 17:01:16', '2025-08-10 17:01:16'),
(331, 'KUN36514', 'PhạM Thị Minh Lý', NULL, NULL, NULL, 0, '2025-08-10 17:29:04', '2025-08-10 17:29:04'),
(332, 'KUN89677', 'Hoang Thang', NULL, NULL, NULL, 0, '2025-08-10 17:33:11', '2025-08-10 17:33:11'),
(333, 'KUN49631', 'Thanh Nam', NULL, NULL, NULL, 0, '2025-08-10 17:34:23', '2025-08-10 17:34:23'),
(334, 'KUN37413', 'DoãN QuyếT', NULL, NULL, NULL, 0, '2025-08-10 17:39:05', '2025-08-10 17:39:05'),
(335, 'KUN49717', 'Bạch Hổ Dlnn', NULL, NULL, NULL, 0, '2025-08-10 17:40:08', '2025-08-10 17:40:08'),
(336, 'KUN14048', 'Nguyễn Thị Thanh Loan', NULL, NULL, NULL, 0, '2025-08-10 17:43:17', '2025-08-10 17:43:17'),
(337, 'KUN84793', 'LợI', NULL, NULL, NULL, 0, '2025-08-10 17:50:40', '2025-08-10 17:50:40'),
(338, 'KUN79351', 'Huế Nguyễn', NULL, NULL, NULL, 0, '2025-08-10 17:56:40', '2025-08-10 17:56:40'),
(339, 'KUN27829', 'LưU', NULL, NULL, NULL, 0, '2025-08-10 17:58:32', '2025-08-10 17:58:32'),
(340, 'KUN61692', 'Vin', NULL, NULL, NULL, 0, '2025-08-10 17:59:45', '2025-08-10 17:59:45'),
(341, 'KUN39476', 'Chau Nguyen', NULL, NULL, NULL, 0, '2025-08-10 18:04:45', '2025-08-10 18:04:45'),
(342, 'KUN16242', 'Phan TuyêN TuyêN', NULL, NULL, NULL, 0, '2025-08-10 18:07:53', '2025-08-10 18:07:53'),
(343, 'KUN07838', 'DiệU HuyềN', NULL, NULL, NULL, 0, '2025-08-10 18:08:59', '2025-08-10 18:08:59'),
(344, 'KUN47232', 'Nam Du Vt', NULL, NULL, NULL, 0, '2025-08-10 18:10:01', '2025-08-10 18:10:01'),
(345, 'KUN34648', 'Hà Phương', NULL, NULL, NULL, 0, '2025-08-11 14:16:44', '2025-08-11 14:16:44'),
(346, 'KUN35073', 'Vi - Oxalis Adventure', NULL, NULL, NULL, 0, '2025-08-11 16:55:40', '2025-08-11 16:55:40'),
(347, 'KUN36370', 'Vân Hải', NULL, NULL, NULL, 0, '2025-08-12 18:44:53', '2025-08-12 18:44:53'),
(348, 'KUN70644', 'Nguyễn Duy Kiên', NULL, NULL, NULL, 0, '2025-08-12 18:47:12', '2025-08-12 18:47:12'),
(349, 'KUN08784', 'Phúc Nam', NULL, NULL, NULL, 0, '2025-08-13 13:30:41', '2025-08-13 13:30:41'),
(350, 'KUN26522', 'TrầN XuâN ThủY', NULL, NULL, NULL, 0, '2025-08-13 13:35:12', '2025-08-13 13:35:12'),
(351, 'KUN14590', 'Diệu Thu', NULL, NULL, NULL, 0, '2025-08-13 14:07:16', '2025-08-13 14:07:16'),
(353, 'KUN12243', 'Hxthang', NULL, NULL, NULL, 0, '2025-08-14 13:50:06', '2025-08-14 13:50:06'),
(354, 'KUN74963', 'Nguyệt Tròn', NULL, NULL, NULL, 0, '2025-08-14 13:51:03', '2025-11-22 17:56:12'),
(355, 'KUN99275', 'Trinh Sally', NULL, NULL, NULL, 0, '2025-08-14 13:52:00', '2025-08-14 13:52:00'),
(356, 'KUN90429', 'Trung Huỳnh', NULL, NULL, NULL, 0, '2025-08-15 17:56:21', '2025-08-15 17:56:21'),
(357, 'KUN49585', 'Thái Sơn', NULL, NULL, NULL, 0, '2025-08-15 18:00:02', '2025-08-15 18:00:02'),
(358, 'KUN17617', 'NhậT TíNh', NULL, NULL, NULL, 0, '2025-08-15 18:00:37', '2025-08-15 18:00:37'),
(359, 'KUN55803', 'Minh PhụNg', NULL, NULL, NULL, 0, '2025-08-16 16:32:43', '2025-08-16 16:32:43'),
(360, 'KUN30785', 'Chủ gia đình 1', 'hoangthanh13232@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(361, 'KUN99110', 'Thành viên - misuclosetshop', 'misuclosetshop@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(362, 'KUN24832', 'Thành viên - 10423154', '10423154@student.vgu.edu.vn', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(363, 'KUN22541', 'Thành viên - roknghean2', 'roknghean2@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(364, 'KUN41655', 'Thành viên - xuanhaohiu', 'xuanhaohiu@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(365, 'KUN46426', 'Thành viên - sacmaunhoccompany', 'sacmaunhoccompany@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(366, 'KUN78807', 'Chủ gia đình 2', 'kieusinh2297@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(367, 'KUN39913', 'Thành viên - bacto3526', 'bacto3526@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(368, 'KUN39794', 'Thành viên - minhduc12589', 'minhduc12589@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(369, 'KUN23937', 'Thành viên - topjobfpt', 'topjobfpt@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(370, 'KUN88187', 'Thành viên - deliciouscookie20', 'deliciouscookie20@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(371, 'KUN15710', 'Thành viên - thinhnguyenydhp', 'thinhnguyenydhp@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(372, 'KUN39553', 'Chủ gia đình 3', 'macoanh2082@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(373, 'KUN94386', 'Thành viên - nguyenthiminhanh.ltcd24', 'nguyenthiminhanh.ltcd24@sptwnt.edu.vn', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(374, 'KUN68111', 'Thành viên - 64jxbc2c', '64jxbc2c@taikhoanvip.io.vn', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(375, 'KUN59574', 'Thành viên - thieugiadeptrai1994', 'thieugiadeptrai1994@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(376, 'KUN35687', 'Thành viên - dattao11032003', 'dattao11032003@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(377, 'KUN15961', 'Thành viên - sonhuynh23011991', 'sonhuynh23011991@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(378, 'KUN41140', 'Thành viên - levanvi.chatgpt', 'levanvi.chatgpt@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(379, 'KUN01078', 'Thành viên - khoitrandang0312', 'khoitrandang0312@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(380, 'KUN77759', 'Chủ gia đình 4', 'vongthu250468@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(381, 'KUN52999', 'Thành viên - nguyenhai982003', 'nguyenhai982003@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(382, 'KUN35801', 'Thành viên - nguyenlanoanhh', 'nguyenlanoanhh@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(383, 'KUN47521', 'Thành viên - chulchul11028', 'chulchul11028@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(384, 'KUN24732', 'Thành viên - vntraveladvisory', 'vntraveladvisory@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(385, 'KUN49969', 'Thành viên - hainguyenthi2110', 'hainguyenthi2110@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(386, 'KUN19050', 'Thành viên - cuong.vtcdtvt58', 'cuong.vtcdtvt58@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(387, 'KUN50492', 'Chủ gia đình 5', 'phanthien6098375@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(388, 'KUN83965', 'Thành viên - vmphuong1110', 'vmphuong1110@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(389, 'KUN50111', 'Thành viên - thephong.hust', 'thephong.hust@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(390, 'KUN83300', 'Thành viên - dothikimtuyen03081984', 'dothikimtuyen03081984@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(391, 'KUN56128', 'Chủ gia đình 6', 'phantom827@wotomail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(392, 'KUN48928', 'Thành viên - joearmitage', 'joearmitage@gobuybox.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(393, 'KUN39061', 'Thành viên - ducminhhuynh0305', 'ducminhhuynh0305@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(394, 'KUN14989', 'Thành viên - congchuamituot2011', 'congchuamituot2011@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(395, 'KUN85692', 'Thành viên - nxnt2016', 'nxnt2016@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(396, 'KUN78896', 'Thành viên - lnhuuquinh1609', 'lnhuuquinh1609@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(397, 'KUN93023', 'Thành viên - thgo0202', 'thgo0202@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(398, 'KUN24627', 'Chủ gia đình 7', 'hendrikdekker434@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(399, 'KUN90704', 'Thành viên - buixuanhien020244', 'buixuanhien020244@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(400, 'KUN07510', 'Thành viên - huyhuy28052003', 'huyhuy28052003@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(401, 'KUN09323', 'Thành viên - gaschburdab0', 'gaschburdab0@outlook.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(402, 'KUN30171', 'Thành viên - uyengiagoodluck1', 'uyengiagoodluck1@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(403, 'KUN69175', 'Thành viên - ptattmpxd01012004', 'ptattmpxd01012004@gmail.com', '', 'Tự động tạo cho family account', 0, '2025-08-16 10:38:12', '2025-08-16 10:38:12'),
(404, 'KUN51344', 'Phi Bảo', NULL, NULL, NULL, 0, '2025-08-18 18:15:11', '2025-08-18 18:15:11'),
(405, 'KUN08553', 'Phi Bảo', NULL, NULL, NULL, 0, '2025-08-18 18:15:11', '2025-08-18 18:15:11'),
(406, 'KUN08559', 'NguyêN Fashion', NULL, NULL, NULL, 0, '2025-08-18 18:17:15', '2025-08-18 18:17:15'),
(407, 'KUN49189', 'Loan Nguyen', NULL, NULL, NULL, 0, '2025-08-20 16:01:11', '2025-08-20 16:01:11'),
(408, 'KUN43600', 'TùNg LâM', NULL, NULL, NULL, 0, '2025-08-20 16:06:31', '2025-08-20 16:06:31'),
(409, 'KUN90988', 'Thuong Kiet', NULL, NULL, NULL, 0, '2025-08-21 16:40:52', '2025-08-21 16:40:52'),
(410, 'KUN05881', 'Hồng Thắm', NULL, NULL, NULL, 0, '2025-08-21 16:42:40', '2025-08-21 16:42:40'),
(411, 'KUN35698', 'Ngô NhậT Huy', NULL, NULL, NULL, 0, '2025-08-22 17:48:15', '2025-08-22 17:48:15'),
(412, 'KUN65213', 'HồNg Hà', NULL, NULL, NULL, 0, '2025-08-22 17:49:06', '2025-08-22 17:49:06'),
(413, 'KUN66081', 'NhấT NguyễN', NULL, NULL, NULL, 0, '2025-08-24 02:10:50', '2025-08-24 02:10:50'),
(414, 'KUN08728', 'Bi Linh NguyễN', NULL, NULL, NULL, 0, '2025-08-25 17:28:32', '2025-08-25 17:28:32'),
(415, 'KUN33301', 'TàI Lê', NULL, NULL, NULL, 0, '2025-08-28 16:04:12', '2025-08-28 16:04:12'),
(418, 'FAM804600', 'Kiên Đỗ Trung', 'joearmitage@gogbuybox.com', NULL, NULL, 0, '2025-08-28 18:28:35', '2025-08-28 18:28:35'),
(420, 'LOOKUP001', 'Khách hàng test tra cứu', 'test@tracuu.com', '0123456789', NULL, 0, '2025-08-28 18:43:15', '2025-08-28 18:43:15'),
(421, 'KUN76196', 'Kami Core', NULL, NULL, NULL, 0, '2025-08-31 15:57:56', '2025-08-31 15:57:56'),
(422, 'KUN27212', 'Thanh Son', NULL, NULL, NULL, 0, '2025-08-31 16:04:59', '2025-08-31 16:04:59'),
(423, 'CTV11766', 'Trần Văn Mạnh', NULL, NULL, NULL, 1, '2025-08-31 17:40:47', '2025-08-31 17:42:37'),
(424, 'CTV70451', 'Duy Nam', NULL, NULL, NULL, 1, '2025-08-31 17:43:03', '2025-08-31 17:43:03'),
(425, 'CTV47198', 'MạNh TuấN', NULL, NULL, NULL, 1, '2025-08-31 17:45:05', '2025-08-31 17:45:05'),
(426, 'CTV25038', 'Leo Edgar', NULL, NULL, NULL, 1, '2025-08-31 17:45:29', '2025-08-31 17:45:29'),
(429, 'CTV56208', 'BắC', NULL, NULL, NULL, 1, '2025-08-31 17:47:56', '2025-08-31 17:47:56'),
(430, 'CTV15976', 'NguyễN ChíNh', NULL, NULL, NULL, 1, '2025-08-31 17:48:47', '2025-08-31 17:48:47'),
(431, 'CTV74094', 'NgọC HạNh', NULL, NULL, NULL, 1, '2025-08-31 17:49:31', '2025-08-31 17:49:31'),
(432, 'KUN19031', 'Hà Nguyên', NULL, NULL, NULL, 0, '2025-09-01 15:12:19', '2025-09-01 15:12:19'),
(433, 'KUN64137', 'Thongth Petrolimex', NULL, NULL, NULL, 0, '2025-09-03 16:22:14', '2025-09-03 16:22:14'),
(434, 'KUN24324', 'Tuan Nghia', NULL, NULL, NULL, 0, '2025-09-03 16:48:44', '2025-09-03 16:48:44'),
(435, 'KUN81312', 'Toán Math', NULL, NULL, NULL, 0, '2025-09-03 16:52:15', '2025-09-03 16:52:15'),
(436, 'KUN06260', 'hoanglongpro121@gmail.com', NULL, NULL, NULL, 0, '2025-09-03 17:30:42', '2025-09-03 17:30:42'),
(437, 'KUN62121', 'hoanglongpro121@gmail.com', NULL, NULL, NULL, 0, '2025-09-03 17:30:43', '2025-09-03 17:30:43'),
(438, 'KUN16272', 'Hà NguyễN', NULL, NULL, NULL, 0, '2025-09-10 16:48:54', '2025-09-10 16:48:54'),
(439, 'KUN59221', 'VươNg ViệT HoàNg', NULL, NULL, NULL, 0, '2025-09-10 16:55:35', '2025-09-10 16:55:35'),
(440, 'KUN65915', 'Hai Nguyen', NULL, NULL, NULL, 0, '2025-09-10 17:05:58', '2025-09-10 17:05:58'),
(441, 'KUN33628', 'Khuat Thi Duyen', NULL, NULL, NULL, 0, '2025-09-10 17:06:52', '2025-09-10 17:06:52'),
(442, 'KUN16024', 'ToàN NguyễN', NULL, NULL, NULL, 0, '2025-09-10 17:08:59', '2025-09-10 17:08:59'),
(443, 'KUN12833', 'Mỹ Linh', NULL, NULL, NULL, 0, '2025-09-10 17:12:00', '2025-09-10 17:12:00'),
(444, 'KUN79596', 'Nguyễn Tú Anh', NULL, NULL, NULL, 0, '2025-09-10 17:15:34', '2025-09-10 17:15:34'),
(445, 'KUN50915', 'Cuong', NULL, NULL, NULL, 0, '2025-09-10 17:20:00', '2025-09-10 17:20:00'),
(446, 'KUN47222', 'Vuong Anh Tuyet', NULL, NULL, NULL, 0, '2025-09-10 17:27:56', '2025-09-10 17:27:56'),
(447, 'KUN31563', 'Quỳnh Giao Medst Group', NULL, NULL, NULL, 0, '2025-09-10 17:30:18', '2025-09-10 17:30:18'),
(448, 'KUN21663', 'Nguyễn Tú', NULL, NULL, NULL, 0, '2025-09-12 16:36:53', '2025-09-12 16:36:53'),
(449, 'KUN79648', 'Luonghuuminh43@Gmail.Com', NULL, NULL, NULL, 0, '2025-09-12 16:38:02', '2025-09-12 16:38:02'),
(450, 'KUN88284', 'Tài Liệu LP23@gmail.com', NULL, NULL, NULL, 0, '2025-09-12 17:01:27', '2025-09-12 17:01:27'),
(451, 'KUN85001', 'thailien.231197@gmail.com', NULL, NULL, NULL, 0, '2025-09-13 03:03:14', '2025-09-13 03:03:14'),
(452, 'KUN69149', 'Tube238@Gmail.Com', NULL, NULL, NULL, 0, '2025-09-13 03:03:53', '2025-09-13 03:03:53'),
(453, 'KUN28147', 'Phamthiphuonganh9999@gmail.com', NULL, NULL, NULL, 0, '2025-09-17 17:28:49', '2025-09-17 17:28:49'),
(454, 'KUN95732', 'Nguyễn Phương Hà', NULL, NULL, NULL, 0, '2025-09-17 17:33:07', '2025-09-17 17:33:07'),
(455, 'KUN89993', 'VậN ChuyểN QuốC Tế', NULL, NULL, NULL, 0, '2025-09-17 17:33:48', '2025-09-17 17:33:48'),
(456, 'KUN63074', 'miniriviu@gmail.com', NULL, NULL, NULL, 0, '2025-09-17 17:35:10', '2025-09-17 17:35:10'),
(457, 'KUN52845', 'BạCh Thị Thu Hà', NULL, NULL, NULL, 0, '2025-09-17 17:39:09', '2025-09-17 17:39:09'),
(458, 'KUN02194', 'QuáCh Như Loan', NULL, NULL, NULL, 0, '2025-09-17 17:40:07', '2025-09-17 17:40:07'),
(459, 'KUN01758', 'VUHO', NULL, NULL, NULL, 0, '2025-09-17 17:44:32', '2025-09-17 17:44:32'),
(460, 'KUN87728', 'SơN HồNg', NULL, NULL, NULL, 0, '2025-09-17 17:45:17', '2025-09-17 17:45:17'),
(461, 'KUN53162', 'HàNh', NULL, NULL, NULL, 0, '2025-09-17 17:48:07', '2025-09-17 17:48:07'),
(462, 'KUN28207', 'Trung HoàNg', NULL, NULL, NULL, 0, '2025-09-17 17:49:55', '2025-09-17 17:49:55'),
(463, 'KUN19286', 'Kim NgọC', NULL, NULL, NULL, 0, '2025-09-17 17:56:55', '2025-09-17 17:56:55'),
(464, 'KUN99880', 'Cao Quý', NULL, NULL, NULL, 0, '2025-09-17 18:03:07', '2025-09-17 18:03:07'),
(465, 'KUN98662', 'Quyết', NULL, NULL, NULL, 0, '2025-09-18 16:24:32', '2025-09-18 16:24:32'),
(467, 'KUN56788', 'Phuongphuong', NULL, NULL, NULL, 0, '2025-09-18 16:28:29', '2025-09-18 16:28:29'),
(468, 'KUN50925', 'NguyễN Trang', NULL, NULL, NULL, 0, '2025-09-18 16:29:15', '2025-09-18 16:29:15'),
(469, 'KUN64547', 'Minh Lê', NULL, NULL, NULL, 0, '2025-09-21 04:19:11', '2025-09-21 04:19:11'),
(470, 'KUN95111', 'Quang Trung', NULL, NULL, NULL, 0, '2025-09-21 04:20:11', '2025-09-21 04:20:11'),
(471, 'KUN76351', 'Tùng Ken', NULL, NULL, NULL, 0, '2025-09-21 04:21:18', '2025-09-21 04:21:18'),
(472, 'KUN63556', 'TrầN VăN SáNg', NULL, NULL, NULL, 0, '2025-09-21 04:22:34', '2025-09-21 04:22:34'),
(473, 'KUN06974', 'Lê Phúc', NULL, NULL, NULL, 0, '2025-09-22 15:07:10', '2025-09-22 15:07:10'),
(474, 'KUN39371', 'Diamond', NULL, NULL, NULL, 0, '2025-09-22 15:07:58', '2025-09-22 15:07:58'),
(475, 'KUN15091', 'Vinh Thuy', NULL, NULL, NULL, 0, '2025-09-22 15:10:33', '2025-09-22 15:10:33'),
(477, 'KUN72125', 'Nhakhoaphuocnguyen@gmail.com', NULL, NULL, NULL, 0, '2025-09-25 14:15:09', '2025-09-25 14:15:09'),
(478, 'KUN88270', 'Bui Van Nam', NULL, NULL, NULL, 0, '2025-09-25 14:17:00', '2025-09-25 14:17:00'),
(479, 'KUN96842', 'Thương Hoài Lê', NULL, NULL, NULL, 0, '2025-09-25 14:18:47', '2025-09-25 14:18:47'),
(480, 'KUN91174', 'HoàNg Thanh HiệP', NULL, NULL, NULL, 0, '2025-09-25 14:22:11', '2025-09-25 14:22:11'),
(481, 'KUN13287', 'Johntâm - Kent Nguyen International Ltd', NULL, NULL, NULL, 0, '2025-09-25 14:24:16', '2025-09-25 14:24:16'),
(482, 'KUN85869', 'HiêN TrầN', NULL, NULL, NULL, 0, '2025-09-25 14:25:30', '2025-09-25 14:25:30'),
(483, 'KUN79717', 'sighhh1509@gmail.com', NULL, NULL, NULL, 0, '2025-09-25 16:11:01', '2025-09-25 16:11:01'),
(484, 'KUN04536', 'hungpchy.pixie@gmail.com', NULL, NULL, NULL, 0, '2025-09-25 16:23:08', '2025-09-25 16:23:08'),
(485, 'KUN71538', 'NgọC Chi', NULL, NULL, NULL, 0, '2025-09-28 14:33:55', '2025-09-28 14:33:55'),
(486, 'KUN29313', 'Linh Huynh', NULL, NULL, NULL, 0, '2025-09-28 14:38:35', '2025-09-28 14:38:35'),
(487, 'KUN69297', 'Haha', NULL, NULL, NULL, 0, '2025-09-29 14:33:42', '2025-09-29 14:33:42'),
(488, 'KUN44723', 'Do Trang', NULL, NULL, NULL, 0, '2025-09-30 17:04:01', '2025-09-30 17:04:01'),
(489, 'KUN92144', 'PhươNg', NULL, NULL, NULL, 0, '2025-09-30 17:05:11', '2025-09-30 17:05:11'),
(490, 'KUN03356', 'PhạM NgọC HoàNg Tú', NULL, NULL, NULL, 0, '2025-09-30 17:07:55', '2025-09-30 17:07:55'),
(491, 'KUN70691', 'Phạm Ngọc Hoàng Tú', NULL, NULL, NULL, 0, '2025-09-30 17:08:56', '2025-09-30 17:08:56'),
(492, 'KUN81423', 'NguyễN ChiêN', NULL, NULL, NULL, 0, '2025-09-30 17:14:23', '2025-09-30 17:14:23'),
(493, 'KUN44317', 'Duong Pham HL', NULL, NULL, NULL, 0, '2025-10-02 15:47:38', '2025-10-02 15:47:38'),
(494, 'KUN83863', 'PhạM VăN ĐạT', NULL, NULL, NULL, 0, '2025-10-04 16:59:01', '2025-10-04 16:59:01'),
(495, 'KUN88937', 'NguyễN ĐìNh ĐAn Huy', NULL, NULL, NULL, 0, '2025-10-06 17:20:00', '2025-10-06 17:20:00'),
(496, 'KUN27452', 'Châu Oanh', NULL, NULL, NULL, 0, '2025-10-06 17:21:00', '2025-10-06 17:21:00'),
(497, 'KUN67999', 'NguyễN QuốC TriệU', NULL, NULL, NULL, 0, '2025-10-06 17:24:36', '2025-10-06 17:24:36'),
(498, 'KUN58517', 'Ha Giang', NULL, NULL, NULL, 0, '2025-10-08 16:49:07', '2025-10-08 16:49:07'),
(499, 'KUN11383', 'PhạM XuâN ThàNh', NULL, NULL, NULL, 0, '2025-10-09 17:16:36', '2025-10-09 17:16:36'),
(500, 'KUN94542', 'Hoa Trương', NULL, NULL, NULL, 0, '2025-10-09 17:18:47', '2025-10-09 17:18:47'),
(501, 'KUN47795', 'Huỳnh Phú Khánh', NULL, NULL, NULL, 0, '2025-10-10 16:05:13', '2025-10-10 16:05:13'),
(502, 'KUN72516', 'Đặng Quang F͒p͒t͒', NULL, NULL, NULL, 0, '2025-10-10 16:06:18', '2025-10-10 16:06:18'),
(503, 'KUN51986', 'TrọNg ThuậN', NULL, NULL, NULL, 0, '2025-10-10 16:12:42', '2025-10-10 16:12:42'),
(504, 'KUN06838', 'Châu Nguyễn', NULL, NULL, NULL, 0, '2025-10-11 16:54:20', '2025-10-11 16:54:20'),
(505, 'KUN25201', 'Minh Linh', NULL, NULL, NULL, 0, '2025-10-11 16:55:22', '2025-10-11 16:55:22'),
(506, 'KUN44309', 'Nhu Manh Khanh', NULL, NULL, NULL, 0, '2025-10-11 16:56:27', '2025-10-11 16:56:27'),
(507, 'KUN47211', 'TrươNg Trí NhâN', NULL, NULL, NULL, 0, '2025-10-14 16:01:22', '2025-10-14 16:01:22'),
(508, 'KUN50458', 'Anh Zồ', NULL, NULL, NULL, 0, '2025-10-17 06:13:52', '2025-10-17 06:13:52'),
(509, 'KUN52793', 'Huy Cao', NULL, NULL, NULL, 0, '2025-10-17 08:37:27', '2025-10-17 08:37:27'),
(510, 'KUN01488', 'Hoang Luu', NULL, NULL, NULL, 0, '2025-10-17 08:38:25', '2025-10-17 08:38:25'),
(511, 'KUN34057', 'ĐứC PhươNg', NULL, NULL, NULL, 0, '2025-10-18 16:33:15', '2025-10-18 16:33:15'),
(512, 'KUN25961', 'Trần Hoàng Sơn', NULL, NULL, NULL, 0, '2025-10-19 18:36:20', '2025-10-19 18:36:20'),
(513, 'KUN33923', 'Lê HảI', NULL, NULL, NULL, 0, '2025-10-19 18:39:32', '2025-10-19 18:39:32'),
(514, 'KUN93905', 'Minh ỦN ỈN', NULL, NULL, NULL, 0, '2025-10-20 17:05:18', '2025-10-20 17:05:18'),
(516, 'KUN64488', 'Nguyễn Văn Phát Đạt', NULL, NULL, NULL, 0, '2025-10-21 18:06:13', '2025-10-21 18:06:13'),
(517, 'CTV49890', 'HoàNg Thế Anh', NULL, NULL, NULL, 1, '2025-10-21 18:09:58', '2025-10-21 18:09:58'),
(518, 'KUN18150', 'Kim Anh', NULL, NULL, NULL, 0, '2025-10-21 18:12:25', '2025-10-21 18:12:25'),
(519, 'KUN44046', 'NguyễN QuốC Anh', NULL, NULL, NULL, 0, '2025-10-21 18:19:02', '2025-10-21 18:19:02'),
(520, 'KUN46514', 'Nguyệt Anh', NULL, NULL, NULL, 0, '2025-11-02 16:00:27', '2025-11-02 16:00:27'),
(521, 'KUN60617', 'Nam', NULL, NULL, NULL, 0, '2025-11-03 15:08:40', '2025-11-03 15:08:40'),
(522, 'KUN92468', 'thanhbtht@gmail.com', NULL, NULL, NULL, 0, '2025-11-03 15:19:22', '2025-11-03 15:19:22'),
(523, 'KUN32492', 'Nhat Anh Pham', NULL, NULL, NULL, 0, '2025-11-03 15:23:35', '2025-11-03 15:23:35'),
(524, 'KUN46811', 'Duc Duc', NULL, NULL, NULL, 0, '2025-11-06 15:46:01', '2025-11-06 15:46:01'),
(525, 'KUN84920', 'Thu HảO', NULL, NULL, NULL, 0, '2025-11-06 16:20:57', '2025-11-06 16:20:57'),
(526, 'KUN58013', 'Đoàn -xd Văn Hóa -ln Bảo Hà -htx Tcmn', NULL, NULL, NULL, 0, '2025-11-06 16:31:05', '2025-11-06 16:31:05'),
(527, 'CTV54480', 'Le Mai', NULL, NULL, NULL, 1, '2025-11-06 16:33:41', '2025-11-06 16:33:41'),
(529, 'KUN18973', 'Tuệ LộC', NULL, NULL, NULL, 0, '2025-11-06 18:05:49', '2025-11-06 18:05:49'),
(530, 'KUN50901', 'Quang Vinhomes Dương Kinh', NULL, NULL, NULL, 0, '2025-11-08 17:18:34', '2025-11-08 17:18:34'),
(531, 'KUN46707', 'Lê PhươNg', NULL, NULL, NULL, 0, '2025-11-08 17:20:55', '2025-11-08 17:20:55'),
(532, 'KUN82216', 'NguyễN QuâN', NULL, NULL, NULL, 0, '2025-11-08 17:21:45', '2025-11-08 17:21:45'),
(533, 'KUN84672', 'Nguyen Yen Ngoc', NULL, NULL, NULL, 0, '2025-11-08 17:32:07', '2025-11-08 17:32:07'),
(534, 'KUN67987', 'Vũ Phan Digital', NULL, NULL, NULL, 0, '2025-11-08 17:33:02', '2025-11-08 17:33:02'),
(535, 'KUN89597', 'Nangxuan', NULL, NULL, NULL, 0, '2025-11-08 17:36:17', '2025-11-08 17:36:17'),
(536, 'KUN36121', 'Non', NULL, NULL, NULL, 0, '2025-11-08 17:37:58', '2025-11-08 17:37:58'),
(537, 'KUN56217', 'HoàNg Trang', NULL, NULL, NULL, 0, '2025-11-08 17:45:01', '2025-11-08 17:45:01'),
(538, 'KUN19647', 'Nguyễn Dược', NULL, NULL, NULL, 0, '2025-11-08 17:49:39', '2025-11-08 17:49:39'),
(539, 'KUN91239', 'TrầN NguyễN Vinh Quang', NULL, NULL, NULL, 0, '2025-11-08 17:53:23', '2025-11-08 17:53:23'),
(540, 'KUN06947', 'Hoàng Thịnh', NULL, NULL, NULL, 0, '2025-11-08 18:05:07', '2025-11-08 18:05:07'),
(541, 'KUN61178', 'Ledongquang', NULL, NULL, NULL, 0, '2025-11-08 18:06:26', '2025-11-08 18:06:26'),
(542, 'KUN64158', 'Nguyễn Quang Tùng', NULL, NULL, NULL, 0, '2025-11-08 18:09:44', '2025-11-08 18:09:44'),
(543, 'KUN12703', 'Tranh GạO NhậT Huy', NULL, NULL, NULL, 0, '2025-11-09 18:13:51', '2025-11-09 18:13:51'),
(544, 'KUN03860', 'GạCh Men HoàNg TuấN  ThắNg', NULL, NULL, NULL, 0, '2025-11-10 18:03:55', '2025-11-10 18:03:55'),
(545, 'KUN20671', 'HoàNg HuyềN', NULL, NULL, NULL, 0, '2025-11-11 19:02:14', '2025-11-11 19:02:14'),
(546, 'KUN05925', 'Huỳnh Lương', NULL, NULL, NULL, 0, '2025-11-11 19:05:23', '2025-11-11 19:05:23'),
(547, 'KUN68612', 'Ngô Thành Danh', NULL, NULL, NULL, 0, '2025-11-11 19:07:04', '2025-11-11 19:07:04'),
(548, 'KUN87746', 'Chi', NULL, NULL, NULL, 0, '2025-11-15 18:46:59', '2025-11-15 18:46:59'),
(549, 'KUN79343', 'NguyễN ĐứC BìNh', NULL, NULL, NULL, 0, '2025-11-15 18:48:03', '2025-11-15 18:48:03'),
(550, 'KUN71560', 'TrươNg HồNg HạNh', NULL, NULL, NULL, 0, '2025-11-15 18:52:38', '2025-11-15 18:52:38'),
(551, 'KUN06524', 'VâN Anh', NULL, NULL, NULL, 0, '2025-11-16 15:35:13', '2025-11-16 15:35:13'),
(552, 'KUN41799', 'Đoàn Thư Bảo Ngọc', NULL, NULL, NULL, 0, '2025-11-17 18:15:16', '2025-11-17 18:15:16'),
(553, 'KUN34271', 'Minh Nguyệt', NULL, NULL, NULL, 0, '2025-11-17 18:16:18', '2025-11-17 18:16:18'),
(554, 'KUN00743', 'Le Quy Tuyen', NULL, NULL, NULL, 0, '2025-11-17 18:20:39', '2025-11-17 18:20:39'),
(555, 'KUN35421', 'An NguyễN', NULL, NULL, NULL, 0, '2025-11-18 16:40:37', '2025-11-18 16:40:37'),
(556, 'KUN89494', 'ĐứC PhạM Marketiing', NULL, NULL, NULL, 0, '2025-11-18 16:44:36', '2025-11-18 16:44:36'),
(557, 'CTV07742', 'Trà My', NULL, NULL, NULL, 1, '2025-11-18 16:48:09', '2025-11-18 16:48:09'),
(558, 'KUN88958', 'Võ VăN Tý Nhà ĐẹP Lệ ThủY', NULL, NULL, NULL, 0, '2025-11-18 16:51:17', '2025-11-18 16:51:17'),
(559, 'KUN46669', 'HùNg Phan', NULL, NULL, NULL, 0, '2025-11-19 17:34:04', '2025-11-19 17:34:04'),
(560, 'KUN26640', 'NguyễN Thu Trang', NULL, NULL, NULL, 0, '2025-11-22 17:59:19', '2025-11-22 17:59:19'),
(561, 'CTV51977', 'B A O', NULL, NULL, NULL, 1, '2025-11-23 16:09:47', '2025-11-23 16:09:47'),
(562, 'KUN98245', 'Nguyễn Văn Đô', NULL, NULL, NULL, 0, '2025-11-23 16:13:02', '2025-11-23 16:13:02'),
(563, 'KUN07251', 'Phuong Lien', NULL, NULL, NULL, 0, '2025-11-23 16:13:53', '2025-11-23 16:13:53'),
(564, 'KUN18711', 'Thuý Trúc', NULL, NULL, NULL, 0, '2025-11-23 16:37:10', '2025-11-23 16:37:10'),
(565, 'KUN14564', 'Dung Mýt', NULL, NULL, NULL, 0, '2025-11-24 16:13:29', '2025-11-24 16:13:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_services`
--

CREATE TABLE `customer_services` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `service_package_id` bigint UNSIGNED NOT NULL,
  `family_account_id` bigint UNSIGNED DEFAULT NULL,
  `assigned_by` bigint UNSIGNED DEFAULT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_service_id` bigint UNSIGNED DEFAULT NULL,
  `login_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activated_at` timestamp NOT NULL,
  `expires_at` timestamp NOT NULL,
  `status` enum('active','expired','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `duration_days` int DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Đã gửi nhắc nhở',
  `reminder_sent_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian gửi nhắc nhở gần nhất',
  `reminder_count` int NOT NULL DEFAULT '0' COMMENT 'Số lần đã nhắc nhở',
  `reminder_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú về các lần nhắc nhở',
  `two_factor_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã 2FA của tài khoản dùng chung',
  `recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Danh sách mã khôi phục (JSON format)',
  `shared_account_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú riêng cho tài khoản dùng chung',
  `customer_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Hướng dẫn/ghi chú gửi cho khách hàng',
  `password_expires_at` datetime DEFAULT NULL COMMENT 'Ngày hết hạn mật khẩu',
  `two_factor_updated_at` datetime DEFAULT NULL COMMENT 'Ngày cập nhật 2FA gần nhất',
  `is_password_shared` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Có phải mật khẩu được chia sẻ không',
  `shared_with_customers` json DEFAULT NULL COMMENT 'Danh sách khách hàng đã chia sẻ thông tin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customer_services`
--

INSERT INTO `customer_services` (`id`, `customer_id`, `service_package_id`, `family_account_id`, `assigned_by`, `supplier_id`, `supplier_service_id`, `login_email`, `login_password`, `activated_at`, `expires_at`, `status`, `duration_days`, `cost_price`, `price`, `internal_notes`, `created_at`, `updated_at`, `reminder_sent`, `reminder_sent_at`, `reminder_count`, `reminder_notes`, `two_factor_code`, `recovery_codes`, `shared_account_notes`, `customer_instructions`, `password_expires_at`, `two_factor_updated_at`, `is_password_shared`, `shared_with_customers`) VALUES
(12, 88, 6, NULL, 1, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-06-30 17:00:00', '2026-06-30 17:00:00', 'active', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-08-11 07:26:01', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(57, 129, 3, NULL, 1, NULL, NULL, 'captureitraw@moistal.com', '', '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'expired', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-10-29 05:01:37', 1, '2025-07-29 09:35:49', 1, '[29/07/2025 23:35] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(59, 130, 3, NULL, 1, NULL, 18, 'kiendtph491822@gmail.com', '', '2025-06-23 17:00:00', '2026-06-23 17:00:00', 'active', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-08-03 10:12:16', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(60, 131, 34, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-06-29 17:00:00', '2025-08-19 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(64, 133, 28, NULL, 1, NULL, NULL, 'loriingram231@gmail.com', '123456', '2025-08-12 17:00:00', '2025-09-12 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(70, 137, 7, NULL, 1, NULL, NULL, 'sondo3125@gmail.com', '', '2025-07-08 17:00:00', '2026-07-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-08-02 19:30:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(72, 139, 52, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-06 17:00:00', '2026-07-06 17:00:00', 'active', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-11-24 19:16:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(75, 140, 6, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-06-08 17:00:00', '2026-06-08 17:00:00', 'active', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-08-16 16:34:43', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(77, 142, 34, NULL, 1, NULL, 29, 'kiendtph491822@gmail.com', '', '2025-07-08 17:00:00', '2025-08-07 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 1, '2025-08-06 20:02:32', 1, '[07/08/2025 03:02] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(78, 143, 28, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-06-29 17:00:00', '2025-07-29 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 1, '2025-07-29 09:35:22', 1, '[29/07/2025 23:35] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(79, 143, 28, NULL, 1, NULL, NULL, 'alanry292@ggemyu.com', '', '2025-07-08 17:00:00', '2025-08-07 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 1, '2025-08-06 20:02:28', 1, '[07/08/2025 03:02] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(83, 147, 7, NULL, 1, NULL, 27, 'kiendtph491822@gmail.com', '', '2025-06-28 17:00:00', '2026-06-28 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-08-02 19:30:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(86, 149, 34, NULL, 1, NULL, 34, 'kiendtph491822@gmail.com', '', '2025-07-07 17:00:00', '2025-08-06 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(88, 151, 34, NULL, 1, NULL, 35, 'kiendtph491822@gmail.com', '', '2025-07-06 17:00:00', '2025-08-05 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(89, 152, 52, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-04 17:00:00', '2026-07-04 17:00:00', 'active', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-11-24 19:16:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(93, 155, 29, NULL, 1, NULL, 21, 'payne.llife11@outlook.com', '', '2025-07-03 17:00:00', '2025-08-02 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(100, 160, 3, NULL, 1, NULL, 25, 'captureitraw@moistal.com', 'July123', '2025-07-03 17:00:00', '2025-08-02 17:00:00', 'expired', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(101, 161, 29, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-05-18 17:00:00', '2025-07-17 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(103, 163, 28, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-04 17:00:00', '2025-09-03 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(105, 165, 34, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-06-24 17:00:00', '2025-07-24 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 1, '2025-07-24 02:54:36', 1, '[24/07/2025 16:54] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(106, 165, 28, NULL, 1, NULL, 32, 'kiendtph491822@gmail.com', '', '2025-06-24 17:00:00', '2025-07-23 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(108, 167, 34, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-06-25 17:00:00', '2025-07-25 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 1, '2025-07-24 02:54:53', 1, '[24/07/2025 16:54] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(109, 168, 34, NULL, 1, NULL, 22, 'kiendtph491822@gmail.com', '', '2025-07-03 17:00:00', '2025-08-02 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(110, 169, 7, NULL, 1, NULL, 27, 'kiendtph491822@gmail.com', '', '2025-07-03 17:00:00', '2026-07-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-08-02 19:30:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(113, 175, 29, NULL, 1, NULL, 21, 'tuttlemhov@outlook.com', '', '2025-07-02 17:00:00', '2025-08-01 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 1, '2025-07-30 21:34:45', 1, '[31/07/2025 11:34] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(117, 175, 29, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-07-02 17:00:00', '2025-08-01 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 1, '2025-07-30 21:34:48', 1, '[31/07/2025 11:34] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(127, 185, 29, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-05-15 17:00:00', '2025-07-14 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:37', 0, '2025-07-10 10:32:37', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(130, 188, 29, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-05-17 17:00:00', '2025-07-16 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(131, 189, 29, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-05-15 17:00:00', '2025-07-14 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:38', 0, '2025-07-10 10:32:37', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(132, 190, 29, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-05-30 17:00:00', '2025-07-29 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:38', 1, '2025-07-29 09:35:10', 1, '[29/07/2025 23:35] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(134, 192, 34, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-07-09 17:00:00', '2026-07-09 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-08-02 19:30:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(137, 193, 24, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-06-19 17:00:00', '2026-06-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-08-03 17:56:56', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(140, 195, 6, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-06-18 17:00:00', '2026-06-18 17:00:00', 'active', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-09-30 17:07:08', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(143, 112, 29, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-06-18 17:00:00', '2025-07-18 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(145, 85, 28, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-06-19 17:00:00', '2025-07-19 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(146, 160, 28, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', '', '2025-06-22 17:00:00', '2025-07-22 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-07-11 01:37:10', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(149, 147, 30, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-06-13 17:00:00', '2026-06-13 17:00:00', 'active', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-08-03 18:44:36', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(150, 200, 34, NULL, 1, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-06-18 17:00:00', '2026-06-18 17:00:00', 'active', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-07-11 01:37:10', '2025-08-09 18:59:57', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(154, 260, 6, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-21 11:36:32', '2026-07-16 10:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491822@gmail.com', '2025-07-21 11:36:32', '2025-08-02 19:30:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(156, 264, 7, NULL, NULL, NULL, NULL, 'hiepnguyen2797@gmail.com', NULL, '2025-07-21 11:36:33', '2026-07-16 10:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: hiepnguyen2797@gmail.com', '2025-07-21 11:36:33', '2025-11-24 19:29:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(160, 262, 22, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-21 11:36:33', '2025-08-14 10:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491822@gmail.com', '2025-07-21 11:36:33', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(162, 180, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-13 17:00:00', '2025-09-12 17:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491822@gmail.com', '2025-07-21 11:36:33', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(165, 257, 52, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-20 17:00:00', '2026-07-12 17:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491822@gmail.com', '2025-07-21 11:36:33', '2025-11-24 19:16:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(166, 278, 52, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-20 17:00:00', '2026-01-08 17:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491822@gmail.com', '2025-07-21 11:36:33', '2025-08-10 17:58:07', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(167, 248, 52, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-20 17:00:00', '2026-07-10 17:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491822@gmail.com', '2025-07-21 11:36:33', '2025-11-24 19:16:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(168, 248, 49, NULL, NULL, NULL, NULL, 'nguyencuong8886@gmail.com', NULL, '2025-07-20 17:00:00', '2025-08-11 17:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: nguyencuong8886@gmail.com', '2025-07-21 11:36:33', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(172, 244, 5, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-21 11:36:33', '2026-07-11 10:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491822@gmail.com', '2025-07-21 11:36:33', '2025-08-02 19:30:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(173, 305, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-21 11:36:33', '2025-08-09 10:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491822@gmail.com', '2025-07-21 11:36:33', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(176, 105, 52, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-20 17:00:00', '2025-12-08 17:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491822@gmail.com', '2025-07-21 11:36:33', '2025-08-03 17:25:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(180, 275, 16, NULL, NULL, NULL, NULL, 'Tanyaweatherfordpqauw3htm@hotmail.com', NULL, '2025-07-21 11:36:33', '2025-08-18 10:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: Tanyaweatherfordpqauw3htm@hotmail.com', '2025-07-21 11:36:33', '2025-10-29 05:01:38', 1, '2025-08-16 17:44:21', 1, '[17/08/2025 00:44] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(182, 270, 11, NULL, NULL, NULL, NULL, 'tecnopova.heroli8@gmail.com', NULL, '2025-07-21 11:36:33', '2026-07-17 10:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: tecnopova.heroli8@gmail.com', '2025-07-21 11:36:33', '2025-11-24 19:26:32', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(188, 250, 52, NULL, NULL, NULL, NULL, 'kiendtph491828@gmail.com', NULL, '2025-07-20 17:00:00', '2026-01-12 17:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491828@gmail.com', '2025-07-21 11:36:33', '2025-08-11 07:06:24', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(191, 130, 11, NULL, NULL, NULL, NULL, 'wcoblugd3791909@hotmail.com', NULL, '2025-07-21 11:36:33', '2025-08-13 10:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: wcoblugd3791909@hotmail.com', '2025-07-21 11:36:33', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(192, 130, 71, NULL, NULL, NULL, NULL, 'tranngoc478622@gmail.com', NULL, '2025-07-21 11:36:33', '2025-08-16 10:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: tranngoc478622@gmail.com', '2025-07-21 11:36:33', '2025-11-24 19:22:34', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(198, 196, 17, NULL, NULL, NULL, NULL, 'kiendtph491828@gmail.com', NULL, '2025-07-21 11:36:33', '2026-01-11 10:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491828@gmail.com', '2025-07-21 11:36:33', '2025-08-02 19:30:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(201, 200, 34, NULL, NULL, NULL, NULL, 'kiendtph491828@gmail.com', NULL, '2025-07-20 17:00:00', '2026-07-15 17:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491828@gmail.com', '2025-07-21 11:36:33', '2025-08-09 19:00:04', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(206, 161, 3, NULL, NULL, NULL, NULL, 'kiendtph491828@gmail.com', NULL, '2025-07-21 11:36:33', '2025-08-15 10:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491828@gmail.com', '2025-07-21 11:36:33', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(209, 254, 52, NULL, NULL, NULL, NULL, 'hunganh.lalan@gmail.com', NULL, '2025-07-20 17:00:00', '2026-01-07 17:00:00', 'active', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: hunganh.lalan@gmail.com', '2025-07-21 11:36:33', '2025-09-10 17:26:13', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(210, 276, 19, NULL, NULL, NULL, NULL, 'nguyenthuyy376@gmail.com', NULL, '2025-07-21 11:36:33', '2025-08-11 10:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: nguyenthuyy376@gmail.com', '2025-07-21 11:36:33', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(215, 132, 21, NULL, NULL, NULL, NULL, 'kiendtph491828@gmail.com', NULL, '2025-07-21 11:36:33', '2025-10-09 10:00:00', 'expired', NULL, NULL, NULL, 'Khôi phục từ JSON - Login: kiendtph491828@gmail.com', '2025-07-21 11:36:33', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(222, 78, 3, NULL, 1, NULL, NULL, 'shared.test@example.com', 'SharedPassword123', '2025-08-03 09:24:45', '2025-09-02 09:24:45', 'expired', NULL, NULL, NULL, 'Converted from shared to personal account on 2025-08-03 17:12:16', '2025-08-03 09:24:45', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(227, 247, 49, NULL, NULL, NULL, NULL, 'doanthuthanh3979@gmail.com', NULL, '2025-07-27 17:00:00', '2026-01-23 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-03 17:06:27', '2025-11-24 19:03:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(228, 247, 20, NULL, NULL, NULL, NULL, 'luongminhgiang07@gmail.com', NULL, '2025-09-15 17:00:00', '2025-10-15 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-03 17:07:15', '2025-10-29 05:01:38', 1, '2025-10-15 04:16:37', 2, '[10/08/2025 00:49] Đánh dấu từ giao diện web\n[15/10/2025 11:16] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(229, 303, 20, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-31 17:00:00', '2025-08-30 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-03 17:08:49', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(230, 303, 29, NULL, NULL, NULL, NULL, 'alexnguyen.mta.8889@gmail.com', NULL, '2025-08-03 17:00:00', '2026-08-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-03 17:09:51', '2025-08-03 17:09:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(231, 303, 52, NULL, NULL, NULL, NULL, 'alexnguyen.mta.8889@gmail.com', NULL, '2025-07-21 17:00:00', '2026-07-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-03 17:14:19', '2025-11-24 19:16:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(232, 303, 7, 35, NULL, NULL, NULL, 'alexnguyen.mta.8889@gmail.com', NULL, '2025-07-10 17:00:00', '2026-07-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-03 17:17:56', '2025-09-13 03:00:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(233, 105, 29, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-25 17:00:00', '2026-07-25 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-03 17:26:38', '2025-08-03 17:26:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(238, 248, 11, NULL, NULL, NULL, NULL, 'nguyencuong8886@gmail.com', NULL, '2025-07-29 17:00:00', '2025-08-28 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-03 18:03:41', '2025-10-29 05:01:38', 1, '2025-08-28 04:52:56', 1, '[28/08/2025 11:52] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(239, 248, 20, NULL, NULL, NULL, NULL, 'nguyencuong8886@gmail.com', NULL, '2025-07-29 17:00:00', '2025-08-28 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-03 18:04:00', '2025-10-29 05:01:38', 1, '2025-08-28 04:53:00', 1, '[28/08/2025 11:53] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(242, 309, 49, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-03 17:00:00', '2025-09-02 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-03 18:39:39', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(243, 147, 17, NULL, NULL, NULL, NULL, 'vule.tuyengiao@gmail.com', NULL, '2025-07-19 17:00:00', '2025-08-18 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-03 18:41:45', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(244, 303, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-07 17:00:00', '2025-11-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-08 15:47:58', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(247, 148, 7, 33, NULL, NULL, NULL, 'tnphuonga2nd2018@gmail.com', NULL, '2025-08-07 17:00:00', '2026-08-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-08 15:55:26', '2025-11-24 19:29:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(248, 313, 3, NULL, NULL, NULL, NULL, 'nguyenphuoc0927@gmail.com', NULL, '2025-08-07 17:00:00', '2025-11-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-08 15:56:52', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(249, 313, 54, NULL, NULL, NULL, NULL, 'nguyenphuoc0927@gmail.com', NULL, '2025-08-07 17:00:00', '2026-08-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-08 16:39:57', '2025-08-08 16:39:57', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(256, 314, 3, NULL, NULL, NULL, NULL, 'vud544690@gmail.com', NULL, '2025-08-08 17:00:00', '2025-11-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:29:38', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(257, 315, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-07 17:00:00', '2025-11-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:30:36', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(258, 136, 6, NULL, NULL, NULL, NULL, 'der.laurente@gmail.com', NULL, '2025-06-24 17:00:00', '2026-06-24 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:37:22', '2025-08-09 18:37:22', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(259, 136, 31, NULL, NULL, NULL, NULL, 'hlinh.dinh@gmail.com', NULL, '2025-07-19 17:00:00', '2026-07-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:38:37', '2025-08-09 18:38:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(261, 136, 3, NULL, NULL, NULL, NULL, 'trantram1952a@gmail.com', NULL, '2025-08-08 17:00:00', '2025-11-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:40:48', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(262, 316, 52, NULL, NULL, NULL, NULL, 'gpt.impth.49@gmail.com', NULL, '2025-07-25 17:00:00', '2026-01-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:43:19', '2025-08-09 18:43:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(263, 316, 19, NULL, NULL, NULL, NULL, 'impth.49@gmail.com', NULL, '2025-07-19 17:00:00', '2025-08-18 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-09 18:45:09', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(264, 316, 21, NULL, NULL, NULL, NULL, 'gpt.impth.49@gmail.com', NULL, '2025-07-19 17:00:00', '2025-10-17 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-09 18:45:57', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(266, 177, 3, NULL, NULL, NULL, NULL, 'ngo074208@gmail.com', NULL, '2025-08-07 17:00:00', '2025-11-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:49:33', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(267, 159, 3, NULL, NULL, NULL, NULL, 'ziven.design@gmail.com', NULL, '2025-08-07 17:00:00', '2025-11-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:50:52', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(269, 201, 49, NULL, NULL, NULL, NULL, 'hanhmain2345@gmail.com', NULL, '2025-07-23 17:00:00', '2025-08-22 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-09 18:53:46', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(271, 134, 52, NULL, NULL, NULL, NULL, 'ngoclam308@gmail.com', NULL, '2025-08-06 17:00:00', '2026-02-02 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:56:26', '2025-08-09 18:56:26', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(272, 257, 7, 33, NULL, NULL, NULL, 'huethutrieuphu92@gmail.com', NULL, '2025-07-15 17:00:00', '2026-07-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 18:58:55', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(273, 200, 3, NULL, NULL, NULL, NULL, 'Leduyhungpt91@gmail.com', NULL, '2025-08-06 17:00:00', '2025-11-04 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 19:00:49', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(275, 321, 49, NULL, NULL, NULL, NULL, 'drmai290892@gmail.com', NULL, '2025-08-06 17:00:00', '2025-09-05 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-09 19:16:26', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(276, 152, 19, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-06 17:00:00', '2025-09-05 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-09 19:17:45', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(278, 268, 54, NULL, NULL, NULL, NULL, 'onghoangthai0475@gmail.com', NULL, '2025-08-06 17:00:00', '2026-08-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 19:20:05', '2025-08-09 19:20:05', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(279, 183, 49, 30, NULL, NULL, NULL, 'ngocquynh911@gmail.com', NULL, '2025-07-24 17:00:00', '2026-01-20 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 19:23:22', '2025-11-24 19:03:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(280, 183, 54, NULL, NULL, NULL, NULL, 'ngocquynh911@gmail.com', NULL, '2025-08-06 17:00:00', '2026-08-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 19:24:15', '2025-08-09 19:24:15', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(284, 323, 52, NULL, NULL, NULL, NULL, 'kakason3979@gmail.com', NULL, '2025-08-05 17:00:00', '2026-02-01 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 19:28:40', '2025-08-09 19:28:40', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(287, 157, 52, NULL, NULL, NULL, NULL, 'Tranvinhqui9290@gmail.com', NULL, '2025-08-05 17:00:00', '2026-02-01 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 19:35:13', '2025-08-09 19:35:13', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(291, 326, 29, NULL, NULL, NULL, NULL, 'kimdung732024@gmail.com', NULL, '2025-08-04 17:00:00', '2026-08-04 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-09 19:44:09', '2025-08-09 19:44:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(295, 327, 52, NULL, NULL, NULL, NULL, 'lethithao3076@gmail.com', NULL, '2025-08-10 17:00:00', '2026-02-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 16:48:05', '2025-08-10 16:48:05', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(296, 201, 21, NULL, NULL, NULL, NULL, 'tgiap2249@gmail.com', NULL, '2025-08-09 17:00:00', '2025-11-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 16:48:55', '2025-08-10 16:48:55', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(297, 256, 3, NULL, NULL, NULL, NULL, 'hasang1156y@gmail.com', NULL, '2025-08-09 17:00:00', '2025-11-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 16:51:17', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(298, 263, 52, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-29 17:00:00', '2026-02-25 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 16:53:34', '2025-08-10 16:53:34', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(299, 329, 59, NULL, NULL, NULL, NULL, 'wallsfsteirrylfhm@kontotaniej.it.com', NULL, '2025-08-09 17:00:00', '2026-08-09 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 16:55:17', '2025-08-10 16:55:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(300, 105, 1, NULL, NULL, NULL, NULL, 'doanmaitanbinhz@gmail.com', NULL, '2025-08-09 17:00:00', '2025-11-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 16:58:00', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(301, 191, 1, NULL, NULL, NULL, NULL, 'doanmaitanbinhz@gmail.com', NULL, '2025-08-09 17:00:00', '2025-11-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 17:00:58', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(302, 330, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-09 17:00:00', '2025-11-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 17:02:04', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(304, 120, 30, NULL, NULL, NULL, NULL, 'duc319459@gmail.com', NULL, '2025-07-28 17:00:00', '2026-07-28 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 17:25:56', '2025-08-10 17:25:56', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(305, 171, 49, NULL, NULL, NULL, NULL, 'nguyenhai982003@gmail.com', NULL, '2025-08-02 17:00:00', '2025-09-01 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-10 17:27:40', '2025-10-29 05:01:38', 1, '2025-08-31 17:05:52', 1, '[01/09/2025 00:05] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(307, 332, 29, NULL, NULL, NULL, NULL, 't0989690986@gmail.com', NULL, '2025-07-31 17:00:00', '2026-07-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 17:33:39', '2025-08-10 17:33:39', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(308, 333, 7, 32, NULL, NULL, NULL, 'quachanhmaker@gmail.com', NULL, '2025-07-30 17:00:00', '2025-11-28 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 17:35:14', '2025-09-12 17:21:51', 1, '2025-08-28 04:53:08', 1, '[28/08/2025 11:53] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(311, 335, 29, NULL, NULL, NULL, NULL, 'letienphongav@gmail.com', NULL, '2025-07-24 17:00:00', '2026-07-24 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 17:40:40', '2025-08-10 17:40:40', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(315, 199, 28, NULL, NULL, NULL, NULL, 'j7hvf7g55a@capcut3.name.ng', NULL, '2025-09-26 17:00:00', '2025-10-26 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-10 17:45:59', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(321, 92, 49, NULL, NULL, NULL, NULL, 'camhuongOpenAi@gmail.com', NULL, '2025-07-23 17:00:00', '2025-08-22 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-10 17:53:47', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(322, 92, 49, NULL, NULL, NULL, NULL, 'Fors30121990@gmail.com', NULL, '2025-07-23 17:00:00', '2025-08-22 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-10 17:54:04', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(323, 338, 19, NULL, NULL, NULL, NULL, 'nguyenhue.hmu@gmail.com', NULL, '2025-07-21 17:00:00', '2025-08-20 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-10 17:57:00', '2025-10-29 05:01:38', 1, '2025-08-20 15:52:30', 1, '[20/08/2025 22:52] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(326, 197, 28, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-20 17:00:00', '2025-08-19 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-10 18:01:45', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(327, 243, 52, NULL, NULL, NULL, NULL, 'q4g4q5a5@gimeell.com', NULL, '2025-07-11 17:00:00', '2026-01-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 18:04:35', '2025-08-10 18:04:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(329, 341, 28, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-20 17:00:00', '2025-08-19 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-10 18:06:10', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(333, 344, 28, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-07-19 17:00:00', '2025-08-18 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-10 18:10:23', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(335, 154, 7, 35, NULL, NULL, NULL, 'Dinhtruongloc.1996@gmail.com', NULL, '2025-07-14 17:00:00', '2026-07-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 18:14:01', '2025-09-13 03:05:03', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(336, 154, 52, NULL, NULL, NULL, NULL, 'hanoiogbarber@gmail.com', NULL, '2025-07-11 17:00:00', '2026-01-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-10 18:15:07', '2025-08-10 18:15:07', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(337, 250, 1, NULL, NULL, NULL, NULL, 'doanmaitanbinhz@gmail.com', NULL, '2025-08-10 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-11 07:05:43', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(339, 345, 1, NULL, NULL, NULL, NULL, 'doanmaitanbinhz@gmail.com', NULL, '2025-08-10 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-11 14:17:33', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(341, 347, 52, NULL, NULL, NULL, NULL, 'haivantran903@gmail.com', NULL, '2025-08-12 17:00:00', '2026-02-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-12 18:45:19', '2025-08-12 18:45:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(343, 349, 1, NULL, NULL, NULL, NULL, 'hason9600s@gmail.com', NULL, '2025-08-12 17:00:00', '2025-11-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-13 13:31:05', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(344, 350, 28, NULL, NULL, NULL, NULL, 'u6lkx6gd7i@capcut80.name.ng', NULL, '2025-09-12 17:00:00', '2025-10-12 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-13 13:35:28', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(347, 186, 3, NULL, NULL, NULL, NULL, 'nhamthinh7463w@gmail.com', NULL, '2025-08-12 17:00:00', '2025-11-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-13 14:04:34', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(350, 354, 28, NULL, NULL, NULL, NULL, 'norva@shareithub.com', NULL, '2025-08-13 17:00:00', '2025-09-12 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-14 13:51:25', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(351, 355, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-13 17:00:00', '2025-11-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-14 13:52:28', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(354, 348, 58, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-15 17:00:00', '2025-09-14 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-15 17:57:37', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(356, 354, 52, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-15 17:00:00', '2026-02-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-15 17:59:27', '2025-08-15 17:59:27', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(357, 354, 29, NULL, NULL, NULL, NULL, 'vintagevn86@gmail.com', NULL, '2025-08-15 17:00:00', '2026-08-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-15 17:59:48', '2025-08-15 17:59:48', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(358, 357, 21, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-15 17:00:00', '2025-11-13 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-15 18:00:16', '2025-08-15 18:00:16', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(359, 358, 1, NULL, NULL, NULL, NULL, 'hason9600s@gmail.com', NULL, '2025-08-14 17:00:00', '2025-11-12 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-15 18:01:10', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(360, 187, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-14 17:00:00', '2025-11-12 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-15 18:02:08', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(361, 92, 29, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-14 17:00:00', '2026-08-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-15 18:02:52', '2025-08-15 18:02:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(362, 150, 52, NULL, NULL, NULL, NULL, 'mieilinh55@gmail.com', NULL, '2025-08-15 17:00:00', '2026-02-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-16 16:32:27', '2025-08-16 16:32:27', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(363, 359, 52, NULL, NULL, NULL, NULL, 'ksbossvangmir2@gmail.com', NULL, '2025-08-15 17:00:00', '2026-02-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-16 16:33:01', '2025-08-16 16:33:01', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(364, 111, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-15 17:00:00', '2025-11-13 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-16 16:33:45', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(366, 404, 1, NULL, NULL, NULL, NULL, 'hason9600s@gmail.com', NULL, '2025-08-17 17:00:00', '2025-11-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-18 18:15:40', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(367, 404, 29, NULL, NULL, NULL, NULL, 'Baole55442@gmail.com', NULL, '2025-08-17 17:00:00', '2026-08-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-18 18:15:55', '2025-08-18 18:15:55', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(369, 259, 1, NULL, NULL, NULL, NULL, 'hason9600s@gmail.com', NULL, '2025-08-17 17:00:00', '2025-11-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-18 18:18:29', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(370, 356, 7, 33, NULL, NULL, NULL, 'Htrung27791@gmail.com', NULL, '2025-07-22 17:00:00', '2026-07-22 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-19 16:53:29', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(371, 356, 29, NULL, NULL, NULL, NULL, 'Htrung27791@gmail.com', NULL, '2025-08-18 17:00:00', '2026-08-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-19 16:53:39', '2025-08-19 16:53:39', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(372, 354, 29, NULL, NULL, NULL, NULL, 'nguyetanh29s6@gmail.com', NULL, '2025-08-18 17:00:00', '2026-08-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-19 16:54:15', '2025-08-19 16:54:15', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(374, 266, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-18 17:00:00', '2025-11-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-19 16:55:30', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(375, 317, 1, NULL, NULL, NULL, NULL, 'nhamgiang7675q@gmail.com', NULL, '2025-08-18 17:00:00', '2025-11-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-19 16:56:18', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(376, 407, 1, NULL, NULL, NULL, NULL, 'nhamgiang7675q@gmail.com', NULL, '2025-08-19 17:00:00', '2025-11-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-20 16:01:45', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(377, 178, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-19 17:00:00', '2025-11-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-20 16:02:23', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(378, 99, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-19 17:00:00', '2025-11-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-20 16:03:03', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(380, 304, 1, NULL, NULL, NULL, NULL, 'nhamgiang7675q@gmail.com', NULL, '2025-08-19 17:00:00', '2025-11-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-20 16:04:08', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(381, 342, 28, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-20 17:00:00', '2025-10-20 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-20 16:04:42', '2025-10-29 05:01:38', 1, '2025-09-18 08:26:51', 1, '[18/09/2025 15:26] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(382, 268, 52, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-19 17:00:00', '2026-02-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-20 16:05:42', '2025-08-20 16:05:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(385, 408, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-19 17:00:00', '2025-11-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-20 16:06:48', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(387, 102, 7, 36, NULL, NULL, NULL, 'chungvietphuong2311@gmail.com', NULL, '2025-08-20 17:00:00', '2026-08-20 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-21 16:40:37', '2025-09-13 03:07:05', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(389, 186, 31, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-20 17:00:00', '2026-08-20 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-21 16:41:50', '2025-08-21 16:41:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(390, 410, 52, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-20 17:00:00', '2026-02-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-21 16:42:52', '2025-08-21 16:42:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(391, 319, 29, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-19 17:00:00', '2026-08-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-21 16:43:55', '2025-08-21 16:43:55', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(392, 324, 7, 36, NULL, NULL, NULL, 'Quypcna@gmail.com', NULL, '2025-08-22 17:00:00', '2026-08-22 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-22 17:48:05', '2025-09-13 03:06:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(393, 411, 21, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-21 17:00:00', '2025-11-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-22 17:48:30', '2025-08-22 17:48:30', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(394, 411, 19, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-21 17:00:00', '2025-09-20 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-22 17:48:48', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(395, 412, 3, NULL, NULL, NULL, NULL, 'hhg121105@gmail.com', NULL, '2025-08-21 17:00:00', '2025-11-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-22 17:49:26', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(397, 154, 62, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-22 17:00:00', '2025-09-21 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-24 02:02:31', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(398, 413, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-21 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-24 02:11:13', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(399, 179, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-23 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-24 02:12:09', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(401, 103, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-23 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-24 02:13:10', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(403, 109, 1, NULL, NULL, NULL, NULL, 'ladung9877o@gmail.com', NULL, '2025-08-24 17:00:00', '2025-11-22 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-25 17:28:20', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(404, 414, 1, NULL, NULL, NULL, NULL, 'ladung9877o@gmail.com', NULL, '2025-08-24 17:00:00', '2025-11-22 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-25 17:28:52', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(405, 178, 28, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-24 17:00:00', '2025-09-23 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-25 17:29:26', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(406, 356, 52, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-24 17:00:00', '2026-02-20 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-25 17:30:43', '2025-08-25 17:30:43', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(407, 93, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-24 17:00:00', '2025-11-22 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-25 17:31:16', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(408, 100, 61, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-24 17:00:00', '2025-09-23 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-25 17:32:21', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(409, 113, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-27 17:00:00', '2025-11-25 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-28 15:55:11', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(410, 140, 29, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-25 17:00:00', '2026-08-25 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-28 15:56:01', '2025-08-28 15:56:01', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(411, 140, 7, 32, NULL, NULL, NULL, 'duongdtvn2@gmail.com', NULL, '2025-08-25 17:00:00', '2026-08-25 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-28 15:56:17', '2025-11-24 19:29:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(413, 308, 3, NULL, NULL, NULL, NULL, 'trieuhaphuoclong@gmail.com', NULL, '2025-08-25 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-28 16:04:02', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(415, 92, 63, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-27 17:00:00', '2026-08-27 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-28 16:08:22', '2025-08-28 16:08:22', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(417, 326, 1, NULL, NULL, NULL, NULL, 'ladung9877o@gmail.com', NULL, '2025-08-26 17:00:00', '2025-11-24 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-28 16:11:02', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(419, 361, 61, NULL, 1, NULL, NULL, 'misuclosetshop@gmail.com', NULL, '2025-07-28 17:00:00', '2025-08-27 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 1 - Đỗ Trung Kiên (ID: 22). Thành viên Family Member ID: 5', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL);
INSERT INTO `customer_services` (`id`, `customer_id`, `service_package_id`, `family_account_id`, `assigned_by`, `supplier_id`, `supplier_service_id`, `login_email`, `login_password`, `activated_at`, `expires_at`, `status`, `duration_days`, `cost_price`, `price`, `internal_notes`, `created_at`, `updated_at`, `reminder_sent`, `reminder_sent_at`, `reminder_count`, `reminder_notes`, `two_factor_code`, `recovery_codes`, `shared_account_notes`, `customer_instructions`, `password_expires_at`, `two_factor_updated_at`, `is_password_shared`, `shared_with_customers`) VALUES
(420, 362, 61, NULL, 1, NULL, NULL, '10423154@student.vgu.edu.vn', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 1 - Đỗ Trung Kiên (ID: 22). Thành viên Family Member ID: 6', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(421, 363, 61, NULL, 1, NULL, NULL, 'roknghean2@gmail.com', NULL, '2025-08-03 17:00:00', '2025-09-02 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 1 - Đỗ Trung Kiên (ID: 22). Thành viên Family Member ID: 7', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(422, 364, 61, NULL, 1, NULL, NULL, 'xuanhaohiu@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 1 - Đỗ Trung Kiên (ID: 22). Thành viên Family Member ID: 8', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(423, 365, 61, NULL, 1, NULL, NULL, 'sacmaunhoccompany@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 1 - Đỗ Trung Kiên (ID: 22). Thành viên Family Member ID: 9', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(424, 367, 61, NULL, 1, NULL, NULL, 'bacto3526@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 2 - Đỗ Trung Kiên (ID: 23). Thành viên Family Member ID: 10', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(425, 368, 61, NULL, 1, NULL, NULL, 'minhduc12589@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 2 - Đỗ Trung Kiên (ID: 23). Thành viên Family Member ID: 11', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(426, 369, 61, NULL, 1, NULL, NULL, 'topjobfpt@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 2 - Đỗ Trung Kiên (ID: 23). Thành viên Family Member ID: 12', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(427, 370, 61, NULL, 1, NULL, NULL, 'deliciouscookie20@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 2 - Đỗ Trung Kiên (ID: 23). Thành viên Family Member ID: 13', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(428, 371, 61, NULL, 1, NULL, NULL, 'thinhnguyenydhp@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 2 - Đỗ Trung Kiên (ID: 23). Thành viên Family Member ID: 14', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(429, 373, 61, NULL, 1, NULL, NULL, 'nguyenthiminhanh.ltcd24@sptwnt.edu.vn', NULL, '2025-08-05 17:00:00', '2025-09-04 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 3 - Đỗ Trung Kiên (ID: 24). Thành viên Family Member ID: 15', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(430, 374, 61, NULL, 1, NULL, NULL, '64jxbc2c@taikhoanvip.io.vn', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 3 - Đỗ Trung Kiên (ID: 24). Thành viên Family Member ID: 16', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(431, 375, 61, NULL, 1, NULL, NULL, 'thieugiadeptrai1994@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 3 - Đỗ Trung Kiên (ID: 24). Thành viên Family Member ID: 17', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(432, 376, 61, NULL, 1, NULL, NULL, 'dattao11032003@gmail.com', NULL, '2025-08-01 17:00:00', '2025-08-31 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 3 - Đỗ Trung Kiên (ID: 24). Thành viên Family Member ID: 18', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 1, '2025-08-30 18:04:45', 1, '[31/08/2025 01:04] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(433, 377, 61, NULL, 1, NULL, NULL, 'sonhuynh23011991@gmail.com', NULL, '2025-08-04 17:00:00', '2025-09-03 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 3 - Đỗ Trung Kiên (ID: 24). Thành viên Family Member ID: 19', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(434, 378, 61, NULL, 1, NULL, NULL, 'levanvi.chatgpt@gmail.com', NULL, '2025-08-01 17:00:00', '2025-08-31 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 3 - Đỗ Trung Kiên (ID: 24). Thành viên Family Member ID: 20', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 1, '2025-08-30 18:04:56', 1, '[31/08/2025 01:04] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(435, 379, 61, NULL, 1, NULL, NULL, 'khoitrandang0312@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 3 - Đỗ Trung Kiên (ID: 24). Thành viên Family Member ID: 21', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(436, 381, 61, NULL, 1, NULL, NULL, 'nguyenhai982003@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 4 - Đỗ Trung Kiên (ID: 25). Thành viên Family Member ID: 23', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(437, 382, 61, NULL, 1, NULL, NULL, 'nguyenlanoanhh@gmail.com', NULL, '2025-08-03 17:00:00', '2025-09-02 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 4 - Đỗ Trung Kiên (ID: 25). Thành viên Family Member ID: 24', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(438, 383, 61, NULL, 1, NULL, NULL, 'chulchul11028@gmail.com', NULL, '2025-08-02 17:00:00', '2025-09-01 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 4 - Đỗ Trung Kiên (ID: 25). Thành viên Family Member ID: 25', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 1, '2025-08-31 17:05:45', 1, '[01/09/2025 00:05] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(439, 384, 61, NULL, 1, NULL, NULL, 'vntraveladvisory@gmail.com', NULL, '2025-08-01 17:00:00', '2025-08-31 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 4 - Đỗ Trung Kiên (ID: 25). Thành viên Family Member ID: 26', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 1, '2025-08-30 18:04:40', 1, '[31/08/2025 01:04] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(440, 385, 61, NULL, 1, NULL, NULL, 'hainguyenthi2110@gmail.com', NULL, '2025-08-03 17:00:00', '2025-09-02 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 4 - Đỗ Trung Kiên (ID: 25). Thành viên Family Member ID: 27', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(441, 386, 61, NULL, 1, NULL, NULL, 'cuong.vtcdtvt58@gmail.com', NULL, '2025-08-02 17:00:00', '2025-09-01 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 4 - Đỗ Trung Kiên (ID: 25). Thành viên Family Member ID: 28', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 1, '2025-08-31 17:05:39', 1, '[01/09/2025 00:05] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(442, 388, 61, NULL, 1, NULL, NULL, 'vmphuong1110@gmail.com', NULL, '2025-08-11 17:00:00', '2025-09-10 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 5 - Đỗ Trung Kiên (ID: 26). Thành viên Family Member ID: 29', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(443, 389, 61, NULL, 1, NULL, NULL, 'thephong.hust@gmail.com', NULL, '2025-08-13 17:00:00', '2025-09-12 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 5 - Đỗ Trung Kiên (ID: 26). Thành viên Family Member ID: 30', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(444, 390, 61, NULL, 1, NULL, NULL, 'dothikimtuyen03081984@gmail.com', NULL, '2025-08-14 17:00:00', '2025-09-13 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 5 - Đỗ Trung Kiên (ID: 26). Thành viên Family Member ID: 31', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(445, 392, 61, NULL, 1, NULL, NULL, 'joearmitage@gobuybox.com', NULL, '2025-08-05 17:00:00', '2025-09-04 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 6 - Đỗ Trung Kiên (ID: 27). Thành viên Family Member ID: 32', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(446, 393, 61, NULL, 1, NULL, NULL, 'ducminhhuynh0305@gmail.com', NULL, '2025-08-05 17:00:00', '2025-09-04 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 6 - Đỗ Trung Kiên (ID: 27). Thành viên Family Member ID: 33', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(447, 394, 61, NULL, 1, NULL, NULL, 'congchuamituot2011@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 6 - Đỗ Trung Kiên (ID: 27). Thành viên Family Member ID: 34', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(448, 395, 61, NULL, 1, NULL, NULL, 'nxnt2016@gmail.com', NULL, '2025-08-12 17:00:00', '2025-09-11 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 6 - Đỗ Trung Kiên (ID: 27). Thành viên Family Member ID: 35', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(449, 396, 61, NULL, 1, NULL, NULL, 'lnhuuquinh1609@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 6 - Đỗ Trung Kiên (ID: 27). Thành viên Family Member ID: 36', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(450, 397, 61, NULL, 1, NULL, NULL, 'thgo0202@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 6 - Đỗ Trung Kiên (ID: 27). Thành viên Family Member ID: 37', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(451, 399, 61, NULL, 1, NULL, NULL, 'buixuanhien020244@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 7 - Đỗ Trung Kiên (ID: 28). Thành viên Family Member ID: 38', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(452, 400, 61, NULL, 1, NULL, NULL, 'huyhuy28052003@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 7 - Đỗ Trung Kiên (ID: 28). Thành viên Family Member ID: 40', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(453, 401, 61, NULL, 1, NULL, NULL, 'gaschburdab0@outlook.com', NULL, '2025-08-04 17:00:00', '2025-09-03 17:00:00', 'expired', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 7 - Đỗ Trung Kiên (ID: 28). Thành viên Family Member ID: 41', '2025-08-30 11:34:51', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(454, 402, 61, NULL, 1, NULL, NULL, 'uyengiagoodluck1@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 7 - Đỗ Trung Kiên (ID: 28). Thành viên Family Member ID: 42', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(455, 403, 61, NULL, 1, NULL, NULL, 'ptattmpxd01012004@gmail.com', NULL, '2025-06-30 17:00:00', '2025-07-30 17:00:00', 'cancelled', NULL, NULL, NULL, 'Dịch vụ được tạo tự động từ Family Account: Nhóm 7 - Đỗ Trung Kiên (ID: 28). Thành viên Family Member ID: 43', '2025-08-30 11:34:51', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(456, 334, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-29 17:00:00', '2025-11-27 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-30 17:56:31', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(458, 265, 1, NULL, NULL, NULL, NULL, 'ladung9877o@gmail.com', NULL, '2025-08-28 17:00:00', '2025-11-26 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-30 17:59:20', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(459, 273, 28, NULL, NULL, NULL, NULL, 'fa3vfnc2wv@kindleeo5.name.ng', NULL, '2025-09-26 17:00:00', '2025-10-26 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-30 18:00:04', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(460, 156, 28, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-29 17:00:00', '2025-09-28 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-08-30 18:00:45', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(464, 342, 29, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-30 17:00:00', '2026-08-30 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-31 16:04:45', '2025-08-31 16:04:45', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(465, 422, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-30 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-08-31 16:05:21', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(477, 422, 28, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-08-31 17:00:00', '2025-09-30 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-01 15:12:01', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(478, 432, 67, NULL, NULL, NULL, NULL, 'hathuynguyen69@gmail.com', NULL, '2025-08-31 17:00:00', '2026-08-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-01 15:14:19', '2025-09-01 15:14:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(479, 132, 71, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', 'trungkienchatgptplus1', '2025-08-31 17:00:00', '2026-11-24 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-01 15:15:08', '2025-11-24 19:22:34', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(480, 425, 49, 30, NULL, NULL, NULL, 'Tungmozu@gmail.com', NULL, '2025-08-30 17:00:00', '2025-12-20 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-01 15:25:55', '2025-11-21 17:10:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(482, 132, 7, 32, NULL, NULL, NULL, 'quachanhmaker@gmail.com', NULL, '2025-09-02 17:00:00', '2026-09-02 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-03 16:19:47', '2025-09-12 17:21:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(483, 429, 49, 30, NULL, NULL, NULL, 'Tranthiminhanh90@gmail.com', NULL, '2025-09-02 17:00:00', '2025-12-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-03 16:20:41', '2025-11-09 18:17:21', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(485, 433, 7, 32, NULL, NULL, NULL, 'quachanhmaker@gmail.com', NULL, '2025-09-02 17:00:00', '2026-09-02 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-03 16:46:37', '2025-09-12 17:21:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(487, 434, 69, NULL, NULL, NULL, NULL, 'justindam777@gmail.com', NULL, '2025-09-03 17:00:00', '2026-09-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-03 16:50:09', '2025-09-03 16:50:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(489, 435, 67, NULL, NULL, NULL, NULL, 'liennhohpu2@gmail.com', NULL, '2025-09-02 17:00:00', '2026-09-02 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-03 16:53:23', '2025-09-03 16:53:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(490, 435, 7, 33, NULL, NULL, NULL, 'nguyenthilien25091995@gmail.com', NULL, '2025-07-25 17:00:00', '2026-07-25 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-03 16:56:27', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(491, 325, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-02 17:00:00', '2025-12-01 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-03 16:57:18', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(493, 425, 49, 30, NULL, NULL, NULL, 'lllethang4002@gmail.com', NULL, '2025-09-02 17:00:00', '2025-10-02 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-03 17:00:32', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(494, 437, 7, 33, NULL, NULL, NULL, 'hoanglongpro121@gmail.com', NULL, '2025-07-19 17:00:00', '2026-07-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-03 17:31:12', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(497, 213, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-09 17:00:00', '2025-12-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 16:48:37', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(498, 438, 1, NULL, NULL, NULL, NULL, 'kieudinhkhanh4@gmail.com', NULL, '2025-09-09 17:00:00', '2025-12-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 16:49:35', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(499, 268, 70, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-08 17:00:00', '2026-09-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 16:52:19', '2025-09-10 16:52:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(501, 94, 1, NULL, NULL, NULL, NULL, 'kieudinhkhanh4@gmail.com', NULL, '2025-09-09 17:00:00', '2025-12-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 16:54:28', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(502, 153, 1, NULL, NULL, NULL, NULL, 'buiphus1641@gmail.com', NULL, '2025-09-04 17:00:00', '2025-12-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 16:55:22', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(505, 440, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-08 17:00:00', '2025-12-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:06:39', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(506, 441, 21, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-07 17:00:00', '2025-12-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:07:47', '2025-09-10 17:07:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(507, 87, 1, NULL, NULL, NULL, NULL, 'phamthaogovap@gmail.com', NULL, '2025-09-10 17:00:00', '2025-12-09 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:08:37', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(509, 443, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-05 17:00:00', '2025-12-04 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:13:02', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(510, 196, 5, NULL, NULL, NULL, NULL, 'huylecsc@gmail.com', NULL, '2025-09-04 17:00:00', '2025-10-04 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-10 17:14:09', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(511, 444, 72, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-07 17:00:00', '2025-10-07 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-10 17:16:09', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(512, 81, 1, NULL, NULL, NULL, NULL, 'buiphus1641@gmail.com', NULL, '2025-09-07 17:00:00', '2025-12-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:17:32', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(513, 347, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-06 17:00:00', '2025-12-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:18:21', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(514, 135, 1, NULL, NULL, NULL, NULL, 'buiphus1641@gmail.com', NULL, '2025-09-04 17:00:00', '2025-12-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:19:45', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(515, 445, 6, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-05 17:00:00', '2026-09-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:20:36', '2025-09-10 17:20:36', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(516, 251, 1, NULL, NULL, NULL, NULL, 'phamthaogovap@gmail.com', NULL, '2025-09-04 17:00:00', '2025-12-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:21:34', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(518, 322, 1, NULL, NULL, NULL, NULL, 'phamthaogovap@gmail.com', NULL, '2025-09-04 17:00:00', '2025-12-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:25:12', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(519, 254, 20, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-04 17:00:00', '2025-10-04 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-10 17:26:43', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(520, 101, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-04 17:00:00', '2025-12-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:27:35', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(521, 446, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-04 17:00:00', '2025-12-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:28:24', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(523, 447, 19, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-02 17:00:00', '2025-10-02 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-10 17:31:11', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(524, 214, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-03 17:00:00', '2025-12-02 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:32:05', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(526, 180, 3, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-02 17:00:00', '2025-12-01 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-10 17:34:43', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(527, 351, 30, NULL, NULL, NULL, NULL, 'kiendtph491822@gmail.com', NULL, '2025-09-07 17:00:00', '2026-09-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-12 16:31:27', '2025-09-12 16:31:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(529, 88, 7, 34, NULL, NULL, NULL, 'minhtuanpro134@gmail.com', NULL, '2025-09-11 17:00:00', '2026-09-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-12 16:35:08', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(530, 166, 7, 34, NULL, NULL, NULL, 'hoangnamhg1212@gmail.com', NULL, '2025-09-11 17:00:00', '2026-09-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-12 16:36:01', '2025-09-12 17:21:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(531, 146, 1, NULL, NULL, NULL, NULL, 'kieudinhkhanh4@gmail.com', NULL, '2025-09-11 17:00:00', '2025-12-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-12 16:36:39', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(532, 448, 3, NULL, NULL, NULL, NULL, 'tudevapp@gmail.com', NULL, '2025-09-11 17:00:00', '2025-12-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-12 16:37:31', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(533, 449, 52, NULL, NULL, NULL, NULL, 'luonghuuminh43@gmail.com', NULL, '2025-09-11 17:00:00', '2026-03-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-12 16:38:24', '2025-09-12 16:38:24', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(534, 450, 7, 32, NULL, NULL, NULL, 'quachanhmaker@gmail.com', NULL, '2025-09-10 17:00:00', '2026-09-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-12 17:01:59', '2025-09-12 17:21:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(535, 451, 7, 35, NULL, NULL, NULL, 'thailien.231197@gmail.com', NULL, '2025-07-17 17:00:00', '2026-07-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-13 03:03:43', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(536, 452, 7, 35, NULL, NULL, NULL, 'tube238@gmail.com', NULL, '2025-07-09 17:00:00', '2026-07-09 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-13 03:04:33', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(537, 453, 7, 34, NULL, NULL, NULL, 'Phamthiphuonganh9999@gmail.com', NULL, '2025-09-14 17:00:00', '2026-09-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:32:41', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(538, 454, 28, NULL, NULL, NULL, NULL, 'zt7vknb4pl@kinapp13.name.ng', NULL, '2025-09-17 17:00:00', '2025-10-17 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-17 17:33:32', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(540, 455, 3, NULL, NULL, NULL, NULL, 'Anhtuyent94@gmail.com', NULL, '2025-09-16 17:00:00', '2025-12-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:34:41', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(541, 456, 7, 34, NULL, NULL, NULL, 'miniriviu@gmail.com', NULL, '2025-09-16 17:00:00', '2026-09-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:35:32', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(542, 342, 3, NULL, NULL, NULL, NULL, 'ngothuyha393@gmail.com', NULL, '2025-09-16 17:00:00', '2025-12-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:36:26', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(544, 457, 3, NULL, NULL, NULL, NULL, 'duonganh1115@gmail.com', NULL, '2025-09-15 17:00:00', '2025-12-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:39:54', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(545, 458, 3, NULL, NULL, NULL, NULL, 'quachnhuloanhb@gmail.com', NULL, '2025-09-15 17:00:00', '2025-12-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:40:40', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(547, 327, 71, NULL, NULL, NULL, NULL, 'thaoduyent50@gmail.com', NULL, '2025-09-15 17:00:00', '2026-12-09 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:42:59', '2025-11-24 19:22:34', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(548, 327, 3, NULL, NULL, NULL, NULL, 'lieuthuy3214d@gmail.com', NULL, '2025-09-17 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:44:19', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(550, 460, 3, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-09-15 17:00:00', '2025-12-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:45:56', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(551, 198, 3, NULL, NULL, NULL, NULL, 'doanngocgiang777@gmail.com', NULL, '2025-09-15 17:00:00', '2025-12-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:47:02', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(552, 461, 3, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-09-15 17:00:00', '2025-12-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:48:35', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(555, 463, 3, NULL, NULL, NULL, NULL, 'tranthikimngoc0111@gmail.com', NULL, '2025-09-14 17:00:00', '2025-12-13 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:57:55', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(556, 463, 3, NULL, NULL, NULL, NULL, 'tranthikimngoc0111@gmail.com', NULL, '2025-09-14 17:00:00', '2025-10-14 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-17 17:59:13', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(557, 463, 29, NULL, NULL, NULL, NULL, 'tranthikimngoc0111@gmail.com', NULL, '2025-09-14 17:00:00', '2026-09-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 17:59:48', '2025-09-17 17:59:48', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(558, 463, 52, NULL, NULL, NULL, NULL, 'davidnguyen112014@gmail.com', NULL, '2025-09-14 17:00:00', '2026-03-13 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-17 18:00:28', '2025-09-17 18:00:28', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(562, 455, 29, NULL, NULL, NULL, NULL, 'Anhtuyent94@gmail.com', NULL, '2025-09-17 17:00:00', '2026-09-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-18 16:23:43', '2025-09-18 16:23:43', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(563, 465, 29, NULL, NULL, NULL, NULL, 'minhquyet3185@gmail.com', NULL, '2025-09-16 17:00:00', '2026-09-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-18 16:25:31', '2025-09-18 16:25:31', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(564, 465, 3, NULL, NULL, NULL, NULL, 'kienhaphuc93@gmail.com', NULL, '2025-09-16 17:00:00', '2025-12-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-18 16:26:06', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(565, 465, 28, NULL, NULL, NULL, NULL, 'pqfehqp22q@ltria33.name.ng', NULL, '2025-09-16 17:00:00', '2025-10-16 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-18 16:26:34', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(566, 465, 71, NULL, NULL, NULL, NULL, 'lethuyxuyen56@gmail.com', NULL, '2025-09-16 17:00:00', '2025-10-10 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-18 16:27:24', '2025-11-24 19:22:34', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(567, 215, 3, NULL, NULL, NULL, NULL, 'hduy10167@gmail.com', NULL, '2025-09-17 17:00:00', '2025-12-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-18 16:28:05', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(568, 467, 71, NULL, NULL, NULL, NULL, 'shop8@shopdtcom.com', NULL, '2025-09-17 17:00:00', '2025-10-17 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-18 16:28:58', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(569, 468, 52, NULL, NULL, NULL, NULL, 'trangntt.nhe94@gmail.com', NULL, '2025-09-17 17:00:00', '2026-05-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-18 16:30:21', '2025-09-18 16:30:21', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(570, 140, 1, NULL, NULL, NULL, NULL, 'kieudinhkhanh4@gmail.com', NULL, '2025-09-20 17:00:00', '2025-12-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-21 03:57:24', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(572, 469, 3, NULL, NULL, NULL, NULL, 'minhbrine2812@gmail.com', NULL, '2025-08-28 17:00:00', '2025-11-26 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-21 04:19:53', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(574, 471, 29, NULL, NULL, NULL, NULL, 'thanhtungnguyen171197@gmail.com', NULL, '2025-09-18 17:00:00', '2026-09-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-21 04:22:12', '2025-09-21 04:22:12', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(575, 472, 28, NULL, NULL, NULL, NULL, 'dwuu84z79h@kinapp10.name.ng', NULL, '2025-09-18 17:00:00', '2025-10-18 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-21 04:22:57', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(577, 308, 29, NULL, NULL, NULL, NULL, 'shopgauyeu2025@gmail.com', NULL, '2025-09-21 17:00:00', '2026-09-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-22 15:04:34', '2025-09-22 15:04:34', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(578, 448, 74, NULL, NULL, NULL, NULL, 'Tuhn22@gmail.com', NULL, '2025-09-21 17:00:00', '2026-09-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-22 15:06:12', '2025-09-22 15:06:12', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(579, 140, 3, NULL, NULL, NULL, NULL, 'dotheduong@apd.edu.vn', NULL, '2025-09-21 17:00:00', '2025-12-20 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-22 15:06:49', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(580, 473, 74, NULL, NULL, NULL, NULL, 'lephuc11052006@gmail.com', NULL, '2025-09-21 17:00:00', '2026-09-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-22 15:07:38', '2025-09-22 15:07:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(581, 474, 35, NULL, NULL, NULL, NULL, 'bc2@vivu.top', NULL, '2025-08-21 17:00:00', '2025-09-20 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-22 15:08:31', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(583, 268, 71, NULL, NULL, NULL, NULL, 'giaando75@gmail.com', NULL, '2025-08-21 17:00:00', '2026-11-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-22 15:10:12', '2025-11-24 19:22:34', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(584, 475, 28, NULL, NULL, NULL, NULL, '65cklc1ux6@capcut5.name.ng', NULL, '2025-08-21 17:00:00', '2025-09-20 17:00:00', 'expired', NULL, NULL, NULL, NULL, '2025-09-22 15:11:00', '2025-10-29 05:01:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(585, 477, 29, NULL, NULL, NULL, NULL, 'Nhakhoaphuocnguyen@gmail.com', NULL, '2025-09-24 17:00:00', '2026-09-24 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 14:15:36', '2025-09-25 14:15:36', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(586, 478, 1, NULL, NULL, NULL, NULL, 'kieudinhkhanh4@gmail.com', NULL, '2025-09-24 17:00:00', '2025-12-23 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 14:17:24', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(587, 479, 29, NULL, NULL, NULL, NULL, 'thuonghoaile07@gmail.com', NULL, '2025-09-22 17:00:00', '2026-09-22 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 14:19:17', '2025-09-25 14:19:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(588, 479, 3, NULL, NULL, NULL, NULL, 'thuonghoaile07@gmail.com', NULL, '2025-09-24 17:00:00', '2025-12-23 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 14:19:49', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(590, 268, 3, NULL, NULL, NULL, NULL, 'myrleabdilbare4ey0@gmail.com', NULL, '2025-09-24 17:00:00', '2025-12-23 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 14:21:15', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(591, 480, 29, NULL, NULL, NULL, NULL, 'hoangthanhhiep92@gmail.com', NULL, '2025-09-22 17:00:00', '2026-09-22 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 14:23:09', '2025-09-25 14:23:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(592, 441, 21, NULL, NULL, NULL, NULL, 'tranthubinh90@gmail.com', NULL, '2025-09-22 17:00:00', '2025-12-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 14:23:52', '2025-09-25 14:23:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(593, 481, 7, 34, NULL, NULL, NULL, 'kimngoc.kentenglish@gmail.com', NULL, '2025-09-22 17:00:00', '2026-09-22 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 14:25:15', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(594, 482, 28, NULL, NULL, NULL, NULL, 'longthanhly99@gmail.com', NULL, '2025-09-22 17:00:00', '2025-12-22 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 14:26:14', '2025-09-25 14:26:14', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(595, 483, 7, 38, NULL, NULL, NULL, 'sighhh1509@gmail.com', NULL, '2025-09-18 17:00:00', '2026-09-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 16:22:50', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(596, 484, 7, 38, NULL, NULL, NULL, 'hungpchy.pixie@gmail.com', NULL, '2025-09-18 17:00:00', '2026-09-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-25 16:23:27', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(598, 430, 3, NULL, NULL, NULL, NULL, 'maihungthang001@gmail.com', NULL, '2025-09-26 17:00:00', '2025-12-25 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-28 14:26:30', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(599, 430, 3, NULL, NULL, NULL, NULL, 'Tueanh1104@gmail.com', NULL, '2025-09-26 17:00:00', '2025-12-25 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-28 14:27:02', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(600, 430, 3, NULL, NULL, NULL, NULL, 'Thuc1300@gmail.com', NULL, '2025-09-27 17:00:00', '2025-12-26 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-28 14:27:32', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(601, 430, 3, NULL, NULL, NULL, NULL, 'doanquyentrinhminh16577@gmail.com', NULL, '2025-09-27 17:00:00', '2026-03-26 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-28 14:30:03', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(603, 459, 54, NULL, NULL, NULL, NULL, 'taichel99@gmail.com', NULL, '2025-09-26 17:00:00', '2026-09-26 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-28 14:32:25', '2025-09-28 14:32:25', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(604, 485, 75, NULL, NULL, NULL, NULL, 'hoangqgsivkhanh@gmail.com', NULL, '2025-09-26 17:00:00', '2026-09-26 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-28 14:35:54', '2025-09-28 14:35:54', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(605, 486, 75, NULL, NULL, NULL, NULL, 'huynhquocdexeg5651@gmail.com', NULL, '2025-09-26 17:00:00', '2026-09-26 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-28 14:39:16', '2025-09-28 14:39:16', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(606, 247, 3, NULL, NULL, NULL, NULL, 'ngocanh.doan27@gmail.com', NULL, '2025-09-28 17:00:00', '2025-12-27 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-29 14:25:44', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(608, 270, 3, NULL, NULL, NULL, NULL, 'hanghoangnam06@gmail.com', NULL, '2025-09-28 17:00:00', '2025-12-27 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-29 14:32:26', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(609, 404, 30, NULL, NULL, NULL, NULL, 'chilinhnguyen209@gmail.com', NULL, '2025-09-28 17:00:00', '2026-09-28 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-29 14:33:19', '2025-09-29 14:33:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(610, 487, 3, NULL, NULL, NULL, NULL, 'baotunkhanh1904@gmail.com', NULL, '2025-09-28 17:00:00', '2025-12-27 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-29 14:34:25', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(611, 423, 3, NULL, NULL, NULL, NULL, 'davidschubertrenate@gmail.com', NULL, '2025-09-28 17:00:00', '2025-12-27 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-29 14:36:28', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(612, 423, 3, NULL, NULL, NULL, NULL, 'khuongbzmjephuong@gmail.com', NULL, '2025-09-30 17:00:00', '2025-12-29 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-30 17:02:07', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(613, 430, 3, NULL, NULL, NULL, NULL, 'longduong15489@gmail.com', NULL, '2025-09-30 17:00:00', '2025-12-29 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-30 17:02:55', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(614, 430, 3, NULL, NULL, NULL, NULL, 'vithanhtungai@gmail.com', NULL, '2025-09-30 17:00:00', '2025-12-29 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-30 17:03:18', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(615, 430, 3, NULL, NULL, NULL, NULL, 'tuanbavi12336612@gmail.com', NULL, '2025-09-30 17:00:00', '2025-12-29 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-30 17:03:44', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(616, 488, 3, NULL, NULL, NULL, NULL, 'dotrangcdsphn@gmail.com', NULL, '2025-09-30 17:00:00', '2025-12-29 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-30 17:04:42', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(618, 195, 3, NULL, NULL, NULL, NULL, 'duongbichhanghong@gmail.com', NULL, '2025-09-30 17:00:00', '2025-12-29 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-30 17:07:37', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(620, 87, 7, 38, NULL, NULL, NULL, 'vhnauy@gmail.com', NULL, '2025-09-30 17:00:00', '2026-09-30 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-30 17:11:06', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(621, 132, 49, 30, NULL, NULL, NULL, 'ngantq-tng@abic.com.vn', NULL, '2025-09-30 17:00:00', '2025-11-29 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-30 17:13:21', '2025-11-24 17:44:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(623, 492, 1, NULL, NULL, NULL, NULL, 'duonghuutam50257@gmail.com', NULL, '2025-09-30 17:00:00', '2025-12-29 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-09-30 17:14:57', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(624, 158, 61, 37, NULL, NULL, NULL, 'Xuanbinh0295@gmail.com', NULL, '2025-10-01 17:00:00', '2025-10-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-02 15:43:26', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(625, 132, 58, NULL, NULL, NULL, NULL, 'johnsondavidsskaq8011@gmail.com', NULL, '2025-10-01 17:00:00', '2025-10-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-02 15:44:07', '2025-10-02 15:44:07', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(627, 443, 3, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-01 17:00:00', '2025-12-30 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-02 15:46:16', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(628, 491, 7, 38, NULL, NULL, NULL, 'Tutbtv@gmail.com', NULL, '2025-09-30 17:00:00', '2026-09-30 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-02 15:47:22', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(629, 493, 75, NULL, NULL, NULL, NULL, 'tonganhtrang11@gmail.com', NULL, '2025-10-01 17:00:00', '2026-10-01 17:00:00', 'active', NULL, NULL, NULL, 'Gmail | Pass | MKP\r\n\r\ntonganhtrang11@gmail.com | cxygcVCcx4vK | 290cac9ac1dc@drmail.in\r\n\r\nLogin cố định 1 - 2 thiết bị, chờ 5-7 ngày mới đổi all thông tin chính chủ, tránh die mail\r\n\r\nHSD: 02/10/2025 - 02/10/2026', '2025-10-02 15:48:18', '2025-10-02 15:48:18', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(630, 415, 28, NULL, NULL, NULL, NULL, '83devmdodf@capcut78.name.ng', NULL, '2025-10-01 17:00:00', '2025-10-31 17:00:00', 'active', NULL, NULL, NULL, '83devmdodf@capcut78.name.ng 1234567', '2025-10-02 15:49:04', '2025-10-02 15:49:04', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(631, 423, 3, NULL, NULL, NULL, NULL, 'vantrangvu836@gmail.com', NULL, '2025-10-01 17:00:00', '2025-12-30 17:00:00', 'active', NULL, NULL, NULL, 'vantrangvu836@gmail.com|cDcIBC2stW7P|WLAPSBKIYDCXT2POUQZFY72PRKIQDPAH', '2025-10-02 15:50:10', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(632, 429, 49, 30, NULL, NULL, NULL, 'tuandoann11@gmail.com', NULL, '2025-10-01 17:00:00', '2025-10-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-02 15:51:11', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(633, 429, 49, 30, NULL, NULL, NULL, 'khoitrandang0312@gmail.com', NULL, '2025-09-30 17:00:00', '2025-10-30 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-02 15:52:31', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(634, 139, 21, NULL, NULL, NULL, NULL, 'daryawerwinum7ot9@hotmail.com', NULL, '2025-10-01 17:00:00', '2025-12-30 17:00:00', 'active', NULL, NULL, NULL, 'daryawerwinum7ot9@hotmail.com|Elevenlab123@', '2025-10-02 15:54:26', '2025-10-02 15:54:26', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(635, 425, 49, 30, NULL, NULL, NULL, 'Buidinhhoan71cg@gmail.com', NULL, '2025-10-01 17:00:00', '2025-10-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-02 15:56:26', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(636, 182, 27, NULL, NULL, NULL, NULL, 'reed0165@flinders.edu.au', NULL, '2025-09-30 17:00:00', '2026-09-30 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-02 15:57:49', '2025-10-02 15:57:49', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(637, 423, 61, 31, NULL, NULL, NULL, 'misuclosetshop@gmail.com', NULL, '2025-10-02 17:00:00', '2025-11-01 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-03 16:18:02', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(638, 459, 28, NULL, NULL, NULL, NULL, 'kdi5dhbvig@capcut65.name.ng', NULL, '2025-10-02 17:00:00', '2025-11-01 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-03 16:19:26', '2025-10-03 16:19:26', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(640, 421, 3, NULL, NULL, NULL, NULL, 'phanthiquan557@gmail.com', NULL, '2025-10-02 17:00:00', '2025-12-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-03 16:22:08', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(641, 430, 21, NULL, NULL, NULL, NULL, 'frigaydewigk0rzk7@hotmail.com', NULL, '2025-10-02 17:00:00', '2025-12-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-03 16:22:47', '2025-10-03 16:22:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(642, 430, 3, NULL, NULL, NULL, NULL, 'trongsonle538@gmail.com', NULL, '2025-10-02 17:00:00', '2025-12-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-03 16:23:17', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(643, 430, 3, NULL, NULL, NULL, NULL, 'phananhnga15@gmail.com', NULL, '2025-10-02 17:00:00', '2025-12-31 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-03 16:23:42', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL);
INSERT INTO `customer_services` (`id`, `customer_id`, `service_package_id`, `family_account_id`, `assigned_by`, `supplier_id`, `supplier_service_id`, `login_email`, `login_password`, `activated_at`, `expires_at`, `status`, `duration_days`, `cost_price`, `price`, `internal_notes`, `created_at`, `updated_at`, `reminder_sent`, `reminder_sent_at`, `reminder_count`, `reminder_notes`, `two_factor_code`, `recovery_codes`, `shared_account_notes`, `customer_instructions`, `password_expires_at`, `two_factor_updated_at`, `is_password_shared`, `shared_with_customers`) VALUES
(646, 493, 21, NULL, NULL, NULL, NULL, 'remiesgaretxjx9p@hotmail.com', NULL, '2025-10-03 17:00:00', '2026-01-01 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-04 16:58:10', '2025-10-04 16:58:10', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(647, 491, 29, NULL, NULL, NULL, NULL, 'Tutbtv@gmail.com', NULL, '2025-10-03 17:00:00', '2026-10-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-04 16:58:47', '2025-10-04 16:58:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(648, 494, 3, NULL, NULL, NULL, NULL, 'Dhscons01@gmail.com', NULL, '2025-10-03 17:00:00', '2026-01-01 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-04 16:59:26', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(650, 157, 1, NULL, NULL, NULL, NULL, 'duonghuutam50257@gmail.com', NULL, '2025-10-04 17:00:00', '2026-01-02 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-05 17:43:36', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(651, 425, 49, 30, NULL, NULL, NULL, 'lllethang4002@gmail.com', NULL, '2025-10-04 17:00:00', '2025-11-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-05 17:44:17', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(652, 425, 49, 30, NULL, NULL, NULL, 'sharingchatgptplus95@gmail.com', NULL, '2025-10-05 17:00:00', '2025-11-04 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-06 17:19:40', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(653, 495, 3, NULL, NULL, NULL, NULL, 'nguyendinhdanhuy94@gmail.com', NULL, '2025-10-05 17:00:00', '2026-01-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-06 17:20:28', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(654, 496, 3, NULL, NULL, NULL, NULL, 'chauoanh.8485@gmail.com', NULL, '2025-10-05 17:00:00', '2026-01-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-06 17:21:32', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(655, 340, 1, NULL, NULL, NULL, NULL, 'duonghuutam50257@gmail.com', NULL, '2025-10-05 17:00:00', '2026-01-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-06 17:23:10', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(657, 104, 3, NULL, NULL, NULL, NULL, 'nguyenthanhthienan20092006@gmail.com', NULL, '2025-10-05 17:00:00', '2026-01-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-06 17:24:18', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(658, 497, 3, NULL, NULL, NULL, NULL, '2253010092@student.ctump.edu.vn', NULL, '2025-10-05 17:00:00', '2026-01-03 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-06 17:25:06', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(659, 430, 62, NULL, NULL, NULL, NULL, 'spahrsmanort8544@hotmail.com', NULL, '2025-10-07 17:00:00', '2025-11-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-08 16:48:30', '2025-10-08 16:48:30', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(660, 498, 3, NULL, NULL, NULL, NULL, 'phamvibo8@gmail.com', NULL, '2025-10-07 17:00:00', '2026-01-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-08 16:49:45', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(661, 423, 3, NULL, NULL, NULL, NULL, 'bonthui3005@gmail.com', NULL, '2025-10-07 17:00:00', '2026-01-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-08 16:50:28', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(663, 320, 11, NULL, NULL, NULL, NULL, 'yqleiogk381@hotmail.com', NULL, '2025-10-07 17:00:00', '2025-11-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-08 16:51:30', '2025-10-08 16:51:30', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(664, 304, 35, NULL, NULL, NULL, NULL, 'gillianbrown11@skyhush.com', NULL, '2025-10-06 17:00:00', '2025-11-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-08 16:52:13', '2025-10-08 16:52:13', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(665, 259, 1, NULL, NULL, NULL, NULL, 'duonghuutam50257@gmail.com', NULL, '2025-10-07 17:00:00', '2026-01-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-08 16:53:07', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(666, 425, 49, 30, NULL, NULL, NULL, 'ngochuyit.ai.01@gmail.com', NULL, '2025-10-08 17:00:00', '2025-11-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-09 17:14:37', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(667, 491, 74, NULL, NULL, NULL, NULL, 'Nhahangquancathaibinh@gmail.com', NULL, '2025-10-08 17:00:00', '2026-10-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-09 17:15:27', '2025-10-09 17:15:27', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(668, 423, 28, NULL, NULL, NULL, NULL, '5kmesd56i1@kinapp30.name.ng', NULL, '2025-10-08 17:00:00', '2025-11-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-09 17:16:18', '2025-10-09 17:16:18', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(669, 499, 3, NULL, NULL, NULL, NULL, 'lienvietcompany02@gmail.com', NULL, '2025-10-08 17:00:00', '2026-01-06 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-09 17:17:18', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(670, 499, 52, NULL, NULL, NULL, NULL, 'lienvietcompany02@gmail.com', NULL, '2025-10-08 17:00:00', '2026-10-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-09 17:17:55', '2025-11-24 19:16:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(671, 499, 7, 38, NULL, NULL, NULL, 'thanhphamxuan2042005@gmail.com', NULL, '2025-10-09 17:00:00', '2026-10-09 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-09 17:18:31', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(672, 500, 1, NULL, NULL, NULL, NULL, 'caotelu3@gmail.com', NULL, '2025-10-09 17:00:00', '2026-01-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-09 17:19:38', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(673, 88, 1, NULL, NULL, NULL, NULL, 'caotelu3@gmail.com', NULL, '2025-10-09 17:00:00', '2026-01-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-10 16:03:04', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(675, 501, 59, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-09 17:00:00', '2026-10-09 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-10 16:05:51', '2025-10-10 16:05:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(676, 502, 52, NULL, NULL, NULL, NULL, 'haireguhaigure@gmail.com', NULL, '2025-10-09 17:00:00', '2026-04-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-10 16:06:43', '2025-10-10 16:06:43', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(677, 270, 3, NULL, NULL, NULL, NULL, 'thanhtung11028@gmail.com', NULL, '2025-10-09 17:00:00', '2026-01-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-10 16:07:29', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(678, 270, 3, NULL, NULL, NULL, NULL, 'nhatminh151117@gmail.com', NULL, '2025-10-09 17:00:00', '2026-01-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-10 16:08:11', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(679, 425, 49, 30, NULL, NULL, NULL, 'long.vutien@gmail.com', NULL, '2025-10-09 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-10 16:09:02', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(680, 425, 49, 30, NULL, NULL, NULL, 'Tranhung91nd@gmail.com', NULL, '2025-10-09 17:00:00', '2025-11-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-10 16:09:38', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(682, 268, 3, NULL, NULL, NULL, NULL, 'nthanhquoc52@gmail.com', NULL, '2025-10-09 17:00:00', '2026-01-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-10 16:12:22', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(683, 503, 3, NULL, NULL, NULL, NULL, 'trongthuan.growx@gmail.com', NULL, '2025-10-09 17:00:00', '2026-01-07 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-10 16:13:19', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(685, 504, 3, NULL, NULL, NULL, NULL, 'Bichchau81.nguyen@gmail.com', NULL, '2025-10-10 17:00:00', '2026-01-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-11 16:54:57', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(686, 505, 3, NULL, NULL, NULL, NULL, 'Buiminhlinh88@gmail.com', NULL, '2025-10-10 17:00:00', '2026-01-08 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-11 16:55:47', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(687, 506, 7, 39, NULL, NULL, NULL, 'nhumanhkhanh@gmail.com', NULL, '2025-10-10 17:00:00', '2026-10-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-11 17:00:04', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(688, 120, 1, NULL, NULL, NULL, NULL, 'caotelu3@gmail.com', NULL, '2025-10-11 17:00:00', '2026-01-09 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-11 17:01:13', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(689, 103, 3, NULL, NULL, NULL, NULL, 'Naduc1113@gmail.com', NULL, '2025-10-11 17:00:00', '2026-01-09 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-12 15:21:11', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(690, 421, 67, NULL, NULL, NULL, NULL, 'ngocnguyen115+100@gmail.com', NULL, '2025-10-11 17:00:00', '2026-10-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-12 15:22:00', '2025-10-12 15:22:00', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(691, 430, 70, NULL, NULL, NULL, NULL, 'ParkerOlivia4384@hotmail.com', NULL, '2025-10-11 17:00:00', '2026-10-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-12 15:22:41', '2025-10-12 15:22:41', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(692, 430, 21, NULL, NULL, NULL, NULL, 'indiratunneydsb1982@hotmail.com', NULL, '2025-10-12 17:00:00', '2026-01-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 15:57:58', '2025-10-13 15:57:58', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(693, 480, 71, NULL, NULL, NULL, NULL, 'zehumac856@gmail.com', NULL, '2025-10-12 17:00:00', '2027-01-05 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 15:58:55', '2025-11-24 19:22:34', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(694, 327, 76, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-12 17:00:00', '2025-10-29 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 15:59:55', '2025-10-13 15:59:55', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(695, 429, 28, NULL, NULL, NULL, NULL, 'qo6zg0v7yl@ltria34.name.ng', NULL, '2025-10-12 17:00:00', '2025-11-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 16:00:29', '2025-10-13 16:00:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(696, 429, 28, NULL, NULL, NULL, NULL, 'cinroz@capcut76.name.ng', NULL, '2025-10-12 17:00:00', '2025-11-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 16:00:46', '2025-10-13 16:00:46', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(697, 270, 3, NULL, NULL, NULL, NULL, 'thengoc.phulien@gmail.com', NULL, '2025-10-12 17:00:00', '2026-01-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 16:01:33', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(698, 351, 28, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-12 17:00:00', '2025-11-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 16:02:18', '2025-10-13 16:02:18', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(699, 431, 28, NULL, NULL, NULL, NULL, 'kkjguvahkm@capcut13.name.ng', NULL, '2025-10-12 17:00:00', '2025-11-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 16:02:58', '2025-10-13 16:02:58', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(700, 431, 28, NULL, NULL, NULL, NULL, 'whrugga4h5@capcut34.name.ng', NULL, '2025-10-12 17:00:00', '2025-11-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 16:03:14', '2025-10-13 16:03:14', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(701, 320, 11, NULL, NULL, NULL, NULL, 'RaeMurillofpw92792c@thecu.org', NULL, '2025-10-12 17:00:00', '2025-11-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 16:03:47', '2025-10-13 16:03:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(702, 177, 3, NULL, NULL, NULL, NULL, 'vuduchieu062004@gmail.com', NULL, '2025-10-12 17:00:00', '2026-01-10 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-13 16:04:31', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(703, 270, 3, NULL, NULL, NULL, NULL, 'bsquangthoaictch1986@gmail.com', NULL, '2025-10-13 17:00:00', '2026-01-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-14 15:55:47', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(704, 320, 11, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-13 17:00:00', '2025-11-12 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-14 15:58:52', '2025-10-14 15:58:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(705, 320, 11, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-13 17:00:00', '2025-11-12 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-14 16:00:02', '2025-10-14 16:00:02', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(706, 464, 3, NULL, NULL, NULL, NULL, 'diepdiepquy@gmail.com', NULL, '2025-10-13 17:00:00', '2026-01-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-14 16:00:39', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(707, 507, 28, NULL, NULL, NULL, NULL, 'joesia@mailcc26.name.ng', NULL, '2025-10-13 17:00:00', '2025-11-12 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-14 16:01:48', '2025-10-14 16:01:48', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(708, 508, 75, NULL, NULL, NULL, NULL, 'Levanlanh2245@gmail.com', NULL, '2025-10-16 17:00:00', '2026-10-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 06:14:32', '2025-10-17 06:14:32', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(709, 505, 19, NULL, NULL, NULL, NULL, 'Buiminhlinh88@gmail.com', NULL, '2025-10-15 17:00:00', '2025-11-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:23:47', '2025-10-17 08:23:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(710, 430, 3, NULL, NULL, NULL, NULL, 'vuonghaiimdws937@gmail.com', NULL, '2025-10-14 17:00:00', '2026-04-12 17:00:00', 'active', NULL, NULL, NULL, 'vuonghaiimdws937@gmail.com}	minhhai3225@|luongquyen930@hotmail.com', '2025-10-17 08:25:55', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(711, 430, 52, NULL, NULL, NULL, NULL, 'vantoan211291@gmail.com', NULL, '2025-10-15 17:00:00', '2026-04-13 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:28:42', '2025-10-17 08:28:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(712, 455, 28, NULL, NULL, NULL, NULL, 'bushog@capcut10.name.ng', NULL, '2025-10-16 17:00:00', '2025-11-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:29:22', '2025-10-17 08:29:22', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(713, 455, 71, NULL, NULL, NULL, NULL, 'agencygu11504@info.thuanday1.io.vn', NULL, '2025-10-15 17:00:00', '2025-11-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:29:49', '2025-10-17 08:29:49', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(715, 508, 28, NULL, NULL, NULL, NULL, 'jevtuv@kingplan13.name.ng', NULL, '2025-10-16 17:00:00', '2025-11-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:31:50', '2025-10-17 08:31:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(716, 270, 21, NULL, NULL, NULL, NULL, 'eikenhbosca6a4o@outlook.com', NULL, '2025-10-15 17:00:00', '2026-01-13 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:33:47', '2025-10-17 08:33:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(717, 462, 5, NULL, NULL, NULL, NULL, 'qtbody@gmail.com', NULL, '2025-10-15 17:00:00', '2025-11-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:35:20', '2025-10-17 08:35:20', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(718, 183, 34, NULL, NULL, NULL, NULL, 'ngocquynh911@gmail.com', NULL, '2025-10-15 17:00:00', '2026-10-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:37:00', '2025-10-17 08:37:00', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(719, 509, 7, 39, NULL, NULL, NULL, 'huycao0356@gmail.com', NULL, '2025-10-14 17:00:00', '2026-10-14 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:37:59', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(720, 510, 3, NULL, NULL, NULL, NULL, 'luuhoang.mrboss@gmail.com', NULL, '2025-10-14 17:00:00', '2026-01-12 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:38:52', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(721, 140, 49, 30, NULL, NULL, NULL, 'duongdtvn2@gmail.com', NULL, '2025-10-12 17:00:00', '2025-11-11 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-17 08:40:00', '2025-11-24 17:44:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(722, 166, 35, NULL, NULL, NULL, NULL, 'bf1@meobin.icu', NULL, '2025-10-17 17:00:00', '2025-11-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-18 16:24:39', '2025-10-18 16:24:39', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(723, 430, 70, NULL, NULL, NULL, NULL, 'yhfrztjls26632@hotmail.com', NULL, '2025-10-17 17:00:00', '2026-10-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-18 16:25:36', '2025-10-18 16:25:36', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(724, 459, 75, NULL, NULL, NULL, NULL, 'taichel99@gmail.com', NULL, '2025-10-17 17:00:00', '2026-10-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-18 16:26:54', '2025-10-18 16:26:54', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(725, 459, 21, NULL, NULL, NULL, NULL, 'taichel99@gmail.com', NULL, '2025-10-17 17:00:00', '2026-01-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-18 16:27:10', '2025-10-18 16:27:10', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(726, 225, 71, NULL, NULL, NULL, NULL, 'leoedgar440@hr.edgaragency2.io.vn', NULL, '2025-10-17 17:00:00', '2025-11-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-18 16:27:59', '2025-10-18 16:27:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(727, 433, 78, NULL, NULL, NULL, NULL, 'Thongth@0978758585', NULL, '2025-10-16 17:00:00', '2025-11-15 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-18 16:31:38', '2025-10-18 16:31:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(728, 425, 61, NULL, NULL, NULL, NULL, 'thanhnienlaichau.vn@gmail.com', NULL, '2025-10-17 17:00:00', '2025-11-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-18 16:32:17', '2025-11-24 17:37:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(729, 470, 28, NULL, NULL, NULL, NULL, 'veasag@fatima.io.vn', NULL, '2025-10-17 17:00:00', '2025-11-16 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-18 16:32:56', '2025-10-18 16:32:56', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(730, 511, 74, NULL, NULL, NULL, NULL, 'luatducphuong@gmail.com', NULL, '2025-10-17 17:00:00', '2026-10-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-18 16:33:50', '2025-10-18 16:33:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(731, 512, 28, NULL, NULL, NULL, NULL, 'sidne@korrect.cfd', NULL, '2025-10-18 17:00:00', '2025-11-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-19 18:37:22', '2025-10-19 18:37:22', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(732, 277, 28, NULL, NULL, NULL, NULL, 'zihhiu@goon.io.vn', NULL, '2025-10-18 17:00:00', '2025-11-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-19 18:38:08', '2025-10-19 18:38:08', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(733, 166, 34, NULL, NULL, NULL, NULL, 'Hoangnamhg1212@gmail.com', NULL, '2025-10-18 17:00:00', '2026-10-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-19 18:39:12', '2025-10-19 18:39:12', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(734, 513, 28, NULL, NULL, NULL, NULL, 'samjac@mailcc14.name.ng', NULL, '2025-10-18 17:00:00', '2025-11-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-19 18:40:27', '2025-10-19 18:40:27', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(735, 429, 28, NULL, NULL, NULL, NULL, 'edd8@harlowfashion.shop', NULL, '2025-10-19 17:00:00', '2025-11-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-19 18:41:07', '2025-10-19 18:41:07', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(736, 429, 28, NULL, NULL, NULL, NULL, 'ramon@integrately.net', NULL, '2025-10-19 17:00:00', '2025-11-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-19 18:41:29', '2025-10-19 18:41:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(737, 406, 71, NULL, NULL, NULL, NULL, 'guagency304@info.thuanne.io.vn', NULL, '2025-10-19 17:00:00', '2025-11-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-20 17:04:56', '2025-11-24 19:22:34', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(738, 514, 29, NULL, NULL, NULL, NULL, 'trieuminhaq@gmail.com', NULL, '2025-10-19 17:00:00', '2026-10-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-20 17:05:55', '2025-10-20 17:05:55', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(739, 139, 20, NULL, NULL, NULL, NULL, 'deliasomersdja1977@hotmail.com', NULL, '2025-10-19 17:00:00', '2025-11-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-20 17:07:06', '2025-10-20 17:07:06', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(740, 431, 28, NULL, NULL, NULL, NULL, 'mayna@kutsartor.shop', NULL, '2025-10-20 17:00:00', '2025-11-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-20 17:08:18', '2025-10-20 17:08:18', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(742, 183, 49, 30, NULL, NULL, NULL, 'ngocquynh911@gmail.com', NULL, '2025-10-19 17:00:00', '2026-04-17 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-20 17:12:24', '2025-11-24 19:03:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(743, 136, 79, NULL, NULL, NULL, NULL, 'hlinh.dinh@gmail.com', NULL, '2025-10-19 17:00:00', '2026-10-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-20 17:18:16', '2025-10-20 17:18:16', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(745, 102, 34, NULL, NULL, NULL, NULL, 'chungvietphuonghbl@gmail.com', NULL, '2025-10-21 17:00:00', '2026-10-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:09:39', '2025-10-21 18:09:39', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(746, 517, 29, NULL, NULL, NULL, NULL, 'lptt0396526886@gmail.com', NULL, '2025-10-21 17:00:00', '2026-10-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:10:55', '2025-10-21 18:10:55', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(747, 517, 49, 30, NULL, NULL, NULL, 'lptt0396526886@gmail.com', NULL, '2025-10-20 17:00:00', '2025-11-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:11:24', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(748, 517, 71, NULL, NULL, NULL, NULL, 'guagency94980@info.thuanne.io.vn', NULL, '2025-10-20 17:00:00', '2025-11-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:12:12', '2025-10-21 18:12:12', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(749, 518, 1, NULL, NULL, NULL, NULL, 'caotelu3@gmail.com', NULL, '2025-10-20 17:00:00', '2026-01-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:13:06', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(750, 425, 29, NULL, NULL, NULL, NULL, 'simplus9686@gmail.com', NULL, '2025-10-20 17:00:00', '2026-10-20 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:13:58', '2025-10-21 18:13:58', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(751, 435, 81, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-20 17:00:00', '2025-11-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:15:40', '2025-10-21 18:15:40', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(752, 308, 3, NULL, NULL, NULL, NULL, 'ClarksonTP27@onlinejobss.site', NULL, '2025-10-20 17:00:00', '2026-01-18 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:16:44', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(753, 343, 29, NULL, NULL, NULL, NULL, 'tranthihien_thbt@quangbinh.edu.vn', NULL, '2025-10-21 17:00:00', '2026-10-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:17:50', '2025-10-21 18:17:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(754, 343, 29, NULL, NULL, NULL, NULL, 'lienscpy@gmail.com', NULL, '2025-10-21 17:00:00', '2026-10-21 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:18:24', '2025-10-21 18:18:24', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(755, 519, 21, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-21 17:00:00', '2026-01-19 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-21 18:19:27', '2025-10-21 18:19:27', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(758, 519, 21, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-26 17:00:00', '2026-10-26 17:00:00', 'active', NULL, NULL, NULL, NULL, '2025-10-27 16:06:51', '2025-10-27 16:06:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(759, 516, 3, NULL, NULL, NULL, NULL, 'nguyenvanphatdat.2007tg@gmail.com', NULL, '2025-10-21 17:00:00', '2025-11-20 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-10-29 17:19:23', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(760, 425, 49, 30, NULL, NULL, NULL, 'duongchatgptplus@gmail.com', NULL, '2025-10-28 17:00:00', '2025-11-27 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-02 15:33:08', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(761, 425, 49, 30, NULL, NULL, NULL, 'Vanphongubndbinhlu@gmail.com', NULL, '2025-10-28 17:00:00', '2025-11-27 17:00:00', 'active', 60, 0.00, 0.00, NULL, '2025-11-02 15:34:14', '2025-11-02 15:37:55', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(763, 520, 3, NULL, NULL, NULL, NULL, 'Xx.smash1@gmail.com', NULL, '2025-10-28 17:00:00', '2025-11-27 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-02 16:02:28', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(764, 174, 71, NULL, NULL, NULL, NULL, 'kien@roneey.dpdns.org', NULL, '2025-10-28 17:00:00', '2025-11-27 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-03 03:43:46', '2025-11-03 03:43:46', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(765, 174, 52, NULL, NULL, NULL, NULL, 'nv41twefer@riesz.blema.io.vn', NULL, '2025-10-28 17:00:00', '2025-11-27 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-03 03:44:37', '2025-11-03 14:52:45', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(766, 431, 49, 30, NULL, NULL, NULL, 'phutungfreyvn@gmail.com', NULL, '2025-10-28 17:00:00', '2026-10-23 17:00:00', 'active', 360, 0.00, 0.00, NULL, '2025-11-03 03:46:07', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(767, 521, 29, NULL, NULL, NULL, NULL, 'hnduynam@gmail.com', NULL, '2025-10-28 17:00:00', '2026-10-23 17:00:00', 'active', 360, 0.00, 0.00, NULL, '2025-11-03 15:10:14', '2025-11-03 15:10:14', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(768, 431, 49, 30, NULL, NULL, NULL, 'teebumin@gmail.com', NULL, '2025-10-28 17:00:00', '2025-11-27 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-03 15:12:29', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(769, 522, 3, NULL, NULL, NULL, NULL, 'thanhbtht@gmail.com', NULL, '2025-10-21 17:00:00', '2025-11-20 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-03 15:19:58', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(770, 523, 61, 31, NULL, NULL, NULL, 'Rynopham@gmail.com', NULL, '2025-10-21 17:00:00', '2025-11-20 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-03 15:24:25', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(771, 215, 28, NULL, NULL, NULL, NULL, 'hoytt@cuongaquarium.com', NULL, '2025-10-21 17:00:00', '2025-11-20 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-03 15:25:15', '2025-11-03 15:25:15', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(772, 140, 3, NULL, NULL, NULL, NULL, 'tawondagabbettt145117@hotmail.com', NULL, '2025-10-21 17:00:00', '2026-01-04 17:00:00', 'active', 75, 0.00, 0.00, NULL, '2025-11-03 15:28:23', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(773, 140, 54, NULL, NULL, NULL, NULL, 'duongdtvn2@gmail.com', NULL, '2025-10-21 17:00:00', '2026-10-16 17:00:00', 'active', 360, 0.00, 0.00, NULL, '2025-11-03 15:28:59', '2025-11-03 15:28:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(777, 480, 71, NULL, NULL, NULL, NULL, 'guagency35774@guagency43.io.vn', NULL, '2025-10-22 17:00:00', '2025-11-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 15:44:38', '2025-11-21 03:56:06', 1, '2025-11-21 03:56:06', 1, '[21/11/2025 10:56] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(778, 356, 35, NULL, NULL, NULL, NULL, 'c31@mitri.top', NULL, '2025-10-22 17:00:00', '2025-11-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 15:45:47', '2025-11-21 03:55:57', 1, '2025-11-21 03:55:57', 1, '[21/11/2025 10:55] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(779, 524, 3, NULL, NULL, NULL, NULL, 'nealacunninghamvdcpd@jour.us', NULL, '2025-10-22 17:00:00', '2026-01-12 17:00:00', 'active', 82, 0.00, 0.00, NULL, '2025-11-06 15:47:08', '2025-11-06 15:47:08', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(780, 194, 28, NULL, NULL, NULL, NULL, 'ukjd09hxf1@nbikx.blema.io.vn', NULL, '2025-10-25 17:00:00', '2025-11-24 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 15:52:57', '2025-11-06 15:52:57', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(781, 270, 29, NULL, NULL, NULL, NULL, 'hoalt2810@gmail.com', NULL, '2025-10-23 17:00:00', '2026-10-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 16:20:44', '2025-11-06 16:24:33', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(782, 525, 1, NULL, NULL, NULL, NULL, 'purdumyoungers2986@hotmail.com', NULL, '2025-10-26 17:00:00', '2026-01-09 17:00:00', 'active', 75, 0.00, 0.00, NULL, '2025-11-06 16:22:04', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(783, 505, 29, NULL, NULL, NULL, NULL, 'Huynhmy1775@gmail.com', NULL, '2025-10-25 17:00:00', '2026-10-25 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 16:23:25', '2025-11-06 16:23:25', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(784, 202, 1, NULL, NULL, NULL, NULL, 'caotelu3@gmail.com', NULL, '2025-10-25 17:00:00', '2025-12-24 17:00:00', 'active', 60, 0.00, 0.00, NULL, '2025-11-06 16:25:31', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(785, 343, 71, NULL, NULL, NULL, NULL, 'guagency112@info.hungveoai.io.vn', NULL, '2025-10-26 17:00:00', '2025-11-25 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 16:30:30', '2025-11-06 16:30:30', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(786, 526, 74, NULL, NULL, NULL, NULL, 'Nguyenha85hp@gmail.com', NULL, '2025-11-02 17:00:00', '2026-11-02 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 16:32:20', '2025-11-06 16:32:20', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(787, 526, 74, NULL, NULL, NULL, NULL, 'minhdoan.hp@gmail.com', NULL, '2025-10-19 17:00:00', '2026-10-19 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 16:33:12', '2025-11-06 16:33:12', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(788, 527, 3, NULL, NULL, NULL, NULL, 'monicanixonhgpgl@ickets.us', NULL, '2025-10-26 17:00:00', '2026-01-09 17:00:00', 'active', 75, 0.00, 0.00, NULL, '2025-11-06 16:35:14', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(789, 527, 28, NULL, NULL, NULL, NULL, 'x51pvabnrj@iczxm.adubadu.io.vn', NULL, '2025-10-23 17:00:00', '2025-11-22 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 16:35:51', '2025-11-06 16:35:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(790, 527, 49, 30, NULL, NULL, NULL, 'ngachatgpt82@gmail.com', NULL, '2025-10-22 17:00:00', '2025-11-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 16:36:29', '2025-11-24 17:29:52', 1, '2025-11-21 03:53:21', 1, '[21/11/2025 10:53] Đánh dấu từ giao diện web', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(791, 308, 49, 30, NULL, NULL, NULL, 'shopgauyeu2025@gmail.com', NULL, '2025-10-23 17:00:00', '2026-10-23 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 16:37:30', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(792, 145, 26, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-27 17:00:00', '2025-11-26 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 16:41:04', '2025-11-06 16:41:04', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(793, 176, 49, 30, NULL, NULL, NULL, 'haianh2602003@gmail.com', NULL, '2025-10-26 17:00:00', '2025-11-25 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 16:42:33', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(794, 176, 29, NULL, NULL, NULL, NULL, 'th.anh2602003@gmail.com', NULL, '2025-10-27 17:00:00', '2026-10-27 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 16:43:21', '2025-11-06 16:43:21', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(795, 430, 70, NULL, NULL, NULL, NULL, 'jezabelpilid9629@hotmail.com', NULL, '2025-10-25 17:00:00', '2026-10-25 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 16:44:43', '2025-11-06 16:44:43', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(797, 430, 21, NULL, NULL, NULL, NULL, 'vinhx1991@gmail.com', NULL, '2025-10-25 17:00:00', '2026-10-25 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 17:28:12', '2025-11-06 17:28:12', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(798, 406, 4, NULL, NULL, NULL, NULL, 'ayarsjluddy76t@hotmail.com', NULL, '2025-10-24 17:00:00', '2025-11-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:29:26', '2025-11-06 17:29:26', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(799, 425, 49, 30, NULL, NULL, NULL, 'Deliciouscookie20@gmail.com', NULL, '2025-10-21 17:00:00', '2025-11-20 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:30:11', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(800, 425, 49, 30, NULL, NULL, NULL, 'conmeongungocgpt@gmail.com', NULL, '2025-10-24 17:00:00', '2025-11-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:30:39', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(801, 425, 49, 30, NULL, NULL, NULL, 'thinhnguyenydhp@gmail.com', NULL, '2025-10-25 17:00:00', '2025-11-24 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:32:09', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(802, 425, 49, 30, NULL, NULL, NULL, 'services.mutual.01@gmail.com', NULL, '2025-10-26 17:00:00', '2025-11-25 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:32:54', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(803, 425, 49, 30, NULL, NULL, NULL, 'nguyenhieukhagpt@gmail.com', NULL, '2025-10-27 17:00:00', '2025-11-26 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:33:29', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(804, 425, 49, 30, NULL, NULL, NULL, 'minhanh123vnnn@gmail.com', NULL, '2025-10-27 17:00:00', '2025-11-26 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:33:58', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(805, 425, 49, 30, NULL, NULL, NULL, 'duongchatgptplus@gmail.com', NULL, '2025-10-28 17:00:00', '2025-11-27 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:34:28', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(806, 425, 49, 30, NULL, NULL, NULL, 'Sirnguyen2016@gmail.com', NULL, '2025-10-28 17:00:00', '2025-11-27 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:35:09', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(807, 307, 26, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-27 17:00:00', '2025-11-26 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 17:39:46', '2025-11-06 17:39:46', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(808, 435, 81, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-11-06 17:00:00', '2025-11-16 17:00:00', 'active', 10, 0.00, 0.00, NULL, '2025-11-06 17:40:17', '2025-11-06 17:40:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(809, 423, 1, NULL, NULL, NULL, NULL, 'caotelu3@gmail.com', NULL, '2025-10-25 17:00:00', '2025-12-24 17:00:00', 'active', 60, 0.00, 0.00, NULL, '2025-11-06 17:41:06', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(811, 103, 7, 39, NULL, NULL, NULL, 'nhakhoacourse@gmail.com', NULL, '2025-10-24 17:00:00', '2026-10-24 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 17:43:25', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(812, 431, 49, 30, NULL, NULL, NULL, 'phutungfreyvn@gmail.com', NULL, '2025-10-24 17:00:00', '2026-10-24 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 17:47:28', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(813, 491, 3, NULL, NULL, NULL, NULL, 'hertagrahamdw6jy@jour.us', NULL, '2025-10-22 17:00:00', '2026-01-12 17:00:00', 'active', 82, 0.00, 0.00, NULL, '2025-11-06 17:57:08', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(814, 193, 26, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-10-27 17:00:00', '2025-11-26 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 18:05:38', '2025-11-06 18:05:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(815, 529, 7, 39, NULL, NULL, NULL, 'Thegioidecor12345@gmail.com', NULL, '2025-10-29 17:00:00', '2026-10-29 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 18:06:19', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(816, 527, 3, NULL, NULL, NULL, NULL, 'foepuc@mmocommunity.io.vn', NULL, '2025-10-30 17:00:00', '2025-11-29 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 18:07:40', '2025-11-06 18:07:40', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(817, 414, 82, NULL, NULL, NULL, NULL, 'cuong383483@gmail.com', NULL, '2025-10-30 17:00:00', '2026-10-30 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 18:09:25', '2025-11-06 18:09:25', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(818, 491, 3, NULL, NULL, NULL, NULL, 'nataymuoi@gmail.com', NULL, '2025-10-30 17:00:00', '2025-11-29 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 18:10:14', '2025-11-06 18:10:14', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(819, 425, 29, NULL, NULL, NULL, NULL, 'khiemvuonggia@gmail.com', NULL, '2025-10-30 17:00:00', '2026-10-30 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 18:11:01', '2025-11-06 18:11:01', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(820, 425, 29, NULL, NULL, NULL, NULL, 'khiemvuonggia@gmail.com', NULL, '2025-10-30 17:00:00', '2026-10-30 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 18:11:01', '2025-11-06 18:11:01', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(821, 425, 49, 30, NULL, NULL, NULL, 'coffee.dotteyvu@gmail.com', NULL, '2025-10-30 17:00:00', '2025-11-29 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 18:11:31', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(822, 425, 49, 30, NULL, NULL, NULL, 'yenly.hanoi@gmail.com', NULL, '2025-10-30 17:00:00', '2025-11-29 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 18:12:00', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(823, 430, 29, NULL, NULL, NULL, NULL, 'lethithuhuongctv@gmail.com', NULL, '2025-10-30 17:00:00', '2026-10-30 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 18:12:35', '2025-11-06 18:12:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(824, 430, 49, 30, NULL, NULL, NULL, 'tkhanhdang@gmail.com', NULL, '2025-10-30 17:00:00', '2026-05-05 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-06 18:15:29', '2025-11-08 17:54:32', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(825, 132, 58, NULL, NULL, NULL, NULL, 'titaniclover@myself.com', NULL, '2025-11-01 17:00:00', '2025-12-01 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 18:19:44', '2025-11-06 18:19:44', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(826, 132, 71, NULL, NULL, NULL, NULL, 'mai.dang@roneey.dpdns.org', NULL, '2025-10-31 17:00:00', '2025-11-30 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 18:20:59', '2025-11-06 18:20:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(827, 467, 71, NULL, NULL, NULL, NULL, 'phuong.ho@roneey.dpdns.org', NULL, '2025-11-01 17:00:00', '2025-12-01 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-06 18:21:50', '2025-11-06 18:21:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(828, 530, 4, NULL, NULL, NULL, NULL, 'anhbibi11s@gmail.com', NULL, '2025-11-02 17:00:00', '2025-12-02 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:19:13', '2025-11-08 17:19:13', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(829, 170, 28, NULL, NULL, NULL, NULL, 'ginkk90@son8.capytumbum.online', NULL, '2025-11-02 17:00:00', '2025-12-02 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:20:39', '2025-11-08 17:20:39', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(830, 531, 71, NULL, NULL, NULL, NULL, 'bao.tran@roneey.dpdns.org', NULL, '2025-11-02 17:00:00', '2025-12-02 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:21:35', '2025-11-08 17:21:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(831, 532, 3, NULL, NULL, NULL, NULL, 'dobey92184@hh7f.com', NULL, '2025-11-02 17:00:00', '2025-12-02 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:22:26', '2025-11-08 17:22:26', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(832, 104, 31, NULL, NULL, NULL, NULL, 'superphagame@gmail.com', NULL, '2025-11-03 17:00:00', '2026-11-03 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-08 17:23:03', '2025-11-08 17:23:03', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(833, 99, 24, NULL, NULL, NULL, NULL, 'auqiha43637@duongvandat.id.vn', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:24:01', '2025-11-08 17:24:01', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(834, 345, 29, NULL, NULL, NULL, NULL, 'Haphuong1101tvk@gmail.com', NULL, '2025-11-03 17:00:00', '2025-12-03 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:24:52', '2025-11-08 17:24:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(835, 225, 71, NULL, NULL, NULL, NULL, 'long4578418@tlultra2.io.vn', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:25:53', '2025-11-08 17:25:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(836, 184, 24, NULL, NULL, NULL, NULL, 'kmlrspirr@mail.cnctuankiet.com', NULL, '2025-10-20 17:00:00', '2025-11-19 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:26:43', '2025-11-08 17:26:43', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(837, 186, 8, NULL, NULL, NULL, NULL, 'johntaylorb3v@triangle', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:27:31', '2025-11-08 17:27:31', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(838, 320, 71, NULL, NULL, NULL, NULL, 'long442758@tlultra2.io.vn', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:28:35', '2025-11-08 17:28:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(839, 320, 71, NULL, NULL, NULL, NULL, 'long5694145@tlultra2.io.vn', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:28:56', '2025-11-08 17:28:56', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(840, 320, 11, NULL, NULL, NULL, NULL, 'AndiIrawan4jG0VF@hotmail.com', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:29:27', '2025-11-24 19:26:32', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(841, 335, 28, NULL, NULL, NULL, NULL, 'wkoup25@rentproxy.xyz', NULL, '2025-11-05 17:00:00', '2025-12-05 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:30:30', '2025-11-08 17:30:30', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(842, 335, 1, NULL, NULL, NULL, NULL, 'carrmaiaxbwlz2006@hotmail.com', NULL, '2025-11-05 17:00:00', '2025-12-05 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:31:13', '2025-11-08 17:31:13', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(843, 200, 49, 30, NULL, NULL, NULL, 'Leduyhungpt91@gmail.com', NULL, '2025-11-05 17:00:00', '2026-02-03 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-08 17:32:00', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(844, 533, 29, NULL, NULL, NULL, NULL, 'yenngocn203@gmail.com', NULL, '2025-11-05 17:00:00', '2026-11-05 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-08 17:32:51', '2025-11-08 17:32:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(845, 534, 29, NULL, NULL, NULL, NULL, 'ironsfleetwoodbrb1983@hotmail.com', NULL, '2025-11-05 17:00:00', '2026-11-05 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-08 17:33:50', '2025-11-08 17:33:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(846, 425, 49, 30, NULL, NULL, NULL, 'sharingchatgptplus95@gmail.com', NULL, '2025-11-03 17:00:00', '2025-12-03 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:35:11', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(847, 425, 49, 30, NULL, NULL, NULL, 'ngochuyit.ai.01@gmail.com', NULL, '2025-11-05 17:00:00', '2025-12-05 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:35:33', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(848, 425, 49, 30, NULL, NULL, NULL, 'Buidinhhoan71cg@gmail.com', NULL, '2025-11-05 17:00:00', '2025-12-05 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:35:58', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(849, 535, 3, NULL, NULL, NULL, NULL, 'bayroseyvn1977@hotmail.com', NULL, '2025-11-05 17:00:00', '2025-12-05 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:37:05', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(850, 442, 77, NULL, NULL, NULL, NULL, 'rugsai@amarhost.store', NULL, '2025-11-05 17:00:00', '2025-12-05 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:37:49', '2025-11-08 17:37:49', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(851, 536, 29, NULL, NULL, NULL, NULL, 'nyen28415@gmail.com', NULL, '2025-11-05 17:00:00', '2026-11-05 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-08 17:38:46', '2025-11-08 17:38:46', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL);
INSERT INTO `customer_services` (`id`, `customer_id`, `service_package_id`, `family_account_id`, `assigned_by`, `supplier_id`, `supplier_service_id`, `login_email`, `login_password`, `activated_at`, `expires_at`, `status`, `duration_days`, `cost_price`, `price`, `internal_notes`, `created_at`, `updated_at`, `reminder_sent`, `reminder_sent_at`, `reminder_count`, `reminder_notes`, `two_factor_code`, `recovery_codes`, `shared_account_notes`, `customer_instructions`, `password_expires_at`, `two_factor_updated_at`, `is_password_shared`, `shared_with_customers`) VALUES
(852, 325, 58, NULL, NULL, NULL, NULL, 'BernettaPannellokb67128f@bsr2w.org', NULL, '2025-11-05 17:00:00', '2025-12-05 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:39:43', '2025-11-08 17:39:43', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(853, 325, 28, NULL, NULL, NULL, NULL, 'hxopgi27ze@ttbva.anglis.io.vn', NULL, '2025-11-05 17:00:00', '2025-12-05 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:40:18', '2025-11-08 17:40:18', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(854, 414, 71, NULL, NULL, NULL, NULL, 'uyen.truong@roneey.dpdns.org', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:41:04', '2025-11-08 17:41:04', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(855, 135, 29, NULL, NULL, NULL, NULL, 'soobin66889@gmail.com', NULL, '2025-11-05 17:00:00', '2026-11-05 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-08 17:43:48', '2025-11-08 17:43:48', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(856, 134, 29, NULL, NULL, NULL, NULL, 'vanphu33@gmail.com', NULL, '2025-11-05 17:00:00', '2026-11-05 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-08 17:44:39', '2025-11-08 17:44:39', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(857, 537, 35, NULL, NULL, NULL, NULL, 'lara1@skylerr.shop', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:45:32', '2025-11-08 17:45:32', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(858, 346, 28, NULL, NULL, NULL, NULL, 'teorwgq9yh@xqgov.blasis.io.vn', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:46:20', '2025-11-08 17:46:20', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(859, 132, 29, NULL, NULL, NULL, NULL, 'tqngan86@gmail.com', NULL, '2025-11-06 17:00:00', '2026-11-06 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-08 17:47:47', '2025-11-08 17:47:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(860, 132, 20, NULL, NULL, NULL, NULL, 'sfperson180@easylangways.com', NULL, '2025-11-04 17:00:00', '2025-12-04 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:48:26', '2025-11-08 17:48:26', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(861, 538, 1, NULL, NULL, NULL, NULL, 'ladung9877o@gmail.com', NULL, '2025-08-25 17:00:00', '2025-11-23 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-08 17:50:23', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(862, 140, 4, NULL, NULL, NULL, NULL, 'lussitsalak38htkt@outlook.com', NULL, '2025-11-06 17:00:00', '2025-12-06 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:51:09', '2025-11-08 17:51:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(863, 426, 52, NULL, NULL, NULL, NULL, 'guagency5926@guagency27.io.vn', NULL, '2025-11-06 17:00:00', '2026-05-05 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-08 17:51:47', '2025-11-08 17:51:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(864, 351, 29, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-11-06 17:00:00', '2025-12-06 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:52:40', '2025-11-08 17:52:40', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(865, 459, 28, NULL, NULL, NULL, NULL, 'azubf78@rentproxy.xyz', NULL, '2025-11-06 17:00:00', '2025-12-06 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:53:13', '2025-11-08 17:53:13', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(866, 539, 29, NULL, NULL, NULL, NULL, 'trannguyenvinhquang2005@gmail.com', NULL, '2025-11-06 17:00:00', '2026-11-06 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-08 17:53:49', '2025-11-08 17:53:49', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(867, 430, 49, 30, NULL, NULL, NULL, 'tkhanhdang@gmail.com', NULL, '2025-11-06 17:00:00', '2026-05-05 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-08 17:54:32', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(868, 425, 49, 30, NULL, NULL, NULL, 'lllethang4002@gmail.com', NULL, '2025-11-06 17:00:00', '2025-12-06 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:55:16', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(869, 431, 1, NULL, NULL, NULL, NULL, 'carrmaiaxbwlz2006@hotmail.com', NULL, '2025-11-06 17:00:00', '2025-12-06 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 17:56:10', '2025-11-08 17:56:10', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(870, 266, 29, NULL, NULL, NULL, NULL, 'duykhangvanvu@gmail.com', NULL, '2025-11-07 17:00:00', '2026-11-07 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-08 18:00:23', '2025-11-08 18:00:23', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(871, 323, 21, NULL, NULL, NULL, NULL, 'munroearmandfbzo1992@hotmail.com', NULL, '2025-11-07 17:00:00', '2026-02-05 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-08 18:01:41', '2025-11-08 18:01:41', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(872, 330, 3, NULL, NULL, NULL, NULL, 'richlowelljzs1985@hotmail.com', NULL, '2025-11-07 17:00:00', '2025-12-07 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 18:02:51', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(873, 89, 49, 30, NULL, NULL, NULL, 'phuongkhoa23@gmail.com', NULL, '2025-11-07 17:00:00', '2026-02-05 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-08 18:03:49', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(874, 540, 71, NULL, NULL, NULL, NULL, 'tram.dinh@roneey.dpdns.org', NULL, '2025-11-07 17:00:00', '2025-11-27 17:00:00', 'active', 20, 0.00, 0.00, NULL, '2025-11-08 18:05:49', '2025-11-08 18:05:49', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(875, 541, 28, NULL, NULL, NULL, NULL, 'bd2f44adae@yxgge.adubadu.io.vn', NULL, '2025-11-07 17:00:00', '2025-12-07 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 18:06:57', '2025-11-08 18:06:57', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(876, 351, 71, NULL, NULL, NULL, NULL, 'minh.dinh@roneey.dpdns.org', NULL, '2025-11-07 17:00:00', '2025-11-27 17:00:00', 'active', 20, 0.00, 0.00, NULL, '2025-11-08 18:08:04', '2025-11-08 18:08:04', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(877, 303, 71, NULL, NULL, NULL, NULL, 'khanh.hoang@roneey.dpdns.org', NULL, '2025-11-07 17:00:00', '2025-11-27 17:00:00', 'active', 20, 0.00, 0.00, NULL, '2025-11-08 18:09:27', '2025-11-08 18:09:27', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(878, 542, 3, NULL, NULL, NULL, NULL, 'nguyenminhvuong20421@gmail.com', NULL, '2025-11-07 17:00:00', '2025-12-07 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-08 18:10:10', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(879, 162, 1, NULL, NULL, NULL, NULL, 'carrmaiaxbwlz2006@hotmail.com', NULL, '2025-11-07 17:00:00', '2026-01-06 17:00:00', 'active', 60, 0.00, 0.00, NULL, '2025-11-08 18:11:04', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(880, 103, 3, NULL, NULL, NULL, NULL, 'anjaturnbullsojdl2005@hotmail.com', NULL, '2025-11-08 17:00:00', '2025-12-08 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-09 18:10:14', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(881, 423, 74, NULL, NULL, NULL, NULL, 'sodienthoaik@gmail.com', NULL, '2025-11-08 17:00:00', '2026-11-08 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-09 18:10:55', '2025-11-09 18:10:55', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(882, 148, 61, NULL, NULL, NULL, NULL, 'trantn232@gmail.com', NULL, '2025-11-08 17:00:00', '2025-12-08 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-09 18:11:38', '2025-11-24 17:35:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(883, 193, 54, NULL, NULL, NULL, NULL, 'nvnkhkt@gmail.com', NULL, '2025-11-08 17:00:00', '2026-11-08 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-09 18:12:52', '2025-11-09 18:12:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(884, 193, 3, NULL, NULL, NULL, NULL, 'johnsfensai1979@hotmail.com', NULL, '2025-11-08 17:00:00', '2025-12-08 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-09 18:13:38', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(885, 543, 52, NULL, NULL, NULL, NULL, 'nhathuymusic1508@gmail.com', NULL, '2025-11-08 17:00:00', '2026-05-07 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-09 18:14:30', '2025-11-09 18:14:30', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(886, 517, 61, NULL, NULL, NULL, NULL, 'cuongshivs@gmail.com', NULL, '2025-11-08 17:00:00', '2025-12-08 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-09 18:15:25', '2025-11-24 17:37:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(887, 242, 49, 30, NULL, NULL, NULL, 'pmcam100603@gmail.com', NULL, '2025-11-08 17:00:00', '2025-12-08 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-09 18:16:39', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(888, 429, 49, 30, NULL, NULL, NULL, 'Tranthiminhanh90@gmail.com', NULL, '2025-11-08 17:00:00', '2025-12-08 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-09 18:17:21', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(889, 527, 29, NULL, NULL, NULL, NULL, 'kiennv0904@gmail.com', NULL, '2025-11-08 17:00:00', '2026-11-08 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-09 18:18:05', '2025-11-09 18:18:05', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(890, 406, 19, NULL, NULL, NULL, NULL, 'samsungwhitenguyen@gmail.com', NULL, '2025-11-08 17:00:00', '2025-12-08 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-09 18:18:58', '2025-11-09 18:18:58', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(891, 249, 1, NULL, NULL, NULL, NULL, 'doanmaitanbinhz@gmail.com', NULL, '2025-11-09 17:00:00', '2026-02-07 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-09 18:20:08', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(892, 327, 3, NULL, NULL, NULL, NULL, 'sherhaleoayi1977@hotmail.com', NULL, '2025-11-09 17:00:00', '2026-02-07 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-10 18:02:49', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(893, 320, 11, NULL, NULL, NULL, NULL, 'vallehelga0664@hotmail.com', NULL, '2025-11-10 17:00:00', '2025-12-10 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-10 18:03:39', '2025-11-10 18:03:39', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(894, 544, 71, NULL, NULL, NULL, NULL, 'tu.vo@roneey.dpdns.org', NULL, '2025-11-09 17:00:00', '2025-11-28 17:00:00', 'active', 19, 0.00, 0.00, NULL, '2025-11-10 18:04:33', '2025-11-10 18:04:33', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(895, 409, 49, 30, NULL, NULL, NULL, 'kiet.you2285@gmail.com', NULL, '2025-11-09 17:00:00', '2025-12-09 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-10 18:05:24', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(896, 517, 29, NULL, NULL, NULL, NULL, 'julie.linng@gmail.com', NULL, '2025-11-10 17:00:00', '2026-11-10 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-10 18:06:43', '2025-11-10 18:06:43', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(897, 517, 28, NULL, NULL, NULL, NULL, 'bkvhr31@boranora.com', NULL, '2025-11-10 17:00:00', '2025-12-10 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-10 18:07:15', '2025-11-10 18:07:15', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(898, 517, 49, 30, NULL, NULL, NULL, 'ngh.truclinh@gmail.com', NULL, '2025-11-10 17:00:00', '2025-12-10 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-10 18:07:49', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(899, 529, 20, NULL, NULL, NULL, NULL, 'thegioidecor12345@gmail.com', NULL, '2025-11-09 17:00:00', '2025-12-09 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-10 18:08:37', '2025-11-10 18:08:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(900, 529, 29, NULL, NULL, NULL, NULL, 'thegioidecor12345@gmail.com', NULL, '2025-11-07 17:00:00', '2026-11-07 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-10 18:09:51', '2025-11-10 18:09:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(901, 529, 52, NULL, NULL, NULL, NULL, 'thegioidecor12345@gmail.com', NULL, '2025-11-10 17:00:00', '2026-05-09 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-10 18:10:22', '2025-11-10 18:10:22', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(902, 508, 49, 30, NULL, NULL, NULL, 'Tranthicamk47@gamil.com', NULL, '2025-11-09 17:00:00', '2025-12-09 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-10 18:11:21', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(903, 326, 28, NULL, NULL, NULL, NULL, 'kimdung732024@gmail.com', NULL, '2025-11-10 17:00:00', '2025-12-10 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-10 18:12:08', '2025-11-10 18:12:08', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(904, 423, 3, NULL, NULL, NULL, NULL, 'reuelcarinagznfe1992@hotmail.com', NULL, '2025-11-10 17:00:00', '2025-12-10 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-11 19:01:23', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(905, 423, 61, 31, NULL, NULL, NULL, 'ptattmpxd01012004@gmail.com', NULL, '2025-11-10 17:00:00', '2025-12-10 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-11 19:02:04', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(906, 545, 1, NULL, NULL, NULL, NULL, 'carrmaiaxbwlz2006@hotmail.com', NULL, '2025-11-10 17:00:00', '2026-02-08 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-11 19:02:45', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(907, 546, 3, NULL, NULL, NULL, NULL, 'hiancoel@gmail.com', NULL, '2025-11-11 17:00:00', '2025-12-11 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-11 19:06:01', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(908, 546, 52, NULL, NULL, NULL, NULL, 'hiancoel@gmail.com', NULL, '2025-11-10 17:00:00', '2026-05-09 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-11 19:06:33', '2025-11-11 19:06:33', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(909, 547, 7, 39, NULL, NULL, NULL, 'M0903366155@gmail.com', NULL, '2025-11-10 17:00:00', '2026-11-10 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-11 19:07:50', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(910, 530, 1, NULL, NULL, NULL, NULL, 'carrmaiaxbwlz2006@hotmail.com', NULL, '2025-11-11 17:00:00', '2026-02-09 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-12 19:09:48', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(911, 215, 3, NULL, NULL, NULL, NULL, 'lethicamly2472004@gmail.com', NULL, '2025-11-11 17:00:00', '2025-12-11 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-12 19:10:43', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(912, 103, 3, NULL, NULL, NULL, NULL, 'walcottmavisbabca1986@hotmail.com', NULL, '2025-11-11 17:00:00', '2025-12-11 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-12 19:11:16', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(913, 136, 61, 31, NULL, NULL, NULL, 'der.laurente@gmail.com', NULL, '2025-11-11 17:00:00', '2025-12-11 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-12 19:12:00', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(914, 425, 49, 30, NULL, NULL, NULL, 'Ngochuyit.ai.09@gmail.com', NULL, '2025-11-11 17:00:00', '2025-12-11 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-12 19:12:54', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(915, 425, 49, 30, NULL, NULL, NULL, 'ngochuyit.ai.10@gmail.com', NULL, '2025-11-11 17:00:00', '2025-12-11 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-12 19:13:21', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(916, 425, 49, 30, NULL, NULL, NULL, 'Danghaiphap@dhsphue.edu.vn', NULL, '2025-11-11 17:00:00', '2025-12-11 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-12 19:13:39', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(917, 423, 7, NULL, NULL, NULL, NULL, 'minhhatlu@gmail.com', NULL, '2025-11-11 17:00:00', '2026-11-11 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-12 19:16:13', '2025-11-24 17:35:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(918, 517, 29, NULL, NULL, NULL, NULL, 'bs.truongngocvan@gmail.com', NULL, '2025-11-11 17:00:00', '2026-11-11 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-12 19:16:45', '2025-11-12 19:16:45', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(919, 349, 1, NULL, NULL, NULL, NULL, 'zellalitchfieldbpmau1981@hotmail.com', NULL, '2025-11-11 17:00:00', '2026-02-09 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-12 19:17:22', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(920, 358, 1, NULL, NULL, NULL, NULL, 'zellalitchfieldbpmau1981@hotmail.com', NULL, '2025-11-11 17:00:00', '2026-02-09 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-12 19:18:29', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(921, 91, 1, NULL, NULL, NULL, NULL, 'zellalitchfieldbpmau1981@hotmail.com', NULL, '2025-11-11 17:00:00', '2026-02-09 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-12 19:19:17', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(922, 509, 7, 39, NULL, NULL, NULL, 'nguyenngocsonkdol@gmail.com', NULL, '2025-11-10 17:00:00', '2026-11-10 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-15 18:41:21', '2025-11-24 17:29:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(923, 140, 49, 30, NULL, NULL, NULL, 'thanhlongssc@gmail.com', NULL, '2025-11-11 17:00:00', '2025-12-11 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:43:13', '2025-11-24 17:44:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(924, 140, 49, 30, NULL, NULL, NULL, 'duongdtvn2403@apd.edu.vn', NULL, '2025-11-11 17:00:00', '2025-12-11 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:43:52', '2025-11-24 17:44:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(925, 425, 28, NULL, NULL, NULL, NULL, 'alexnguyen@b-media.vn', NULL, '2025-11-12 17:00:00', '2026-05-11 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-15 18:45:35', '2025-11-15 18:45:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(926, 425, 49, 30, NULL, NULL, NULL, 'daotuuyen2022@gmail.com', NULL, '2025-11-12 17:00:00', '2025-12-12 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:46:17', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(927, 548, 3, NULL, NULL, NULL, NULL, 'tdm.chi03@gmail.com', NULL, '2025-11-13 17:00:00', '2025-12-13 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:47:41', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(928, 549, 52, NULL, NULL, NULL, NULL, 'binhducnguyen89nt@gmail.com', NULL, '2025-11-05 17:00:00', '2026-05-04 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-15 18:48:38', '2025-11-15 18:48:38', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(929, 430, 29, NULL, NULL, NULL, NULL, 'bmhieu2303@gmail.com', NULL, '2025-11-13 17:00:00', '2026-11-13 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-15 18:49:29', '2025-11-15 18:49:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(930, 430, 77, NULL, NULL, NULL, NULL, 'soonet@bongnex.store', NULL, '2025-11-14 17:00:00', '2025-12-14 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:50:05', '2025-11-15 18:50:05', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(931, 527, 1, NULL, NULL, NULL, NULL, 'wrentuttlebdg1995@hotmail.com', NULL, '2025-11-14 17:00:00', '2025-12-14 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:50:35', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(932, 527, 1, NULL, NULL, NULL, NULL, 'wrentuttlebdg1995@hotmail.com', NULL, '2025-11-14 17:00:00', '2025-12-14 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:50:54', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(933, 431, 52, NULL, NULL, NULL, NULL, 'thanhtc.digitalmkt@gmail.com', NULL, '2025-11-14 17:00:00', '2026-05-13 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-15 18:51:37', '2025-11-15 18:51:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(934, 423, 3, NULL, NULL, NULL, NULL, 'biancajustincueg1981@hotmail.com', NULL, '2025-11-14 17:00:00', '2025-12-14 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:52:25', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(935, 550, 28, NULL, NULL, NULL, NULL, 'tahiv@stecuste.cyou', NULL, '2025-11-14 17:00:00', '2025-12-14 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:53:08', '2025-11-15 18:53:08', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(936, 459, 19, NULL, NULL, NULL, NULL, 'taichel99@gmail.com', NULL, '2025-11-14 17:00:00', '2025-12-14 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:54:20', '2025-11-15 18:54:20', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(937, 462, 5, NULL, NULL, NULL, NULL, 'qtbody@gmail.com', NULL, '2025-11-14 17:00:00', '2025-12-14 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:55:12', '2025-11-15 18:55:12', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(938, 320, 3, NULL, NULL, NULL, NULL, 'datnguyen98441@mmocommunity.io.vn', NULL, '2025-11-13 17:00:00', '2025-12-13 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:55:57', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(939, 320, 3, NULL, NULL, NULL, NULL, 'datnguyen32151@mmocommunity.io.vn', NULL, '2025-11-13 17:00:00', '2025-12-13 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-15 18:56:18', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(940, 517, 52, NULL, NULL, NULL, NULL, 'thanhcong6868688@gmail.com', NULL, '2025-11-14 17:00:00', '2026-05-13 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-15 18:58:47', '2025-11-15 18:58:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(941, 139, 83, NULL, NULL, NULL, NULL, 'salanguyenduoc@gmail.com', NULL, '2025-11-15 17:00:00', '2026-11-15 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-16 15:31:22', '2025-11-16 15:31:22', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(942, 496, 71, NULL, NULL, NULL, NULL, 'binh.vo@roneey.dpdns.org', NULL, '2025-11-15 17:00:00', '2025-11-28 17:00:00', 'active', 13, 0.00, 0.00, NULL, '2025-11-16 15:32:14', '2025-11-16 15:32:14', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(943, 527, 3, NULL, NULL, NULL, NULL, 'lediepcb@gmail.com', NULL, '2025-11-15 17:00:00', '2025-12-15 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-16 15:33:03', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(944, 527, 1, NULL, NULL, NULL, NULL, 'wrentuttlebdg1995@hotmail.com', NULL, '2025-11-15 17:00:00', '2025-12-15 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-16 15:33:55', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(945, 527, 28, NULL, NULL, NULL, NULL, 'mapih@waroengmail.app', NULL, '2025-11-15 17:00:00', '2025-12-15 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-16 15:34:26', '2025-11-16 15:34:26', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(946, 431, 28, NULL, NULL, NULL, NULL, 'xowew@waroengmail.app', NULL, '2025-11-15 17:00:00', '2025-12-15 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-16 15:34:58', '2025-11-16 15:34:58', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(947, 551, 7, 40, NULL, NULL, NULL, 'hoangthanhbinh472001@gmail.com', NULL, '2025-11-15 17:00:00', '2026-11-15 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-16 15:35:44', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(948, 322, 61, 37, NULL, NULL, NULL, 'nguyenthibangngoc2000@gmail.com', NULL, '2025-11-15 17:00:00', '2025-12-15 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-16 16:33:12', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(949, 552, 71, NULL, NULL, NULL, NULL, 'dat88947570@dat4.kc4.io.vn', NULL, '2025-11-16 17:00:00', '2025-12-16 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-17 18:15:58', '2025-11-17 18:15:58', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(950, 553, 71, NULL, NULL, NULL, NULL, 'dat73774312@datne1.kc7.io.vn', NULL, '2025-11-16 17:00:00', '2025-12-16 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-17 18:16:59', '2025-11-17 18:16:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(951, 425, 49, 30, NULL, NULL, NULL, 'chatgptplusforlearning@gmail.com', NULL, '2025-11-16 17:00:00', '2025-12-16 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-17 18:17:42', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(952, 408, 49, 30, NULL, NULL, NULL, 'hung2706@gmail.com', NULL, '2025-11-16 17:00:00', '2026-05-15 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-17 18:18:27', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(953, 340, 28, NULL, NULL, NULL, NULL, 'rcgoh13@nhotv.com', NULL, '2025-11-16 17:00:00', '2025-12-16 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-17 18:19:33', '2025-11-17 18:19:33', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(954, 517, 83, NULL, NULL, NULL, NULL, 'letuananhhn@gmail.com', NULL, '2025-11-16 17:00:00', '2026-11-16 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-17 18:20:10', '2025-11-17 18:20:10', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(955, 554, 52, NULL, NULL, NULL, NULL, 'mymylyly2025@gmail.com', NULL, '2025-11-16 17:00:00', '2026-05-15 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-17 18:21:54', '2025-11-17 18:21:54', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(956, 554, 52, NULL, NULL, NULL, NULL, 'ht6815965@gmail.com', NULL, '2025-11-17 17:00:00', '2026-05-16 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-17 18:22:16', '2025-11-17 18:22:16', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(957, 554, 52, NULL, NULL, NULL, NULL, 'tvglobal2025999@gmail.com', NULL, '2025-11-17 17:00:00', '2026-05-16 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-17 18:22:40', '2025-11-17 18:22:40', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(958, 107, 52, NULL, NULL, NULL, NULL, 'dphuc305@gmail.com', NULL, '2025-11-17 17:00:00', '2026-05-16 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-17 18:23:19', '2025-11-17 18:23:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(959, 555, 29, NULL, NULL, NULL, NULL, 'robocon1984@gmail.com', NULL, '2025-11-17 17:00:00', '2026-11-17 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-18 16:41:22', '2025-11-18 16:41:22', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(960, 425, 29, NULL, NULL, NULL, NULL, 'luxlushindo@gmail.com', NULL, '2025-11-17 17:00:00', '2026-11-17 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-18 16:41:47', '2025-11-18 16:41:47', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(961, 340, 28, NULL, NULL, NULL, NULL, 'tomlinvossfgcqa1989@hotmail.com', NULL, '2025-11-17 17:00:00', '2025-12-17 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-18 16:42:49', '2025-11-18 16:42:49', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(962, 517, 54, NULL, NULL, NULL, NULL, 'amtma8383081518@gmail.com', NULL, '2025-11-17 17:00:00', '2026-11-17 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-18 16:43:35', '2025-11-18 16:43:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(963, 317, 1, NULL, NULL, NULL, NULL, 'tomlinvossfgcqa1989@hotmail.com', NULL, '2025-11-17 17:00:00', '2026-02-15 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-18 16:44:15', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(964, 556, 19, NULL, NULL, NULL, NULL, 'lamnhuchoi4.0@gmail.com', NULL, '2025-11-17 17:00:00', '2025-12-17 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-18 16:45:10', '2025-11-18 16:45:10', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(965, 556, 52, NULL, NULL, NULL, NULL, 'hungvipngocdong@gmail.com', NULL, '2025-11-17 17:00:00', '2026-05-16 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-18 16:46:02', '2025-11-18 16:46:02', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(966, 527, 61, NULL, NULL, NULL, NULL, 'mytrang01k@gmail.com', NULL, '2025-11-17 17:00:00', '2025-12-17 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-18 16:47:20', '2025-11-24 17:37:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(967, 527, 28, NULL, NULL, NULL, NULL, '5oaewba1ty7vc@longsbkt.xyz', NULL, '2025-11-17 17:00:00', '2025-12-17 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-18 16:47:45', '2025-11-18 16:47:45', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(968, 557, 7, 40, NULL, NULL, NULL, 'Beotoetls95@gmail.com', NULL, '2025-11-17 17:00:00', '2026-02-15 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-18 16:48:38', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(969, 513, 71, NULL, NULL, NULL, NULL, 'mpvln09@proxy4gs.com', NULL, '2025-11-17 17:00:00', '2025-12-17 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-18 16:49:59', '2025-11-18 16:49:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(970, 513, 28, NULL, NULL, NULL, NULL, 'dat12123411@dat1.kc6.io.vn', NULL, '2025-11-17 17:00:00', '2025-12-17 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-18 16:50:20', '2025-11-18 16:50:20', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(971, 132, 52, NULL, NULL, NULL, NULL, 'nganday02@gmail.com', NULL, '2025-11-17 17:00:00', '2026-05-16 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-18 16:51:05', '2025-11-18 16:51:05', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(972, 558, 52, NULL, NULL, NULL, NULL, 'ty.vietbuilding@gmail.com', NULL, '2025-11-17 17:00:00', '2026-05-16 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-18 16:51:42', '2025-11-18 16:51:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(973, 270, 83, NULL, NULL, NULL, NULL, 'khoathiet89@gmail.com', NULL, '2025-11-19 17:00:00', '2026-11-19 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-19 17:33:50', '2025-11-19 17:33:50', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(974, 559, 61, 37, NULL, NULL, NULL, 'hungphan2162005@gmail.com', NULL, '2025-11-19 17:00:00', '2025-12-19 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-19 17:34:36', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(975, 508, 28, NULL, NULL, NULL, NULL, 'pusog@waroengpt.com', NULL, '2025-11-18 17:00:00', '2025-12-18 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-19 17:35:16', '2025-11-19 17:35:16', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(976, 517, 61, NULL, NULL, NULL, NULL, 'nguyenhuynhbaolong82@gmail.com', NULL, '2025-11-18 17:00:00', '2025-12-18 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-19 17:35:59', '2025-11-24 17:37:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(977, 517, 61, NULL, NULL, NULL, NULL, 'lptt0396526886@gmail.com', NULL, '2025-11-18 17:00:00', '2025-12-18 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-19 17:36:45', '2025-11-24 17:37:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(978, 107, 35, NULL, NULL, NULL, NULL, 'jessdantonio@glintwater.com', NULL, '2025-11-18 17:00:00', '2025-12-18 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-19 17:37:33', '2025-11-19 17:37:33', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(979, 320, 71, NULL, NULL, NULL, NULL, 'hanam000145@hanam2.io.vn', NULL, '2025-11-19 17:00:00', '2025-12-19 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 05:22:14', '2025-11-21 05:22:14', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(980, 517, 52, NULL, NULL, NULL, NULL, 'mifit@waroengpt.com', NULL, '2025-11-19 17:00:00', '2025-12-19 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 05:22:53', '2025-11-21 05:22:53', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(981, 544, 7, 40, NULL, NULL, NULL, 'leanhthang0903@gmail.com', NULL, '2025-11-19 17:00:00', '2026-11-19 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-21 05:23:49', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(982, 139, 20, NULL, NULL, NULL, NULL, 'reedrushworthconok1989@hotmail.com', NULL, '2025-11-19 17:00:00', '2025-12-19 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 05:24:35', '2025-11-21 05:24:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(983, 430, 29, NULL, NULL, NULL, NULL, 'Hoangdaithanhlong@gmail.com', NULL, '2025-11-20 17:00:00', '2026-11-20 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-21 05:25:18', '2025-11-21 05:25:18', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(984, 527, 28, NULL, NULL, NULL, NULL, 'juyus@waroengpt.com', NULL, '2025-11-20 17:00:00', '2025-12-20 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 05:25:51', '2025-11-21 05:25:51', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(985, 553, 28, NULL, NULL, NULL, NULL, 'yulij@waroengpt.com', NULL, '2025-11-19 17:00:00', '2025-12-19 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 05:26:19', '2025-11-21 05:26:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(986, 195, 54, NULL, NULL, NULL, NULL, 'Maimanhtuantkgg@gmail.com', NULL, '2025-11-19 17:00:00', '2026-11-19 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-21 05:26:59', '2025-11-21 05:26:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(987, 193, 71, NULL, NULL, NULL, NULL, 'hanam5352514@hn.hanam2.io.vn', NULL, '2025-11-19 17:00:00', '2025-12-19 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 05:27:58', '2025-11-21 05:27:58', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(988, 215, 28, NULL, NULL, NULL, NULL, 'gamey@waroengpt.com', NULL, '2025-11-20 17:00:00', '2025-12-20 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 17:09:35', '2025-11-21 17:09:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(989, 425, 49, 30, NULL, NULL, NULL, 'Tungmozu@gmail.com', NULL, '2025-11-20 17:00:00', '2025-12-20 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 17:10:09', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(990, 517, 61, NULL, NULL, NULL, NULL, 'anhhtph51128@gmail.com', NULL, '2025-11-21 17:00:00', '2025-12-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 17:10:44', '2025-11-24 17:37:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(991, 517, 7, NULL, NULL, NULL, NULL, 'lenhung1550@gmail.com', NULL, '2025-11-20 17:00:00', '2026-11-20 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-21 17:14:51', '2025-11-24 17:35:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(992, 523, 61, 31, NULL, NULL, NULL, 'Rynopham@gmail.com', NULL, '2025-11-20 17:00:00', '2025-12-20 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-21 17:15:40', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(993, 320, 71, NULL, NULL, NULL, NULL, 'hanam53428@tk.veone.io.vn', NULL, '2025-11-21 17:00:00', '2025-12-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-22 17:53:15', '2025-11-22 17:53:15', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(994, 320, 71, NULL, NULL, NULL, NULL, 'hanam1999@tk.veone.io.vn', NULL, '2025-11-21 17:00:00', '2025-12-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-22 17:53:42', '2025-11-22 17:53:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(995, 470, 28, NULL, NULL, NULL, NULL, 'lomiy@waroengpt.com', NULL, '2025-11-21 17:00:00', '2025-12-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-22 17:54:19', '2025-11-22 17:54:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(996, 325, 28, NULL, NULL, NULL, NULL, 'lolez@waroengpt.com', NULL, '2025-11-21 17:00:00', '2025-12-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-22 17:54:49', '2025-11-22 17:54:49', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(997, 353, 49, 30, NULL, NULL, NULL, 'avbuilding18@gmail.com', NULL, '2025-11-21 17:00:00', '2026-05-20 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-22 17:57:31', '2025-11-24 19:03:59', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(998, 353, 7, NULL, NULL, NULL, NULL, 'avbuilding18@gmail.com', NULL, '2025-11-21 17:00:00', '2026-11-21 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-22 17:58:04', '2025-11-24 17:35:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(999, 353, 54, NULL, NULL, NULL, NULL, 'avbuilding18@gmail.com', NULL, '2025-11-21 17:00:00', '2026-11-21 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-22 17:58:42', '2025-11-22 17:58:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1000, 560, 28, NULL, NULL, NULL, NULL, 'mezap@waroengpt.com', NULL, '2025-11-21 17:00:00', '2025-12-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-22 17:59:57', '2025-11-22 17:59:57', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1001, 517, 28, NULL, NULL, NULL, NULL, 'mebim@waroengpt.com', NULL, '2025-11-21 17:00:00', '2025-12-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-22 18:01:30', '2025-11-22 18:01:30', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1002, 430, 28, NULL, NULL, NULL, NULL, 'bolom@waroengpt.com', NULL, '2025-11-21 17:00:00', '2025-12-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-22 18:02:10', '2025-11-22 18:02:10', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1003, 430, 52, NULL, NULL, NULL, NULL, 'lethikimthoa03031988@gmail.com', NULL, '2025-11-21 17:00:00', '2026-05-20 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-22 18:02:42', '2025-11-22 18:02:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1004, 527, 71, NULL, NULL, NULL, NULL, 'hanam333@veoshop.io.vn', NULL, '2025-11-21 17:00:00', '2025-12-21 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-22 18:03:26', '2025-11-22 18:03:26', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1005, 561, 28, NULL, NULL, NULL, NULL, 'rores@waroengpt.com', NULL, '2025-11-22 17:00:00', '2025-12-22 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-23 16:10:28', '2025-11-23 16:10:28', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1006, 547, 61, NULL, NULL, NULL, NULL, 'm0903366155@gmail.com', NULL, '2025-11-22 17:00:00', '2025-12-22 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-23 16:11:15', '2025-11-24 17:35:19', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1007, 430, 18, NULL, NULL, NULL, NULL, 'lethikimthoa03031988@gmail.com', NULL, '2025-11-22 17:00:00', '2025-12-22 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-23 16:12:31', '2025-11-23 16:12:31', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1008, 562, 28, NULL, NULL, NULL, NULL, '2qltxvufnf@y1hr.capcut11.name.ng', NULL, '2025-11-22 17:00:00', '2025-12-22 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-23 16:13:32', '2025-11-23 16:13:32', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1009, 563, 49, 30, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-11-22 17:00:00', '2025-12-24 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-23 16:14:39', '2025-11-24 18:20:18', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1010, 563, 21, NULL, NULL, NULL, NULL, 'arj6zvbqu@nhotv.com', NULL, '2025-11-22 17:00:00', '2026-02-20 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-23 16:15:17', '2025-11-23 16:15:17', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1011, 414, 1, NULL, NULL, NULL, NULL, 'ladung9877o@gmail.com', NULL, '2025-11-22 17:00:00', '2026-02-20 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-23 16:16:00', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1012, 538, 1, NULL, NULL, NULL, NULL, 'ladung9877o@gmail.com', NULL, '2025-11-22 17:00:00', '2026-02-20 17:00:00', 'active', 90, 0.00, 0.00, NULL, '2025-11-23 16:16:45', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1013, 564, 1, NULL, NULL, NULL, NULL, 'tomlinvossfgcqa1989@hotmail.com', NULL, '2025-11-22 17:00:00', '2025-12-22 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-23 16:37:36', '2025-11-24 19:01:29', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1014, 565, 3, NULL, NULL, NULL, NULL, 'hongdung.dd@gmail.com', NULL, '2025-11-23 17:00:00', '2025-12-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-24 16:14:14', '2025-11-24 16:14:14', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1015, 423, 3, NULL, NULL, NULL, NULL, 'tudoryuleyszc1977@hotmail.com', NULL, '2025-11-23 17:00:00', '2025-12-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-24 16:15:03', '2025-11-24 18:57:42', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1016, 527, 28, NULL, NULL, NULL, NULL, 'zanet@waroengpt.com', NULL, '2025-11-23 17:00:00', '2025-12-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-24 16:15:45', '2025-11-24 16:15:45', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1017, 527, 71, NULL, NULL, NULL, NULL, 'hanam999@admin.shophanam.io.vn', NULL, '2025-11-23 17:00:00', '2025-12-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-24 16:16:18', '2025-11-24 16:16:18', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1018, 426, 81, NULL, NULL, NULL, NULL, 'kiendtph49182@gmail.com', NULL, '2025-11-23 17:00:00', '2025-11-24 17:00:00', 'active', 1, 0.00, 0.00, NULL, '2025-11-24 16:16:54', '2025-11-24 16:16:54', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1019, 430, 29, NULL, NULL, NULL, NULL, 'hoanganhdo03@gmail.com', NULL, '2025-11-23 17:00:00', '2026-11-23 17:00:00', 'active', 365, 0.00, 0.00, NULL, '2025-11-24 16:17:37', '2025-11-24 16:17:37', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1020, 430, 52, NULL, NULL, NULL, NULL, 'tristonmann87@chrmo.cc10.name.ng', NULL, '2025-11-23 17:00:00', '2026-05-22 17:00:00', 'active', 180, 0.00, 0.00, NULL, '2025-11-24 16:18:20', '2025-11-24 16:18:20', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1021, 425, 49, 30, NULL, NULL, NULL, 'hocaichatgpt2025@gmail.com', NULL, '2025-11-23 17:00:00', '2025-12-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-24 16:18:53', '2025-11-24 17:29:52', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1022, 194, 28, NULL, NULL, NULL, NULL, 'leref@waroengpt.com', NULL, '2025-11-23 17:00:00', '2025-12-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-24 16:19:35', '2025-11-24 16:19:35', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(1023, 472, 28, NULL, NULL, NULL, NULL, 'zanet@waroengpt.com', NULL, '2025-11-23 17:00:00', '2025-12-23 17:00:00', 'active', 30, 0.00, 0.00, NULL, '2025-11-24 16:20:09', '2025-11-24 16:20:09', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_services_backup`
--

CREATE TABLE `customer_services_backup` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `service_package_id` bigint UNSIGNED NOT NULL,
  `login_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activated_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `status` enum('active','expired','suspended','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `family_accounts`
--

CREATE TABLE `family_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `family_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên family plan',
  `family_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã family plan duy nhất',
  `service_package_id` bigint UNSIGNED NOT NULL,
  `owner_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email đăng nhập chính của family owner',
  `owner_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên chủ gia đình',
  `max_members` int NOT NULL DEFAULT '6' COMMENT 'Số thành viên tối đa cho phép',
  `current_members` int NOT NULL DEFAULT '0' COMMENT 'Số thành viên hiện tại',
  `activated_at` datetime NOT NULL COMMENT 'Ngày kích hoạt family plan',
  `expires_at` datetime NOT NULL COMMENT 'Ngày hết hạn family plan',
  `status` enum('active','expired','suspended','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `family_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú về family account',
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú nội bộ',
  `family_settings` json DEFAULT NULL COMMENT 'Cài đặt family (JSON)',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `managed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Đang đổ dữ liệu cho bảng `family_accounts`
--

INSERT INTO `family_accounts` (`id`, `family_name`, `family_code`, `service_package_id`, `owner_email`, `owner_name`, `max_members`, `current_members`, `activated_at`, `expires_at`, `status`, `family_notes`, `internal_notes`, `family_settings`, `created_by`, `managed_by`, `created_at`, `updated_at`) VALUES
(30, 'Chat GPT Plant', 'FAM-BNCCWNDG', 49, 'kiendtph49182@gmail.com', 'GPT Plant', 100, 58, '2025-09-01 22:23:41', '2025-12-01 00:00:00', 'active', NULL, NULL, NULL, 1, 1, '2025-09-01 15:23:41', '2025-11-24 17:56:34'),
(31, 'GPT TEAM', 'FAM-7WN1CZY4', 61, 'kiendtph49182@gmail.com', 'Kiên Đỗ Trung', 20, 9, '2025-09-01 22:24:44', '2025-12-01 00:00:00', 'active', NULL, NULL, NULL, 1, 1, '2025-09-01 15:24:44', '2025-11-24 17:38:21'),
(32, 'GG ONE 4(quachanhmaker@gmail.com)', 'FAM-REXZKAIA', 7, 'quachanhmaker@gmail.com', 'kiên', 5, 5, '2025-09-03 23:18:37', '2026-08-30 00:00:00', 'active', 'phongtran110905', NULL, NULL, 1, 1, '2025-09-03 16:18:37', '2025-09-12 17:21:51'),
(33, 'GG ONE 2 (lowkeyzz2008@gmail.com)', 'FAM-6DRVSBPI', 7, 'lowkeyzz2008@gmail.com', 'kiên', 5, 5, '2025-09-03 23:18:38', '2026-08-30 00:00:00', 'active', 'phongtran110905', NULL, NULL, 1, 1, '2025-09-03 16:18:38', '2025-09-12 16:57:50'),
(34, 'GG ONE 5 ( buimyst952485@gmail.com)', 'FAM-XSC0VYYM', 7, 'buimyst952485@gmail.com', 'Kiên Đỗ Trung', 5, 5, '2025-09-12 23:34:22', '2026-09-12 00:00:00', 'active', 'mật khẩu :Thinh@123', NULL, NULL, 1, 1, '2025-09-12 16:34:22', '2025-09-25 14:25:15'),
(35, 'GG ONE 1(kiendtph49182@gmail.com)', 'FAM-OVRHI2WE', 7, 'kiendtph49182@gmail.com', 'đỗ trung kiên', 5, 4, '2025-09-13 09:35:36', '2026-09-13 00:00:00', 'active', NULL, NULL, NULL, 1, 1, '2025-09-13 02:35:36', '2025-09-13 03:05:03'),
(36, 'GG ONE 3( geminipro.1661@gmail.com)', 'FAM-7Q1RZDLQ', 7, 'geminipro.1661@gmail.com', 'Kiên Đỗ Trung', 2, 2, '2025-09-13 09:36:30', '2026-09-13 00:00:00', 'active', NULL, 'hihi', NULL, 1, 1, '2025-09-13 02:36:31', '2025-09-13 03:07:05'),
(37, 'team gpt 1', 'FAM-YBZCC1WY', 61, 'tranthaomrese7755@gmail.com', 'Đỗ Trung Kiên', 7, 6, '2025-09-18 00:54:28', '2025-10-14 00:00:00', 'active', 'có cc', NULL, NULL, 1, 1, '2025-09-17 17:54:28', '2025-11-19 17:35:59'),
(38, 'GG ONE 6', 'FAM-JQVAFVEP', 7, 'khoathanhphuc@gmail.com', 'Đỗ Trung Kiên', 5, 5, '2025-09-25 23:21:03', '2026-09-25 00:00:00', 'active', 'khoathanhphuc@gmail.com	|	kimlong@27d7	|	khoathanhphuc@ammunro.com', 'https://tinyhost.shop/', NULL, 1, 1, '2025-09-25 16:21:03', '2025-10-09 17:18:31'),
(39, 'GG ONE 7 _kienchatgpt945@gmail.com', 'FAM-AT44FR8F', 7, 'kienchatgpt945@gmail.com', 'Đỗ Trung Kiên', 5, 5, '2025-10-11 23:59:07', '2026-10-11 00:00:00', 'active', NULL, NULL, NULL, 1, 1, '2025-10-11 16:59:07', '2025-11-11 19:07:50'),
(40, 'gg one 8 ( vietanhc190@gmail.com)', 'FAM-RCFZLSX4', 7, 'vietanhc190@gmail.com', 'Đỗ Trung Kiên', 5, 5, '2025-11-13 02:15:27', '2026-11-12 00:00:00', 'active', NULL, NULL, NULL, 1, 1, '2025-11-12 19:15:27', '2025-11-21 05:23:49'),
(41, 'GG ONE 9( anhvandz.2bn@gmail.com)', 'FAM-HNPOY0EU', 7, 'anhvandz.2bn@gmail.com', 'Đỗ Trung Kiên', 5, 2, '2025-11-22 00:14:02', '2026-12-21 00:00:00', 'active', NULL, NULL, NULL, 1, 1, '2025-11-21 17:14:02', '2025-11-22 17:58:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `family_members`
--

CREATE TABLE `family_members` (
  `id` bigint UNSIGNED NOT NULL,
  `family_account_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `member_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `member_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email cá nhân của thành viên',
  `member_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member' COMMENT 'Vai trò: owner, admin, member',
  `status` enum('active','inactive','removed','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `permissions` json DEFAULT NULL COMMENT 'Quyền hạn của thành viên (JSON)',
  `last_active_at` datetime DEFAULT NULL COMMENT 'Lần hoạt động cuối',
  `removed_at` datetime DEFAULT NULL COMMENT 'Ngày bị xóa khỏi family',
  `usage_count` int NOT NULL DEFAULT '0' COMMENT 'Số lần sử dụng dịch vụ',
  `first_usage_at` datetime DEFAULT NULL COMMENT 'Lần sử dụng đầu tiên',
  `last_usage_at` datetime DEFAULT NULL COMMENT 'Lần sử dụng cuối',
  `member_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú về thành viên',
  `expires_at` timestamp NULL DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú nội bộ',
  `added_by` bigint UNSIGNED DEFAULT NULL,
  `removed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `family_members`
--

INSERT INTO `family_members` (`id`, `family_account_id`, `customer_id`, `member_name`, `member_email`, `member_role`, `status`, `permissions`, `last_active_at`, `removed_at`, `usage_count`, `first_usage_at`, `last_usage_at`, `member_notes`, `expires_at`, `start_date`, `end_date`, `internal_notes`, `added_by`, `removed_by`, `created_at`, `updated_at`, `joined_at`) VALUES
(49, 30, 425, 'MạNh TuấN', 'Tungmozu@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-21', '2025-12-21', NULL, NULL, NULL, '2025-09-01 15:25:55', '2025-11-21 17:10:09', '2025-09-01 15:25:55'),
(50, 31, 425, 'MạNh TuấN', 'hocaichatgpt2025@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-24', '2025-12-24', NULL, NULL, NULL, '2025-09-01 15:36:01', '2025-11-24 16:18:53', '2025-09-01 15:36:01'),
(51, 32, 132, 'Hưng Ngân', 'tqngan86@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-03', '2026-09-03', NULL, NULL, NULL, '2025-09-03 16:19:47', '2025-09-03 16:19:47', '2025-09-03 16:19:47'),
(52, 30, 429, 'BắC', 'Tranthiminhanh90@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-09', '2025-12-09', NULL, NULL, NULL, '2025-09-03 16:20:41', '2025-11-09 18:17:21', '2025-09-03 16:20:41'),
(53, 32, 433, 'Thongth Petrolimex', 'thongth@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-03', '2026-09-03', NULL, NULL, NULL, '2025-09-03 16:46:37', '2025-09-03 16:46:37', '2025-09-03 16:46:37'),
(54, 31, 423, 'Trần Văn Mạnh', 'ptattmpxd01012004@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-11', '2025-12-11', NULL, NULL, NULL, '2025-09-03 16:51:10', '2025-11-11 19:02:04', '2025-09-03 16:51:10'),
(55, 33, 435, 'Toán Math', 'nguyenthilien25091995@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-26', '2026-07-26', NULL, NULL, NULL, '2025-09-03 16:56:27', '2025-09-03 16:56:27', '2025-09-03 16:56:27'),
(56, 33, 148, 'Nguyên Phương', 'tnphuonga2nd2018@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-08-08', '2026-08-08', NULL, NULL, NULL, '2025-09-03 17:15:57', '2025-09-03 17:15:57', '2025-09-03 17:15:57'),
(57, 33, 356, 'Trung Huỳnh', 'Htrung27791@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-23', '2026-07-23', NULL, NULL, NULL, '2025-09-03 17:17:51', '2025-09-03 17:17:51', '2025-09-03 17:17:51'),
(58, 33, 257, 'Thu', 'huethutrieuphu92@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-16', '2026-07-16', NULL, NULL, NULL, '2025-09-03 17:18:49', '2025-09-03 17:18:49', '2025-09-03 17:18:49'),
(59, 33, 437, 'hoanglongpro121@gmail.com', 'hoanglongpro121@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-20', '2026-07-20', NULL, NULL, NULL, '2025-09-03 17:31:12', '2025-09-03 17:31:12', '2025-09-03 17:31:12'),
(60, 34, 88, 'Trần Minh Tuân', 'minhtuanpro134@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-12', '2026-09-12', NULL, NULL, NULL, '2025-09-12 16:35:08', '2025-09-12 16:35:08', '2025-09-12 16:35:08'),
(61, 34, 166, 'Hoàng Nam', 'hoangnamhg1212@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-12', '2026-09-12', NULL, NULL, NULL, '2025-09-12 16:36:01', '2025-09-12 16:36:01', '2025-09-12 16:36:01'),
(62, 32, 333, 'Thanh Nam', 'huyhoang22334@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-31', '2025-11-29', NULL, NULL, NULL, '2025-09-12 17:00:11', '2025-09-12 17:00:11', '2025-09-12 17:00:11'),
(63, 32, 450, 'tailieulp23@gmail.com', 'tailieulp23@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-11', '2026-09-11', NULL, NULL, NULL, '2025-09-12 17:01:59', '2025-09-12 17:01:59', '2025-09-12 17:01:59'),
(64, 32, 140, 'Duong Do', 'duongdtvn2@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-08-26', '2026-08-26', NULL, NULL, NULL, '2025-09-12 17:02:57', '2025-09-12 17:24:44', '2025-09-12 17:02:57'),
(65, 35, 303, 'Alex Nguyễn', 'alexnguyen.mta.8889@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-11', '2026-07-11', NULL, NULL, NULL, '2025-09-13 03:00:53', '2025-09-13 03:00:53', '2025-09-13 03:00:53'),
(66, 35, 451, 'thailien.231197@gmail.com', 'thailien.231197@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-18', '2026-07-18', NULL, NULL, NULL, '2025-09-13 03:03:43', '2025-09-13 03:03:43', '2025-09-13 03:03:43'),
(67, 35, 452, 'Tube238@Gmail.Com', 'tube238@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-10', '2026-07-10', NULL, NULL, NULL, '2025-09-13 03:04:33', '2025-09-13 03:04:33', '2025-09-13 03:04:33'),
(68, 35, 154, 'Đinh Trường Lộc', 'Dinhtruongloc.1996@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-15', '2026-07-15', NULL, NULL, NULL, '2025-09-13 03:05:03', '2025-09-13 03:05:03', '2025-09-13 03:05:03'),
(69, 36, 324, 'Dư Quang Quý Pcna', 'Quypcna@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-08-23', '2026-08-23', NULL, NULL, NULL, '2025-09-13 03:06:17', '2025-09-13 03:06:17', '2025-09-13 03:06:17'),
(70, 36, 102, 'Việt Phương', 'chungvietphuong2311@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-08-21', '2026-08-21', NULL, NULL, NULL, '2025-09-13 03:07:05', '2025-09-13 03:07:05', '2025-09-13 03:07:05'),
(71, 34, 453, 'Phamthiphuonganh9999@gmail.com', 'Phamthiphuonganh9999@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-15', '2026-09-15', NULL, NULL, NULL, '2025-09-17 17:32:41', '2025-09-17 17:32:41', '2025-09-17 17:32:41'),
(72, 34, 456, 'miniriviu@gmail.com', 'miniriviu@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-17', '2026-09-17', NULL, NULL, NULL, '2025-09-17 17:35:32', '2025-09-17 17:35:32', '2025-09-17 17:35:32'),
(73, 37, 462, 'Trung HoàNg', 'ngoxuan287@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-15', '2025-10-15', NULL, NULL, NULL, '2025-09-17 17:56:24', '2025-09-17 17:56:24', '2025-09-17 17:56:24'),
(74, 30, 464, 'Cao Quý', 'kiendtph49182@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-15', '2025-10-15', NULL, NULL, NULL, '2025-09-17 18:03:55', '2025-09-17 18:03:55', '2025-09-17 18:03:55'),
(75, 34, 481, 'Johntâm - Kent Nguyen International Ltd', 'kimngoc.kentenglish@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-23', '2026-09-23', NULL, NULL, NULL, '2025-09-25 14:25:15', '2025-09-25 14:25:15', '2025-09-25 14:25:15'),
(76, 38, 483, 'sighhh1509@gmail.com', 'sighhh1509@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-19', '2026-09-19', NULL, NULL, NULL, '2025-09-25 16:22:50', '2025-09-25 16:22:50', '2025-09-25 16:22:50'),
(77, 38, 484, 'hungpchy.pixie@gmail.com', 'hungpchy.pixie@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-09-19', '2026-09-19', NULL, NULL, NULL, '2025-09-25 16:23:27', '2025-09-25 16:23:27', '2025-09-25 16:23:27'),
(78, 30, 431, 'NgọC HạNh', 'phutungfreyvn@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-25', '2026-10-25', NULL, NULL, NULL, '2025-09-28 14:31:29', '2025-11-06 17:47:28', '2025-09-28 14:31:29'),
(79, 38, 87, 'Trần Nguyễn Minh Thiên', 'vhnauy@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-01', '2026-10-01', NULL, NULL, NULL, '2025-09-30 17:11:06', '2025-09-30 17:11:06', '2025-09-30 17:11:06'),
(80, 30, 132, 'Hưng Ngân', 'ngantq-tng@abic.com.vn', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-01', '2025-11-30', NULL, NULL, NULL, '2025-09-30 17:13:21', '2025-09-30 17:13:21', '2025-09-30 17:13:21'),
(81, 37, 158, 'Bình Nguyễn', 'Xuanbinh0295@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-02', '2025-11-01', NULL, NULL, NULL, '2025-10-02 15:43:26', '2025-10-02 15:43:26', '2025-10-02 15:43:26'),
(82, 38, 491, 'Phạm Ngọc Hoàng Tú', 'Tutbtv@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-01', '2026-10-01', NULL, NULL, NULL, '2025-10-02 15:47:22', '2025-10-02 15:47:22', '2025-10-02 15:47:22'),
(83, 37, 423, 'Trần Văn Mạnh', 'misuclosetshop@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-03', '2025-11-02', NULL, NULL, NULL, '2025-10-03 16:18:02', '2025-10-03 16:18:02', '2025-10-03 16:18:02'),
(84, 38, 499, 'PhạM XuâN ThàNh', 'thanhphamxuan2042005@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-10', '2026-10-10', NULL, NULL, NULL, '2025-10-09 17:18:31', '2025-10-09 17:18:31', '2025-10-09 17:18:31'),
(85, 30, 242, 'Phạm Mạnh Cầm', 'pmcam100603@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-09', '2025-12-09', NULL, NULL, NULL, '2025-10-10 16:11:31', '2025-11-09 18:16:39', '2025-10-10 16:11:31'),
(86, 39, 506, 'Nhu Manh Khanh', 'nhumanhkhanh@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-11', '2026-10-11', NULL, NULL, NULL, '2025-10-11 17:00:04', '2025-10-11 17:00:04', '2025-10-11 17:00:04'),
(87, 30, 430, 'NguyễN ChíNh', 'tkhanhdang@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-07', '2026-05-06', NULL, NULL, NULL, '2025-10-17 08:25:55', '2025-11-08 17:54:32', '2025-10-17 08:25:55'),
(88, 39, 509, 'Huy Cao', 'huycao0356@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-15', '2026-10-15', NULL, NULL, NULL, '2025-10-17 08:37:59', '2025-10-17 08:37:59', '2025-10-17 08:37:59'),
(89, 30, 140, 'Duong Do', 'duongdtvn2403@apd.edu.vn', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-12', '2025-12-12', NULL, NULL, NULL, '2025-10-17 08:40:00', '2025-11-15 18:43:52', '2025-10-17 08:40:00'),
(90, 30, 183, 'Quynh Nguyen', 'ngocquynh911@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-20', '2026-04-18', NULL, NULL, NULL, '2025-10-20 17:12:24', '2025-10-20 17:12:24', '2025-10-20 17:12:24'),
(91, 30, 423, 'Trần Văn Mạnh', 'dangkhang16125@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-22', '2025-11-21', NULL, NULL, NULL, '2025-10-21 18:08:47', '2025-10-21 18:08:47', '2025-10-21 18:08:47'),
(92, 30, 517, 'HoàNg Thế Anh', 'ngh.truclinh@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-11', '2025-12-11', NULL, NULL, NULL, '2025-10-21 18:11:24', '2025-11-10 18:07:49', '2025-10-21 18:11:24'),
(93, 31, 523, 'Nhat Anh Pham', 'Rynopham@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-21', '2025-12-21', NULL, NULL, NULL, '2025-11-03 15:24:25', '2025-11-21 17:15:40', '2025-11-03 15:24:25'),
(94, 30, 527, 'Le Mai', 'ngachatgpt82@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-23', '2025-11-22', NULL, NULL, NULL, '2025-11-06 16:36:29', '2025-11-06 16:36:29', '2025-11-06 16:36:29'),
(95, 30, 308, 'Trang Cao', 'shopgauyeu2025@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-24', '2026-10-24', NULL, NULL, NULL, '2025-11-06 16:37:30', '2025-11-06 16:37:30', '2025-11-06 16:37:30'),
(96, 30, 176, 'Tao Hai Anh', 'haianh2602003@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-27', '2025-11-26', NULL, NULL, NULL, '2025-11-06 16:42:33', '2025-11-06 16:42:33', '2025-11-06 16:42:33'),
(98, 39, 103, 'Mai Hoàng Tú', 'nhakhoacourse@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-25', '2026-10-25', NULL, NULL, NULL, '2025-11-06 17:43:25', '2025-11-06 17:43:25', '2025-11-06 17:43:25'),
(99, 39, 529, 'Tuệ LộC', 'Thegioidecor12345@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-10-30', '2026-10-30', NULL, NULL, NULL, '2025-11-06 18:06:19', '2025-11-06 18:06:19', '2025-11-06 18:06:19'),
(100, 30, 200, 'Hưng Lê', 'Leduyhungpt91@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-06', '2026-02-04', NULL, NULL, NULL, '2025-11-08 17:32:00', '2025-11-08 17:32:00', '2025-11-08 17:32:00'),
(101, 30, 89, 'Lâm Phương', 'phuongkhoa23@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-08', '2026-02-06', NULL, NULL, NULL, '2025-11-08 18:03:49', '2025-11-08 18:03:49', '2025-11-08 18:03:49'),
(102, 31, 148, 'Nguyên Phương', 'trantn232@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-09', '2025-12-09', NULL, NULL, NULL, '2025-11-09 18:11:38', '2025-11-09 18:11:38', '2025-11-09 18:11:38'),
(103, 31, 517, 'HoàNg Thế Anh', 'anhhtph51128@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-22', '2025-12-22', NULL, NULL, NULL, '2025-11-09 18:15:25', '2025-11-21 17:10:44', '2025-11-09 18:15:25'),
(104, 30, 409, 'Thuong Kiet', 'kiet.you2285@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-10', '2025-12-10', NULL, NULL, NULL, '2025-11-10 18:05:24', '2025-11-10 18:05:24', '2025-11-10 18:05:24'),
(105, 30, 508, 'Anh Zồ', 'Tranthicamk47@gamil.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-10', '2025-12-10', NULL, NULL, NULL, '2025-11-10 18:11:21', '2025-11-10 18:11:21', '2025-11-10 18:11:21'),
(106, 39, 547, 'Ngô Thành Danh', 'M0903366155@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-11', '2026-11-11', NULL, NULL, NULL, '2025-11-11 19:07:50', '2025-11-11 19:07:50', '2025-11-11 19:07:50'),
(107, 31, 136, 'Ivy Preparation', 'der.laurente@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-12', '2025-12-12', NULL, NULL, NULL, '2025-11-12 19:12:00', '2025-11-12 19:12:00', '2025-11-12 19:12:00'),
(108, 40, 423, 'Trần Văn Mạnh', 'minhhatlu@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-12', '2026-11-12', NULL, NULL, NULL, '2025-11-12 19:16:13', '2025-11-12 19:16:13', '2025-11-12 19:16:13'),
(109, 40, 509, 'Huy Cao', 'nguyenngocsonkdol@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-11', '2026-11-11', NULL, NULL, NULL, '2025-11-15 18:41:22', '2025-11-15 18:41:22', '2025-11-15 18:41:22'),
(110, 40, 551, 'VâN Anh', 'hoangthanhbinh472001@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-16', '2026-11-16', NULL, NULL, NULL, '2025-11-16 15:35:44', '2025-11-16 15:35:44', '2025-11-16 15:35:44'),
(111, 37, 322, 'NgọC', 'nguyenthibangngoc2000@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-16', '2025-12-16', NULL, NULL, NULL, '2025-11-16 16:33:12', '2025-11-16 16:33:12', '2025-11-16 16:33:12'),
(112, 30, 408, 'TùNg LâM', 'hung2706@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-17', '2026-05-16', NULL, NULL, NULL, '2025-11-17 18:18:27', '2025-11-17 18:18:27', '2025-11-17 18:18:27'),
(113, 31, 527, 'Le Mai', 'mytrang01k@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-18', '2025-12-18', NULL, NULL, NULL, '2025-11-18 16:47:20', '2025-11-18 16:47:20', '2025-11-18 16:47:20'),
(114, 40, 557, 'Trà My', 'Beotoetls95@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-18', '2026-02-16', NULL, NULL, NULL, '2025-11-18 16:48:38', '2025-11-18 16:48:38', '2025-11-18 16:48:38'),
(115, 37, 559, 'HùNg Phan', 'hungphan2162005@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-20', '2025-12-20', NULL, NULL, NULL, '2025-11-19 17:34:36', '2025-11-19 17:34:36', '2025-11-19 17:34:36'),
(116, 37, 517, 'HoàNg Thế Anh', 'nguyenhuynhbaolong82@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-19', '2025-12-19', NULL, NULL, NULL, '2025-11-19 17:35:59', '2025-11-19 17:35:59', '2025-11-19 17:35:59'),
(117, 40, 544, 'GạCh Men HoàNg TuấN  ThắNg', 'leanhthang0903@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-20', '2026-11-20', NULL, NULL, NULL, '2025-11-21 05:23:49', '2025-11-21 05:23:49', '2025-11-21 05:23:49'),
(118, 41, 517, 'HoàNg Thế Anh', 'lenhung1550@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-21', '2026-11-21', NULL, NULL, NULL, '2025-11-21 17:14:51', '2025-11-21 17:14:51', '2025-11-21 17:14:51'),
(119, 30, 353, 'Hxthang', 'avbuilding18@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-22', '2026-05-21', NULL, NULL, NULL, '2025-11-22 17:57:31', '2025-11-22 17:57:31', '2025-11-22 17:57:31'),
(120, 41, 353, 'Hxthang', 'avbuilding18@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-22', '2026-11-22', NULL, NULL, NULL, '2025-11-22 17:58:04', '2025-11-22 17:58:04', '2025-11-22 17:58:04'),
(121, 31, 547, 'Ngô Thành Danh', 'm0903366155@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-23', '2025-12-23', NULL, NULL, NULL, '2025-11-23 16:11:15', '2025-11-23 16:11:15', '2025-11-23 16:11:15'),
(122, 31, 563, 'Phuong Lien', 'phuong.assire@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-23', '2025-12-23', NULL, NULL, NULL, '2025-11-23 16:14:39', '2025-11-23 16:14:39', '2025-11-23 16:14:39'),
(123, 30, 563, 'Phuong Lien', 'kiendtph49182@gmail.com', 'member', 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-11-25', '2025-12-25', NULL, NULL, NULL, '2025-11-24 17:56:34', '2025-11-24 18:20:18', '2025-11-24 17:56:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `group_members`
--

CREATE TABLE `group_members` (
  `id` bigint UNSIGNED NOT NULL,
  `target_group_id` bigint UNSIGNED NOT NULL,
  `zalo_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Zalo ID của thành viên',
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên hiển thị',
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số điện thoại (nếu có)',
  `avatar_url` text COLLATE utf8mb4_unicode_ci COMMENT 'URL avatar',
  `status` enum('new','contacted','converted','failed','blocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `joined_at` timestamp NULL DEFAULT NULL COMMENT 'Ngày tham gia nhóm',
  `last_contacted_at` timestamp NULL DEFAULT NULL COMMENT 'Lần liên hệ cuối',
  `contact_count` int NOT NULL DEFAULT '0' COMMENT 'Số lần đã liên hệ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `leads`
--

CREATE TABLE `leads` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zalo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `needs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('new','contacted','interested','quoted','negotiating','won','lost','follow_up') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estimated_value` decimal(15,2) DEFAULT NULL,
  `service_package_id` bigint UNSIGNED DEFAULT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `last_contact_at` timestamp NULL DEFAULT NULL,
  `next_follow_up_at` timestamp NULL DEFAULT NULL,
  `converted_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `potential_value` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `leads`
--

INSERT INTO `leads` (`id`, `name`, `phone`, `zalo`, `email`, `needs`, `status`, `priority`, `requirements`, `estimated_value`, `service_package_id`, `assigned_to`, `last_contact_at`, `next_follow_up_at`, `converted_at`, `customer_id`, `notes`, `source`, `potential_value`, `created_at`, `updated_at`) VALUES
(1, 'Hồ Thị Nga', '0354628071', NULL, NULL, NULL, 'new', 'medium', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'email_marketing', NULL, '2025-07-17 07:20:02', '2025-07-20 17:55:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lead_activities`
--

CREATE TABLE `lead_activities` (
  `id` bigint UNSIGNED NOT NULL,
  `lead_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` enum('call','email','meeting','note','quote','follow_up','converted','lost') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lead_care_schedules`
--

CREATE TABLE `lead_care_schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `lead_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `scheduled_at` timestamp NOT NULL,
  `type` enum('call','message','email','meeting','follow_up','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'call',
  `status` enum('scheduled','completed','cancelled','missed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notification_sent` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `message_campaigns`
--

CREATE TABLE `message_campaigns` (
  `id` bigint UNSIGNED NOT NULL,
  `campaign_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên chiến dịch',
  `target_group_id` bigint UNSIGNED NOT NULL,
  `own_group_id` bigint UNSIGNED DEFAULT NULL,
  `message_template` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mẫu tin nhắn',
  `start_date` date NOT NULL COMMENT 'Ngày bắt đầu',
  `end_date` date DEFAULT NULL COMMENT 'Ngày kết thúc',
  `daily_target` int NOT NULL DEFAULT '50' COMMENT 'Mục tiêu gửi tin/ngày',
  `status` enum('draft','active','paused','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `total_sent` int NOT NULL DEFAULT '0' COMMENT 'Tổng số tin đã gửi',
  `total_delivered` int NOT NULL DEFAULT '0' COMMENT 'Tổng số tin đã gửi thành công',
  `total_failed` int NOT NULL DEFAULT '0' COMMENT 'Tổng số tin thất bại',
  `total_converted` int NOT NULL DEFAULT '0' COMMENT 'Tổng số người đã join nhóm',
  `conversion_rate` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Tỷ lệ chuyển đổi %',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `message_logs`
--

CREATE TABLE `message_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `campaign_id` bigint UNSIGNED NOT NULL,
  `zalo_account_id` bigint UNSIGNED NOT NULL,
  `group_member_id` bigint UNSIGNED NOT NULL,
  `message_content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nội dung tin nhắn đã gửi',
  `status` enum('pending','sent','delivered','failed','blocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Thông báo lỗi (nếu có)',
  `sent_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian gửi',
  `delivered_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian gửi thành công',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_22_191553_create_service_categories_table', 1),
(5, '2025_06_22_191559_create_customers_table', 1),
(6, '2025_06_22_191605_create_service_packages_table', 1),
(7, '2025_06_22_191611_create_customer_services_table', 1),
(8, '2025_06_22_194932_add_cost_price_to_service_packages_table', 1),
(9, '2025_06_22_200030_create_content_posts_table', 1),
(10, '2025_06_22_200814_create_leads_table', 1),
(11, '2025_06_22_200906_create_lead_care_schedules_table', 1),
(12, '2025_06_23_090024_create_admins_table', 1),
(13, '2025_06_24_083956_create_lead_activities_table', 1),
(14, '2025_06_24_091854_add_lead_management_columns_to_leads_table', 1),
(15, '2025_06_24_181308_create_collaborators_table', 1),
(16, '2025_06_24_184432_add_address_to_customers_table', 1),
(17, '2025_06_27_094656_add_assigned_by_to_customer_services_table', 1),
(18, '2025_06_27_101433_create_suppliers_table', 1),
(19, '2025_06_27_102703_add_supplier_id_to_customer_services_table', 1),
(20, '2025_06_27_174227_simplify_suppliers_table', 1),
(21, '2025_06_27_181158_create_supplier_products_table', 1),
(22, '2025_06_27_181328_remove_product_fields_from_suppliers_table', 1),
(23, '2025_06_27_183500_add_supplier_service_id_to_customer_services_table', 1),
(24, '2025_06_28_083438_create_collaborator_services_table', 1),
(25, '2025_06_28_083445_create_collaborator_service_accounts_table', 1),
(26, '2025_06_28_083524_update_collaborators_table', 1),
(27, '2025_06_28_094913_add_warranty_days_to_supplier_products_table', 1),
(28, '2025_07_02_183739_fix_assigned_by_foreign_key_in_customer_services_table', 1),
(29, '2025_07_10_172531_add_reminder_fields_to_customer_services_table', 1),
(30, '2025_07_11_091941_add_shared_account_fields_to_customer_services_table', 1),
(31, '2025_07_19_020618_create_potential_suppliers_table', 1),
(32, '2025_07_19_020643_create_potential_supplier_services_table', 1),
(33, '2025_07_21_204559_add_notes_to_customers_table', 1),
(34, '2025_07_23_070000_create_family_accounts_table', 1),
(35, '2025_07_23_070001_create_family_members_table', 1),
(36, '2025_07_23_080000_simplify_family_accounts_system', 1),
(37, '2025_07_24_create_shared_account_logout_logs_table', 1),
(38, '2025_07_27_230441_add_date_fields_to_family_members_table', 1),
(39, '2025_07_31_000857_update_family_accounts_max_members_constraint', 1),
(40, '2025_08_01_231730_create_telegram_users_table', 1),
(41, '2025_08_01_231824_create_telegram_bot_logs_table', 1),
(42, '2025_08_02_000001_backup_service_data_before_migration', 1),
(43, '2025_08_02_000002_add_warranty_fields_to_service_packages', 1),
(44, '2025_08_02_004154_fix_telegram_users_table_structure', 1),
(45, '2025_08_17_010522_add_expires_at_to_family_members_table', 1),
(46, '2025_08_20_232932_optimize_family_members_indexes', 1),
(47, '2025_08_23_032500_fix_family_members_member_name_field', 1),
(48, '2025_07_21_create_sessions_table', 1),
(49, '2025_08_23_120447_create_family_accounts_table', 1),
(50, '2025_08_23_120524_create_family_members_table', 1),
(51, '2025_08_23_122453_rename_old_family_tables_for_refactor', 1),
(52, '2025_08_31_222507_create_profits_table', 2),
(53, '2025_09_01_003553_add_is_collaborator_to_customers_table', 3),
(54, '2025_09_01_013050_add_joined_at_to_family_members_table', 4),
(55, '2025_09_03_100000_update_profits_table_increase_amount_precision', 5),
(56, '2025_09_13_001817_add_family_account_id_to_customer_services_table', 6),
(57, '2025_10_22_000001_create_zalo_accounts_table', 7),
(58, '2025_10_22_000002_create_target_groups_table', 8),
(59, '2025_10_22_000003_create_group_members_table', 9),
(60, '2025_10_22_000004_create_message_campaigns_table', 10),
(61, '2025_10_22_000005_create_message_logs_table', 11),
(62, '2025_10_22_000006_create_conversion_logs_table', 12),
(63, '2025_10_23_000000_fix_customer_name_encoding', 13),
(65, '2025_10_26_005115_add_pricing_fields_to_customer_services_table', 14),
(66, '2025_10_28_004458_remove_default_duration_from_service_packages', 14),
(67, '2025_10_28_005259_remove_default_pricing_from_service_packages', 15),
(68, '2025_11_06_003000_change_profit_amount_to_bigint', 16),
(69, '2025_11_06_004000_fix_incorrect_profit_amounts', 17),
(70, '2025_11_25_000144_remove_max_members_limit_from_family_accounts', 18);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `potential_suppliers`
--

CREATE TABLE `potential_suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú về lý do tiềm năng',
  `reason_potential` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Lý do được coi là tiềm năng',
  `priority` enum('low','medium','high') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium' COMMENT 'Mức độ ưu tiên',
  `expected_cooperation_date` date DEFAULT NULL COMMENT 'Ngày dự kiến hợp tác',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `potential_supplier_services`
--

CREATE TABLE `potential_supplier_services` (
  `id` bigint UNSIGNED NOT NULL,
  `potential_supplier_id` bigint UNSIGNED NOT NULL,
  `service_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên dịch vụ',
  `estimated_price` decimal(15,2) NOT NULL COMMENT 'Giá ước tính',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả dịch vụ',
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đơn vị (cái, chiếc, tháng...)',
  `warranty_days` int DEFAULT NULL COMMENT 'Số ngày bảo hành',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú về dịch vụ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `profits`
--

CREATE TABLE `profits` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_service_id` bigint UNSIGNED NOT NULL,
  `profit_amount` bigint UNSIGNED NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `profits`
--

INSERT INTO `profits` (`id`, `customer_service_id`, `profit_amount`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 465, 200000, NULL, NULL, '2025-08-31 16:37:39', '2025-08-31 16:37:39'),
(2, 464, 150000, NULL, NULL, '2025-08-31 16:37:52', '2025-08-31 16:37:52'),
(6, 460, 40000, NULL, NULL, '2025-08-31 16:38:39', '2025-08-31 16:38:39'),
(7, 459, 40000, NULL, NULL, '2025-08-31 16:38:50', '2025-08-31 16:38:50'),
(8, 458, 170000, NULL, NULL, '2025-08-31 16:39:28', '2025-08-31 16:39:28'),
(10, 456, 340000, NULL, NULL, '2025-08-31 16:40:16', '2025-08-31 16:40:16'),
(21, 477, 40000, NULL, NULL, '2025-09-01 15:12:01', '2025-09-01 15:12:01'),
(22, 478, 850000, NULL, NULL, '2025-09-01 15:14:19', '2025-09-01 15:14:19'),
(23, 479, 85000, NULL, NULL, '2025-09-01 15:15:08', '2025-09-01 15:15:08'),
(24, 480, 20000, 'Đã sửa lỗi thiếu 3 số 0 (từ 20 → 20.000) - 06/11/2025 01:36:28', NULL, '2025-09-01 15:25:55', '2025-11-05 18:36:28'),
(25, 482, 350000, NULL, NULL, '2025-09-03 16:19:47', '2025-09-03 16:19:47'),
(26, 483, 40000, NULL, NULL, '2025-09-03 16:20:41', '2025-09-03 16:20:41'),
(28, 485, 1000000, NULL, NULL, '2025-09-03 16:46:37', '2025-09-03 16:46:37'),
(30, 487, 130000, NULL, NULL, '2025-09-03 16:50:09', '2025-09-03 16:50:09'),
(32, 489, 450000, NULL, NULL, '2025-09-03 16:53:23', '2025-09-03 16:53:23'),
(33, 490, 250000, NULL, NULL, '2025-09-03 16:56:27', '2025-09-03 16:56:27'),
(34, 491, 350000, NULL, NULL, '2025-09-03 16:57:18', '2025-09-03 16:57:18'),
(36, 493, 20000, NULL, NULL, '2025-09-03 17:00:32', '2025-09-03 17:00:32'),
(38, 497, 370000, NULL, NULL, '2025-09-10 16:48:37', '2025-09-10 16:48:37'),
(39, 498, 140000, NULL, NULL, '2025-09-10 16:49:35', '2025-09-10 16:49:35'),
(40, 499, 300000, NULL, NULL, '2025-09-10 16:52:19', '2025-09-10 16:52:19'),
(41, 501, 140000, NULL, NULL, '2025-09-10 16:54:28', '2025-09-10 16:54:28'),
(42, 502, 190000, NULL, NULL, '2025-09-10 16:55:22', '2025-09-10 16:55:22'),
(46, 505, 370000, NULL, NULL, '2025-09-10 17:06:39', '2025-09-10 17:06:39'),
(47, 506, 200000, NULL, NULL, '2025-09-10 17:07:47', '2025-09-10 17:07:47'),
(48, 507, 140000, NULL, NULL, '2025-09-10 17:08:37', '2025-09-10 17:08:37'),
(50, 509, 340000, NULL, NULL, '2025-09-10 17:13:02', '2025-09-10 17:13:02'),
(51, 510, 100000, NULL, NULL, '2025-09-10 17:14:09', '2025-09-10 17:14:09'),
(52, 511, 170000, NULL, NULL, '2025-09-10 17:16:09', '2025-09-10 17:16:09'),
(53, 512, 140000, NULL, NULL, '2025-09-10 17:17:32', '2025-09-10 17:17:32'),
(54, 513, 370000, NULL, NULL, '2025-09-10 17:18:21', '2025-09-10 17:18:21'),
(55, 514, 140000, NULL, NULL, '2025-09-10 17:19:45', '2025-09-10 17:19:45'),
(56, 515, 170000, NULL, NULL, '2025-09-10 17:20:36', '2025-09-10 17:20:36'),
(57, 516, 140000, NULL, NULL, '2025-09-10 17:21:34', '2025-09-10 17:21:34'),
(59, 518, 190000, NULL, NULL, '2025-09-10 17:25:12', '2025-09-10 17:25:12'),
(60, 209, 150000, NULL, NULL, '2025-09-10 17:26:13', '2025-09-10 17:26:13'),
(61, 519, 110000, NULL, NULL, '2025-09-10 17:26:43', '2025-09-10 17:26:43'),
(62, 520, 370000, NULL, NULL, '2025-09-10 17:27:35', '2025-09-10 17:27:35'),
(63, 521, 300000, NULL, NULL, '2025-09-10 17:28:24', '2025-09-10 17:28:24'),
(65, 523, 90000, NULL, NULL, '2025-09-10 17:31:11', '2025-09-10 17:31:11'),
(66, 524, 360000, NULL, NULL, '2025-09-10 17:32:05', '2025-09-10 17:32:05'),
(68, 526, 350000, NULL, NULL, '2025-09-10 17:34:43', '2025-09-10 17:34:43'),
(69, 527, 30000, NULL, NULL, '2025-09-12 16:31:27', '2025-09-12 16:31:27'),
(71, 529, 340000, NULL, NULL, '2025-09-12 16:35:08', '2025-09-12 16:35:08'),
(72, 530, 310000, NULL, NULL, '2025-09-12 16:36:01', '2025-09-12 16:36:01'),
(73, 531, 190000, NULL, NULL, '2025-09-12 16:36:39', '2025-09-12 16:36:39'),
(74, 532, 370000, NULL, NULL, '2025-09-12 16:37:31', '2025-09-12 16:37:31'),
(75, 533, 80000, NULL, NULL, '2025-09-12 16:38:24', '2025-09-12 16:38:24'),
(76, 534, 200000, NULL, NULL, '2025-09-12 17:01:59', '2025-09-12 17:01:59'),
(78, 537, 180000, NULL, NULL, '2025-09-17 17:32:41', '2025-09-17 17:32:41'),
(79, 538, 40000, NULL, NULL, '2025-09-17 17:33:32', '2025-09-17 17:33:32'),
(81, 540, 300000, NULL, NULL, '2025-09-17 17:34:41', '2025-09-17 17:34:41'),
(82, 541, 200000, NULL, NULL, '2025-09-17 17:35:32', '2025-09-17 17:35:32'),
(83, 542, 370000, NULL, NULL, '2025-09-17 17:36:26', '2025-09-17 17:36:26'),
(85, 544, 330000, NULL, NULL, '2025-09-17 17:39:54', '2025-09-17 17:39:54'),
(86, 545, 330000, NULL, NULL, '2025-09-17 17:40:40', '2025-09-17 17:40:40'),
(88, 547, 65000, NULL, NULL, '2025-09-17 17:42:59', '2025-09-17 17:42:59'),
(89, 548, 170000, NULL, NULL, '2025-09-17 17:44:19', '2025-09-17 17:44:19'),
(91, 550, 330000, NULL, NULL, '2025-09-17 17:45:56', '2025-09-17 17:45:56'),
(92, 551, 260000, NULL, NULL, '2025-09-17 17:47:02', '2025-09-17 17:47:02'),
(93, 552, 330000, NULL, NULL, '2025-09-17 17:48:35', '2025-09-17 17:48:35'),
(96, 555, 370000, NULL, NULL, '2025-09-17 17:57:55', '2025-09-17 17:57:55'),
(97, 556, 50000, NULL, NULL, '2025-09-17 17:59:13', '2025-09-17 17:59:13'),
(98, 557, 150000, NULL, NULL, '2025-09-17 17:59:48', '2025-09-17 17:59:48'),
(99, 558, 180000, NULL, NULL, '2025-09-17 18:00:28', '2025-09-17 18:00:28'),
(104, 344, 40000, NULL, NULL, '2025-09-17 18:08:13', '2025-09-17 18:08:13'),
(107, 562, 140000, NULL, NULL, '2025-09-18 16:23:43', '2025-09-18 16:23:43'),
(109, 563, 50000, NULL, NULL, '2025-09-18 16:25:31', '2025-09-18 16:25:31'),
(110, 564, 170000, NULL, NULL, '2025-09-18 16:26:06', '2025-09-18 16:26:06'),
(111, 565, 10000, NULL, NULL, '2025-09-18 16:26:34', '2025-09-18 16:26:34'),
(112, 566, 85000, NULL, NULL, '2025-09-18 16:27:24', '2025-09-18 16:27:24'),
(113, 567, 470000, NULL, NULL, '2025-09-18 16:28:05', '2025-09-18 16:28:05'),
(114, 568, 350000, NULL, NULL, '2025-09-18 16:28:58', '2025-09-18 16:28:58'),
(115, 569, 280000, NULL, NULL, '2025-09-18 16:30:21', '2025-09-18 16:30:21'),
(116, 570, 190000, NULL, NULL, '2025-09-21 03:57:24', '2025-09-21 03:57:24'),
(118, 572, 370000, NULL, NULL, '2025-09-21 04:19:53', '2025-09-21 04:19:53'),
(120, 574, 140000, NULL, NULL, '2025-09-21 04:22:12', '2025-09-21 04:22:12'),
(121, 575, 40000, NULL, NULL, '2025-09-21 04:22:57', '2025-09-21 04:22:57'),
(123, 577, 100000, NULL, NULL, '2025-09-22 15:04:34', '2025-09-22 15:04:34'),
(124, 578, 130000, NULL, NULL, '2025-09-22 15:06:12', '2025-09-22 15:06:12'),
(125, 579, 370000, NULL, NULL, '2025-09-22 15:06:49', '2025-09-22 15:06:49'),
(126, 580, 130000, NULL, NULL, '2025-09-22 15:07:38', '2025-09-22 15:07:38'),
(127, 581, 25000, NULL, NULL, '2025-09-22 15:08:31', '2025-09-22 15:08:31'),
(129, 583, 65000, NULL, NULL, '2025-09-22 15:10:12', '2025-09-22 15:10:12'),
(130, 584, 33000, NULL, NULL, '2025-09-22 15:11:00', '2025-09-22 15:11:00'),
(131, 381, 40000, NULL, NULL, '2025-09-22 15:11:57', '2025-09-22 15:11:57'),
(132, 585, 70000, NULL, NULL, '2025-09-25 14:15:36', '2025-09-25 14:15:36'),
(134, 586, 149000, NULL, NULL, '2025-09-25 14:17:24', '2025-09-25 14:17:24'),
(135, 587, 140000, NULL, NULL, '2025-09-25 14:19:17', '2025-09-25 14:19:17'),
(136, 588, 360000, NULL, NULL, '2025-09-25 14:19:49', '2025-09-25 14:19:49'),
(138, 590, 120000, NULL, NULL, '2025-09-25 14:21:15', '2025-09-25 14:21:15'),
(139, 591, 130000, NULL, NULL, '2025-09-25 14:23:09', '2025-09-25 14:23:09'),
(140, 592, 350000, NULL, NULL, '2025-09-25 14:23:52', '2025-09-25 14:23:52'),
(141, 593, 400000, NULL, NULL, '2025-09-25 14:25:15', '2025-09-25 14:25:15'),
(142, 594, 200000, NULL, NULL, '2025-09-25 14:26:14', '2025-09-25 14:26:14'),
(143, 595, 190000, NULL, NULL, '2025-09-25 16:22:50', '2025-09-25 16:22:50'),
(144, 596, 190000, NULL, NULL, '2025-09-25 16:23:27', '2025-09-25 16:23:27'),
(146, 598, 180000, NULL, NULL, '2025-09-28 14:26:30', '2025-09-28 14:26:30'),
(147, 599, 180000, NULL, NULL, '2025-09-28 14:27:02', '2025-09-28 14:27:02'),
(148, 600, 180000, NULL, NULL, '2025-09-28 14:27:32', '2025-09-28 14:27:32'),
(149, 601, 600000, NULL, NULL, '2025-09-28 14:30:03', '2025-09-28 14:30:03'),
(151, 603, 400000, NULL, NULL, '2025-09-28 14:32:25', '2025-09-28 14:32:25'),
(152, 604, 550000, NULL, NULL, '2025-09-28 14:35:54', '2025-09-28 14:35:54'),
(153, 315, 40000, NULL, NULL, '2025-09-28 14:36:34', '2025-09-28 14:36:34'),
(155, 605, 550000, NULL, NULL, '2025-09-28 14:39:16', '2025-09-28 14:39:16'),
(157, 228, 150000, NULL, NULL, '2025-09-29 14:25:14', '2025-09-29 14:25:14'),
(158, 606, 360000, NULL, NULL, '2025-09-29 14:25:44', '2025-09-29 14:25:44'),
(160, 608, 170000, NULL, NULL, '2025-09-29 14:32:26', '2025-09-29 14:32:26'),
(161, 609, 90000, NULL, NULL, '2025-09-29 14:33:19', '2025-09-29 14:33:19'),
(162, 610, 185000, NULL, NULL, '2025-09-29 14:34:25', '2025-09-29 14:34:25'),
(164, 611, 160000, NULL, NULL, '2025-09-29 14:36:28', '2025-09-29 14:36:28'),
(165, 612, 160000, NULL, NULL, '2025-09-30 17:02:07', '2025-09-30 17:02:07'),
(166, 613, 180000, NULL, NULL, '2025-09-30 17:02:55', '2025-09-30 17:02:55'),
(167, 614, 180000, NULL, NULL, '2025-09-30 17:03:18', '2025-09-30 17:03:18'),
(168, 615, 180000, NULL, NULL, '2025-09-30 17:03:44', '2025-09-30 17:03:44'),
(169, 616, 375000, NULL, NULL, '2025-09-30 17:04:42', '2025-09-30 17:04:42'),
(171, 140, 150000, NULL, NULL, '2025-09-30 17:07:08', '2025-09-30 17:07:08'),
(172, 618, 375000, NULL, NULL, '2025-09-30 17:07:37', '2025-09-30 17:07:37'),
(174, 620, 340000, NULL, NULL, '2025-09-30 17:11:06', '2025-09-30 17:11:06'),
(175, 621, 160000, NULL, NULL, '2025-09-30 17:13:21', '2025-09-30 17:13:21'),
(177, 623, 140000, NULL, NULL, '2025-09-30 17:14:57', '2025-09-30 17:14:57'),
(178, 624, 250000, NULL, NULL, '2025-10-02 15:43:26', '2025-10-02 15:43:26'),
(179, 625, 50000, NULL, NULL, '2025-10-02 15:44:07', '2025-10-02 15:44:07'),
(181, 627, 360000, NULL, NULL, '2025-10-02 15:46:16', '2025-10-02 15:46:16'),
(182, 628, 340000, NULL, NULL, '2025-10-02 15:47:22', '2025-10-02 15:47:22'),
(183, 629, 700000, NULL, NULL, '2025-10-02 15:48:18', '2025-10-02 15:48:18'),
(184, 630, 40000, NULL, NULL, '2025-10-02 15:49:04', '2025-10-02 15:49:04'),
(185, 631, 160000, NULL, NULL, '2025-10-02 15:50:10', '2025-10-02 15:50:10'),
(186, 632, 40000, NULL, NULL, '2025-10-02 15:51:11', '2025-10-02 15:51:11'),
(187, 633, 40000, NULL, NULL, '2025-10-02 15:52:31', '2025-10-02 15:52:31'),
(188, 634, 350000, NULL, NULL, '2025-10-02 15:54:26', '2025-10-02 15:54:26'),
(189, 635, 20000, NULL, NULL, '2025-10-02 15:56:26', '2025-10-02 15:56:26'),
(190, 636, 350000, NULL, NULL, '2025-10-02 15:57:49', '2025-10-02 15:57:49'),
(191, 637, 40000, NULL, NULL, '2025-10-03 16:18:02', '2025-10-03 16:18:02'),
(192, 638, 40000, NULL, NULL, '2025-10-03 16:19:26', '2025-10-03 16:19:26'),
(194, 640, 360000, NULL, NULL, '2025-10-03 16:22:08', '2025-10-03 16:22:08'),
(195, 641, 200000, NULL, NULL, '2025-10-03 16:22:47', '2025-10-03 16:22:47'),
(196, 642, 180000, NULL, NULL, '2025-10-03 16:23:17', '2025-10-03 16:23:17'),
(197, 643, 180000, NULL, NULL, '2025-10-03 16:23:42', '2025-10-03 16:23:42'),
(200, 646, 350000, NULL, NULL, '2025-10-04 16:58:10', '2025-10-04 16:58:10'),
(201, 647, 140000, NULL, NULL, '2025-10-04 16:58:47', '2025-10-04 16:58:47'),
(202, 648, 360000, NULL, NULL, '2025-10-04 16:59:26', '2025-10-04 16:59:26'),
(204, 650, 150000, NULL, NULL, '2025-10-05 17:43:36', '2025-10-05 17:43:36'),
(205, 651, 20000, NULL, NULL, '2025-10-05 17:44:17', '2025-10-05 17:44:17'),
(206, 652, 40000, NULL, NULL, '2025-10-06 17:19:40', '2025-10-06 17:19:40'),
(207, 653, 290000, NULL, NULL, '2025-10-06 17:20:28', '2025-10-06 17:20:28'),
(208, 654, 360000, NULL, NULL, '2025-10-06 17:21:32', '2025-10-06 17:21:32'),
(209, 655, 149000, NULL, NULL, '2025-10-06 17:23:10', '2025-10-06 17:23:10'),
(211, 657, 360000, NULL, NULL, '2025-10-06 17:24:18', '2025-10-06 17:24:18'),
(212, 658, 360000, NULL, NULL, '2025-10-06 17:25:06', '2025-10-06 17:25:06'),
(213, 659, 150000, NULL, NULL, '2025-10-08 16:48:30', '2025-10-08 16:48:30'),
(214, 660, 360000, NULL, NULL, '2025-10-08 16:49:45', '2025-10-08 16:49:45'),
(215, 661, 180000, NULL, NULL, '2025-10-08 16:50:28', '2025-10-08 16:50:28'),
(217, 663, 70000, NULL, NULL, '2025-10-08 16:51:30', '2025-10-08 16:51:30'),
(218, 664, 40000, NULL, NULL, '2025-10-08 16:52:13', '2025-10-08 16:52:13'),
(219, 665, 149000, NULL, NULL, '2025-10-08 16:53:07', '2025-10-08 16:53:07'),
(220, 666, 20000, NULL, NULL, '2025-10-09 17:14:37', '2025-10-09 17:14:37'),
(221, 668, 10000, NULL, NULL, '2025-10-09 17:16:18', '2025-10-09 17:16:18'),
(222, 669, 370000, NULL, NULL, '2025-10-09 17:17:18', '2025-10-09 17:17:18'),
(223, 670, 239000, NULL, NULL, '2025-10-09 17:17:55', '2025-10-09 17:17:55'),
(224, 672, 180000, NULL, NULL, '2025-10-09 17:19:38', '2025-10-09 17:19:38'),
(225, 673, 149000, NULL, NULL, '2025-10-10 16:03:04', '2025-10-10 16:03:04'),
(227, 675, 200000, NULL, NULL, '2025-10-10 16:05:51', '2025-10-10 16:05:51'),
(228, 676, 150000, NULL, NULL, '2025-10-10 16:06:43', '2025-10-10 16:06:43'),
(229, 677, 180000, NULL, NULL, '2025-10-10 16:07:29', '2025-10-10 16:07:29'),
(230, 678, 180000, NULL, NULL, '2025-10-10 16:08:11', '2025-10-10 16:08:11'),
(231, 679, 20000, NULL, NULL, '2025-10-10 16:09:02', '2025-10-10 16:09:02'),
(232, 680, 20000, NULL, NULL, '2025-10-10 16:09:38', '2025-10-10 16:09:38'),
(234, 682, 360000, NULL, NULL, '2025-10-10 16:12:22', '2025-10-10 16:12:22'),
(235, 683, 360000, NULL, NULL, '2025-10-10 16:13:19', '2025-10-10 16:13:19'),
(237, 685, 360000, NULL, NULL, '2025-10-11 16:54:57', '2025-10-11 16:54:57'),
(238, 686, 360000, NULL, NULL, '2025-10-11 16:55:47', '2025-10-11 16:55:47'),
(239, 687, 350000, NULL, NULL, '2025-10-11 17:00:04', '2025-10-11 17:00:04'),
(240, 688, 130000, NULL, NULL, '2025-10-11 17:01:13', '2025-10-11 17:01:13'),
(241, 689, 160000, NULL, NULL, '2025-10-12 15:21:11', '2025-10-12 15:21:11'),
(242, 690, 420000, NULL, NULL, '2025-10-12 15:22:00', '2025-10-12 15:22:00'),
(243, 691, 100000, NULL, NULL, '2025-10-12 15:22:41', '2025-10-12 15:22:41'),
(244, 692, 200000, NULL, NULL, '2025-10-13 15:57:58', '2025-10-13 15:57:58'),
(245, 693, 80000, NULL, NULL, '2025-10-13 15:58:55', '2025-10-13 15:58:55'),
(246, 694, 189000, NULL, NULL, '2025-10-13 15:59:55', '2025-10-13 15:59:55'),
(247, 695, 10000, NULL, NULL, '2025-10-13 16:00:29', '2025-10-13 16:00:29'),
(248, 696, 10000, NULL, NULL, '2025-10-13 16:00:46', '2025-10-13 16:00:46'),
(249, 697, 180000, NULL, NULL, '2025-10-13 16:01:33', '2025-10-13 16:01:33'),
(250, 698, 40000, NULL, NULL, '2025-10-13 16:02:18', '2025-10-13 16:02:18'),
(251, 699, 13000, NULL, NULL, '2025-10-13 16:02:58', '2025-10-13 16:02:58'),
(252, 700, 14000, NULL, NULL, '2025-10-13 16:03:14', '2025-10-13 16:03:14'),
(253, 701, 100000, NULL, NULL, '2025-10-13 16:03:47', '2025-10-13 16:03:47'),
(254, 702, 370000, NULL, NULL, '2025-10-13 16:04:31', '2025-10-13 16:04:31'),
(255, 703, 170000, NULL, NULL, '2025-10-14 15:55:47', '2025-10-14 15:55:47'),
(256, 704, 100000, NULL, NULL, '2025-10-14 15:58:52', '2025-10-14 15:58:52'),
(257, 705, 100000, NULL, NULL, '2025-10-14 16:00:02', '2025-10-14 16:00:02'),
(258, 706, 370000, NULL, NULL, '2025-10-14 16:00:39', '2025-10-14 16:00:39'),
(259, 707, 40000, NULL, NULL, '2025-10-14 16:01:48', '2025-10-14 16:01:48'),
(260, 708, 1000000, NULL, NULL, '2025-10-17 06:14:32', '2025-10-17 06:14:32'),
(261, 709, 250000, NULL, NULL, '2025-10-17 08:23:47', '2025-10-17 08:23:47'),
(262, 710, 500000, NULL, NULL, '2025-10-17 08:25:55', '2025-10-17 08:25:55'),
(263, 711, 100000, NULL, NULL, '2025-10-17 08:28:42', '2025-10-17 08:28:42'),
(264, 712, 40000, NULL, NULL, '2025-10-17 08:29:22', '2025-10-17 08:29:22'),
(265, 713, 200000, NULL, NULL, '2025-10-17 08:29:49', '2025-10-17 08:29:49'),
(267, 715, 40000, NULL, NULL, '2025-10-17 08:31:50', '2025-10-17 08:31:50'),
(268, 716, 200000, NULL, NULL, '2025-10-17 08:33:47', '2025-10-17 08:33:47'),
(269, 717, 100000, NULL, NULL, '2025-10-17 08:35:20', '2025-10-17 08:35:20'),
(270, 718, 180000, NULL, NULL, '2025-10-17 08:37:00', '2025-10-17 08:37:00'),
(271, 719, 350000, NULL, NULL, '2025-10-17 08:37:59', '2025-10-17 08:37:59'),
(272, 720, 370000, NULL, NULL, '2025-10-17 08:38:52', '2025-10-17 08:38:52'),
(273, 721, 100000, NULL, NULL, '2025-10-17 08:40:00', '2025-10-17 08:40:00'),
(274, 722, 40000, NULL, NULL, '2025-10-18 16:24:39', '2025-10-18 16:24:39'),
(275, 723, 100000, NULL, NULL, '2025-10-18 16:25:36', '2025-10-18 16:25:36'),
(276, 724, 1000000, NULL, NULL, '2025-10-18 16:26:54', '2025-10-18 16:26:54'),
(277, 725, 350000, NULL, NULL, '2025-10-18 16:27:10', '2025-10-18 16:27:10'),
(278, 726, 200000, NULL, NULL, '2025-10-18 16:27:59', '2025-10-18 16:27:59'),
(279, 727, 380000, NULL, NULL, '2025-10-18 16:31:38', '2025-10-18 16:31:38'),
(280, 728, 20000, NULL, NULL, '2025-10-18 16:32:17', '2025-10-18 16:32:17'),
(281, 729, 23000, NULL, NULL, '2025-10-18 16:32:56', '2025-10-18 16:32:56'),
(282, 730, 189000, NULL, NULL, '2025-10-18 16:33:50', '2025-10-18 16:33:50'),
(283, 731, 40000, NULL, NULL, '2025-10-19 18:37:22', '2025-10-19 18:37:22'),
(284, 732, 40000, NULL, NULL, '2025-10-19 18:38:08', '2025-10-19 18:38:08'),
(285, 733, 250000, NULL, NULL, '2025-10-19 18:39:12', '2025-10-19 18:39:12'),
(286, 734, 40000, NULL, NULL, '2025-10-19 18:40:27', '2025-10-19 18:40:27'),
(287, 735, 10000, NULL, NULL, '2025-10-19 18:41:07', '2025-10-19 18:41:07'),
(288, 736, 10000, NULL, NULL, '2025-10-19 18:41:29', '2025-10-19 18:41:29'),
(289, 737, 200000, NULL, NULL, '2025-10-20 17:04:56', '2025-10-20 17:04:56'),
(290, 739, 110000, NULL, NULL, '2025-10-20 17:07:06', '2025-10-20 17:07:06'),
(291, 740, 13000, NULL, NULL, '2025-10-20 17:08:18', '2025-10-20 17:08:18'),
(292, 742, 600000, NULL, NULL, '2025-10-20 17:12:24', '2025-10-20 17:12:24'),
(293, 743, 289000, NULL, NULL, '2025-10-20 17:18:16', '2025-10-20 17:18:16'),
(295, 745, 250000, NULL, NULL, '2025-10-21 18:09:39', '2025-10-21 18:09:39'),
(296, 747, 40000, NULL, NULL, '2025-10-21 18:11:24', '2025-10-21 18:11:24'),
(297, 748, 80000, NULL, NULL, '2025-10-21 18:12:12', '2025-10-21 18:12:12'),
(298, 749, 110000, NULL, NULL, '2025-10-21 18:13:06', '2025-10-21 18:13:06'),
(299, 751, 150000, NULL, NULL, '2025-10-21 18:15:40', '2025-10-21 18:15:40'),
(300, 752, 160000, NULL, NULL, '2025-10-21 18:16:44', '2025-10-21 18:16:44'),
(301, 754, 50000, NULL, NULL, '2025-10-21 18:18:24', '2025-10-21 18:18:24'),
(302, 755, 400000, NULL, NULL, '2025-10-21 18:19:27', '2025-10-21 18:19:27'),
(305, 758, 40000, 'sffd | Đã sửa lỗi thiếu 3 số 0 (từ 40 → 40.000) - 06/11/2025 01:36:28', NULL, '2025-10-27 16:06:51', '2025-11-05 18:36:28'),
(306, 759, 170000, 'Đã sửa lỗi thiếu 3 số 0 (từ 170 → 170.000) - 06/11/2025 01:36:28', NULL, '2025-10-29 17:19:24', '2025-11-05 18:36:28'),
(307, 760, 20000, 'Đã sửa lỗi thiếu 3 số 0 (từ 20 → 20.000) - 06/11/2025 01:36:28', NULL, '2025-11-02 15:33:08', '2025-11-05 18:36:28'),
(308, 761, 40000, NULL, NULL, '2025-11-02 15:34:14', '2025-11-02 15:37:55'),
(310, 763, 170000, 'Đã sửa lỗi thiếu 3 số 0 (từ 170 → 170.000) - 06/11/2025 01:36:28', NULL, '2025-11-02 16:02:28', '2025-11-05 18:36:28'),
(311, 764, 220000, 'Đã sửa lỗi thiếu 3 số 0 (từ 220 → 220.000) - 06/11/2025 01:36:28', NULL, '2025-11-03 03:43:46', '2025-11-05 18:36:28'),
(312, 765, 50000, NULL, NULL, '2025-11-03 03:44:37', '2025-11-03 14:52:45'),
(313, 766, 560000, 'Đã sửa lỗi thiếu 3 số 0 (từ 560 → 560.000) - 06/11/2025 01:36:28', NULL, '2025-11-03 03:46:07', '2025-11-05 18:36:28'),
(314, 767, 159000, 'Đã sửa lỗi thiếu 3 số 0 (từ 159 → 159.000) - 06/11/2025 01:36:28', NULL, '2025-11-03 15:10:14', '2025-11-05 18:36:28'),
(315, 768, 40000, 'Đã sửa lỗi thiếu 3 số 0 (từ 40 → 40.000) - 06/11/2025 01:36:28', NULL, '2025-11-03 15:12:29', '2025-11-05 18:36:28'),
(316, 769, 170000, 'Đã sửa lỗi thiếu 3 số 0 (từ 170 → 170.000) - 06/11/2025 01:36:28', NULL, '2025-11-03 15:19:58', '2025-11-05 18:36:28'),
(317, 770, 120000, 'Đã sửa lỗi thiếu 3 số 0 (từ 120 → 120.000) - 06/11/2025 01:36:28', NULL, '2025-11-03 15:24:25', '2025-11-05 18:36:28'),
(318, 771, 40000, 'Đã sửa lỗi thiếu 3 số 0 (từ 40 → 40.000) - 06/11/2025 01:36:28', NULL, '2025-11-03 15:25:15', '2025-11-05 18:36:28'),
(319, 772, 250000, 'Đã sửa lỗi thiếu 3 số 0 (từ 250 → 250.000) - 06/11/2025 01:36:28', NULL, '2025-11-03 15:28:23', '2025-11-05 18:36:28'),
(320, 773, 400000, 'Đã sửa lỗi thiếu 3 số 0 (từ 400 → 400.000) - 06/11/2025 01:36:28', NULL, '2025-11-03 15:28:59', '2025-11-05 18:36:28'),
(321, 12, 120000, 'Test profit với giá trị 120000', 1, '2025-11-05 18:23:20', '2025-11-05 18:23:20'),
(325, 777, 300000, NULL, NULL, '2025-11-06 15:44:38', '2025-11-06 15:44:38'),
(326, 778, 40000, NULL, NULL, '2025-11-06 15:45:47', '2025-11-06 15:45:47'),
(327, 779, 250000, NULL, NULL, '2025-11-06 15:47:08', '2025-11-06 15:47:08'),
(328, 780, 40000, NULL, NULL, '2025-11-06 15:52:57', '2025-11-06 15:52:57'),
(329, 781, 50000, NULL, NULL, '2025-11-06 16:20:44', '2025-11-06 16:20:44'),
(330, 782, 220000, NULL, NULL, '2025-11-06 16:22:04', '2025-11-06 16:22:04'),
(331, 783, 180000, NULL, NULL, '2025-11-06 16:23:25', '2025-11-06 16:23:25'),
(332, 784, 140000, NULL, NULL, '2025-11-06 16:25:31', '2025-11-06 16:25:31'),
(333, 785, 150000, NULL, NULL, '2025-11-06 16:30:30', '2025-11-06 16:30:30'),
(334, 786, 189000, NULL, NULL, '2025-11-06 16:32:20', '2025-11-06 16:32:20'),
(335, 787, 189000, NULL, NULL, '2025-11-06 16:33:12', '2025-11-06 16:33:12'),
(336, 788, 150000, NULL, NULL, '2025-11-06 16:35:14', '2025-11-06 16:35:14'),
(337, 789, 10000, NULL, NULL, '2025-11-06 16:35:51', '2025-11-06 16:35:51'),
(338, 790, 50000, NULL, NULL, '2025-11-06 16:36:29', '2025-11-06 16:36:29'),
(339, 791, 600000, NULL, NULL, '2025-11-06 16:37:30', '2025-11-06 16:37:30'),
(340, 792, 200000, NULL, NULL, '2025-11-06 16:41:04', '2025-11-06 16:41:04'),
(341, 793, 130000, NULL, NULL, '2025-11-06 16:42:33', '2025-11-06 16:42:33'),
(342, 794, 159000, NULL, NULL, '2025-11-06 16:43:21', '2025-11-06 16:43:21'),
(343, 795, 110000, NULL, NULL, '2025-11-06 16:44:43', '2025-11-06 16:44:43'),
(345, 797, 1000000, NULL, NULL, '2025-11-06 17:28:12', '2025-11-06 17:28:12'),
(346, 798, 100000, NULL, NULL, '2025-11-06 17:29:26', '2025-11-06 17:29:26'),
(347, 799, 20000, NULL, NULL, '2025-11-06 17:30:11', '2025-11-06 17:30:11'),
(348, 800, 20000, NULL, NULL, '2025-11-06 17:30:39', '2025-11-06 17:30:39'),
(349, 801, 20000, NULL, NULL, '2025-11-06 17:32:09', '2025-11-06 17:32:09'),
(350, 802, 20000, NULL, NULL, '2025-11-06 17:32:54', '2025-11-06 17:32:54'),
(351, 803, 20000, NULL, NULL, '2025-11-06 17:33:29', '2025-11-06 17:33:29'),
(352, 804, 20000, NULL, NULL, '2025-11-06 17:33:58', '2025-11-06 17:33:58'),
(353, 805, 20000, NULL, NULL, '2025-11-06 17:34:28', '2025-11-06 17:34:28'),
(354, 806, 20000, NULL, NULL, '2025-11-06 17:35:09', '2025-11-06 17:35:09'),
(355, 807, 200000, NULL, NULL, '2025-11-06 17:39:46', '2025-11-06 17:39:46'),
(356, 808, 300000, NULL, NULL, '2025-11-06 17:40:17', '2025-11-06 17:40:17'),
(357, 809, 100000, NULL, NULL, '2025-11-06 17:41:06', '2025-11-06 17:41:06'),
(359, 811, 350000, NULL, NULL, '2025-11-06 17:43:25', '2025-11-06 17:43:25'),
(360, 812, 500000, NULL, NULL, '2025-11-06 17:47:28', '2025-11-06 17:47:28'),
(361, 813, 200000, NULL, NULL, '2025-11-06 17:57:08', '2025-11-06 17:57:08'),
(362, 814, 200000, NULL, NULL, '2025-11-06 18:05:38', '2025-11-06 18:05:38'),
(363, 815, 350000, NULL, NULL, '2025-11-06 18:06:19', '2025-11-06 18:06:19'),
(364, 816, 100000, NULL, NULL, '2025-11-06 18:07:40', '2025-11-06 18:07:40'),
(365, 817, 350000, NULL, NULL, '2025-11-06 18:09:25', '2025-11-06 18:09:25'),
(366, 818, 100000, NULL, NULL, '2025-11-06 18:10:14', '2025-11-06 18:10:14'),
(367, 819, 45000, NULL, NULL, '2025-11-06 18:11:01', '2025-11-06 18:11:01'),
(368, 820, 45000, NULL, NULL, '2025-11-06 18:11:01', '2025-11-06 18:11:01'),
(369, 821, 20000, NULL, NULL, '2025-11-06 18:11:31', '2025-11-06 18:11:31'),
(370, 822, 20000, NULL, NULL, '2025-11-06 18:12:00', '2025-11-06 18:12:00'),
(371, 823, 45000, NULL, NULL, '2025-11-06 18:12:35', '2025-11-06 18:12:35'),
(372, 824, 400000, NULL, NULL, '2025-11-06 18:15:29', '2025-11-06 18:16:24'),
(373, 825, 70000, NULL, NULL, '2025-11-06 18:19:44', '2025-11-06 18:19:44'),
(374, 826, 220000, NULL, NULL, '2025-11-06 18:20:59', '2025-11-06 18:20:59'),
(375, 827, 250000, NULL, NULL, '2025-11-06 18:21:50', '2025-11-06 18:21:50'),
(376, 828, 100000, NULL, NULL, '2025-11-08 17:19:13', '2025-11-08 17:19:13'),
(377, 829, 40000, NULL, NULL, '2025-11-08 17:20:39', '2025-11-08 17:20:39'),
(378, 830, 200000, NULL, NULL, '2025-11-08 17:21:35', '2025-11-08 17:21:35'),
(379, 831, 180000, NULL, NULL, '2025-11-08 17:22:26', '2025-11-08 17:22:26'),
(380, 832, 200000, NULL, NULL, '2025-11-08 17:23:03', '2025-11-08 17:23:03'),
(381, 833, 300000, NULL, NULL, '2025-11-08 17:24:01', '2025-11-08 17:24:01'),
(382, 834, 30000, NULL, NULL, '2025-11-08 17:24:52', '2025-11-08 17:24:52'),
(383, 835, 230000, NULL, NULL, '2025-11-08 17:25:53', '2025-11-08 17:25:53'),
(384, 836, 340000, NULL, NULL, '2025-11-08 17:26:43', '2025-11-08 17:26:43'),
(385, 837, 140000, NULL, NULL, '2025-11-08 17:27:31', '2025-11-08 17:27:31'),
(386, 838, 110000, NULL, NULL, '2025-11-08 17:28:35', '2025-11-08 17:28:35'),
(387, 839, 110000, NULL, NULL, '2025-11-08 17:28:56', '2025-11-08 17:28:56'),
(388, 840, 150000, NULL, NULL, '2025-11-08 17:29:27', '2025-11-08 17:29:27'),
(389, 841, 40000, NULL, NULL, '2025-11-08 17:30:30', '2025-11-08 17:30:30'),
(390, 842, 60000, NULL, NULL, '2025-11-08 17:31:13', '2025-11-08 17:31:13'),
(391, 843, 300000, NULL, NULL, '2025-11-08 17:32:00', '2025-11-08 17:32:00'),
(392, 844, 160000, NULL, NULL, '2025-11-08 17:32:51', '2025-11-08 17:32:51'),
(393, 845, 400000, NULL, NULL, '2025-11-08 17:33:50', '2025-11-08 17:33:50'),
(394, 846, 20000, NULL, NULL, '2025-11-08 17:35:11', '2025-11-08 17:35:11'),
(395, 847, 20000, NULL, NULL, '2025-11-08 17:35:33', '2025-11-08 17:35:33'),
(396, 848, 20000, NULL, NULL, '2025-11-08 17:35:59', '2025-11-08 17:35:59'),
(397, 849, 180000, NULL, NULL, '2025-11-08 17:37:05', '2025-11-08 17:37:05'),
(398, 850, 80000, NULL, NULL, '2025-11-08 17:37:49', '2025-11-08 17:37:49'),
(399, 851, 160000, NULL, NULL, '2025-11-08 17:38:46', '2025-11-08 17:38:46'),
(400, 852, 40000, NULL, NULL, '2025-11-08 17:39:43', '2025-11-08 17:39:43'),
(401, 853, 40000, NULL, NULL, '2025-11-08 17:40:18', '2025-11-08 17:40:18'),
(402, 854, 170000, NULL, NULL, '2025-11-08 17:41:04', '2025-11-08 17:41:04'),
(403, 855, 160000, NULL, NULL, '2025-11-08 17:43:48', '2025-11-08 17:43:48'),
(404, 856, 150000, NULL, NULL, '2025-11-08 17:44:39', '2025-11-08 17:44:39'),
(405, 857, 40000, NULL, NULL, '2025-11-08 17:45:32', '2025-11-08 17:45:32'),
(406, 858, 40000, NULL, NULL, '2025-11-08 17:46:20', '2025-11-08 17:46:20'),
(407, 859, 150000, NULL, NULL, '2025-11-08 17:47:47', '2025-11-08 17:47:47'),
(408, 860, 100000, NULL, NULL, '2025-11-08 17:48:26', '2025-11-08 17:48:26'),
(409, 861, 199000, NULL, NULL, '2025-11-08 17:50:23', '2025-11-08 17:50:23'),
(410, 862, 90000, NULL, NULL, '2025-11-08 17:51:09', '2025-11-08 17:51:09'),
(411, 863, 90000, NULL, NULL, '2025-11-08 17:51:47', '2025-11-08 17:51:47'),
(412, 865, 40000, NULL, NULL, '2025-11-08 17:53:13', '2025-11-08 17:53:13'),
(413, 866, 160000, NULL, NULL, '2025-11-08 17:53:49', '2025-11-08 17:53:49'),
(414, 867, 200000, NULL, NULL, '2025-11-08 17:54:32', '2025-11-08 17:54:32'),
(415, 868, 20000, NULL, NULL, '2025-11-08 17:55:16', '2025-11-08 17:55:16'),
(416, 869, 60000, NULL, NULL, '2025-11-08 17:56:10', '2025-11-08 17:56:10'),
(417, 870, 160000, NULL, NULL, '2025-11-08 18:00:23', '2025-11-08 18:00:23'),
(418, 871, 250000, NULL, NULL, '2025-11-08 18:01:41', '2025-11-08 18:01:41'),
(419, 872, 180000, NULL, NULL, '2025-11-08 18:02:51', '2025-11-08 18:02:51'),
(420, 873, 240000, NULL, NULL, '2025-11-08 18:03:49', '2025-11-08 18:03:49'),
(421, 874, 89000, NULL, NULL, '2025-11-08 18:05:49', '2025-11-08 18:05:49'),
(422, 875, 30000, NULL, NULL, '2025-11-08 18:06:57', '2025-11-08 18:06:57'),
(423, 876, 89000, NULL, NULL, '2025-11-08 18:08:04', '2025-11-08 18:08:04'),
(424, 877, 89000, NULL, NULL, '2025-11-08 18:09:27', '2025-11-08 18:09:27'),
(425, 878, 170000, NULL, NULL, '2025-11-08 18:10:10', '2025-11-08 18:10:10'),
(426, 879, 100000, NULL, NULL, '2025-11-08 18:11:04', '2025-11-08 18:11:04'),
(427, 880, 90000, NULL, NULL, '2025-11-09 18:10:14', '2025-11-09 18:10:14'),
(428, 881, 90000, NULL, NULL, '2025-11-09 18:10:55', '2025-11-09 18:10:55'),
(429, 882, 80000, NULL, NULL, '2025-11-09 18:11:38', '2025-11-09 18:11:38'),
(430, 883, 200000, NULL, NULL, '2025-11-09 18:12:52', '2025-11-09 18:12:52'),
(431, 884, 170000, NULL, NULL, '2025-11-09 18:13:38', '2025-11-09 18:13:38'),
(432, 885, 170000, NULL, NULL, '2025-11-09 18:14:30', '2025-11-09 18:14:30'),
(433, 886, 30000, NULL, NULL, '2025-11-09 18:15:25', '2025-11-09 18:15:25'),
(434, 887, 100000, NULL, NULL, '2025-11-09 18:16:39', '2025-11-09 18:16:39'),
(435, 888, 40000, NULL, NULL, '2025-11-09 18:17:21', '2025-11-09 18:17:21'),
(436, 889, 45000, NULL, NULL, '2025-11-09 18:18:05', '2025-11-09 18:18:05'),
(437, 890, 150000, NULL, NULL, '2025-11-09 18:18:58', '2025-11-09 18:18:58'),
(438, 891, 150000, NULL, NULL, '2025-11-09 18:20:08', '2025-11-09 18:20:08'),
(439, 892, 370000, NULL, NULL, '2025-11-10 18:02:49', '2025-11-10 18:02:49'),
(440, 893, 140000, NULL, NULL, '2025-11-10 18:03:39', '2025-11-10 18:03:39'),
(441, 894, 70000, NULL, NULL, '2025-11-10 18:04:33', '2025-11-10 18:04:33'),
(442, 895, 130000, NULL, NULL, '2025-11-10 18:05:24', '2025-11-10 18:05:24'),
(443, 896, 45000, NULL, NULL, '2025-11-10 18:06:43', '2025-11-10 18:06:43'),
(444, 897, 10000, NULL, NULL, '2025-11-10 18:07:15', '2025-11-10 18:07:15'),
(445, 898, 40000, NULL, NULL, '2025-11-10 18:07:49', '2025-11-10 18:07:49'),
(446, 899, 100000, NULL, NULL, '2025-11-10 18:08:37', '2025-11-10 18:08:37'),
(447, 900, 160000, NULL, NULL, '2025-11-10 18:09:51', '2025-11-10 18:09:51'),
(448, 901, 189000, NULL, NULL, '2025-11-10 18:10:22', '2025-11-10 18:10:22'),
(449, 902, 130000, NULL, NULL, '2025-11-10 18:11:21', '2025-11-10 18:11:21'),
(450, 903, 40000, NULL, NULL, '2025-11-10 18:12:09', '2025-11-10 18:12:09'),
(451, 904, 80000, NULL, NULL, '2025-11-11 19:01:23', '2025-11-11 19:01:23'),
(452, 905, 40000, NULL, NULL, '2025-11-11 19:02:04', '2025-11-11 19:02:04'),
(453, 906, 229000, NULL, NULL, '2025-11-11 19:02:45', '2025-11-11 19:02:45'),
(454, 907, 180000, NULL, NULL, '2025-11-11 19:06:01', '2025-11-11 19:06:01'),
(455, 908, 189000, NULL, NULL, '2025-11-11 19:06:34', '2025-11-11 19:06:34'),
(456, 909, 350000, NULL, NULL, '2025-11-11 19:07:50', '2025-11-11 19:07:50'),
(457, 910, 150000, NULL, NULL, '2025-11-12 19:09:48', '2025-11-12 19:09:48'),
(458, 911, 180000, NULL, NULL, '2025-11-12 19:10:43', '2025-11-12 19:10:43'),
(459, 912, 90000, NULL, NULL, '2025-11-12 19:11:16', '2025-11-12 19:11:16'),
(460, 913, 80000, NULL, NULL, '2025-11-12 19:12:00', '2025-11-12 19:12:00'),
(461, 914, 20000, NULL, NULL, '2025-11-12 19:12:54', '2025-11-12 19:12:54'),
(462, 915, 20000, NULL, NULL, '2025-11-12 19:13:21', '2025-11-12 19:13:21'),
(463, 916, 20000, NULL, NULL, '2025-11-12 19:13:39', '2025-11-12 19:13:39'),
(464, 917, 170000, NULL, NULL, '2025-11-12 19:16:13', '2025-11-12 19:16:13'),
(465, 918, 45000, NULL, NULL, '2025-11-12 19:16:45', '2025-11-12 19:16:45'),
(466, 919, 200000, NULL, NULL, '2025-11-12 19:17:22', '2025-11-12 19:17:22'),
(467, 920, 200000, NULL, NULL, '2025-11-12 19:18:29', '2025-11-12 19:18:29'),
(468, 921, 200000, NULL, NULL, '2025-11-12 19:19:17', '2025-11-12 19:19:17'),
(469, 922, 350000, NULL, NULL, '2025-11-15 18:41:22', '2025-11-15 18:41:22'),
(470, 923, 130000, NULL, NULL, '2025-11-15 18:43:13', '2025-11-15 18:43:13'),
(471, 924, 130000, NULL, NULL, '2025-11-15 18:43:52', '2025-11-15 18:43:52'),
(472, 925, 70000, NULL, NULL, '2025-11-15 18:45:35', '2025-11-15 18:45:35'),
(473, 926, 20000, NULL, NULL, '2025-11-15 18:46:17', '2025-11-15 18:46:17'),
(474, 927, 100000, NULL, NULL, '2025-11-15 18:47:41', '2025-11-15 18:47:41'),
(475, 928, 189000, NULL, NULL, '2025-11-15 18:48:38', '2025-11-15 18:48:38'),
(476, 929, 45000, NULL, NULL, '2025-11-15 18:49:29', '2025-11-15 18:49:29'),
(477, 930, 40000, NULL, NULL, '2025-11-15 18:50:05', '2025-11-15 18:50:05'),
(478, 931, 50000, NULL, NULL, '2025-11-15 18:50:35', '2025-11-15 18:50:35'),
(479, 932, 50000, NULL, NULL, '2025-11-15 18:50:54', '2025-11-15 18:50:54'),
(480, 933, 90000, NULL, NULL, '2025-11-15 18:51:37', '2025-11-15 18:51:37'),
(481, 934, 80000, NULL, NULL, '2025-11-15 18:52:25', '2025-11-15 18:52:25'),
(482, 935, 30000, NULL, NULL, '2025-11-15 18:53:08', '2025-11-15 18:53:08'),
(483, 936, 340000, NULL, NULL, '2025-11-15 18:54:20', '2025-11-15 18:54:20'),
(484, 937, 100000, NULL, NULL, '2025-11-15 18:55:12', '2025-11-15 18:55:12'),
(485, 938, 150000, NULL, NULL, '2025-11-15 18:55:57', '2025-11-15 18:55:57'),
(486, 939, 140000, NULL, NULL, '2025-11-15 18:56:18', '2025-11-15 18:56:18'),
(487, 940, 90000, NULL, NULL, '2025-11-15 18:58:47', '2025-11-15 18:58:47'),
(488, 941, 589000, NULL, NULL, '2025-11-16 15:31:22', '2025-11-16 15:31:22'),
(489, 942, 69000, NULL, NULL, '2025-11-16 15:32:14', '2025-11-16 15:32:14'),
(490, 943, 90000, NULL, NULL, '2025-11-16 15:33:03', '2025-11-16 15:33:03'),
(491, 944, 50000, NULL, NULL, '2025-11-16 15:33:55', '2025-11-16 15:33:55'),
(492, 945, 10000, NULL, NULL, '2025-11-16 15:34:26', '2025-11-16 15:34:26'),
(493, 946, 10000, NULL, NULL, '2025-11-16 15:34:58', '2025-11-16 15:34:58'),
(494, 947, 350000, NULL, NULL, '2025-11-16 15:35:44', '2025-11-16 15:35:44'),
(495, 948, 99000, NULL, NULL, '2025-11-16 16:33:12', '2025-11-16 16:33:12'),
(496, 949, 160000, NULL, NULL, '2025-11-17 18:15:58', '2025-11-17 18:15:58'),
(497, 950, 120000, NULL, NULL, '2025-11-17 18:16:59', '2025-11-17 18:16:59'),
(498, 951, 20000, NULL, NULL, '2025-11-17 18:17:42', '2025-11-17 18:17:42'),
(499, 952, 660000, NULL, NULL, '2025-11-17 18:18:27', '2025-11-17 18:18:27'),
(500, 953, 30000, NULL, NULL, '2025-11-17 18:19:33', '2025-11-17 18:19:33'),
(501, 954, 290000, NULL, NULL, '2025-11-17 18:20:10', '2025-11-17 18:20:10'),
(502, 955, 150000, NULL, NULL, '2025-11-17 18:21:54', '2025-11-17 18:21:54'),
(503, 956, 150000, NULL, NULL, '2025-11-17 18:22:16', '2025-11-17 18:22:16'),
(504, 957, 150000, NULL, NULL, '2025-11-17 18:22:40', '2025-11-17 18:22:40'),
(505, 958, 180000, NULL, NULL, '2025-11-17 18:23:19', '2025-11-17 18:23:19'),
(506, 959, 159000, NULL, NULL, '2025-11-18 16:41:22', '2025-11-18 16:41:22'),
(507, 960, 45000, NULL, NULL, '2025-11-18 16:41:47', '2025-11-18 16:41:47'),
(508, 961, 45000, NULL, NULL, '2025-11-18 16:42:49', '2025-11-18 16:42:49'),
(509, 962, 180000, NULL, NULL, '2025-11-18 16:43:35', '2025-11-18 16:43:35'),
(510, 963, 200000, NULL, NULL, '2025-11-18 16:44:15', '2025-11-18 16:44:15'),
(511, 964, 100000, NULL, NULL, '2025-11-18 16:45:10', '2025-11-18 16:45:10'),
(512, 965, 100000, NULL, NULL, '2025-11-18 16:46:02', '2025-11-18 16:46:02'),
(513, 966, 50000, NULL, NULL, '2025-11-18 16:47:20', '2025-11-18 16:47:20'),
(514, 967, 20000, NULL, NULL, '2025-11-18 16:47:45', '2025-11-18 16:47:45'),
(515, 968, 60000, NULL, NULL, '2025-11-18 16:48:38', '2025-11-18 16:48:38'),
(516, 969, 120000, NULL, NULL, '2025-11-18 16:49:59', '2025-11-18 16:49:59'),
(517, 970, 30000, NULL, NULL, '2025-11-18 16:50:20', '2025-11-18 16:50:20'),
(518, 971, 160000, NULL, NULL, '2025-11-18 16:51:05', '2025-11-18 16:51:05'),
(519, 972, 180000, NULL, NULL, '2025-11-18 16:51:42', '2025-11-18 16:51:42'),
(520, 973, 290000, NULL, NULL, '2025-11-19 17:33:50', '2025-11-19 17:33:50'),
(521, 974, 100000, NULL, NULL, '2025-11-19 17:34:36', '2025-11-19 17:34:36'),
(522, 975, 45000, NULL, NULL, '2025-11-19 17:35:16', '2025-11-19 17:35:16'),
(523, 976, 50000, NULL, NULL, '2025-11-19 17:35:59', '2025-11-19 17:35:59'),
(524, 977, 40000, NULL, NULL, '2025-11-19 17:36:45', '2025-11-19 17:36:45'),
(525, 978, 35000, NULL, NULL, '2025-11-19 17:37:33', '2025-11-19 17:37:33'),
(526, 979, 90000, NULL, NULL, '2025-11-21 05:22:14', '2025-11-21 05:22:14'),
(527, 980, 20000, NULL, NULL, '2025-11-21 05:22:53', '2025-11-21 05:22:53'),
(528, 981, 350000, NULL, NULL, '2025-11-21 05:23:49', '2025-11-21 05:23:49'),
(529, 982, 100000, NULL, NULL, '2025-11-21 05:24:35', '2025-11-21 05:24:35'),
(530, 983, 45000, NULL, NULL, '2025-11-21 05:25:18', '2025-11-21 05:25:18'),
(531, 984, 20000, NULL, NULL, '2025-11-21 05:25:51', '2025-11-21 05:25:51'),
(532, 985, 45000, NULL, NULL, '2025-11-21 05:26:19', '2025-11-21 05:26:19'),
(533, 986, 425000, NULL, NULL, '2025-11-21 05:26:59', '2025-11-21 05:26:59'),
(534, 987, 160000, NULL, NULL, '2025-11-21 05:27:58', '2025-11-21 05:27:58'),
(535, 988, 45000, NULL, NULL, '2025-11-21 17:09:35', '2025-11-21 17:09:35'),
(536, 989, 20000, NULL, NULL, '2025-11-21 17:10:09', '2025-11-21 17:10:09'),
(537, 990, 50000, NULL, NULL, '2025-11-21 17:10:44', '2025-11-21 17:10:44'),
(538, 991, 200000, NULL, NULL, '2025-11-21 17:14:51', '2025-11-21 17:14:51'),
(539, 992, 130000, NULL, NULL, '2025-11-21 17:15:40', '2025-11-21 17:15:40'),
(540, 993, 90000, NULL, NULL, '2025-11-22 17:53:15', '2025-11-22 17:53:15'),
(541, 994, 90000, NULL, NULL, '2025-11-22 17:53:42', '2025-11-22 17:53:42'),
(542, 995, 30000, NULL, NULL, '2025-11-22 17:54:19', '2025-11-22 17:54:19'),
(543, 996, 15000, NULL, NULL, '2025-11-22 17:54:49', '2025-11-22 17:54:49'),
(544, 997, 600000, NULL, NULL, '2025-11-22 17:57:31', '2025-11-22 17:57:31'),
(545, 998, 350000, NULL, NULL, '2025-11-22 17:58:04', '2025-11-22 17:58:04'),
(546, 999, 440000, NULL, NULL, '2025-11-22 17:58:42', '2025-11-22 17:58:42'),
(547, 1000, 45000, NULL, NULL, '2025-11-22 17:59:57', '2025-11-22 17:59:57'),
(548, 1001, 20000, NULL, NULL, '2025-11-22 18:01:30', '2025-11-22 18:01:30'),
(549, 1002, 20000, NULL, NULL, '2025-11-22 18:02:10', '2025-11-22 18:02:10'),
(550, 1003, 130000, NULL, NULL, '2025-11-22 18:02:42', '2025-11-22 18:02:42'),
(551, 1004, 35000, NULL, NULL, '2025-11-22 18:03:26', '2025-11-22 18:03:26'),
(552, 1005, 10000, NULL, NULL, '2025-11-23 16:10:28', '2025-11-23 16:10:28'),
(553, 1006, 130000, NULL, NULL, '2025-11-23 16:11:15', '2025-11-23 16:11:15'),
(554, 1007, 70000, NULL, NULL, '2025-11-23 16:12:31', '2025-11-23 16:12:31'),
(555, 1008, 40000, NULL, NULL, '2025-11-23 16:13:32', '2025-11-23 16:13:32'),
(556, 1009, 120000, NULL, NULL, '2025-11-23 16:14:39', '2025-11-23 16:14:39'),
(557, 1010, 280000, NULL, NULL, '2025-11-23 16:15:17', '2025-11-23 16:15:17'),
(558, 1011, 190000, NULL, NULL, '2025-11-23 16:16:00', '2025-11-23 16:16:00'),
(559, 1012, 200000, NULL, NULL, '2025-11-23 16:16:45', '2025-11-23 16:16:45'),
(560, 1013, 70000, NULL, NULL, '2025-11-23 16:37:36', '2025-11-23 16:37:36'),
(561, 1014, 170000, NULL, NULL, '2025-11-24 16:14:14', '2025-11-24 16:14:28'),
(562, 1015, 80000, NULL, NULL, '2025-11-24 16:15:03', '2025-11-24 16:15:03'),
(563, 1016, 20000, NULL, NULL, '2025-11-24 16:15:45', '2025-11-24 16:15:45'),
(564, 1017, 35000, NULL, NULL, '2025-11-24 16:16:18', '2025-11-24 16:16:18'),
(565, 1018, 200000, NULL, NULL, '2025-11-24 16:16:54', '2025-11-24 16:16:54'),
(566, 1019, 45000, NULL, NULL, '2025-11-24 16:17:37', '2025-11-24 16:17:37'),
(567, 1020, 80000, NULL, NULL, '2025-11-24 16:18:20', '2025-11-24 16:18:20'),
(568, 1021, 20000, NULL, NULL, '2025-11-24 16:18:53', '2025-11-24 16:18:53'),
(569, 1022, 40000, NULL, NULL, '2025-11-24 16:19:35', '2025-11-24 16:19:35'),
(570, 1023, 40000, NULL, NULL, '2025-11-24 16:20:09', '2025-11-24 16:20:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_categories`
--

CREATE TABLE `service_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `service_categories`
--

INSERT INTO `service_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'AI phổ thông', NULL, '2025-08-24 01:59:55', '2025-08-24 01:59:55'),
(2, 'AI làm video', NULL, '2025-08-24 02:00:02', '2025-08-24 02:00:02'),
(3, 'AI coding', NULL, '2025-08-24 02:00:12', '2025-08-24 02:00:12'),
(4, 'công cụ làm việc', NULL, '2025-08-24 02:00:28', '2025-08-24 02:00:28'),
(5, 'công cụ giải trí', NULL, '2025-08-24 02:00:37', '2025-08-24 02:00:37'),
(6, 'Giáo dục & Học tập', 'Các dịch vụ hỗ trợ giáo dục và học tập online', '2025-08-28 15:58:58', '2025-08-28 15:58:58'),
(7, 'Giải trí & Media', 'Các dịch vụ giải trí, phim ảnh và âm nhạc', '2025-08-28 15:58:58', '2025-08-28 15:58:58'),
(8, 'Công cụ văn phòng', 'Các dịch vụ hỗ trợ công việc văn phòng', '2025-08-28 15:59:59', '2025-08-28 15:59:59'),
(9, 'Cloud Storage', 'Các dịch vụ lưu trữ đám mây', '2025-08-28 15:59:59', '2025-08-28 15:59:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_categories_backup`
--

CREATE TABLE `service_categories_backup` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_packages`
--

CREATE TABLE `service_packages` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_duration_days` int DEFAULT NULL,
  `custom_duration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thời hạn tùy chỉnh như "15 ngày", "1 năm"',
  `price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `warranty_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Loại bảo hành: full, KBH, 1MONTH, 3 tháng',
  `detailed_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú chi tiết về gói dịch vụ',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_renewable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Có thể gia hạn hay không',
  `device_limit` int DEFAULT NULL COMMENT 'Giới hạn số thiết bị, null = không giới hạn',
  `shared_users_limit` int DEFAULT NULL COMMENT 'Số người dùng chung, null = không dùng chung',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `service_packages`
--

INSERT INTO `service_packages` (`id`, `category_id`, `name`, `account_type`, `default_duration_days`, `custom_duration`, `price`, `cost_price`, `description`, `warranty_type`, `detailed_notes`, `is_active`, `is_renewable`, `device_limit`, `shared_users_limit`, `created_at`, `updated_at`) VALUES
(1, 1, 'ChatGPT Plus dùng chung', 'Tài khoản dùng chung', NULL, NULL, NULL, NULL, 'ChatGPT Plus dùng chung', 'full', 'dùng chung 4 người, sử dụng 1 thiết bị', 1, 1, 1, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(3, 1, 'ChatGPT Plus chính chủ (cá nhân)', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'ChatGPT Plus chính chủ cá nhân', 'full', 'gia hạn được, không giới hạn thiết bị', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(4, 1, 'Supper Grok dùng chung', 'Tài khoản dùng chung', NULL, NULL, NULL, NULL, 'Supper Grok dùng chung', 'full', 'dùng chung 5 người, sử dụng 2 thiết bị', 1, 1, 2, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(5, 1, 'Supper Grok chính chủ', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Supper Grok chính chủ', 'full', 'không giới hạn thiết bị', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(6, 1, 'Perplexity chính chủ', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Perplexity Pro chính chủ', '3 tháng', 'không giới hạn thiết bị, mail cá nhân', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(7, 1, 'Gemini Pro + 2TB drive chính chủ', 'Tài khoản add family', NULL, NULL, NULL, NULL, 'Gemini Pro + 2TB Drive (tặng Youtube Premium)', 'full', 'mail cá nhân, dùng ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-09-01 07:58:40'),
(8, 1, 'Claude AI chính chủ', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Claude AI chính chủ', 'full', 'mail cá nhân, dùng ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(11, 2, 'Kling AI', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Kling Standard plan', 'full', 'ổn định, không giới hạn thiết bị', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-11-24 19:27:27'),
(15, 2, 'Hailuo Standard', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Hailuo Standard plan', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(16, 2, 'Hailuo Unlimited/Pro', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Hailuo Unlimited hoặc Pro plan', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(17, 2, 'Gamma Plus', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Gamma Plus plan', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(18, 2, 'Gamma Pro', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Gamma Pro plan', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(19, 2, 'HeyGen Creator', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'HeyGen Creator plan', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(20, 2, 'VidIQ Boost', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'VidIQ Boost plan', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:07', '2025-08-02 18:58:07'),
(21, 3, 'Elevenlab Creator', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Elevenlab Creator plan', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(22, 3, 'Minimax Creator', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Minimax Creator plan', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(23, 3, 'MiniMax Standard', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'MiniMax Standard plan', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(24, 4, 'Cursor Pro', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Cursor Pro plan', '1MONTH', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(25, 4, 'Augment 15k request', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Augment với 15k request', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(26, 4, 'Augment 30k request', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Augment với 30k request', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-03 10:47:18'),
(27, 4, 'Github Copilot', 'Tài khoản cấp (dùng riêng)', NULL, NULL, NULL, NULL, 'Github Copilot', '3 tháng', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(28, 5, 'CapCut Pro ( tài khoản cấp )', 'Tài khoản cấp (dùng riêng)', NULL, NULL, NULL, NULL, 'CapCut Pro', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-11-24 19:19:26'),
(29, 5, 'Canva Pro', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Canva Pro', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(30, 6, 'Duolingo Super', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Duolingo Super', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(31, 6, 'Quizlet Plus', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Quizlet Plus', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(33, 6, 'Coursera Business', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Coursera Business', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-03 10:47:02'),
(34, 7, 'YouTube Premium', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'YouTube Premium', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(35, 7, 'Netflix (hồ sơ riêng)', 'Tài khoản cấp (dùng riêng)', NULL, NULL, NULL, NULL, 'Netflix với hồ sơ riêng', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(36, 7, 'Vieon VIP', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'Vieon VIP', 'full', 'ổn định', 1, 1, NULL, NULL, '2025-08-02 18:58:28', '2025-08-02 18:58:28'),
(49, 1, 'CHATGPT PLANT', 'Tài khoản add family', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-08-03 17:04:05', '2025-11-24 19:04:53'),
(52, 5, 'CAPCUT PRO( chính chủ)', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-08-03 17:25:20', '2025-11-24 19:18:13'),
(54, 1, 'Perplexity  bảo hành 1 năm', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-08-08 15:57:45', '2025-08-08 15:57:45'),
(58, 1, 'Suno Pro', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-08-10 16:46:38', '2025-08-10 16:46:38'),
(59, 5, 'Adobe K12', 'Tài khoản cấp (dùng riêng)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-08-10 16:54:40', '2025-08-10 16:54:40'),
(61, 1, 'Chatgpt Plus Add Team (không gia hạn )', 'Tài khoản add family', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-08-10 17:05:08', '2025-11-24 19:05:28'),
(62, 2, 'Higgsfield', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-08-24 02:01:30', '2025-08-24 02:01:30'),
(63, 4, 'wordwall', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-08-28 16:07:55', '2025-08-28 16:07:55'),
(67, 4, 'Zoom Pro', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-09-01 15:13:13', '2025-09-01 15:13:13'),
(68, 2, 'Openart AI essential', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, 'tot', NULL, NULL, 1, 1, NULL, NULL, '2025-09-03 16:48:00', '2025-09-03 16:48:00'),
(69, 4, 'Wink VIP', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-09-03 16:49:24', '2025-09-03 16:49:24'),
(70, 2, 'DreamFace Pro', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-09-10 16:51:47', '2025-09-10 16:51:47'),
(71, 2, 'VEO 3 ultra ( tài khoản cấp )', 'Tài khoản cấp (dùng riêng)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-09-10 17:04:42', '2025-11-24 19:24:22'),
(72, 2, 'Pacdora Lite', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-09-10 17:15:16', '2025-09-10 17:15:16'),
(74, 4, 'Ofical 365', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-09-22 15:05:39', '2025-09-22 15:05:39'),
(75, 2, 'Veo 3  Ultra( chính chủ)', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-09-28 14:35:10', '2025-11-24 19:23:54'),
(76, 2, 'HIGGSFIELD', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-09-30 17:08:43', '2025-09-30 17:08:43'),
(77, 3, 'github copilot 1 thang', 'Tài khoản cấp (dùng riêng)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-10-02 15:45:09', '2025-10-02 15:45:09'),
(78, 5, 'My TV ( svip)', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-10-18 16:29:52', '2025-10-18 16:29:52'),
(79, 6, 'Lingokids', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-10-20 17:17:03', '2025-10-20 17:17:03'),
(81, 6, 'fb', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-10-21 18:15:09', '2025-10-21 18:15:09'),
(82, 6, 'Khóa học AI', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-11-06 18:08:35', '2025-11-06 18:08:35'),
(83, 1, 'Chat GPT Go', 'Tài khoản chính chủ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-11-16 15:30:27', '2025-11-16 15:30:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_packages_backup`
--

CREATE TABLE `service_packages_backup` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_duration_days` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('06Gjn2oULrkzwFSzNyH59cO5uPwsd9IHsiFg9Cod', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzZGbDRCMENJVmw3MGZ2c0hkaXVkTEZVNVBacUh2Z1lZUzBkS1RaUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI4OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9zZXJ2aWNlLXBhY2thZ2VzP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2Mjk4NDM2NiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756662985),
('0M34qp6mHSMBZMEdoCeAqyJLkBwu9N0vpKSOtB2T', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQjRGYnFwVFp0WEtsS3lvTHA2VDk5akx5WGEwT3k4VWt6eFlVTVpoZCI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760258815),
('0RrwAc5W531WdvticEsm20wvoMI6V5YMPY6owdim', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVERNTEl4ZEtTYnlHQ2NXVkh3Mzc3T1FVdkdXeTZCU0x6eEhmM05rMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTY6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4MC9hZG1pbi9jdXN0b21lcnMvNTIzL2Fzc2lnbi1zZXJ2aWNlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762369537),
('1aAp8bMxoCrohRgMkYHXuw0luHAQjL92VMoeuFeZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieXFzYmxNck5sVTJOWTZZWlh5T2JwUHM2NnZCS1h1WG1wbTZ0aVRyUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4MC9hZG1pbi9jdXN0b21lcnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762369534),
('1F0parR2bgRYQJD5xUkGNgfBxOcP7fVAoVgsV4BH', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ0xiVEZRSHlhUVZRMWs2TkhQWmd4OE0xeEo0TXdQWnF4QWloRmtORCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvNDMxL2Fzc2lnbi1zZXJ2aWNlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2NDAwNDMzNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756664006),
('1HVQJJE1PfW9FOi0xih3GwhS8mmj5TbpPEmr8MUS', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicnNYdlIwOWVBeUROQkJJYlVIZDFRMkZPQXM1WEZObGlDNVFsZmI1ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTE5OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXZlbnVlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MTM4MDU2NyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756661381),
('1V5xVqVTdIoUju2NoSKwjlTVic4SpynFy6OLjGrQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaWlLeTh6ZkdMdGl5V2UzVnhOQ2w4dEhZc0xmMXJMYlVxWHpkNmRuUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTE5OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9wcm9maXRzP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY1NzA5MDY4NyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756657093),
('3cdi5J9ZtoSXfU0IIoq3oEPYsZrRZZoqz4eHN3nI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.104.2 Chrome/138.0.7204.235 Electron/37.3.1 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTjZoTlp4N25vYTNuNGxCajI4ekhaOURzOGhCYUxIZnN3STVrSnFlWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTA4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vcmV2ZW51ZT9pZD02ZjBlN2U2MC0yOGY4LTQxMjMtOGMzNi02NGU0ZjllZjdiMGYmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTk1MTIzMzI4OTYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1759512333),
('3L4ER5A9K9iWysaN4h4bRqlZ6wIHsi7CZYGhDuwT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUGVITlpTU1lrclhLVUNRMEJidDlEUm1RMmxsaFFZQmxOaWZRZzF3aCI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1764011151),
('3yADQC1460UXd9MHt0tBfiX2OxF7yE0ubmbnPIag', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT2NEbVRUYTRER0ZBeWN4ekxVRDF0QzNGRE5GV1lKbk5KNnVNYW83OCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTc6Imh0dHA6Ly90cnV5Y3V1dGhvbmd0aW4udGVzdDo4MDgwL2FkbWluL3NlcnZpY2UtcGFja2FnZXMvMyI7fX0=', 1759199116),
('44mD0fMTCu5h96Q4r0c9qbqVF9xy10WMBMn79iGf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicXo2dERLcTMzZGpGam9GUVlJOU5NRkgwY0JLd3FnczRwcVB0Zm9VOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4MC9hZG1pbi9jdXN0b21lci1zZXJ2aWNlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1762369515),
('4PIkH3tvfOMtv6hXSqxVd8QBU4Y5ZooMxB0SENY0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXBQc3pnZnV2VVZxWWJMc25BSHhWN2ttZmRrWjJDcDRjbWlHU2V3SyI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760349519),
('50uENBICkxv5GqSmEAZfwOLFSLI8KMTUqrFCZSpY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWVRjRVNlQWtJU09MRWI5V0JvR0poU3NGSEtTZ1NUTjRvcURNdnozTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTIxOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnM/aWQ9YWJkNDNjNDMtMzA3ZS00MmM4LThhOTUtNDRjNTMyMWJjNTk1JnZzY29kZUJyb3dzZXJSZXFJZD0xNzU2NDYxMjcxODE5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1756461272),
('55stE7GxKyD0A3mMiv1qsIjqWeWHy0yKjHme61kL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRTY2SDBKTlRHWnZLTkxDOHd0dWk5aVkzWDA3Mk5LYUhkVmxHSGppZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI2OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXBvcnRzL3Byb2ZpdD9pZD03YmM2Y2E1ZC1iZjc0LTQyMGQtYTljZi0wOTY0ZTFiODk5MjgmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY0MDY5Mjc3ODgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756406929),
('6WWibMu6pLRnD3FTY9SbA0FKQSGZtkHAzaTD10Cx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOE5zdldRa2pYVUtSMmJybUllOGxnazFXNnZDU21uVnBmSU5wdTZpYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTE5OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXZlbnVlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MTE2MDg2OCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756661161),
('7qsWipQIMMpLbLLQbxjJhMeO96yyJYPZO3y77FNC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYWdJczFZa1ZRR0E3OEpVNGx4N3NQcFBIZDdjVkUwWEt5TjhYME1oZiI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760503451),
('8cRJ91EfWsZuR2FWRtgtUjWicPvNkDPYLLixdWqw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTG1DZ01qRVBtMTVyeVZwbFZldndWejVzdDhtUVlZOTU2Q1M1RE9pRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQ0OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lci1zZXJ2aWNlcz9maWx0ZXI9ZXhwaXJlZCZpZD05MjViNTIwZS1kMWJlLTRiMTMtOTJiMy0wMWVkZWQzNGI4MDcmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY1NTM3Mzg2MjYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756553738),
('8M7D1P76yxqybN7PXpesH5S2hDmV1v28J1ZaNH6X', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidmpKR0lrT0NOMm9tZHlzdm0xMkc3d2ZwckhxTjhyQWFJQmNPQ3g3ViI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1756656912),
('9nvSZg0c6fJ6Kh8LTtKrFNgtIu2mSjZeITLVFeDL', NULL, '127.0.0.1', 'curl/8.4.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZmdRNDJ6Z1owdVMyRVhvcWZkSUxXM0taNWxuUzlZdFh1VGdQSEh3VCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjM6Imh0dHA6Ly90cnV5Y3V1dGhvbmd0aW4udGVzdDo4MDgwL2FkbWluL3Byb2ZpdHMvdG9kYXktc3RhdGlzdGljcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756656888),
('9Ub781X2ragwjbIQA1Nm4fyg64GMZsratoIWDrK2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSktCTEpReUhwM2pITHVUTlYxVGFka0NPek1VaXh2dFlUaEtlalpERiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTE5OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXZlbnVlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MDg1NzE4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756660857),
('9vR0DNey94CKlIs96hnw08TWNRL9BrlGJwQssdqp', NULL, '127.0.0.1', 'curl/8.4.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYmtVcHRCZGZ4c3R0RjhvVEVXc2tVQjkyamlDSExPdnRhUHhvMnRrSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTA2OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXZlbnVlL2RhdGE/ZW5kX2RhdGU9MjAyNS0xMi0zMSZncm91cF9ieT1kYXkmc3RhcnRfZGF0ZT0yMDI1LTAxLTAxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762454011),
('acS3MXX1DErTZWl1x19nvym1yX3r6wg0l8efLwNy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRzNIdXBBUnZjQjQzUlRuSmFMMXN5ekVNSmFUZHRpaDVNMWxFc2V5MSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTE5OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXZlbnVlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MDMyODUyMiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756660332),
('BfPiICuBUil6u3nwdgKfc7Ebdrh5nv1um4vz8M3k', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ2s2bnRnaHYzVDJGRUk3WG5oVUxiY1RqdTFQUmU3aUlJTWhkUk82NiI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1764010315),
('Bip9gjuF8NiaXSoBEYUX16hgS4Y7e3eXPQquN2UL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieGhtQUNsQVdEWVZnbDhoWGpBckhNcEljbDByRkMxOFdlbTdLOThjQSI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1756713525),
('bNCbn3oyPsSnFGjqZoCHehdK1vk0aLkpe36XXvlw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNGtzUVZPcWpoQnFwdml0MHg1THA2S3VDS3BxZTMyYW1lMUdaT3pTbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQxOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9mYW1pbHktYWNjb3VudHMvMTkvYWRkLW1lbWJlcj9pZD03YmM2Y2E1ZC1iZjc0LTQyMGQtYTljZi0wOTY0ZTFiODk5MjgmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY0MDU2Mzg3NTIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756405638),
('ckjF1hkZ3wQGVrFrRbjN5F9rZsl7Oc0qx9IBUxKW', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT3FuU29JUkhNN0JYaEYyUGVrS3pVcVJnZ2hNT0ZkMlNVVTRXa0EybiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI5OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC90ZXN0L2NyZWF0ZS1sb29rdXAtZGF0YT9pZD03YmM2Y2E1ZC1iZjc0LTQyMGQtYTljZi0wOTY0ZTFiODk5MjgmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY0MDY4MzA0NzAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756406830),
('Clnuq2u0d1PWkOCENorTVafhLkKF3ni5WvcL0ddZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialRUN0l4dVBVNG1jeFFybncxQ1RWUHZQYVhVOFRDQ0thNjd2czFqbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI2OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9kZWJ1Zy9mYW1pbHktbWVtYmVycz9pZD00NzM5MTk5My1hY2ViLTQzYjgtYTI2YS0wZDUyODJjMTA1MzcmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY2NjQ3Nzk1MTUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756664779),
('cXgrV3bfp7h82GpJoyK4VZrUaSRs8ISmuMewUVYi', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUEg4STdDSW5FenJOWktrbkhseHlzZjJxbndFQjROaVdGWmFtZllsTCI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1763746251),
('d24AlRX1YR1Lbzt8SK1RDYqhjmnO0VWdzAwICu6B', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM3JXM3ZSMUg1Wnl3bWhiNXUwQ2NTRHhGTEt5bFpVVnByV1JPQU9mWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTM4OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lci1zZXJ2aWNlcy80NjUvZWRpdD9pZD00NzM5MTk5My1hY2ViLTQzYjgtYTI2YS0wZDUyODJjMTA1MzcmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY2NTkwMDUwMTgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756659005),
('DFc1GZmyZVGfPlrwSnCCxEMv5lgZjiBBOVF9kmfo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ09iT2tqTmF1ODdBUzhwWThOenRiaXRWVVJSSEpMYjBuWk1rV3FKRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvNDMxL2Fzc2lnbi1zZXJ2aWNlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2NDkyMTcwMyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756664921),
('DmhFsCbVff0O6p7i05ki4Tv0VcI83vQc1SNGqXFz', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRlhoblpOb2tNeGhIN21yNG9ZOWZmZWloTkVmVnJjamxjWEFsRWRyZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvNDMxL2Fzc2lnbi1zZXJ2aWNlP2lkPWU2YTQ2ZDY4LWMzMDQtNDM5Mi05MjMwLTJiOGYwNmFiYWVmNCZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjczOTgwODYwOSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756739808),
('EARuW0zRpKV0cNrlhX7pUsH9KdAbOmsA2ZMXJw8f', NULL, '127.0.0.1', 'curl/8.4.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibnVZanRmREk2eUxIdExGNzFwd2w3M2VFaEhqTVRDNVh1d3FRQmJRVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTA2OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXZlbnVlL2RhdGE/ZW5kX2RhdGU9MjAyNS0xMi0zMSZncm91cF9ieT1kYXkmc3RhcnRfZGF0ZT0yMDI1LTAxLTAxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762453950),
('EMbHpPeyyOYKlfWfCAnlfjxc3UT6ym1E3AuZJvao', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMnFKcldIZXFBNmgxOUFOVVZUZGVkWmJ5NDQ3eE9PR253bk94Wlk2NCI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1756408513),
('f0nZ6TwgkNEHjFMhxUDAeatyRm9wxDcwmZfHEfx0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYk1uR0Q2eFVBYUR1MkJ5VVRJMG1sSFpTdndUcnFUME1IaDVUaUNGTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI1OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvMTQwP2lkPTdiYzZjYTVkLWJmNzQtNDIwZC1hOWNmLTA5NjRlMWI4OTkyOCZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjQwMzM3MjE3OCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756403391),
('fcMhL2WLqtGasSvropM79IRoc9VxhxgR1rfcCiT3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid2pQbUtnWFN6Wkxlb2xyNTV3RWpNa2xVY0xPWW91VGtxd3JQbVdlaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTcwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lci1zZXJ2aWNlcy80NjEvZWRpdD9jdXN0b21lcl9pZD00MjEmaWQ9NDczOTE5OTMtYWNlYi00M2I4LWEyNmEtMGQ1MjgyYzEwNTM3JnNvdXJjZT1jdXN0b21lciZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY1OTc2Mjg4OSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756659764),
('hUl7GX1rOLSFAzkIe8BUrzyI80NqnAwr6Yyvu9yb', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFRjN09MUXNCbTZLbmFkWHgwNHpjWDR5MVM5RzdvbXJDMGFWOUk2dyI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760681613),
('HVDz4IGbjVAKBrQe9kgFK3BMfvXgDkGxg94QNcsz', NULL, '127.0.0.1', 'curl/8.4.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibENvaVZ4eE5WWHRaTUpTdnZxUDBOMGdwaFBTR0lxRzNPWFB3Q3NITSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTk6Imh0dHA6Ly90cnV5Y3V1dGhvbmd0aW4udGVzdDo4MDgwL2FkbWluL3Byb2ZpdHMvdG9kYXktb3JkZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1756658085),
('hwFpbkNdAmcMjG3nXkYynsQphbBQjG76gQ5g1EWF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFpWb1E5M1hGazZZdE00aG9iRnF2bk5JUGtrNjlKRDZzeXRwcXRlNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTMwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9mYW1pbHktYWNjb3VudHMvMTk/aWQ9N2JjNmNhNWQtYmY3NC00MjBkLWE5Y2YtMDk2NGUxYjg5OTI4JnZzY29kZUJyb3dzZXJSZXFJZD0xNzU2NDA1MDkwMjI3Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1756405090),
('iSZKiKCDxIRnSwwgSB3L0Dbvt4K2nUuN3DbUpnUe', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia1p3d3FSRXplS2FQcmxyTjdDRmlaYmZOUkkwS0o3REV6OTFmb0hFSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI2OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXBvcnRzL3Byb2ZpdD9pZD03YmM2Y2E1ZC1iZjc0LTQyMGQtYTljZi0wOTY0ZTFiODk5MjgmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY0MDYxMzkzMzUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756406140),
('IZsPviaYOYb5DniCnG74Gs11Ivxx2CzcRmeDR9st', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibmNTUmRFb29IYTB4Y2E4Y0pUTGVoa0RBM1UzbFBSQ21vV29zQXR6eCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvNDMxL2Fzc2lnbi1zZXJ2aWNlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MzUzODExNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756663539),
('js7aH93DMhvZqfRLvjjnS7S9yJ1rwIWlfGxPYgsc', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmZ6d1Y3WHo1NjNQYzFJWmRJV0JNcE9zT2ZtT0VXdnI4a2lmV3ljViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTMxOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi90ZXN0LXByb2ZpdHMtZGlyZWN0P2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY1NzI4NjgxNiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756657287),
('KepiYAkJKsdxHBIPWjdTGcuO9C4VsfJYLQYWuCbZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNng5ZXBnU1ExbERYdVZxZGFsVkFINktJV0l6aExrUlRVcFRQTndFVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTEzOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC90cmEtY3V1P2lkPTdiYzZjYTVkLWJmNzQtNDIwZC1hOWNmLTA5NjRlMWI4OTkyOCZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjQwNjgzNzAwNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756406837),
('L6RBpZnuLo3mTbMZPYafxxdICnlNCKFraFeypFBY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicnpTQ0JqTjF3aHc3OG0wbEFIR3VrcVRZSk5aVjRFY0ozZU1MT3NIdSI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1764011321),
('LAtOCeGgNEe7rghxVo8mN1TLPaCMivXw2j9tAUPi', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUHNYaWdsTTRVeGduY3o4ZE5YV1lQem5tYjJEQTlRcmdxaHpFR2lZOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI2OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9kZWJ1Zy9mYW1pbHktbWVtYmVycz9pZD00NzM5MTk5My1hY2ViLTQzYjgtYTI2YS0wZDUyODJjMTA1MzcmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY2NjQ3MTY0MzUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756664716),
('LBRroPmpCYOZGo2ROjNHbGT7qc1AnK0vhINGCLa2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSHZCWW9XZjBJR3ZSQ2lkUTc4ejN3OWdBWDdHenBBTW45bnRjTTUzYyI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760681722),
('lpW3NRtXsidEb7y0SOQh54l0LV2DTlaIdrOxKUD4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMU1VaHhQTnRDdTFvUHdNODVyU2JidkRpeVg3ZEVFT21aYlllZW1lbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTEwOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYWRtaW4vaWNvbi1kZW1vP2lkPTkxZDQyZjU2LTFlOTktNDE3MC1hNDY4LTQ3NmU3MTBhMGU5MCZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjQ4MzQ5OTQ2OSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756483500),
('MeYBq3CEYYd2b9q7p4FiiQ4mwTY5tK3bp7FOy0xi', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiem5KeE5rQmc1ZURtcmMxMEVDaUEzT0RUQnFqQmZzRUpQNGtzQ0xaQiI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760258841),
('MQXooYddSwy4ILoKAYmwtWYjWMAPq4pNjHII7ib4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNzV3bldxUXRUazFUNFFQMzJMcVdLY2J6d1VBSWtaQmE5b0ZFaGl0diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQxOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9mYW1pbHktYWNjb3VudHMvMTkvYWRkLW1lbWJlcj9pZD03YmM2Y2E1ZC1iZjc0LTQyMGQtYTljZi0wOTY0ZTFiODk5MjgmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY0MDU2NjQyODgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756405664),
('mrHJHhbSBncc8jdaA2JahkGBUbZkTKLmum9IMslv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTjQ5aXZGT1VZZXVBZVN5ckZCMWlvU1lZVDFmVGU1MDFQVUgxRExQRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvNDMxL2Fzc2lnbi1zZXJ2aWNlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2NDMxNDIyMiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756664315),
('MW4c2FOEg0eyQtUu8bSuyVE6gHtN22Yd5icspLad', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQzhFVngzTXdaV1hFTjlUNmQzM3JETkNCSm5LSHY4Wno3U3F0SEhiTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTIxOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnM/aWQ9MWNmNDQ0Y2ItYmNkYi00NjMxLWE5NmEtMWE5Nzc0NmE3NTRiJnZzY29kZUJyb3dzZXJSZXFJZD0xNzU2NDYxMTA2Nzg0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1756461107),
('NrNXpxWXqKa0stvJaKJkfJiIKoKVA6zu4uNTrhcZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiR1ZBZVhWcThMaHFWdlk4V1ZKbGFOalZxQWdVcU11Q3RLUnJJZGdIQiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762369603),
('OaVawkijfYSTHtzAEEOc2EpYjD8tVq7hEKF4K1fo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNW5vc0RqRzhjOGZyUTdqV0thbU5pdndYc2hKSTlEOWNFR1BlUUw5biI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI4OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9zZXJ2aWNlLXBhY2thZ2VzP2lkPTkxZDQyZjU2LTFlOTktNDE3MC1hNDY4LTQ3NmU3MTBhMGU5MCZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjQ4MjY1NTIxOCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756482655),
('oH0YfVoIZ6cKNX2WNKgzrmSI6YLhEPv8tgOmWtaA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV3U5Y0tvNmt2NmM3eXZOVGRzZUg0MUJuTFJvclI4UW9UYWpSQm5FbCI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1763746162),
('OMILB2TxEuqDcvnHH6tH4jLUogpIU8GxuVgauzhF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieGFkZGd3MU94dlc1bDZzVDJDVW1lVzdRNnhNU3Z0MWtKdnJiRTZuYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQxOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9mYW1pbHktYWNjb3VudHMvMTkvYWRkLW1lbWJlcj9pZD03YmM2Y2E1ZC1iZjc0LTQyMGQtYTljZi0wOTY0ZTFiODk5MjgmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY0MDU0OTY5NjUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756405497),
('pfM52YrpyLTm7CuwgDn1PwxqYSfzb5LtJfZ7En0W', NULL, '127.0.0.1', 'curl/8.4.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZTVtN0VrOWpYSUNFQTExOVZyVzhUb2lLVzNYajlkVVRDcUI0b0x4USI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTA2OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXZlbnVlL2RhdGE/ZW5kX2RhdGU9MjAyNS0xMS0wNyZncm91cF9ieT1kYXkmc3RhcnRfZGF0ZT0yMDI1LTExLTA2Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762454024),
('PMK928fsOTKUzhPdMaSspes11ZLX6I6o0xLHaifF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUWQyWlIxVVZBWlZmM2F3QlduQXR4TzZEVFRZNWN6ekZXTTUxSUJmNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTcwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lci1zZXJ2aWNlcy80NjEvZWRpdD9jdXN0b21lcl9pZD00MjEmaWQ9NDczOTE5OTMtYWNlYi00M2I4LWEyNmEtMGQ1MjgyYzEwNTM3JnNvdXJjZT1jdXN0b21lciZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY1OTI1OTMxMyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756659260),
('PUHVS4AYgQ9ZHmId0YMoRy3hhtgYXRWDMmjzXJsV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNHI3Q0VqeWt5TUNzVzZuOWxNNExXd3VkV051TTVWNjlMOEdmY254aCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI0OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi90ZXN0LXByb2ZpdHM/aWQ9NDczOTE5OTMtYWNlYi00M2I4LWEyNmEtMGQ1MjgyYzEwNTM3JnZzY29kZUJyb3dzZXJSZXFJZD0xNzU2NjU3MDE5MjYyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1756657019),
('qFXiq94a7IYCFeMY77wmxEYY6ofo4SOuO3b0t8KO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYTQ2TzB1Q1hHcXRVYXR5c3BtOHpRUmlXM09JbVlSbXJTQm5ORjJQRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvNDMxL2Fzc2lnbi1zZXJ2aWNlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MzY4MzAzNyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756663683),
('qGodxV9zOt9cSJ1GykJC5LLEmnDdGwLZO4Bk4ccn', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFcxaVdnY3docFZ2N2YwMWZTeDB4bzVGNTZxaHgxMm9Cb3lZUTlkRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI1OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvMTQwP2lkPTk2MWIxMWM1LTYzODgtNDc5YS04NzFkLTAwMTJhNjc2MWY3YSZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjM5Njc2MzY2MyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756396764),
('QpICE3KsbNvCXxcHJsNvNWwx4RCtGwjln652HNKE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSTNiVWZKZTB6anFMUThFb0tqUnNXNndVWDV1VlBMQUExTUJ1S21qMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTcwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lci1zZXJ2aWNlcy80NjEvZWRpdD9jdXN0b21lcl9pZD00MjEmaWQ9NDczOTE5OTMtYWNlYi00M2I4LWEyNmEtMGQ1MjgyYzEwNTM3JnNvdXJjZT1jdXN0b21lciZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY1OTMxODA4NSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756659319),
('rUNpWnaApgAR5efTxxG4oRvSPFaU9muW33AKV0Kd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzIwMWdTUzhDeHVUMEw3MnhGYm96RDFTb1hoVnBhSUZxVDRwMUk1RiI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760330916),
('s2sbINr5H0preGShFtfpJfzDzprbsTAhnVtyxF8R', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTk1yN1NFSUdDT1padjhGUlR1cjdPaHRvcHk3eFpwTGdSclhKTkJZZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTIxOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnM/aWQ9OTFkNDJmNTYtMWU5OS00MTcwLWE0NjgtNDc2ZTcxMGEwZTkwJnZzY29kZUJyb3dzZXJSZXFJZD0xNzU2NDgyNTEwMzc3Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1756482523),
('SCl4uz6Q2JjxSajK4i0rwceexcXlFXlDhl5ceoXG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTllhUTMxYkFJcHE4d3pyMkF3V2JvOWVUQXRUN3lHckZMTkkzV0VqQiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI4OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvY3JlYXRlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MjA5NDQxMiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756662094),
('sMdb9yQm21CoZYgJTrs1kvZj8HX4xDHI7YuddOHm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTGtXRGY4Q3l6ek9SQUhwVkJUZWtsRVNnVnk5OUk4SG9mbW5ncUtJTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvNDMxL2Fzc2lnbi1zZXJ2aWNlP2lkPTY2NGE3OTYzLWIyYTktNGU5MC1hYjczLWQ0ZDY2M2E5OGZlZiZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjcxNDEwMjUwOSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756714104),
('SZQ79cOcMC2O6tohI8Ub0Oc2LqKkYaaURDvbyMe3', NULL, '127.0.0.1', 'curl/8.4.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialpjUmRSWjVPRnU5RDJnNUZ3Rmh5aGdaZWdmMzkyeDlVYUplbGVQZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTk6Imh0dHA6Ly90cnV5Y3V1dGhvbmd0aW4udGVzdDo4MDgwL2FkbWluL3Byb2ZpdHMvdG9kYXktb3JkZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1756658062),
('toJIT9QFlhEkUQJHRfk8RlAbC4kTI5YkgAhWOrH1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQTVHaVZkODFnUE9xbDZ3UUQ2V3ZtajZpT3poOG41ZzJUNVJ4ajMwaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTA0OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvaWNvbi10ZXN0P2lkPTkxZDQyZjU2LTFlOTktNDE3MC1hNDY4LTQ3NmU3MTBhMGU5MCZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjQ4MzMzMTQ4NCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756483332),
('tRrnhY8lWyBu001KQaeKcA86MCMuhAdTGQtNjShj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMjdZYmc2Rmc3R2VVbWlBU2psandJdEpkOXJGc0JWTFB6a0V6VWVvTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly90cnV5Y3V1dGhvbmd0aW4udGVzdDo4MDgwL2FkbWluL2N1c3RvbWVycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1764012840),
('TwHWN761MflvXVEBDBOvj9ZC6hsnf5Ir9TWwHakk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZGpkdWdMa1pTM0NMZzMxb0dLdGU1elltRzN0NDdBQkxFemZ5RWp2MCI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760258330),
('U5BxVnJ8lLprKh6L3bOK04NGaxnazLfKKTZnQcLj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS2NjVGhqNHpkNkpDUFR1UXBQc1B2cjJvVlA1MWhuWkVjRzVNZURWWSI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760681714),
('uDXJeHDV15dNbGj0e4WaxhDVwBxl35jObU1TeoIQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVGlqNlNYTUJ4NU5GTEU4dzNWbmNVNnhtWW9IcUh1TW1keTZldVowcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTQwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvNDMxL2Fzc2lnbi1zZXJ2aWNlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2NDQ1MjUwNyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756664453),
('ufbefzDyehODnR5gSn3gn2AYpVyEg9PFdyoMyxWI', NULL, '127.0.0.1', 'curl/8.4.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQURrMnlLT2t4MXRvUXU4ZThFTHBSSzlGWFR3dUZTVlhKRnVHaktrZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTk6Imh0dHA6Ly90cnV5Y3V1dGhvbmd0aW4udGVzdDo4MDgwL2FkbWluL3Byb2ZpdHMvdG9kYXktb3JkZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1756656716),
('uQmt2jOoNpMCwe53jnoEdr1X5vGirpdFlrbbXhgD', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibFkxVU95bTJ5UlEyTUV4MXA4U2tFcE5CYW5QTERVWWJqNkxOS0JPNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTEzOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC90cmEtY3V1P2lkPTdiYzZjYTVkLWJmNzQtNDIwZC1hOWNmLTA5NjRlMWI4OTkyOCZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjQwNjUzODc0NiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756406539),
('vbQIRlOWsN6AIEaw7R3KEG6gRjbaFVpemLsYwtkW', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicmdLamNGMTlSSlFlYjA1RWdsdE9NeHp5b1VCSmt4aUNONVRyQTZCaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTE5OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9wcm9maXRzP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY1NjY2MzUwOCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756656663),
('VZs0BaVA13eArBuHsPd6SgnwXGjjgckHJQR1szGK', NULL, '127.0.0.1', 'curl/8.4.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoianlIdWNTSFFaMERsT3RTa1k1dkZnS0U0QzBmcURZNWlUTnRoU3NUSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6OTM6Imh0dHA6Ly90cnV5Y3V1dGhvbmd0aW4udGVzdDo4MDgwL2FkbWluL3JldmVudWUvZGF0YT9lbmRfZGF0ZT0yMDI1LTEwLTEyJnN0YXJ0X2RhdGU9MjAyNS0xMC0xMiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1760205440),
('W6KYKSGsF9jXTGn8oQ907fsPgpY3EnbEu9QaBJwE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib29qU2ttd2VHV0NhVUNycENCcXd6ZjBqWDhnQnVvMkVkTnNSZjNpUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI4OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvY3JlYXRlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MjA2ODA4MyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756662068),
('Whe3QyItLpFC7PQYAWwCSKc5dZLdla48UBp4p1Oo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQzBIZnF6MDhkRVBKYmU5d1IzS1BHT1hBaEFScmNmSzBUbEJpRGlVbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTcwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lci1zZXJ2aWNlcy80NjEvZWRpdD9jdXN0b21lcl9pZD00MjEmaWQ9NDczOTE5OTMtYWNlYi00M2I4LWEyNmEtMGQ1MjgyYzEwNTM3JnNvdXJjZT1jdXN0b21lciZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY1OTE3MzUzNCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756659174),
('wPAn7r9oIE1xhIZ9DJSGEC29yuIgVE4y5GdntTAC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia2R5ZDFCQk92TUZuUGtUR1ZzNTBwcGlQaVMwOE5DeG5qcEdEWkFIaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI2OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXBvcnRzL3Byb2ZpdD9pZD03YmM2Y2E1ZC1iZjc0LTQyMGQtYTljZi0wOTY0ZTFiODk5MjgmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY0MDYyMzY0MTIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756406237),
('wVpfVKXTNtBGXsNZ7xArEQ3JaSVTMZfinwLAWJCj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVlZTNmF5TDFlVnlnSHpRSVZrWDU0eHBPWXY0Qm9uZU1JOHFYQ3ZBaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTEzOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC90cmEtY3V1P2lkPTdiYzZjYTVkLWJmNzQtNDIwZC1hOWNmLTA5NjRlMWI4OTkyOCZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjQwNjUwMzk0NCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756406504),
('xA6Mb156yXhqHfny1Q2c3fe5SZCu2Qc40XDneOij', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSm41aXh1c2J1Zk1EanpXYnRuM3hyalZkQXkxTjNDSHpkbElmWUZ1NSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI4OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lcnMvY3JlYXRlP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MTgzNDI4OSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756661834),
('xSOSd7SYucE33Pz2HBjJBeIjbWOCk5jpzmZh5fm3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYnB4eHdIcURES1owNEJ0eEFlVmFaZ3ZLY3ByeEU1cnF5M2NUUnFDZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI2OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9yZXBvcnRzL3Byb2ZpdD9pZD03YmM2Y2E1ZC1iZjc0LTQyMGQtYTljZi0wOTY0ZTFiODk5MjgmdnNjb2RlQnJvd3NlclJlcUlkPTE3NTY0MDcwNDYxNjIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756407046),
('zBqI2WcfY2juzC6HwzdqDYGWvBTYESpsDkITkGsy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibVc0SFBZVmRONm5tbnRTWnRLNWxlbWJSN29sNE15enJWTjdscFVLbiI7czo3OiJzdWNjZXNzIjtzOjI3OiLEkMSDbmcgeHXhuqV0IHRow6BuaCBjw7RuZyEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YToxOntpOjA7czo3OiJzdWNjZXNzIjt9fX0=', 1760258808),
('ZHMLOx10sxoJSWhrfCXAnOu8cHgk5znNEaBp7Lop', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWl5T2FFOVpmQ29RYlNEQkt3d1lJRkplTG1Kd2NoSkhHTjZBVE1vaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTcwOiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9jdXN0b21lci1zZXJ2aWNlcy80NjEvZWRpdD9jdXN0b21lcl9pZD00MjEmaWQ9NDczOTE5OTMtYWNlYi00M2I4LWEyNmEtMGQ1MjgyYzEwNTM3JnNvdXJjZT1jdXN0b21lciZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY1OTU3NTI5NyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756659576),
('zjTFeYq42GKi5LF9a1S4wZavVZbfXcb2vVIjUxnI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.103.2 Chrome/138.0.7204.100 Electron/37.2.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicTR2dUlyMVdma1prTTl2cXQ5REhSdXhvRWlPUTBKTnRDWGJDdE1qUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTI4OiJodHRwOi8vdHJ1eWN1dXRob25ndGluLnRlc3Q6ODA4MC9hZG1pbi9zZXJ2aWNlLXBhY2thZ2VzP2lkPTQ3MzkxOTkzLWFjZWItNDNiOC1hMjZhLTBkNTI4MmMxMDUzNyZ2c2NvZGVCcm93c2VyUmVxSWQ9MTc1NjY2MjgxNjIwNyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756662817);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shared_account_logout_logs`
--

CREATE TABLE `shared_account_logout_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `login_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email tài khoản dùng chung',
  `service_package_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên gói dịch vụ',
  `logout_at` timestamp NOT NULL COMMENT 'Thời điểm logout',
  `performed_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Người thực hiện logout',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Lý do logout',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú thêm',
  `affected_customers` json DEFAULT NULL COMMENT 'Danh sách khách hàng bị ảnh hưởng',
  `affected_count` int NOT NULL DEFAULT '0' COMMENT 'Số lượng khách hàng bị ảnh hưởng',
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa chỉ IP thực hiện',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User agent',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `shared_account_logout_logs`
--

INSERT INTO `shared_account_logout_logs` (`id`, `login_email`, `service_package_name`, `logout_at`, `performed_by`, `reason`, `notes`, `affected_customers`, `affected_count`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 'gaschburdab0@outlook.com', 'ChatGPT Plus dùng chung', '2025-08-25 04:36:54', 'Admin', 'Thành viên hết hạn', NULL, '[{\"id\": 115, \"name\": \"Li Ti4\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-08-21 17:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 120, \"name\": \"Tiến Đức\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-10-17 17:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 271, \"name\": \"Duy Khánh\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-08-17 17:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 322, \"name\": \"NgọC\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-06 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 144, \"name\": \"Phạm Hồng Nhung\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-04 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}]', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-25 04:36:54', '2025-08-25 04:36:54'),
(2, '64jxcb2c@taikhoanvip.io.vn', 'ChatGPT Plus dùng chung', '2025-08-25 04:39:14', 'Admin', 'Thành viên hết hạn', 'sfs', '[{\"id\": 80, \"name\": \"Kim Ngọc Nam\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-03 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 83, \"name\": \"Quỳnh Như\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-08-24 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 86, \"name\": \"Lại Hoàng Thế Vũ\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-08-23 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 88, \"name\": \"Trần Minh Tuân\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-08 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}]', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-25 04:39:14', '2025-08-25 04:39:14'),
(3, 'dtkien18@gmail.com', 'ChatGPT Plus dùng chung', '2025-08-25 04:39:47', 'Admin', NULL, NULL, '[{\"id\": 246, \"name\": \"Nguyễn Hoàng Long\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-10 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 306, \"name\": \"Duy Mirae\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-08-23 17:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 336, \"name\": \"Nguyễn Thị Thanh Loan\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-08-28 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}]', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-25 04:39:47', '2025-08-25 04:39:47'),
(4, 'leehoangtung435@gmail.com', 'ChatGPT Plus dùng chung', '2025-09-04 05:32:09', 'Admin', 'Thành viên hết hạn', 'sfd', '[{\"id\": 94, \"name\": \"Minh Khoa\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-09 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 89, \"name\": \"Lâm Phương\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-02 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 331, \"name\": \"PhạM Thị Minh Lý\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-08-27 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}]', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 05:32:09', '2025-09-04 05:32:09'),
(5, '64jxcb2c@taikhoanvip.io.vn', 'ChatGPT Plus dùng chung', '2025-09-04 05:35:23', 'Admin', 'Thành viên hết hạn', 'ss', '[{\"id\": 80, \"name\": \"Kim Ngọc Nam\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-03 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 86, \"name\": \"Lại Hoàng Thế Vũ\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-24 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 88, \"name\": \"Trần Minh Tuân\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-08 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}]', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 05:35:23', '2025-09-04 05:35:23'),
(6, 'kien83667@gmail.com', 'ChatGPT Plus dùng chung', '2025-09-04 05:37:02', 'Admin', 'Thành viên hết hạn', NULL, '[{\"id\": 146, \"name\": \"Nguyễn Xuân Lợi\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-09 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 153, \"name\": \"Nguyễn Duy Chiến\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-05 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 162, \"name\": \"Trịnh Bảo\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-04 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 318, \"name\": \"Hoàng Hợi\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-07 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 319, \"name\": \"An Yên\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-04 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 157, \"name\": \"Vĩnh Qui\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-10-05 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}]', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 05:37:02', '2025-09-04 05:37:02'),
(7, 'dtkien18@gmail.com', 'ChatGPT Plus dùng chung', '2025-09-11 06:41:20', 'Admin', 'Thành viên hết hạn', NULL, '[{\"id\": 246, \"name\": \"Nguyễn Hoàng Long\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-09-10 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 306, \"name\": \"Duy Mirae\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-08-23 17:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}, {\"id\": 336, \"name\": \"Nguyễn Thị Thanh Loan\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-08-28 00:00:00\", \"service_name\": \"ChatGPT Plus dùng chung\"}]', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-11 06:41:20', '2025-09-11 06:41:20'),
(8, 'doanmaitanbinhz@gmail.com', 'Chat GPT 3 tháng 2 thiết bị ( dùng chung )', '2025-11-11 19:12:14', 'Admin', 'Thành viên hết hạn', NULL, '[{\"id\": 345, \"name\": \"Hà Phương\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-11-09 00:00:00\", \"service_name\": \"Chat GPT 3 tháng 2 thiết bị ( dùng chung )\"}, {\"id\": 249, \"name\": \"đăng đăng\", \"email\": null, \"phone\": null, \"expires_at\": \"2026-02-08 00:00:00\", \"service_name\": \"Chat GPT 3 tháng 2 thiết bị ( dùng chung )\"}, {\"id\": 105, \"name\": \"Dương Văn Tuấn\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-11-08 00:00:00\", \"service_name\": \"ChatGPT 3 tháng dùng chung\"}, {\"id\": 191, \"name\": \"In Ấn Hồng Phát\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-11-08 00:00:00\", \"service_name\": \"ChatGPT 3 tháng dùng chung\"}, {\"id\": 250, \"name\": \"Vi Vi\", \"email\": null, \"phone\": null, \"expires_at\": \"2025-11-09 00:00:00\", \"service_name\": \"ChatGPT 3 tháng dùng chung\"}]', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-11 19:12:14', '2025-11-11 19:12:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã người cấp hàng (tự động sinh)',
  `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên người cấp',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_code`, `supplier_name`, `created_at`, `updated_at`) VALUES
(1, 'SUP00001', 'Nguyễn Văn An', '2025-08-02 19:03:22', '2025-08-02 19:03:22'),
(2, 'SUP00002', 'Công ty TNHH Phong Vũ', '2025-08-02 19:03:22', '2025-08-02 19:03:22'),
(3, 'SUP00003', 'Trần Thị Bình', '2025-08-02 19:03:22', '2025-08-02 19:03:22'),
(4, 'SUP00004', 'Công ty Cổ phần FPT', '2025-08-02 19:03:22', '2025-08-02 19:03:22'),
(5, 'SUP00005', 'Lê Hoàng Dũng', '2025-08-02 19:03:22', '2025-08-02 19:03:22'),
(6, 'SUP00006', 'Công ty TNHH Thế Giới Di Động', '2025-08-02 19:03:22', '2025-08-02 19:03:22'),
(7, 'SUP00007', 'Phan Văn Cường', '2025-08-02 19:03:22', '2025-08-02 19:03:22'),
(8, 'SUP00008', 'Công ty CP Điện máy Xanh', '2025-08-02 19:03:22', '2025-08-02 19:03:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `supplier_products`
--

CREATE TABLE `supplier_products` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên sản phẩm',
  `price` decimal(15,2) NOT NULL COMMENT 'Giá tiền',
  `warranty_days` int NOT NULL DEFAULT '0' COMMENT 'Số ngày bảo hành',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả sản phẩm',
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đơn vị (cái, chiếc, kg...)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `target_groups`
--

CREATE TABLE `target_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên nhóm',
  `group_link` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Link nhóm Zalo',
  `group_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID nhóm Zalo',
  `topic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chủ đề nhóm',
  `total_members` int NOT NULL DEFAULT '0' COMMENT 'Tổng số thành viên',
  `opening_date` date DEFAULT NULL COMMENT 'Ngày khai giảng (nếu có)',
  `group_type` enum('competitor','own') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'competitor' COMMENT 'Loại nhóm: đối thủ hoặc của mình',
  `status` enum('active','inactive','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_scanned_at` timestamp NULL DEFAULT NULL COMMENT 'Lần quét thành viên cuối',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả nhóm',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `zalo_accounts`
--

CREATE TABLE `zalo_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên tài khoản Zalo',
  `email_or_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email hoặc SĐT',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mật khẩu (encrypted)',
  `access_token` text COLLATE utf8mb4_unicode_ci COMMENT 'Access token',
  `refresh_token` text COLLATE utf8mb4_unicode_ci COMMENT 'Refresh token',
  `token_expires_at` timestamp NULL DEFAULT NULL COMMENT 'Token expiry time',
  `status` enum('active','inactive','blocked','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `daily_message_limit` int NOT NULL DEFAULT '100' COMMENT 'Giới hạn tin nhắn/ngày',
  `messages_sent_today` int NOT NULL DEFAULT '0' COMMENT 'Số tin đã gửi hôm nay',
  `last_message_date` date DEFAULT NULL COMMENT 'Ngày gửi tin cuối',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `collaborators`
--
ALTER TABLE `collaborators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `collaborators_collaborator_code_unique` (`collaborator_code`);

--
-- Chỉ mục cho bảng `collaborator_services`
--
ALTER TABLE `collaborator_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collaborator_services_collaborator_id_foreign` (`collaborator_id`);

--
-- Chỉ mục cho bảng `collaborator_service_accounts`
--
ALTER TABLE `collaborator_service_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collaborator_service_accounts_collaborator_service_id_foreign` (`collaborator_service_id`);

--
-- Chỉ mục cho bảng `content_posts`
--
ALTER TABLE `content_posts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `conversion_logs`
--
ALTER TABLE `conversion_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversion_logs_group_member_id_foreign` (`group_member_id`),
  ADD KEY `conversion_logs_message_log_id_foreign` (`message_log_id`),
  ADD KEY `conversion_logs_own_group_id_foreign` (`own_group_id`),
  ADD KEY `conversion_logs_campaign_id_joined_at_index` (`campaign_id`,`joined_at`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_customer_code_unique` (`customer_code`);

--
-- Chỉ mục cho bảng `customer_services`
--
ALTER TABLE `customer_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_services_customer_id_foreign` (`customer_id`),
  ADD KEY `customer_services_service_package_id_foreign` (`service_package_id`),
  ADD KEY `customer_services_supplier_id_foreign` (`supplier_id`),
  ADD KEY `customer_services_supplier_service_id_foreign` (`supplier_service_id`),
  ADD KEY `customer_services_assigned_by_foreign` (`assigned_by`),
  ADD KEY `customer_services_family_account_id_foreign` (`family_account_id`);

--
-- Chỉ mục cho bảng `customer_services_backup`
--
ALTER TABLE `customer_services_backup`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `family_accounts`
--
ALTER TABLE `family_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `family_accounts_family_code_unique` (`family_code`),
  ADD KEY `family_accounts_created_by_foreign` (`created_by`),
  ADD KEY `family_accounts_managed_by_foreign` (`managed_by`),
  ADD KEY `family_accounts_status_expires_at_index` (`status`,`expires_at`),
  ADD KEY `family_accounts_service_package_id_status_index` (`service_package_id`,`status`),
  ADD KEY `family_accounts_owner_email_index` (`owner_email`),
  ADD KEY `family_accounts_family_code_index` (`family_code`);

--
-- Chỉ mục cho bảng `family_members`
--
ALTER TABLE `family_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_family_customer` (`family_account_id`,`customer_id`),
  ADD KEY `family_members_added_by_foreign` (`added_by`),
  ADD KEY `family_members_removed_by_foreign` (`removed_by`),
  ADD KEY `family_members_family_account_id_status_index` (`family_account_id`,`status`),
  ADD KEY `family_members_customer_id_status_index` (`customer_id`,`status`),
  ADD KEY `family_members_member_email_index` (`member_email`),
  ADD KEY `idx_status_family_account` (`status`,`family_account_id`),
  ADD KEY `idx_role_status` (`member_role`,`status`),
  ADD KEY `idx_created_status` (`created_at`,`status`),
  ADD KEY `family_members_member_name_index` (`member_name`);

--
-- Chỉ mục cho bảng `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_members_target_group_id_zalo_id_unique` (`target_group_id`,`zalo_id`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leads_service_package_id_foreign` (`service_package_id`),
  ADD KEY `leads_assigned_to_foreign` (`assigned_to`),
  ADD KEY `leads_customer_id_foreign` (`customer_id`);

--
-- Chỉ mục cho bảng `lead_activities`
--
ALTER TABLE `lead_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_activities_user_id_foreign` (`user_id`),
  ADD KEY `lead_activities_lead_id_created_at_index` (`lead_id`,`created_at`),
  ADD KEY `lead_activities_type_index` (`type`);

--
-- Chỉ mục cho bảng `lead_care_schedules`
--
ALTER TABLE `lead_care_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_care_schedules_lead_id_foreign` (`lead_id`);

--
-- Chỉ mục cho bảng `message_campaigns`
--
ALTER TABLE `message_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_campaigns_target_group_id_foreign` (`target_group_id`),
  ADD KEY `message_campaigns_own_group_id_foreign` (`own_group_id`);

--
-- Chỉ mục cho bảng `message_logs`
--
ALTER TABLE `message_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_logs_group_member_id_foreign` (`group_member_id`),
  ADD KEY `message_logs_campaign_id_sent_at_index` (`campaign_id`,`sent_at`),
  ADD KEY `message_logs_zalo_account_id_sent_at_index` (`zalo_account_id`,`sent_at`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `potential_suppliers`
--
ALTER TABLE `potential_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `potential_suppliers_supplier_code_unique` (`supplier_code`);

--
-- Chỉ mục cho bảng `potential_supplier_services`
--
ALTER TABLE `potential_supplier_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `potential_supplier_services_potential_supplier_id_foreign` (`potential_supplier_id`);

--
-- Chỉ mục cho bảng `profits`
--
ALTER TABLE `profits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profits_created_by_foreign` (`created_by`),
  ADD KEY `profits_customer_service_id_created_at_index` (`customer_service_id`,`created_at`);

--
-- Chỉ mục cho bảng `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `service_categories_backup`
--
ALTER TABLE `service_categories_backup`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `service_packages`
--
ALTER TABLE `service_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_packages_category_id_foreign` (`category_id`);

--
-- Chỉ mục cho bảng `service_packages_backup`
--
ALTER TABLE `service_packages_backup`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `shared_account_logout_logs`
--
ALTER TABLE `shared_account_logout_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shared_account_logout_logs_login_email_logout_at_index` (`login_email`,`logout_at`),
  ADD KEY `shared_account_logout_logs_logout_at_index` (`logout_at`),
  ADD KEY `shared_account_logout_logs_performed_by_index` (`performed_by`),
  ADD KEY `shared_account_logout_logs_login_email_index` (`login_email`);

--
-- Chỉ mục cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_supplier_code_unique` (`supplier_code`);

--
-- Chỉ mục cho bảng `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_products_supplier_id_foreign` (`supplier_id`);

--
-- Chỉ mục cho bảng `target_groups`
--
ALTER TABLE `target_groups`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Chỉ mục cho bảng `zalo_accounts`
--
ALTER TABLE `zalo_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `zalo_accounts_email_or_phone_unique` (`email_or_phone`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `collaborators`
--
ALTER TABLE `collaborators`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `collaborator_services`
--
ALTER TABLE `collaborator_services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `collaborator_service_accounts`
--
ALTER TABLE `collaborator_service_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `content_posts`
--
ALTER TABLE `content_posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `conversion_logs`
--
ALTER TABLE `conversion_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=569;

--
-- AUTO_INCREMENT cho bảng `customer_services`
--
ALTER TABLE `customer_services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1025;

--
-- AUTO_INCREMENT cho bảng `customer_services_backup`
--
ALTER TABLE `customer_services_backup`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `family_accounts`
--
ALTER TABLE `family_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `family_members`
--
ALTER TABLE `family_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT cho bảng `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `lead_activities`
--
ALTER TABLE `lead_activities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lead_care_schedules`
--
ALTER TABLE `lead_care_schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `message_campaigns`
--
ALTER TABLE `message_campaigns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `message_logs`
--
ALTER TABLE `message_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT cho bảng `potential_suppliers`
--
ALTER TABLE `potential_suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `potential_supplier_services`
--
ALTER TABLE `potential_supplier_services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `profits`
--
ALTER TABLE `profits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=572;

--
-- AUTO_INCREMENT cho bảng `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `service_categories_backup`
--
ALTER TABLE `service_categories_backup`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `service_packages`
--
ALTER TABLE `service_packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT cho bảng `service_packages_backup`
--
ALTER TABLE `service_packages_backup`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `shared_account_logout_logs`
--
ALTER TABLE `shared_account_logout_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `supplier_products`
--
ALTER TABLE `supplier_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `target_groups`
--
ALTER TABLE `target_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `zalo_accounts`
--
ALTER TABLE `zalo_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `collaborator_services`
--
ALTER TABLE `collaborator_services`
  ADD CONSTRAINT `collaborator_services_collaborator_id_foreign` FOREIGN KEY (`collaborator_id`) REFERENCES `collaborators` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `collaborator_service_accounts`
--
ALTER TABLE `collaborator_service_accounts`
  ADD CONSTRAINT `collaborator_service_accounts_collaborator_service_id_foreign` FOREIGN KEY (`collaborator_service_id`) REFERENCES `collaborator_services` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `conversion_logs`
--
ALTER TABLE `conversion_logs`
  ADD CONSTRAINT `conversion_logs_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `message_campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversion_logs_group_member_id_foreign` FOREIGN KEY (`group_member_id`) REFERENCES `group_members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversion_logs_message_log_id_foreign` FOREIGN KEY (`message_log_id`) REFERENCES `message_logs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `conversion_logs_own_group_id_foreign` FOREIGN KEY (`own_group_id`) REFERENCES `target_groups` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `customer_services`
--
ALTER TABLE `customer_services`
  ADD CONSTRAINT `customer_services_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_services_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_services_family_account_id_foreign` FOREIGN KEY (`family_account_id`) REFERENCES `family_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_services_service_package_id_foreign` FOREIGN KEY (`service_package_id`) REFERENCES `service_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_services_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_services_supplier_service_id_foreign` FOREIGN KEY (`supplier_service_id`) REFERENCES `supplier_products` (`id`) ON DELETE SET NULL;

--
-- Ràng buộc cho bảng `family_accounts`
--
ALTER TABLE `family_accounts`
  ADD CONSTRAINT `family_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `family_accounts_managed_by_foreign` FOREIGN KEY (`managed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `family_accounts_service_package_id_foreign` FOREIGN KEY (`service_package_id`) REFERENCES `service_packages` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `family_members`
--
ALTER TABLE `family_members`
  ADD CONSTRAINT `family_members_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `family_members_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_members_family_account_id_foreign` FOREIGN KEY (`family_account_id`) REFERENCES `family_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_members_removed_by_foreign` FOREIGN KEY (`removed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Ràng buộc cho bảng `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_target_group_id_foreign` FOREIGN KEY (`target_group_id`) REFERENCES `target_groups` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_service_package_id_foreign` FOREIGN KEY (`service_package_id`) REFERENCES `service_packages` (`id`) ON DELETE SET NULL;

--
-- Ràng buộc cho bảng `lead_activities`
--
ALTER TABLE `lead_activities`
  ADD CONSTRAINT `lead_activities_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `lead_care_schedules`
--
ALTER TABLE `lead_care_schedules`
  ADD CONSTRAINT `lead_care_schedules_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `message_campaigns`
--
ALTER TABLE `message_campaigns`
  ADD CONSTRAINT `message_campaigns_own_group_id_foreign` FOREIGN KEY (`own_group_id`) REFERENCES `target_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `message_campaigns_target_group_id_foreign` FOREIGN KEY (`target_group_id`) REFERENCES `target_groups` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `message_logs`
--
ALTER TABLE `message_logs`
  ADD CONSTRAINT `message_logs_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `message_campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_logs_group_member_id_foreign` FOREIGN KEY (`group_member_id`) REFERENCES `group_members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_logs_zalo_account_id_foreign` FOREIGN KEY (`zalo_account_id`) REFERENCES `zalo_accounts` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `potential_supplier_services`
--
ALTER TABLE `potential_supplier_services`
  ADD CONSTRAINT `potential_supplier_services_potential_supplier_id_foreign` FOREIGN KEY (`potential_supplier_id`) REFERENCES `potential_suppliers` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `profits`
--
ALTER TABLE `profits`
  ADD CONSTRAINT `profits_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `profits_customer_service_id_foreign` FOREIGN KEY (`customer_service_id`) REFERENCES `customer_services` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `service_packages`
--
ALTER TABLE `service_packages`
  ADD CONSTRAINT `service_packages_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD CONSTRAINT `supplier_products_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

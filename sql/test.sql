-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 18 2017 г., 10:29
-- Версия сервера: 10.1.21-MariaDB
-- Версия PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `component`
--

CREATE TABLE `component` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `component` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `href` varchar(255) NOT NULL,
  `src` varchar(255) DEFAULT NULL,
  `rel` varchar(255) DEFAULT NULL,
  `media` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `component`
--

INSERT INTO `component` (`id`, `component`, `type`, `title`, `link`, `href`, `src`, `rel`, `media`) VALUES
(1, 'css', 'text/css', 'Bootstrap', 'https://v4-alpha.getbootstrap.com/components/navbar/', 'css/bootstrap.css', NULL, 'stylesheet', 'all'),
(2, 'css', 'text/css', 'Font Awesome', 'http://fontawesome.io/icons/', 'css/font-awesome.css', NULL, 'stylesheet', 'all'),
(3, 'js', '', 'JQuery', 'http://jquery.com/', '', 'js/jquery-3.1.1.js', NULL, ''),
(4, 'js', '', 'Tether', 'http://tether.io/', '', 'js/tether.min.js', NULL, ''),
(5, 'js', '', 'Bootstrap', 'https://v4-alpha.getbootstrap.com/components/navbar/', '', 'js/bootstrap.js', NULL, '');

-- --------------------------------------------------------

--
-- Структура таблицы `contact`
--

CREATE TABLE `contact` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `contact`
--

INSERT INTO `contact` (`id`, `title`, `value`) VALUES
(1, 'Name', 'NineBits LTD'),
(2, 'Address', 'Bila Tserkva'),
(3, 'E-mail', 'ninebits@meta.ua'),
(4, 'WWW', 'intellectual.systems');

-- --------------------------------------------------------

--
-- Структура таблицы `menu`
--

CREATE TABLE `menu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `href` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `menu`
--

INSERT INTO `menu` (`id`, `key`, `title`, `text`, `icon`, `href`, `class`) VALUES
(1, 'home', 'Main page - Home', 'Home', 'fa-home', '', 'nav-item'),
(2, 'projects', 'Projects', 'Projects', 'fa-file-code-o', 'projects/', 'nav-item'),
(3, 'contacts', 'Contacts', 'Contacts', 'fa-phone', 'contacts/', 'nav-item'),
(4, 'faq', 'List of questions and answers', 'FAQ', 'fa-question', 'faq/', 'nav-item');

-- --------------------------------------------------------

--
-- Структура таблицы `project`
--

CREATE TABLE `project` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `demo` varchar(255) DEFAULT NULL,
  `real` tinyint(1) NOT NULL,
  `download` varchar(255) DEFAULT NULL,
  `year` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `project`
--

INSERT INTO `project` (`id`, `type`, `title`, `description`, `demo`, `real`, `download`, `year`) VALUES
(1, 'Complete Projects', 'ISS Ukraine', 'Intelegent security systems', 'http://www.isscctv.com.ua/', 1, NULL, 2012),
(2, 'Complete Projects', 'Lompier', 'Interior design bureau', 'http://lompier.com/', 1, NULL, 2014),
(3, 'Complete Projects', 'Worklay', 'Automation event and wedding business', 'http://worklay.biz/', 1, NULL, 2015),
(4, 'Current projects', 'BMU', 'Construction and assembly department', 'http://bmu.intellectual.systems/', 0, NULL, 2016),
(5, 'Current projects', 'Self Portfolio', 'Self test portfolio site', 'http://portfolio.intellectual.systems/', 0, NULL, 2017);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_role_id` int(20) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pwd_hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `user_role_id`, `firstname`, `lastname`, `email`, `pwd_hash`) VALUES
(2, 0, 'Djon', '', '', ''),
(3, 1, 'Sara', '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `user2`
--

CREATE TABLE `user2` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstname` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `user_role`
--

CREATE TABLE `user_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `right` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_role`
--

INSERT INTO `user_role` (`id`, `name`, `right`) VALUES
(1, 'Admin', 777);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `component`
--
ALTER TABLE `component`
  ADD PRIMARY KEY (`id`),
  ADD KEY `component` (`component`);

--
-- Индексы таблицы `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`);

--
-- Индексы таблицы `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `key` (`key`);

--
-- Индексы таблицы `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_role_id` (`user_role_id`);

--
-- Индексы таблицы `user2`
--
ALTER TABLE `user2`
  ADD UNIQUE KEY `id` (`id`);

--
-- Индексы таблицы `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `component`
--
ALTER TABLE `component`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `contact`
--
ALTER TABLE `contact`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `menu`
--
ALTER TABLE `menu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `project`
--
ALTER TABLE `project`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `user2`
--
ALTER TABLE `user2`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Окт 03 2019 г., 19:37
-- Версия сервера: 10.1.31-MariaDB
-- Версия PHP: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `hotel`
--

-- --------------------------------------------------------

--
-- Структура таблицы `booking`
--

CREATE TABLE `booking` (
  `id_booking` int(11) NOT NULL,
  `booking_name` datetime NOT NULL,
  `id_customers` int(11) NOT NULL,
  `booking_number_guests` int(11) NOT NULL,
  `booking_start_date` date NOT NULL,
  `booking_end_date` date NOT NULL,
  `id_room` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `booking`
--

INSERT INTO `booking` (`id_booking`, `booking_name`, `id_customers`, `booking_number_guests`, `booking_start_date`, `booking_end_date`, `id_room`) VALUES
(5, '0000-00-00 00:00:00', 1, 4, '2019-09-17', '2019-09-21', 1),
(7, '0000-00-00 00:00:00', 2, 3, '2019-09-16', '2019-09-18', 2),
(8, '2019-09-22 08:58:15', 1, 1, '2019-09-22', '2019-09-25', 1),
(10, '2019-09-22 18:12:02', 3, 1, '2019-09-22', '2019-09-27', 3),
(11, '2019-09-22 18:22:32', 1, 1, '2019-09-22', '2019-09-25', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `customers`
--

CREATE TABLE `customers` (
  `id_customers` int(11) NOT NULL,
  `customers_name` varchar(255) NOT NULL,
  `customers_surname` varchar(255) NOT NULL,
  `customers_mobile` varchar(15) NOT NULL,
  `customers_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `customers`
--

INSERT INTO `customers` (`id_customers`, `customers_name`, `customers_surname`, `customers_mobile`, `customers_email`) VALUES
(1, 'Михаил', 'Шмонов', '89265506815', 'shmihmih@yandex.ru'),
(2, 'Никита', 'Иванов', '89366213434', 'shmihmih@gmail.com'),
(3, 'Арина', 'Моисеева', '89161103105', 'shmihmih@yandex.ru');

-- --------------------------------------------------------

--
-- Структура таблицы `facilities`
--

CREATE TABLE `facilities` (
  `id_facilities` int(11) NOT NULL,
  `id_room` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `facilities_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `facilities`
--

INSERT INTO `facilities` (`id_facilities`, `id_room`, `id_object`, `facilities_quantity`) VALUES
(3, 1, 2, 1),
(7, 1, 1, 1),
(8, 3, 2, 2),
(9, 3, 3, 2),
(10, 2, 1, 2),
(11, 3, 1, 1),
(12, 4, 1, 1),
(13, 5, 1, 1),
(14, 6, 1, 1),
(15, 4, 3, 1),
(16, 6, 6, 0),
(17, 5, 6, 0),
(18, 5, 4, 0),
(19, 4, 4, 0),
(20, 6, 4, 0),
(21, 3, 4, 1),
(22, 5, 3, 1),
(23, 6, 3, 1),
(24, 4, 2, 1),
(25, 5, 2, 1),
(26, 6, 2, 1),
(27, 4, 6, 1),
(28, 6, 5, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `object`
--

CREATE TABLE `object` (
  `id_object` int(11) NOT NULL,
  `object_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `object`
--

INSERT INTO `object` (`id_object`, `object_name`) VALUES
(1, 'Кровать'),
(2, 'Телевизор'),
(3, 'Диван'),
(4, 'Ванная'),
(5, 'Кухня'),
(6, 'Wifi');

-- --------------------------------------------------------

--
-- Структура таблицы `payment`
--

CREATE TABLE `payment` (
  `id_payment` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `id_customers` int(11) NOT NULL,
  `id_paymethod` int(11) NOT NULL,
  `payment_sum` int(11) NOT NULL,
  `payment_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `payment`
--

INSERT INTO `payment` (`id_payment`, `id_booking`, `id_customers`, `id_paymethod`, `payment_sum`, `payment_status`) VALUES
(1, 8, 1, 2, 1000, 1),
(2, 8, 1, 1, 3000, 0),
(3, 8, 2, 2, 1200, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `paymethod`
--

CREATE TABLE `paymethod` (
  `id_paymethod` int(11) NOT NULL,
  `paymethod_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `paymethod`
--

INSERT INTO `paymethod` (`id_paymethod`, `paymethod_name`) VALUES
(1, 'Наличные'),
(2, 'Карта');

-- --------------------------------------------------------

--
-- Структура таблицы `price`
--

CREATE TABLE `price` (
  `id_price` int(11) NOT NULL,
  `price_per_night` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `price`
--

INSERT INTO `price` (`id_price`, `price_per_night`) VALUES
(1, 5000),
(2, 7000),
(3, 12000),
(4, 10000);

-- --------------------------------------------------------

--
-- Структура таблицы `room`
--

CREATE TABLE `room` (
  `id_room` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `id_type` int(11) NOT NULL,
  `id_price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `room`
--

INSERT INTO `room` (`id_room`, `room_name`, `id_type`, `id_price`) VALUES
(1, 'номер №1', 1, 2),
(2, 'номер №2', 1, 1),
(3, 'номер №3', 2, 2),
(4, 'номер №4', 2, 4),
(5, 'номер №5', 3, 4),
(6, 'номер №6', 3, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `type`
--

CREATE TABLE `type` (
  `id_type` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `type`
--

INSERT INTO `type` (`id_type`, `type_name`) VALUES
(1, 'обычный номер'),
(2, 'премиум номер'),
(3, 'люкс номер');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `id_customer` (`id_customers`),
  ADD KEY `id_room` (`id_room`);

--
-- Индексы таблицы `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id_customers`);

--
-- Индексы таблицы `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id_facilities`),
  ADD KEY `id_room` (`id_room`),
  ADD KEY `id_object` (`id_object`);

--
-- Индексы таблицы `object`
--
ALTER TABLE `object`
  ADD PRIMARY KEY (`id_object`);

--
-- Индексы таблицы `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id_payment`),
  ADD KEY `id_paymethod` (`id_paymethod`),
  ADD KEY `id_customers` (`id_customers`),
  ADD KEY `id_booking` (`id_booking`);

--
-- Индексы таблицы `paymethod`
--
ALTER TABLE `paymethod`
  ADD PRIMARY KEY (`id_paymethod`);

--
-- Индексы таблицы `price`
--
ALTER TABLE `price`
  ADD PRIMARY KEY (`id_price`);

--
-- Индексы таблицы `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id_room`),
  ADD KEY `id_type` (`id_type`),
  ADD KEY `id_price` (`id_price`);

--
-- Индексы таблицы `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`id_type`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `customers`
--
ALTER TABLE `customers`
  MODIFY `id_customers` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id_facilities` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT для таблицы `object`
--
ALTER TABLE `object`
  MODIFY `id_object` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `payment`
--
ALTER TABLE `payment`
  MODIFY `id_payment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `paymethod`
--
ALTER TABLE `paymethod`
  MODIFY `id_paymethod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `price`
--
ALTER TABLE `price`
  MODIFY `id_price` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `room`
--
ALTER TABLE `room`
  MODIFY `id_room` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `type`
--
ALTER TABLE `type`
  MODIFY `id_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_customers`) REFERENCES `customers` (`id_customers`),
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`id_room`) REFERENCES `room` (`id_room`);

--
-- Ограничения внешнего ключа таблицы `facilities`
--
ALTER TABLE `facilities`
  ADD CONSTRAINT `facilities_ibfk_1` FOREIGN KEY (`id_room`) REFERENCES `room` (`id_room`),
  ADD CONSTRAINT `facilities_ibfk_2` FOREIGN KEY (`id_object`) REFERENCES `object` (`id_object`);

--
-- Ограничения внешнего ключа таблицы `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`id_paymethod`) REFERENCES `paymethod` (`id_paymethod`),
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`id_customers`) REFERENCES `customers` (`id_customers`),
  ADD CONSTRAINT `payment_ibfk_3` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`Id_booking`);

--
-- Ограничения внешнего ключа таблицы `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`id_type`) REFERENCES `type` (`id_type`),
  ADD CONSTRAINT `room_ibfk_2` FOREIGN KEY (`id_price`) REFERENCES `price` (`id_price`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

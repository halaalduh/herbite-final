-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 13, 2026 at 08:41 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `herbits_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `blockeduser`
--

CREATE TABLE `blockeduser` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `emailAddress` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blockeduser`
--

INSERT INTO `blockeduser` (`id`, `firstName`, `lastName`, `emailAddress`) VALUES
(1, 'test', 'blocked', 'test@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `userID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `userID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `photoFileName` varchar(255) DEFAULT NULL,
  `videoFilePath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`id`, `userID`, `categoryID`, `name`, `description`, `photoFileName`, `videoFilePath`) VALUES
(1, 5, 1, 'Berry Yogurt Glow Bowl', 'recipe', 'berry yogurt.jpeg', NULL),
(2, 5, 2, 'Avocado Glow Toast', 'recipe', 'avocado toast.jpeg', NULL),
(3, 5, 3, 'Banana Oat Cookies', 'recipe', 'banana oat cookies.jpeg', NULL),
(4, 4, 2, 'Skin Boost Nut Mix', 'recipe', 'nut mix.jpeg', NULL),
(5, 4, 1, 'Iron Boost Spinach Salad', 'recipe', 'spinach salad.jpeg', NULL),
(6, 4, 3, 'Apple Cinnamon Snack Bites', 'recipe', 'apple cinnamon.jpeg', NULL),
(7, 5, 2, 'Date Cocoa Energy Bites', 'recipe', 'date bites.jpeg', NULL),
(8, 4, 3, 'Peanut Butter Energy Balls', 'recipe', 'peanut butter balls.jpeg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `recipecategory`
--

CREATE TABLE `recipecategory` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipecategory`
--

INSERT INTO `recipecategory` (`id`, `categoryName`) VALUES
(1, 'Inner Glow'),
(2, 'Outer Glow'),
(3, 'Healthy Snacks');

-- --------------------------------------------------------

--
-- Table structure for table `recipeingredient`
--

CREATE TABLE `recipeingredient` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL,
  `ingredientName` varchar(150) NOT NULL,
  `quantity` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipeingredient`
--

INSERT INTO `recipeingredient` (`id`, `recipeID`, `ingredientName`, `quantity`) VALUES
(1, 1, 'Greek yoghurt', '1 cup'),
(2, 1, 'Mixed berries', '1/2 cup'),
(3, 1, 'Chia seeds', '1 tsp'),
(4, 1, 'Honey', '1 tsp'),
(5, 2, 'Whole grain toast', '1 slice'),
(6, 2, 'Avocado', '1/2'),
(7, 2, 'Olive oil', 'few drops'),
(8, 2, 'Chili flakes', 'pinch'),
(9, 3, 'Banana', '1 mashed'),
(10, 3, 'Oats', '1 cup'),
(11, 3, 'Honey', '1 tbsp'),
(12, 3, 'Dark chocolate chips', '1 tbsp'),
(13, 4, 'Mixed nuts', '1 cup'),
(14, 4, 'Pumpkin seeds', '2 tbsp'),
(15, 4, 'Sunflower seeds', '2 tbsp'),
(16, 5, 'Spinach', '2 cups'),
(17, 5, 'Pomegranate seeds', '1/4 cup'),
(18, 5, 'Feta cheese', '2 tbsp'),
(19, 6, 'Apple', '1 diced'),
(20, 6, 'Cinnamon', '1 tsp'),
(21, 6, 'Oats', '1/2 cup'),
(22, 7, 'Dates', '8 pieces'),
(23, 7, 'Cocoa powder', '1 tbsp'),
(24, 7, 'Oats', '1/2 cup'),
(25, 8, 'Peanut butter', '1/2 cup'),
(26, 8, 'Oats', '1 cup'),
(27, 8, 'Honey', '1 tbsp');

-- --------------------------------------------------------

--
-- Table structure for table `recipeinstruction`
--

CREATE TABLE `recipeinstruction` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL,
  `stepNumber` int(11) NOT NULL,
  `instructionText` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipeinstruction`
--

INSERT INTO `recipeinstruction` (`id`, `recipeID`, `stepNumber`, `instructionText`) VALUES
(1, 1, 1, 'Add yoghurt to a bowl.'),
(2, 1, 2, 'Top with mixed berries.'),
(3, 1, 3, 'Sprinkle chia seeds.'),
(4, 1, 4, 'Drizzle honey and serve.'),
(5, 2, 1, 'Toast the bread.'),
(6, 2, 2, 'Mash the avocado.'),
(7, 2, 3, 'Spread avocado and season before serving.'),
(8, 3, 1, 'Mix all ingredients in a bowl.'),
(9, 3, 2, 'Shape the mixture into cookies.'),
(10, 3, 3, 'Bake for 12 minutes.'),
(11, 4, 1, 'Mix all nuts and seeds together.'),
(12, 4, 2, 'Serve immediately or store in a sealed jar.'),
(13, 5, 1, 'Wash the spinach well.'),
(14, 5, 2, 'Mix spinach with the remaining ingredients.'),
(15, 5, 3, 'Serve fresh.'),
(16, 6, 1, 'Mix diced apple with oats and cinnamon.'),
(17, 6, 2, 'Shape into bite-sized pieces.'),
(18, 6, 3, 'Chill before serving.'),
(19, 7, 1, 'Blend dates, cocoa powder, and oats.'),
(20, 7, 2, 'Shape into balls.'),
(21, 7, 3, 'Chill for 15 minutes.'),
(22, 8, 1, 'Mix peanut butter, oats, and honey.'),
(23, 8, 2, 'Roll into small balls.'),
(24, 8, 3, 'Refrigerate until firm.');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `recipeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `userType` enum('user','admin') NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photoFileName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userType`, `firstName`, `lastName`, `emailAddress`, `password`, `photoFileName`) VALUES
(1, 'admin', 'Admin', 'User', 'admin@herbite.com', '$2y$10$3ukJu3ykLTx92hPA3o63e.jGGC9wfm8DlEyYniZOXxFJ5GzJlujbS', 'admin.png'),
(3, 'user', 'Sara', 'Ahmed', 'sara@test.com', '$2y$10$wdRXOY/zWCICx0iqU/62L.OISQS46v/xi9INeEaY4VbzcrYs5Ecoa', 'default.png'),
(4, 'user', 'Noor', 'Ali', 'noor@test.com', '$2y$10$8ijqm7pPaxfsfQXRaadPiuxmAlWR7r5rk5RdHqzLtwTx.8Jn4syFu', 'blonde-girl.png'),
(5, 'user', 'Hanan', 'Alharbi', 'hanan@test.com', '$2y$10$8ijqm7pPaxfsfQXRaadPiuxmAlWR7r5rk5RdHqzLtwTx.8Jn4syFu', 'curly-girl.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`userID`,`recipeID`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipeingredient`
--
ALTER TABLE `recipeingredient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipeinstruction`
--
ALTER TABLE `recipeinstruction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
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
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `recipeingredient`
--
ALTER TABLE `recipeingredient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `recipeinstruction`
--
ALTER TABLE `recipeinstruction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

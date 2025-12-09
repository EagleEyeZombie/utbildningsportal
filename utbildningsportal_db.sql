-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 05, 2025 at 08:15 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `utbildningsportal`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `a_id` int NOT NULL,
  `a_name` varchar(255) NOT NULL,
  `a_description` text NOT NULL,
  `a_icon` varchar(255) NOT NULL COMMENT 'T.ex. fa-star, fa-trophy',
  `a_xp_required` int NOT NULL DEFAULT '0' COMMENT 'XP som krävs för att få denna'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`a_id`, `a_name`, `a_description`, `a_icon`, `a_xp_required`) VALUES
(1, 'Nykomling', 'Loggat in på ditt konto.', 'bi-person-check-fill', 0),
(2, 'Första Steget', 'Klara din första uppgift.', 'bi-star-fill', 10),
(3, 'Bokmal', 'Samla 1000 XP.', 'bi-book-half', 1000),
(4, 'Mästare', 'Nå Level 10.', 'bi-trophy-fill', 5000),
(5, 'Legend', 'Nå Level 15.', 'bi-crown', 15000),
(6, 'Quizmästaren', 'Klara nivå 10 i Flervalsfrågor.', 'bi-list-check', 90001),
(7, 'Ordningsvakten', 'Klara nivå 10 i Sortering.', 'bi-sort-down', 90002),
(8, 'Pusselbiten', 'Klara nivå 10 i Para ihop.', 'bi-puzzle-fill', 90003),
(9, 'Sanningssägaren', 'Klara nivå 10 i Sant/Falskt.', 'bi-check-circle-fill', 90004),
(10, 'Ordgeniet', 'Klara nivå 10 i Textluckor.', 'bi-input-cursor-text', 90005),
(11, 'Drakryttaren', 'Klara nivå 10 i Fantasy.', 'bi-magic', 90006),
(12, 'Astronauten', 'Klara nivå 10 i Sci-Fi.', 'bi-rocket-takeoff-fill', 90007),
(13, 'Detektiven', 'Klara nivå 10 i Deckare.', 'bi-search', 90008),
(14, 'Spökjägaren', 'Klara nivå 10 i Skräck.', 'bi-emoji-dizzy', 90009),
(15, 'Professorn', 'Klara nivå 10 i Fakta.', 'bi-mortarboard-fill', 90010),
(16, 'Nyfiken Start', 'Klara 5 uppdrag på Nivå 1.', 'bi-1-square', 90011),
(17, 'Uppvärmd', 'Klara 10 uppdrag på Nivå 1.', 'bi-1-square-fill', 90012),
(18, 'På God Väg', 'Klara 5 uppdrag på Nivå 5.', 'bi-5-square', 90013),
(19, 'Erfaren', 'Klara 10 uppdrag på Nivå 5.', 'bi-5-square-fill', 90014),
(20, 'Eliten', 'Klara 5 uppdrag på Nivå 10.', 'bi-10-square', 90015),
(21, 'Omöjlig', 'Klara 10 uppdrag på Nivå 10.', 'bi-10-square-fill', 90016);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `c_id` int NOT NULL,
  `c_name` varchar(255) NOT NULL,
  `c_progress_speed_fk` int DEFAULT NULL,
  `c_teacher_fk` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`c_id`, `c_name`, `c_progress_speed_fk`, `c_teacher_fk`) VALUES
(1, 'Årskurs 7A', NULL, 1),
(2, 'Årskurs 8B', NULL, 3),
(3, 'Årskurs 9C', NULL, 1),
(4, 'Årskurs 7B', NULL, 1),
(5, 'Årskurs 8A', NULL, 3),
(6, 'Årskurs 9A', NULL, 1),
(7, 'TestKlass 9B', NULL, 1),
(8, 'Raseborgs IT', NULL, 1),
(9, 'Familjen Kjellberg och Paukku', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `g_id` int NOT NULL,
  `g_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`g_id`, `g_name`) VALUES
(1, 'Fantasy'),
(2, 'Sci-Fi'),
(3, 'Deckare'),
(4, 'Skräck'),
(5, 'Fakta');

-- --------------------------------------------------------

--
-- Table structure for table `level_config`
--

CREATE TABLE `level_config` (
  `lc_level` int NOT NULL,
  `lc_xp_required` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `level_config`
--

INSERT INTO `level_config` (`lc_level`, `lc_xp_required`) VALUES
(1, 0),
(2, 100),
(3, 300),
(4, 600),
(5, 1000),
(6, 1500),
(7, 2100),
(8, 2800),
(9, 3600),
(10, 5000),
(11, 6500),
(12, 8200),
(13, 10000),
(14, 12000),
(15, 15000);

-- --------------------------------------------------------

--
-- Table structure for table `progress_speeds`
--

CREATE TABLE `progress_speeds` (
  `ps_id` int NOT NULL,
  `ps_name` varchar(255) NOT NULL,
  `ps_multiplier` float NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress_speeds`
--

INSERT INTO `progress_speeds` (`ps_id`, `ps_name`, `ps_multiplier`) VALUES
(1, 'Normal', 1),
(2, 'Snabb (1.5x)', 1.5),
(3, 'Supersnabb (2x)', 2),
(4, 'Turbo (4x)', 4);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `r_id` int NOT NULL,
  `r_name` varchar(255) NOT NULL,
  `r_level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`r_id`, `r_name`, `r_level`) VALUES
(1, 'Elev', 1),
(2, 'Lärare', 5),
(3, 'Admin', 10);

-- --------------------------------------------------------

--
-- Table structure for table `student_achievements`
--

CREATE TABLE `student_achievements` (
  `sa_id` int NOT NULL,
  `sa_student_fk` int NOT NULL,
  `sa_achievement_fk` int NOT NULL,
  `sa_date_earned` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_achievements`
--

INSERT INTO `student_achievements` (`sa_id`, `sa_student_fk`, `sa_achievement_fk`, `sa_date_earned`) VALUES
(1, 58, 1, '2025-11-26 23:16:30'),
(2, 58, 2, '2025-11-26 23:58:59'),
(3, 1, 1, '2025-11-27 10:54:01'),
(4, 1, 2, '2025-11-27 10:55:12'),
(5, 61, 1, '2025-11-28 21:20:09'),
(6, 61, 2, '2025-11-28 21:22:48'),
(7, 61, 3, '2025-11-28 21:30:00'),
(8, 61, 10, '2025-11-28 21:30:31'),
(9, 61, 14, '2025-11-28 21:30:31'),
(10, 61, 4, '2025-11-28 21:35:10'),
(11, 61, 7, '2025-11-28 21:36:08'),
(12, 1, 3, '2025-12-03 21:13:32');

-- --------------------------------------------------------

--
-- Table structure for table `student_tasks`
--

CREATE TABLE `student_tasks` (
  `st_id` int NOT NULL,
  `st_s_id_fk` int NOT NULL COMMENT 'Student ID (user)',
  `st_t_id_fk` int NOT NULL COMMENT 'Task ID',
  `st_completed` tinyint(1) NOT NULL DEFAULT '0',
  `st_score` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_tasks`
--

INSERT INTO `student_tasks` (`st_id`, `st_s_id_fk`, `st_t_id_fk`, `st_completed`, `st_score`) VALUES
(1, 58, 91, 1, 100),
(2, 1, 91, 1, 100),
(3, 1, 92, 1, 100),
(4, 1, 93, 1, 100),
(5, 61, 91, 1, 100),
(6, 61, 92, 1, 100),
(7, 61, 93, 1, 100),
(8, 61, 303, 1, 100),
(9, 61, 304, 1, 100),
(10, 61, 305, 1, 100),
(11, 61, 306, 1, 100),
(12, 61, 307, 1, 100),
(13, 61, 308, 1, 100),
(14, 61, 309, 1, 100),
(15, 61, 310, 1, 100),
(16, 61, 311, 1, 100),
(17, 61, 312, 1, 100),
(18, 61, 94, 1, 100),
(19, 61, 95, 1, 100),
(20, 61, 96, 1, 100),
(21, 61, 97, 1, 100),
(22, 61, 98, 1, 100),
(23, 61, 99, 1, 100),
(24, 61, 100, 1, 100),
(25, 58, 92, 1, 100),
(26, 58, 93, 1, 100),
(27, 58, 94, 1, 100),
(28, 58, 95, 1, 100),
(29, 1, 113, 1, 100),
(30, 1, 273, 1, 100),
(31, 1, 274, 1, 100),
(32, 1, 275, 1, 100),
(33, 1, 276, 1, 100),
(34, 1, 277, 1, 100),
(35, 1, 278, 1, 100),
(36, 1, 94, 1, 100);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `t_id` int NOT NULL,
  `t_name` varchar(255) NOT NULL,
  `t_type_fk` int NOT NULL,
  `t_genre_fk` int DEFAULT NULL COMMENT 'Genre ID',
  `t_teacher_fk` int DEFAULT NULL,
  `t_class_fk` int DEFAULT NULL COMMENT 'Koppling till klass',
  `t_text` text,
  `t_questions` text COMMENT 'Kan vara JSON-data med frågor och svar',
  `t_level_fk` int NOT NULL,
  `t_xp` int NOT NULL DEFAULT '10' COMMENT 'XP som uppgiften ger',
  `t_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`t_id`, `t_name`, `t_type_fk`, `t_genre_fk`, `t_teacher_fk`, `t_class_fk`, `t_text`, `t_questions`, `t_level_fk`, `t_xp`, `t_created`) VALUES
(41, 'Kapitel 1: Mörkret faller', 1, 1, 1, NULL, 'Byn Solsång har alltid levt i ljus, skyddad av den magiska Solstenen i tornet. Men en natt stjäl den ondskefulla Skuggmagikern stenen. Byn täcks i ett evigt, kallt mörker. Den unga Elara bestämmer sig för att hämta tillbaka stenen. Hon hittar en gammal karta i byns arkiv som visar vägen till Skuggmagikerns fäste.', '[{\"q\":\"Vad skyddade byn Solsång?\",\"a\":\"Solstenen\",\"w1\":\"En armé\",\"w2\":\"En mur\",\"w3\":\"En flod\"},{\"q\":\"Vem stal stenen?\",\"a\":\"Skuggmagikern\",\"w1\":\"En drake\",\"w2\":\"En tjuv\",\"w3\":\"\"},{\"q\":\"Vad hittade Elara i arkivet?\",\"a\":\"En gammal karta\",\"w1\":\"Ett svärd\",\"w2\":\"En sköld\",\"w3\":\"\"}]', 4, 10, '2025-12-03 19:33:46'),
(42, 'Kapitel 2: Resans början', 1, 1, 3, NULL, 'Elara packar sin väska. Hon tar med sig ett rep, lite torkat kött och sin pappas gamla dolk. Kartan visar att hon först måste korsa den viskande skogen. Skogen sägs vara full av fällor och märkliga ljud. Elara är rädd, men tanken på sitt hem ger henne mod.', '[{\"q\":\"Vad packade Elara?\",\"a\":\"Rep, kött och en dolk\",\"w1\":\"Bara mat\",\"w2\":\"Ett stort svärd\",\"w3\":\"Ingenting\"},{\"q\":\"Vilken plats måste hon korsa först?\",\"a\":\"Den viskande skogen\",\"w1\":\"Det stora havet\",\"w2\":\"Tysta bergen\",\"w3\":\"\"},{\"q\":\"Vad gav Elara mod?\",\"a\":\"Tanken på sitt hem\",\"w1\":\"Sin magiska styrka\",\"w2\":\"Hon var inte rädd\",\"w3\":\"\"}]', 5, 20, '2025-12-03 19:33:46'),
(43, 'Kapitel 3: Goblinskogen', 1, 1, 1, NULL, 'Mitt i skogen stöter Elara på två Goblins som vaktar en bro. De vägrar låta henne passera. \\\"Ge oss all din mat!\\\" piper den ena. Elara ser en bikupa som hänger från en gren ovanför dem. Hon kastar en sten på bikupan. Arga bin svärmar ut och jagar iväg Goblins-vakterna. Elara springer snabbt över bron.', '[{\"q\":\"Vem vaktade bron?\",\"a\":\"Två Goblins\",\"w1\":\"En jätte\",\"w2\":\"Ett troll\",\"w3\":\"\"},{\"q\":\"Vad ville vakterna ha?\",\"a\":\"Hennes mat\",\"w1\":\"Hennes dolk\",\"w2\":\"Hennes karta\",\"w3\":\"\"},{\"q\":\"Hur skrämde Elara bort dem?\",\"a\":\"Hon kastade en sten på en bikupa\",\"w1\":\"Hon slogs med sin dolk\",\"w2\":\"Hon skrek högt\",\"w3\":\"\"}]', 6, 30, '2025-12-03 19:33:46'),
(44, 'Kapitel 4: Den Viskande Grottan', 1, 1, 3, NULL, 'Efter bron leder kartan Elara till en mörk grotta. Inuti hör hon viskningar. En röst ekar: \\\"Jag har städer utan hus, skogar utan träd och vatten utan fisk. Vad är jag?\\\" Elara tänker länge. Hon minns kartan hon håller i. \\\"Du är en karta!\\\" ropar hon. Viskningarna tystnar och en dörr öppnas.', '[{\"q\":\"Vart ledde kartan henne efter bron?\",\"a\":\"Till en mörk grotta\",\"w1\":\"Till ett slott\",\"w2\":\"Till en sjö\",\"w3\":\"\"},{\"q\":\"Vad var gåtans svar?\",\"a\":\"En karta\",\"w1\":\"En spegel\",\"w2\":\"En bok\",\"w3\":\"Ett berg\"},{\"q\":\"Vad hände när hon svarade rätt?\",\"a\":\"En dörr öppnades\",\"w1\":\"Hon fick en skatt\",\"w2\":\"Viskningarna blev högre\",\"w3\":\"\"}]', 7, 40, '2025-12-03 19:33:46'),
(45, 'Kapitel 5: Den Griniga Erövraren', 1, 1, 1, NULL, 'I grottan möter Elara en gammal man vid namn Kael. Han är en före detta erövrare som tröttnat på att slåss. \\\"En till äventyrare?\\\" muttrar han. Elara förklarar sitt uppdrag. Kael ser hennes mod och bestämmer sig för att hjälpa. \\\"Skuggmagikern hatar ljus. Du behöver detta.\\\" Han ger henne en spegelsköld.', '[{\"q\":\"Vem mötte Elara i grottan?\",\"a\":\"Kael, en gammal erövrare\",\"w1\":\"En magiker\",\"w2\":\"En fånge\",\"w3\":\"\"},{\"q\":\"Varför var Kael grinig?\",\"a\":\"Han var trött på äventyrare\",\"w1\":\"Hon väckte honom\",\"w2\":\"Hon stal hans mat\",\"w3\":\"\"},{\"q\":\"Vad gav Kael till Elara?\",\"a\":\"En spegelsköld\",\"w1\":\"Ett svärd\",\"w2\":\"En ny karta\",\"w3\":\"\"}]', 8, 50, '2025-12-03 19:33:46'),
(46, 'Kapitel 6: Skuggsjön', 1, 1, 3, NULL, 'Grottan leder ut till en stor, mörk sjö. Det enda sättet att korsa den är med en flotte. När Elara är halvvägs dyker skugghänder upp ur vattnet och försöker dra ner henne. Hon kommer ihåg Kaels ord. Hon tar fram spegelskölden och vinklar den mot den lilla strimma ljus som finns. Ljuset träffar händerna och de drar sig tillbaka med ett fräsande.', '[{\"q\":\"Hur korsade Elara sjön?\",\"a\":\"Med en flotte\",\"w1\":\"Genom att simma\",\"w2\":\"Genom att gå runt den\",\"w3\":\"\"},{\"q\":\"Vad attackerade henne?\",\"a\":\"Skugghänder\",\"w1\":\"Hajar\",\"w2\":\"Krokodiler\",\"w3\":\"\"},{\"q\":\"Hur besegrade hon dem?\",\"a\":\"Hon reflekterade ljus med skölden\",\"w1\":\"Hon använde sin dolk\",\"w2\":\"Hon rodde snabbare\",\"w3\":\"\"}]', 9, 60, '2025-12-03 19:33:46'),
(47, 'Kapitel 7: Fästets Portar', 1, 1, 1, NULL, 'Elara når äntligen Skuggmagikerns fäste. Porten är låst. Hon ser två statyer, en av en sol och en av en måne. Ovanför står en inskription: \\\"Bara en kan öppna vägen, den andra vaktar i evighet.\\\" Elara trycker på Sol-statyn, men inget händer. Hon trycker på Mån-statyn. Porten öppnas.', '[{\"q\":\"Vad vaktade porten?\",\"a\":\"Två statyer\",\"w1\":\"Två vakter\",\"w2\":\"En vallgrav\",\"w3\":\"\"},{\"q\":\"Vilka var statyerna av?\",\"a\":\"Solen och månen\",\"w1\":\"En kung och en drottning\",\"w2\":\"En varg och en örn\",\"w3\":\"\"},{\"q\":\"Vilken staty öppnade porten?\",\"a\":\"Månen\",\"w1\":\"Solen\",\"w2\":\"Båda samtidigt\",\"w3\":\"\"}]', 10, 70, '2025-12-03 19:33:46'),
(48, 'Kapitel 8: Skuggmagikern', 1, 1, 3, NULL, 'Inne i tronrummet sitter Skuggmagikern. Han är inte en hemsk man, utan en ledsen figur omgiven av mörker. \\\"Varför tog du Solstenen?\\\" frågar Elara. Magikern förklarar att han är allergisk mot solljus, det gör att han nyser. Han ville bara ha lite lugn och ro.', '[{\"q\":\"Hur såg Skuggmagikern ut?\",\"a\":\"Ledsen\",\"w1\":\"Hemsk och ond\",\"w2\":\"Stor och stark\",\"w3\":\"\"},{\"q\":\"Varför stal han stenen?\",\"a\":\"Han var allergisk mot solljus\",\"w1\":\"Han ville ha makt\",\"w2\":\"Han ville bli rik\",\"w3\":\"\"},{\"q\":\"Vad gjorde solljuset med honom?\",\"a\":\"Det fick honom att nysa\",\"w1\":\"Det brände honom\",\"w2\":\"Det gjorde honom osynlig\",\"w3\":\"\"}]', 11, 80, '2025-12-03 19:33:46'),
(49, 'Kapitel 9: Lösningen', 1, 1, 1, NULL, 'Elara förstår. Hon vill inte slåss. Hon använder sin dolk och Kaels spegelsköld för att försiktigt dela Solstenen i två delar. \\\"Här\\\", säger hon. \\\"Du kan ta denna lilla bit och bo i din grotta där ljuset är svagt. Men byn måste få tillbaka sin halva.\\\" Magikern, som ser att den lilla biten ger ett svagt och vackert sken, går med på det.', '[{\"q\":\"Hur löste Elara problemet?\",\"a\":\"Hon delade stenen i två delar\",\"w1\":\"Hon dödade magikern\",\"w2\":\"Hon stal tillbaka hela stenen\",\"w3\":\"\"},{\"q\":\"Vilka verktyg använde hon?\",\"a\":\"Dolk och spegelsköld\",\"w1\":\"En hammare\",\"w2\":\"Magi\",\"w3\":\"\"},{\"q\":\"Gick magikern med på planen?\",\"a\":\"Ja\",\"w1\":\"Nej\",\"w2\":\"Nej, han fängslade Elara\",\"w3\":\"\"}]', 12, 90, '2025-12-03 19:33:46'),
(50, 'Kapitel 10: Ljusets Återkomst', 1, 1, 3, NULL, 'Elara återvänder till Solsång som en hjälte. Hon placerar sin halva av Solstenen i tornet. Ett mjukt, varmt ljus sprider sig över byn. Det är inte lika starkt som förut, men det räcker. Folket firar. Elara är glad att hon kunde hjälpa både sin by och den ledsna magikern.', '[{\"q\":\"Hur återvände Elara?\",\"a\":\"Som en hjälte\",\"w1\":\"Som en fånge\",\"w2\":\"Inte alls\",\"w3\":\"\"},{\"q\":\"Var ljuset från stenen starkare än förut?\",\"a\":\"Nej, det var svagare\",\"w1\":\"Ja, det var starkare\",\"w2\":\"Det var ingen skillnad\",\"w3\":\"\"},{\"q\":\"Var Elara nöjd med lösningen?\",\"a\":\"Ja, hon hjälpte båda sidor\",\"w1\":\"Nej, hon ville ha hela stenen\",\"w2\":\"Nej, hon var arg på magikern\",\"w3\":\"\"}]', 13, 100, '2025-12-03 19:33:46'),
(51, 'Kapitel 1: Smedens Fynd', 4, 1, 1, NULL, 'Den unga smedlärlingen Finn arbetade med att sortera gammalt skrot. Djupt i en hög hittade han en mörk, trasig metallskärva. När han rörde vid den kändes den iskall och han hörde en svag viskning. Han gömde skärvan i sin ficka.', '[{\"q\":\"Finn var en mästersmed.\",\"a\":\"Falskt\"},{\"q\":\"Han hittade en hel hjälm.\",\"a\":\"Falskt\"},{\"q\":\"Skärvan kändes varm.\",\"a\":\"Falskt\"},{\"q\":\"Finn gömde skärvan.\",\"a\":\"Sant\"}]', 4, 10, '2025-12-03 19:33:46'),
(52, 'Kapitel 2: Svärdets Viskningar', 4, 1, 3, NULL, 'På natten kunde Finn inte sova. Skärvan viskade till honom. Den berättade att den var en del av det legendariska Skuggsvärdet, som splittrats i fem delar. Svärdet lovade Finn stor makt om han kunde göra det helt igen.', '[{\"q\":\"Finn sov gott hela natten.\",\"a\":\"Falskt\"},{\"q\":\"Skärvan var tyst på natten.\",\"a\":\"Falskt\"},{\"q\":\"Skuggsvärdet var splittrat i tio delar.\",\"a\":\"Falskt\"},{\"q\":\"Svärdet lovade Finn makt.\",\"a\":\"Sant\"}]', 5, 20, '2025-12-03 19:33:46'),
(53, 'Kapitel 3: Isande Topparna', 4, 1, 1, NULL, 'Den första viskningen ledde Finn till de Isande Topparna. Efter en lång klättring hittade han en grotta. Inuti vaktade en is-elemental den andra skärvan. Finn, som var smed, använde sin lilla fält-ässja (en bärbar ugn) för att skapa en intensiv hetta, vilket fick is-varelsen att smälta och fly.', '[{\"q\":\"Den första resan gick till en vulkan.\",\"a\":\"Falskt\"},{\"q\":\"En drake vaktade skärvan.\",\"a\":\"Falskt\"},{\"q\":\"Finn besegrade varelsen med hetta från sin ässja.\",\"a\":\"Sant\"},{\"q\":\"Finn gav varelsen mat.\",\"a\":\"Falskt\"}]', 6, 30, '2025-12-03 19:33:46'),
(54, 'Kapitel 4: De Sjunkna Ruinerna', 4, 1, 3, NULL, 'Svärdet ledde nu Finn till en sjö där en gammal stad sjunkit. Han fick dyka ner i det mörka vattnet. På botten hittade han den tredje skärvan i en kista, bevakad av slingrande sjögräs-monster. Han använde sin dolk för att skära sig loss och simma upp.', '[{\"q\":\"Finn hittade den tredje skärvan i en öken.\",\"a\":\"Falskt\"},{\"q\":\"Han behövde inte dyka.\",\"a\":\"Falskt\"},{\"q\":\"Skärvan låg i en kista.\",\"a\":\"Sant\"},{\"q\":\"Ingenting vaktade kistan.\",\"a\":\"Falskt\"}]', 7, 40, '2025-12-03 19:33:46'),
(55, 'Kapitel 5: Den Brinnande Öknen', 4, 1, 1, NULL, 'Den fjärde skärvan fanns i den Brinnande Öknen. Värmen var outhärdlig. Skärvan hölls av en eldjinn (en ande) som älskade gåtor. \\\"Vad slukar allt, men dör om det dricker?\\\" frågade jinnen. \\\"Eld!\\\" svarade Finn snabbt. Jinnen skrattade och gav honom skärvan.', '[{\"q\":\"Finn reste till en kall plats.\",\"a\":\"Falskt\"},{\"q\":\"En eldjinn vaktade skärvan.\",\"a\":\"Sant\"},{\"q\":\"Finn stal skärvan från jinnen.\",\"a\":\"Falskt\"},{\"q\":\"Svaret på gåtan var \\\"Vatten\\\".\",\"a\":\"Falskt\"}]', 8, 50, '2025-12-03 19:33:46'),
(56, 'Kapitel 6: Den Sista Skärvan', 4, 1, 3, NULL, 'Den sista skärvan fanns i Kungens skattkammare. Finn ville inte stjäla från kungen. Han gick istället till kungen och visade de fyra skärvorna. Han förklarade att svärdet var det enda som kunde besegra de skuggmonster som anfallit byarna. Kungen gav honom den sista skärvan.', '[{\"q\":\"Den sista skärvan var i en draks skatt.\",\"a\":\"Falskt\"},{\"q\":\"Finn stal den sista skärvan från kungen.\",\"a\":\"Falskt\"},{\"q\":\"Kungen gav Finn skärvan frivilligt.\",\"a\":\"Sant\"},{\"q\":\"Skuggmonster hade anfallit byarna.\",\"a\":\"Sant\"}]', 9, 60, '2025-12-03 19:33:46'),
(57, 'Kapitel 7: Smedens Mästarprov', 4, 1, 1, NULL, 'Finn återvände till sin mästersmed med de fem skärvorna. Mästersmeden var imponerad. \\\"Att smida ett magiskt svärd är det ultimata provet\\\", sa han. De arbetade tillsammans i tre dagar utan sömn, i den hetaste elden, för att foga samman delarna.', '[{\"q\":\"Finn smidde svärdet ensam.\",\"a\":\"Falskt\"},{\"q\":\"Det tog bara en timme att smida svärdet.\",\"a\":\"Falskt\"},{\"q\":\"De behövde en speciell, het eld.\",\"a\":\"Sant\"},{\"q\":\"Finn hade bara tre skärvor.\",\"a\":\"Falskt\"}]', 10, 70, '2025-12-03 19:33:46'),
(58, 'Kapitel 8: Skuggsvärdet', 4, 1, 3, NULL, 'När Finn lyfte det färdiga svärdet var det inte mörkt och ondskefullt. De viskningar han hört var bara svärdets ensamhet. Nu när det var helt, och smitt med Finns goda hjärta, lyste det med ett klart, vitt ljus. Det var inte Skuggsvärdet – det var Ljussvärdet.', '[{\"q\":\"Svärdet var kolsvart och pulserade av ondska.\",\"a\":\"Falskt\"},{\"q\":\"Svärdet lyste med ett vitt ljus.\",\"a\":\"Sant\"},{\"q\":\"Viskningarna hade varit onda.\",\"a\":\"Falskt\"},{\"q\":\"Svärdet döptes om till Ljussvärdet.\",\"a\":\"Sant\"}]', 11, 80, '2025-12-03 19:33:46'),
(59, 'Kapitel 9: Striden vid Solsång', 4, 1, 1, NULL, 'Finn reste till byn Solsång, som var belägrad av skuggmonster (Skuggmagikerns armé från den andra sagan!). Han lyfte Ljussvärdet. Ljuset från svärdet var starkare än monstren klarade av. De löstes upp och flydde. Finn hade räddat byn.', '[{\"q\":\"Finn reste till en okänd by.\",\"a\":\"Falskt\"},{\"q\":\"Byn var belägrad av skuggmonster.\",\"a\":\"Sant\"},{\"q\":\"Finn besegrade monstren med vanlig strid.\",\"a\":\"Falskt\"},{\"q\":\"Ljuset från svärdet skadade monstren.\",\"a\":\"Sant\"}]', 12, 90, '2025-12-03 19:33:46'),
(60, 'Kapitel 10: De Två Hjältarna', 4, 1, 3, NULL, 'I byn Solsång mötte Finn en ung kvinna vid namn Elara. Hon höll i en halv, lysande Solsten. Finn höll upp sitt Ljussvärd. De insåg att deras öden var sammanlänkade. Tillsammans hade de besegrat mörkret på två olika sätt. De var byns hjältar.', '[{\"q\":\"Finn mötte en gammal magiker i byn.\",\"a\":\"Falskt\"},{\"q\":\"Elara hade en mörk skug-sten.\",\"a\":\"Falskt\"},{\"q\":\"Finn och Elara var fiender.\",\"a\":\"Falskt\"},{\"q\":\"De hade båda besegrat mörkret.\",\"a\":\"Sant\"}]', 13, 100, '2025-12-03 19:33:46'),
(61, 'Kapitel 1: Skogens Rop', 2, 1, 1, NULL, 'Tariq var en ung lärling hos byns druid. Han studerade örter och skogens språk. En dag började den Stora Skogen att vissna. Löven blev svarta och djuren flydde i panik. Druiden sa att skogens hjärta var sjukt och bara kunde helas av en uråldrig ritual vid Månkällan.', '{\"s\":[\"Tariq var en ung druid-lärling.\",\"En dag började den Stora Skogen att vissna.\",\"Löven blev svarta och djuren flydde.\",\"Druiden sa att skogens hjärta var sjukt.\",\"Bara en uråldrig ritual vid Månkällan kunde hela skogen.\"]}', 4, 10, '2025-12-03 19:33:46'),
(62, 'Kapitel 2: Vägen till Gläntan', 2, 1, 3, NULL, 'För att börja ritualen måste Tariq hitta den Glömda Gläntan, där Månkällan sägs finnas. Han packade en väska med torkade örter och sin vandringsstav. Han följde en silverfärgad bäck som flödade mot de norra bergen. Bäcken ledde honom till ett dolt vattenfall. Bakom vattenfallet fanns en gömd stig.', '{\"s\":[\"Tariq måste hitta den Glömda Gläntan.\",\"Han packade en väska med örter och sin stav.\",\"Han följde en silverfärgad bäck mot bergen.\",\"Bäcken ledde honom till ett vattenfall.\",\"Bakom vattenfallet fanns en gömd stig.\"]}', 5, 20, '2025-12-03 19:33:46'),
(63, 'Kapitel 3: Vargflocken', 2, 1, 1, NULL, 'Den gömda stigen vaktades av en vargflock. Vargarna morrade och visade tänderna. Tariq, som kunde tala med djur, såg att ledarvargen haltade. Den hade en stor, infekterad tagg i tassen. Tariq tog mod till sig och gick långsamt fram. Han visade vargen en helande ört och drog försiktigt ut taggen. Som tack lät vargflocken honom passera.', '{\"s\":[\"En vargflock vaktade stigen.\",\"Tariq kunde tala med djur.\",\"Ledarvargen haltade på grund av en tagg.\",\"Han visade vargen en helande ört.\",\"Han drog försiktigt ut taggen.\",\"Vargflocken lät honom passera som tack.\"]}', 6, 30, '2025-12-03 19:33:46'),
(64, 'Kapitel 4: Stengolemen', 2, 1, 3, NULL, 'Stigen slutade abrupt vid en klippvägg. En stor Stengolem, täckt av mossa, reste sig ur marken. Den blockerade vägen. Tariq försökte inte slåss. Istället tog han fram sina örter och lade dem på Golemens huvud. Mossan på Golemen började omedelbart växa och blomma. Golemen log ett stenigt leende och klev åt sidan.', '{\"s\":[\"Stigen blockerades av en Stengolem.\",\"Golemen var täckt av mossa.\",\"Tariq använde inte våld.\",\"Han lade helande örter på Golemens huvud.\",\"Mossan på Golemen började blomma.\",\"Golemen flyttade sig och lät honom passera.\"]}', 7, 40, '2025-12-03 19:33:46'),
(65, 'Kapitel 5: Månkällan', 2, 1, 1, NULL, 'Tariq kom äntligen fram till den Glömda Gläntan. I mitten fanns Månkällan, men dess vatten var mörkt och stilla. Skogens hjärta var tyst. Ritualen krävde tre saker: En tår från en varg, en blomma från en Golem, och ett minne av ljus. Tariq hade redan de två första.', '{\"s\":[\"Tariq nådde den Glömda Gläntan.\",\"Månkällans vatten var mörkt och stilla.\",\"Ritualen krävde tre saker.\",\"De tre sakerna var en tår, en blomma och ett minne.\",\"Tariq hade redan tåren från vargen och blomman från Golemen.\"]}', 8, 50, '2025-12-03 19:33:46'),
(66, 'Kapitel 6: Minnet av Ljus', 2, 1, 3, NULL, 'Hur får man tag i \\\"ett minne av ljus\\\"? Tariq förstod inte. Han satte sig vid källan och mediterade. Han tänkte på sin by, Solsång, och hur ljuset från Solstenen kändes (detta kopplar ihop sagorna!). Han mindes värmen och glädjen. Han fokuserade all sin kraft på det minnet. En liten ljusglob formades i hans hand.', '{\"s\":[\"Tariq visste först inte hur han skulle få ett minne av ljus.\",\"Han satte sig ner för att meditera.\",\"Han tänkte på sin hemby Solsång.\",\"Han mindes ljuset från Solstenen.\",\"Ett litet klot av ljus formades i hans hand.\"]}', 9, 60, '2025-12-03 19:33:46'),
(67, 'Kapitel 7: Ritualen', 2, 1, 1, NULL, 'Tariq hade nu alla tre ingredienser. Han gick fram till källan. Försiktigt lade han vargens tår i vattnet. Sedan placerade han Golemens blomma på ytan. Till sist släppte han ljusminnet i källan. Vattnet började bubbla och ett starkt, vitt ljus sköt upp mot himlen.', '{\"s\":[\"Tariq hade alla tre ingredienser.\",\"Han lade först vargens tår i vattnet.\",\"Sedan placerade han Golemens blomma på ytan.\",\"Till sist släppte han ljusminnet i källan.\",\"Vattnet började bubbla och lysa starkt.\"]}', 10, 70, '2025-12-03 19:33:46'),
(68, 'Kapitel 8: Skogens Ande', 2, 1, 3, NULL, 'Ur ljuset steg en gestalt fram. Det var Skogens Ande, som hade varit fångad av mörkret. \\\"Du har befriat mig, unga druid\\\", sa Anden. \\\"Skogen var inte sjuk, den var förgiftad av Skuggmagikerns mörker. Ditt ljus har renat den.\\\" Anden gav Tariq en stav gjord av Månljus.', '{\"s\":[\"En gestalt steg fram ur ljuset.\",\"Det var Skogens Ande.\",\"Anden förklarade att skogen var förgiftad.\",\"Skuggmagikern var källan till förgiftningen.\",\"Tariqs ritual renade skogen.\",\"Som tack fick Tariq en stav av Månljus.\"]}', 11, 80, '2025-12-03 19:33:46'),
(69, 'Kapitel 9: Återväxten', 2, 1, 1, NULL, 'Tariq reste tillbaka genom skogen. Överallt där han gått började nu skogen att läka. De svarta löven föll av och nya, gröna knoppar sprack fram. Djuren började återvända. Vargflocken han mött ylade en hälsning när han passerade bron.', '{\"s\":[\"Tariq reste tillbaka genom skogen.\",\"Han såg att skogen började läka.\",\"Nya, gröna knoppar sprack fram.\",\"Djuren började återvända.\",\"Vargflocken ylade en vänskaplig hälsning.\"]}', 12, 90, '2025-12-03 19:33:46'),
(70, 'Kapitel 10: Druidmästaren', 2, 1, 3, NULL, 'När Tariq kom tillbaka till sin by var mörkret borta. Skogen var grön igen. Den gamla druiden mötte honom. &amp;quot;Du är ingen lärling längre&amp;quot;, sa han stolt. &amp;quot;Du är Tariq, Skogens Vän och en sann Druidmästare.&amp;quot; Tariq visste att hans äventyr bara hade börjat.', '{\"s\":[\"När Tariq kom tillbaka var skogen grön igen.\",\"Den gamla druiden mötte honom stolt.\",\"Druiden sa att Tariq inte längre var en lärling.\",\"Han kallade honom \'Skogens Vän\' och \'Druidmästare\'.\",\"Tariq förstod att detta bara var början.\"]}', 13, 100, '2025-12-03 19:33:46'),
(71, 'Kapitel 1: Uppskjutningen', 1, 2, 3, NULL, 'Astronauten Leo spände fast sig i sätet. Raketen \"Stjärnfalken\" darrade när motorerna startade. \"Tio, nio, åtta...\" räknade datorn ner. Leo tog ett djupt andetag. Hans uppdrag var att hitta vatten på Mars. Med ett enormt dån lyfte raketen mot himlen.', '[{\"q\":\"Vad hette raketen?\",\"a\":\"Stjärnfalken\",\"w1\":\"Månstrålen\",\"w2\":\"Marsresan\",\"w3\":\"Kometen\"},{\"q\":\"Vart var Leo på väg?\",\"a\":\"Till Mars\",\"w1\":\"Till Månen\",\"w2\":\"Till Solen\",\"w3\":\"\"},{\"q\":\"Vad var hans uppdrag?\",\"a\":\"Hitta vatten\",\"w1\":\"Hitta liv\",\"w2\":\"Plantera träd\",\"w3\":\"\"}]', 4, 10, '2025-12-03 19:33:46'),
(72, 'Kapitel 2: Tyngdlös', 1, 2, 3, NULL, 'Ute i rymden stängdes motorerna av. Plötsligt kände Leo sig fjäderlätt. Han lossade bältet och svävade upp ur stolen. Hans penna svävade bredvid honom. Genom fönstret såg han Jorden, som en liten blå kula långt borta.', '[{\"q\":\"Vad hände när motorerna stängdes av?\",\"a\":\"Leo blev tyngdlös\",\"w1\":\"Raketen stannade\",\"w2\":\"Det blev mörkt\",\"w3\":\"\"},{\"q\":\"Vad såg han genom fönstret?\",\"a\":\"Jorden\",\"w1\":\"Mars\",\"w2\":\"En annan raket\",\"w3\":\"\"},{\"q\":\"Vad svävade bredvid honom?\",\"a\":\"En penna\",\"w1\":\"En bok\",\"w2\":\"Hans hjälm\",\"w3\":\"\"}]', 5, 20, '2025-12-03 19:33:46'),
(73, 'Kapitel 3: Meteorregn', 1, 2, 3, NULL, 'Varningslampan började blinka rött. \"Varning! Meteorregn!\" sa datorn. Leo flög snabbt till kontrollpanelen. Han styrde raketen åt vänster, sedan höger. En liten sten träffade vingen med ett \"KLONK\", men de klarade sig igenom.', '[{\"q\":\"Varför blinkade lampan?\",\"a\":\"Det var meteorregn\",\"w1\":\"Motorn var trasig\",\"w2\":\"Bensinen var slut\",\"w3\":\"\"},{\"q\":\"Vad gjorde Leo?\",\"a\":\"Han styrde undan\",\"w1\":\"Han gömde sig\",\"w2\":\"Han ropade på hjälp\",\"w3\":\"\"},{\"q\":\"Blev raketen träffad?\",\"a\":\"Ja, på vingen\",\"w1\":\"Nej, de missade allt\",\"w2\":\"Ja, fönstret gick sönder\",\"w3\":\"\"}]', 6, 30, '2025-12-03 19:33:46'),
(74, 'Kapitel 4: Den Röda Planeten', 1, 2, 3, NULL, 'Efter sex månader såg Leo äntligen Mars. Hela planeten var täckt av röd sand och höga berg. Han startade landningsmotorerna. \"Stjärnfalken\" sjönk sakta ner mot ytan. Damm virvlade upp när de landade mjukt.', '[{\"q\":\"Hur lång tid tog resan?\",\"a\":\"Sex månader\",\"w1\":\"Sex dagar\",\"w2\":\"Ett år\",\"w3\":\"\"},{\"q\":\"Vilken färg hade planeten?\",\"a\":\"Röd\",\"w1\":\"Blå\",\"w2\":\"Grön\",\"w3\":\"\"},{\"q\":\"Hur landade de?\",\"a\":\"Mjukt\",\"w1\":\"De kraschade\",\"w2\":\"I havet\",\"w3\":\"\"}]', 7, 40, '2025-12-03 19:33:46'),
(75, 'Kapitel 5: Marsvandring', 1, 2, 3, NULL, 'Leo tog på sig sin rymddräkt och hjälm. Han öppnade luckan och klev ut. Gravitationen var svagare här, så han kunde ta jättelånga hopp. Han samlade sandprover i små burkar. Det var tyst och ödsligt, men vackert.', '[{\"q\":\"Varför tog han långa hopp?\",\"a\":\"Gravitationen var svagare\",\"w1\":\"Han var glad\",\"w2\":\"Marken var het\",\"w3\":\"\"},{\"q\":\"Vad samlade han?\",\"a\":\"Sandprover\",\"w1\":\"Blommor\",\"w2\":\"Guld\",\"w3\":\"\"},{\"q\":\"Hur var det på Mars?\",\"a\":\"Tyst och ödsligt\",\"w1\":\"Bullrigt\",\"w2\":\"Fullt av djur\",\"w3\":\"\"}]', 8, 50, '2025-12-03 19:33:46'),
(76, 'Kapitel 6: Roboten R1', 1, 2, 3, NULL, 'Leo var inte ensam. Han hade med sig roboten R1. R1 rullade på larvfötter och hade en kamera som öga. \"Jag skannar området\", pep R1. Roboten kunde se saker som Leo missade. Plötsligt stannade R1 vid en stor klippa.', '[{\"q\":\"Vem var R1?\",\"a\":\"En robot\",\"w1\":\"En hund\",\"w2\":\"En annan astronaut\",\"w3\":\"\"},{\"q\":\"Hur tog sig R1 fram?\",\"a\":\"På larvfötter\",\"w1\":\"Den flög\",\"w2\":\"Den gick på ben\",\"w3\":\"\"},{\"q\":\"Vad gjorde R1?\",\"a\":\"Skannade området\",\"w1\":\"Lagade mat\",\"w2\":\"Sov\",\"w3\":\"\"}]', 9, 60, '2025-12-03 19:33:46'),
(77, 'Kapitel 7: Grottan', 1, 2, 3, NULL, 'R1 hade hittat en öppning i berget. En grotta! Leo tände sin ficklampa och gick in. Det var svalare där inne. Väggarna glittrade av kristaller. Längst in i grottan såg han något som blänkte annorlunda. Det såg ut som is.', '[{\"q\":\"Vad hade R1 hittat?\",\"a\":\"En grotta\",\"w1\":\"Ett hus\",\"w2\":\"En sjö\",\"w3\":\"\"},{\"q\":\"Vad fanns på väggarna?\",\"a\":\"Kristaller\",\"w1\":\"Målningar\",\"w2\":\"Mossa\",\"w3\":\"\"},{\"q\":\"Vad såg Leo längst in?\",\"a\":\"Något som såg ut som is\",\"w1\":\"Ett monster\",\"w2\":\"En rymdvarelse\",\"w3\":\"\"}]', 10, 70, '2025-12-03 19:33:46'),
(78, 'Kapitel 8: Upptäckten', 1, 2, 3, NULL, 'Leo gick fram och rörde vid det blanka. Det var hårt och kallt. Han hackade loss en bit och lade i en behållare som R1 bar. \"Analys: Fruset vatten\", sa R1. Leo jublade! Han hade hittat is på Mars. Det betydde att människor kanske kunde bo här i framtiden.', '[{\"q\":\"Vad var det blanka för något?\",\"a\":\"Fruset vatten (Is)\",\"w1\":\"Glas\",\"w2\":\"Silver\",\"w3\":\"\"},{\"q\":\"Vad gjorde Leo?\",\"a\":\"Hackade loss en bit\",\"w1\":\"Smälte det\",\"w2\":\"Åt upp det\",\"w3\":\"\"},{\"q\":\"Varför var det viktigt?\",\"a\":\"Människor kan bo där i framtiden\",\"w1\":\"Han blev rik\",\"w2\":\"Det var snyggt\",\"w3\":\"\"}]', 11, 80, '2025-12-03 19:33:46'),
(79, 'Kapitel 9: Sandstormen', 1, 2, 3, NULL, 'När de kom ut ur grottan hade himlen blivit mörk. En sandstorm var på väg! \"Varning, vindhastighet ökar\", sa R1. Leo och roboten fick kämpa sig tillbaka mot raketen. Sanden piskade mot hjälmen så han knappt såg något. De hann precis in och stänga luckan.', '[{\"q\":\"Vad hände när de kom ut?\",\"a\":\"En sandstorm kom\",\"w1\":\"Det började regna\",\"w2\":\"Det blev natt\",\"w3\":\"\"},{\"q\":\"Varför var det svårt att gå?\",\"a\":\"Vinden var stark och sanden piskade\",\"w1\":\"Det var för varmt\",\"w2\":\"R1 gick sönder\",\"w3\":\"\"},{\"q\":\"Var sökte de skydd?\",\"a\":\"I raketen\",\"w1\":\"I grottan\",\"w2\":\"Under en sten\",\"w3\":\"\"}]', 12, 90, '2025-12-03 19:33:46'),
(80, 'Kapitel 10: Hemfärd', 1, 2, 3, NULL, 'Stormen lade sig. Leo gjorde raketen redo för start. Han hade lyckats med sitt uppdrag. Han startade motorerna och \"Stjärnfalken\" lämnade den röda planeten. Leo tittade ner en sista gång. Han visste att han skulle komma tillbaka en dag.', '[{\"q\":\"Vad gjorde Leo efter stormen?\",\"a\":\"Åkte hem\",\"w1\":\"Gick ut igen\",\"w2\":\"Byggde ett hus\",\"w3\":\"\"},{\"q\":\"Hade han lyckats?\",\"a\":\"Ja\",\"w1\":\"Nej\",\"w2\":\"Kanske\",\"w3\":\"\"},{\"q\":\"Vad tänkte Leo när han åkte?\",\"a\":\"Att han skulle komma tillbaka\",\"w1\":\"Att han aldrig ville se Mars igen\",\"w2\":\"Att han var hungrig\",\"w3\":\"\"}]', 13, 100, '2025-12-03 19:33:46'),
(81, 'Kapitel 1: Det försvunna halsbandet', 4, 3, 1, NULL, 'Detektiv Holmes satt på tåget mot London. Plötsligt skrek en dam i kupén bredvid. \"Mitt diamanthalsband! Det är borta!\" Holmes reste sig genast. Han gick in till damen, Fru Rask. Hon pekade på en tom sammetsask på bordet.', '[{\"q\":\"Detektiven hette Holmes.\",\"a\":\"Sant\"},{\"q\":\"Tåget var på väg till Paris.\",\"a\":\"Falskt\"},{\"q\":\"Fru Rask hade tappat sin plånbok.\",\"a\":\"Falskt\"},{\"q\":\"Halsbandet låg i en sammetsask.\",\"a\":\"Sant\"}]', 4, 10, '2025-12-03 19:33:46'),
(82, 'Kapitel 2: De misstänkta', 4, 3, 1, NULL, 'Det fanns tre andra personer i vagnen. En ung man som läste en tidning, en äldre herre med käpp, och en kvinna med en stor hatt. Holmes bad konduktören att låsa dörrarna. Ingen fick lämna vagnen förrän halsbandet var funnet.', '[{\"q\":\"Det fanns fem misstänkta.\",\"a\":\"Falskt\"},{\"q\":\"En man läste en tidning.\",\"a\":\"Sant\"},{\"q\":\"En kvinna hade en stor hatt.\",\"a\":\"Sant\"},{\"q\":\"Holmes lät alla gå hem.\",\"a\":\"Falskt\"}]', 5, 20, '2025-12-03 19:33:46'),
(83, 'Kapitel 3: Förhöret', 4, 3, 1, NULL, 'Holmes pratade med den unge mannen först. \"Jag har sovit hela tiden\", sa han. Men Holmes såg att han höll tidningen upp och ner. Det verkade misstänkt. Den äldre herren sa att han inte kunde gå bra, så han hade suttit stilla. Kvinnan med hatten verkade nervös.', '[{\"q\":\"Den unge mannen sa att han sovit.\",\"a\":\"Sant\"},{\"q\":\"Han höll tidningen rätt väg.\",\"a\":\"Falskt\"},{\"q\":\"Den äldre herren hade en cykel.\",\"a\":\"Falskt\"},{\"q\":\"Kvinnan med hatten var lugn.\",\"a\":\"Falskt\"}]', 6, 30, '2025-12-03 19:33:46'),
(84, 'Kapitel 4: Ledtråden på golvet', 4, 3, 1, NULL, 'Holmes undersökte golvet med sitt förstoringsglas. Vid den äldre herrens fötter hittade han en liten bit röd tråd. Men sammetsasken var blå. Tråden måste komma från tjuven! Holmes tittade på de andras kläder.', '[{\"q\":\"Holmes använde ett mikroskop.\",\"a\":\"Falskt\"},{\"q\":\"Han hittade en röd tråd.\",\"a\":\"Sant\"},{\"q\":\"Asken var röd.\",\"a\":\"Falskt\"},{\"q\":\"Tråden var en ledtråd.\",\"a\":\"Sant\"}]', 7, 40, '2025-12-03 19:33:46'),
(85, 'Kapitel 5: Den röda halsduken', 4, 3, 1, NULL, 'Ingen av passagerarna hade röda kläder. Det var konstigt. Då såg Holmes att den unge mannens väska stack fram lite under sätet. En bit av en röd halsduk hängde ut. \"Får jag se din väska?\" frågade Holmes.', '[{\"q\":\"Alla passagerare hade röda kläder.\",\"a\":\"Falskt\"},{\"q\":\"Den unge mannen hade en röd halsduk i väskan.\",\"a\":\"Sant\"},{\"q\":\"Väskan låg på hyllan.\",\"a\":\"Falskt\"},{\"q\":\"Holmes bad att få se väskan.\",\"a\":\"Sant\"}]', 8, 50, '2025-12-03 19:33:46'),
(86, 'Kapitel 6: En oväntad vändning', 4, 3, 1, NULL, 'Mannen gav motvilligt väskan till Holmes. I den låg halsduken, men inget halsband. \"Jag stal halsduken\", erkände mannen, \"men inte diamanten!\" Holmes trodde honom. Tjuven var smartare än så. Han letade vidare.', '[{\"q\":\"Halsbandet låg i väskan.\",\"a\":\"Falskt\"},{\"q\":\"Mannen hade stulit halsduken.\",\"a\":\"Sant\"},{\"q\":\"Holmes trodde att mannen ljög om diamanten.\",\"a\":\"Falskt\"},{\"q\":\"Tjuven var smart.\",\"a\":\"Sant\"}]', 9, 60, '2025-12-03 19:33:46'),
(87, 'Kapitel 7: Käppen', 4, 3, 1, NULL, 'Holmes gick tillbaka till den äldre herren. Han tittade noga på hans käpp. Den såg ovanligt tjock ut. \"Får jag låna din käpp?\" frågade Holmes. Herren blev blek men räckte över den. Käppen kändes tung.', '[{\"q\":\"Holmes undersökte herrens hatt.\",\"a\":\"Falskt\"},{\"q\":\"Käppen såg tjock ut.\",\"a\":\"Sant\"},{\"q\":\"Herren blev glad att låna ut käppen.\",\"a\":\"Falskt\"},{\"q\":\"Käppen var tung.\",\"a\":\"Sant\"}]', 10, 70, '2025-12-03 19:33:46'),
(88, 'Kapitel 8: Hemligheten', 4, 3, 1, NULL, 'Holmes skruvade på käppens handtag. Det gick att skruva av! Inuti käppen fanns ett hålrum. Holmes vände käppen upp och ner. Ut föll det gnistrande diamanthalsbandet. \"Aha!\" ropade Holmes.', '[{\"q\":\"Handtaget gick att skruva av.\",\"a\":\"Sant\"},{\"q\":\"Käppen var massivt trä rakt igenom.\",\"a\":\"Falskt\"},{\"q\":\"Halsbandet var gömt i käppen.\",\"a\":\"Sant\"},{\"q\":\"Holmes hittade ingenting.\",\"a\":\"Falskt\"}]', 11, 80, '2025-12-03 19:33:46'),
(89, 'Kapitel 9: Tjuven avslöjad', 4, 3, 1, NULL, 'Den äldre herren var egentligen en känd juveltjuv vid namn \"Skuggan\". Han hade klätt ut sig för att lura polisen. Han hade stulit halsbandet när tåget åkte in i en tunnel och det blev mörkt. Fru Rask fick tillbaka sitt smycke.', '[{\"q\":\"Herren var egentligen en tjuv.\",\"a\":\"Sant\"},{\"q\":\"Han kallades \"Solen\".\",\"a\":\"Falskt\"},{\"q\":\"Han stal smycket när det var ljust.\",\"a\":\"Falskt\"},{\"q\":\"Fru Rask fick tillbaka halsbandet.\",\"a\":\"Sant\"}]', 12, 90, '2025-12-03 19:33:46'),
(90, 'Kapitel 10: Fallet löst', 4, 3, 1, NULL, 'När tåget kom fram till London väntade polisen på perrongen. Holmes överlämnade tjuven. \"Ett snyggt jobb\", sa konduktören. Holmes log och tände sin pipa. \"Det var elementärt\", sa han. Fallet var avslutat.', '[{\"q\":\"Polisen väntade i London.\",\"a\":\"Sant\"},{\"q\":\"Tjuven lyckades rymma.\",\"a\":\"Falskt\"},{\"q\":\"Holmes sa att det var svårt.\",\"a\":\"Falskt\"},{\"q\":\"Fallet var löst.\",\"a\":\"Sant\"}]', 13, 100, '2025-12-03 19:33:46'),
(91, 'Kapitel 1: Vadet', 2, 4, 3, NULL, 'Alex och hans vänner stod framför det gamla, öde huset på kullen. Ingen hade bott där på femtio år. \"Jag vågar dig att gå in\", sa Alex kompis Sam. Alex ville inte verka feg. Han tog ett djupt andetag och gick mot den gnisslande grinden.', '{\"s\":[\"Alex och vännerna stod framför det öde huset.\",\"Sam vågade Alex att gå in.\",\"Alex ville inte vara feg.\",\"Han tog ett djupt andetag.\",\"Han gick mot den gnisslande grinden.\"]}', 4, 10, '2025-12-03 19:33:46'),
(92, 'Kapitel 2: Entrén', 2, 4, 3, NULL, 'Alex tryckte upp den tunga ytterdörren. Den gled upp med ett långt jämmer. Han klev in i hallen. Det luktade damm och gamla möbler. Golvplankorna knarrade under hans fötter. Plötsligt slog dörren igen bakom honom med en smäll!', '{\"s\":[\"Alex tryckte upp ytterdörren.\",\"Han klev in i hallen.\",\"Det luktade damm.\",\"Golvet knarrade under fötterna.\",\"Dörren slog igen bakom honom.\"]}', 5, 20, '2025-12-03 19:33:46'),
(93, 'Kapitel 3: Fotstegen', 2, 4, 3, NULL, 'Han ryckte i handtaget, men dörren var låst. Alex var instängd. Då hörde han ljud från övervåningen. Tunga fotsteg. Dunk. Dunk. Dunk. Någon gick långsamt i trappan. Alex hjärta bultade hårt.', '{\"s\":[\"Alex försökte öppna dörren men den var låst.\",\"Han hörde ljud från övervåningen.\",\"Han hörde tunga fotsteg.\",\"Någon gick i trappan.\",\"Alex hjärta bultade hårt.\"]}', 6, 30, '2025-12-03 19:33:46'),
(94, 'Kapitel 4: Tavlan', 2, 4, 3, NULL, 'Alex backade in i vardagsrummet. På väggen hängde en tavla av en gammal man med sträng blick. Alex tittade på tavlan. Mannens ögon verkade röra sig. De följde Alex när han gick genom rummet. Han rös och skyndade vidare.', '{\"s\":[\"Alex backade in i vardagsrummet.\",\"Han såg en tavla på väggen.\",\"Tavlan föreställde en gammal man.\",\"Mannens ögon rörde sig.\",\"Alex rös och gick vidare.\"]}', 7, 40, '2025-12-03 19:33:46'),
(95, 'Kapitel 5: Köket', 2, 4, 3, NULL, 'Han kom in i köket. Det var kallare här. På bordet stod en tallrik med rutten mat. En stol drogs ut av sig själv över golvet. Alex skrek till. En kall vindpust svepte förbi honom och släckte hans ficklampa.', '{\"s\":[\"Han kom in i det kalla köket.\",\"En stol drogs ut av sig själv.\",\"Alex skrek till.\",\"En kall vindpust kom.\",\"Ficklampan slocknade.\"]}', 8, 50, '2025-12-03 19:33:46'),
(96, 'Kapitel 6: I Mörkret', 2, 4, 3, NULL, 'Nu var det kolmörkt. Alex trevade sig fram längs väggen. Hans hand rörde vid något kladdigt. Han torkade av handen på byxorna. Han hörde en viskning precis vid örat: \"Gå härifrån...\". Han ville inget hellre.', '{\"s\":[\"Det blev kolmörkt.\",\"Alex trevade längs väggen.\",\"Han rörde vid något kladdigt.\",\"Han hörde en viskning vid örat.\",\"Rösten sa åt honom att gå.\"]}', 9, 60, '2025-12-03 19:33:46'),
(97, 'Kapitel 7: Biblioteket', 2, 4, 3, NULL, 'Han snubblade in i biblioteket. Månljus lyste in genom fönstret. Böcker började ramla ut ur hyllorna, en efter en. De flög genom luften mot honom. Alex dök ner bakom en soffa för att skydda sig.', '{\"s\":[\"Han kom in i biblioteket.\",\"Månljus lyste in.\",\"Böcker ramlade ur hyllorna.\",\"Böckerna flög mot honom.\",\"Han gömde sig bakom en soffa.\"]}', 10, 70, '2025-12-03 19:33:46'),
(98, 'Kapitel 8: Nyckeln', 2, 4, 3, NULL, 'Under soffan såg han något glimma. En gammal rostig nyckel! Kunde den gå till ytterdörren? Böckerna slutade flyga. Det blev tyst. Alex kröp fram och nappade åt sig nyckeln. Han sprang mot hallen.', '{\"s\":[\"Han såg något glimma under soffan.\",\"Det var en rostig nyckel.\",\"Böckerna slutade flyga.\",\"Alex tog nyckeln.\",\"Han sprang mot hallen.\"]}', 11, 80, '2025-12-03 19:33:46'),
(99, 'Kapitel 9: Flykten', 2, 4, 3, NULL, 'Han nådde ytterdörren. Han darrade på handen när han satte nyckeln i låset. Fotstegen i trappan kom närmare. Nyckeln vreds om med ett klick. Han tryckte upp dörren och ramlade ut i gräset.', '{\"s\":[\"Han nådde ytterdörren.\",\"Han satte nyckeln i låset.\",\"Fotstegen kom närmare.\",\"Låset klickade upp.\",\"Han ramlade ut i gräset.\"]}', 12, 90, '2025-12-03 19:33:46'),
(100, 'Kapitel 10: Aldrig mer', 2, 4, 3, NULL, 'Alex vänner stod utanför och väntade. \"Du var bara där inne i en minut!\" sa Sam. Alex var blek och skakade. Han vände sig om. I fönstret på övervåningen såg han den gamla mannen från tavlan titta ut. Alex lovade sig själv att aldrig gå nära det huset igen.', '{\"s\":[\"Vännerna väntade utanför.\",\"Sam sa att det bara gått en minut.\",\"Alex vände sig om.\",\"Han såg mannen i fönstret.\",\"Han lovade att aldrig gå tillbaka.\"]}', 13, 100, '2025-12-03 19:33:46'),
(101, 'Kapitel 1: Pyramiderna', 1, 5, 1, NULL, 'I Egyptens öken står de stora pyramiderna. De byggdes för tusentals år sedan som gravar åt faraonerna. Den största heter Cheopspyramiden. Den är byggd av över två miljoner stenblock. Det är ett av världens sju underverk.', '[{\"q\":\"Var ligger pyramiderna?\",\"a\":\"I Egypten\",\"w1\":\"I Sverige\",\"w2\":\"I Kina\",\"w3\":\"\"},{\"q\":\"Vad användes de som?\",\"a\":\"Gravar\",\"w1\":\"Bostadshus\",\"w2\":\"Butiker\",\"w3\":\"\"},{\"q\":\"Vad heter den största pyramiden?\",\"a\":\"Cheopspyramiden\",\"w1\":\"Sfinxen\",\"w2\":\"Lund\",\"w3\":\"\"}]', 4, 10, '2025-12-03 19:33:46'),
(102, 'Kapitel 2: Kinesiska Muren', 1, 5, 1, NULL, 'Kinesiska muren är världens längsta byggnadsverk. Den är över 600 mil lång! Den byggdes för att skydda Kina från fiender i norr. Muren är så stor att man ibland säger att den syns från rymden, men det är faktiskt en myt.', '[{\"q\":\"Hur lång är muren?\",\"a\":\"Över 600 mil\",\"w1\":\"100 mil\",\"w2\":\"10 mil\",\"w3\":\"\"},{\"q\":\"Varför byggdes den?\",\"a\":\"För skydd mot fiender\",\"w1\":\"För att den var fin\",\"w2\":\"Som en väg\",\"w3\":\"\"},{\"q\":\"Syns den från månen?\",\"a\":\"Nej, det är en myt\",\"w1\":\"Ja, tydligt\",\"w2\":\"Endast på natten\",\"w3\":\"\"}]', 5, 20, '2025-12-03 19:33:46'),
(103, 'Kapitel 3: Amazonas Regnskog', 1, 5, 1, NULL, 'Amazonas i Sydamerika är världens största regnskog. Den kallas för \"Jordens lungor\" för att träden producerar så mycket syre. Här bor miljoner olika djurarter, som jaguarer, apor och färgglada papegojor. Genom skogen rinner Amazonfloden.', '[{\"q\":\"Vad kallas Amazonas?\",\"a\":\"Jordens lungor\",\"w1\":\"Jordens hjärta\",\"w2\":\"Jordens mage\",\"w3\":\"\"},{\"q\":\"Var ligger Amazonas?\",\"a\":\"Sydamerika\",\"w1\":\"Afrika\",\"w2\":\"Asien\",\"w3\":\"\"},{\"q\":\"Vilken flod rinner där?\",\"a\":\"Amazonfloden\",\"w1\":\"Nilen\",\"w2\":\"Themsen\",\"w3\":\"\"}]', 6, 30, '2025-12-03 19:33:46'),
(104, 'Kapitel 4: Mount Everest', 1, 5, 1, NULL, 'Mount Everest är världens högsta berg. Toppen ligger 8 848 meter över havet. Det är mycket farligt att klättra dit. Luften är tunn och det är iskallt. De som bor i området och hjälper klättrare kallas för Sherpas.', '[{\"q\":\"Vad är Mount Everest?\",\"a\":\"Världens högsta berg\",\"w1\":\"En vulkan\",\"w2\":\"En djup dal\",\"w3\":\"\"},{\"q\":\"Hur högt är det?\",\"a\":\"8 848 meter\",\"w1\":\"5 000 meter\",\"w2\":\"10 000 meter\",\"w3\":\"\"},{\"q\":\"Vad kallas hjälparna?\",\"a\":\"Sherpas\",\"w1\":\"Guider\",\"w2\":\"Klättrare\",\"w3\":\"\"}]', 7, 40, '2025-12-03 19:33:46'),
(105, 'Kapitel 5: Marianergraven', 1, 5, 1, NULL, 'Djupt nere i Stilla Havet ligger Marianergraven. Det är världens djupaste plats. Den är 11 kilometer djup! Det är djupare än Mount Everest är högt. Där nere är det totalt mörker och enormt tryck. Ändå lever det märkliga fiskar där.', '[{\"q\":\"Var ligger Marianergraven?\",\"a\":\"I Stilla Havet\",\"w1\":\"I Atlanten\",\"w2\":\"I Östersjön\",\"w3\":\"\"},{\"q\":\"Hur djup är den?\",\"a\":\"11 kilometer\",\"w1\":\"2 kilometer\",\"w2\":\"100 meter\",\"w3\":\"\"},{\"q\":\"Finns det liv där?\",\"a\":\"Ja, märkliga fiskar\",\"w1\":\"Nej, det är dött\",\"w2\":\"Bara växter\",\"w3\":\"\"}]', 8, 50, '2025-12-03 19:33:46'),
(106, 'Kapitel 6: Antarktis', 1, 5, 1, NULL, 'Antarktis ligger på Sydpolen. Det är den kallaste platsen på jorden. Marken är täckt av tjock is. Inga människor bor där permanent, bara forskare. Men där bor många pingviner som tål kylan bra.', '[{\"q\":\"Var ligger Antarktis?\",\"a\":\"På Sydpolen\",\"w1\":\"På Nordpolen\",\"w2\":\"I Europa\",\"w3\":\"\"},{\"q\":\"Vilka bor där permanent?\",\"a\":\"Inga människor\",\"w1\":\"Forskare\",\"w2\":\"Eskimåer\",\"w3\":\"\"},{\"q\":\"Vilka djur är vanliga där?\",\"a\":\"Pingviner\",\"w1\":\"Isbjörnar\",\"w2\":\"Lejon\",\"w3\":\"\"}]', 9, 60, '2025-12-03 19:33:46'),
(107, 'Kapitel 7: Colosseum', 1, 5, 1, NULL, 'I Rom i Italien ligger Colosseum. Det är en gammal amfiteater som byggdes av romarna. Förr i tiden tittade folk på gladiatorer som stred mot varandra och mot vilda djur där. Det rymde över 50 000 åskådare.', '[{\"q\":\"Var ligger Colosseum?\",\"a\":\"I Rom\",\"w1\":\"I Paris\",\"w2\":\"I London\",\"w3\":\"\"},{\"q\":\"Vad tittade man på där?\",\"a\":\"Gladiatorer\",\"w1\":\"Fotboll\",\"w2\":\"Bio\",\"w3\":\"\"},{\"q\":\"Vem byggde det?\",\"a\":\"Romarna\",\"w1\":\"Grekerna\",\"w2\":\"Vikingarna\",\"w3\":\"\"}]', 10, 70, '2025-12-03 19:33:46'),
(108, 'Kapitel 8: Norrsken', 1, 5, 1, NULL, 'Norrsken är ett vackert ljusfenomen på himlen. Det ser ut som gröna, lila och rosa gardiner som dansar. Det händer när partiklar från solen krockar med jordens atmosfär. Man ser det bäst nära Nordpolen på vintern.', '[{\"q\":\"Vad är norrsken?\",\"a\":\"Ett ljusfenomen\",\"w1\":\"En stjärna\",\"w2\":\"Ett moln\",\"w3\":\"\"},{\"q\":\"Vilka färger är vanliga?\",\"a\":\"Grön, lila, rosa\",\"w1\":\"Svart och vitt\",\"w2\":\"Bara gult\",\"w3\":\"\"},{\"q\":\"När ser man det bäst?\",\"a\":\"På vintern\",\"w1\":\"På sommaren\",\"w2\":\"På dagen\",\"w3\":\"\"}]', 11, 80, '2025-12-03 19:33:46'),
(109, 'Kapitel 9: Vulkaner', 1, 5, 1, NULL, 'En vulkan är en öppning i jordskorpan där magma kommer ut. När magman kommer upp till ytan kallas den lava. Vissa vulkaner sover, andra är aktiva och kan få utbrott. Pompeji var en stad som begravdes av aska från en vulkan.', '[{\"q\":\"Vad kommer ut ur en vulkan?\",\"a\":\"Magma/Lava\",\"w1\":\"Vatten\",\"w2\":\"Olja\",\"w3\":\"\"},{\"q\":\"Vad kallas magma på ytan?\",\"a\":\"Lava\",\"w1\":\"Sten\",\"w2\":\"Eld\",\"w3\":\"\"},{\"q\":\"Vilken stad begravdes?\",\"a\":\"Pompeji\",\"w1\":\"Rom\",\"w2\":\"Aten\",\"w3\":\"\"}]', 12, 90, '2025-12-03 19:33:46'),
(110, 'Kapitel 10: Månen', 1, 5, 1, NULL, '1969 landade de första människorna på månen. Astronauten Neil Armstrong var den första som gick på ytan. Han sa: \"Ett litet steg för en människa, ett jättekliv för mänskligheten.\" Det finns ingen luft på månen, så de måste ha rymddräkter.', '[{\"q\":\"När landade vi på månen?\",\"a\":\"1969\",\"w1\":\"2000\",\"w2\":\"1850\",\"w3\":\"\"},{\"q\":\"Vem var först?\",\"a\":\"Neil Armstrong\",\"w1\":\"Buzz Lightyear\",\"w2\":\"Yuri Gagarin\",\"w3\":\"\"},{\"q\":\"Finns det luft på månen?\",\"a\":\"Nej\",\"w1\":\"Ja\",\"w2\":\"Lite grann\",\"w3\":\"\"}]', 13, 100, '2025-12-03 19:33:46'),
(113, 'Kapitel 1: Den Trasiga Klockan', 3, 1, 1, NULL, 'Aria var en ung uppfinnare som bodde i Klockstaden. En dag stannade den Stora Klockan i tornet. Tiden började gå baklänges! Aria hittade en gammal ritning i sin verkstad som visade att klockan behövde Tidens Nyckel för att lagas.', '[\r\n        {\"term\":\"Aria\", \"def\":\"En ung uppfinnare\"},\r\n        {\"term\":\"Klockstaden\", \"def\":\"Staden där Aria bor\"},\r\n        {\"term\":\"Stora Klockan\", \"def\":\"Gick sönder och stannade\"},\r\n        {\"term\":\"Tidens Nyckel\", \"def\":\"Behövs för att laga klockan\"}\r\n    ]', 4, 10, '2025-12-03 19:33:46'),
(114, 'Kapitel 2: Den Talande Vargen', 3, 1, 1, NULL, 'För att hitta nyckeln var Aria tvungen att korsa Viskande Skogen. Där mötte hon Kael, en stor silvervarg med lysande blå ögon. Till Arias förvåning kunde Kael prata! \"Jag vaktar skogen\", sa vargen. \"Vem är du som stör tystnaden?\"', '[\r\n        {\"term\":\"Kael\", \"def\":\"En talande silvervarg\"},\r\n        {\"term\":\"Viskande Skogen\", \"def\":\"Platsen där de möttes\"},\r\n        {\"term\":\"Silver\", \"def\":\"Färgen på Kaels päls\"},\r\n        {\"term\":\"Blå\", \"def\":\"Färgen på Kaels ögon\"}\r\n    ]', 5, 20, '2025-12-03 19:33:46'),
(115, 'Kapitel 3: Gåtornas Bro', 3, 1, 1, NULL, 'Aria och Kael kom fram till en gammal stenbro över en djup ravin. En liten trollgubbe hoppade fram. \"För att passera måste ni para ihop mina magiska ord med deras rätta betydelse\", skrek han. Aria tog fram sin anteckningsbok.', '[\r\n        {\"term\":\"Stenbro\", \"def\":\"Gick över en djup ravin\"},\r\n        {\"term\":\"Trollgubbe\", \"def\":\"Vaktade bron\"},\r\n        {\"term\":\"Anteckningsbok\", \"def\":\"Arias hjälpmedel\"},\r\n        {\"term\":\"Para ihop\", \"def\":\"Vad de var tvungna att göra\"}\r\n    ]', 6, 30, '2025-12-03 19:33:46'),
(116, 'Kapitel 4: Kristallgrottan', 3, 1, 1, NULL, 'På andra sidan bron fanns en mörk grotta. Inuti lyste tusentals kristaller i olika färger. \"Dessa är Minnes-kristaller\", viskade Kael. \"De visar vad som hände för länge sedan.\" Aria såg en bild av nyckeln i en röd kristall.', '[\r\n        {\"term\":\"Minnes-kristaller\", \"def\":\"Lyste i grottan\"},\r\n        {\"term\":\"Kael\", \"def\":\"Visste vad kristallerna var\"},\r\n        {\"term\":\"Röd kristall\", \"def\":\"Visade en bild av nyckeln\"},\r\n        {\"term\":\"Mörk\", \"def\":\"Hur grottan var innan kristallerna lyste\"}\r\n    ]', 7, 40, '2025-12-03 19:33:46'),
(117, 'Kapitel 5: Skuggornas Dal', 3, 1, 1, NULL, 'De lämnade grottan och kom till Skuggornas Dal. Här var allt grått och trist. Skuggvarelser smög i hörnen. Kael morrade för att skrämma dem. Aria använde sin uppfinning, en Ljuslykta, för att lysa upp vägen och hålla skuggorna borta.', '[\r\n        {\"term\":\"Skuggornas Dal\", \"def\":\"En plats där allt var grått\"},\r\n        {\"term\":\"Skuggvarelser\", \"def\":\"Smög i hörnen\"},\r\n        {\"term\":\"Ljuslykta\", \"def\":\"Arias uppfinning\"},\r\n        {\"term\":\"Morra\", \"def\":\"Det Kael gjorde för att skrämmas\"}\r\n    ]', 8, 50, '2025-12-03 19:33:46'),
(118, 'Kapitel 6: Det Gamla Biblioteket', 3, 1, 3, NULL, 'Mitt i dalen stod ett förfallet bibliotek. Aria hoppades hitta en ledtråd där. Böckerna flög runt av sig själva! En gammal bok landade framför henne. Den handlade om Tidens Väktare som gömde nyckeln i Molnslottet.', '[\r\n        {\"term\":\"Bibliotek\", \"def\":\"Byggnad full med böcker\"},\r\n        {\"term\":\"Flyga\", \"def\":\"Det böckerna gjorde\"},\r\n        {\"term\":\"Tidens Väktare\", \"def\":\"Den som gömde nyckeln\"},\r\n        {\"term\":\"Molnslottet\", \"def\":\"Platsen där nyckeln finns\"}\r\n    ]', 9, 60, '2025-12-03 19:33:46'),
(119, 'Kapitel 7: Uppfinnarverkstaden', 3, 1, 3, NULL, 'För att nå Molnslottet behövde de flyga. Aria byggde snabbt om sin ryggsäck till en jetmotor med hjälp av delar hon hittat. Kael tittade skeptiskt på henne. \"Lita på mig\", sa Aria och spände fast vargen i en sele.', '[\r\n        {\"term\":\"Jetmotor\", \"def\":\"Det Aria byggde\"},\r\n        {\"term\":\"Ryggsäck\", \"def\":\"Det hon byggde om\"},\r\n        {\"term\":\"Skeptisk\", \"def\":\"Så Kael tittade på henne\"},\r\n        {\"term\":\"Sele\", \"def\":\"Det Kael spändes fast i\"}\r\n    ]', 10, 70, '2025-12-03 19:33:46'),
(120, 'Kapitel 8: Molnslottet', 3, 1, 3, NULL, 'De flög högt upp bland molnen och landade på slottets borggård. Slottet var gjort av ren dimma och guld. Väktaren, en stor örn, satt på tronen. \"Endast den som kan namnge elementen får nyckeln\", kraxade örnen.', '[\r\n        {\"term\":\"Dimma och guld\", \"def\":\"Det slottet var gjort av\"},\r\n        {\"term\":\"Örn\", \"def\":\"Väktaren på tronen\"},\r\n        {\"term\":\"Borggård\", \"def\":\"Där de landade\"},\r\n        {\"term\":\"Elementen\", \"def\":\"Det Aria var tvungen att namnge\"}\r\n    ]', 11, 80, '2025-12-03 19:33:46'),
(121, 'Kapitel 9: Provet', 3, 1, 3, NULL, 'Örnen visade fyra symboler. Aria pekade på dem en efter en. \"Eld smälter is. Vatten släcker eld. Vind flyttar moln. Jord ger liv.\" Örnen nickade imponerat och räckte över Tidens Nyckel, som lyste med ett gyllene sken.', '[\r\n        {\"term\":\"Eld\", \"def\":\"Smälter is\"},\r\n        {\"term\":\"Vatten\", \"def\":\"Släcker eld\"},\r\n        {\"term\":\"Vind\", \"def\":\"Flyttar moln\"},\r\n        {\"term\":\"Jord\", \"def\":\"Ger liv\"},\r\n        {\"term\":\"Gyllene\", \"def\":\"Färgen på nyckelns sken\"}\r\n    ]', 12, 90, '2025-12-03 19:33:46'),
(122, 'Kapitel 10: Tiden Lagas', 3, 1, 3, NULL, 'Aria och Kael skyndade tillbaka till Klockstaden. Aria satte nyckeln i den Stora Klockan och vred om. Kugghjulen började snurra åt rätt håll igen! Folket jublade. Aria blev stadens hjältinna och Kael blev hennes trogna följeslagare.', '[\r\n        {\"term\":\"Klockstaden\", \"def\":\"Staden som räddades\"},\r\n        {\"term\":\"Kugghjul\", \"def\":\"Delar i klockan som snurrade\"},\r\n        {\"term\":\"Hjältinna\", \"def\":\"Det Aria blev kallad\"},\r\n        {\"term\":\"Följeslagare\", \"def\":\"Det Kael blev till Aria\"},\r\n        {\"term\":\"Jubla\", \"def\":\"Det folket gjorde\"}\r\n    ]', 13, 100, '2025-12-03 19:33:46'),
(123, 'Kapitel 1: Det Gamla Huset', 3, 4, 1, NULL, 'Alma och hennes pappa flyttade in i ett gammalt trähus på kullen. Det var långt till närmaste granne. Huset hade många fönster som såg ut som mörka ögon. Vinden ven runt knutarna och fick hela huset att knarra.', '[\r\n        {\"term\":\"Kråkslott\", \"def\":\"Ett gammalt och slitet hus\"},\r\n        {\"term\":\"Kulle\", \"def\":\"En liten höjd\"},\r\n        {\"term\":\"Granne\", \"def\":\"Den som bor bredvid\"},\r\n        {\"term\":\"Knarra\", \"def\":\"Ett ljud från gammalt trä\"}\r\n    ]', 4, 10, '2025-12-03 19:33:46');
INSERT INTO `tasks` (`t_id`, `t_name`, `t_type_fk`, `t_genre_fk`, `t_teacher_fk`, `t_class_fk`, `t_text`, `t_questions`, `t_level_fk`, `t_xp`, `t_created`) VALUES
(124, 'Kapitel 2: Den Låsta Dörren', 3, 4, 3, NULL, 'I källaren hittade Alma en dörr av järn. Den hade inget handtag, bara ett stort nyckelhål. När hon lade örat mot dörren tyckte hon sig höra någon som andades där inne. Det var kallt i källaren.', '[\r\n        {\"term\":\"Järn\", \"def\":\"En hård metall\"},\r\n        {\"term\":\"Nyckelhål\", \"def\":\"Där man sticker in nyckeln\"},\r\n        {\"term\":\"Andetag\", \"def\":\"Ljudet av luft\"},\r\n        {\"term\":\"Källare\", \"def\":\"Rummet under huset\"}\r\n    ]', 5, 20, '2025-12-03 19:33:46'),
(125, 'Kapitel 3: Katten Misse', 3, 4, 1, NULL, 'Almas katt Misse var modig, men han vägrade gå ner i källaren. Varje gång Alma öppnade källardörren reste han ragg och fräste rakt ut i mörkret. Djur ser saker som människor inte ser, tänkte Alma.', '[\r\n        {\"term\":\"Modig\", \"def\":\"Inte rädd\"},\r\n        {\"term\":\"Vägrade\", \"def\":\"Sa nej\"},\r\n        {\"term\":\"Ragg\", \"def\":\"När pälsen reser sig\"},\r\n        {\"term\":\"Fräsa\", \"def\":\"Ljudet en arg katt gör\"}\r\n    ]', 6, 30, '2025-12-03 19:33:46'),
(126, 'Kapitel 4: Fyndet', 3, 4, 3, NULL, 'En dag när Alma grävde i trädgården stötte spaden mot något hårt. Det var en liten ask av metall. Hon öppnade den försiktigt. Inuti låg en gammal, rostig nyckel med ett konstigt märke på.', '[\r\n        {\"term\":\"Spade\", \"def\":\"Redskap att gräva med\"},\r\n        {\"term\":\"Ask\", \"def\":\"En liten låda\"},\r\n        {\"term\":\"Rostig\", \"def\":\"Gammal metall som blivit brun\"},\r\n        {\"term\":\"Märke\", \"def\":\"En symbol eller bild\"}\r\n    ]', 7, 40, '2025-12-03 19:33:46'),
(127, 'Kapitel 5: Nyckeln Passar', 3, 4, 1, NULL, 'Alma smög ner i källaren med nyckeln. Hennes hand darrade när hon stack in den i låset. Det sa \"klick\". Hon drog upp den tunga dörren. En unken lukt av gamla böcker och damm slog emot henne.', '[\r\n        {\"term\":\"Darra\", \"def\":\"Skaka av rädsla\"},\r\n        {\"term\":\"Unken\", \"def\":\"Dålig lukt av gammal luft\"},\r\n        {\"term\":\"Damm\", \"def\":\"Grått pulver som samlas\"},\r\n        {\"term\":\"Lås\", \"def\":\"Håller dörren stängd\"}\r\n    ]', 8, 50, '2025-12-03 19:33:46'),
(128, 'Kapitel 6: Det Hemliga Rummet', 3, 4, 3, NULL, 'Rummet var litet och hade inga fönster. Mitt på golvet stod en stol. På väggen hängde en tavla av en pojke med sorgsna ögon. När Alma flyttade sin ficklampa verkade det som om pojkens ögon följde ljuset.', '[\r\n        {\"term\":\"Sorgsen\", \"def\":\"Ledsen\"},\r\n        {\"term\":\"Ficklampa\", \"def\":\"Ger ljus i mörkret\"},\r\n        {\"term\":\"Tavla\", \"def\":\"En målning på väggen\"},\r\n        {\"term\":\"Följa\", \"def\":\"Titta efter någon som rör sig\"}\r\n    ]', 9, 60, '2025-12-03 19:33:46'),
(129, 'Kapitel 7: Dagboken', 3, 4, 1, NULL, 'På stolen låg en dagbok. Alma började läsa. \"Jag är fast här inne\", stod det med spretig handstil. \"Skuggan i spegeln vill inte släppa ut mig. Den vill ha min plats.\" Alma rös av obehag.', '[\r\n        {\"term\":\"Dagbok\", \"def\":\"Bok där man skriver tankar\"},\r\n        {\"term\":\"Spretig\", \"def\":\"Slarvig stil\"},\r\n        {\"term\":\"Skugga\", \"def\":\"Mörkt område\"},\r\n        {\"term\":\"Obehag\", \"def\":\"En otäck känsla\"}\r\n    ]', 10, 70, '2025-12-03 19:33:46'),
(130, 'Kapitel 8: Spegeln', 3, 4, 3, NULL, 'I hörnet stod en stor spegel täckt av ett skynke. Alma drog bort tyget. Glaset var mörkt och immigt. Hon såg sin egen spegelbild, men bakom henne i spegeln stod en mörk figur och log elakt.', '[\r\n        {\"term\":\"Skynke\", \"def\":\"Ett stort tygstycke\"},\r\n        {\"term\":\"Immig\", \"def\":\"Täckt av fukt eller dimma\"},\r\n        {\"term\":\"Spegelbild\", \"def\":\"Det du ser i spegeln\"},\r\n        {\"term\":\"Elakt\", \"def\":\"Dumt och ont\"}\r\n    ]', 11, 80, '2025-12-03 19:33:46'),
(131, 'Kapitel 9: Skuggan Anfaller', 3, 4, 1, NULL, 'Skuggan i spegeln sträckte ut en hand genom glaset! Alma skrek till och backade. Hon letade efter något att försvara sig med. Hon grep tag i den tunga järnnyckeln och kastade den med all kraft mot spegeln.', '[\r\n        {\"term\":\"Glas\", \"def\":\"Det spegeln är gjord av\"},\r\n        {\"term\":\"Försvara\", \"def\":\"Skydda sig själv\"},\r\n        {\"term\":\"Gripa\", \"def\":\"Ta tag i något hårt\"},\r\n        {\"term\":\"Kraft\", \"def\":\"Styrka\"}\r\n    ]', 12, 90, '2025-12-03 19:33:46'),
(132, 'Kapitel 10: Friheten', 3, 4, 3, NULL, 'Spegeln krossades i tusen bitar. Ett ljust sken fyllde rummet och en varm vind svepte förbi. Alma hörde en viskning: \"Tack\". Pojken på tavlan log nu. Skuggan var borta och huset kändes inte längre skrämmande.', '[\r\n        {\"term\":\"Krossas\", \"def\":\"Gå sönder i småbitar\"},\r\n        {\"term\":\"Sken\", \"def\":\"Starkt ljus\"},\r\n        {\"term\":\"Viskning\", \"def\":\"Prata väldigt tyst\"},\r\n        {\"term\":\"Skrämmande\", \"def\":\"Otäckt och läskigt\"},\r\n        {\"term\":\"Frihet\", \"def\":\"Att inte vara fångad\"}\r\n    ]', 13, 100, '2025-12-03 19:33:46'),
(133, 'Kapitel 1: Det försvunna halsbandet', 3, 3, 1, NULL, 'Detektiv Holmes satt på tåget mot London. Plötsligt skrek en dam i kupén bredvid. \"Mitt diamanthalsband! Det är borta!\" Holmes reste sig genast. Han gick in till damen, Fru Rask. Hon pekade på en tom sammetsask på bordet.', '[\r\n        {\"term\":\"Detektiv\", \"def\":\"Löser brott\"},\r\n        {\"term\":\"Holmes\", \"def\":\"Detektivens namn\"},\r\n        {\"term\":\"London\", \"def\":\"Staden tåget åkte till\"},\r\n        {\"term\":\"Sammetsask\", \"def\":\"Där halsbandet låg\"}\r\n    ]', 4, 10, '2025-12-03 19:33:46'),
(134, 'Kapitel 2: De misstänkta', 3, 3, 3, NULL, 'Det fanns tre andra personer i vagnen. En ung man som läste en tidning, en äldre herre med käpp, och en kvinna med en stor hatt. Holmes bad konduktören att låsa dörrarna. Ingen fick lämna vagnen förrän halsbandet var funnet.', '[\r\n        {\"term\":\"Misstänkta\", \"def\":\"Personer som kan ha gjort brottet\"},\r\n        {\"term\":\"Konduktör\", \"def\":\"Jobbar på tåget\"},\r\n        {\"term\":\"Käpp\", \"def\":\"Hjälpmedel för att gå\"},\r\n        {\"term\":\"Vagn\", \"def\":\"Del av ett tåg\"}\r\n    ]', 5, 20, '2025-12-03 19:33:46'),
(135, 'Kapitel 3: Förhöret', 3, 3, 1, NULL, 'Holmes pratade med den unge mannen först. \"Jag har sovit hela tiden\", sa han. Men Holmes såg att han höll tidningen upp och ner. Det verkade misstänkt. Den äldre herren sa att han inte kunde gå bra, så han hade suttit stilla. Kvinnan med hatten verkade nervös.', '[\r\n        {\"term\":\"Förhör\", \"def\":\"När polisen ställer frågor\"},\r\n        {\"term\":\"Nervös\", \"def\":\"Orolig och rädd\"},\r\n        {\"term\":\"Misstänkt\", \"def\":\"Något som verkar fel\"},\r\n        {\"term\":\"Tidning\", \"def\":\"Mannen höll den upp och ner\"}\r\n    ]', 6, 30, '2025-12-03 19:33:46'),
(136, 'Kapitel 4: Ledtråden på golvet', 3, 3, 3, NULL, 'Holmes undersökte golvet med sitt förstoringsglas. Vid den äldre herrens fötter hittade han en liten bit röd tråd. Men sammetsasken var blå. Tråden måste komma från tjuven! Holmes tittade på de andras kläder.', '[\r\n        {\"term\":\"Ledtråd\", \"def\":\"Spår som hjälper att lösa gåtan\"},\r\n        {\"term\":\"Förstoringsglas\", \"def\":\"Gör saker större så man ser bättre\"},\r\n        {\"term\":\"Röd tråd\", \"def\":\"Hittades på golvet\"},\r\n        {\"term\":\"Blå\", \"def\":\"Färgen på asken\"}\r\n    ]', 7, 40, '2025-12-03 19:33:46'),
(137, 'Kapitel 5: Den röda halsduken', 3, 3, 1, NULL, 'Ingen av passagerarna hade röda kläder. Det var konstigt. Då såg Holmes att den unge mannens väska stack fram lite under sätet. En bit av en röd halsduk hängde ut. \"Får jag se din väska?\" frågade Holmes.', '[\r\n        {\"term\":\"Passagerare\", \"def\":\"De som åker med tåget\"},\r\n        {\"term\":\"Halsduk\", \"def\":\"Plagg runt halsen\"},\r\n        {\"term\":\"Säte\", \"def\":\"Plats man sitter på\"},\r\n        {\"term\":\"Väska\", \"def\":\"Där halsduken låg\"}\r\n    ]', 8, 50, '2025-12-03 19:33:46'),
(138, 'Kapitel 6: En oväntad vändning', 3, 3, 3, NULL, 'Mannen gav motvilligt väskan till Holmes. I den låg halsduken, men inget halsband. \"Jag stal halsduken\", erkände mannen, \"men inte diamanten!\" Holmes trodde honom. Tjuven var smartare än så. Han letade vidare.', '[\r\n        {\"term\":\"Erkänna\", \"def\":\"Berätta sanningen om ett brott\"},\r\n        {\"term\":\"Vändning\", \"def\":\"När något oväntat händer\"},\r\n        {\"term\":\"Smart\", \"def\":\"Klok och listig\"},\r\n        {\"term\":\"Motvilligt\", \"def\":\"När man inte vill göra något\"}\r\n    ]', 9, 60, '2025-12-03 19:33:46'),
(139, 'Kapitel 7: Käppen', 3, 3, 1, NULL, 'Holmes gick tillbaka till den äldre herren. Han tittade noga på hans käpp. Den såg ovanligt tjock ut. \"Får jag låna din käpp?\" frågade Holmes. Herren blev blek men räckte över den. Käppen kändes tung.', '[\r\n        {\"term\":\"Blek\", \"def\":\"När man tappar färg i ansiktet\"},\r\n        {\"term\":\"Tung\", \"def\":\"Väger mycket\"},\r\n        {\"term\":\"Ovanligt\", \"def\":\"Inte som det brukar vara\"},\r\n        {\"term\":\"Låna\", \"def\":\"Få ha något en stund\"}\r\n    ]', 10, 70, '2025-12-03 19:33:46'),
(140, 'Kapitel 8: Hemligheten', 3, 3, 3, NULL, 'Holmes skruvade på käppens handtag. Det gick att skruva av! Inuti käppen fanns ett hålrum. Holmes vände käppen upp och ner. Ut föll det gnistrande diamanthalsbandet. \"Aha!\" ropade Holmes.', '[\r\n        {\"term\":\"Hålrum\", \"def\":\"Tomt utrymme inuti något\"},\r\n        {\"term\":\"Skruva\", \"def\":\"Vrida runt\"},\r\n        {\"term\":\"Gnistrande\", \"def\":\"Skiner och blänker\"},\r\n        {\"term\":\"Handtag\", \"def\":\"Delen man håller i\"}\r\n    ]', 11, 80, '2025-12-03 19:33:46'),
(141, 'Kapitel 9: Tjuven avslöjad', 3, 3, 1, NULL, 'Den äldre herren var egentligen en känd juveltjuv vid namn \"Skuggan\". Han hade klätt ut sig för att lura polisen. Han hade stulit halsbandet när tåget åkte in i en tunnel och det blev mörkt. Fru Rask fick tillbaka sitt smycke.', '[\r\n        {\"term\":\"Avslöjad\", \"def\":\"När sanningen kommer fram\"},\r\n        {\"term\":\"Juveltjuv\", \"def\":\"Stjäl dyra smycken\"},\r\n        {\"term\":\"Tunnel\", \"def\":\"Väg genom berg eller under jord\"},\r\n        {\"term\":\"Klä ut sig\", \"def\":\"Ta på sig andra kläder för att luras\"}\r\n    ]', 12, 90, '2025-12-03 19:33:46'),
(142, 'Kapitel 10: Fallet löst', 3, 3, 3, NULL, 'När tåget kom fram till London väntade polisen på perrongen. Holmes överlämnade tjuven. \"Ett snyggt jobb\", sa konduktören. Holmes log och tände sin pipa. \"Det var elementärt\", sa han. Fallet var avslutat.', '[\r\n        {\"term\":\"Perrong\", \"def\":\"Där man väntar på tåget\"},\r\n        {\"term\":\"Överlämna\", \"def\":\"Ge till någon\"},\r\n        {\"term\":\"Elementärt\", \"def\":\"Enkelt och grundläggande\"},\r\n        {\"term\":\"Avslutat\", \"def\":\"Färdigt och klart\"}\r\n    ]', 13, 100, '2025-12-03 19:33:46'),
(143, 'Lärarlegitimation: Myndigheten', 3, 5, 1, NULL, 'För att arbeta som lärare i Sverige finns det många regler att hålla reda på. Det första steget är att veta vem som bestämmer. Det finns många myndigheter i Skolsverige, men en av dem har det yttersta ansvaret för att utfärda din legitimation.', '[{\"q\":\"Vilken myndighet ansvarar för att utfärda lärar- och förskollärarlegitimation i Sverige?\",\"a\":\"Skolverket\",\"w1\":\"Skolinspektionen\",\"w2\":\"Universitetskanslersämbetet\",\"w3\":\"Socialstyrelsen\"}]', 4, 10, '2025-12-03 19:33:46'),
(144, 'Lärarlegitimation: Grundkravet', 3, 5, 3, NULL, 'Drömmen om att bli lärare börjar ofta med en vilja att göra skillnad. Men för att få den eftertraktade legitimationen räcker det inte med god vilja eller erfarenhet. Lagen ställer ett specifikt krav på din utbildningsbakgrund.', '[{\"q\":\"Vilket är det grundläggande kravet för att kunna ansöka om lärarlegitimation?\",\"a\":\"En behörighetsgivande lärarexamen\",\"w1\":\"En gymnasieexamen\",\"w2\":\"Minst tre års arbetslivserfarenhet\",\"w3\":\"Ett godkännande från rektorn\"}]', 5, 20, '2025-12-03 19:33:46'),
(145, 'Lärarlegitimation: Introduktion', 3, 5, 1, NULL, 'När du väl står där med din examen i handen är du inte helt ensam. Första tiden i yrket kan vara tuff, och därför har systemet byggts upp för att ge dig stöd. Det kallas för en introduktionsperiod.', '[{\"q\":\"Vad innebär introduktionsperioden för en nyexaminerad lärare?\",\"a\":\"Ett år av mentorskap och stöd\",\"w1\":\"En provanställning utan undervisning\",\"w2\":\"En period där rektorn sätter betyg\",\"w3\":\"En obligatorisk kurs på universitetet\"}]', 6, 30, '2025-12-03 19:33:46'),
(146, 'Lärarlegitimation: Betygssättning', 3, 5, 3, NULL, 'Att sätta betyg är myndighetsutövning. Det är ett stort ansvar som påverkar elevens framtid. Därför är det strikt reglerat i Skollagen vem som faktiskt får skriva under i betygskatalogen.', '[{\"q\":\"Vem får sätta betyg självständigt i den svenska skolan?\",\"a\":\"Endast legitimerade lärare\",\"w1\":\"Alla anställda lärare\",\"w2\":\"Både legitimerade lärare och vikarier\",\"w3\":\"Rektorn sätter alla betyg\"}]', 7, 40, '2025-12-03 19:33:46'),
(147, 'Lärarlegitimation: Obehöriga', 3, 5, 1, NULL, 'Ibland uppstår situationer där en skola måste anställa en lärare som ännu inte fått sin legitimation. Men terminen tar slut och betyg måste sättas. Hur löser man den situationen på ett rättssäkert sätt?', '[{\"q\":\"Vad händer om en icke-legitimerad lärare ska sätta betyg?\",\"a\":\"Betyget sätts med en legitimerad lärare\",\"w1\":\"Eleven får inget betyg\",\"w2\":\"Rektorn sätter betyget ensam\",\"w3\":\"Läraren får dispens\"}]', 8, 50, '2025-12-03 19:33:46'),
(148, 'Lärarlegitimation: Fritidshem', 3, 5, 3, NULL, 'Fritidshemmet är en central del av elevernas dag. Det är inte bara \"passning\" utan en pedagogisk verksamhet med egen läroplan. Men ställs det samma krav på personalen här som i klassrummet?', '[{\"q\":\"Gäller kravet på legitimation även för lärare i fritidshem?\",\"a\":\"Ja, för fast anställning\",\"w1\":\"Nej, det är undantaget\",\"w2\":\"Endast vid estetiska ämnen\",\"w3\":\"Ja, utfärdas av kommunen\"}]', 9, 60, '2025-12-03 19:33:46'),
(149, 'Lärarlegitimation: Undantag', 3, 5, 1, NULL, 'Ingen regel utan undantag. I vissa specifika ämnen eller situationer har lagstiftaren bedömt att annan kompetens väger tyngre än den generella lärarlegitimationen. Vet du vilka dessa grupper är?', '[{\"q\":\"Vilken typ av lärare är generellt undantagna från legitimationskravet?\",\"a\":\"Modersmålslärare och yrkeslärare\",\"w1\":\"Gymnasielärare i engelska\",\"w2\":\"Matematiklärare på högstadiet\",\"w3\":\"Speciallärare\"}]', 10, 70, '2025-12-03 19:33:46'),
(150, 'Lärarlegitimation: Utlandet', 3, 5, 3, NULL, 'Världen krymper och många lärare har utbildat sig i andra länder. Men det svenska skolsystemet är unikt. Vad gäller för den som kommer med en utländsk lärarexamen och vill arbeta i Sverige?', '[{\"q\":\"Kan en person med utländsk examen få svensk legitimation?\",\"a\":\"Ja, efter prövning av Skolverket\",\"w1\":\"Nej, måste läsa om allt\",\"w2\":\"Ja, om man är från Norden\",\"w3\":\"Ja, gäller automatiskt i EU\"}]', 11, 80, '2025-12-03 19:33:46'),
(151, 'Lärarlegitimation: Behörighet', 3, 5, 1, NULL, 'På ditt legitimationsbevis står det exakt vilka ämnen och årskurser du är behörig i. Det är inte rektorn som bestämmer detta, och inte heller du själv. Det baseras på kalla fakta.', '[{\"q\":\"Vad avgör vilka ämnen en lärare blir behörig i?\",\"a\":\"Innehållet i examen och studier\",\"w1\":\"Rektorns beslut\",\"w2\":\"Lärarens egen bedömning\",\"w3\":\"Antal år i yrket\"}]', 12, 90, '2025-12-03 19:33:46'),
(152, 'Lärarlegitimation: Anställning', 3, 5, 3, NULL, 'Trygghet i arbetslivet är viktigt. En tillsvidareanställning (fast tjänst) är målet för de flesta. Skollagen sätter en tydlig gräns för vem som får ges denna trygghet för att säkra kvaliteten i skolan.', '[{\"q\":\"Vad krävs för att få en tillsvidareanställning som lärare?\",\"a\":\"Att man har lärarlegitimation\",\"w1\":\"Två års vikariat\",\"w2\":\"Pågående utbildning\",\"w3\":\"Lärarbrist i kommunen\"}]', 13, 100, '2025-12-03 19:33:46'),
(153, 'Kapitel 1: Alarmet', 3, 2, 3, NULL, 'Kapten Nova vaknade av att larmet tjöt på rymdstationen Omega. Röda lampor blinkade överallt. Hon svävade snabbt ur sin säng. Datorn varnade för en luftläcka i sektor 7. Nova måste skynda sig att täta hålet innan luften tog slut.', '[{\"term\":\"Nova\", \"def\":\"Kapten på stationen\"},{\"term\":\"Omega\", \"def\":\"Rymdstationens namn\"},{\"term\":\"Sektor 7\", \"def\":\"Där läckan fanns\"},{\"term\":\"Sväva\", \"def\":\"Röra sig utan tyngdkraft\"}]', 4, 10, '2025-12-03 19:33:46'),
(154, 'Kapitel 2: Rymddräkten', 3, 2, 3, NULL, 'Nova tog sig till luftslussen. Hon tog på sig sin vita rymddräkt. Den var tung och klumpig, men den skulle skydda henne mot kylan och vakuumet utanför. Hon kopplade fast sin syretank och fällde ner visiret på hjälmen.', '[{\"term\":\"Luftsluss\", \"def\":\"Dörr ut till rymden\"},{\"term\":\"Rymddräkt\", \"def\":\"Skyddskläder\"},{\"term\":\"Vakuum\", \"def\":\"Tomrummet i rymden\"},{\"term\":\"Syretank\", \"def\":\"Ger luft att andas\"}]', 5, 20, '2025-12-03 19:33:46'),
(155, 'Kapitel 3: Promenaden', 3, 2, 3, NULL, 'Hon öppnade dörren och klev ut i mörkret. Stjärnorna lyste klarare än någonsin. Under henne snurrade den blå planeten Jorden. Nova använde magneter i sina stövlar för att gå på stationens utsida utan att flyga iväg.', '[{\"term\":\"Stjärnor\", \"def\":\"Lyste i mörkret\"},{\"term\":\"Jorden\", \"def\":\"Den blå planeten\"},{\"term\":\"Magneter\", \"def\":\"Höll fast stövlarna\"},{\"term\":\"Utsida\", \"def\":\"Där Nova gick\"}]', 6, 30, '2025-12-03 19:33:46'),
(156, 'Kapitel 4: Skadan', 3, 2, 3, NULL, 'Hon hittade hålet i väggen. Det såg ut som om en liten sten hade träffat stationen med enorm fart. Luften pysye ut som vit rök. Nova tog fram sitt lagningskit. Hon hade en speciell pasta som stelnade direkt i kylan.', '[{\"term\":\"Meteorit\", \"def\":\"Liten sten från rymden\"},{\"term\":\"Pysa\", \"def\":\"Ljudet av luft som läcker\"},{\"term\":\"Lagningskit\", \"def\":\"Verktyg för att laga\"},{\"term\":\"Pasta\", \"def\":\"Tätningsmedel\"}]', 7, 40, '2025-12-03 19:33:46'),
(157, 'Kapitel 5: Roboten Bip', 3, 2, 3, NULL, 'Plötsligt kom den lilla roboten Bip svävande. Bip hjälpte till med reparationer. \"Behöver du hjälp, Kapten?\" pep Bip. Nova nickade. \"Håll lampan åt mig, Bip\", sa hon. Bip lyste med sitt öga så Nova kunde se bättre.', '[{\"term\":\"Bip\", \"def\":\"En hjälpsam robot\"},{\"term\":\"Reparation\", \"def\":\"Att laga något\"},{\"term\":\"Pep\", \"def\":\"Ljudet roboten gjorde\"},{\"term\":\"Lampa\", \"def\":\"Det Bip lyste med\"}]', 8, 50, '2025-12-03 19:33:46'),
(158, 'Kapitel 6: Solstormen', 3, 2, 3, NULL, 'Precis när hålet var lagat varnade datorn igen. \"Varning! Solstorm på väg!\" En solstorm är en våg av farlig strålning från solen. Nova och Bip var tvungna att ta skydd snabbt. De kunde inte vara kvar ute.', '[{\"term\":\"Solstorm\", \"def\":\"Utbrott på solen\"},{\"term\":\"Strålning\", \"def\":\"Farlig energi\"},{\"term\":\"Skydd\", \"def\":\"Säker plats\"},{\"term\":\"Datorn\", \"def\":\"Varnade för faran\"}]', 9, 60, '2025-12-03 19:33:46'),
(159, 'Kapitel 7: Låst dörr', 3, 2, 3, NULL, 'De skyndade tillbaka till slussen, men panelen var trasig. Dörren öppnades inte! Strålningen kom närmare. Nova måste hacka det elektroniska låset. Hon öppnade en lucka och kopplade om några färgglada sladdar.', '[{\"term\":\"Panel\", \"def\":\"Där man styr dörren\"},{\"term\":\"Hacka\", \"def\":\"Ta sig förbi ett datorsystem\"},{\"term\":\"Elektronisk\", \"def\":\"Drivs med el\"},{\"term\":\"Sladdar\", \"def\":\"Leder ström\"}]', 10, 70, '2025-12-03 19:33:46'),
(160, 'Kapitel 8: Inne igen', 3, 2, 3, NULL, 'Med ett pysande ljud öppnades dörren precis i tid. Nova och Bip tumlade in i säkerhet. De stängde den tjocka metalldörren bakom sig. Utanför lyste rymden upp av solstormens osynliga vågor.', '[{\"term\":\"Säkerhet\", \"def\":\"Där ingen fara finns\"},{\"term\":\"Tumla\", \"def\":\"Ramla runt\"},{\"term\":\"Metall\", \"def\":\"Materialet i dörren\"},{\"term\":\"Osynlig\", \"def\":\"Går inte att se\"}]', 11, 80, '2025-12-03 19:33:46'),
(161, 'Kapitel 9: Rapporten', 3, 2, 3, NULL, 'Nova tog av sig hjälmen och pustade ut. Hon svävade till kommandobryggan för att ringa Jorden. \"Uppdraget slutfört\", sa hon i mikrofonen. \"Stationen är hel och vi är säkra.\" En röst från Jorden svarade: \"Bra jobbat, Omega.\"', '[{\"term\":\"Pusta ut\", \"def\":\"Andas ut av lättnad\"},{\"term\":\"Kommandobrygga\", \"def\":\"Rummet man styr ifrån\"},{\"term\":\"Mikrofon\", \"def\":\"Man pratar i den\"},{\"term\":\"Rapport\", \"def\":\"Meddelande om vad som hänt\"}]', 12, 90, '2025-12-03 19:33:46'),
(162, 'Kapitel 10: Hemlängtan', 3, 2, 3, NULL, 'Nova tittade ut genom fönstret igen. Solstormen hade skapat ett vackert norrsken runt Jorden. Hon längtade hem till gräset och havet, men hon visste att hennes jobb i rymden var viktigt. Bip pep glatt och gav henne en påse torkad rymdglass.', '[{\"term\":\"Norrsken\", \"def\":\"Ljusfenomen på himlen\"},{\"term\":\"Hemlängtan\", \"def\":\"Saknad efter hemmet\"},{\"term\":\"Viktigt\", \"def\":\"Något som betyder mycket\"},{\"term\":\"Rymdglass\", \"def\":\"Mat för astronauter\"}]', 13, 100, '2025-12-03 19:33:46'),
(163, 'Kapitel 1: Stölden', 3, 3, 1, NULL, 'Det var kaos på museet. Den kända diamanten \"Silverstjärnan\" var borta! Glasmontern var krossad. Detektiven Leo kom dit med sin hund, Snuffe. Leo tog fram sitt anteckningsblock. \"Ingen lämnar rummet!\" ropade han.', '[{\"term\":\"Kaos\", \"def\":\"Oordning och rörigt\"},{\"term\":\"Silverstjärnan\", \"def\":\"Namnet på diamanten\"},{\"term\":\"Monter\", \"def\":\"Låda av glas\"},{\"term\":\"Snuffe\", \"def\":\"Leos hund\"}]', 4, 10, '2025-12-03 19:33:46'),
(164, 'Kapitel 2: Spåren', 3, 3, 1, NULL, 'Leo undersökte golvet. Han såg glasbitar och leriga fotspår. Fotspåren ledde från fönstret fram till montern. \"Tjuven kom in utifrån\", sa Leo. Snuffe nosade på leran och nös.', '[{\"term\":\"Undersöka\", \"def\":\"Titta noga på något\"},{\"term\":\"Skärvor\", \"def\":\"Bitar av trasigt glas\"},{\"term\":\"Lera\", \"def\":\"Blöt jord\"},{\"term\":\"Fönster\", \"def\":\"Där tjuven kom in\"}]', 5, 20, '2025-12-03 19:33:46'),
(165, 'Kapitel 3: Vittnet', 3, 3, 1, NULL, 'Nattvakten Sune satt på en stol och såg rädd ut. \"Jag hörde en smäll\", sa han. \"Sen såg jag en skugga smita ut.\" Leo skrev upp allt Sune sa. Var Sune verkligen så oskyldig som han såg ut?', '[{\"term\":\"Nattvakt\", \"def\":\"Jobbar med att vakta på natten\"},{\"term\":\"Smäll\", \"def\":\"Ett högt ljud\"},{\"term\":\"Skugga\", \"def\":\"Mörk form av en person\"},{\"term\":\"Oskyldig\", \"def\":\"Har inte gjort något fel\"}]', 6, 30, '2025-12-03 19:33:46'),
(166, 'Kapitel 4: Handsken', 3, 3, 1, NULL, 'Snuffe skällde vid en buske utanför fönstret. Leo gick dit. I busken hängde en svart handske. Den måste ha fastnat när tjuven flydde. \"Bra jobbat, Snuffe!\" sa Leo och lade handsken i en påse.', '[{\"term\":\"Buske\", \"def\":\"Liten växt\"},{\"term\":\"Handske\", \"def\":\"Klädesplagg för handen\"},{\"term\":\"Flydde\", \"def\":\"Sprang därifrån\"},{\"term\":\"Påse\", \"def\":\"Där Leo la beviset\"}]', 7, 40, '2025-12-03 19:33:46'),
(167, 'Kapitel 5: Laboratoriet', 3, 3, 1, NULL, 'Leo tog handsken till sitt labb. Han använde ett mikroskop för att titta på den. Han hittade ett rött hårstrå på tyget. \"Intressant\", mumlade Leo. \"Tjuven har rött hår, eller kanske en röd katt?\"', '[{\"term\":\"Labb\", \"def\":\"Plats för experiment\"},{\"term\":\"Mikroskop\", \"def\":\"Gör små saker jättestora\"},{\"term\":\"Hårstrå\", \"def\":\"Växer på huvudet\"},{\"term\":\"Tyg\", \"def\":\"Det handsken var gjord av\"}]', 8, 50, '2025-12-03 19:33:46'),
(168, 'Kapitel 6: Biblioteket', 3, 3, 1, NULL, 'Leo gick till biblioteket för att läsa om kända tjuvar. Han hittade en bild på \"Röda Räven\", en tjuv som alltid bar svarta handskar. Och han hade rött hår! Leo visste nu vem han jagade.', '[{\"term\":\"Bibliotek\", \"def\":\"Hus med böcker\"},{\"term\":\"Röda Räven\", \"def\":\"Namnet på tjuven\"},{\"term\":\"Känd\", \"def\":\"Många vet vem det är\"},{\"term\":\"Jaga\", \"def\":\"Försöka fånga någon\"}]', 9, 60, '2025-12-03 19:33:46'),
(169, 'Kapitel 7: Bageriet', 3, 3, 1, NULL, 'Någon hade sett Röda Räven köpa munkar på bageriet. Leo och Snuffe skyndade dit. Bagaren sa: \"Ja, han var här nyss. Han tappade den här lappen.\" På lappen stod en tid och en plats: \"Hamnen kl 12\".', '[{\"term\":\"Bageri\", \"def\":\"Där man bakar bröd\"},{\"term\":\"Munk\", \"def\":\"En sorts kaka\"},{\"term\":\"Lapp\", \"def\":\"Papper med text\"},{\"term\":\"Hamn\", \"def\":\"Där båtar stannar\"}]', 10, 70, '2025-12-03 19:33:46'),
(170, 'Kapitel 8: Fällan', 3, 3, 1, NULL, 'Leo smög ner till hamnen. Han gömde sig bakom en låda. Klockan slog tolv. En båt kom in mot kajen. En man med rött hår kom gående. Det var Röda Räven! Han höll hårt i en väska.', '[{\"term\":\"Smyga\", \"def\":\"Gå tyst\"},{\"term\":\"Gömställe\", \"def\":\"Där man inte syns\"},{\"term\":\"Kaj\", \"def\":\"Kanten mot vattnet\"},{\"term\":\"Väska\", \"def\":\"Där stöldgodset låg\"}]', 11, 80, '2025-12-03 19:33:46'),
(171, 'Kapitel 9: Gripandet', 3, 3, 1, NULL, 'Precis när Räven skulle hoppa på båten, hoppade Snuffe fram och bet honom i byxbenet. Leo sprang fram. \"Du är arresterad!\" ropade han. Polisen kom och satte handklovar på tjuven.', '[{\"term\":\"Hoppa\", \"def\":\"Skutta med benen\"},{\"term\":\"Byxben\", \"def\":\"Del av byxan\"},{\"term\":\"Arresterad\", \"def\":\"Tagen av polisen\"},{\"term\":\"Handklovar\", \"def\":\"Låser fast händerna\"}]', 12, 90, '2025-12-03 19:33:46'),
(172, 'Kapitel 10: Belöningen', 3, 3, 1, NULL, 'Museet fick tillbaka sin diamant. Leo och Snuffe fick en medalj av borgmästaren. Snuffe fick också ett stort ben. \"Ännu ett fall löst\", sa Leo och klappade sin duktiga hund.', '[{\"term\":\"Belöning\", \"def\":\"Pris för bra jobb\"},{\"term\":\"Medalj\", \"def\":\"Pris av metall\"},{\"term\":\"Borgmästare\", \"def\":\"Bestämmer i staden\"},{\"term\":\"Fall\", \"def\":\"Ett mysterium\"} ]', 13, 100, '2025-12-03 19:33:46'),
(173, 'Kapitel 1: Vågar du?', 3, 4, 3, NULL, 'Det gamla huset på kullen stod tomt. Fönstren var trasiga och trädgården var vild. \"Ingen vågar gå in där\", sa Max. \"Jag vågar visst\", sa Elias. De slog vad om en påse godis. Elias gick långsamt mot grinden.', '[{\"term\":\"Ödehus\", \"def\":\"Hus där ingen bor\"},{\"term\":\"Vild\", \"def\":\"Växer okontrollerat\"},{\"term\":\"Våga\", \"def\":\"Inte vara feg\"},{\"term\":\"Grind\", \"def\":\"Dörr i ett staket\"}]', 4, 10, '2025-12-03 19:33:46'),
(174, 'Kapitel 2: Hallen', 3, 4, 3, NULL, 'Dörren knarrade när Elias öppnade den. Inne i hallen var det mörkt och dammigt. Spindelväv hängde från taket. Plötsligt slog dörren igen bakom honom med en smäll! Elias ryckte i handtaget, men det var låst.', '[{\"term\":\"Knarra\", \"def\":\"Låta som gammalt trä\"},{\"term\":\"Dammigt\", \"def\":\"Smutsigt av grått pulver\"},{\"term\":\"Spindelväv\", \"def\":\"Nät gjort av spindlar\"},{\"term\":\"Låst\", \"def\":\"Går inte att öppna\"}]', 5, 20, '2025-12-03 19:33:46'),
(175, 'Kapitel 3: Fotstegen', 3, 4, 3, NULL, 'Elias var ensam i mörkret. Då hörde han det. Dunk, dunk, dunk. Tunga steg på övervåningen. Men huset skulle ju vara tomt? Han höll andan och lyssnade. Stegen kom närmare trappan.', '[{\"term\":\"Ensam\", \"def\":\"Ingen annan är där\"},{\"term\":\"Dunk\", \"def\":\"Ett tungt ljud\"},{\"term\":\"Övervåning\", \"def\":\"Våningen ovanför\"},{\"term\":\"Trappa\", \"def\":\"Väg mellan våningar\"}]', 6, 30, '2025-12-03 19:33:46'),
(176, 'Kapitel 4: Tavlan', 3, 4, 3, NULL, 'Han backade in i vardagsrummet. En blixt lyste upp rummet. På väggen hängde en tavla av en gammal tant. Hennes ögon såg elaka ut. När Elias flyttade sig, tyckte han att tavlans ögon följde efter honom.', '[{\"term\":\"Blixt\", \"def\":\"Ljus från åskväder\"},{\"term\":\"Tant\", \"def\":\"Gammal kvinna\"},{\"term\":\"Elak\", \"def\":\"Inte snäll\"},{\"term\":\"Följa\", \"def\":\"Titta på någon som går\"}]', 7, 40, '2025-12-03 19:33:46'),
(177, 'Kapitel 5: Rösten', 3, 4, 3, NULL, 'En kall vind svepte genom rummet, trots att alla fönster var stängda. Elias rös. Sen hörde han en viskning precis vid örat. \"Gå ut...\" viskade rösten. Elias snurrade runt, men ingen var där.', '[{\"term\":\"Vind\", \"def\":\"Luft som rör sig\"},{\"term\":\"Rysa\", \"def\":\"Skaka av kyla eller rädsla\"},{\"term\":\"Viskning\", \"def\":\"Prata väldigt tyst\"},{\"term\":\"Osynlig\", \"def\":\"Går inte att se\"}]', 8, 50, '2025-12-03 19:33:46'),
(178, 'Kapitel 6: Köket', 3, 4, 3, NULL, 'Han sprang in i köket för att gömma sig. På bordet stod en tallrik med ruttna äpplen. En stol drogs ut från bordet med ett skrapande ljud, som om någon osynlig satte sig ner. Elias hjärta bankade hårt.', '[{\"term\":\"Gömma sig\", \"def\":\"Se till att inte synas\"},{\"term\":\"Rutten\", \"def\":\"Gammal och äcklig mat\"},{\"term\":\"Skrapa\", \"def\":\"Ljud mot golvet\"},{\"term\":\"Banka\", \"def\":\"Slå hårt och fort\"}]', 9, 60, '2025-12-03 19:33:46'),
(179, 'Kapitel 7: Källaren', 3, 4, 3, NULL, 'Elias hittade en dörr och öppnade den. Det var trappan ner till källaren. Där nere lyste ett grönt, spöklikt sken. Han ville inte gå ner, men trappan uppåt var blockerad av gamla möbler.', '[{\"term\":\"Källare\", \"def\":\"Rum under marken\"},{\"term\":\"Sken\", \"def\":\"Svagt ljus\"},{\"term\":\"Spöklikt\", \"def\":\"Otäckt och onaturligt\"},{\"term\":\"Blockerad\", \"def\":\"Vägen är stängd\"}]', 10, 70, '2025-12-03 19:33:46'),
(180, 'Kapitel 8: Spöket', 3, 4, 3, NULL, 'I källaren svävade ett lakan i luften. Det var ett spöke! \"Varför är du här?\" kved spöket sorgset. \"Jag vill bara ha min nalle.\" Elias såg en smutsig nallebjörn i hörnet. Han plockade modigt upp den.', '[{\"term\":\"Lakan\", \"def\":\"Tyg man har i sängen\"},{\"term\":\"Kvida\", \"def\":\"Gråta tyst\"},{\"term\":\"Sorgsen\", \"def\":\"Mycket ledsen\"},{\"term\":\"Modigt\", \"def\":\"Utan att vara rädd\"}]', 11, 80, '2025-12-03 19:33:46'),
(181, 'Kapitel 9: Hjälpen', 3, 4, 3, NULL, 'Elias gav nallen till spöket. Spöket kramade den och slutade lysa grönt. \"Tack\", sa spöket och log. \"Nu kan jag vila.\" Med en suck försvann spöket och blev till glittrande damm. Ytterdörren klickade upp där uppe.', '[{\"term\":\"Krama\", \"def\":\"Hålla om någon\"},{\"term\":\"Vila\", \"def\":\"Sova och ta det lugnt\"},{\"term\":\"Suck\", \"def\":\"Ljud när man andas ut\"},{\"term\":\"Försvinna\", \"def\":\"Inte synas längre\"}]', 12, 90, '2025-12-03 19:33:46'),
(182, 'Kapitel 10: Modigast', 3, 4, 3, NULL, 'Elias sprang ut ur huset. Solen hade börjat gå upp. Max stod kvar vid grinden. \"Du var där inne hela natten!\" sa Max. \"Du är modigast i världen.\" Elias log och åt upp godiset han vunnit.', '[{\"term\":\"Soluppgång\", \"def\":\"När dagen börjar\"},{\"term\":\"Vänta\", \"def\":\"Stå kvar tills någon kommer\"},{\"term\":\"Vunnit\", \"def\":\"Fått pris i en tävling\"},{\"term\":\"Godis\", \"def\":\"Sötsaker man äter\"}]', 13, 100, '2025-12-03 19:33:46'),
(183, 'Kapitel 1: Skåne', 3, 5, 1, NULL, 'Vi börjar vår resa längst ner i söder, i landskapet Skåne. Här är marken platt och bra för odling. Skåne är känt för sina gula rapsfält och den gamla byggnaden Turning Torso i Malmö.', '[{\"term\":\"Söder\", \"def\":\"Väderstreck neråt på kartan\"},{\"term\":\"Platt\", \"def\":\"Utan höga berg\"},{\"term\":\"Raps\", \"def\":\"Gul växt man gör olja av\"},{\"term\":\"Turning Torso\", \"def\":\"Högt hus i Malmö\"}]', 4, 10, '2025-12-03 19:33:46'),
(184, 'Kapitel 2: Småland', 3, 5, 1, NULL, 'Norr om Skåne ligger Småland. Här finns det mycket skog och många sjöar. Det är här Astrid Lindgren föddes. Småland är också känt för sin glastillverkning, \"Glasriket\".', '[{\"term\":\"Skog\", \"def\":\"Plats med många träd\"},{\"term\":\"Astrid Lindgren\", \"def\":\"Känd författare\"},{\"term\":\"Glasriket\", \"def\":\"Där man gör glas\"},{\"term\":\"Sjö\", \"def\":\"Stort vatten i naturen\"}]', 5, 20, '2025-12-03 19:33:46'),
(185, 'Kapitel 3: Västkusten', 3, 5, 1, NULL, 'Vi åker västerut till Göteborg och Bohuslän. Här finns saltvatten, klippor och maneter. Många fiskar efter räkor och kräftor här. Göteborg är Sveriges näst största stad.', '[{\"term\":\"Västerut\", \"def\":\"Mot vänster på kartan\"},{\"term\":\"Klippor\", \"def\":\"Hårda stenar vid havet\"},{\"term\":\"Saltvatten\", \"def\":\"Vatten i havet\"},{\"term\":\"Göteborg\", \"def\":\"Stor stad på västkusten\"}]', 6, 30, '2025-12-03 19:33:46'),
(186, 'Kapitel 4: Stockholm', 3, 5, 1, NULL, 'Nu åker vi till huvudstaden Stockholm på ostkusten. Staden är byggd på öar. Här finns Kungliga Slottet där kungen bor ibland. Gamla Stan har smala gränder och gamla hus.', '[{\"term\":\"Huvudstad\", \"def\":\"Landets viktigaste stad\"},{\"term\":\"Ö\", \"def\":\"Land med vatten runt om\"},{\"term\":\"Slott\", \"def\":\"Stort hus för kungligheter\"},{\"term\":\"Gränd\", \"def\":\"Smal gata\"}]', 7, 40, '2025-12-03 19:33:46'),
(187, 'Kapitel 5: Dalarna', 3, 5, 1, NULL, 'Mitt i Sverige ligger Dalarna. Här målar man dalahästar i rött med fina mönster. Varje år åker många Vasaloppet på skidor här. Naturen har många berg och dalar.', '[{\"term\":\"Dalahäst\", \"def\":\"Trähäst målad i rött\"},{\"term\":\"Vasaloppet\", \"def\":\"Lång tävling på skidor\"},{\"term\":\"Dal\", \"def\":\"Lågt område mellan berg\"},{\"term\":\"Mönster\", \"def\":\"Upprepade figurer och färger\"}]', 8, 50, '2025-12-03 19:33:46'),
(188, 'Kapitel 6: Norrland', 3, 5, 1, NULL, 'Norrland är den största delen av Sverige. Här finns höga berg som kallas fjäll. På vintern är det mörkt nästan hela dagen, men man kan se norrsken på himlen. Här rinner stora älvar.', '[{\"term\":\"Fjäll\", \"def\":\"Höga berg utan träd på toppen\"},{\"term\":\"Vinter\", \"def\":\"Den kallaste årstiden\"},{\"term\":\"Norrsken\", \"def\":\"Ljus på himlen\"},{\"term\":\"Älv\", \"def\":\"Stor flod\"}]', 9, 60, '2025-12-03 19:33:46'),
(189, 'Kapitel 7: Samerna', 3, 5, 1, NULL, 'Samerna är Sveriges ursprungsbefolkning. De har bott i norr mycket länge. Många samer arbetar med renar. De har en egen flagga och egna språk. Deras traditionella dräkt kallas kolt.', '[{\"term\":\"Ursprungsbefolkning\", \"def\":\"De som bodde där först\"},{\"term\":\"Ren\", \"def\":\"Djur med horn\"},{\"term\":\"Kolt\", \"def\":\"Samisk klädedräkt\"},{\"term\":\"Flagga\", \"def\":\"Tyg som symboliserar ett folk\"}]', 10, 70, '2025-12-03 19:33:46'),
(190, 'Kapitel 8: Gruvorna', 3, 5, 1, NULL, 'I norra Sverige, i staden Kiruna, finns världens största underjordiska järnmalmsgruva. Man gräver djupt ner i marken för att hitta järn. Det är så stort att man måste flytta hela staden för att inte husen ska rasa ner.', '[{\"term\":\"Kiruna\", \"def\":\"Stad i norr\"},{\"term\":\"Gruva\", \"def\":\"Där man gräver upp metall\"},{\"term\":\"Järnmalm\", \"def\":\"Sten som innehåller järn\"},{\"term\":\"Underjordisk\", \"def\":\"Under marken\"}]', 11, 80, '2025-12-03 19:33:46'),
(191, 'Kapitel 9: Kebnekaise', 3, 5, 1, NULL, 'Sveriges högsta berg heter Kebnekaise. Det ligger långt upp i norr. Toppen är täckt av en glaciär, som är tjock is. Många vandrare vill klättra upp till toppen för att se utsikten.', '[{\"term\":\"Kebnekaise\", \"def\":\"Sveriges högsta berg\"},{\"term\":\"Topp\", \"def\":\"Den högsta punkten\"},{\"term\":\"Glaciär\", \"def\":\"Is som aldrig smälter\"},{\"term\":\"Utsikt\", \"def\":\"Det man ser från höjden\"}]', 12, 90, '2025-12-03 19:33:46'),
(192, 'Kapitel 10: Sammanfattning', 3, 5, 1, NULL, 'Sverige är ett långt land med mycket olika natur. Från Skånes platta åkrar i söder till Lapplands höga fjäll i norr. Vi har kuster, skogar, sjöar och städer. Alla delar behövs för att göra Sverige till det land det är.', '[{\"term\":\"Långt\", \"def\":\"Formen på landet Sverige\"},{\"term\":\"Natur\", \"def\":\"Skog, berg och vatten\"},{\"term\":\"Åker\", \"def\":\"Där bonden odlar mat\"},{\"term\":\"Kust\", \"def\":\"Där landet möter havet\"}]', 13, 100, '2025-12-03 19:33:46'),
(193, 'Kapitel 1: Signalen', 4, 2, 1, NULL, 'Astronauten Kim satt ensam på sin rymdstation och åt frukost. Plötsligt började en röd lampa blinka på kontrollpanelen. Det pep högt. Kim tittade på skärmen. Det var en nödsignal från Mars! Någon behövde hjälp.', '[\r\n        {\"q\":\"Kim var på en ubåt.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"En röd lampa började blinka.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Signalen kom från Månen.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Någon behövde hjälp på Mars.\",\"a\":\"Sant\"}\r\n    ]', 4, 10, '2025-12-03 19:33:46'),
(194, 'Kapitel 2: Bip vaknar', 4, 2, 3, NULL, 'Kim sprang till robotverkstaden. Där inne sov hennes lilla robot, Bip, i sin laddare. \"Vakna Bip!\" ropade Kim. Bip tände sina blå ögon och pep glatt. Kim berättade om signalen. Bip rullade genast mot raketen.', '[\r\n        {\"q\":\"Bip är en stor hund.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Bip sov i en laddare.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Robotens ögon lyste rött.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"De skulle åka med raketen.\",\"a\":\"Sant\"}\r\n    ]', 5, 20, '2025-12-03 19:33:46'),
(195, 'Kapitel 3: Starten', 4, 2, 1, NULL, 'Kim spände fast sig i sätet. Bip kopplade in sig i datorn. \"3... 2... 1... Start!\" Motorn dånade och hela stationen skakade. Raketen sköt fart ut i den mörka rymden. Stjärnorna såg ut som utdragna streck när de åkte i super-fart.', '[\r\n        {\"q\":\"Kim stod upp när de startade.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Motorn var tyst.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"De åkte ut i rymden.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Stjärnorna såg ut som streck.\",\"a\":\"Sant\"}\r\n    ]', 6, 30, '2025-12-03 19:33:46'),
(196, 'Kapitel 4: Asteroidbältet', 4, 2, 3, NULL, 'Vägen till Mars var farlig. De var tvungna att åka igenom ett bälte av asteroider. Stora stenar flög mot dem. Kim styrde raketen upp och ner för att undvika krockar. En liten sten träffade vingen med ett \"KLONK\", men de klarade sig.', '[\r\n        {\"q\":\"Vägen till Mars var säker och lugn.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Asteroider är stora stenar i rymden.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Kim blundade när hon styrde.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Raketen blev träffad av en liten sten.\",\"a\":\"Sant\"}\r\n    ]', 7, 40, '2025-12-03 19:33:46'),
(197, 'Kapitel 5: Landningen', 4, 2, 1, NULL, 'Den röda planeten blev större och större i fönstret. Det var dags att landa. \"Fäll ut landningsställen,\" sa Kim. Bip pep och tryckte på en knapp. Raketen landade mjukt i den röda sanden. De var framme.', '[\r\n        {\"q\":\"Planeten var blå.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Kim fällde ut landningsställen själv.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Raketen landade i sanden.\",\"a\":\"Sant\"},\r\n        {\"q\":\"De kraschade hårt.\",\"a\":\"Falskt\"}\r\n    ]', 8, 50, '2025-12-03 19:33:46'),
(198, 'Kapitel 6: Spåret', 4, 2, 3, NULL, 'Kim tog på sig sin rymddräkt och klev ut. Det var kallt och blåsigt. Bip skannade marken med en grön laser. \"Spår hittade!\" sa roboten. De såg märkliga fotspår i sanden. De såg inte ut som människofötter.', '[\r\n        {\"q\":\"Kim gick ut utan rymddräkt.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Det var varmt på Mars.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Bip använde en grön laser.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Fotspåren såg ut som människors.\",\"a\":\"Falskt\"}\r\n    ]', 9, 60, '2025-12-03 19:33:46'),
(199, 'Kapitel 7: Grottan', 4, 2, 1, NULL, 'Spåren ledde till en mörk grotta. Kim tände sin ficklampa. Där inne satt en liten, grön varelse och grät. Dess rymdskepp låg trasigt bredvid. \"Var inte rädd,\" sa Kim lugnt. \"Vi är här för att hjälpa dig.\"', '[\r\n        {\"q\":\"Grottan var ljus och solig.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Varelsen var grön.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Varelsen skrattade.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Kim ville hjälpa varelsen.\",\"a\":\"Sant\"}\r\n    ]', 10, 70, '2025-12-03 19:33:46'),
(200, 'Kapitel 8: Reparationen', 4, 2, 3, NULL, 'Bip rullade fram till det trasiga skeppet. Han tog fram sina verktyg och började laga motorn. Varelsen tittade nyfiket på. Kim gav varelsen lite vatten. Efter en stund lyste skeppet upp igen. Det var lagat!', '[\r\n        {\"q\":\"Bip lagade skeppet.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Varelsen hjälpte till att laga.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Kim gav varelsen vatten.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Skeppet var fortfarande trasigt efteråt.\",\"a\":\"Falskt\"}\r\n    ]', 11, 80, '2025-12-03 19:33:46'),
(201, 'Kapitel 9: Avskedet', 4, 2, 1, NULL, 'Varelsen klev in i sitt skepp och vinkade glatt. Det gav ifrån sig ett surrande ljud och lyfte rakt upp i luften. Kim och Bip vinkade tillbaka. De hade fått en ny vän från en annan planet.', '[\r\n        {\"q\":\"Varelsen var arg när den åkte.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Skeppet lät surrande.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Kim och Bip vinkade inte.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"De hade fått en ny vän.\",\"a\":\"Sant\"}\r\n    ]', 12, 90, '2025-12-03 19:33:46'),
(202, 'Kapitel 10: Hemfärd', 4, 2, 3, NULL, 'Kim och Bip gick tillbaka till sin egen raket. \"Uppdraget slutfört,\" sa Kim och log. De startade motorn och flög hemåt mot Jorden igen. Bip visade en bild på skärmen: en selfie med Kim och den gröna varelsen.', '[\r\n        {\"q\":\"De stannade kvar på Mars.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"Uppdraget misslyckades.\",\"a\":\"Falskt\"},\r\n        {\"q\":\"De flög mot Jorden.\",\"a\":\"Sant\"},\r\n        {\"q\":\"Bip hade tagit en bild.\",\"a\":\"Sant\"}\r\n    ]', 13, 100, '2025-12-03 19:33:46'),
(203, 'Kapitel 1: Det övergivna tivolit', 4, 4, 1, NULL, 'Det var en mörk och dimmig kväll. Alex och hans vänner cyklade förbi det gamla tivolit som varit stängt i många år. Grindarna var låsta med rostiga kedjor. Innanför staketet stod karusellerna stilla och tysta. \"Jag vågar gå in\", viskade Alex.', '[{\"q\":\"Det var en solig dag.\",\"a\":\"Falskt\"},{\"q\":\"Tivolit var öppet.\",\"a\":\"Falskt\"},{\"q\":\"Grindarna var låsta.\",\"a\":\"Sant\"},{\"q\":\"Alex ville gå in.\",\"a\":\"Sant\"}]', 4, 10, '2025-12-03 19:33:46'),
(204, 'Kapitel 2: Hålet i staketet', 4, 4, 3, NULL, 'De hittade ett litet hål i staketet. Alex kröp igenom först. Hans kompis Sam följde efter. Det luktade gammalt trä och popcorn. Plötsligt hörde de ett gnisslande ljud. Det kom från pariserhjulet.', '[{\"q\":\"De klättrade över staketet.\",\"a\":\"Falskt\"},{\"q\":\"Alex gick in först.\",\"a\":\"Sant\"},{\"q\":\"Det luktade popcorn.\",\"a\":\"Sant\"},{\"q\":\"Pariserhjulet gnisslade.\",\"a\":\"Sant\"}]', 5, 20, '2025-12-03 19:33:46'),
(205, 'Kapitel 3: Spökhuset', 4, 4, 1, NULL, 'De gick mot spökhuset. Dörren hängde snett. Inne i mörkret såg de något som rörde sig. \"Är det en råtta?\" frågade Sam darrande. Men skuggan var för stor för att vara en råtta. Den såg ut som en människa.', '[{\"q\":\"De gick till en berg-och-dalbana.\",\"a\":\"Falskt\"},{\"q\":\"Dörren var trasig.\",\"a\":\"Sant\"},{\"q\":\"Sam trodde det var en råtta.\",\"a\":\"Sant\"},{\"q\":\"Skuggan såg ut som en hund.\",\"a\":\"Falskt\"}]', 6, 30, '2025-12-03 19:33:46'),
(206, 'Kapitel 4: Skrattet', 4, 4, 3, NULL, 'Ett högt, gällt skratt ekade genom parken. Det lät inte som en människa, utan mer som en clown. Alex rös. \"Vi borde gå hem\", sa Sam. Men Alex var nyfiken. Han tände sin ficklampa och lyste mot ljudet.', '[{\"q\":\"De hörde någon gråta.\",\"a\":\"Falskt\"},{\"q\":\"Skrattet lät som en clown.\",\"a\":\"Sant\"},{\"q\":\"Sam ville stanna kvar.\",\"a\":\"Falskt\"},{\"q\":\"Alex tände sin ficklampa.\",\"a\":\"Sant\"}]', 7, 40, '2025-12-03 19:33:46'),
(207, 'Kapitel 5: Spegelhallen', 4, 4, 1, NULL, 'De kom in i spegelhallen. Överallt såg de sina egna spegelbilder, men förvridna och konstiga. I en spegel såg Alex inte sig själv, utan en blek pojke som stod bakom honom. Han vände sig snabbt om, men ingen var där.', '[{\"q\":\"De var i ett lustiga huset.\",\"a\":\"Falskt\"},{\"q\":\"Spegelbilderna såg konstiga ut.\",\"a\":\"Sant\"},{\"q\":\"Alex såg en blek pojke i spegeln.\",\"a\":\"Sant\"},{\"q\":\"Pojken stod där på riktigt.\",\"a\":\"Falskt\"}]', 8, 50, '2025-12-03 19:33:46'),
(208, 'Kapitel 6: Karusellen startar', 4, 4, 3, NULL, 'Plötsligt tändes lamporna på den gamla hästkarusellen. Musiken började spela, falskt och långsamt. Hästarna började guppa upp och ner. Men det fanns ingen elektricitet på tivolit! Hur var det möjligt?', '[{\"q\":\"Karusellen var en berg-och-dalbana.\",\"a\":\"Falskt\"},{\"q\":\"Musiken lät vackert.\",\"a\":\"Falskt\"},{\"q\":\"Det fanns ingen ström på tivolit.\",\"a\":\"Sant\"},{\"q\":\"Hästarna började röra sig.\",\"a\":\"Sant\"}]', 9, 60, '2025-12-03 19:33:46'),
(209, 'Kapitel 7: Biljettluckan', 4, 4, 1, NULL, 'I den gamla biljettluckan satt en docka. Den var smutsig och hade bara ett öga. När de gick förbi, vred dockan på huvudet och följde dem med blicken. Sam skrek till och backade. Dockan blinkade.', '[{\"q\":\"Det satt en riktig människa i luckan.\",\"a\":\"Falskt\"},{\"q\":\"Dockan hade två ögon.\",\"a\":\"Falskt\"},{\"q\":\"Dockan rörde på huvudet.\",\"a\":\"Sant\"},{\"q\":\"Sam blev rädd.\",\"a\":\"Sant\"}]', 10, 70, '2025-12-03 19:33:46'),
(210, 'Kapitel 8: Berg-och-dalbanan', 4, 4, 3, NULL, 'En vagn kom farande på den rostiga rälsen högt ovanför dem. Den gnisslade och skrek. I vagnen satt två figurer som såg ut som skelett. De vinkade ner mot pojkarna. Alex kände hur blodet isade sig.', '[{\"q\":\"En vagn kom åkande.\",\"a\":\"Sant\"},{\"q\":\"Rälsen var ny och fin.\",\"a\":\"Falskt\"},{\"q\":\"Det satt clowner i vagnen.\",\"a\":\"Falskt\"},{\"q\":\"Skeletten vinkade.\",\"a\":\"Sant\"}]', 11, 80, '2025-12-03 19:33:46'),
(211, 'Kapitel 9: Flykten', 4, 4, 1, NULL, 'Nu var det nog. \"Spring!\" skrek Alex. De rusade mot hålet i staketet. Marken verkade mjuk och gripande, som om den ville hålla kvar dem. De hörde fotsteg springa efter dem i mörkret.', '[{\"q\":\"De bestämde sig för att stanna.\",\"a\":\"Falskt\"},{\"q\":\"De sprang mot utgången.\",\"a\":\"Sant\"},{\"q\":\"Marken kändes hård.\",\"a\":\"Falskt\"},{\"q\":\"Någon sprang efter dem.\",\"a\":\"Sant\"}]', 12, 90, '2025-12-03 19:33:46'),
(212, 'Kapitel 10: Aldrig mer', 4, 4, 3, NULL, 'De kastade sig genom hålet i staketet och cyklade hem så fort de kunde. De vände sig inte om. Nästa dag i skolan berättade ingen vad de sett. Men de cyklade aldrig förbi det gamla tivolit igen.', '[{\"q\":\"De gick lugnt hem.\",\"a\":\"Falskt\"},{\"q\":\"De tittade bakåt hela tiden.\",\"a\":\"Falskt\"},{\"q\":\"De berättade för alla i skolan.\",\"a\":\"Falskt\"},{\"q\":\"De undvek tivolit efteråt.\",\"a\":\"Sant\"}]', 13, 100, '2025-12-03 19:33:46'),
(213, 'Kapitel 1: Pyramiderna', 4, 5, 1, NULL, 'I Egyptens öken finns stora pyramider. De byggdes för mycket länge sedan av faraonerna. De är gjorda av enorma stenblock. Pyramiderna användes som gravar för kungarna.', '[{\"q\":\"Pyramiderna ligger i Sverige.\",\"a\":\"Falskt\"},{\"q\":\"De är byggda av sten.\",\"a\":\"Sant\"},{\"q\":\"Faraonerna byggde dem.\",\"a\":\"Sant\"},{\"q\":\"De var hus att bo i.\",\"a\":\"Falskt\"}]', 4, 10, '2025-12-03 19:33:46'),
(214, 'Kapitel 2: Kinesiska Muren', 4, 5, 3, NULL, 'Kinesiska muren är en jättelång mur i Kina. Den byggdes för att skydda landet från fiender. Muren är så lång att man kan gå på den i många dagar. Den är byggd av sten och tegel.', '[{\"q\":\"Muren ligger i Kina.\",\"a\":\"Sant\"},{\"q\":\"Den byggdes för att skydda landet.\",\"a\":\"Sant\"},{\"q\":\"Muren är kort.\",\"a\":\"Falskt\"},{\"q\":\"Den är gjord av trä.\",\"a\":\"Falskt\"}]', 5, 20, '2025-12-03 19:33:46'),
(215, 'Kapitel 3: Amazonas', 4, 5, 1, NULL, 'Amazonas är världens största regnskog. Den ligger i Sydamerika. Här regnar det ofta och är varmt. I skogen bor många djur, som apor och jaguarer. Genom skogen rinner en stor flod.', '[{\"q\":\"Amazonas är en öken.\",\"a\":\"Falskt\"},{\"q\":\"Det är kallt där.\",\"a\":\"Falskt\"},{\"q\":\"Det bor apor i skogen.\",\"a\":\"Sant\"},{\"q\":\"En flod rinner genom skogen.\",\"a\":\"Sant\"}]', 6, 30, '2025-12-03 19:33:46'),
(216, 'Kapitel 4: Mount Everest', 4, 5, 3, NULL, 'Mount Everest är världens högsta berg. Toppen är täckt av snö och is året om. Det är mycket svårt och farligt att klättra upp dit. Luften är tunn, så det är svårt att andas.', '[{\"q\":\"Berget är världens högsta.\",\"a\":\"Sant\"},{\"q\":\"Det är varmt på toppen.\",\"a\":\"Falskt\"},{\"q\":\"Det är lätt att klättra upp.\",\"a\":\"Falskt\"},{\"q\":\"Det finns snö på berget.\",\"a\":\"Sant\"}]', 7, 40, '2025-12-03 19:33:46'),
(217, 'Kapitel 5: Marianergraven', 4, 5, 1, NULL, 'Djupt nere i havet finns en plats som heter Marianergraven. Det är världens djupaste plats. Det är mörkt och kallt där nere. Inga människor kan simma dit utan ubåt, för trycket är för högt.', '[{\"q\":\"Marianergraven ligger på land.\",\"a\":\"Falskt\"},{\"q\":\"Det är världens djupaste plats.\",\"a\":\"Sant\"},{\"q\":\"Det är ljust där nere.\",\"a\":\"Falskt\"},{\"q\":\"Man behöver en ubåt.\",\"a\":\"Sant\"}]', 8, 50, '2025-12-03 19:33:46'),
(218, 'Kapitel 6: Antarktis', 4, 5, 3, NULL, 'Antarktis ligger på Sydpolen. Det är en hel kontinent täckt av is. Det är den kallaste platsen på jorden. Här bor inga människor permanent, men många pingviner trivs i kylan.', '[{\"q\":\"Antarktis ligger på Nordpolen.\",\"a\":\"Falskt\"},{\"q\":\"Det är mycket varmt där.\",\"a\":\"Falskt\"},{\"q\":\"Det finns mycket is.\",\"a\":\"Sant\"},{\"q\":\"Pingviner bor där.\",\"a\":\"Sant\"}]', 9, 60, '2025-12-03 19:33:46');
INSERT INTO `tasks` (`t_id`, `t_name`, `t_type_fk`, `t_genre_fk`, `t_teacher_fk`, `t_class_fk`, `t_text`, `t_questions`, `t_level_fk`, `t_xp`, `t_created`) VALUES
(219, 'Kapitel 7: Colosseum', 4, 5, 1, NULL, 'I staden Rom i Italien ligger Colosseum. Det är en gammal arena. Förr i tiden tittade folk på gladiatorer som kämpade där. Det är byggt som en stor cirkel av sten.', '[{\"q\":\"Colosseum ligger i Paris.\",\"a\":\"Falskt\"},{\"q\":\"Det är en gammal arena.\",\"a\":\"Sant\"},{\"q\":\"Gladiatorer kämpade där.\",\"a\":\"Sant\"},{\"q\":\"Den är byggd av trä.\",\"a\":\"Falskt\"}]', 10, 70, '2025-12-03 19:33:46'),
(220, 'Kapitel 8: Stora Barriärrevet', 4, 5, 3, NULL, 'Utanför Australien ligger världens största korallrev. Det kallas Stora Barriärrevet. Det lever massor av färgglada fiskar och koraller där. Vattnet är klart och varmt. Man kan dyka där för att se det.', '[{\"q\":\"Revet ligger i Sverige.\",\"a\":\"Falskt\"},{\"q\":\"Det finns koraller där.\",\"a\":\"Sant\"},{\"q\":\"Vattnet är kallt och mörkt.\",\"a\":\"Falskt\"},{\"q\":\"Man kan dyka där.\",\"a\":\"Sant\"}]', 11, 80, '2025-12-03 19:33:46'),
(221, 'Kapitel 9: Grand Canyon', 4, 5, 1, NULL, 'Grand Canyon är en enorm ravin i USA. Den har skapats av floden Colorado som runnit där i miljontals år. Klipporna är röda och orangea. Det är en väldigt känd turistplats.', '[{\"q\":\"Grand Canyon ligger i USA.\",\"a\":\"Sant\"},{\"q\":\"En flod har skapat ravinen.\",\"a\":\"Sant\"},{\"q\":\"Klipporna är blå.\",\"a\":\"Falskt\"},{\"q\":\"Ingen åker dit.\",\"a\":\"Falskt\"}]', 12, 90, '2025-12-03 19:33:46'),
(222, 'Kapitel 10: Norrsken', 4, 5, 3, NULL, 'Norrsken är ett ljusfenomen på himlen. Det ser ut som gröna och lila gardiner som rör sig. Det händer när partiklar från solen träffar jordens atmosfär. Man ser det bäst nära polerna på vintern.', '[{\"q\":\"Norrsken är en sorts fågel.\",\"a\":\"Falskt\"},{\"q\":\"Det syns på himlen.\",\"a\":\"Sant\"},{\"q\":\"Det är ofta grönt.\",\"a\":\"Sant\"},{\"q\":\"Det kommer från månen.\",\"a\":\"Falskt\"}]', 13, 100, '2025-12-03 19:33:46'),
(223, 'Kapitel 1: En växts liv', 2, 5, 1, NULL, 'Hur blir ett litet frö till en vacker blomma? Det krävs jord, vatten och sol. Sortera stegen i rätt ordning från början till slut.', '{\"s\":[\"Vi planterar ett frö i jorden.\",\"Fröet får vatten och sol.\",\"En liten grodd tittar upp.\",\"Växten får blad.\",\"En blomma slår ut.\"]}', 4, 10, '2025-12-03 19:33:46'),
(224, 'Kapitel 2: Baka bröd', 2, 5, 3, NULL, 'Att baka bröd är kemi i köket. Man måste göra sakerna i rätt ordning för att brödet ska jäsa och bli gott. Sortera stegen i bakningen.', '{\"s\":[\"Blanda mjöl, vatten och jäst.\",\"Knåda degen.\",\"Låt degen jäsa.\",\"Forma degen till limpor.\",\"Grädda brödet i ugnen.\"]}', 5, 20, '2025-12-03 19:33:46'),
(225, 'Kapitel 3: Fjärilens livscykel', 2, 5, 1, NULL, 'Fjärilen genomgår en fantastisk förvandling under sitt liv. Det kallas metamorfos. Kan du sortera fjärilens olika stadier i rätt ordning?', '{\"s\":[\"En fjäril lägger ägg på ett blad.\",\"En larv kläcks ur ägget.\",\"Larven äter och växer sig stor.\",\"Larven spinner en puppa.\",\"En färdig fjäril kryper ut.\"]}', 6, 30, '2025-12-03 19:33:46'),
(226, 'Kapitel 4: Vattnets kretslopp', 2, 5, 3, NULL, 'Vattnet på jorden tar aldrig slut, det går bara runt i ett kretslopp. Solen driver processen. Sortera hur en vattendroppe rör sig.', '{\"s\":[\"Solen värmer havet.\",\"Vattenånga stiger uppåt.\",\"Ångan bildar moln.\",\"Det börjar regna.\",\"Vattnet rinner tillbaka ut i havet.\"]}', 7, 40, '2025-12-03 19:33:46'),
(227, 'Kapitel 5: Postens väg', 2, 5, 1, NULL, 'Vad händer när du skickar ett brev till en vän? Brevet åker på en lång resa innan det kommer fram. Sortera stegen för ett brev.', '{\"s\":[\"Du skriver ett brev och lägger på lådan.\",\"Brevbäraren tömmer brevlådan.\",\"Brevet sorteras på en terminal.\",\"Brevet åker bil eller tåg till rätt stad.\",\"Brevbäraren lägger brevet i vännens låda.\"]}', 8, 50, '2025-12-03 19:33:46'),
(228, 'Kapitel 6: Från träd till papper', 2, 5, 3, NULL, 'Papperet du skriver på kommer från skogen. Det är en lång process att förvandla trä till tunna pappersark. Sortera tillverkningen.', '{\"s\":[\"Skogsmaskiner fäller träden.\",\"Timret körs till sågverket.\",\"Trädet hackas sönder till flis.\",\"Flisen kokas till pappersmassa.\",\"Massan pressas och torkas till papper.\"]}', 9, 60, '2025-12-03 19:33:46'),
(229, 'Kapitel 7: Matsmältningen', 2, 5, 1, NULL, 'Kroppen är som en maskin som behöver bränsle. När vi äter mat går den genom kroppen och näringen tas upp. Sortera matens väg.', '{\"s\":[\"Vi tuggar maten i munnen.\",\"Maten sväljs ner i matstrupen.\",\"Maten landar i magsäcken och knådas.\",\"Näringen tas upp i tunntarmen.\",\"Resten går ut genom tjocktarmen.\"]}', 10, 70, '2025-12-03 19:33:46'),
(230, 'Kapitel 8: Husbygge', 2, 5, 3, NULL, 'Att bygga ett hus kräver planering. Man kan inte lägga taket innan väggarna är uppe. Sortera i vilken ordning man bygger ett hus.', '{\"s\":[\"Arkitekten ritar en ritning.\",\"Grävmaskiner gräver grunden.\",\"Man gjuter en platta av betong.\",\"Snickarna reser väggarna.\",\"Taket läggs på.\",\"Målare och elektriker gör klart insidan.\"]}', 11, 80, '2025-12-03 19:33:46'),
(231, 'Kapitel 9: Återvinning av glas', 2, 5, 1, NULL, 'Glas kan återvinnas hur många gånger som helst. Det sparar energi och naturresurser. Sortera vad som händer med din gamla glasflaska.', '{\"s\":[\"Du slänger flaskan i en glasigloo.\",\"En lastbil hämtar glaset.\",\"Glaset rensas och sorteras efter färg.\",\"Glaset krossas och smälts ner i en ugn.\",\"Den smälta massan formas till nya flaskor.\",\"De nya flaskorna fylls med dryck.\"]}', 12, 90, '2025-12-03 19:33:46'),
(232, 'Kapitel 10: Evolutionen', 2, 5, 3, NULL, 'Livet på jorden har utvecklats under miljarder år, från enkla celler till komplexa djur och människor. Sortera djurgrupperna i den ordning de uppstod i evolutionen.', '{\"s\":[\"Encelliga organismer i havet.\",\"Fiskar (ryggradsdjur i vatten).\",\"Groddjur (börjar gå upp på land).\",\"Reptiler (lägger ägg på land).\",\"Däggdjur (har päls och ger di).\",\"Människor.\"]}', 13, 100, '2025-12-03 19:33:46'),
(233, 'Kapitel 1: Upptäckten', 2, 2, 1, NULL, 'Kai städade sin morfars gamla vind. Under en presenning hittade han en märklig maskin av metall och glas. Det fanns en lapp där det stod \"Tidsmaskin - Hantera varsamt\". Kai dammade av den och såg en röd knapp.', '{\"s\":[\"Kai städade på vinden.\",\"Han hittade en märklig maskin.\",\"Han läste lappen om tidsmaskinen.\",\"Kai dammade av maskinen.\",\"Han såg en röd knapp.\"]}', 4, 10, '2025-12-03 19:33:46'),
(234, 'Kapitel 2: Starten', 2, 2, 3, NULL, 'Nyfikenheten tog över. Kai satte sig i stolen och spände fast bältet. Han ställde in årtalet på displayen till år 3000. Sedan blundade han och tryckte på den röda knappen. Maskinen började vibrera och tjuta.', '{\"s\":[\"Kai satte sig i maskinen.\",\"Han spände fast bältet.\",\"Han ställde in år 3000.\",\"Han tryckte på startknappen.\",\"Maskinen började vibrera.\"]}', 5, 20, '2025-12-03 19:33:46'),
(235, 'Kapitel 3: Framtiden', 2, 2, 1, NULL, 'När Kai öppnade ögonen var vinden borta. Han stod mitt i en stad av glas. Bilar flög i luften mellan skyskraporna. Robotor gick på gatorna och pratade med varandra. Det var en helt ny värld.', '{\"s\":[\"Kai öppnade ögonen.\",\"Vinden var borta.\",\"Han såg flygande bilar.\",\"Robotar gick på gatorna.\",\"Han förstod att han var i framtiden.\"]}', 6, 30, '2025-12-03 19:33:46'),
(236, 'Kapitel 4: Batteriet dör', 2, 2, 3, NULL, 'Kai ville åka hem igen och berätta vad han sett. Han tryckte på knappen, men inget hände. En skärm blinkade: \"Energi låg\". Batteriet var dött! Han var fast i framtiden om han inte hittade en ny energikälla.', '{\"s\":[\"Kai försökte åka hem.\",\"Knappen fungerade inte.\",\"Skärmen varnade för låg energi.\",\"Batteriet var dött.\",\"Kai var fast i framtiden.\"]}', 7, 40, '2025-12-03 19:33:46'),
(237, 'Kapitel 5: Robot-verkstaden', 2, 2, 1, NULL, 'Han frågade en robot om vägen till en verkstad. Roboten pekade mot en stor byggnad med blått ljus. Kai sprang dit. Inne i verkstaden fanns massor av reservdelar och kablar. Han letade efter något som liknade hans batteri.', '{\"s\":[\"Kai frågade en robot om vägen.\",\"Han sprang till verkstaden.\",\"Han gick in i byggnaden.\",\"Där fanns många reservdelar.\",\"Han letade efter ett batteri.\"]}', 8, 50, '2025-12-03 19:33:46'),
(238, 'Kapitel 6: Cyborgen Nova', 2, 2, 3, NULL, 'En cyborg vid namn Nova kom fram till honom. Hon hade en arm av metall. \"Vad letar du efter, tidsresenär?\" frågade hon. Kai visade sitt trasiga batteri. Nova log och sa att hon kunde hjälpa honom att laga det.', '{\"s\":[\"Cyborgen Nova kom fram.\",\"Hon frågade vad Kai letade efter.\",\"Kai visade det trasiga batteriet.\",\"Nova tittade på det.\",\"Hon lovade att hjälpa honom.\"]}', 9, 60, '2025-12-03 19:33:46'),
(239, 'Kapitel 7: Reparationen', 2, 2, 1, NULL, 'Nova tog fram sina verktyg. Först skruvade hon isär batteriet. Sedan bytte hon ut den trasiga kärnan mot en blå kristall. Hon skruvade ihop det igen och det började lysa. Batteriet var som nytt.', '{\"s\":[\"Nova tog fram verktyg.\",\"Hon skruvade isär batteriet.\",\"Hon bytte ut kärnan mot en kristall.\",\"Hon skruvade ihop batteriet.\",\"Det började lysa igen.\"]}', 10, 70, '2025-12-03 19:33:46'),
(240, 'Kapitel 8: Jakten', 2, 2, 3, NULL, 'Plötsligt tjöt ett larm. \"Tidspolisen!\" ropade Nova. \"De gillar inte otillåtna tidsresor.\" Kai ryckte åt sig batteriet. De sprang ut bakvägen genom en mörk gränd. Svävande polisbilar med blåljus letade efter dem.', '{\"s\":[\"Ett larm gick igång.\",\"Nova varnade för Tidspolisen.\",\"Kai tog batteriet.\",\"De sprang ut bakvägen.\",\"Polisbilar letade efter dem.\"]}', 11, 80, '2025-12-03 19:33:46'),
(241, 'Kapitel 9: Tillbaka till maskinen', 2, 2, 1, NULL, 'De smög tillbaka till platsen där tidsmaskinen stod. Nova hjälpte Kai att montera det nya batteriet. \"Skynda dig!\" sa hon. Kai tackade henne för hjälpen och hoppade in i stolen. Han hörde poliserna komma närmare.', '{\"s\":[\"De smög tillbaka till maskinen.\",\"Nova monterade batteriet.\",\"Kai tackade Nova.\",\"Han hoppade in i stolen.\",\"Poliserna närmade sig.\"]}', 12, 90, '2025-12-03 19:33:46'),
(242, 'Kapitel 10: Hemma igen', 2, 2, 3, NULL, 'Kai tryckte på knappen precis i tid. Världen snurrade och blev suddig. När det stannade var han tillbaka på morfars dammiga vind. Allt var precis som han lämnat det. Men i fickan låg en liten metallbit från framtiden som minne.', '{\"s\":[\"Kai tryckte på knappen.\",\"Världen blev suddig.\",\"Maskinen stannade på vinden.\",\"Han var hemma igen.\",\"Han kände metallbiten i fickan.\"]}', 13, 100, '2025-12-03 19:33:46'),
(243, 'Kapitel 1: Stölden', 2, 3, 1, NULL, 'På det lyxiga Hotell Grand upptäckte grevinnan att hennes halsband var borta. Hon skrek högt. Hotellchefen kom springande. Han såg det tomma smyckeskrinet. Han låste genast dörrarna till hotellet.', '{\"s\":[\"Grevinnan upptäckte att halsbandet var borta.\",\"Hon skrek högt.\",\"Hotellchefen kom springande.\",\"Han såg det tomma skrinet.\",\"Han låste hotellets dörrar.\"]}', 4, 10, '2025-12-03 19:33:46'),
(244, 'Kapitel 2: Detektiven anländer', 2, 3, 3, NULL, 'Privatdetektiven Alex var redan på hotellet. Han drack kaffe i lobbyn. När han hörde larmet reste han sig upp. Han tog fram sitt förstoringsglas. Sedan gick han mot grevinnans rum.', '{\"s\":[\"Alex drack kaffe i lobbyn.\",\"Han hörde larmet gå.\",\"Han reste sig upp.\",\"Han tog fram sitt förstoringsglas.\",\"Han gick mot grevinnans rum.\"]}', 5, 20, '2025-12-03 19:33:46'),
(245, 'Kapitel 3: Brottsplatsen', 2, 3, 1, NULL, 'Inne på rummet var det stökigt. Stolar var välta. Fönstret stod på glänt. Alex såg leriga fotspår på mattan. Han mätte fotspåren noggrant.', '{\"s\":[\"Rummet var stökigt.\",\"Stolar var välta.\",\"Fönstret stod på glänt.\",\"Alex såg leriga fotspår.\",\"Han mätte fotspåren.\"]}', 6, 30, '2025-12-03 19:33:46'),
(246, 'Kapitel 4: Vittnet', 2, 3, 3, NULL, 'Städerskan Anna hade sett något. Hon städade i korridoren. Hon såg en man springa förbi. Han bar på en röd väska. Sedan försvann han in i hissen.', '{\"s\":[\"Anna städade i korridoren.\",\"Hon såg en man springa förbi.\",\"Mannen hade en röd väska.\",\"Han försvann in i hissen.\",\"Anna berättade detta för Alex.\"]}', 7, 40, '2025-12-03 19:33:46'),
(247, 'Kapitel 5: Kocken', 2, 3, 1, NULL, 'Alex gick till köket för att förhöra kocken. Kocken hackade lök och grät. Han sa att han varit i köket hela tiden. Men Alex såg lera på kockens skor. Kocken blev nervös.', '{\"s\":[\"Alex gick till köket.\",\"Kocken hackade lök.\",\"Han sa att han varit där hela tiden.\",\"Alex såg lera på hans skor.\",\"Kocken blev nervös.\"]}', 8, 50, '2025-12-03 19:33:46'),
(248, 'Kapitel 6: Trädgårdsmästaren', 2, 3, 3, NULL, 'Ute i trädgården jobbade trädgårdsmästaren. Det hade regnat så det var lerigt. Alex frågade om hans skor. De matchade fotspåren i rummet! Men trädgårdsmästaren sa att han tappat sina skor igår.', '{\"s\":[\"Trädgårdsmästaren jobbade ute.\",\"Det var lerigt i trädgården.\",\"Alex kollade hans skor.\",\"Skorna matchade fotspåren.\",\"Trädgårdsmästaren sa att han tappat dem.\"]}', 9, 60, '2025-12-03 19:33:46'),
(249, 'Kapitel 7: Piaman', 2, 3, 1, NULL, 'I baren satt en man och spelade piano. Det var pianisten Pierre. Han hade en röd väska vid fötterna. Alex bad att få titta i väskan. Pierre vägrade och försökte springa iväg.', '{\"s\":[\"Pierre spelade piano i baren.\",\"Han hade en röd väska.\",\"Alex ville titta i väskan.\",\"Pierre vägrade öppna den.\",\"Han försökte springa iväg.\"]}', 10, 70, '2025-12-03 19:33:46'),
(250, 'Kapitel 8: Jakten', 2, 3, 3, NULL, 'Alex sprang efter Pierre. De sprang genom köket och välte en vagn med tårtor. De sprang ut i trädgården. Pierre halkade i leran. Alex hann ikapp och grep honom.', '{\"s\":[\"Alex sprang efter Pierre.\",\"De sprang genom köket.\",\"En tårtvagn välte.\",\"De sprang ut i trädgården.\",\"Pierre halkade och blev fångad.\"]}', 11, 80, '2025-12-03 19:33:46'),
(251, 'Kapitel 9: Beviset', 2, 3, 1, NULL, 'Alex öppnade den röda väskan. I den låg trädgårdsmästarens stulna skor. Men halsbandet var inte där! Då såg Alex att Pierres piano hade en lös tangent. Han lyfte på tangenten.', '{\"s\":[\"Alex öppnade väskan.\",\"I väskan låg de stulna skorna.\",\"Halsbandet var inte i väskan.\",\"Alex såg en lös tangent på pianot.\",\"Han lyfte på tangenten.\"]}', 12, 90, '2025-12-03 19:33:46'),
(252, 'Kapitel 10: Fallet löst', 2, 3, 3, NULL, 'Under tangenten glimmade halsbandet. Pierre hade stulit skorna för att lägga skulden på trädgårdsmästaren. Polisen kom och hämtade Pierre. Grevinnan fick tillbaka sitt smycke och Alex fick en belöning.', '{\"s\":[\"Halsbandet låg under tangenten.\",\"Pierre hade försökt lura alla.\",\"Polisen hämtade tjuven.\",\"Grevinnan fick sitt halsband.\",\"Alex fick en belöning.\"]}', 13, 100, '2025-12-03 19:33:46'),
(253, 'Kapitel 1: Stölden på museet', 1, 3, 1, NULL, 'Det var kaos på museet. Den kända diamanten \"Silverstjärnan\" var borta! Glasmontern var krossad. Detektiven Leo kom dit med sin hund, Snuffe. Leo tog fram sitt anteckningsblock. \"Ingen lämnar rummet!\" ropade han.', '[{\"q\":\"Vad hade stulits?\",\"a\":\"En diamant\",\"w1\":\"En tavla\",\"w2\":\"En staty\",\"w3\":\"En krona\"},{\"q\":\"Vad heter diamanten?\",\"a\":\"Silverstjärnan\",\"w1\":\"Guldstjärnan\",\"w2\":\"Blå Månen\",\"w3\":\"Röda Rosen\"},{\"q\":\"Vem är Snuffe?\",\"a\":\"Leos hund\",\"w1\":\"Tjuven\",\"w2\":\"En polis\",\"w3\":\"Museichefen\"}]', 4, 10, '2025-12-03 19:33:46'),
(254, 'Kapitel 2: Spåren i golvet', 1, 3, 3, NULL, 'Leo undersökte golvet. Han såg glasbitar och leriga fotspår. Fotspåren ledde från fönstret fram till montern. \"Tjuven kom in utifrån\", sa Leo. Snuffe nosade på leran och nös högt.', '[{\"q\":\"Vad hittade Leo på golvet?\",\"a\":\"Glas och leriga fotspår\",\"w1\":\"Bara glas\",\"w2\":\"En tappad vante\",\"w3\":\"Ingenting\"},{\"q\":\"Varifrån kom tjuven?\",\"a\":\"Utifrån\",\"w1\":\"Från taket\",\"w2\":\"Från källaren\",\"w3\":\"\"},{\"q\":\"Vad gjorde Snuffe?\",\"a\":\"Nosade och nös\",\"w1\":\"Skällde\",\"w2\":\"Sov\",\"w3\":\"\"}]', 5, 20, '2025-12-03 19:33:46'),
(255, 'Kapitel 3: Vittnet Sune', 1, 3, 1, NULL, 'Nattvakten Sune satt på en stol och såg rädd ut. \"Jag hörde en smäll\", sa han. \"Sen såg jag en skugga smita ut.\" Leo skrev upp allt Sune sa. Var Sune verkligen så oskyldig som han såg ut?', '[{\"q\":\"Vem var Sune?\",\"a\":\"Nattvakten\",\"w1\":\"Tjuven\",\"w2\":\"Polisen\",\"w3\":\"\"},{\"q\":\"Vad hade Sune sett?\",\"a\":\"En skugga\",\"w1\":\"Tjuvens ansikte\",\"w2\":\"En bil\",\"w3\":\"\"},{\"q\":\"Hur kände sig Sune?\",\"a\":\"Rädd\",\"w1\":\"Glad\",\"w2\":\"Arg\",\"w3\":\"\"}]', 6, 30, '2025-12-03 19:33:46'),
(256, 'Kapitel 4: Handsken i busken', 1, 3, 3, NULL, 'Snuffe skällde vid en buske utanför fönstret. Leo gick dit. I busken hängde en svart handske. Den måste ha fastnat när tjuven flydde. \"Bra jobbat, Snuffe!\" sa Leo och lade handsken i en bevispåse.', '[{\"q\":\"Var skällde Snuffe?\",\"a\":\"Vid en buske\",\"w1\":\"Vid dörren\",\"w2\":\"Vid bilen\",\"w3\":\"\"},{\"q\":\"Vad hittade de?\",\"a\":\"En svart handske\",\"w1\":\"En sko\",\"w2\":\"En mössa\",\"w3\":\"\"},{\"q\":\"Vad gjorde Leo med den?\",\"a\":\"Lade den i en påse\",\"w1\":\"Tog på sig den\",\"w2\":\"Kastade den\",\"w3\":\"\"}]', 7, 40, '2025-12-03 19:33:46'),
(257, 'Kapitel 5: Laboratoriet', 1, 3, 1, NULL, 'Leo tog handsken till sitt labb. Han använde ett mikroskop för att titta på den. Han hittade ett rött hårstrå på tyget. \"Intressant\", mumlade Leo. \"Tjuven har rött hår, eller kanske en röd katt?\"', '[{\"q\":\"Vart tog Leo handsken?\",\"a\":\"Till sitt labb\",\"w1\":\"Till polisen\",\"w2\":\"Hem\",\"w3\":\"\"},{\"q\":\"Vad använde han för att titta?\",\"a\":\"Ett mikroskop\",\"w1\":\"Glasögon\",\"w2\":\"En kikare\",\"w3\":\"\"},{\"q\":\"Vad hittade han på handsken?\",\"a\":\"Ett rött hårstrå\",\"w1\":\"Blod\",\"w2\":\"Färg\",\"w3\":\"Guld\"}]', 8, 50, '2025-12-03 19:33:46'),
(258, 'Kapitel 6: Biblioteket', 1, 3, 3, NULL, 'Leo gick till biblioteket för att läsa om kända tjuvar. Han hittade en bild på \"Röda Räven\", en tjuv som alltid bar svarta handskar. Och han hade rött hår! Leo visste nu vem han jagade.', '[{\"q\":\"Vem var Röda Räven?\",\"a\":\"En känd tjuv\",\"w1\":\"En polis\",\"w2\":\"En författare\",\"w3\":\"\"},{\"q\":\"Vad hade han alltid på sig?\",\"a\":\"Svarta handskar\",\"w1\":\"En hatt\",\"w2\":\"En mask\",\"w3\":\"\"},{\"q\":\"Vilken hårfärg hade han?\",\"a\":\"Rött\",\"w1\":\"Svart\",\"w2\":\"Blont\",\"w3\":\"\"}]', 9, 60, '2025-12-03 19:33:46'),
(259, 'Kapitel 7: Spåret till Bageriet', 1, 3, 1, NULL, 'Någon hade sett Röda Räven köpa munkar på bageriet. Leo och Snuffe skyndade dit. Bagaren sa: \"Ja, han var här nyss. Han tappade den här lappen.\" På lappen stod en tid och en plats: \"Hamnen kl 12\".', '[{\"q\":\"Var hade tjuven synts till?\",\"a\":\"På bageriet\",\"w1\":\"På banken\",\"w2\":\"I parken\",\"w3\":\"\"},{\"q\":\"Vad hade han köpt?\",\"a\":\"Munkar\",\"w1\":\"Bröd\",\"w2\":\"Tårta\",\"w3\":\"\"},{\"q\":\"Vad stod på lappen?\",\"a\":\"Hamnen kl 12\",\"w1\":\"Skolan kl 8\",\"w2\":\"Torget kl 10\",\"w3\":\"Stationen kl 12\"}]', 10, 70, '2025-12-03 19:33:46'),
(260, 'Kapitel 8: Fällan i Hamnen', 1, 3, 3, NULL, 'Leo smög ner till hamnen. Han gömde sig bakom en stor låda. Klockan slog tolv. En båt kom in mot kajen. En man med rött hår kom gående. Det var Röda Räven! Han höll hårt i en väska.', '[{\"q\":\"Var gömde sig Leo?\",\"a\":\"Bakom en låda\",\"w1\":\"I en båt\",\"w2\":\"I vattnet\",\"w3\":\"\"},{\"q\":\"Vad hände kl 12?\",\"a\":\"Röda Räven kom\",\"w1\":\"Båten åkte\",\"w2\":\"Det började regna\",\"w3\":\"\"},{\"q\":\"Vad bar tjuven på?\",\"a\":\"En väska\",\"w1\":\"En säck\",\"w2\":\"En kista\",\"w3\":\"\"}]', 11, 80, '2025-12-03 19:33:46'),
(261, 'Kapitel 9: Gripandet', 1, 3, 1, NULL, 'Precis när Räven skulle hoppa på båten, hoppade Snuffe fram och bet honom i byxbenet. Leo sprang fram. \"Du är arresterad!\" ropade han. Polisen kom och satte handklovar på tjuven.', '[{\"q\":\"Vem stoppade tjuven först?\",\"a\":\"Snuffe\",\"w1\":\"Leo\",\"w2\":\"Polisen\",\"w3\":\"\"},{\"q\":\"Vad gjorde Snuffe?\",\"a\":\"Bet i byxbenet\",\"w1\":\"Skällde\",\"w2\":\"Hoppade på ryggen\",\"w3\":\"\"},{\"q\":\"Vad gjorde polisen?\",\"a\":\"Satte på handklovar\",\"w1\":\"Körde iväg\",\"w2\":\"Sköt\",\"w3\":\"\"}]', 12, 90, '2025-12-03 19:33:46'),
(262, 'Kapitel 10: Belöningen', 1, 3, 3, NULL, 'Museet fick tillbaka sin diamant. Leo och Snuffe fick en medalj av borgmästaren. Snuffe fick också ett stort ben. \"Ännu ett fall löst\", sa Leo och klappade sin duktiga hund.', '[{\"q\":\"Vad fick Leo och Snuffe?\",\"a\":\"En medalj\",\"w1\":\"Pengar\",\"w2\":\"En semester\",\"w3\":\"\"},{\"q\":\"Vad fick Snuffe mer?\",\"a\":\"Ett stort ben\",\"w1\":\"En ny leksak\",\"w2\":\"En ny halsband\",\"w3\":\"\"},{\"q\":\"Vem delade ut priset?\",\"a\":\"Borgmästaren\",\"w1\":\"Kungen\",\"w2\":\"Polischefen\",\"w3\":\"\"}]', 13, 100, '2025-12-03 19:33:46'),
(263, 'Kapitel 1: Vilse i dimman', 1, 4, 1, NULL, 'Alex och Mia var ute och plockade svamp. Plötsligt rullade en tjock, kall dimma in över skogen. De kunde inte längre se stigen. Träden såg ut som krokiga fingrar som sträckte sig efter dem. Det blev snabbt mörkt.', '[{\"q\":\"Vad gjorde barnen i skogen?\",\"a\":\"Plockade svamp\",\"w1\":\"Lekte kurragömma\",\"w2\":\"Tältade\",\"w3\":\"Cyklade\"},{\"q\":\"Vad hände med vädret?\",\"a\":\"En tjock dimma kom\",\"w1\":\"Det började snöa\",\"w2\":\"Solen sken starkt\",\"w3\":\"Det började åska\"},{\"q\":\"Hur såg träden ut?\",\"a\":\"Som krokiga fingrar\",\"w1\":\"Som gröna klubbor\",\"w2\":\"Som julgranar\",\"w3\":\"Som snälla jättar\"}]', 4, 10, '2025-12-03 19:33:46'),
(264, 'Kapitel 2: Den gamla stugan', 1, 4, 3, NULL, 'De irrade runt i timmar. Till slut såg de ett ljus. Det kom från en gammal, förfallen stuga mitt i skogen. Fönstren var trasiga och dörren hängde på trekvart. \"Vi kanske kan söka skydd där,\" viskade Mia darrande.', '[{\"q\":\"Vad hittade de i skogen?\",\"a\":\"En gammal stuga\",\"w1\":\"Ett slott\",\"w2\":\"En busshållplats\",\"w3\":\"En grotta\"},{\"q\":\"Hur såg stugan ut?\",\"a\":\"Förfallen och trasig\",\"w1\":\"Ny och fin\",\"w2\":\"Målad i guld\",\"w3\":\"Gjord av godis\"},{\"q\":\"Vem ville gå in?\",\"a\":\"Mia\",\"w1\":\"Alex\",\"w2\":\"Ingen\",\"w3\":\"En hund\"}]', 5, 20, '2025-12-03 19:33:46'),
(265, 'Kapitel 3: Inuti mörkret', 1, 4, 1, NULL, 'De gick in. Golvet knarrade högt. Det luktade mögel och gammal jord. På ett bord mitt i rummet låg en stor, dammig bok uppslagen. Sidorna var gjorda av något som liknade gammalt skinn.', '[{\"q\":\"Vad hördes när de gick in?\",\"a\":\"Golvet knarrade\",\"w1\":\"Musik spelades\",\"w2\":\"En klocka tickade\",\"w3\":\"Någon skrattade\"},{\"q\":\"Vad låg på bordet?\",\"a\":\"En dammig bok\",\"w1\":\"En kniv\",\"w2\":\"En karta\",\"w3\":\"En nyckel\"},{\"q\":\"Vad luktade det?\",\"a\":\"Mögel och jord\",\"w1\":\"Blommor\",\"w2\":\"Nybakat bröd\",\"w3\":\"Parfym\"}]', 6, 30, '2025-12-03 19:33:46'),
(266, 'Kapitel 4: Viskningarna', 1, 4, 3, NULL, 'Alex gick fram till boken. Texten var skriven med rött bläck. När han läste orden tyst för sig själv, började det viska i rummet. Viskningarna kom från väggarna. \"Hjälp oss... släpp ut oss...\", sa rösterna.', '[{\"q\":\"Vilken färg hade bläcket?\",\"a\":\"Rött\",\"w1\":\"Blått\",\"w2\":\"Svart\",\"w3\":\"Grönt\"},{\"q\":\"Vad hände när han läste?\",\"a\":\"Det började viska\",\"w1\":\"Ljuset tändes\",\"w2\":\"Boken brann upp\",\"w3\":\"Taket rasade in\"},{\"q\":\"Varifrån kom rösterna?\",\"a\":\"Från väggarna\",\"w1\":\"Från källaren\",\"w2\":\"Från vinden\",\"w3\":\"Från utsisdan\"}]', 7, 40, '2025-12-03 19:33:46'),
(267, 'Kapitel 5: Skuggorna vaknar', 1, 4, 1, NULL, 'Plötsligt slocknade stearinljuset de hade tänt. Mörka skuggor lösgjorde sig från hörnen. De hade formen av människor, men med lysande röda ögon. Skuggorna började röra sig mot barnen.', '[{\"q\":\"Vad hände med ljuset?\",\"a\":\"Det slocknade\",\"w1\":\"Det blev starkare\",\"w2\":\"Det blev blått\",\"w3\":\"Det exploderade\"},{\"q\":\"Vad kom fram ur hörnen?\",\"a\":\"Mörka skuggor\",\"w1\":\"Råttor\",\"w2\":\"Fladdermöss\",\"w3\":\"Spindlar\"},{\"q\":\"Vilken färg hade ögonen?\",\"a\":\"Röda\",\"w1\":\"Gula\",\"w2\":\"Gröna\",\"w3\":\"Vita\"}]', 8, 50, '2025-12-03 19:33:46'),
(268, 'Kapitel 6: Flykten', 1, 4, 3, NULL, '\"Spring!\" skrek Alex. De rusade mot dörren, men den slog igen framför näsan på dem. Skuggorna kom närmare. Mia såg en öppen lucka i golvet. \"Ner i källaren!\" ropade hon och drog med sig Alex.', '[{\"q\":\"Varför kom de inte ut?\",\"a\":\"Dörren slog igen\",\"w1\":\"De var fastbundna\",\"w2\":\"Det fanns ingen dörr\",\"w3\":\"Vägen var blockerad av sten\"},{\"q\":\"Vart flydde de istället?\",\"a\":\"Ner i källaren\",\"w1\":\"Upp på vinden\",\"w2\":\"Ut genom fönstret\",\"w3\":\"In i skorstenen\"},{\"q\":\"Vem hittade luckan?\",\"a\":\"Mia\",\"w1\":\"Alex\",\"w2\":\"En katt\",\"w3\":\"Skuggan\"}]', 9, 60, '2025-12-03 19:33:46'),
(269, 'Kapitel 7: Under jorden', 1, 4, 1, NULL, 'De landade på ett jordgolv. Källaren var en lång tunnel. Det droppade vatten från taket. Långt borta hördes ett släpande ljud, som om någon drog en tung kedja. De hade inget val, de måste gå framåt.', '[{\"q\":\"Vad var källaren egentligen?\",\"a\":\"En tunnel\",\"w1\":\"Ett kök\",\"w2\":\"En fängelsehåla\",\"w3\":\"En grav\"},{\"q\":\"Vad hördes långt borta?\",\"a\":\"Ett släpande ljud\",\"w1\":\"Ett skrik\",\"w2\":\"Vattenfall\",\"w3\":\"Musik\"},{\"q\":\"Vad lät det som?\",\"a\":\"En tung kedja\",\"w1\":\"En säck potatis\",\"w2\":\"En kista\",\"w3\":\"En vagn\"}]', 10, 70, '2025-12-03 19:33:46'),
(270, 'Kapitel 8: Kyrkogården', 1, 4, 3, NULL, 'Tunneln ledde upp till en gammal kyrkogård. Gravstenarna stod snett och dimman låg tät. Marken började röra på sig framför dem. En hand stack upp ur jorden! De döda höll på att vakna.', '[{\"q\":\"Vart ledde tunneln?\",\"a\":\"Till en kyrkogård\",\"w1\":\"Till en strand\",\"w2\":\"Till byn\",\"w3\":\"Till en skola\"},{\"q\":\"Vad hände med marken?\",\"a\":\"Den rörde på sig\",\"w1\":\"Den sprack\",\"w2\":\"Den blev till vatten\",\"w3\":\"Den blev het\"},{\"q\":\"Vad stack upp ur jorden?\",\"a\":\"En hand\",\"w1\":\"En blomma\",\"w2\":\"En mask\",\"w3\":\"En sten\"}]', 11, 80, '2025-12-03 19:33:46'),
(271, 'Kapitel 9: Klocktornet', 1, 4, 1, NULL, 'De såg kyrkans klocktorn. \"Om vi ringer i klockan kanske någon hör oss!\" sa Alex. De klättrade upp för den rangliga stegen. Zombies samlades nedanför och försökte klättra efter.', '[{\"q\":\"Vad ville de göra?\",\"a\":\"Ringa i klockan\",\"w1\":\"Gömma sig\",\"w2\":\"Hoppa ner\",\"w3\":\"Sova\"},{\"q\":\"Hur tog de sig upp?\",\"a\":\"Klättrade på en stege\",\"w1\":\"Tog hissen\",\"w2\":\"Flög\",\"w3\":\"Sprang i trappor\"},{\"q\":\"Vad jagade dem?\",\"a\":\"Zombies\",\"w1\":\"Vargar\",\"w2\":\"Spöken\",\"w3\":\"Vampyrer\"}]', 12, 90, '2025-12-03 19:33:46'),
(272, 'Kapitel 10: Räddningen', 1, 4, 3, NULL, 'Klockan klämtade högt över skogen: DONG! DONG! Ljudet verkade skrämma monstren. De höll för öronen och sjönk tillbaka ner i jorden. Solens första strålar bröt igenom dimman. Alex och Mia var räddade.', '[{\"q\":\"Vad hände med monstren?\",\"a\":\"De sjönk ner i jorden\",\"w1\":\"De åt upp barnen\",\"w2\":\"De började dansa\",\"w3\":\"De blev till sten\"},{\"q\":\"Vad skrämde dem?\",\"a\":\"Klockans ljud\",\"w1\":\"Elden\",\"w2\":\"Vatten\",\"w3\":\"Ett svärd\"},{\"q\":\"Vad hände med vädret?\",\"a\":\"Solen gick upp\",\"w1\":\"Det började regna\",\"w2\":\"Det blev natt\",\"w3\":\"Det började snöa\"}]', 13, 100, '2025-12-03 19:33:46'),
(273, 'Kapitel 1: Lärlingen', 5, 1, 1, NULL, 'Elin var en ung magikerlärling. Hon bodde i ett högt torn tillsammans med sin mästare, den gamle trollkarlen Zardoz. Hennes uppgift var att städa laboratoriet och sortera alla flaskor.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Elin var en ung ___.\", \"word\": \"lärling\"},\r\n            {\"sentence\": \"Hon bodde i ett ___.\", \"word\": \"torn\"},\r\n            {\"sentence\": \"Hennes mästare hette ___.\", \"word\": \"Zardoz\"}\r\n        ],\r\n        \"distractors\": [\"riddare\", \"grotta\", \"kock\"]\r\n    }', 4, 10, '2025-12-03 19:33:46'),
(274, 'Kapitel 2: Den dammiga boken', 5, 1, 3, NULL, 'En dag när Elin städade hittade hon en bok som hon aldrig sett förut. Den låg gömd under en hög med papper. Boken var bunden i rött läder och hade ett lås av silver.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Elin hittade en ___ bok.\", \"word\": \"gammal\"},\r\n            {\"sentence\": \"Boken låg ___ under papper.\", \"word\": \"gömd\"},\r\n            {\"sentence\": \"Låset var gjort av ___.\", \"word\": \"silver\"}\r\n        ],\r\n        \"distractors\": [\"ny\", \"framme\", \"guld\", \"plast\"]\r\n    }', 5, 20, '2025-12-03 19:33:46'),
(275, 'Kapitel 3: Nyckeln', 5, 1, 1, NULL, 'Boken var låst, men Elin var nyfiken. Hon letade överallt efter nyckeln. Till slut såg hon något som glimmade i Zardoz tekopp. Det var en liten silvernyckel! Mästaren måste ha tappat den.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Elin var ___ på vad som stod i boken.\", \"word\": \"nyfiken\"},\r\n            {\"sentence\": \"Nyckeln låg i en ___.\", \"word\": \"tekopp\"},\r\n            {\"sentence\": \"Mästaren hade ___ nyckeln.\", \"word\": \"tappat\"}\r\n        ],\r\n        \"distractors\": [\"rädd\", \"kista\", \"gömt\", \"ätit\"]\r\n    }', 6, 30, '2025-12-03 19:33:46'),
(276, 'Kapitel 4: Formeln', 5, 1, 3, NULL, 'Elin öppnade boken. Sidorna var gula av ålder. På första sidan stod en enda formel: \"Levitations-förtrollningen\". Det var magin som fick saker att flyga! Elin hade alltid drömt om att kunna flyga.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Sidorna i boken var ___.\", \"word\": \"gula\"},\r\n            {\"sentence\": \"Formeln handlade om ___.\", \"word\": \"levitation\"},\r\n            {\"sentence\": \"Elin drömde om att ___.\", \"word\": \"flyga\"}\r\n        ],\r\n        \"distractors\": [\"vita\", \"eld\", \"simma\", \"sova\"]\r\n    }', 7, 40, '2025-12-03 19:33:46'),
(277, 'Kapitel 5: Försöket', 5, 1, 1, NULL, 'Hon bestämde sig för att prova. Hon tog sin trollstav och pekade på en stol. Hon sa de magiska orden högt och tydligt: \"Wingardium Leviosa!\". Ingenting hände. Stolen stod kvar på golvet.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Hon pekade med sin ___.\", \"word\": \"trollstav\"},\r\n            {\"sentence\": \"Hon sa orden ___ och tydligt.\", \"word\": \"högt\"},\r\n            {\"sentence\": \"Stolen stod ___.\", \"word\": \"kvar\"}\r\n        ],\r\n        \"distractors\": [\"hand\", \"tyst\", \"flög\", \"borta\"]\r\n    }', 8, 50, '2025-12-03 19:33:46'),
(278, 'Kapitel 6: Misstaget', 5, 1, 3, NULL, 'Elin försökte igen, men denna gång viftade hon för mycket med staven. En gnista flög iväg och träffade Zardoz uggla, Hoot. Ugglan började plötsligt växa! Snart var den stor som en häst.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Elin viftade för ___ med staven.\", \"word\": \"mycket\"},\r\n            {\"sentence\": \"En ___ träffade ugglan.\", \"word\": \"gnista\"},\r\n            {\"sentence\": \"Ugglan blev stor som en ___.\", \"word\": \"häst\"}\r\n        ],\r\n        \"distractors\": [\"lite\", \"boll\", \"mus\", \"hus\"]\r\n    }', 9, 60, '2025-12-03 19:33:46'),
(279, 'Kapitel 7: Kaos i tornet', 5, 1, 1, NULL, 'Den jättelika ugglan flaxade runt i rummet. Den välte hyllor och krossade flaskor. Elin gömde sig under bordet. \"Vad har jag gjort?\" tänkte hon. Hon måste hitta en mot-formel snabbt innan mästaren kom hem.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Ugglan ___ runt i rummet.\", \"word\": \"flaxade\"},\r\n            {\"sentence\": \"Den krossade många ___.\", \"word\": \"flaskor\"},\r\n            {\"sentence\": \"Elin behövde en ___.\", \"word\": \"mot-formel\"}\r\n        ],\r\n        \"distractors\": [\"gick\", \"stenar\", \"nyckel\", \"kaka\"]\r\n    }', 10, 70, '2025-12-03 19:33:46'),
(280, 'Kapitel 8: Mästaren återvänder', 5, 1, 3, NULL, 'Dörren öppnades och Zardoz klev in. Han såg den jättelika ugglan och röran på golvet. Han såg inte arg ut, bara förvånad. Han knäppte med fingrarna och ugglan kryimpte tillbaka till normal storlek.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Zardoz såg ___ ut.\", \"word\": \"förvånad\"},\r\n            {\"sentence\": \"Han knäppte med ___.\", \"word\": \"fingrarna\"},\r\n            {\"sentence\": \"Ugglan blev ___ igen.\", \"word\": \"liten\"}\r\n        ],\r\n        \"distractors\": [\"arg\", \"tårna\", \"stor\", \"osynlig\"]\r\n    }', 11, 80, '2025-12-03 19:33:46'),
(281, 'Kapitel 9: Förlåtelsen', 5, 1, 1, NULL, 'Elin kröp fram och bad om ursäkt. Hon erkände att hon tagit boken utan lov. Zardoz log vänligt. \"Magin kräver tålamod, Elin\", sa han. \"Du är inte redo för den boken än, men du har talang.\"', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Elin bad om ___.\", \"word\": \"ursäkt\"},\r\n            {\"sentence\": \"Magi kräver ___.\", \"word\": \"tålamod\"},\r\n            {\"sentence\": \"Zardoz tyckte att Elin hade ___.\", \"word\": \"talang\"}\r\n        ],\r\n        \"distractors\": [\"lov\", \"styrka\", \"tur\", \"pengar\"]\r\n    }', 12, 90, '2025-12-03 19:33:46'),
(282, 'Kapitel 10: En ny lektion', 5, 1, 3, NULL, 'Zardoz gav Elin en annan bok. Den var mindre och handlade om hur man städar med magi. \"Lär dig denna först\", sa han. Elin log. Det var kanske inte lika häftigt som att flyga, men det var en början på hennes resa som magiker.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Den nya boken handlade om ___.\", \"word\": \"städning\"},\r\n            {\"sentence\": \"Elin ___ åt uppgiften.\", \"word\": \"log\"},\r\n            {\"sentence\": \"Detta var början på hennes ___.\", \"word\": \"resa\"}\r\n        ],\r\n        \"distractors\": [\"krig\", \"grät\", \"slut\", \"vila\"]\r\n    }', 13, 100, '2025-12-03 19:33:46'),
(283, 'Kapitel 1: Larmet går', 5, 2, 3, NULL, 'Kapten Nova vaknade av ett tjutande larm på rymdstationen. Röda lampor blinkade överallt. Hon svävade snabbt ur sin säng för att se vad som hänt.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Nova vaknade av ett ___.\", \"word\": \"larm\"},\r\n            {\"sentence\": \"Röda lampor ___.\", \"word\": \"blinkade\"},\r\n            {\"sentence\": \"Hon ___ ur sängen.\", \"word\": \"svävade\"}\r\n        ],\r\n        \"distractors\": [\"sov\", \"grön\", \"gick\", \"åt\"]\r\n    }', 4, 10, '2025-12-03 19:33:46'),
(284, 'Kapitel 2: Läckan', 5, 2, 1, NULL, 'Datorn varnade för en läcka i skrovet. Luften pyste ut i rymden. Nova var tvungen att hitta hålet snabbt innan syret tog slut.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Datorn varnade för en ___.\", \"word\": \"läcka\"},\r\n            {\"sentence\": \"Luften pyste ut i ___.\", \"word\": \"rymden\"},\r\n            {\"sentence\": \"Hon måste hitta ___.\", \"word\": \"hålet\"}\r\n        ],\r\n        \"distractors\": [\"maten\", \"vatten\", \"bilen\", \"skolan\"]\r\n    }', 5, 20, '2025-12-03 19:33:46'),
(285, 'Kapitel 3: Rymddräkten', 5, 2, 3, NULL, 'Nova sprang till luftslussen. Hon tog på sig sin vita rymddräkt och hjälm. Dräkten var tung, men den skyddade henne mot kylan utanför.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Hon sprang till ___.\", \"word\": \"luftslussen\"},\r\n            {\"sentence\": \"Hon tog på sig sin ___.\", \"word\": \"rymddräkt\"},\r\n            {\"sentence\": \"Dräkten skyddade mot ___.\", \"word\": \"kylan\"}\r\n        ],\r\n        \"distractors\": [\"köket\", \"pyjamas\", \"värmen\", \"solen\"]\r\n    }', 6, 30, '2025-12-03 19:33:46'),
(286, 'Kapitel 4: Promenaden', 5, 2, 1, NULL, 'Hon öppnade dörren och klev ut. Det var alldeles tyst. Stjärnorna lyste klart i mörkret. Hon använde magnetiska skor för att gå på stationens utsida.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Det var alldeles ___.\", \"word\": \"tyst\"},\r\n            {\"sentence\": \"___ lyste klart.\", \"word\": \"Stjärnorna\"},\r\n            {\"sentence\": \"Hon använde ___ skor.\", \"word\": \"magnetiska\"}\r\n        ],\r\n        \"distractors\": [\"bullrigt\", \"lamporna\", \"vanliga\", \"snabba\"]\r\n    }', 7, 40, '2025-12-03 19:33:46'),
(287, 'Kapitel 5: Roboten Bip', 5, 2, 3, NULL, 'En liten robot kom rullande mot henne. Den hette Bip. Bip hade verktyg i sina armar. \"Jag kan hjälpa till\", pep Bip och blinkade med sitt blåa öga.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"En liten ___ kom rullande.\", \"word\": \"robot\"},\r\n            {\"sentence\": \"Bip hade ___ i armarna.\", \"word\": \"verktyg\"},\r\n            {\"sentence\": \"Bip hade ett ___ öga.\", \"word\": \"blått\"}\r\n        ],\r\n        \"distractors\": [\"hund\", \"godis\", \"rött\", \"stort\"]\r\n    }', 8, 50, '2025-12-03 19:33:46'),
(288, 'Kapitel 6: Hålet', 5, 2, 1, NULL, 'De hittade skadan. En liten meteorit hade träffat väggen. Hålet var litet men farligt. Nova tog fram en platta av metall för att laga det.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"En ___ hade träffat väggen.\", \"word\": \"meteorit\"},\r\n            {\"sentence\": \"Hålet var ___ men farligt.\", \"word\": \"litet\"},\r\n            {\"sentence\": \"Hon lagade det med ___.\", \"word\": \"metall\"}\r\n        ],\r\n        \"distractors\": [\"fågel\", \"stort\", \"papper\", \"tejp\"]\r\n    }', 9, 60, '2025-12-03 19:33:46'),
(289, 'Kapitel 7: Svetsen', 5, 2, 3, NULL, 'Bip räckte henne en lasersvets. Nova smälte fast metallplattan över hålet. Gnistor flög ut i rymden men slocknade direkt. Nu var det tätt igen.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Bip räckte henne en ___.\", \"word\": \"lasersvets\"},\r\n            {\"sentence\": \"Hon smälte fast ___.\", \"word\": \"plattan\"},\r\n            {\"sentence\": \"___ flög ut i rymden.\", \"word\": \"Gnistor\"}\r\n        ],\r\n        \"distractors\": [\"hammare\", \"tejpen\", \"Fåglar\", \"Stenar\"]\r\n    }', 10, 70, '2025-12-03 19:33:46'),
(290, 'Kapitel 8: Solstormen', 5, 2, 1, NULL, 'Plötsligt varnade datorn i hjälmen. En solstorm var på väg! Strålningen kunde vara farlig. De måste skynda sig in innan stormen nådde dem.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"En ___ var på väg.\", \"word\": \"solstorm\"},\r\n            {\"sentence\": \"___ kunde vara farlig.\", \"word\": \"Strålningen\"},\r\n            {\"sentence\": \"De måste skynda sig ___.\", \"word\": \"in\"}\r\n        ],\r\n        \"distractors\": [\"regnskur\", \"Vinden\", \"ut\", \"hem\"]\r\n    }', 11, 80, '2025-12-03 19:33:46'),
(291, 'Kapitel 9: Säkerhet', 5, 2, 3, NULL, 'Nova och Bip tog sig snabbt tillbaka till slussen. De stängde den tunga dörren precis när stormen drog förbi utanför. Stationen skakade lite, men de var säkra.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"De tog sig tillbaka till ___.\", \"word\": \"slussen\"},\r\n            {\"sentence\": \"De stängde den ___ dörren.\", \"word\": \"tunga\"},\r\n            {\"sentence\": \"Stationen ___ lite.\", \"word\": \"skakade\"}\r\n        ],\r\n        \"distractors\": [\"köket\", \"öppna\", \"flög\", \"sov\"]\r\n    }', 12, 90, '2025-12-03 19:33:46'),
(292, 'Kapitel 10: Uppdrag slutfört', 5, 2, 1, NULL, 'Nova tog av sig hjälmen och pustade ut. \"Bra jobbat, Bip\", sa hon. De tittade ut genom fönstret på den vackra jorden som snurrade där nere. Allt var lugnt igen.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Nova tog av sig ___.\", \"word\": \"hjälmen\"},\r\n            {\"sentence\": \"De tittade på ___.\", \"word\": \"jorden\"},\r\n            {\"sentence\": \"Allt var ___ igen.\", \"word\": \"lugnt\"}\r\n        ],\r\n        \"distractors\": [\"skorna\", \"månen\", \"farligt\", \"mörkt\"]\r\n    }', 13, 100, '2025-12-03 19:33:46'),
(293, 'Kapitel 1: Det stulna halsbandet', 5, 3, 1, NULL, 'Fru Rask kom inspringande på kontoret. Hon var mycket upprörd. Hennes dyra diamanthalsband var borta! Det hade försvunnit från hennes rum på Hotell Grand.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Fru Rask var ___.\", \"word\": \"upprörd\"},\r\n            {\"sentence\": \"Hennes ___ var borta.\", \"word\": \"halsband\"},\r\n            {\"sentence\": \"Det försvann från ___.\", \"word\": \"hotellet\"}\r\n        ],\r\n        \"distractors\": [\"glad\", \"skor\", \"skolan\", \"bilen\"]\r\n    }', 4, 10, '2025-12-03 19:33:46'),
(294, 'Kapitel 2: Detektiverna', 5, 3, 3, NULL, 'Sam och Alex tog genast fallet. De packade ner sina saker i ryggsäckarna. De tog med förstoringsglas, anteckningsblock och en ficklampa. De cyklade snabbt till hotellet.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Sam och Alex tog ___.\", \"word\": \"fallet\"},\r\n            {\"sentence\": \"De packade ner ___.\", \"word\": \"förstoringsglas\"},\r\n            {\"sentence\": \"De cyklade till ___.\", \"word\": \"hotellet\"}\r\n        ],\r\n        \"distractors\": [\"bussen\", \"godis\", \"hem\", \"sov\"]\r\n    }', 5, 20, '2025-12-03 19:33:46'),
(295, 'Kapitel 3: Brottsplatsen', 5, 3, 1, NULL, 'Inne på rummet var det stökigt. Lådor var utdragna och kläder låg på golvet. Fönstret stod på glänt. Alex hittade leriga fotspår på mattan.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Det var ___ på rummet.\", \"word\": \"stökigt\"},\r\n            {\"sentence\": \"Fönstret stod på ___.\", \"word\": \"glänt\"},\r\n            {\"sentence\": \"Alex hittade ___ fotspår.\", \"word\": \"leriga\"}\r\n        ],\r\n        \"distractors\": [\"rent\", \"stängt\", \"rena\", \"blöta\"]\r\n    }', 6, 30, '2025-12-03 19:33:46'),
(296, 'Kapitel 4: Ledtråden', 5, 3, 3, NULL, 'Sam tittade noga vid fönstret. Där fastnat i gardinen hängde en liten bit tyg. Den var röd och gjord av silke. \"Tjuven måste ha rivit sönder sin jacka\", sa Sam.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Sam hittade en bit ___.\", \"word\": \"tyg\"},\r\n            {\"sentence\": \"Tyget var ___.\", \"word\": \"rött\"},\r\n            {\"sentence\": \"Det kom från en ___.\", \"word\": \"jacka\"}\r\n        ],\r\n        \"distractors\": [\"papper\", \"blått\", \"sko\", \"byxa\"]\r\n    }', 7, 40, '2025-12-03 19:33:46'),
(297, 'Kapitel 5: Vittnet', 5, 3, 1, NULL, 'De gick ner till receptionen. Där stod portieren. Han hade sett en man springa ut genom bakdörren. Mannen bar på en stor väska och hade en röd jacka.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Portieren hade sett en ___.\", \"word\": \"man\"},\r\n            {\"sentence\": \"Han sprang ut genom ___.\", \"word\": \"bakdörren\"},\r\n            {\"sentence\": \"Han hade en ___ jacka.\", \"word\": \"röd\"}\r\n        ],\r\n        \"distractors\": [\"kvinna\", \"fönstret\", \"blå\", \"grön\"]\r\n    }', 8, 50, '2025-12-03 19:33:46'),
(298, 'Kapitel 6: Spåret', 5, 3, 3, NULL, 'Alex och Sam följde spåren ut i trädgården. Det hade regnat så marken var mjuk. De tydliga fotspåren ledde mot parkeringen. Där tog spåren slut.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"De följde spåren till ___.\", \"word\": \"trädgården\"},\r\n            {\"sentence\": \"Marken var ___.\", \"word\": \"mjuk\"},\r\n            {\"sentence\": \"Spåren tog ___ vid parkeringen.\", \"word\": \"slut\"}\r\n        ],\r\n        \"distractors\": [\"huset\", \"hård\", \"började\", \"vidare\"]\r\n    }', 9, 60, '2025-12-03 19:33:46'),
(299, 'Kapitel 7: Bilen', 5, 3, 1, NULL, 'På parkeringen stod en gammal bil. Motorn var fortfarande varm. I baksätet såg Alex en röd jacka! Men bilen var låst. De skrev upp registreringsnumret.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Motorn var ___.\", \"word\": \"varm\"},\r\n            {\"sentence\": \"I baksätet låg en ___.\", \"word\": \"jacka\"},\r\n            {\"sentence\": \"Bilen var ___.\", \"word\": \"låst\"}\r\n        ],\r\n        \"distractors\": [\"kall\", \"hatt\", \"öppen\", \"borta\"]\r\n    }', 10, 70, '2025-12-03 19:33:46'),
(300, 'Kapitel 8: Ägaren', 5, 3, 3, NULL, 'De ringde polisen och berättade om bilen. Polisen kom snabbt. De kollade vem som ägde bilen. Det var hotellchefen! Han hade jobbat där i tio år.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"De ringde ___.\", \"word\": \"polisen\"},\r\n            {\"sentence\": \"Bilen ägdes av ___.\", \"word\": \"hotellchefen\"},\r\n            {\"sentence\": \"Han hade jobbat i ___ år.\", \"word\": \"tio\"}\r\n        ],\r\n        \"distractors\": [\"mamman\", \"tjuven\", \"två\", \"fem\"]\r\n    }', 11, 80, '2025-12-03 19:33:46'),
(301, 'Kapitel 9: Avslöjandet', 5, 3, 1, NULL, 'Polisen öppnade bilen. I jackfickan låg halsbandet! Hotellchefen kom ut och såg skamsen ut. Han erkände att han tagit halsbandet för att han behövde pengar.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Halsbandet låg i ___.\", \"word\": \"fickan\"},\r\n            {\"sentence\": \"Chefen såg ___ ut.\", \"word\": \"skamsen\"},\r\n            {\"sentence\": \"Han behövde ___.\", \"word\": \"pengar\"}\r\n        ],\r\n        \"distractors\": [\"väskan\", \"glad\", \"mat\", \"hjälp\"]\r\n    }', 12, 90, '2025-12-03 19:33:46'),
(302, 'Kapitel 10: Belöningen', 5, 3, 3, NULL, 'Fru Rask blev överlycklig när hon fick tillbaka sitt halsband. Hon bjöd Sam och Alex på tårta i hotellets finaste matsal. \"Ni är stadens bästa detektiver!\" sa hon.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Fru Rask blev ___.\", \"word\": \"överlycklig\"},\r\n            {\"sentence\": \"Hon bjöd på ___.\", \"word\": \"tårta\"},\r\n            {\"sentence\": \"De kallades ___ detektiver.\", \"word\": \"bästa\"}\r\n        ],\r\n        \"distractors\": [\"arg\", \"vatten\", \"dåliga\", \"små\"]\r\n    }', 13, 100, '2025-12-03 19:33:46'),
(303, 'Kapitel 1: Fullmånen', 5, 4, 1, NULL, 'Det var natt i den lilla byn Mörkved. Fullmånen lyste stor och gul på himlen. Alla bybor hade låst sina dörrar. De visste vad som väntade där ute i mörkret.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Det var ___ i byn.\", \"word\": \"natt\"},\r\n            {\"sentence\": \"___ lyste på himlen.\", \"word\": \"Fullmånen\"},\r\n            {\"sentence\": \"Dörrarna var ___.\", \"word\": \"låsta\"}\r\n        ],\r\n        \"distractors\": [\"dag\", \"Solen\", \"öppna\", \"trasiga\"]\r\n    }', 4, 10, '2025-12-03 19:33:46'),
(304, 'Kapitel 2: Ylandet', 5, 4, 3, NULL, 'Ett hemskt ljud hördes från skogen. Det var ett långt, ylande läte. En varg! Men det lät inte som en vanlig varg. Det lät som ett monster.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Ett ___ ljud hördes.\", \"word\": \"hemskt\"},\r\n            {\"sentence\": \"Det kom från ___.\", \"word\": \"skogen\"},\r\n            {\"sentence\": \"Det lät som ett ___.\", \"word\": \"monster\"}\r\n        ],\r\n        \"distractors\": [\"vackert\", \"havet\", \"fågel\", \"barn\"]\r\n    }', 5, 20, '2025-12-03 19:33:46'),
(305, 'Kapitel 3: Tassavtrycket', 5, 4, 1, NULL, 'Dagen efter gick Max och Lisa ut. De hittade ett spår i leran. Det var ett tassavtryck, men det var enormt stort. Det hade långa klor.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"De hittade ett ___.\", \"word\": \"spår\"},\r\n            {\"sentence\": \"Det var ___ stort.\", \"word\": \"enormt\"},\r\n            {\"sentence\": \"Det hade långa ___.\", \"word\": \"klor\"}\r\n        ],\r\n        \"distractors\": [\"sko\", \"litet\", \"skor\", \"tänder\"]\r\n    }', 6, 30, '2025-12-03 19:33:46'),
(306, 'Kapitel 4: Gamla Greta', 5, 4, 3, NULL, 'De gick till Gamla Greta som bodde i utkanten av byn. Hon visste allt om monster. \"Det är en varulv\", viskade hon. \"Den som blir biten förvandlas vid fullmåne.\"', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Greta visste allt om ___.\", \"word\": \"monster\"},\r\n            {\"sentence\": \"Det är en ___.\", \"word\": \"varulv\"},\r\n            {\"sentence\": \"Man förvandlas vid ___.\", \"word\": \"fullmåne\"}\r\n        ],\r\n        \"distractors\": [\"blommor\", \"vampyr\", \"solsken\", \"jul\"]\r\n    }', 7, 40, '2025-12-03 19:33:46'),
(307, 'Kapitel 5: Silvret', 5, 4, 1, NULL, '\"Bara silver kan stoppa den\", sa Greta. Max tittade på sin ring. Den var gjord av silver. \"Vi kan smälta ner den\", sa han modigt.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Bara ___ kan stoppa den.\", \"word\": \"silver\"},\r\n            {\"sentence\": \"Max hade en ___.\", \"word\": \"ring\"},\r\n            {\"sentence\": \"Han sa det ___.\", \"word\": \"modigt\"}\r\n        ],\r\n        \"distractors\": [\"guld\", \"boll\", \"rädd\", \"tyst\"]\r\n    }', 8, 50, '2025-12-03 19:33:46'),
(308, 'Kapitel 6: Smedjan', 5, 4, 3, NULL, 'De smög till smedjan mitt i natten. Smeden sov. De tände elden och smälte ringen. De formade det smälta silvret till en pilspets.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"De smög till ___.\", \"word\": \"smedjan\"},\r\n            {\"sentence\": \"Smeden ___.\", \"word\": \"sov\"},\r\n            {\"sentence\": \"De gjorde en ___.\", \"word\": \"pilspets\"}\r\n        ],\r\n        \"distractors\": [\"skolan\", \"åt\", \"boll\", \"ring\"]\r\n    }', 9, 60, '2025-12-03 19:33:46'),
(309, 'Kapitel 7: Fällan', 5, 4, 1, NULL, 'De grävde en grop i skogen och täckte den med grenar. De hängde en köttbit i ett träd ovanför som bete. Sedan gömde de sig bakom en sten och väntade.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"De grävde en ___.\", \"word\": \"grop\"},\r\n            {\"sentence\": \"De använde kött som ___.\", \"word\": \"bete\"},\r\n            {\"sentence\": \"De ___ sig bakom en sten.\", \"word\": \"gömde\"}\r\n        ],\r\n        \"distractors\": [\"hög\", \"leksak\", \"visade\", \"sprang\"]\r\n    }', 10, 70, '2025-12-03 19:33:46');
INSERT INTO `tasks` (`t_id`, `t_name`, `t_type_fk`, `t_genre_fk`, `t_teacher_fk`, `t_class_fk`, `t_text`, `t_questions`, `t_level_fk`, `t_xp`, `t_created`) VALUES
(310, 'Kapitel 8: Monstret kommer', 5, 4, 3, NULL, 'De hörde knakande grenar. En stor, mörk skugga kom fram. Ögonen lyste gula. Varulven luktade på köttet. Den tog ett steg mot fällan.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Grenarna ___.\", \"word\": \"knakade\"},\r\n            {\"sentence\": \"Ögonen lyste ___.\", \"word\": \"gula\"},\r\n            {\"sentence\": \"Varulven tog ett ___.\", \"word\": \"steg\"}\r\n        ],\r\n        \"distractors\": [\"tystnade\", \"blåa\", \"hopp\", \"skutt\"]\r\n    }', 11, 80, '2025-12-03 19:33:46'),
(311, 'Kapitel 9: Fångad', 5, 4, 1, NULL, 'Varulven trampade på grenarna och föll ner i gropen! Den ylade av ilska. Lisa sköt sin pil med silverspetsen. Den träffade varulven i benet. Monstret skrek till och föll ihop.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Varulven föll i ___.\", \"word\": \"gropen\"},\r\n            {\"sentence\": \"Den ylade av ___.\", \"word\": \"ilska\"},\r\n            {\"sentence\": \"Pilen träffade ___.\", \"word\": \"benet\"}\r\n        ],\r\n        \"distractors\": [\"fällan\", \"glädje\", \"armen\", \"huvudet\"]\r\n    }', 12, 90, '2025-12-03 19:33:46'),
(312, 'Kapitel 10: Förvandlingen', 5, 4, 3, NULL, 'Pälsen försvann och klorna drogs in. Där nere låg inte ett monster, utan bagaren i byn! Förbannelsen var bruten. Han var människa igen. Byn var räddad.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Pälsen ___.\", \"word\": \"försvann\"},\r\n            {\"sentence\": \"Det var ___.\", \"word\": \"bagaren\"},\r\n            {\"sentence\": \"Han var ___ igen.\", \"word\": \"människa\"}\r\n        ],\r\n        \"distractors\": [\"växte\", \"smeden\", \"varg\", \"monster\"]\r\n    }', 13, 100, '2025-12-03 19:33:46'),
(313, 'Kapitel 1: Dinosauriernas tid', 5, 5, 1, NULL, 'För miljoner år sedan levde dinosaurier på jorden. Vissa åt växter och andra åt kött. Den mest kända köttätaren var Tyrannosaurus Rex. De dog ut när en stor meteorit krockade med jorden.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Dinosaurier levde för ___ år sedan.\", \"word\": \"miljoner\"},\r\n            {\"sentence\": \"Vissa åt ___.\", \"word\": \"växter\"},\r\n            {\"sentence\": \"De dog ut av en ___.\", \"word\": \"meteorit\"}\r\n        ],\r\n        \"distractors\": [\"tusen\", \"godis\", \"bil\", \"båt\"]\r\n    }', 4, 10, '2025-12-03 19:33:46'),
(314, 'Kapitel 2: Vikingarna', 5, 5, 3, NULL, 'Vikingarna levde i Norden för tusen år sedan. De var duktiga sjöfarare och byggde snabba skepp. De reste långt bort för att handla och ibland plundra. Deras bokstäver kallades runor.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Vikingarna var duktiga ___.\", \"word\": \"sjöfarare\"},\r\n            {\"sentence\": \"De byggde snabba ___.\", \"word\": \"skepp\"},\r\n            {\"sentence\": \"Deras bokstäver hette ___.\", \"word\": \"runor\"}\r\n        ],\r\n        \"distractors\": [\"bönder\", \"bilar\", \"klimp\", \"sol\"]\r\n    }', 5, 20, '2025-12-03 19:33:46'),
(315, 'Kapitel 3: Rymden', 5, 5, 1, NULL, 'Jorden är en planet som snurrar runt solen. Månen snurrar runt jorden. Det finns åtta planeter i vårt solsystem. Den största planeten heter Jupiter och den är gjord av gas.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Jorden snurrar runt ___.\", \"word\": \"solen\"},\r\n            {\"sentence\": \"Det finns ___ planeter.\", \"word\": \"åtta\"},\r\n            {\"sentence\": \"Jupiter är gjord av ___.\", \"word\": \"gas\"}\r\n        ],\r\n        \"distractors\": [\"månen\", \"hundra\", \"sten\", \"vatten\"]\r\n    }', 6, 30, '2025-12-03 19:33:46'),
(316, 'Kapitel 4: Havet', 5, 5, 3, NULL, 'Havet täcker större delen av jordens yta. Det är saltvatten. I havet lever världens största djur, blåvalen. Den kan bli 30 meter lång och väga lika mycket som 25 elefanter.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Havet består av ___.\", \"word\": \"saltvatten\"},\r\n            {\"sentence\": \"Världens största djur är ___.\", \"word\": \"blåvalen\"},\r\n            {\"sentence\": \"Den kan bli 30 ___ lång.\", \"word\": \"meter\"}\r\n        ],\r\n        \"distractors\": [\"sötvatten\", \"hajar\", \"kilometer\", \"mil\"]\r\n    }', 7, 40, '2025-12-03 19:33:46'),
(317, 'Kapitel 5: Insekter', 5, 5, 1, NULL, 'Insekter är små djur med sex ben. Många insekter har vingar och kan flyga. Bin är viktiga insekter eftersom de pollinerar blommor så att vi får frukt och bär. Myror lever tillsammans i stora samhällen.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Insekter har ___ ben.\", \"word\": \"sex\"},\r\n            {\"sentence\": \"Bin ___ blommor.\", \"word\": \"pollinerar\"},\r\n            {\"sentence\": \"Myror lever i ___.\", \"word\": \"samhällen\"}\r\n        ],\r\n        \"distractors\": [\"fyra\", \"äter\", \"hus\", \"ensamma\"]\r\n    }', 8, 50, '2025-12-03 19:33:46'),
(318, 'Kapitel 6: Kroppen', 5, 5, 3, NULL, 'Människokroppen består av många delar. Skelettet ger kroppen stadga och skyddar organen. Hjärtat pumpar runt blodet i kroppen. Hjärnan styr allt vi gör och tänker.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"___ ger kroppen stadga.\", \"word\": \"Skelettet\"},\r\n            {\"sentence\": \"Hjärtat pumpar ___.\", \"word\": \"blod\"},\r\n            {\"sentence\": \"___ styr allt vi gör.\", \"word\": \"Hjärnan\"}\r\n        ],\r\n        \"distractors\": [\"Skinnet\", \"vatten\", \"Magen\", \"Foten\"]\r\n    }', 9, 60, '2025-12-03 19:33:46'),
(319, 'Kapitel 7: Elektricitet', 5, 5, 1, NULL, 'Vi använder elektricitet varje dag till lampor och datorer. Strömmen kan komma från vindkraftverk, solceller eller vattenkraft. Metaller som koppar leder ström bra, medan plast stoppar den.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Vi använder ___ till lampor.\", \"word\": \"elektricitet\"},\r\n            {\"sentence\": \"Ström kan komma från ___.\", \"word\": \"solceller\"},\r\n            {\"sentence\": \"Koppar ___ ström.\", \"word\": \"leder\"}\r\n        ],\r\n        \"distractors\": [\"eld\", \"träd\", \"stoppar\", \"äter\"]\r\n    }', 10, 70, '2025-12-03 19:33:46'),
(320, 'Kapitel 8: Vulkaner', 5, 5, 3, NULL, 'En vulkan är ett berg som kan spruta eld. Inne i jorden är det mycket varmt och stenarna smälter till magma. När magman kommer ut kallas den lava. När lavan stelnar blir den till ny sten.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Inne i jorden finns ___.\", \"word\": \"magma\"},\r\n            {\"sentence\": \"Utanför kallas det ___.\", \"word\": \"lava\"},\r\n            {\"sentence\": \"Lavan stelnar till ___.\", \"word\": \"sten\"}\r\n        ],\r\n        \"distractors\": [\"vatten\", \"eld\", \"sand\", \"glas\"]\r\n    }', 11, 80, '2025-12-03 19:33:46'),
(321, 'Kapitel 9: Regnskogen', 5, 5, 1, NULL, 'Regnskogen är viktig för hela jorden. Träden där tar upp koldioxid och släpper ut syre som vi andas. Det är alltid varmt och fuktigt i regnskogen. Tyvärr huggs många träd ner.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Träden släpper ut ___.\", \"word\": \"syre\"},\r\n            {\"sentence\": \"Det är ___ i regnskogen.\", \"word\": \"fuktigt\"},\r\n            {\"sentence\": \"Många träd ___ ner.\", \"word\": \"huggs\"}\r\n        ],\r\n        \"distractors\": [\"rök\", \"kallt\", \"växer\", \"brinner\"]\r\n    }', 12, 90, '2025-12-03 19:33:46'),
(322, 'Kapitel 10: Framtiden', 5, 5, 3, NULL, 'I framtiden kommer tekniken att utvecklas ännu mer. Vi kanske har robotar som hjälper oss hemma. Bilar kanske kör av sig själva. Det är viktigt att vi tar hand om miljön så att jorden mår bra i framtiden.', '{\r\n        \"gaps\": [\r\n            {\"sentence\": \"Tekniken kommer att ___.\", \"word\": \"utvecklas\"},\r\n            {\"sentence\": \"Bilar kanske kör ___.\", \"word\": \"själva\"},\r\n            {\"sentence\": \"Vi måste ta hand om ___.\", \"word\": \"miljön\"}\r\n        ],\r\n        \"distractors\": [\"stanna\", \"aldrig\", \"pengarna\", \"rymden\"]\r\n    }', 13, 100, '2025-12-03 19:33:46'),
(330, 'Fredrichs? Raderad Lärare', 1, 3, 1, 7, 'Raderad Lärare Raderad Lärare Raderad Lärare Raderad Lärare', '[{\"q\":\"Raderad Lärare\",\"a\":\"Raderad Lärare\",\"w1\":\"Raderad Lärare\",\"w2\":\"Raderad Lärare\",\"w3\":\"Raderad Lärare\"}]', 4, 10, '2025-12-04 09:42:22');

-- --------------------------------------------------------

--
-- Table structure for table `task_levels`
--

CREATE TABLE `task_levels` (
  `tl_id` int NOT NULL,
  `tl_name` varchar(255) NOT NULL,
  `tl_level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_levels`
--

INSERT INTO `task_levels` (`tl_id`, `tl_name`, `tl_level`) VALUES
(4, 'Nivå 1', 1),
(5, 'Nivå 2', 2),
(6, 'Nivå 3', 3),
(7, 'Nivå 4', 4),
(8, 'Nivå 5', 5),
(9, 'Nivå 6', 6),
(10, 'Nivå 7', 7),
(11, 'Nivå 8', 8),
(12, 'Nivå 9', 9),
(13, 'Nivå 10', 10);

-- --------------------------------------------------------

--
-- Table structure for table `task_types`
--

CREATE TABLE `task_types` (
  `tt_id` int NOT NULL,
  `tt_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_types`
--

INSERT INTO `task_types` (`tt_id`, `tt_name`) VALUES
(1, 'Flervalsfrågor'),
(2, 'Sortering'),
(3, 'Para ihop'),
(4, 'Sant/Falskt'),
(5, 'Textluckor');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `u_id` int NOT NULL,
  `u_name` varchar(255) NOT NULL,
  `u_fname` varchar(255) NOT NULL,
  `u_lname` varchar(255) NOT NULL,
  `u_email` varchar(255) NOT NULL,
  `u_password` varchar(255) NOT NULL,
  `u_lastlogin` datetime DEFAULT NULL,
  `u_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_isactive` tinyint(1) NOT NULL,
  `u_role_fk` int NOT NULL,
  `u_xp` int NOT NULL DEFAULT '0' COMMENT 'Total XP',
  `u_level` int NOT NULL DEFAULT '1' COMMENT 'Nuvarande nivå',
  `u_class_fk` int DEFAULT NULL,
  `u_progress_speed_fk` int NOT NULL DEFAULT '1',
  `u_theme` varchar(50) NOT NULL DEFAULT 'default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`u_id`, `u_name`, `u_fname`, `u_lname`, `u_email`, `u_password`, `u_lastlogin`, `u_created`, `u_isactive`, `u_role_fk`, `u_xp`, `u_level`, `u_class_fk`, `u_progress_speed_fk`, `u_theme`) VALUES
(1, 'Fredrich', 'Fredrich', 'Kjellberg', 'fredrich.kjellberg@gmail.com', '$2y$10$0WFU5QqG2nSxUyisfElMsuWdYi8e5omc5hE6D1LpWva54w9kaYWGi', '2025-12-04 09:42:31', '2025-12-04 10:59:22', 1, 3, 1840, 6, NULL, 4, 'fantasy'),
(2, 'oliver.jansson', 'Oliver', 'Jansson', 'oliver.jansson@testelev.fi', '$2y$10$kG7eByRy4MlzCLyMYqteweVrH/0ysKxJ6i0GneNGhX8P7TY3Q3vbO', '2025-11-19 15:55:57', '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(3, 'Testlärare', 'Test', 'Lärare', 'test@larare.fi', '$2y$10$N4g3hvhtORwrpo7r0eCXjutqs4yhICLX5jlGiaTSrDTYs4ALqLEqu', '2025-11-27 10:13:45', '2025-11-28 17:23:22', 1, 2, 0, 1, NULL, 1, 'fantasy'),
(4, 'elin.lindqvist', 'Elin', 'Lindqvist', 'elin.lindqvist@testelev.fi', '$2y$10$871pUclYUUWqPOioPIRpouD0Wfw3lF50qk/lNEe5czP1WHCKNzv/e', '2025-11-17 19:23:52', '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(5, 'liam.bengtsson', 'Liam', 'Bengtsson', 'liam.bengtsson@testelev.fi', '$2y$10$LH4k30uREQ/2Z68kgQ./9.oYS4WRzGTyzziLBLMkkXa3LhTf1VCNu', '2025-11-17 20:18:36', '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(6, 'sam.mattsson', 'Sam', 'Mattsson', 'sam.mattsson@testelev.fi', '$2y$10$WQfJJFcVdYBVrxIchAmaRuOxKR5t.1sJ4H9fjhTXeBv1J1e3MuJyS', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(7, 'benjamin.lindqvist', 'Benjamin', 'Lindqvist', 'benjamin.lindqvist@testelev.fi', '$2y$10$oYeEDauRHA5jbOYBKvlb7eSRK.1HczJxN9CIH/lgQASKND6L0BZXq', '2025-11-19 12:18:42', '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(8, 'ellen.olsson', 'Ellen', 'Olsson', 'ellen.olsson@testelev.fi', '$2y$10$jctYUH02JLroyctqDGtyOuIxXZMEwfLYUF1BTDHPvSyVwhUKTgQBO', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(9, 'ida.lindgren', 'Ida', 'Lindgren', 'ida.lindgren@testelev.fi', '$2y$10$iDrZmnAw.qGOM/qLjhgR3eBVy9ubBzH6sPnsPb9BCyhseMWKHBoea', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(10, 'ida.hansson', 'Ida', 'Hansson', 'ida.hansson@testelev.fi', '$2y$10$am.xI7euzSZvXyBo4nhvpePqyS1w0Chb1H3GwHVnVBfzSUnXqyNUm', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(11, 'lucas.karlsson', 'Lucas', 'Karlsson', 'lucas.karlsson@testelev.fi', '$2y$10$DrXQCZMMzyjkd8dFOOsV1OBP3/f.9atJZX02stoR6BmNPjQgDbXP.', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(12, 'wilma.lindstrom', 'Wilma', 'Lindström', 'wilma.lindstrom@testelev.fi', '$2y$10$GxSU6Jf2Pl/EarENT2utv.jIoJvVEcixZCHgOc9zzSIpqovhlMdT.', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(13, 'william.hansson', 'William', 'Hansson', 'william.hansson@testelev.fi', '$2y$10$uTZz/rJw3QFnV.W4dGg7seWtnVAuKemfp7nx29SsVM3BnuwUakrHW', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(15, 'axel.eriksson', 'Axel', 'Eriksson', 'axel.eriksson@testelev.fi', '$2y$10$3sS5bO57ONJQvx8LMZjT/eH/ZhmHi/UtT9jGyp39wjgD7wk2.8YGO', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(16, 'oliver.jakobsson', 'Oliver', 'Jakobsson', 'oliver.jakobsson@testelev.fi', '$2y$10$Cg/0YmmJZ8DMhOIwsiSe/uf9X8nASZ0H4li.I28Jt.RUGvQoO41Xe', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(17, 'alice.jonsson', 'Alice', 'Jönsson', 'alice.jonsson@testelev.fi', '$2y$10$7Pr9AJeNc8XXtk39h8J/COZanMBbFB3QwDzQ6kT1VnilUOZq2KpP2', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, NULL, 1, 'fantasy'),
(18, 'bjorn.lindqvist', 'Björn', 'Lindqvist', 'bjorn.lindqvist@testelev.fi', '$2y$10$UDnA5sZTLT3WmeGdPAFsm.s3f8wJx7jSdPa9c6bXpL5dxCtflDDjO', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(19, 'alma.jakobsson', 'Alma', 'Jakobsson', 'alma.jakobsson@testelev.fi', '$2y$10$XDvkAxfMCEwNp7KEcwlBQeGZ7Z0naNOd0Uwxfi8WAtT9MyT6DcVXy', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(20, 'emilia.lindstrom', 'Emilia', 'Lindström', 'emilia.lindstrom@testelev.fi', '$2y$10$ZIc4G6C5.sy7fCdXTxEbgOTiaJhBag/KF1WEN.ykY0SoSTVCW1KZG', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(21, 'noah.larsson', 'Noah', 'Larsson', 'noah.larsson@testelev.fi', '$2y$10$tx3kreBrBxnAvfsY.31/iuO7JcV6aS0eIrFXGSUVnQ3RP2OdTFMKa', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(22, 'elin.jonsson', 'Elin', 'Jonsson', 'elin.jonsson@testelev.fi', '$2y$10$xZ46p8I8u4YcRHysKLUHJeRMe.SWFo9suxM3bycU3hFZDCcKXbudS', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(23, 'william.lindberg', 'William', 'Lindberg', 'william.lindberg@testelev.fi', '$2y$10$Po3afj0KGrUj087dOyLiE.mREvhIYGd6iliEwaSKRECu63mlVrMCG', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(24, 'alma.olofsson', 'Alma', 'Olofsson', 'alma.olofsson@testelev.fi', '$2y$10$gq1VtB3cj/ZEKm0EyxP3R.bCGJoBl.XWwmHd/xGRxUen9u2qs5lKq', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(25, 'colin.pettersson', 'Colin', 'Pettersson', 'colin.pettersson@testelev.fi', '$2y$10$njDXTmcvctJbxGFSLqV/.edjYkWG91ZojEFJ1kAxH8mRYo1inebrq', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(26, 'noah.persson', 'Noah', 'Persson', 'noah.persson@testelev.fi', '$2y$10$EBOK.H.9cWPk.zjKryumYOOHghESJEk2EmfeabE8ZEyRBMpPdQVf2', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(27, 'alexander.nilsson', '&lt;script&gt;alert(&#039;HACKAD!&#039;)&lt;/script&gt;', 'Nilsson', 'alexander.nilsson@testelev.fi', '$2y$10$Gydlz8531yGkmuL0E.Zs5uF.jOcXDgDiyFsS9vIYDHRzes1bZwHUa', NULL, '2025-12-03 20:14:12', 1, 1, 0, 1, 3, 1, 'fantasy'),
(28, 'hedda.lindgren', 'Hedda', 'Lindgren', 'hedda.lindgren@testelev.fi', '$2y$10$rJWMo3wqDVTaMiQZHUPsveP6459hFlex0hbuXDVCOYrhJrkRNec5K', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(29, 'benjamin.jonsson', 'Benjamin', 'Jonsson', 'benjamin.jonsson@testelev.fi', '$2y$10$wAiG1sqk3dfezyQDSJyx0.S4fPGRXHC1usi1d/0OJ9/8wrdcjsp/a', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(30, 'noah.jansson', 'Noah', 'Jansson', 'noah.jansson@testelev.fi', '$2y$10$Qif1b7pqeKYBctph3onPQevTw81zJC2LoqJUPdbdA.IzrcyYgN8yq', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(31, 'olivia.olsson', 'Olivia', 'Olsson', 'olivia.olsson@testelev.fi', '$2y$10$R8eNVxa.dJDgBqvJh7TyCO5atW9ROxiL3q5.8ie8eEVS/zZVWJzAO', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(32, 'elias.lundgren', 'Elias', 'Lundgren', 'elias.lundgren@testelev.fi', '$2y$10$uUV63ttx1cw2sly9pteMCu7QpZFhh1JM8pB2SWh6uzeOxJ6SPbyaa', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(33, 'elsa.lindberg', 'Elsa', 'Lindberg', 'elsa.lindberg@testelev.fi', '$2y$10$xnQBctDQQ/p3qa4QaohsMu.iUBaRemeEFg/r8sM.r607jWX44dVrS', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(34, 'ellen.svensson', 'Ellen', 'Svensson', 'ellen.svensson@testelev.fi', '$2y$10$fJp4ICf2AJEZr9cNkaC1ku0gGRbjTGnHkRzENxsdiL.XU5zFAM58W', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(35, 'alma.nilsson', 'Alma', 'Nilsson', 'alma.nilsson@testelev.fi', '$2y$10$5Fa.Qg3uwNhQTYyRc8lKIec/UWOiUzV7Vax7EYHNxAB91af30sT3.', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(36, 'ida.lindberg', 'Ida', 'Lindberg', 'ida.lindberg@testelev.fi', '$2y$10$GlP.5bBB4FT1Qqt0DsRIguiVPxHIFkFphEG6c/XHlkirtpKxyJYIu', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(38, 'filippa.bengtsson', 'Filippa', 'Bengtsson', 'filippa.bengtsson@testelev.fi', '$2y$10$llrZZv6wK4qe5kkW5vM9IOvBHZQ8ljCvz02dKf530hdXCa9PAU1Am', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(39, 'hugo.jakobsson', 'Hugo', 'Jakobsson', 'hugo.jakobsson@testelev.fi', '$2y$10$m.9rx0AlER7jRiPNHmah7u4nYKPFiZQCzsuIJ1SrGIPZlnOD3KKqK', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(40, 'ester.larsson', 'Ester', 'Larsson', 'ester.larsson@testelev.fi', '$2y$10$HF19gZD40dy1u4SLyXY/PeuWL7vksFNEbVpuRWqy2yg./sgWjdPBG', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(41, 'max.lindqvist', 'Max', 'Lindqvist', 'max.lindqvist@testelev.fi', '$2y$10$yPmLh1m2NRXlW6knbQduT.cytaC2bOlaVv5cUXSU4TRYKhUnAOz.y', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(42, 'arvid.axelsson', 'Arvid', 'Axelsson', 'arvid.axelsson@testelev.fi', '$2y$10$9zSlAiwPP1CfsCiJWfO.WOQq9OgGbI2.JYNfiaHE408CcQrrMgANy', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(43, 'oliver.lundberg', 'Oliver', 'Lundberg', 'oliver.lundberg@testelev.fi', '$2y$10$iy8zwWIAQaYN/a2F9CIwRucBI2zsUpKWMbAbCtNXIzHhI8cEOI.j.', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(44, 'maja.jansson', 'Maja', 'Jansson', 'maja.jansson@testelev.fi', '$2y$10$C95CouQJxntu1jkD9QmrdOmjUxE/0A4b5DLzUO6GmLfbHR39r16vm', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(45, 'nils.lundberg', 'Nils', 'Lundberg', 'nils.lundberg@testelev.fi', '$2y$10$q7pCC41vpHTkHIyX.HytG./BL8YiHdh/HS0ETvccje.8P6RclEefG', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(46, 'max.jakobsson', 'Max', 'Jakobsson', 'max.jakobsson@testelev.fi', '$2y$10$ufcvwVD9Bse7q4qwcVigeeqYqkSSRTJ9nSKOs5RyDPmp8BdS0VOo6', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(47, 'ella.bengtsson', 'Ella', 'Bengtsson', 'ella.bengtsson@testelev.fi', '$2y$10$wUZIs0YyQlMIuyh5QMKJfeuW8AAQX7yW2/hhLqc3QuuS69C8yjB8G', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(48, 'oscar.lindberg', 'Oscar', 'Lindberg', 'oscar.lindberg@testelev.fi', '$2y$10$4kKTGI.mdVt7hevT240/3eHVaygEuw1V5dBlJ6x26BiyK1XykxoPm', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(49, 'astrid.karlsson', 'Astrid', 'Karlsson', 'astrid.karlsson@testelev.fi', '$2y$10$0P7dBebaCqp.h4va6/SJI.tW8uZU8oqLkKcd6MMwbILJROJmzb1VS', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(50, 'liam.nilsson', 'Liam', 'Nilsson', 'liam.nilsson@testelev.fi', '$2y$10$3Of.gR94rGDqmXTwjYFA1ePtJXxZYGZrzkCfiFNe8J2ndeE14CTTm', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(51, 'ida.axelsson', 'Ida', 'Axelsson', 'ida.axelsson@testelev.fi', '$2y$10$STIbD5/EBZW2HGiBESWXFOx63jVznhIEDMI4P3iQp5E1bG8b6b2ea', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(52, 'max.berg', 'Max', 'Berg', 'max.berg@testelev.fi', '$2y$10$wo7f9LozUPYuxw.kKNdnNuROrxeErvGA8wIw2ULmBhYVgHdBJ8OW6', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(53, 'josef.lindstrom', 'Josef', 'Lindström', 'josef.lindstrom@testelev.fi', '$2y$10$UBUxfUahGmGw6IQmVnPgn.IyCXRl5gRhZlx2LYAIU6qhOjD2uk03K', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(54, 'nils.lindstrom', 'Nils', 'Lindström', 'nils.lindstrom@testelev.fi', '$2y$10$pK6idhVyiL6ClXgQ0/XyBuSoPkXOU5esvnJ8kxeVPrPYN1lwLzydW', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 1, 1, 'fantasy'),
(55, 'isak.gustafsson', 'Isak', 'Gustafsson', 'isak.gustafsson@testelev.fi', '$2y$10$V91HIkDA2ANOzwurJ7DvzuCONlBzrzQO9zf6UYcuBz16q957.eEs6', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 2, 1, 'fantasy'),
(56, 'ester.mattsson', 'Ester', 'Mattsson', 'ester.mattsson@testelev.fi', '$2y$10$DSMziFm.jWxga8a2H1WJF.XpZ1SRLdgCokttMm3NdeJIE7A0rBsUC', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(57, 'casper.axelsson', 'Casper', 'Axelsson', 'casper.axelsson@testelev.fi', '$2y$10$Ue3m77.QMT4aTDfp54NjcuR/4XPr5NBw3mo9P1PRofY998JDHaNv.', NULL, '2025-11-28 17:23:22', 1, 1, 0, 1, 3, 1, 'fantasy'),
(58, 'TestElev', 'Test', 'Elev', 'test@elev.fi', '$2y$10$dKrTPCEMDlXVZ7xjO96YJ.pwpP28XiVkNqNostvksi7U.mf0qSBTu', '2025-12-03 21:36:49', '2025-12-03 19:36:49', 1, 1, 150, 2, NULL, 1, 'fantasy'),
(59, 'TestAdmin', 'Test', 'Admin', 'test@admin.fi', '$2y$10$sov.dQ6eImhnWTPG/aAf1.2Z6n4aDPD.hgtAgKtqPbaMl2fWCze0O', NULL, '2025-11-28 17:23:22', 1, 3, 0, 1, NULL, 1, 'fantasy'),
(60, 'Level0', 'Level', 'Noll', 'level@0.fi', '$2y$10$QxCVVjrTu6Hawv4hJaRzM.fsPm71zGixojMsv3YYMpmcCDRlAUEPm', '2025-11-26 14:39:47', '2025-11-28 17:23:22', 1, 1, 0, 1, NULL, 1, 'fantasy'),
(61, 'Mithras', 'Mithras', 'Kjellberg', 'mithras.kjellberg@gmail.com', '$2y$10$BscH4xlT9Ints8im08p84.Cx/l48UhpfvG/PvN75aQD7HZ4uZxOAu', '2025-11-28 21:20:09', '2025-12-02 11:21:10', 1, 1, 1100, 5, 7, 1, 'fantasy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`a_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `c_progress_speed_fk` (`c_progress_speed_fk`),
  ADD KEY `c_teacher_fk` (`c_teacher_fk`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`g_id`);

--
-- Indexes for table `level_config`
--
ALTER TABLE `level_config`
  ADD PRIMARY KEY (`lc_level`);

--
-- Indexes for table `progress_speeds`
--
ALTER TABLE `progress_speeds`
  ADD PRIMARY KEY (`ps_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `student_achievements`
--
ALTER TABLE `student_achievements`
  ADD PRIMARY KEY (`sa_id`),
  ADD KEY `sa_student_fk` (`sa_student_fk`),
  ADD KEY `sa_achievement_fk` (`sa_achievement_fk`);

--
-- Indexes for table `student_tasks`
--
ALTER TABLE `student_tasks`
  ADD PRIMARY KEY (`st_id`),
  ADD KEY `st_s_id_fk` (`st_s_id_fk`),
  ADD KEY `st_t_id_fk` (`st_t_id_fk`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`t_id`),
  ADD KEY `t_type_fk` (`t_type_fk`),
  ADD KEY `t_teacher_fk` (`t_teacher_fk`),
  ADD KEY `t_level_fk` (`t_level_fk`),
  ADD KEY `tasks_ibfk_4` (`t_class_fk`),
  ADD KEY `tasks_ibfk_5` (`t_genre_fk`);

--
-- Indexes for table `task_levels`
--
ALTER TABLE `task_levels`
  ADD PRIMARY KEY (`tl_id`);

--
-- Indexes for table `task_types`
--
ALTER TABLE `task_types`
  ADD PRIMARY KEY (`tt_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`u_id`),
  ADD KEY `u_role_fk` (`u_role_fk`),
  ADD KEY `u_class_fk` (`u_class_fk`),
  ADD KEY `fk_user_speed` (`u_progress_speed_fk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `a_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `c_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `g_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `progress_speeds`
--
ALTER TABLE `progress_speeds`
  MODIFY `ps_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `r_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_achievements`
--
ALTER TABLE `student_achievements`
  MODIFY `sa_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `student_tasks`
--
ALTER TABLE `student_tasks`
  MODIFY `st_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `t_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=331;

--
-- AUTO_INCREMENT for table `task_levels`
--
ALTER TABLE `task_levels`
  MODIFY `tl_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `task_types`
--
ALTER TABLE `task_types`
  MODIFY `tt_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`c_progress_speed_fk`) REFERENCES `progress_speeds` (`ps_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`c_teacher_fk`) REFERENCES `users` (`u_id`) ON DELETE SET NULL;

--
-- Constraints for table `student_achievements`
--
ALTER TABLE `student_achievements`
  ADD CONSTRAINT `student_achievements_ibfk_1` FOREIGN KEY (`sa_student_fk`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_achievements_ibfk_2` FOREIGN KEY (`sa_achievement_fk`) REFERENCES `achievements` (`a_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_tasks`
--
ALTER TABLE `student_tasks`
  ADD CONSTRAINT `student_tasks_ibfk_1` FOREIGN KEY (`st_s_id_fk`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_tasks_ibfk_2` FOREIGN KEY (`st_t_id_fk`) REFERENCES `tasks` (`t_id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`t_type_fk`) REFERENCES `task_types` (`tt_id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`t_teacher_fk`) REFERENCES `users` (`u_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`t_level_fk`) REFERENCES `task_levels` (`tl_id`),
  ADD CONSTRAINT `tasks_ibfk_4` FOREIGN KEY (`t_class_fk`) REFERENCES `classes` (`c_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_5` FOREIGN KEY (`t_genre_fk`) REFERENCES `genres` (`g_id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_speed` FOREIGN KEY (`u_progress_speed_fk`) REFERENCES `progress_speeds` (`ps_id`),
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`u_role_fk`) REFERENCES `roles` (`r_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`u_class_fk`) REFERENCES `classes` (`c_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

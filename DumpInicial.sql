-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: despeses
-- ------------------------------------------------------
-- Server version	8.0.45


DROP TABLE IF EXISTS `categories`;


CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB;


--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;

INSERT INTO `categories` VALUES (1,'Alimentació'),(2,'Transport'),(3,'Treballadors');

UNLOCK TABLES;


DROP TABLE IF EXISTS `moviments`;

CREATE TABLE `moviments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuari` int NOT NULL,
  `id_categoria` int NOT NULL,
  `concepte` varchar(255) NOT NULL,
  `import` decimal(12,2) NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_moviments_usuari` (`id_usuari`),
  KEY `fk_moviments_categoria` (`id_categoria`),
  CONSTRAINT `fk_moviments_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_moviments_usuari` FOREIGN KEY (`id_usuari`) REFERENCES `usuaris` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


LOCK TABLES `moviments` WRITE;

INSERT INTO `moviments` VALUES (1,1,1,'Alimentacio Admin',10.00,'2026-04-11'),(2,2,1,'Alimentacio Roger',9.00,'2026-04-11');

UNLOCK TABLES;

DROP TABLE IF EXISTS `usuaris`;

CREATE TABLE `usuaris` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `contrasenya` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB;

--
-- Dumping data for table `usuaris`
--

LOCK TABLES `usuaris` WRITE;

INSERT INTO `usuaris` VALUES (1,'admin','$2y$10$qDi6zaegCICYldGH7w3dgeb70eQOuwGTeZcgTfosWZqsMNUBkU0n2','admin'),(2,'roger','$2y$10$qDi6zaegCICYldGH7w3dgeb70eQOuwGTeZcgTfosWZqsMNUBkU0n2','user');

UNLOCK TABLES;


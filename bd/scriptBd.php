<!-- 
 Table: users
Create Table: CREATE TABLE `users` (
  `id_users` int NOT NULL AUTO_INCREMENT,
  `nome_users` varchar(45) NOT NULL,
  `email_users` varchar(45) NOT NULL,
  `telefone_users` varchar(15) NOT NULL,
  `senha_users` varchar(45) NOT NULL,
  `endereco_users` varchar(255) NOT NULL,
  `cpf_users` varchar(14) NOT NULL,
  PRIMARY KEY (`id_users`),
  UNIQUE KEY `id_users_UNIQUE` (`id_users`),
  UNIQUE KEY `email_users_UNIQUE` (`email_users`),
  UNIQUE KEY `telefone_users_UNIQUE` (`telefone_users`),
  UNIQUE KEY `cpf_users_UNIQUE` (`cpf_users`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

 Table: products
Create Table: CREATE TABLE `products` (
  `id_products` int NOT NULL AUTO_INCREMENT,
  `nome_products` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valor_products` varchar(255) NOT NULL,
  `descricao_products` varchar(225) NOT NULL,
  `estoque_products` int NOT NULL,
  `users_id_users` int NOT NULL,
  `loja_id_loja` int NOT NULL,
  `category_id_category` int NOT NULL,
  `ativo` tinyint DEFAULT '1',
  PRIMARY KEY (`id_products`),
  UNIQUE KEY `id_products_UNIQUE` (`id_products`),
  UNIQUE KEY `unique_product_name_per_store` (`nome_products`,`loja_id_loja`),
  KEY `fk_products_users_idx` (`users_id_users`),
  KEY `fk_products_loja1_idx` (`loja_id_loja`),
  KEY `fk_products_category1_idx` (`category_id_category`),
  CONSTRAINT `fk_products_category1` FOREIGN KEY (`category_id_category`) REFERENCES `category` (`id_category`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_products_loja1` FOREIGN KEY (`loja_id_loja`) REFERENCES `loja` (`id_loja`),
  CONSTRAINT `fk_products_users` FOREIGN KEY (`users_id_users`) REFERENCES `users` (`id_users`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

  Table: cart
Create Table: CREATE TABLE `cart` (
  `id_cart` int NOT NULL AUTO_INCREMENT,
  `user_id_cart` int NOT NULL,
  PRIMARY KEY (`id_cart`),
  UNIQUE KEY `id_cart_UNIQUE` (`id_cart`),
  KEY `fk_user_id` (`user_id_cart`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id_cart`) REFERENCES `users` (`id_users`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

 Table: cart_items
Create Table: CREATE TABLE `cart_items` (
  `id_cart_item` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `user_id` int NOT NULL,
  PRIMARY KEY (`id_cart_item`),
  KEY `cart_id` (`cart_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id_cart`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_products`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=237 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

Table: category
Create Table: CREATE TABLE `category` (
  `id_category` int NOT NULL AUTO_INCREMENT,
  `nome_category` varchar(45) NOT NULL,
  PRIMARY KEY (`id_category`),
  UNIQUE KEY `id_category_UNIQUE` (`id_category`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

Table: imagens
Create Table: CREATE TABLE `imagens` (
  `id_img` int NOT NULL AUTO_INCREMENT,
  `tipo_img` enum('perfil','produto','loja') NOT NULL,
  `caminho_img` varchar(255) NOT NULL,
  `usuarios_id_users` int DEFAULT NULL,
  `produtos_id_products` int DEFAULT NULL,
  `lojas_id_loja` int DEFAULT NULL,
  PRIMARY KEY (`id_img`),
  KEY `usuarios_id_users` (`usuarios_id_users`),
  KEY `fk_imagens_loja` (`lojas_id_loja`),
  KEY `imagens_ibfk_2` (`produtos_id_products`),
  CONSTRAINT `fk_imagens_loja` FOREIGN KEY (`lojas_id_loja`) REFERENCES `loja` (`id_loja`),
  CONSTRAINT `imagens_ibfk_1` FOREIGN KEY (`usuarios_id_users`) REFERENCES `users` (`id_users`) ON DELETE CASCADE,
  CONSTRAINT `imagens_ibfk_2` FOREIGN KEY (`produtos_id_products`) REFERENCES `products` (`id_products`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

Table: loja
Create Table: CREATE TABLE `loja` (
  `id_loja` int NOT NULL AUTO_INCREMENT,
  `nome_loja` varchar(45) NOT NULL,
  `cnpj_loja` varchar(18) NOT NULL,
  `telefone_loja` varchar(15) NOT NULL,
  `users_id_users` int NOT NULL,
  PRIMARY KEY (`id_loja`),
  UNIQUE KEY `id_loja_UNIQUE` (`id_loja`),
  UNIQUE KEY `cnpj_loja_UNIQUE` (`cnpj_loja`),
  UNIQUE KEY `cnpj_loja` (`cnpj_loja`),
  KEY `fk_loja_users1_idx` (`users_id_users`),
  CONSTRAINT `fk_loja_users1` FOREIGN KEY (`users_id_users`) REFERENCES `users` (`id_users`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

Table: vendas
Create Table: CREATE TABLE `vendas` (
  `id_vendas` int NOT NULL AUTO_INCREMENT,
  `transacao_vendas` varchar(225) NOT NULL,
  `data_vendas` datetime NOT NULL,
  `cart_id_cart` int NOT NULL,
  `metodo_pagamento` enum('pix','debito','credito') NOT NULL,
  `parcelas` tinyint DEFAULT NULL,
  `status_pagamento` enum('pendente','aprovado','recusado') DEFAULT 'pendente',
  UNIQUE KEY `id_vendas_UNIQUE` (`id_vendas`),
  KEY `fk_vendas_cart1_idx` (`cart_id_cart`),
  CONSTRAINT `fk_vendas_cart1` FOREIGN KEY (`cart_id_cart`) REFERENCES `cart` (`id_cart`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

 Table: vendas_has_products
Create Table: CREATE TABLE `vendas_has_products` (
  `vendas_id_vendas` int NOT NULL,
  `products_id_products` int NOT NULL,
  `quantity` int NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`vendas_id_vendas`,`products_id_products`),
  KEY `fk_vendas_has_products_products1_idx` (`products_id_products`),
  KEY `fk_vendas_has_products_vendas1_idx` (`vendas_id_vendas`),
  CONSTRAINT `fk_vendas_has_products_products1` FOREIGN KEY (`products_id_products`) REFERENCES `products` (`id_products`),
  CONSTRAINT `fk_vendas_has_products_vendas1` FOREIGN KEY (`vendas_id_vendas`) REFERENCES `vendas` (`id_vendas`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

-->
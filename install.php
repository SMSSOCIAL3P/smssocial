<?php

/**
 * Funcao para criar as tabelas no banco de dados
 */ 
function smsSocial_create_tables () {

	global $wp_sms_db_version, $table_prefix, $wpdb;
		
	//query para crias as tabelas
	$sql2 = " CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_instituicao (
			  id INT NOT NULL AUTO_INCREMENT COMMENT 'identificador da tabela',
			  instituicao VARCHAR(250) NULL COMMENT 'Nome do grupo',
			  flg_atv TINYINT NULL DEFAULT 1 COMMENT 'campo para ativar ou inativar o grupo\n',
			  dt_cadastro TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'campo com a data do cadastro',
			  id_usuario_wp BIGINT NULL COMMENT 'identificador do usuario do wordpress',
			  PRIMARY KEY (id))
			ENGINE = InnoDB;";


	$sql3 = " CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_grupo (
			  id INT NOT NULL AUTO_INCREMENT COMMENT 'identificador da tabela',
			  grupo VARCHAR(150) NULL COMMENT 'Nome do grupo',
			  flg_atv TINYINT NULL DEFAULT 1 COMMENT 'campo para ativar ou inativar o grupo',
			  dt_cadastro TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'campo com a data do cadastro',
			  id_usuario_wp BIGINT NULL COMMENT 'identificador do usuario do wordpress',
			  instituicao_id INT NOT NULL,
			  PRIMARY KEY (id),
			  INDEX fk_smssocial_grupo_smssocial_instituicao1_idx (instituicao_id ASC),
			  CONSTRAINT fk_smssocial_grupo_smssocial_instituicao1
			    FOREIGN KEY (instituicao_id)
			    REFERENCES {$table_prefix}smssocial_instituicao (id)
			    ON DELETE NO ACTION
			    ON UPDATE NO ACTION)
			ENGINE = InnoDB;";

	$sql4 = " CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_contato (
			  id INT NOT NULL AUTO_INCREMENT COMMENT 'identificador da tabela',
			  nome VARCHAR(250) NULL COMMENT 'nome do contato',
			  celular VARCHAR(50) NULL COMMENT 'celular do contato\n',
			  email VARCHAR(150) NULL COMMENT 'email do contato\n',
			  flg_atv TINYINT NULL DEFAULT 1 COMMENT 'campo para ativar ou inativar o contato',
			  dt_cadastro TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Campo para saber quando o contato foi cadastrado',
			  id_telegram VARCHAR(250) NULL COMMENT 'Id do telegram para comunicacao\n',
			  PRIMARY KEY (id))
			ENGINE = InnoDB;";


	$sql5 = " CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_grupo_contato (
			  id INT NOT NULL AUTO_INCREMENT,
			  grupo_id INT NOT NULL,
			  contato_id INT NOT NULL,
			  PRIMARY KEY (id, grupo_id, contato_id),
			  INDEX fk_smssocial_grupo_has_smssocial_contato_smssocial_contato1_idx (contato_id ASC),
			  INDEX fk_smssocial_grupo_has_smssocial_contato_smssocial_grupo1_idx (grupo_id ASC),
			  CONSTRAINT fk_smssocial_grupo_has_smssocial_contato_smssocial_grupo1
			    FOREIGN KEY (grupo_id)
			    REFERENCES {$table_prefix}smssocial_grupo (id)
			    ON DELETE NO ACTION
			    ON UPDATE NO ACTION,
			  CONSTRAINT fk_smssocial_grupo_has_smssocial_contato_smssocial_contato1
			    FOREIGN KEY (contato_id)
			    REFERENCES {$table_prefix}smssocial_contato (id)
			    ON DELETE NO ACTION
			    ON UPDATE NO ACTION)
			ENGINE = InnoDB;";


	$sql6 = " CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_contato_mensagem (
			  id INT NOT NULL AUTO_INCREMENT,
			  contato_id INT NOT NULL,
			  post_ID INT NOT NULL,
			  PRIMARY KEY (id, contato_id),
			  INDEX fk_wp_post_has_smssocial_contato_smssocial_contato1_idx (contato_id ASC),
			  CONSTRAINT fk_wp_post_has_smssocial_contato_smssocial_contato1
			    FOREIGN KEY (contato_id)
			    REFERENCES {$table_prefix}smssocial_contato (id)
			    ON DELETE NO ACTION
			    ON UPDATE NO ACTION)
			ENGINE = InnoDB;";

	$sql7 = " CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_gateway (
			  id INT NOT NULL AUTO_INCREMENT,
			  nome VARCHAR(250) NULL,
			  usuario VARCHAR(250) NULL,
			  senha VARCHAR(250) NULL,
			  dt_cadastro TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (id))
			ENGINE = InnoDB;";

	$sql8 = " CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_conf_telegram (
			  id INT NOT NULL AUTO_INCREMENT,
			  token VARCHAR(250) NULL,
			  nome VARCHAR(250) NULL,
			  dt_cadastro TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (id))
			ENGINE = InnoDB;";

	$sql9 = " CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_telegram_contato (
			  id INT NOT NULL AUTO_INCREMENT,
			  nome VARCHAR(250) NULL,
			  id_telegram VARCHAR(250) NULL COMMENT 'Id do telegram para comunicacao\n',
			  dt_cadastro TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (id))
			ENGINE = InnoDB;";

	$sql10 = "CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_telegram_mensagem (
			  id INT(11) NOT NULL AUTO_INCREMENT,
			  telegram_id INT(11) NOT NULL,
			  post_ID INT(11) NOT NULL,
			  PRIMARY KEY (id,telegram_id),
			  KEY fk_post_smssocial_telegram (telegram_id),
			  CONSTRAINT fk_post_smssocial_telegram_contato FOREIGN KEY (telegram_id) REFERENCES {$table_prefix}smssocial_telegram_contato (id) ON DELETE NO ACTION ON UPDATE NO ACTION
			) ENGINE=INNODB DEFAULT CHARSET=latin1";

	/*$sql11 = "CREATE TABLE IF NOT EXISTS {$table_prefix}smssocial_telegram_grupo (
			  id INT(11) NOT NULL AUTO_INCREMENT,
			  grupo_id INT(11) NOT NULL,
			  telegram_id INT(11) NOT NULL,
			  PRIMARY KEY (id,grupo_id,telegram_id),
			  KEY fk_smssocial_grupo_smssocial_telegram (grupo_id),
			  KEY fk_smssocial_telegram_smssocial_grupo (telegram_id),
			  CONSTRAINT fk_smssocial_telegram_smssocial_grupo FOREIGN KEY (telegram_id) REFERENCES {$table_prefix}smssocial_telegram_contato (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
			  CONSTRAINT fk_smssocial_grupo_smssocial_telegram FOREIGN KEY (grupo_id) REFERENCES {$table_prefix}smssocial_grupo (id) ON DELETE NO ACTION ON UPDATE NO ACTION
			) ENGINE=INNODB DEFAULT CHARSET=latin1";*/


	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	//dbDelta($sql1);
	dbDelta($sql2);
	dbDelta($sql3);
	dbDelta($sql4);
	dbDelta($sql5);
	dbDelta($sql6);
	dbDelta($sql7);
	dbDelta($sql8);
	dbDelta($sql9);
	dbDelta($sql10);
	//dbDelta($sql11);

} 

?>
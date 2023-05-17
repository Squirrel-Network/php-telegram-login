<?php
	class Database {
		private $PDO = null;

		public function __construct ( string $dbhost, string $dbname, string $user, string $password ) {
			try {
				$this->PDO = new PDO( "mysql:host={$dbhost};dbname={$dbname};charset=utf8mb4;", $user, $password );
				$this->PDO->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				$this->PDO->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
			}
			catch ( Exception $e ) {
				throw new Exception( $e->getMessage() );
			}
		}

		public function query ( string $query, array $params ) {
			try {

				$stmt = $this->PDO->prepare( $query );

				foreach ( $params as $param ) {
					if ( $param[ 'type' ] === 'str' )
						$type = PDO::PARAM_STR;
					elseif ( $param[ 'type' ] === 'int' )
						$type = PDO::PARAM_INT;
					elseif ( $param[ 'type' ] === 'bool' )
						$type = PDO::PARAM_BOOL;
					$stmt->bindParam( $param[ 'param' ], $param[ 'value' ], $type );
				}

				$stmt->execute();
				return $stmt->fetchAll( PDO::FETCH_ASSOC );

			}
			catch( PDOException $e ) {
				throw new Exception( $e->getMessage() );
			}
		}
	}

	$db = new Database (
		dbhost:   'localhost',
		dbname:   'telegram_login',
		user:     'user',
		password: 'password'
	);

	$db->query(
		'CREATE TABLE IF NOT EXISTS `users` (
			`id` int NOT NULL AUTO_INCREMENT,
			`first_name` varchar(64) NOT NULL,
			`last_name` varchar(64) DEFAULT NULL,
			`telegram_id` varchar(32) NOT NULL,
			`telegram_username` varchar(64) DEFAULT NULL,
			`profile_picture` varchar(128) DEFAULT NULL,
			`auth_date` varchar(16) NOT NULL,
			`added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;',
		[]
	);
<?php
	session_start();

	if ( isset( $_SESSION[ 'logged-in' ] ) && $_SESSION[ 'logged-in' ] == TRUE )
		die( header( 'Location: user.php' ) );

	require_once 'db-config.php';

	define( 'BOT_TOKEN', 'xxxxxxxxx:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' ); //inserire il token del bot

	if ( !isset( $_GET[ 'hash' ] ) )
		die( 'Telegram hash not found' );

	try {

		$check_hash = $_GET[ 'hash' ];
		unset( $_GET[ 'hash' ] );

		$data_check_arr = [];
		foreach ( $_GET as $key => $value )
			$data_check_arr[] = $key . '=' . $value;
		sort( $data_check_arr );

		if ( strcmp( hash_hmac( 'sha256', implode( "\n", $data_check_arr ), hash( 'sha256', BOT_TOKEN, true ) ), $check_hash ) !== 0 )
			throw new Exception( 'Data is NOT from Telegram' );

		if ( ( time() - $_GET[ 'auth_date' ] ) > 86400 )
			throw new Exception( 'Data is outdated' );

		$params = [
			[
				'param' => ':first_name',
				'value' => htmlspecialchars( $_GET[ 'first_name' ] ),
				'type'  => 'str'
			],
			[
				'param' => ':last_name',
				'value' => isset( $_GET[ 'last_name' ] ) ? htmlspecialchars( $_GET[ 'last_name' ] ) : null,
				'type'  => 'str'
			],
			[
				'param' => ':telegram_id',
				'value' => $_GET[ 'id' ],
				'type'  => 'int'
			],
			[
				'param' => ':telegram_username',
				'value' => $_GET[ 'username' ] ?? null,
				'type'  => 'str'
			],
			[
				'param' => ':profile_picture',
				'value' => $_GET[ 'photo_url' ] ?? null,
				'type'  => 'str'
			],
			[
				'param' => ':auth_date',
				'value' => $_GET[ 'auth_date' ],
				'type'  => 'int'
			]
		];

		$select = $db->query(
			'SELECT `telegram_id`
			FROM `users`
			WHERE `telegram_id` = :id',
			[
				[
					'param' => ':id',
					'value' => $_GET[ 'id' ],
					'type'  => 'int'
				]
			]
		);

		if ( count( $select ) > 0 )
			$db->query(
				'UPDATE
					`users`
				SET
					`first_name`        = :first_name,
					`last_name`         = :last_name,
					`telegram_username` = :telegram_username,
					`profile_picture`   = :profile_picture,
					`auth_date`         = :auth_date
				WHERE
					`telegram_id`       = :telegram_id',
				$params
			);
		else 
			$db->query(
				'INSERT INTO `users` (
					`first_name`,
					`last_name`,
					`telegram_id`,
					`telegram_username`,
					`profile_picture`,
					`auth_date`
				)
				VALUES (
					:first_name,
					:last_name,
					:telegram_id,
					:telegram_username,
					:profile_picture,
					:auth_date
				)',
				$params
			);

		$_SESSION = [
			'logged-in'   => TRUE,
			'telegram_id' => $_GET[ 'id' ]
		];

	}
	catch ( Exception $e ) {
		die( $e->getMessage() );
	}

	die( header( 'Location: user.php' ) );
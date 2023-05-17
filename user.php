<?php
	session_start();

	if ( !isset( $_SESSION[ 'logged-in' ] ) )
		die( header( 'Location: login.php' ) );

	require_once 'db-config.php';

	$user_data = $db->query(
		'SELECT *
		FROM `users`
		WHERE `telegram_id` = :id',
		[
			[
				'param'	=> ':id',
				'value'	=> $_SESSION[ 'telegram_id' ],
				'type'  => 'int'
			]
		]
	);

	$firstName      = $user_data[ 0 ][ 'first_name' ];
	$lastName       = $user_data[ 0 ][ 'last_name' ];
	$profilePicture	= $user_data[ 0 ][ 'profile_picture' ];
	$telegramID     = $user_data[ 0 ][ 'telegram_id' ];
	$username       = $user_data[ 0 ][ 'telegram_username' ];
	$userID         = $user_data[ 0 ][ 'id' ];
?>

<!DOCTYPE html>
<html lang='en-US'>

	<head>
		<title>Logged In User</title>
		<meta charset='UTF-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Nanum+Gothic'>
		<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css'>
		<link rel='stylesheet' href='assets/style.css'>
	</head>

	<body>
		<div class='middle-center'>
			<h1>Hello, <?= $firstName ?> <?= $lastName ?? '' ?>!</h1>

			<?php if ( !is_null( $profilePicture ) ) : ?>
				<a href='<?= $profilePicture ?>' target='_blank'>
					<img class='profile-picture' src='<?= $profilePicture . '?v=' . time() ?>'>
				</a>
			<?php endif; ?>

			<h2 class='user-data'>First Name: <?= $firstName ?></h2>

			<?php if ( !is_null( $lastName ) ) : ?>
				<h2 class='user-data'>Last Name: <?= $lastName ?></h2>
			<?php endif; ?>

			<?php if ( !is_null( $username ) ) : ?>
				<h2 class='user-data'>Username: <a href='<?= "https://t.me/{$username}" ?>' target='_blank'><?= "@{$username}" ?></a></h2>
			<?php endif; ?>

			<h2 class='user-data'>Telegram ID: <?= $telegramID ?></h2>
			<h2 class='user-data'>User ID: <?= $userID ?></h2>

			<a href='logout.php'><h2 class='logout'>Logout</h2></a>
		</div>
	</body>

</html>
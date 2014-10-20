password
========

High security password codec
Each time you encode the same password, the encoded password will be different!


How to use
--------

For store a new password

    include_once 'password.php';
	$psw = password::encode('MyPassword');
	

Compare if a password is correct

    if (password::decode('MyPassword', $psw['password'], $psw['salt'])) { ... }
# Fast Login

This package integrates your custom site with the https://fast.co login system

## Installation

Installation is very easy with composer:

	composer require cognito/fast

If you don't have composer, download the Fast.php file and include it into your project.

## Setup

First, get an account at https://fast.co and create an app.
It's quick and easy. Get the app key and secret key from the app details.

## Integration

If you prefer static functions there are two functions to speed up integration.

To render out the login button:

	<?= \Cognito\Fast::quickLoginButton($fast_app_key) ?>

And to check the login worked on the callback url that fast takes you to:

	<?php
		if (\Cognito\Fast::quickLoginCheck($fast_app_key, $fast_secret_key)) {
			// Allow the user to log on
		} else {
			// Do not allow log on
		}

If you do not like static functions, the class also provides non-static methods.

For example:

	<?php
		$fast = new \Cognito\Fast($fast_app_key, $fast_secret_key);
		if (array_key_exists('challengeId', $_REQUEST)) {
			// Check the login succeeded
			if ($fast->loginCheck()) {
				// Allow the user to log on
			} else {
				// Login failed
			}
		}
		// Render the login button to the browser
		echo $fast->loginButton();
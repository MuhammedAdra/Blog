<?php
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Destroy session
session_start();
session_unset();
session_destroy();

// Redirect to home page
redirect_to('index.php');

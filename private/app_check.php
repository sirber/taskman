<?php
if (!defined("FROM_PUBLIC")) {
	die("fatal error: request not from public/index.php");
}
if (!is_writable(__DIR__ . "/logs/")) {
	die("fatal error: folder private/logs is not writable");
}
if (!is_writable(__DIR__ . "/upload/")) {
	die("fatal error: folder private/upload is not writable");
}
if (!is_file(__DIR__ . "/settings.php")) {
	die("fatal error: settings.php not found");
}
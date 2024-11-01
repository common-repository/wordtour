<?php
header('Content-Type: application/x-javascript; charset=UTF-8');
foreach($_POST as $key=>$value) {
	echo ";";
	include $value;
}
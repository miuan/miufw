<?php

$MIUFW_CFG = new stdClass();

// template configuration
$MIUFW_CFG->TEMPLATES_DIR = 'templates/';
$MIUFW_CFG->template_file_name = 'default.html';
$MIUFW_CFG->template_body = 'template';
$MIUFW_CFG->DEBUG = false;

//DB configuration
// $MIUFW_CFG->DB_TYPE = 'pgsql';
$MIUFW_CFG->DB_TYPE = 'mysql';
$MIUFW_CFG->DB_HOST = 'localhost';
$MIUFW_CFG->DB_LOGIN = '';
$MIUFW_CFG->DB_PASS = '';
$MIUFW_CFG->DB_NAME = '';
$MIUFW_CFG->DB_PORT = '5432';

// var_dump($MIUFW_CFG);

?>
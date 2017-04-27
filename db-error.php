<?php
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/error/error.html')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/error/error.html');
    die;
} else {
    die;
}

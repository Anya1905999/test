<?php

ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);
error_reporting(E_ALL);

if (php_sapi_name() != "cli") {
    $key = filter_var($_GET['key'] ?? '', 513);
    if ($key != 'key') {
        return false;
    }
}

chdir(dirname(__FILE__));

// Тут делаем получение каких-то данных
$data = [];

$message = "Данные: " . $data['data'];
//echo $message;

$channelChatId = ''; // id канала (Формат -1234567890101)
$telegramToken = ''; // Токен (Формат 1234567890:jwdayfuawfhawuifhawJFUFA)
$telegramApiUrl = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=$channelChatId&text=" . urlencode($message);
file_get_contents($telegramApiUrl);

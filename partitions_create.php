<?php
chdir(dirname(__FILE__));
require_once '../vendor/autoload.php';

$db = new PDO(getenv('PRO_STRING'));

ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);
error_reporting(E_ALL);

if (php_sapi_name() != "cli") {
    if (!isset($_GET['key']) || empty($_GET['key'])) {
        $out = [
            'type' => 0,
            'info' => 'wrong key'
        ];
        exit();
    }

    if (filter_var($_GET['key'], 513) !== 'key') {
        $out = [
            'type' => 0,
            'info' => 'wrong key'
        ];
        exit();
    }
}

try{
    $q = "DO $$ 
            DECLARE 
                next_partition_name TEXT := 'table_name' || 'y' || TO_CHAR(current_date + INTERVAL '1 month', 'YYYY') 
                || 'm' || TO_CHAR(current_date + INTERVAL '1 month', 'MM');
                parent_table TEXT := 'table_name_parent'; -- Родительская таблица
            BEGIN
                BEGIN
                    EXECUTE format('
                        CREATE TABLE %I PARTITION OF %I
                        FOR VALUES FROM (%L) TO (%L);
                    ', next_partition_name, parent_table, 
                    TO_CHAR(current_date + INTERVAL '1 month', 'YYYY-MM-01'),
                    TO_CHAR(current_date + INTERVAL '2 month', 'YYYY-MM-01'));
                EXCEPTION 
                    WHEN duplicate_table THEN 
                        -- Если партиция уже есть, просто игнорируем ошибку
                        NULL;
                END;
            END $$;";
    $res = $db->prepare($q);
    if (!$res->execute()) {
        return $res->errorInfo();
    }

} catch (Exception $e){
    return $e;
}

exit();
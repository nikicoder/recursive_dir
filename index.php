<?php

require_once 'vendor/autoload.php';

use DirListener\CountCalculator;

// Поскольку в ТЗ к тестовому не указано должен этот скрипт запускаться в окружени веб-сервера
// или консольно или еще как-то и на всякий случай если кто-то решит проверить его
// работоспособность (алгоритма) то есть демонстрационные данные в папке dummy_data
// на которых работоспособность скрипта проверяется, а тут в свою очередь хардкод на них
$absPath = __DIR__ . '/dummy_data';

try {
    $result = (new CountCalculator($absPath))
        ->run()
        ->getResult();

    echo 'По указанному пути сумма counts: ' . $result . PHP_EOL;
    exit(0);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
<?php

include __DIR__ . "/Utils/CSVColumns.php";
include __DIR__ . "/../vendor/autoload.php";

use CommissionTask\Service\CommissionCalculator;

if ($argc !== 2) {
    echo "Usage: php src/script.php <csv_file_path>\n";
    exit(1);
}

$csvFilePath = $argv[1];
if (!file_exists($csvFilePath)) {
    echo "Error: File '$csvFilePath' does not exist.\n";
    exit(1);
}

$calc = new CommissionCalculator($csvFilePath);
$calc->calcCommission();
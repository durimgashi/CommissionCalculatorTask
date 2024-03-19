<?php

declare(strict_types=1);

namespace CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use CommissionTask\Service\CommissionCalculator;

include __DIR__ . "/../../src/Utils/CSVColumns.php";

class CommissionCalculatorTest extends TestCase {
    public function testCommissionCalculator() {
        $csv_file_path = __DIR__ . '/../../input.csv';
        $commissionCalculator = new CommissionCalculator($csv_file_path);

        ob_start();
        $commissionCalculator->calcCommission();
        $output = ob_get_clean();

        $expectedOutput = "0.60\n3.00\n0.00\n0.06\n1.50\n0\n0.70\n0.30\n0.30\n3.00\n0.00\n0.00\n8612\n";

        $this->assertEquals($output, $expectedOutput);
    }
}

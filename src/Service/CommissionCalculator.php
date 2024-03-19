<?php


namespace CommissionTask\Service;

use CommissionTask\Models\Transaction;

class CommissionCalculator {
    private static $weeklyLimit = 1000;
    private static $freeWithdraws = 3;
    private $data;

    private $allCommissions = [];

    public function __construct($csv_path) {
        $this->data = $this->parseCSV($csv_path);
    }

    public function calcCommission() {
        $groupedTransactions = $this->groupTransactionsByWeekAndUser($this->data);

        foreach($groupedTransactions AS $monday => $transactions) {
            $this->calcCommissions($transactions);
        }

        $this->printCommissions();
    }

    private function calcCommissions($transactions) {
        $withdrawnThisWeek = 0;
        $noFeeTransactions = 0;
        $limitSurpassed = false;

        foreach($transactions as $t) {
            $t = new Transaction($t);
            $amount = $t->getAmountConverted();
            $comm = 0;

            if ($t->isDeposit()) {
                $comm = $amount * $t->getCommissionRate();
            } else {
                $withdrawnThisWeek += $amount;
                $noFeeTransactions++;

                if ($t->isBusiness()) {
                    $comm = $amount * $t->getCommissionRate();
                } else {
                    if ($noFeeTransactions > self::$freeWithdraws)
                        $limitSurpassed = true;

                    if (!$limitSurpassed) {
                        if ($withdrawnThisWeek > self::$weeklyLimit) {
                            $limitSurpassed = true;
                            $comm = ($withdrawnThisWeek - self::$weeklyLimit) * $t->getCommissionRate();
                        }
                    } else {
                        $comm = $amount * $t->getCommissionRate();
                    }
                }
            }

            $t->setCommission($comm);

            $this->allCommissions[$t->getIndex()] = $t->getCommission();
        }
    }

    private function printCommissions() {
        ksort($this->allCommissions);

        foreach ($this->allCommissions AS $index => $commission) {
            echo $commission . "\n";
        }
    }

    private function groupTransactionsByWeekAndUser($transactions): array {
        $groupedTransactions = [];

        foreach ($transactions as $transaction) {
            $transactionDate = date('Y-m-d', strtotime($transaction[CSV_DATE]));
            $monday = $this->getMonday($transactionDate);
            $client = $transaction[CSV_CLIENT_ID];
    
            $weekClientKey = "$monday:$client";
    
            if (!isset($groupedTransactions[$weekClientKey])) {
                $groupedTransactions[$weekClientKey] = [];
            }
    
            $groupedTransactions[$weekClientKey][] = $transaction;
        }

        return $groupedTransactions;
    }

    private function getMonday($date) {
        return date('Y-m-d', strtotime('monday this week', strtotime($date)));
    }

    private function parseCSV($file_path): array {
        $parsedData = [];

        if (($handle = fopen($file_path, "r"))) {
            $index = 0;
            while (($data = fgetcsv($handle, 1000, ","))) {
                array_unshift($data, $index);
                $parsedData[] = $data;

                $index++;
            }
            fclose($handle);
        }

        return $parsedData;
    }
}

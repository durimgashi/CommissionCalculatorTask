<?php

namespace CommissionTask\Models;

class Transaction {
    private $index;
    private $date;
    private $clientID;
    private $clientType;
    private $transactionType;
    private $amount;
    private $currency;
    private $amountConverted;
    private $commission;

    private static $commissionRates = [
        'private' => [
            'deposit' => 0.0003,
            'withdraw' => 0.003
        ],
        'business' => [
            'deposit' => 0.0003,
            'withdraw' => 0.005
        ]
    ];

    private static $CURRENCY_EUR = 'EUR';
    private static $CURRENCY_USD = 'USD';
    private static $CURRENCY_JPY = 'JPY';

    private static $RATES = [
        "USD:EUR" => 1.1497,
        "JPY:EUR" => 129.53
    ];

    public function __construct($d) {
        $this->index = $d[CSV_INDEX];
        $this->date = $d[CSV_DATE];
        $this->clientID = $d[CSV_CLIENT_ID];
        $this->clientType = $d[CSV_CLIENT_TYPE];
        $this->transactionType = $d[CSV_TRANSACTION_TYPE];
        $this->amount = $d[CSV_AMOUNT];
        $this->currency = $d[CSV_CURRENCY];
        $this->amountConverted = $this->convertToEuro();
    }

    public function __destruct() {

    }

    public function getCommissionRate(): float {
        return self::$commissionRates[$this->getClientType()][$this->getTransactionType()];
    }

    public function setCommission($commission) {
        $this->commission = $commission;
        $this->convertCommissionToOriginalCurrency();
    }

    private function convertToEuro() {
        $convertedAmount = 0;
        switch($this->currency) {
            case self::$CURRENCY_USD:
                $convertedAmount = $this->amount / self::$RATES["USD:EUR"];
                break;
            case self::$CURRENCY_JPY:
                $convertedAmount = $this->amount / self::$RATES["JPY:EUR"];
                break;
            case self::$CURRENCY_EUR:
                $convertedAmount = $this->amount;
                break;
        }

        return ceil((float)$convertedAmount);
    }

    public function getIndex() {
        return $this->index;
    }

    public function convertCommissionToOriginalCurrency() {
        switch($this->currency) {
            case self::$CURRENCY_USD:
                $this->commission = $this->commission * self::$RATES["USD:EUR"];
                break;
            case self::$CURRENCY_JPY:
                $this->commission = $this->commission * self::$RATES["JPY:EUR"];
                break;
            case self::$CURRENCY_EUR:
                break;
        }

        $this->roundCommission();
    }

    private function roundCommission() {
        switch ($this->currency) {
            case 'USD':
            case 'EUR':
                $this->commission = round($this->commission, 2);
                $this->commission = number_format($this->commission, 2, '.', '');
                break;
            case 'JPY':
                $this->commission = round($this->commission);
                break;
        }
    }


    public function getClientType() {
        return $this->clientType;
    }

    public function getTransactionType() {
        return $this->transactionType;
    }

    public function getAmountConverted() {
        return $this->amountConverted;
    }

    public function getCommission() {
        return $this->commission;
    }

    public function isDeposit(): bool {
        return $this->transactionType === 'deposit';
    }

    public function isBusiness(): bool {
        return $this->clientType === 'business';
    }
}
# Commission Calculator

This is a PHP script for calculating commissions based on input data from CSV files.

## Installation

Before running the script, make sure you have [Composer](https://getcomposer.org/) installed on your system.

1. Open your terminal.
2. Navigate to the project directory.
3. Run `composer install` to install dependencies.

## Usage

To calculate commissions, run the following command in your terminal:

`php src/script.php <csv-file-path>`

You can use the CSV file inside the directory as input as well by replacing `input.csv` with the appropriate file path.

`php src/script.php input.csv`

The commission fees for the transactions in the file will be displayed in the console

## Running The Automation Test

To run the automated test, execute the following command:

`composer phpunit`




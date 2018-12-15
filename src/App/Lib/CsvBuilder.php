<?php
namespace Dipsor\CsvFaker\Lib;

use Faker\Generator as Faker;
use League\Csv\Writer;

class CsvBuilder {
    private $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    /**
     * @param array $columns
     * @param $rows
     * @return Writer
     */
    public function buildFromArray(array $columns, $rows)
    {
        $csvRows = [];
        $this->addHeader($csvRows, $columns);
        $this->addBody($csvRows, $columns, (int)$rows);

        return $this->buildCsv($csvRows);
    }

    /**
     * @param array $dataRows
     * @param array $columns
     */
    private function addHeader(array &$csvRows, array $columns)
    {
        $header = [];

        foreach ($columns as $column) {
            $header[] = $this->parseHeader($column);
        }

        $csvRows[0] = $header;
    }

    /**
     * @param string $column
     * @return string
     */
    private function parseHeader(string $column): string
    {
        $exploded = explode(':', $column);

        return $exploded[0];
    }

    /**
     * @param array $dataRows
     * @param array $columns
     * @param int $rows
     */
    private function addBody(array &$csvRows, array $columns, int $rows)
    {
        for ($i = 0; $i < $rows; $i++) {
            $csvRows[] = $this->getCsvRow($columns);
        }
    }

    private function getCsvRow(array $columns): array
    {
        $row = [];

        foreach ($columns as $column) {
            $row[] = $this->fakeColumn($column);
        }

        return $row;
    }

    /**
     * @param $column
     * @return mixed
     */
    private function fakeColumn(string $column)
    {
        $property = $this->getPropForFaker($column);

        return $this->faker->$property ?? '';
    }

    /**
     * @param string $column
     * @return mixed
     */
    private function getPropForFaker(string $column)
    {
        $exploded = explode(':', $column);

        return $exploded[1];
    }

    /**
     * @param array $rows
     * @param string $fileName
     * @return Writer
     */
    private function buildCsv(array $rows): Writer
    {
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertAll($rows);

        return $writer;
    }
}

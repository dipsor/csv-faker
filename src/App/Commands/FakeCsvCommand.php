<?php

namespace Dipsor\CsvFaker\Console\Commands;

use Dipsor\CsvFaker\Lib\CsvBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Class FakeCsvCommand
 * $faker = new Faker();
 * $faker->address;
 * $faker->city;
 * $faker->company;
 * $faker->companyEmail;
 * $faker->jobTitle;
 * $faker->firstName;
 * $faker->lastName;
 * $faker->email;
 * $faker->sentence;
 * $faker->password;
 * $faker->numberBetween(0,10);
 * $faker->phoneNumber;
 * @package App\Console\Commands
 */
class FakeCsvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:faker:new {--load=a} {filename?} {rows?} {columns?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command creates csv based on users input. csv:faker:new {--load=a/s} {filename?} {rows?} {columns?}';

    private $inputs = [];
    private $fileName = '';
    private $rows = 1;
    private $inputIndexesToFix = [];
    private $csvBuilder;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CsvBuilder $csvBuilder)
    {
        parent::__construct();
        $this->csvBuilder = $csvBuilder;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->chooseProcess($this->option('load'));
    }

    private function chooseProcess(string $option)
    {
        switch ($option) {
            case 's':
                $this->handleAsString();
                break;
            default:
                $this->handleAsArray();
        }
    }

    private function handleAsArray()
    {
        // ask for input
        $this->fileName = $this->askForName();
        $this->rows = $this->askForRows();
        $this->askForInputs();
    }

    private function handleAsString()
    {
        $this->fileName = $this->argument('filename');
        $this->rows = $this->argument('rows');
        $this->inputs = explode(',', $this->argument('columns'));

        $this->createCsv();
    }

    /**
     * @return string
     *
     * Ask for file name
     */
    private function askForName(): string
    {
        return $this->ask('Filename:');
    }

    /**
     * @return string
     */
    private function askForRows(): string
    {
        return $this->ask('Number of rows');
    }

    /**
     * @param null $i
     *
     *  Keep asking for new input until user type stop.
     *  Then ask to create new csv, or change given value.
     *  then create new csv.
     */
    private function askForInputs($i = null): void
    {
        $index = $i !== null ? $i : 1;

        // zadavej sloupce
        while (($this->input = $this->ask(sprintf("enter %s. column", $index))) !== 'stop') {
            $this->inputs[] = $this->input;
            $this->askForInputs($index+=1);
        }

        // confirm yes and create or accept array of indexes.
        if ($this->input === 'stop') {
            dump($this->inputs);
            $this->fixOrCreate();
        }

        exit();
    }

    /**
     * ask if user want to create csv. if yes, create else ask for indexes of inputs to be fixed.
     * Check if indexes to be fixes are valid, if not, ask until they are valid.
     */
    private function fixOrCreate(): void
    {
        // if user gives array => check if its valid or not, keep asking until its valid.
        if (($input = $this->getConfirmQuestion()) !== 'yes') {
            $this->inputIndexesToFix = $this->getArrayFromString($input);

            // checks if indexes given are valid. If not, it aks again for them and validate.
            while (!$this->checkIndexesExist($this->inputIndexesToFix)) {
                $this->checkIndexesExist($this->inputIndexesToFix);
            }

            // loop through indexes and ask for new value.
            $this->askToFixInput($this->inputIndexesToFix);
        }

        // else create csv.
        $this->createCsv();
    }

    /**
     * @return string
     */
    private function getConfirmQuestion(): string
    {
        return $this->ask('Do you wish to create csv ? please type "yes" or index of input you want to fix');
    }

    /**
     * @param string $input
     * @return array
     *
     * Accept argument in format: "1", "1,2" etc parse it and return as array.
     */
    private function getArrayFromString(string $input): array
    {
        return explode(',', $input);
    }

    /**
     * @param $inputs
     * @return bool
     *
     * Checks the global inputIndexesToFix if its invalid, it asks for new array of indexes
     * and its called again in while loop.
     */
    private function checkIndexesExist($inputs): bool
    {
        $isValid = true;
        foreach($inputs as $index) {
            if (!array_key_exists($index, $this->inputs)) {
                $isValid = false;
                $this->askInputsAgain($index);
            }
        }

        return $isValid;
    }

    /**
     * @param $index
     *
     * Sets globally inputIndexesToFix when its first invalid first time.
     */
    private function askInputsAgain($index): void
    {
        $this->inputIndexesToFix = $this->getArrayFromString($this->ask('Index ' . $index . 'does not exist. Provide array again'));
    }

    /**
     * @param array $inputs
     * Loop through indexes and ask and set new value.
     */
    private function askToFixInput(array $inputs): void
    {
        foreach ($inputs as $input) {
            $this->info('Old value: ' . $this->inputs[$input]);
            $result = $this->ask('Please provide new value');
            $this->updateInputs($input, $result);
        }
    }

    /**
     * @param int $input
     * @param string $result
     *
     * Update array of columns.
     */
    private function updateInputs(int $input, string $result): void
    {
        $this->inputs[$input] = $result;
    }

    /**
     * Create new csv.
     */
    private function createCsv()
    {
        $writer = $this->csvBuilder->buildFromArray($this->inputs, $this->rows, $this->fileName);

        Storage::disk('generated-csv')->put($this->fileName . '.csv', $writer->getContent());

        $this->info('New file was created: ' . storage_path('generated-csv') . '/' . $this->fileName);
    }
}

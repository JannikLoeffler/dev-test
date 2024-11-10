<?php

class DevTest
{
    private string $action;
    private string $file;
    private string $logFile = 'log.txt';
    private string $resultFile = 'result.csv';
    private array $results = [];
    private array $logs = [];

    public function __construct(string $action, string $file)
    {
        $this->action = $action;
        $this->file = $file;
    }

    public function execute(): void
    {
        $this->processFile();
        $this->saveResult();
        $this->saveLog();
    }

    private function processFile(): void
    {
        $handle = fopen($this->file, 'r');
        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            $a = (int)$data[0];
            $b = (int)$data[1];
            $result = $this->calculate($a, $b);

            if ($result > 0) {
                $this->results[] = '$a;$b;$result';
            } else {
                $this->logs[] = 'Numbers $a and $b are wrong';
            }
        }
        fclose($handle);
    }

    private function calculate(int $a, int $b): ?float
    {
        return match ($this->action) {
            'plus' => $a + $b,
            'minus' => $a - $b,
            'multiply' => $a * $b,
            'division' => $b !== 0 ? $a / $b : null,
            default => null
        };
    }

    private function saveResult(): void
    {
        file_put_contents($this->resultFile, implode('\r\n', $this->results) . '\r\n');
    }

    private function saveLog(): void
    {
        if (!empty($this->logs)) {
            file_put_contents($this->logFile, implode('\r\n', $this->logs) . '\r\n');
        }
    }
}

$options = getopt('', ['action:', 'file:']);
$action = $options['action'];
$file = $options['file'];

$devTest = new DevTest($action, $file);
$devTest->execute();
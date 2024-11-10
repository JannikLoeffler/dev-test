<?php

class DevTest
{
    private string $action;
    private string $file;
    private string $logFile;
    private string $resultFile;
    private array $results = [];
    private array $logs = [];

    public function __construct(string $action, string $file)
    {
        $this->action = $action;
        $this->file = $file;
        
        $timestamp = time();
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $logDirectory = 'Logs';
        $resultDirectory = 'Results';

        $this->logFile = sprintf('%s/%s-%s-%s.txt', $logDirectory, $timestamp, $filename, $action);
        $this->resultFile = sprintf('%s/%s-%s-%s.csv', $resultDirectory, $timestamp, $filename, $action);
    }

    public function execute(): void
    {
        if (!$this->isValidAction($this->action)) {
            echo "Invalid action. Allowed actions are: plus, minus, multiply, division.\n";
            exit;
        }

        if (!file_exists($this->file)) {
            echo "File not found: {$this->file}\n";
            exit;
        }

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
                $this->results[] = "$a;$b;$result";
            } else {
                $this->logs[] = "Numbers $a and $b are wrong";
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
        file_put_contents($this->resultFile, implode("\r\n", $this->results) . "\r\n");
    }

    private function saveLog(): void
    {
        if (!empty($this->logs)) {
            file_put_contents($this->logFile, implode("\r\n", $this->logs) . "\r\n");
        }
    }

    private function isValidAction(string $action): bool
    {
        return in_array($action, ['plus', 'minus', 'multiply', 'division']);
    }
}

$options = getopt('', ['action:', 'file:']);
$action = $options['action'] ?? null;
$file = $options['file'] ?? null;

if (!$action || !$file) {
    echo "Usage: php console.php --action {action} --file {file}\n";
    exit;
}

$devTest = new DevTest($action, $file);
$devTest->execute();
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class RunPowershellScript implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $scriptPath,
        public array $args = []
    ) {}

    public function handle(): void
    {
        if (!file_exists($this->scriptPath)) {
            Log::error("PowerShell script not found: {$this->scriptPath}");
            throw new \RuntimeException("Script not found: {$this->scriptPath}");
        }

        $binary = PHP_OS_FAMILY === 'Windows' ? 'powershell' : 'pwsh';

        $command = array_merge([
            $binary,
            '-NoProfile',
            '-NonInteractive',
            '-ExecutionPolicy',
            'Bypass',
            '-File',
            $this->scriptPath,
        ], $this->args);

        $process = new Process($command);
        $process->setTimeout(null);

        Log::info('Running PowerShell script: ' . implode(' ', $command));

        $process->run(function ($type, $buffer) {
            Log::info($buffer);
        });

        if (!$process->isSuccessful()) {
            Log::error('PowerShell script failed: ' . $process->getErrorOutput());
            throw new \RuntimeException('PowerShell script failed: ' . $process->getExitCodeText());
        }

        Log::info('PowerShell script finished successfully.');
    }
}

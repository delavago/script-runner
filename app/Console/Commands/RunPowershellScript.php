<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RunPowershellScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:powershell {script} {--args=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a PowerShell script using pwsh (cross-platform) or powershell (Windows)';

    public function handle(): int
    {
        $script = $this->argument('script');
        $args = $this->option('args') ?? [];

        if (!file_exists($script)) {
            $this->error("Script not found: {$script}");
            return 1;
        }

        $binary = PHP_OS_FAMILY === 'Windows' ? 'powershell' : 'pwsh';

        $command = array_merge([
            $binary,
            '-NoProfile',
            '-NonInteractive',
            '-ExecutionPolicy',
            'Bypass',
            '-File',
            $script,
        ], $args);

        $process = new Process($command);
        $process->setTimeout(null);

        $this->info('Running: ' . implode(' ', $command));

        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            $this->error('PowerShell script failed: ' . $process->getExitCodeText());
            return 1;
        }

        $this->info('PowerShell script finished successfully.');
        return 0;
    }
}

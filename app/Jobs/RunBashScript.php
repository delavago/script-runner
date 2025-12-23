<?php

namespace App\Jobs;

use App\Models\ExecutionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class RunBashScript implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $scriptPath,
        public string $scriptId,
        public string $userId,
        public array $args = []
    ) {}

    public function handle(): void
    {
        Log::info('Bash script execution started', [
            'script_id' => $this->scriptId,
            'user_id' => $this->userId,
            'script_path' => $this->scriptPath,
        ]);

        if (!file_exists($this->scriptPath)) {
            Log::error("Bash script not found: {$this->scriptPath}", [
                'script_id' => $this->scriptId,
                'user_id' => $this->userId,
            ]);
            throw new \RuntimeException("Script not found: {$this->scriptPath}");
        }

        $binary = '/bin/bash';

        $command = array_merge([
            $binary,
            $this->scriptPath,
        ], $this->args);

        $process = new Process($command);
        $process->setTimeout(null);

        Log::info('Running Bash script: ' . implode(' ', $command), [
            'script_id' => $this->scriptId,
            'user_id' => $this->userId,
        ]);

        $output = '';
        $process->run(function ($type, $buffer) use (&$output) {
            $output .= $buffer;
            Log::info($buffer);
        });

        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput();
            $output .= "\n" . $errorOutput;
            
            ExecutionLog::create([
                'script_logs' => $output,
                'script_id' => $this->scriptId,
                'user_id' => $this->userId,
            ]);

            Log::error('Bash script failed: ' . $errorOutput, [
                'script_id' => $this->scriptId,
                'user_id' => $this->userId,
            ]);
            throw new \RuntimeException('Bash script failed: ' . $process->getExitCodeText());
        }

        ExecutionLog::create([
            'script_logs' => $output,
            'script_id' => $this->scriptId,
            'user_id' => $this->userId,
        ]);

        Log::info('Bash script finished successfully.', [
            'script_id' => $this->scriptId,
            'user_id' => $this->userId,
        ]);
    }
}

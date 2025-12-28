<?php

namespace App\Jobs;

use App\Models\ExecutionLog;
use App\Models\Script;
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

        $script = Script::with('credential')->find($this->scriptId);
        $credentialParams = [];

        if ($script && $script->use_credentials && $script->credential) {
            $credential = $script->credential;
            
            if ($credential->username) {
                $credentialParams[] = '--Username=' . $credential->username;
            }
            if ($credential->password) {
                $credentialParams[] = '--Password=' . $credential->password;
            }
            if ($credential->host) {
                $credentialParams[] = '--HostName=' . $credential->host;
            }
            if ($credential->port) {
                $credentialParams[] = '--Port=' . $credential->port;
            }
            if ($credential->domain) {
                $credentialParams[] = '--Domain=' . $credential->domain;
            }
            if ($credential->database) {
                $credentialParams[] = '--Database=' . $credential->database;
            }
            if ($credential->private_key) {
                $credentialParams[] = '--PrivateKey=' . $credential->private_key;
            }
        }

        $binary = '/bin/bash';

        $command = array_merge([
            $binary,
            $this->scriptPath,
        ], $credentialParams, $this->args);

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

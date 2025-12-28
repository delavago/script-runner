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

class RunPowershellScript implements ShouldQueue
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
        Log::info('PowerShell script execution started', [
            'script_id' => $this->scriptId,
            'user_id' => $this->userId,
            'script_path' => $this->scriptPath,
        ]);

        if (!file_exists($this->scriptPath)) {
            Log::error("PowerShell script not found: {$this->scriptPath}", [
                'script_id' => $this->scriptId,
                'user_id' => $this->userId,
            ]);
            throw new \RuntimeException("Script not found: {$this->scriptPath}");
        }

        $script = Script::with('credential')->find($this->scriptId);
        $credentialParams = [];

        Log::info('Script details: ', ['script' => $script]);

        if ($script && $script->use_credentials && $script->credential) {
            $credential = $script->credential;
            Log::info($credential);
            
            if ($credential->username) {
                $credentialParams[] = '-Username';
                $credentialParams[] = $credential->username;
            }
            
            if ($credential->password) {
                $credentialParams[] = '-Password';
                $credentialParams[] = $credential->password;
            }
            
            if ($credential->host) {
                $credentialParams[] = '-HostName';
                $credentialParams[] = $credential->host;
            }
            
            if ($credential->port) {
                $credentialParams[] = '-Port';
                $credentialParams[] = $credential->port;
            }
            
            if ($credential->domain) {
                $credentialParams[] = '-Domain';
                $credentialParams[] = $credential->domain;
            }
            
            if ($credential->database) {
                $credentialParams[] = '-Database';
                $credentialParams[] = $credential->database;
            }
            
            if ($credential->private_key) {
                $credentialParams[] = '-PrivateKey';
                $credentialParams[] = $credential->private_key;
            }
        }

        Log::info('Credential Params: ', $credentialParams);

        $binary = PHP_OS_FAMILY === 'Windows' ? 'powershell' : 'pwsh';

        $command = array_merge([
            $binary,
            '-NoProfile',
            '-NonInteractive',
            '-ExecutionPolicy',
            'Bypass',
            '-File',
            $this->scriptPath,
        ], $credentialParams, $this->args);

        $process = new Process($command);
        $process->setTimeout(null);

        Log::info('Running PowerShell script: ' . implode(' ', $command), [
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

            Log::error('PowerShell script failed: ' . $errorOutput, [
                'script_id' => $this->scriptId,
                'user_id' => $this->userId,
            ]);
            throw new \RuntimeException('PowerShell script failed: ' . $process->getExitCodeText());
        }

        ExecutionLog::create([
            'script_logs' => $output,
            'script_id' => $this->scriptId,
            'user_id' => $this->userId,
        ]);

        Log::info('PowerShell script finished successfully.', [
            'script_id' => $this->scriptId,
            'user_id' => $this->userId,
        ]);
    }
}

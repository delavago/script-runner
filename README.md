# Script Runner

A Laravel-based web application for managing and executing PowerShell, Python, and Bash scripts with credential management and execution logging.

## Features

- **Multi-Language Support**: Execute PowerShell, Python, and Bash scripts
- **Credential Management**: Securely store and use credentials for script execution
- **Role-Based Access Control**: Admin and Script Runner roles with granular permissions
- **Execution Logging**: Track all script executions with detailed logs
- **Queue-Based Execution**: Scripts run asynchronously in the background
- **File Type Auto-Detection**: Automatically determines script type from file extension

## Requirements

- PHP 8.2+
- PostgreSQL
- Composer
- Node.js & npm
- PowerShell Core (pwsh) for PowerShell scripts on macOS/Linux
- Python 3 for Python scripts
- Bash for shell scripts

## Installation

### 1. Clone and Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Database

Edit `.env` file with your PostgreSQL credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=script_runner
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Seed Roles and Permissions

```bash
php artisan db:seed --class=RolesAndPermissionWSeeder
```

### 6. Create Admin User

```bash
php artisan app:create-admin-user
```

Follow the prompts to create your first admin user.

## Running the Application

### Development Server

```bash
# Terminal 1: Start Laravel development server
php artisan serve

# Terminal 2: Start Vite dev server for assets
npm run dev

# Terminal 3: Start queue worker
php artisan queue:work
```

The application will be available at `http://localhost:8000`

### Production Deployment

```bash
# Build assets
npm run build

# Configure supervisor for queue worker
# See Laravel documentation for queue worker deployment
```

## Usage Guide

### User Roles

**Admin Role**
- Full system access
- Manage users, scripts, credentials, and execution logs
- Create, update, delete all resources

**Script Runner Role**
- View and execute scripts
- View execution logs
- Cannot manage users or credentials

### Managing Scripts

1. **Create a Script**
   - Navigate to Scripts → Create
   - Upload your script file (.ps1, .py, or .sh)
   - File type is auto-detected from extension
   - Toggle "Active" to enable/disable execution
   - Optionally link credentials for authenticated execution

2. **Execute a Script**
   - View the script details
   - Click "Run Script" button
   - Confirm execution
   - Script runs in background via queue
   - Check execution logs for output

### Managing Credentials

1. **Create Credentials**
   - Navigate to Credentials → Create
   - Provide credential details:
     - Name (e.g., "Production Database")
     - Type (ssh, database, api_key, etc.)
     - Username, Password, Host, Port
     - Domain, Database (optional)
     - Private Key (for SSH)

2. **Link Credentials to Scripts**
   - Edit a script
   - Enable "Use Credentials"
   - Select the credential from dropdown
   - Credentials are passed as parameters to scripts

### Script Parameters

When credentials are enabled, they're passed to scripts as:

**PowerShell**
```powershell
-Username <value> -Password <value> -HostName <value> -Port <value> -Domain <value> -Database <value> -PrivateKey <value>
```

**Python/Bash**
```bash
--Username=<value> --Password=<value> --HostName=<value> --Port=<value> --Domain=<value> --Database=<value> --PrivateKey=<value>
```

### Viewing Execution Logs

- Navigate to Execution Logs
- View script output, errors, and execution details
- Filter by script or user
- Logs are automatically created for each execution

## Queue Worker

The application uses Laravel queues for background script execution.

### Running Queue Worker

```bash
# Basic queue worker
php artisan queue:work

# With options
php artisan queue:work --tries=3 --timeout=300
```

### Supervisor Configuration (Production)

Create `/etc/supervisor/conf.d/script-runner.conf`:

```ini
[program:script-runner-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your-project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your-project/storage/logs/worker.log
stopwaitsecs=3600
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start script-runner-worker:*
```

## File Structure

```
app/
├── Console/Commands/
│   ├── CreateAdminUser.php       # Admin user creation command
│   └── RunPowershellScript.php   # (Legacy - now a job)
├── Filament/Resources/           # Filament admin panels
├── Jobs/
│   ├── RunPowershellScript.php   # PowerShell execution job
│   ├── RunPythonScript.php       # Python execution job
│   └── RunBashScript.php         # Bash execution job
├── Models/
│   ├── Credential.php            # Credential model
│   ├── ExecutionLog.php          # Execution log model
│   ├── Script.php                # Script model
│   └── User.php                  # User model
└── Policies/                     # Authorization policies

database/
├── migrations/                   # Database migrations
└── seeders/
    └── RolesAndPermissionWSeeder.php  # Roles and permissions seeder

storage/
└── app/
    └── public/
        └── scripts/              # Uploaded script files
```

## Permissions Reference

| Permission | Description |
|------------|-------------|
| `user:view` | View users |
| `user:create` | Create users |
| `user:update` | Update users |
| `user:delete` | Delete users |
| `script:view` | View scripts |
| `script:create` | Create scripts |
| `script:update` | Update scripts |
| `script:delete` | Delete scripts |
| `script:execute` | Execute scripts |
| `execution_log:view` | View execution logs |
| `execution_log:delete` | Delete execution logs |

## Troubleshooting

### Scripts Not Executing

1. **Check Queue Worker**
   ```bash
   php artisan queue:work
   ```
   Ensure the queue worker is running.

2. **Check Script Permissions**
   Ensure uploaded scripts have execute permissions:
   ```bash
   chmod +x storage/app/public/scripts/your-script.sh
   ```

3. **Check Binary Paths**
   - PowerShell: `pwsh` (macOS/Linux) or `powershell` (Windows)
   - Python: `python3`
   - Bash: `/bin/bash`

### Permission Denied Errors

Ensure the web server has permission to execute scripts:
```bash
chown -R www-data:www-data storage/app/public/scripts
chmod -R 755 storage/app/public/scripts
```

### Database Connection Issues

- Verify PostgreSQL is running
- Check `.env` database credentials
- Run `php artisan config:clear`

### Failed Jobs

View failed jobs:
```bash
php artisan queue:failed
```

Retry failed jobs:
```bash
php artisan queue:retry all
```

## Security Considerations

1. **Credential Encryption**: Consider encrypting sensitive credential fields (password, private_key)
2. **Script Validation**: Validate uploaded scripts before execution
3. **Rate Limiting**: Implement rate limiting on script execution
4. **Audit Logging**: All executions are logged with user and timestamp
5. **File Upload Restrictions**: Only allow specific file extensions

## Development

### Run Tests

```bash
php artisan test
```

### Code Style

```bash
./vendor/bin/pint
```

## License

This project is proprietary software.

## Support

For issues and questions, contact your system administrator.

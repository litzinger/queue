
## Consuming Jobs

Make sure a crontab is added to your server. This example will run very minute and process as many jobs
in the queue as it can until the process reaches the PHP timeout.

```bash
*/1 * * * * php /var/www/mysite.com/system/ee/eecli.php queue:work
```

If you want a to slow things down a bit add a worker that runs every 2 minutes and only processes 10 jobs at a time.
     
```bash
*/2 * * * * php /var/www/mysite.com/system/ee/eecli.php queue:work --limit=10
```  

Cron can only execute every minute at minimum. If you want something executing more frequently you can use a bash 
script that executes the worker every N seconds. This will endlessly loop and call the worker every second and process
one job a second.

```bash
#!/bin/bash
while true; do
  php /var/www/html/system/ee/eecli.php queue:work --limit=1
  sleep 1
done
```

You can use `nohup` to run the bash script so it continues to run even after exiting the terminal.

```bash
nohup bash path/to/loop.sh &
```

It is recommended to setup a crontab or bash script to automatically execute the queue worker. If that is not an option 
you can use a service such as [Better Stack](https://betterstack.com/uptime) or [UptimeRobot](https://uptimerobot.com/)
to hit an ExpressionEngine action endpoint (e.g. https://acme.com/?ACT=123) at a regular interval. After you install
Queue the action URL will be presented to you in the module's admin page.

When choosing which method and how often a worker runs it is important to consider _what_ it is processing. If you are running
a cron every minute that ends up processing 100 jobs, and each job is long running task, it increases the chance of
the job failing. Running a worker more frequently and processing a smaller number of jobs helps reduce failures. The queue 
is able to process thousands of jobs a minute (or faster), depending on how resource intensive the jobs are. You'll want to adjust
how you run the `queue:work` command based on what types of jobs you're processing.

### Config overrides

Add some or all of the following to your config.php file to modify the default queue settings.

```php
$config['queue'] = [
    'enable_logging' => 'y', // This will log all queue actions to EE's Developer log. It's best to keep this off unless you need to debug something.
    'enable_detailed_logging' => 'y', // This will append the full payload and stack trace (if applicable) to processed jobs.
    'driver' => 'database', // redis is also supported
    'redis_config' => [ // If using redis you will also need these
        'host' => 'redis',
        'port' => '6379',
        'timeout' => '0',
        'password' => null,
    ],
    'name' => 'default', // Optionally use --name on the consumer to override this value at run time. 
    'enable_logging' => 'y',
    'enable_detailed_logging' => 'y',
    'backoff' => 0,
    'memory' => 1024,
    'timeout' => 120,
    'sleep' => 3,
    'max_tries' => 3,
    'force' => false,
    'stop_when_empty' => true,
    'max_jobs' => 1, // Optionally use --limit on the consumer to override this value at run time. 
    'max_time' => 120,
    'rest' => 0,
];
```

## For Developers

If you're unfamiliar with Laravel Queues it may be a good idea to review https://laravel.com/docs/12.x/queues.

Laravel's documentation has examples of how to construct a queue job handler. You can also refer to the examples here
in the Queue add-on itself. The `Queue/Jobs/` folder contains some examples that are used to test the queue.

To add queue support to your add-on wrap the data you usually process a run-time in a conditional that checks to see
if the Queue module files are present, and the module is actually installed. If not, process your data as usual. 
Whatever you do to proces your data will likely need to be replicated in some manor inside of your custom job handler.
Another thing to remember: make sure your job handler is properly namespaced, otherwise the file will not be found.
A lot of older ExpressionEngine add-ons used `require once 'myfile.php';` which will not work as a job handler.

```php
$dataToProcess = [];

if (ee('Addon')->get('queue')?->isInstalled()) {
    ee('queue:QueueManager')->push(MyJob::class, $dataToProcess);
} else {
    // Process the data now
}

```

If you want your add-on to manage it's own queue pass the name of the queue as the 3rd parameter. The default queue name
is unsurprisingly, `default`. If you use a custom queue be sure to instruct your users to add a queue consumer for that queue.

```php
ee('queue:QueueManager')->push(MyJob::class, 'payload', 'my_addon_queue')
```

## Coilpack

If using the Redis driver, and Coilpack, in your `coilpack/.env file` add the following. This is what works if using DDEV, but
you may need to make adjustments based on your environment configuration.

```dotenv
REDIS_URL=""
REDIS_HOST=redis
REDIS_USERNAME=null
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_PREFIX=""
REDIS_CLUSTER=""
```

## Horizon

If you want to take your queue management to the next level you can install [Coilpack](https://expressionengine.github.io/coilpack-docs/) 
and [Laravel Horizon](https://laravel.com/docs/12.x/horizon). Horizon provides a dashboard and code-driven configuration 
for your Laravel powered Redis queues. Horizon allows you to easily monitor key metrics of your queue system such as job 
throughput, runtime, and job failures. If you are using the database driver for Queue, then Horizon is not an option. 
It only works when using Redis.

If using Horizon, and you visit the Queue module page in the ExpressionEngine control panel, it will show a list of active
queues and their jobs, however, failed jobs will not show up in this interface. Queue is a simplified/standalone version
of Laravel's queue, and once Horizon is installed it manages failed jobs directly in Redis.

In most cases Queue by itself will be enough, but if you're processing hundreds of thousands of jobs and need more robust
monitoring and reporting, Horizon is definitely worth exploring.

To install Horizon cd into your `coilpack` directory and run the following commands.
```
composer require laravel/horizon
php artisan horizon:install
```

At this point you should be able to visit https://yoursite.com/horizon and you should see the Horizon interface. The 
installation is complete, but no supervisors have been started, so the interface will likely say "Inactive" in the upper
right corner. To start horizon, run `php artisan horizon`. If there are any items in your queue, they should start
processing.

## Creating Jobs as an add-on developer

Create a class anywhere in your add-on, but it needs to be properly namespaced. For consistency
creating a `Queue/Jobs` folder in your add-on may be a good place to put it. At minimum you need a class that contains
a `fire()` method accepting 2 parameters. Be sure to check out the [laravel documentation](https://laravel.com/docs/12.x/queues) 
for more in-depth documentation about queues.

```php
<?php

namespace BoldMinded\Queue\Queue\Jobs;

class TestJob implements ShouldQueue, ShouldBeUnique
{
    public function fire($job, $payload): bool
    {
        // Do stuff
    }
}
```

If you want to typehint the `$job` parameter, be sure to use a union. If someone uses your add-on and also uses Horizon, 
when Horizon executes the job it will expect the job namespace to be that of the `vendor`
folder in the Coilpack directory and not have any knowledge of the `BoldMinded\Queue\Dependency` namespace as it will
be executing outside of the ExpressionEngine domain. You could also choose to not typehint `$job` at all. It won't affect
the operation.

To see working examples you can run the following commands.

This will execute queue/Commands/CommandQueueTest.php, which adds 5 jobs to the queue, each an instance of `TestJob` (see below)

```bash
php system/ee/eecli.php queue:test`
```

Then run this command, which will process the jobs, which calls the `fire()` method for each job.

```bash
`php system/ee/eecli.php queue:work --limit=5`
```

Example job class you can use as a starting point.

```php
<?php

namespace BoldMinded\Queue\Queue\Jobs;

use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\Job;
use ExpressionEngine\Cli\CliFactory;

class TestJob implements ShouldQueue, ShouldBeUnique
{
    public function fire(
        Job|\Illuminate\Contracts\Queue\Job $job,
        string|array $payload): bool
    {
        $factory = new CliFactory();
        $output = $factory->newStdio();

        $output->outln('<<yellow>>Processed:<<reset>>');

        if (is_array($payload)) {
            $display = '<<dim>>'. json_encode($payload, JSON_UNESCAPED_UNICODE) .'<<reset>>';
        } else {
            $display = '<<dim>>'. $payload .'<<reset>>';
        }

        $output->outln($display);

        $job->delete();

        return true;
    }
}
```

## Creating a build

If you've checked out this repo from Github, you'll need to create a build of the React app for the EE control panel.

```bash
cd themes/user/app
pnpm install
pnpm build --emptyOutDir
```

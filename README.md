
## Creating a build

```bash
cd themes/user/app
pnpm build --emptyOutDir
```

## Consuming Jobs

Make sure a crontab is added to your server. This example will run very minute and process as many jobs
in the queue as it can until the process reaches the PHP timeout.

```bash
*/1 * * * * php /var/www/mysite.com/system/ee/eecli.php queue:work
```

If you want a to slow things down a bit add a woker that runs every 2 minutes and only processes 10 jobs at a time.
     
```bash
*/2 * * * * php /var/www/mysite.com/system/ee/eecli.php queue:work --limit=10
```  

Cron can only execute every minute at minimum. If you want something executing more frequently you can use a bash 
script that executes the worker every N seconds.

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

When choosing which method and how often a worker runs it is important to consider _what_ it is processing. If you are running
a cron every minute that ends up processing 100 jobs, and each job is long running task, it increases the chance of
the job failing. Running a worker more frequently and processing a smaller number of jobs helps reduce failures.

### Config overrides

Add some or all of the following to your config.php file to modify the default queue settings.

```php
$config['queue'] = [
    'enable_logging' => 'y',
    'enable_detailed_logging' => 'y',
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

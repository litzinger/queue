
## Creating a build

```bash
cd themes/user/app
pnpm build --emptyOutDir
```

## Consuming Jobs
Make sure the following crontab is added to your server. This will run very minute and process as many jobs
in the queue until the process reaches the PHP timeout.

```bash
*/1 * * * * php /var/www/mysite.com/system/ee/eecli.php queue:consume
```

If you want a to slow things down a bit add a consumer that runs every 2 minutes and only processes 10 jobs at a time.
     
```bash
*/2 * * * * php /var/www/mysite.com/system/ee/eecli.php queue:consume --limit=10
```  

You can also use a bash script that executes the consumer every second.

```bash
#!/bin/bash
while true; do
  php /var/www/html/system/ee/eecli.php queue:consume --limit=3
  sleep 1
done
```

You will need to use `nohup` to run the bash script:

```bash
nohup bash path/to/loop.sh &
```

### Config overrides

Add some or all of the following to your config.php file to modify the default queue settings.

```php
$config['queue'] = [<br />
    'name' => 'default', // Optionally use --name on the consumer to override this value at run time. <br />
    'enable_logging' => 'y',<br />
    'enable_detailed_logging' => 'y',<br />
    'backoff' => 0,<br />
    'memory' => 1024,<br />
    'timeout' => 120,<br />
    'sleep' => 3,<br />
    'max_tries' => 3,<br />
    'force' => false,<br />
    'stop_when_empty' => true,<br />
    'max_jobs' => 1, // Optionally use --limit on the consumer to override this value at run time. <br />
    'max_time' => 120,<br />
    'rest' => 0,<br />
];
```

### Adding to the queue

```php
ee('queue:QueueManager')->push(MyJob::class, 'payload')
```

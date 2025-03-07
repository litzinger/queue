<div class="panel">
    <div class="panel-heading">
        <h2>Items in queue: <?= $size ?></h2>
    </div>
    <div class="panel-body">
        <h3>Consuming Jobs</h3>
        <p>Make sure the following crontab is added to your server. This will run very minute and process as many jobs
            in the queue until the process reaches the PHP timeout.</p>
        <pre><code>
*/1 * * * * php /var/www/mysite.com/system/ee/eecli.php queue:consume
    </code></pre>

        <p>If you want a to slow things down a bit add a consumer that runs every 2 minutes and only processes 10 jobs at a time.</p>
        <pre><code>
*/2 * * * * php /var/www/mysite.com/system/ee/eecli.php queue:consume --limit=10
    </code></pre>

        <h3>Config overrides</h3>
        <p>Add some or all of the following to your config.php file to modify the default queue settings.</p>
        <pre>
<code>
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
</code>
</pre>

        <h3>Using</h3>
        <pre><code>ee('queue:QueueManager')->push(MyJob::class, 'payload')</code></pre>
    </div>
</div>


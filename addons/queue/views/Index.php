<script>
    window.APP_CONFIG = {
        urlBase: "<?= $urlBase ?>",
        urlQueueStatus: "<?= $urlQueueStatus ?>",
        urlPurgeAllPendingJobs: "<?= $urlPurgeAllPendingJobs ?>",
        urlGetFailedJob: "<?= $urlGetFailedJob ?>",
        urlRetryFailedJob: "<?= $urlRetryFailedJob ?>",
        urlDeleteFailedJob: "<?= $urlDeleteFailedJob ?>",
        csrfToken: "<?= $csrfToken ?>",
        queueDriver: "<?= $queueDriver ?>",
    };
</script>
<link rel="stylesheet" crossorigin href="<?= $assetPath ?>index.css">
<div id="queue-app"></div>
<script type="module" crossorigin src="<?= $assetPath ?>index.js"></script>

<?= ee('CP/Alert')->get('shared-form') ?>

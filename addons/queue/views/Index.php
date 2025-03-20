<script>
    window.APP_CONFIG = {
        urlQueueStatus: "<?= $urlQueueStatus ?>",
        urlPurgeAllPendingJobs: "<?= $urlPurgeAllPendingJobs ?>",
        urlRetryFailedJob: "<?= $urlRetryFailedJob ?>",
        csrfToken: "<?= $csrfToken ?>",
        queueDriver: "<?= $queueDriver ?>",
    };
</script>
<link rel="stylesheet" crossorigin href="<?= $assetPath ?>index.css">
<div id="queue-app"></div>
<script type="module" crossorigin src="<?= $assetPath ?>index.js"></script>

<?= ee('CP/Alert')->get('shared-form') ?>

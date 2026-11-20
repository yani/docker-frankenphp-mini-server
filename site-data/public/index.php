<?php

// Gather server & PHP info
$phpVersion = phpversion();
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'FrankenPHP';
$serverTime = date('Y-m-d H:i:s T');
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$memoryUsed = round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB';

// Extract FrankenPHP version if exposed in SERVER_SOFTWARE
$frankenVersion = 'Active';
if (preg_match('/FrankenPHP\/([^\s]+)/i', $serverSoftware, $matches)) {
    $frankenVersion = 'v' . $matches[1];
}

// Run an in-memory SQLite query + 
$sqliteStatus = 'Unavailable';
$sqliteOk = false;
try {
    if (class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers())) {
        $pdo = new PDO('sqlite::memory:');
        $stmt = $pdo->query('SELECT sqlite_version()');
        $sqliteVer = $stmt->fetchColumn();
        $sqliteStatus = "Connected (SQLite v{$sqliteVer})";
        $sqliteOk = true;
    }
} catch (Throwable $e) {
    $sqliteStatus = 'Error: ' . $e->getMessage();
}

// Check key extensions
$checkExtensions = ['Zend OPcache', 'pdo_mysql', 'pdo_sqlite', 'gd', 'intl', 'zip', 'mbstring', 'redis', 'bcmath', 'curl'];
$loadedExtensions = get_loaded_extensions();

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <title>FrankenPHP Mini Server</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
        }
        .table th, .table td {
            padding-left: 0.6rem;
        }
    </style>
</head>
<body class="bg-body-tertiary d-flex align-items-center min-vh-100 py-4">
    <div class="container" style="max-width: 720px;">
        <div class="card shadow-sm border-secondary-subtle">
            <div class="card-header bg-primary bg-gradient text-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h4 class="mb-0 fw-bold">⚡ FrankenPHP Mini Server</h4>
                    <small class="opacity-75">High-Performance PHP Application Server</small>
                </div>
                <span class="badge bg-light text-primary fs-6">Online</span>
            </div>

            <div class="card-body p-4">
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <div>
                        <strong>Server is live and executing PHP!</strong>
                        Upload your site files to <code>/public</code> via FTPS to replace this page.
                    </div>
                </div>

                <h6 class="text-uppercase text-muted fw-semibold mb-3">Server Environment</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle mb-0">
                        <tbody>
                            <tr>
                                <th class="w-35 bg-body-secondary">Runtime Engine</th>
                                <td><strong>FrankenPHP</strong> <span class="badge text-bg-info ms-1"><?= htmlspecialchars($frankenVersion) ?></span></td>
                            </tr>
                            <tr>
                                <th class="bg-body-secondary">PHP Version</th>
                                <td><code><?= htmlspecialchars($phpVersion) ?></code> (<?= htmlspecialchars(PHP_SAPI) ?>)</td>
                            </tr>
                            <tr>
                                <th class="bg-body-secondary">Server Software Header</th>
                                <td><code><?= htmlspecialchars($serverSoftware) ?></code></td>
                            </tr>
                            <tr>
                                <th class="bg-body-secondary">Embedded Database Test</th>
                                <td><span class="<?= $sqliteOk ? 'text-success' : 'text-danger' ?> fw-semibold"><?= htmlspecialchars($sqliteStatus) ?></span></td>
                            </tr>
                            <tr>
                                <th class="bg-body-secondary">Server Clock &amp; Script RAM</th>
                                <td><code><?= htmlspecialchars($serverTime) ?></code> &bull; Peak RAM: <code><?= htmlspecialchars($memoryUsed) ?></code></td>
                            </tr>
                            <tr>
                                <th class="bg-body-secondary">Your Detected IP</th>
                                <td><code><?= htmlspecialchars($clientIp) ?></code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="text-uppercase text-muted fw-semibold mb-2">Key PHP Extensions</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($checkExtensions as $ext): ?>
                        <?php $isLoaded = in_array($ext, $loadedExtensions); ?>
                        <span class="badge <?= $isLoaded ? 'text-bg-success' : 'text-bg-secondary' ?>">
                            <?= htmlspecialchars($ext) ?>: <?= $isLoaded ? '✓' : '✗' ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card-footer text-muted small d-flex justify-content-between">
                <span>Document Root: <code>/var/www/public</code></span>
                <span>Total Loaded Extensions: <?= count($loadedExtensions) ?></span>
            </div>
        </div>
    </div>
</body>
</html>
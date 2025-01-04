<?php

$configFile = __DIR__ . '/config/movements_config.php';

function loadConfig($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("Configuration file not found: $filePath");
    }
    return include $filePath;
}

function saveConfig($filePath, $config): void
{
    $configContent = "<?php\nreturn " . var_export($config, true) . ";\n";
    file_put_contents($filePath, $configContent);
}

try {
    $config = loadConfig($configFile);

    // Update the counters
    $config['movements_total_count'] += 1; // Increment the total count
    $config['movements_last_updated'] = date('Y-m-d H:i:s'); // Update timestamp

    // Save the updated configuration
    saveConfig($configFile, $config);

    echo "Configuration updated successfully.\n";
    echo "Total Count: " . $config['movements_total_count'] . "\n";
    echo "Last Updated: " . $config['movements_last_updated'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

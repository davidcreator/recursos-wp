<?php
try {
    require_once 'plugins/adsense-master-pro/adsense-master-pro.php';
    
    if (class_exists('AdSenseMasterPro')) {
        echo 'AdSenseMasterPro class exists' . PHP_EOL;
        
        $reflection = new ReflectionClass('AdSenseMasterPro');
        if ($reflection->hasMethod('preload_resources')) {
            echo 'Method preload_resources exists' . PHP_EOL;
        } else {
            echo 'Method preload_resources does NOT exist' . PHP_EOL;
        }
        
        if ($reflection->hasMethod('add_async_defer_attributes')) {
            echo 'Method add_async_defer_attributes exists' . PHP_EOL;
        } else {
            echo 'Method add_async_defer_attributes does NOT exist' . PHP_EOL;
        }
        
        // List all methods that contain 'preload' or 'async'
        echo "Relevant methods in the class:" . PHP_EOL;
        $methods = $reflection->getMethods();
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'preload') !== false || strpos($method->getName(), 'async') !== false) {
                echo "- " . $method->getName() . PHP_EOL;
            }
        }
    } else {
        echo 'AdSenseMasterPro class does NOT exist' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
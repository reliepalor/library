<?php
if (extension_loaded('imagick')) {
    echo "Imagick extension is loaded.\n";
    $imagick = new Imagick();
    echo "Imagick version: " . $imagick->getVersion()['versionString'] . "\n";
} else {
    echo "Imagick extension is NOT loaded.\n";
}
?>

<?php

function getFolderStructure($folderPath) {
    $folderStructure = array();
    $files = scandir($folderPath);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $folderPath . '/' . $file;

        if (is_dir($filePath)) {
            $folderStructure[] = array(
                'text' => $file,
                'children' => getFolderStructure($filePath),
                'icon' => 'jstree-folder',
            );
        } else {
            // Icon basierend auf Dateityp festlegen
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $icon = getIconForExtension($extension);

            $folderStructure[] = array(
                'text' => $file,
                'icon' => $icon
            );
        }
    }

    return $folderStructure;
}

function getIconForExtension($extension) {
    // Mapping von Dateierweiterung zu Icon-Klassen
    $iconMapping = array(
        'mp4' => 'icon icon-media',
        // weitere Dateierweiterungen und Icons hier hinzufügen
    );

    // Standard-Icon, wenn keine Übereinstimmung gefunden wurde
    $defaultIcon = 'jstree-file';

    // Überprüfen, ob ein spezifisches Icon für die Erweiterung vorhanden ist
    if (isset($iconMapping[$extension])) {
        return $iconMapping[$extension];
    }

    return $defaultIcon;
}

$rootFolder = $_SERVER['DOCUMENT_ROOT'].'/media/';
$folderStructure = getFolderStructure($rootFolder);

header('Content-Type: application/json');
echo json_encode($folderStructure);
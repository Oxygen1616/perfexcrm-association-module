<?php
$request = $_SERVER['REQUEST_URI'];
$basepath = '/perfex-crm';

if (strpos($request, '/membership') === 0) {
    $path = str_replace($basepath, '', $request);
    if ($path === '/membership' || $path === '/membership/') {
        header('Location: index.php?url=membership');
    } else {
        $path = ltrim($path, '/');
        header('Location: index.php?url=' . $path);
    }
    exit;
}

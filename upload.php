<?php
require 'Appsun.php';
require 'Exception.php';

$options = getopt("v:k::a:u:f:");
if (empty($options["k"])) {
    throw new \carono\appsun\Exception('Need set software key');
}
$appsun = new \carono\appsun\Appsun();
$appsun->system_name = $options["k"];
$appsun->version = isset($options['v']) ? $options['v'] : $appsun->getNextVersion()->value;
if (isset($options["v"])) {
    exit($appsun->version);
}
if (isset($options["u"])) {
    if (empty($options["a"])) {
        throw new \carono\appsun\Exception('Need set upload key');
    }
    $appsun->api = $options["a"];
    $data = require $options["f"];
    if (isset($data["installer"])) {
        foreach ($data["installer"] as $file => $slug) {
            $appsun->uploadInstaller($file, $slug);
        }
    }
    if (isset($data["files"])) {
        $appsun->uploadFiles($data["files"]);
    }
}
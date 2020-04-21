<?php
use \Workerman\Worker;

// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';

$worker = new GlobalData\Server('127.0.0.1', 2207);

Worker::runAll();
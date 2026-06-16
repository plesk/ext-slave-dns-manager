<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.
pm_Loader::registerAutoload();
pm_Context::init('slave-dns-manager');

$jsonInput = file_get_contents('php://stdin');
$data = json_decode($jsonInput);
if (!is_array($data)) {
    echo "Invalid json data input: $jsonInput\n";
    exit(1);
}

foreach ($data as $task) {
    $command = (string)$task->command;
    if (!in_array($command, ['create', 'update', 'delete'])) {
        continue;
    }

    $domain = substr((string)$task->zone->name, 0, -1);
    if (!$domain) {
        echo "Invalid zone name: {$task->zone->name}\n";
        continue;
    }

    if ($command !== 'delete') {
        try {
            $request = "<dns><get_rec><filter><site-name>{$domain}</site-name></filter></get_rec></dns>";
            $response = pm_ApiRpc::getService('1.6.9.1')->call($request);
            if ('ok' !== (string)$response->dns->get_rec->result->status) {
                continue;
            }
        } catch (\Exception $e) {
            continue;
        }
    }

    $rndc = new Modules_SlaveDnsManager_Rndc();

    switch ($command) {
        case 'create':
            $rndc->addZone($domain);
            break;
        case 'update':
            $rndc->updateZone($domain);
            break;
        case 'delete':
            $rndc->deleteZone($domain);
            break;
    }
}

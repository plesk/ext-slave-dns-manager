<?php
// Copyright 1999-2021. Plesk International GmbH. All rights reserved.

class Modules_SlaveDnsManager_CustomBackendService
{
    public function enable(): void
    {
        pm_ApiCli::call('server_dns', ['--enable-custom-backend', $this->getCommand()]);
    }

    /**
     * @param Zend_Db_Adapter_Abstract $db
     * @return bool
     */
    public function isEnabled(Zend_Db_Adapter_Abstract $db): bool
    {
        $select = $db->select()
            ->from('ServiceNodeConfiguration', ['value'])
            ->where("section='dnsConnector' AND name='custom'");
        $row = $db->fetchRow($select);
        return !empty($row['value']) && $this->checkScriptCommand($db);
    }

    /**
     * @param Zend_Db_Adapter_Abstract $db
     * @return bool
     */
    private function checkScriptCommand(Zend_Db_Adapter_Abstract $db): bool
    {
        $select = $db->select()->from('ServiceNodeConfiguration', ['value'])->where("section='dnsConnector' AND name='custom_script'");
        $row = $db->fetchRow($select);
        return isset($row['value']) && trim($row['value']) === $this->getCommand();
    }

    private function getCommand(): string
    {
        $moduleId = pm_Context::getModuleId();
        $fileManager = new pm_ServerFileManager();
        $productRoot = pm_ProductInfo::getProductRootDir();
        $cmd = pm_ProductInfo::isWindows()
            ? $fileManager->joinPath($productRoot, 'bin', 'extension.exe')
            : $fileManager->joinPath($productRoot, 'bin', 'extension');
        return "\"{$cmd}\" --exec {$moduleId} slave-dns.php";
    }
}

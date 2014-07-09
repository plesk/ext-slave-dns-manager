<?php
// Copyright 1999-2014. Parallels IP Holdings GmbH. All Rights Reserved.
class Modules_SlaveDnsManager_List_Slaves extends pm_View_List_Simple
{
    public function __construct(Zend_View $view, Zend_Controller_Request_Abstract $request)
    {
        parent::__construct($view, $request);

        $data = array();
        foreach (Modules_SlaveDnsManager_Slave::getList() as $slave) {
            try {
                $rndc = new Modules_SlaveDnsManager_Rndc();
                $details = $rndc->checkStatus($slave);
                $icon = 'ok';
            } catch (Exception $e) {
                $details = $e->getMessage();
                $icon = 'warning';
            }

            $data[] = array(
                'select' => '<input type="checkbox" class="checkbox" name="listCheckbox[]" value="' . (string)$slave->getConfig() . '"/>',
                'status' => '<img class="slave-status" src="/theme/icons/16/plesk/' . $icon . '.png" title="' . $details . '"/>',
                'config' => (string)$slave->getConfig(),
            );
        }

        $this->setData($data);
        $this->setColumns(array(
            'select' => array(
                'title' => '<input type="checkbox" class="checkbox" name="listGlobalCheckbox"/>',
                'sortable' => false,
                'noEscape' => true,
            ),
            'status' => array(
                'title' => $this->lmsg('statusColumnTitle'),
                'sortable' => false,
                'noEscape' => true,
            ),
            'config' => array(
                'title' => $this->lmsg('configColumnTitle'),
            ),
        ));
        $this->setTools(array(
                 array(
                     'title'       => $this->lmsg('addToolTitle'),
                     'description' => $this->lmsg('addToolDescription'),
                     'class'       => 'sb-add-new',
                     'link'        => $view->getHelper('baseUrl')->moduleUrl(array('action' => 'add')),
                 ),
                 array(
                     'title'       => $this->lmsg('refreshToolTitle'),
                     'description' => $this->lmsg('refreshToolDescription'),
                     'class'       => 'sb-refresh',
                     'link'        => pm_Context::getBaseUrl(),
                 ),
                 array(
                     'title'       => $this->lmsg('removeToolTitle'),
                     'description' => $this->lmsg('removeToolDescription'),
                     'class'       => 'sb-remove-selected',
                     'link'        => 'javascript:removeSlaves()',
                 ),
                 array(
                     'title'       => $this->lmsg('helpToolTitle'),
                     'description' => $this->lmsg('helpToolDescription'),
                     'class'       => 'sb-help',
                     'link'        => $view->getHelper('baseUrl')->moduleUrl(array('action' => 'help')),
                 ),
        ));
        $this->setDataUrl(array('action' => 'list-data'));
    }
}
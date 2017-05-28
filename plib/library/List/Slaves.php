<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.
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

            $ip = $view->escape((string)$slave->getIp());
            $config = $view->escape((string)$slave->getConfig());
            $data[] = array(
                'select' => '<input type="checkbox" class="checkbox" name="listCheckbox[]" value="' . $config . '"/>',
                'status' => '<img class="slave-status" src="/theme/icons/16/plesk/' . $icon . '.png" title="' . $view->escape($details) . '"/>',
                'config' => '<a href="' . $view->getHelper('baseUrl')->moduleUrl(array('action' => 'view')) . '?config=' . $config . '">' . $ip . '</a>',
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
                'noEscape' => true,
            ),
            'config' => array(
                'title' => $this->lmsg('configColumnTitle'),
                'noEscape' => true,
            ),
        ));
        $buttons = array(
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
        );
        if (version_compare(pm_ProductInfo::getVersion(), '17.0') >= 0) {
            $buttons[] = array(
                     'title'       => $this->lmsg('resyncToolTitle'),
                     'description' => $this->lmsg('resyncToolDescription'),
                     'class'       => 'sb-restart',
                     'link'        => 'javascript:resyncZones()',
                 );
        }
        $this->setTools($buttons);
        $this->setDataUrl(array('action' => 'list-data'));
    }
}


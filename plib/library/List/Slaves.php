<?php

class Modules_SlaveDnsManager_List_Slaves extends pm_View_List_Simple
{
    public function __construct(Zend_View $view, Zend_Controller_Request_Abstract $request)
    {
        parent::__construct($view, $request);

        $data = array();
        foreach (Modules_SlaveDnsManager_Slave::getList() as $slave) {
            $data[] = array(
                'select' => '<input type="checkbox" class="checkbox" name="listCheckbox[]" value="' . (string)$slave->getConfig() . '"/>',
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
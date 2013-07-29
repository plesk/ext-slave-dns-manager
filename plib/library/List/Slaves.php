<?php

class Modules_SlaveDnsManager_List_Slaves extends pm_View_List_Simple
{
    public function __construct(Zend_View $view, Zend_Controller_Request_Abstract $request)
    {
        parent::__construct($view, $request);

        $data = array();
        foreach (Modules_SlaveDnsManager_Slave::getList() as $slave) {
            $data[] = array(
                'config' => (string)$slave->getConfig(),
            );
        }

        $this->setData($data);
        $this->setColumns(array(
            'config' => array(
                'title' => 'Configuration',
            ),
        ));
        $this->setTools(array(
                 array(
                     'title'       => 'Add Slave',
                     'description' => 'Create remote slave configuration',
                     'class'       => 'sb-add-new',
                     'link'        => $view->getHelper('baseUrl')->moduleUrl(array('action' => 'add')),
                 ),
        ));
        $this->setDataUrl(array('action' => 'list-data'));
    }
}
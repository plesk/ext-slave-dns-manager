<?php

class IndexController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();

        if (!pm_Session::getClient()->isAdmin()) {
            throw new pm_Exception('Permission denied');
        }
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $this->view->pageTitle = 'Slave DNS Manager';

        $slavesList = new Modules_SlaveDnsManager_List_Slaves($this->view, $this->_request);
        $this->view->list = $slavesList;
    }

    public function listDataAction()
    {
        $slavesList = new Modules_SlaveDnsManager_List_Slaves($this->view, $this->_request);
        $this->_helper->json($slavesList->fetchData());
    }

    public function addAction()
    {
        $this->view->pageTitle = 'Create remote slave configuration';
        $this->view->uplevelLink = pm_Context::getBaseUrl();

        $form = new Modules_SlaveDnsManager_Form_Add();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $form->process();

            $this->_status->addMessage('info', 'Slave configuration was successfully saved.');
            $this->_helper->json(array('redirect' => pm_Context::getBaseUrl()));
        }

        $this->view->form = $form;
    }

    public function helpAction()
    {
        $this->view->pageTitle = 'How to setup remote slave server';
        $this->view->uplevelLink = pm_Context::getBaseUrl();
    }
}

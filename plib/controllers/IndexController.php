<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.
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

    public function enableCustomBackendAction()
    {
        try {
            (new Modules_SlaveDnsManager_CustomBackendService())->enable();
            $this->_status->addInfo($this->lmsg('customBackendEnabled'));
        } catch (\Exception $e) {
            $this->_status->addError($this->lmsg('customBackendEnablingError', ['error' => $e->getMessage()]));
        }

        $this->_redirect('/');
    }

    public function listAction()
    {
        $this->view->pageTitle = $this->lmsg('listPageTitle');

        if (!(new Modules_SlaveDnsManager_CustomBackendService())->isEnabled(pm_Bootstrap::getDbAdapter())){
            $this->_status->addWarning($this->lmsg('customBackendAlert', [
                'enableUrl' => pm_Context::getActionUrl('index', 'enable-custom-backend')
            ]), true);
        }

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
        $this->view->pageTitle = $this->lmsg('addPageTitle');
        $this->view->uplevelLink = pm_Context::getBaseUrl();

        $form = new Modules_SlaveDnsManager_Form_Add();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $form->process();

            $this->_status->addMessage('info', $this->lmsg('slaveSaved'));
            $this->_helper->json(array('redirect' => pm_Context::getBaseUrl()));
        }

        $this->view->form = $form;
    }

    public function viewAction()
    {
        $this->view->pageTitle = $this->lmsg('viewPageTitle');
        $this->view->uplevelLink = pm_Context::getBaseUrl();

        $config = $this->_getParam('config');
        $slave = new Modules_SlaveDnsManager_Slave($config);
        $this->view->form = new Modules_SlaveDnsManager_Form_View($slave);
    }

    public function resyncAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new pm_Exception('Method POST is required');
        }

        pm_ApiCli::call('repair', ['--dns', '-sync-zones', '-y']);

        $this->_status->addInfo($this->lmsg('resyncDone'));
        $this->_redirect('/');
    }

    public function removeAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new pm_Exception('Method POST is required');
        }

        $configs = $this->_getParam('config');
        if (!$configs || !is_array($configs)) {
            $this->_status->addMessage('error', $this->lmsg('emptySelection'));
            $this->_redirect('/');
        }

        foreach ($configs as $config) {
            try {
                $slave = new Modules_SlaveDnsManager_Slave($config);
                $slave->remove();
                $this->_status->addMessage('info', $this->lmsg('slaveRemoved'));
            } catch (pm_Exception $e) {
                $this->_status->addMessage('error', $e->getMessage());
            }
        }
        $this->_redirect('/');
    }
}

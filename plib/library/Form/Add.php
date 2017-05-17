<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.
class Modules_SlaveDnsManager_Form_Add extends pm_Form_Simple
{
    public function init()
    {
        parent::init();

        $this->addElement('select', 'masterIp', array(
            'label' => $this->lmsg('masterIpLabel'),
            'multiOptions' => $this->_getIps(),
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Ip', true),
            ),
        ));
        $this->addElement('text', 'ip', array(
            'label' => $this->lmsg('ipLabel'),
            'value' => '',
            'class' => 'f-large-size',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Ip', true),
                array('Callback', true, array(array($this, 'isExistingSlave'))), 
            ),
        ));
        $this->addElement('text', 'port', array(
            'label' => $this->lmsg('portLabel'),
            'value' => '953',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
        ));
        $this->addElement('select', 'algorithm', array(
            'label' => $this->lmsg('algorithmLabel'),
            'multiOptions' => array('hmac-md5' => 'hmac-md5',),
            'value' => 'hmac-md5',
            'required' => true,
        ));
        $this->addElement('text', 'secret', array(
            'label' => $this->lmsg('secretLabel'),
            'value' => $this->_getRandomSecret(),
            'class' => 'f-large-size',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Callback', true, array(array($this, 'isValidSecret'))),
            ),
        ));
        $this->addElement('text', 'rndcView', array(
            'label' => $this->lmsg('viewLabel'),
            'value' => '',
            'required' => false,
            'validators' => array(
                array('Callback', true, array(array($this, 'isValidAlnumDashUnderscore'))),
            ),
        ));

        $this->addControlButtons(array(
            'cancelLink' => pm_Context::getBaseUrl(),
        ));
    }

    public function process()
    {
        $slave = new Modules_SlaveDnsManager_Slave();
        $slave->save($this->getValues());
    }

    public function isExistingSlave($data)
    {
        $slave = new Modules_SlaveDnsManager_Slave("slave_$data.conf");
        
        if (!file_exists($slave->getConfigPath())) {
            return true;
        }
        throw new pm_Exception($this->lmsg('invalidIpExistingSlave'));
    }

    public function isValidSecret($data)
    {
        if (base64_encode(base64_decode($data)) === $data) {
            return true;
        }
        throw new pm_Exception($this->lmsg('invalidSecret'));
    }

    public function isValidAlnumDashUnderscore($data)
    {
        if (preg_match('/^[a-zA-Z0-9_-]+$/', $data)) {
            return true;
        }
        throw new pm_Exception($this->lmsg('invalidAlnumDashUnderscore'));
    }

    private function _getIps()
    {
        return Modules_SlaveDnsManager_IpAddress::getAvailable();
    }

    private function _getRandomSecret()
    {
        mt_srand((double)microtime() * 1000000);
        $secret = md5(uniqid(mt_rand()));
        $secret = substr($secret, 0, 22);
        $secret = base64_encode($secret);
        return $secret;
    }
}

<?php
// Copyright 1999-2015. Parallels IP Holdings GmbH.
class Modules_SlaveDnsManager_Form_Add extends pm_Form_Simple
{
    public function init()
    {
        parent::init();

        $this->addElement('text', 'ip', array(
            'label' => $this->lmsg('ipLabel'),
            'value' => '',
            'class' => 'f-large-size',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Ip', true),
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

        $this->addControlButtons(array(
            'cancelLink' => pm_Context::getBaseUrl(),
        ));
    }

    public function process()
    {
        $slave = new Modules_SlaveDnsManager_Slave();
        $slave->save($this->getValues());
    }

    public function isValidSecret($data)
    {
        if (base64_encode(base64_decode($data)) === $data) {
            return true;
        }
        throw new pm_Exception($this->lmsg('invalidSecret'));
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

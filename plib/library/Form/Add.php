<?php

class Modules_SlaveDnsManager_Form_Add extends pm_Form_Simple
{
    public function init()
    {
        parent::init();

        $this->addElement('text', 'ip', array(
            'label' => $this->lmsg('ipLabel'),
            'value' => '',
            'class' => 'f-middle-size',
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
            'value' => '',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
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
}
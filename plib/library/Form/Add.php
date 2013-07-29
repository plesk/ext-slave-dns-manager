<?php

class Modules_SlaveDnsManager_Form_Add extends pm_Form_Simple
{
    public function init()
    {
        parent::init();

        $this->addElement('text', 'ip', array(
            'label' => 'IP Address',
            'value' => '',
            'class' => 'f-middle-size',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Ip', true),
            ),
        ));
        $this->addElement('text', 'port', array(
            'label' => 'Port',
            'value' => '953',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
            ),
        ));
        $this->addElement('select', 'algorithm', array(
            'label' => 'Algorithm',
            'multiOptions' => array('hmac-md5' => 'hmac-md5',),
            'value' => 'hmac-md5',
            'required' => true,
        ));
        $this->addElement('text', 'secret', array(
            'label' => 'Secret',
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

        $acl = new Modules_SlaveDnsManager_Acl();
        $acl->add($this->getValue('ip'));
    }
}
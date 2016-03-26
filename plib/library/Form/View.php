<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.
class Modules_SlaveDnsManager_Form_View extends pm_Form_Simple
{
    private $_slave;

    public function __construct(Modules_SlaveDnsManager_Slave $slave)
    {
        $this->_slave = $slave;

        parent::__construct();
    }

    public function init()
    {
        parent::init();

        $this->addElement('simpleText', 'path', array(
            'label' => $this->lmsg('pathLabel'),
            'value' => $this->_slave->getConfigPath(),
        ));

        $this->addElement('textarea', 'config', array(
            'value' => $this->_slave->content(),
            'decorators' => [
                ['Callback', ['callback' => [$this, 'decorateConfig']]],
            ],
        ));

        $this->addControlButtons(array(
            'hideLegend' => true,
            'sendHidden' => true,
            'cancelLink' => pm_Context::getBaseUrl(),
        ));
    }

    public function decorateConfig($content, Zend_Form_Element $element, array $options)
    {
        $view = $this->getView();
        $value = $view->escape($element->getValue());

        return '<textarea name="' . $view->escape($element->getName()) . '"'
            . ' class="f-max-size" readonly="readonly"'
            . ' id="' . $view->escape($element->getId()) . '"'
            . ' rows="' . (substr_count($value, "\n") + 1) . '" cols="80" style="width: 100%;"'
            . '>' . $value . '</textarea>';
    }
}

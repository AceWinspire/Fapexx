<?php
/**
 * Override translate helper
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Dusan Bulovan <dusan.bulovan@golive.rs>
 *
 * @version  october 2014
 */
class Zend_View_Helper_Translate extends Zend_View_Helper_Abstract {

    /**
     * If there is no translation for current language get for default one
     *
     * @return String translate value
     */
    public function translate($key) {
        return Zend_Registry::isRegistered($key) ? Zend_Registry::get($key) : $key;
    }
}
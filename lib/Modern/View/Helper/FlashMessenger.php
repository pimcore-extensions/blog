<?php

/**
 * ModernWeb
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modernweb.pl/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@modernweb.pl so we can send you a copy immediately.
 *
 * @category    Modern
 * @package     Modern_View
 * @subpackage  Helper
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * @category    Modern
 * @package     Modern_View
 * @subpackage  Helper
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
    /**
     * @var Modern_Controller_Action_Helper_FlashMessenger
     */
    protected $_messenger;

    /**
     * Current messages HTML rendered via messenger() call.
     *
     * @var string
     */
    protected $_html;

    /**
     * Messenger partial view script path.
     *
     * @var string
     */
    protected static $_partial;

    /**
     * Helper initiation.
     *
     */
    public function __construct()
    {
        $this->_messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    }

    /**
     * Display previous or current flash messages from
     * Modern_Controller_Action_Helper_FlashMessenger.
     *
     * @param boolean $current
     * @return string
     */
    public function flashMessenger($current = false)
    {
        $this->_html = '';

        $hasMessages = ($current)
            ? $this->_messenger->hasCurrentMessages()
            : $this->_messenger->hasMessages();
        if (!$this->_messenger || !$hasMessages) {
            return $this;
        }

        $messages = ($current)
            ? $this->_messenger->getCurrentMessages()
            : $this->_messenger->getMessages();

        if (self::$_partial) {
            $this->_html = $this->view->partial(self::$_partial, array(
                'messages' => $messages,
            ));
        } else {
            $tpl = '<div class="alert alert-%s">%s</div>';
            foreach ($messages as $message) {
                $this->_html .= sprintf($tpl, $message['type'], $message['body']) . PHP_EOL;
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->_html;
    }

    /**
     * @param Zend_Config|array $options
     * @return \Modern_View_Helper_Messenger
     */
    public function setOptions($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (isset($options['partial'])) {
            self::setPartial($options['partial']);
        }

        return $this;
    }

    /**
     * Set messenger partial view script.
     *
     * Path must be relative to one of the registered view script paths.
     *
     * @param string $partial
     */
    public static function setPartial($partial) {
        self::$_partial = $partial;
    }

}

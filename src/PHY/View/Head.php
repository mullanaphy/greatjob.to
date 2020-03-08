<?php

    /**
     * greatjob.to
     *
     * LICENSE
     *
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * http://opensource.org/licenses/osl-3.0.php
     * If you did not receive a copy of the license and are unable to
     * obtain it through the world-wide-web, please send an email
     * to hi@john.mu so we can send you a copy immediately.
     */

    namespace PHY\View;

    use PHY\Event\Item as EventItem;
    use PHY\Model\Config;

    /**
     * Head block.
     *
     * @package PHY\View\Head
     * @category PHY\GreatJob
     * @copyright Copyright (c) 2018 John Mullanaphy (https://john.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <hi@john.mu>
     */
    class Head extends AView
    {

        /**
         * {@inheritDoc}
         */
        public function structure()
        {
            /** @var \PHY\Controller\IController $controller */
            $controller = $this->getLayout()->getController();

            /** @var \PHY\App $app */
            $app = $controller->getApp();
            $event = new EventItem('block/core/head', [
                'xsrfId' => $app->getXsrfId(),
            ]);
            $this->setTemplate('core/sections/head.phtml')->setVariable('xsrfId', $event->xsrfId);

            /** @var \PHY\Database\IDatabase $database */
            $database = $app->get('database');
            $manager = $database->getManager();
            $googleAnalytics = $manager->load(['key' => 'googleAnalytics'], new Config);
            if ($googleAnalytics->value) {
                $this->setVariable('googleAnalytics', $googleAnalytics->value);
            }
        }

        /**
         * Add files to the header.
         *
         * @param string [, ...] $files
         * @return $this
         */
        public function add()
        {
            $files = func_get_args();
            $_files = $this->getVariable('files');
            foreach ($files as $file) {
                if (is_array($file)) {
                    call_user_func_array([$this, 'add'], $file);
                } else {
                    $extension = explode('.', $file);
                    $_files[$extension[count($extension) - 1]][] = $file;
                }
            }
            $this->setVariable('files', $_files);
            return $this;
        }

    }

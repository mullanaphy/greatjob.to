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

    /**
     * Footer block.
     *
     * @package PHY\View\Footer
     * @category PHY\GreatJob
     * @copyright Copyright (c) 2018 John Mullanaphy (https://john.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <hi@john.mu>
     */
    class Footer extends AView
    {

        /**
         * {@inheritDoc}
         */
        public function structure()
        {
            $this->setTemplate('core/sections/footer.phtml')->setVariable('app', $app = $this->getLayout()
                    ->getController()
                    ->getApp());
        }

        /**
         * Add files to the footer.
         *
         * @param string [, ...] $files
         * @return $this
         */
        public function add()
        {
            return $this;
        }

    }

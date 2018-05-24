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
     * to john@jo.mu so we can send you a copy immediately.
     */

    namespace PHY\Controller;

    use PHY\Model\Name;

    /**
     * Home page.
     *
     * @package PHY\Controller\Index
     * @category PHY\GreatJob
     * @copyright Copyright (c) 2018 John Mullanaphy (https://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Index extends AController
    {

        /**
         * GET /
         */
        public function index_get()
        {
            $app = $this->getApp();

            /** @var \PHY\Database\IDatabase $database */
            $database = $app->get('database');
            $manager = $database->getManager();

            $response = $this->getResponse();
            $request = $this->getRequest();

            $name = $request->getActionName();

            $layout = $this->getLayout();
            $content = $layout->block('content');
            if (!$name) {
                $content->setTemplate('name/content.phtml');
                return $response;
            }

            $nameItem = $manager->load(['slug' => $name], new Name);
            $nameItem->count = $nameItem->count + 1;
            $manager->save($nameItem);

            $actions = $layout->block('actions');
            if ($nameItem->banned) {
                $response->setStatusCode(403);
                $content->setTemplate('name/banned.phtml');
                $content->setVariable('reason', $nameItem->reason);
                $actions->setVariable('name', '');
                return $response;
            }

            $content->setTemplate('name/content.phtml');
            $content->setVariable('name', $name);
            $content->setVariable('count', $nameItem->count);
            $actions->setVariable('name', $name);

            return $response;
        }

    }

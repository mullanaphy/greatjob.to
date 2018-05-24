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
    use PHY\Model\Name\Event as NameEvent;

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

            $name = ltrim($request->getPath(), DIRECTORY_SEPARATOR);
            $name = str_replace(DIRECTORY_SEPARATOR, ' ', $name);

            $layout = $this->getLayout();
            $content = $layout->block('content');
            if (!$name || $request->getActionName() === '__index') {
                $actions = $layout->block('modal');
                $actions->setTemplate('name/index/actions.phtml');
                $content->setTemplate('name/index.phtml');
                return;
            }

            $matches = [];
            preg_match('/' . join('|', $app->get('config/bummers')) . '/', strtolower($name), $matches);
            $banned = count($matches) > 0;

            $nameItem = $manager->load(['slug' => $name], new Name);
            $currentTime = date('Y-m-d H:i:s');
            if (!$nameItem->id()) {
                $nameItem->slug = $name;
                $nameItem->created = $currentTime;
                $nameItem->banned = $banned;
            } else {
                $nameItem->updated = $currentTime;
            }
            $nameItem->count = $nameItem->count + 1;
            $manager->save($nameItem);

            // Anonymous visitor will just be 0.0.0.0, otherwise we'll strip off the last octet since we don't need to
            // be precise when storing visitors, we just want to be able to get a general idea of local at a later date.
            $visitor = '0.0.0.0';
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $visitor = explode('.', $_SERVER['REMOTE_ADDR']);
                $visitor[count($visitor) - 1] = '0';
                $visitor = ip2long(implode('.', $visitor));
            }

            $nameEvent = new NameEvent;
            $nameEvent->name_id = $nameItem->id();
            $nameEvent->visitor = $visitor;
            $nameEvent->created = $currentTime;
            $manager->save($nameEvent);

            $actions = $layout->block('modal');
            $actions->setTemplate('name/actions.phtml');
            $actions->setVariable('id', $nameItem->id());
            if ($nameItem->banned) {
                $response->setStatusCode(403);
                $content->setTemplate('name/banned.phtml');
                $content->setVariable('reason', $nameItem->reason);
                $actions->setVariable('name', '');
                return;
            }

            $content->setTemplate('name/content.phtml');
            $content->setVariable('name', $name);
            $content->setVariable('count', $nameItem->count);
            $actions->setVariable('name', $name);
        }
    }

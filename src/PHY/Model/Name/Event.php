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

    namespace PHY\Model\Name;

    use PHY\Model\Entity;

    /**
     * Name of a viewer.
     *
     * @package PHY\Model\Name\Event
     * @category PHY\GreatJob
     * @copyright Copyright (c) 2018 John Mullanaphy (https://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class Event extends Entity
    {

        protected static $_source = [
            'schema' => [
                'primary' => [
                    'table' => 'name_event',
                    'columns' => [
                        'name_id' => 'variable',
                        'visitor' => 'int',
                        'created' => 'date',
                    ],
                    'keys' => [
                        'local' => [
                            'name_id' => 'index',
                        ],
                    ],
                ],
            ],
        ];

    }

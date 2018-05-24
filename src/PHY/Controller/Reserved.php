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

    use PHY\Exception;
    use PHY\Http\Exception as HttpException;
    use PHY\Model\Report;

    /**
     * My admin panel. Theoretically this should probably be broken up into smaller controllers but #yolo.
     *
     * @package PHY\Controller\Admin
     * @category PHY\GreatJob
     * @copyright Copyright (c) 2018 John Mullanaphy (https://jo.mu/)
     * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     * @author John Mullanaphy <john@jo.mu>
     */
    class reserved extends AController
    {
        protected $message = 'Sorry, seems like some stuff broke... Please don\'t judge me harshly...';
        protected $statusCode = 500;
        protected $exception = null;

        protected static $contentTypes = [
            'css' => 'text/css; charset=utf-8',
            'js' => 'text/javascript; charset=utf-8',
        ];

        protected function minify($method = 'index')
        {
            /** @var \MatthiasMullie\Minify\Minify $minifier */
            if (isset(self::$contentTypes[$method])) {
                $type = $method;
            } else {
                $type = 'css';
            }
            $app = $this->getApp();
            $request = $this->getRequest();
            $response = $this->getResponse();
            $response->setHeader('Content-Type', self::$contentTypes[$type]);

            /** @var \PHY\Cache\ICache $cache */
            $cache = $app->get('cache/resources');

            $path = explode('/m/' . $type . '/', $request->getUrl());
            array_shift($path);
            $path = implode('', $path);

            $generated = $app->getRootDirectory() . 'var' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'minified_' . md5($path);
            $content = $cache->get($path);
            if (!is_file($generated) || md5($content) !== file_get_contents($generated)) {
                $class = '\MatthiasMullie\Minify\\' . strtoupper($type);
                $files = explode(',', $path);
                $file = $app->getPublicDirectory() . $this->url(array_shift($files), $type);
                if (!is_readable($file)) {
                    $minifier = new $class('!1;');
                } else {
                    $minifier = new $class(file_get_contents($file));
                }
                if ($files) {
                    $root = $app->getPublicDirectory();
                    foreach ($files as $file) {
                        $file = $root . $this->url($file, $type);
                        if (is_readable($file)) {
                            $minifier->add(file_get_contents($file));
                        }
                    }
                }
                $content = $minifier->minify();
                $cache->set($path, $content);
                if (file_exists($generated)) {
                    @unlink($generated);
                } else {
                    touch($generated);
                }
                file_put_contents($generated, md5($content));
            }
            if ($content === '!1;') {
                $response->setStatusCode(404);
                return $response;
            }

            $lastModified = filemtime($generated);
            $etagFile = file_get_contents($generated);
            $response->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
            $response->setHeader('Etag', $etagFile);
            $response->setHeader('Cache-Control', 'public');

            $ifNotModifiedSince = $request->getEnvironmental('HTTP_IF_NOT_MODIFIED_SINCE', false);
            $etagHeader = $request->getEnvironmental('HTTP_IF_NONE_MATCH', false);

            if (strtotime($ifNotModifiedSince) === $lastModified || $etagHeader === $etagFile) {
                $response->setStatusCode(304);
                return $response;
            }

            $response->setContent([$content]);
            $response->setCompression(true);
            return $response;
        }

        /**
         * {@inheritDoc}
         */
        public function action($action = 'index')
        {
            if ($action === 'm') {
                return $this->minify($action);
            }

            parent::action($action);
        }

        /**
         * POST /reserved/report
         */
        public function report_post()
        {
            $created = date('Y-m-d H:i:s');
            $fields = $this->getRequest()->get('report', [
                'name' => '',
                'email' => '',
                'content' => '',
                'created' => $created,
            ]);
            try {

                $app = $this->getLayout()->getController()->getApp();
                /**
                 * @var \PHY\Database\IDatabase $database
                 */
                $database = $app->get('database');
                $manager = $database->getManager();

                if (!array_key_exists('slug', $fields) || !$fields['slug']) {
                    throw new Exception('Missing slug.');
                }
                if (!array_key_exists('email', $fields) || !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
                    $fields['email'] = '';
                }
                if (!array_key_exists('comment', $fields) || !$fields['comment']) {
                    $fields['comment'] = '';
                }

                $message = new Report([
                    'slug' => $fields['slug'],
                    'email' => $fields['email'],
                    'comment' => $fields['content'],
                    'created' => $created,
                    'updated' => $created,
                ]);
                $manager->save($message);

                $statusCode = 204;
                $error = false;
            } catch (\Exception $e) {
                $statusCode = 500;
                $error = $e->getMessage();
            }
            $response = $this->getResponse();
            $response->setHeader('Content-Type', 'application/json');
            $response->setStatusCode($statusCode);
            if ($error) {
                $response->setContent(['error' => $error]);
            }
            return $response;
        }

        /**
         * Set our error message.
         *
         * @param string $message
         * @return $this
         */
        public function setMessage($message = '')
        {
            $this->message = $message;
            return $this;
        }

        /**
         * Get our error message.
         *
         * @return string
         */
        public function getMessage()
        {
            return $this->message;
        }

        /**
         * Set our exception.
         *
         * @param \Exception $exception
         * @return $this
         */
        public function setException(\Exception $exception)
        {
            $this->exception = $exception;
            return $this;
        }

        /**
         * Get our exception.
         *
         * @return \Exception
         */
        public function getException()
        {
            return $this->exception;
        }

        /**
         * Set our status code.
         *
         * @param int $statusCode
         * @return $this
         */
        public function setStatusCode($statusCode = 500)
        {
            $this->statusCode = $statusCode;
            return $this;
        }

        /**
         * Get our status code.
         *
         * @return int
         */
        public function getStatusCode()
        {
            return $this->statusCode;
        }

        /**
         * Report a HTTP exception.
         *
         * @param HttpException $exception
         * @return $this
         */
        public function httpException(HttpException $exception)
        {
            $this->setMessage($exception->getMessage());
            $this->setStatusCode($exception->getStatusCode());
            $this->setException($exception);
            return $this;
        }

        /**
         * GET /error
         */
        public function index_get()
        {
            $this->getResponse()->setStatusCode($this->getStatusCode());
            $layout = $this->getLayout();
            $layout->block('layout')->setTemplate('core/layout-error.phtml');
            $layout->block('content')->setVariable('title', 'Bummer!');
            if (!is_file($this->getApp()->getRootDirectory() . DIRECTORY_SEPARATOR . 'hideExceptions')) {
                $layout->block('error/exception')->setVariable('exception', $this->getException());
            }
            $layout->block('error/message')->setVariable('message', $this->getMessage());
            $this->getResponse()->addContent($this->getLayout());
        }
    }

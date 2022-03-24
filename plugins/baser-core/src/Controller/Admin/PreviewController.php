<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Controller\Admin;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Http\ServerRequest;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Http\ServerRequestFactory;
use BaserCore\Utility\BcContainerTrait;
use Cake\Http\Exception\ForbiddenException;

class PreviewController extends BcAdminAppController
{

    use BcContainerTrait;

    /**
     * initialize
     *
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcFrontContents');
    }

    /**
     * view
     *
     * @param  mixed $path
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function view(...$path)
    {
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $path = str_replace(BcUtil::getPrefix() . "/" . Inflector::dasherize($this->request->getParam('plugin')) . "/preview/view", "", $this->request->getPath());
        $request = $this->createRequest($path);
        $serviceName = $request->getParam('plugin') . '\\Service\\' . $request->getParam('controller') . Inflector::camelize($request->getParam('action')) . 'ServiceInterface';
        $service = $this->getService($serviceName);
        try {
            $previewData = $service->getPreviewData($this->getRequest());
            $this->setRequest($request);
            $this->set(strtolower(Inflector::singularize($request->getParam('controller'))), $previewData);
            $this->viewBuilder()->setLayout('default');
            $this->viewBuilder()->setTemplate($request->getParam('action'));
            $this->viewBuilder()->setTemplatePath($request->getParam('controller'));
            return $this->render('/' . $request->getParam('controller') .'/default');
        } catch (\Cake\Http\Exception\NotFoundException $e) {
            $this->BcMessage->setError(__d('baser', $e->getMessage()));
            return $this->redirect($this->referer());
        }
    }

    /**
     * createRequest
     *
     * @param  string $url
     * @return ServerRequest
     * @checked
     * @unitTest
     * @note(value="webrootの解析ロジックを反映させる")
     */
    protected function createRequest($url): ServerRequest
    {
        $config = [];
        $method = 'GET';
        if(preg_match('/^http/', $url)) {
            $parseUrl = parse_url($url);
            Configure::write('BcEnv.host', $parseUrl['host']);
            $defaultConfig = [
                'uri' => ServerRequestFactory::createUri([
                    'HTTP_HOST' => $parseUrl['host'],
                    'REQUEST_URI' => $url,
                    'REQUEST_METHOD' => $method,
                    'HTTPS' => (preg_match('/^https/', $url))? 'on' : ''
            ])];
        } else {
            $defaultConfig = [
                'url' => $url,
                'environment' => [
                    'REQUEST_METHOD' => $method
            ]];
        }
        $defaultConfig = array_merge($defaultConfig, $config);
        $request = new ServerRequest($defaultConfig);
        // TODO ucmitz: webrootの解析ロジックを反映させる
        $request = $request->withAttribute('webroot', '/');
        try {
            Router::setRequest($request);
            $params = Router::parseRequest($request);
        } catch (\Exception $e) {
            return $request;
        }
        $request = $request->withAttribute('params', $params);
        return $request;
    }

}

<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasZendLib\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;

/**
 * KtogiasZendLib controller. Currently just extends AbstractActionController.
 *
 * @author ktogias
 */
class Controller extends AbstractActionController{
    
    private $thisClass;
    
    public function onDispatch(MvcEvent $e) {
        $this->thisClass = new \ReflectionClass($this);
        if($this->thisClass->implementsInterface('KtogiasZendLib\Mvc\Controller\JsonControllerInterface')){
            $e->getApplication()->getEventManager()->attach('render', array($this, 'renderJson'), 100);
        }
        else if ($this->thisClass->implementsInterface('KtogiasZendLib\Mvc\Controller\PartialHtmlControllerInterface')){
            $e->getApplication()->getEventManager()->attach('render', array($this, 'renderPartialHtml'), 100);
        }
        else if($this->thisClass->implementsInterface('KtogiasZendLib\Mvc\Controller\AngularJsControllerInterface')){
            $e->getApplication()->getEventManager()->attach('render', array($this, 'renderAngularJs'), 100);
            $e->getApplication()->getEventManager()->attach('finish', array($this, 'finishAngularJs'), 100);
        }
        else if ($this->thisClass->implementsInterface('KtogiasZendLib\Mvc\Controller\JavascriptControllerInterface')){
            $e->getApplication()->getEventManager()->attach('render', array($this, 'renderJavascript'), 100);
            $e->getApplication()->getEventManager()->attach('finish', array($this, 'finishJavascript'), 100);
        }
        else if ($this->thisClass->implementsInterface('KtogiasZendLib\Mvc\Controller\FileControllerInterface')){
            $e->getApplication()->getEventManager()->attach('render', array($this, 'renderFile'), 100);
        }
        else if ($this->thisClass->implementsInterface('KtogiasZendLib\Mvc\Controller\CsvControllerInterface')){
            $e->getApplication()->getEventManager()->attach('render', array($this, 'renderCsv'), 100);
        }
        else if ($this->thisClass->implementsInterface('KtogiasZendLib\Mvc\Controller\PdfControllerInterface')){
            $e->getApplication()->getEventManager()->attach('render', array($this, 'renderPdf'), 100);
            $e->getApplication()->getEventManager()->attach('finish', array($this, 'finishPdf'), 100);
        }
        
        /** @todo: This needs to be rechecked because it fails to handle with 404 errors*/
        $e->getApplication()->getEventManager()->attach('dispatch.error', array($this, 'formatError'), 100);
        $e->getApplication()->getEventManager()->attach('render.error', array($this, 'formatError'), 100);
        
        parent::onDispatch($e);
    }
    
    /**
     * @param  \Zend\Mvc\MvcEvent $e The MvcEvent instance
     * @return void
     */
    public function renderJson(MvcEvent $e)
    {
        $viewModel = $e->getViewModel();
        $viewModelClass = new \ReflectionClass($viewModel);
        if ($viewModelClass->name != 'Zend\View\Model\JsonModel' 
                && !$viewModelClass->isSubclassOf('Zend\View\Model\JsonModel')){
            $vars = $this->getChildViewsVars($e);
            if (isset($vars['exception'])){
                $exception = $vars['exception'];
                $vars['exception'] = [];
                $vars['exception']['class'] = get_class($exception);
                $vars['exception']['message'] = $exception->getMessage();
                $vars['exception']['trace'] = $exception->getTrace();
            }
            $e->setViewModel(new JsonModel($this->getValueArrayCopy($vars)));
        }
        $this->attachJsonStrategy($e);
    }
    
    public function attachJsonStrategy(MvcEvent $e){
        $app          = $e->getTarget();
        $locator      = $app->getServiceManager();
        $view         = $locator->get('Zend\View\View');
        $jsonStrategy = $locator->get('ViewJsonStrategy');

        // Attach strategy, which is a listener aggregate, at high priority
        $view->getEventManager()->attach($jsonStrategy, 100);
    }
    
    public function renderPartialHtml(MvcEvent $e){
        $e->getViewModel()->setTemplate('layout/partial');
    }
    
    /**
     * @param MvcEvent $e
     */
    public function renderAngularJs(MvcEvent $e){
        $templatePathResolver = $this->serviceLocator->get('Zend\View\Resolver\TemplatePathStack');
        /*@var $templatePathResolver \Zend\View\Resolver\TemplatePathStack*/
        $templatePathResolver->setDefaultSuffix('js');
        $this->setAngularJsJavascriptTemplatePath();
        $e->getViewModel()->setTemplate('layout/javascript');
        $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/javascript');
    }
    
    public function finishAngularJs(MvcEvent $e){
        if(!$e->isError()){
            $content = $this->getResponse()->getContent();
            $vars = $this->getChildViewsVars($e);
            $this->importAngularJsTemplates($vars);
            $action = $e->getRouteMatch()->getParam('action');
            $content = "if (typeof serverVars === 'undefined'){"
                        . "var serverVars = {};"
                    . "}"
                    . "serverVars[\"".strtr($this->thisClass->getName(), ["\\" => "\\\\"])
                    ."\\\\".$action."\"] = ".json_encode($vars).";\n"
                    .$content;
            $this->getResponse()->setContent($content);
        }
    }
    
    /**
     * @param MvcEvent $e
     */
    public function renderJavascript(MvcEvent $e){
        $templatePathResolver = $this->serviceLocator->get('Zend\View\Resolver\TemplatePathStack');
        /*@var $templatePathResolver \Zend\View\Resolver\TemplatePathStack*/
        $templatePathResolver->setDefaultSuffix('js');
        $e->getViewModel()->setTemplate('layout/javascript');
        $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'application/javascript');
    }
    
    /**
     * Injects the view vars into javascript
     * @param MvcEvent $e
     */
    public function finishJavascript(MvcEvent $e){
        if(!$e->isError()){
            $content = $this->getResponse()->getContent();
            $vars = $this->getChildViewsVars($e);
            $content = "var serverVars = ".json_encode($vars).";\n".$content;
            $this->getResponse()->setContent($content);
        }
    }
    
    /**
     * Send a file to browser
     * View must provide the following vars:
     *  - path
     *  - disposition (optional)
     *  - filename (optional)
     * @param MvcEvent $e
     * @throws Exception\PathIsEmptyException
     * @throws Exception\FileNotFoundException
     */
    public function renderFile(MvcEvent $e){
        if($this->getResponse()->getStatusCode() == 200){
            $vars = $this->getChildViewsVars($e);
            if (empty($vars['path'])){
                throw new Exception\PathIsEmptyException();
            }
            $path = $vars['path'];
            if (!is_file($path)){
                throw new Exception\FileNotFoundException();
            }
            $contentType = $this->getContentType($path);
            $contentDisposition = 'inline';
            if (!empty($vars['disposition'])){
                $contentDisposition = $vars['disposition'];
            }
            $info = new \SplFileInfo($path);
            $filename = $info->getFilename();
            if (!empty($vars['filename'])){
                $filename = $vars['filename'];
            }

            $response = new \Zend\Http\Response\Stream();
            $response->getHeaders()->clearHeaders()
                    ->addHeaderLine('Content-Type', $contentType)
                    ->addHeaderLine('Content-Disposition', $contentDisposition.'; filename="'.$filename.'"')
                    ->addHeaderLine('Content-Length', $info->getSize())
                    ->addHeaderLine('Cache-control', 'private');
            $response->setStream(fopen($path, 'r'))
                    ->setStatusCode(200)
                    ->setStreamName($info->getFilename());

            $e->setResponse($response)->stopPropagation();
        }
    }
    
    /**
     * Send a file to browser
     * View must provide the following vars:
     *  - path
     *  - data
     *  - disposition (optional)
     *  - filename (optional)
     * 
     * @param MvcEvent $e
     * @throws Exception\PathIsEmptyException
     * @throws Exception\FileNotFoundException
     */
    public function renderCsv(MvcEvent $e){
        $vars = $this->getChildViewsVars($e);
        if (empty($vars['path'])){
            throw new Exception\PathIsEmptyException();
        }
        $path = $vars['path'];
        $filename = $vars['filename']?$vars['filename']:'csv';
        $date = new \DateTime();
        $file = $path.'/'.$filename.'_'.$this->auth->getUser()->getId().'_'.$date->format('YmdHis').'.csv';
        $i = 0;
        while (is_file($file)){
            $i++;
            $rand = rand(100000,999999);
            $file = $path.'/'.$filename.'_'.$this->auth->getUser()->getId().'_'.$date->format('YmdHis').'_'.$rand.'.csv';
            if ($i>100000){
                throw new Exception\CsvFileCreationFailedException('Max iterations to find available filename reached');
            }
        }
        $data = $vars['data'];
        $fp = fopen($file, 'w');
        foreach($data as $row){
            fputcsv($fp, $row);
        }
        fclose($fp);
        $e->getViewModel()->getChildren()[0]->setVariables([
           'path' => $file,
           'disposition' => 'attachment',
           'filename' => $filename.'.csv',
        ]);
        $this->renderFile($e);
    }
    
    public function formatError(MvcEvent $e){
        if ($this->thisClass->implementsInterface('KtogiasZendLib\Mvc\Controller\JsonOnErrorControllerInterface')){
            $e->setViewModel(new JsonModel());
            $this->attachJsonStrategy($e);
        }
        else {
            $viewModel = $e->getViewModel();
            $viewModel->setTemplate('error/layout');
            $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', 'text/html');
        }
    }
    
    /**
     * 
     * @param MvcEvent $e
     * @return array
     */
    private function getChildViewsVars(MvcEvent $e){
        $vars = [];
        $viewModel = $e->getViewModel();
        foreach ($viewModel->getChildren() as $childModel){
            /*@var $childModel \Zend\View\Model\ViewModel*/
            if (is_array($childModel->getVariables())){
                $vars = array_merge($vars, $childModel->getVariables());
            }
        }
        return $vars;
    }
    
    private function getContentType($path){
        $contentType = exec('file -bi '.$path);
        if (empty($contentType)){
            $contentType = 'application/octet-stream; charset=binary';
        }
        return $contentType;
    }
    
    private function setAngularJsJavascriptTemplatePath(){
        $layoutChildren = $this->layout()->getChildren();
        foreach($layoutChildren as $layout){
            $template = $layout->getTemplate();
            if (strpos( $template, 'error/') === false){
                $templateParts = explode('/', $template);
                $script = array_pop($templateParts);
                array_push($templateParts, 'js');
                $layout->setTemplate(implode('/', $templateParts).'/'.$script);
            }
        }
    }
    
    private function importAngularJsTemplates(array &$vars){
        if (array_key_exists('templates', $vars)){
            $templatePathResolver = $this->serviceLocator->get('Zend\View\Resolver\TemplatePathStack');
            /*@var $templatePathResolver \Zend\View\Resolver\TemplatePathStack*/
            $templatesRoot = '';
            $nameSpace = $this->thisClass->getNamespaceName();
            $moduleName = str_replace('\Controller', '', $nameSpace);
            foreach ($templatePathResolver->getPaths() as $path){
                $path = str_replace('\\', '/', $path);
                if (
                        (strpos($path, '/'.$moduleName.'/') !== FALSE 
                        || strpos($path, '/'.lcfirst($moduleName).'/') !== FALSE 
                        || strpos($path, '/'. strtolower($moduleName).'/') !== FALSE) 
                        &&  strpos($path, 'angularjs/') === strlen($path) - strlen('angularjs/')){
                    $templatesRoot = $path;
                }
            } 
            $layoutChildren = $this->layout()->getChildren();
            foreach ($vars['templates'] as $name => &$value){
                foreach ($layoutChildren as $layout){
                    $template = $layout->getTemplate();
                    $templateParts = explode('/', $template);
                    array_pop($templateParts);
                    $templatesPath = $templatesRoot.implode('/', $templateParts).'/../template';
                    $templateFile = $templatesPath.'/'.$value;
                    $value = file_get_contents($templateFile);
                    if ($value === FALSE){
                        throw new Exception\ImportAngularJsTemplateException('Failed to import '.$name.' template: file_get_contents failed on '.$templateFile);
                    }
                }
            }
        }
    }
    
    protected function getValueArrayCopy($value){
        if (is_a($value, 'Traversable', FALSE) || is_array($value)){
            $array = [];
            foreach($value as $key => $item){
                $array[$key] = $this->getValueArrayCopy($item);
            }
            return $array;
        }
        else if (is_object($value) && method_exists($value, 'getSafeArrayCopy')){
            return $value->getSafeArrayCopy();
        }
        else if (is_object($value) && method_exists($value, 'getArrayCopy')){
            return $value->getArrayCopy();
        }
        else {
            return $value;
        }
    } 
    
    /**
     * Render pdf
     * View can provide the following vars:
     *  - render (If present and not empty I will try to render html into pdf)
     *  - mergefiles (If not empty array with paths I will merge these pdf files to one pdf)
     *  - filename
     *  - force_odd_first_pages (If present and not empty empty pages are added so that every merged file's first page is put at odd page)
     * 
     * @param MvcEvent $e
     */
    public function renderPdf(MvcEvent $e){
        if($this->getResponse()->getStatusCode() == 200){
            $e->getViewModel()->setTemplate('layout/pdf');
            $vars = $this->getChildViewsVars($e);
            if (empty($vars['render'])){
                if (!empty($vars['mergefiles'])){
                    $this->mergeFiles($vars['mergefiles'], !empty($vars['force_odd_first_pages']), $vars['footers']);
                }
                if (!empty($vars['headers'])){
                    $this->addHeaders($vars['headers']);
                }
                $this->pdf->Output(empty($vars['filename'])?'document.pdf':$vars['filename'], 'D');
            }
        }
    }
    
    /**
     * Finish pdf
     * View can provide the following vars:
     *  - render (If present and not empty I will try to render html into pdf)
     *  - render_append (If present and not empty I will put rendered html at the end of the pdf)
     *  - html_debug (If present and not empty I will only render html to browser - no pdf)
     *  - mergefiles (If not empty array with paths I will merge these pdf files to one pdf)
     *  - filename
     *  - force_odd_first_pages (If present and not empty empty pages are added so that every merged file's first page is put at odd page)
     * 
     * @param MvcEvent $e
     */
    public function finishPdf(MvcEvent $e){
        if(!$e->isError()){
            $vars = $this->getChildViewsVars($e);
            if (!empty($vars['render'])){
                if (empty($vars['html_debug'])){
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($e->getViewModel());
                    $pdf = $this->pdf;
                    /*@var $pdf \FPDI*/
                    $prepend = empty($vars['render_append']);
                    if ($prepend){
                        $pdf->AddPage('P','A4');
                        $pdf->writeHTML($html);
                        if (!empty($vars['force_odd_first_pages']) && !empty($vars['mergefiles']) && ($pdf->getPage())%2 == 1){
                            $pdf->AddPage();
                        }
                    }
                    if (!empty($vars['mergefiles'])){
                        $this->mergeFiles($vars['mergefiles'], !empty($vars['force_odd_first_pages']), $vars['footers']);
                    }
                    if (!$prepend){
                        $pdf->AddPage('P','A4');
                        $pdf->writeHTML($html);
                    }
                    if (!empty($vars['headers'])){
                        $this->addHeaders($vars['headers']);
                    }
                    $pdf->Output(empty($vars['filename'])?'document.pdf':$vars['filename'], 'D');
                }
            }
        }
    }
    
}

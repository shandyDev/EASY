<?php

namespace AppUser\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Authentication\AuthenticationService,
    Zend\Authentication\Adapter\DbTable as AuthAdapter;

class Auth extends AbstractPlugin
{
    protected $app;
/**
* Return application authentification service
* @return \Zend\Authentication\AuthenticationService
*/
    public function getAuthentificationService()
    {
        return $this->_auth;
    }

    public function setAuthentificationService()
    {
        return $this->_auth;
    }

    public function __construct($authenticationService = null)
    {
        if(empty($authenticationService))
            $this->_auth = new AuthenticationService();
        else {
		if ($authenticationService instanceof AuthenticationService){
			$this->_auth = $authenticationService;
		} else {
			throw new \Exception('sopa!');
		}
	}

    }

    public function routeShutdown(\Zend\Mvc\MvcEvent $event)
    {
        if($this->getAuthentificationService()->hasIdentity()) {
            //$roleName = $this->getAuthentificationService()->getIdentity()->getRole()->getName();
            //if (!empty($roleName)) {
            //    $this->getAcl()->setCurrentRole(new \Zend\Acl\Role\GenericRole($roleName));
            //}

		return;
        }
        
        // \Zend\View\Helper\Navigation\AbstractHelper::setDefaultRole($this->getAcl()->getCurrentRole());
        
        $locator = $event->getTarget()->getServiceManager();
        //if($locator->instanceManager()->hasAlias('sysmap-service')) {
            //$currentResource = $locator->get('sysmap-service')->getIdentifierByRequest($event->getRouteMatch());

            $allow = false;
            //if($currentResource instanceof \Zend\Acl\Resource\GenericResource) {
            //    $allow = $this->getAcl()->isAllowed($this->getAcl()->getCurrentRole(), $currentResource);
            //}

            if(!$allow) {

                $controller = $event->getRouteMatch()->getParam('controller');
                $action = $event->getRouteMatch()->getParam('action');
                $in = $event->getTarget()->getServiceManager()->get('Di')->instanceManager();
                $foundController = $in->hasAlias($controller);
                $foundAction = false;

                if($foundController) {
                    //die('ddd');
                    $controllerInstance = $event->getTarget()->getServiceManager()->get($controller);
                    $method = \Zend\Mvc\Controller\ActionController::getMethodFromAction($action);
                    if (method_exists($controllerInstance, $method)) {
                        $foundAction = true;
                    }
                }

                if($foundAction) {
                    $event->getRouteMatch()->setParam('controller','user');
                    $event->getRouteMatch()->setParam('action','login');
                }
            }
        //}
    }

    public function setApplication(\Zend\Mvc\ApplicationInterface $app)
    {
        $this->app = $app;
    }
    
    protected function getApplication()
    {
        return $this->app;
    }
}
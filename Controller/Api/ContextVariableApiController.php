<?php

namespace TechPromux\DynamicContextConfigurationBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use TechPromux\DynamicContextConfigurationBundle\Manager\ContextVariableManager;

/**
 * Description of ConfiguracionApiController
 *
 * @FOSRest\Route("/context-variable")
 * 
 */
class ContextVariableApiController extends FOSRestController {

    /**
     * Get value by code from variables vars
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get variable value",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the resource is not found"
     *   }
     * )
     *
     * @FOSRest\Get("/{name}",options={"expose"=true})
     *
     * @Security("has_role('ROLE_API')")
     *
     * @param Request $request the request object
     * @param mixed   $name     the variable name
     *
     * @return mixed
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function getAction(Request $request, $name) {
        $value = $this->getContextVariableManager()->getVariableValueByName($name);
        $view = $this->view($value);
        return $this->handleView($view);
    }

    /**
     * @return ContextVariableManager
     */
    protected function getContextVariableManager()
    {
        return $this->get('techpromux_dynamic_context_configuration.manager.context_variable');
    }

}

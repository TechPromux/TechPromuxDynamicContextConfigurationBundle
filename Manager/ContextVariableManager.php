<?php

namespace TechPromux\DynamicContextConfigurationBundle\Manager;

use TechPromux\BaseBundle\Manager\Resource\BaseResourceManager;
use TechPromux\DynamicConfigurationBundle\Manager\DynamicVariableManager;
use TechPromux\DynamicContextConfigurationBundle\Entity\ContextVariable;

/**
 * VariableManager
 *
 */
class ContextVariableManager extends BaseResourceManager
{

    public function getBundleName()
    {
        return 'TechPromuxDynamicContextConfigurationBundle';
    }

    public function getMediaContextId()
    {
        return 'techpromux_dynamic_configuration_media_image';
    }

    /**
     * Get entity class name
     *
     * @return class
     */
    public function getResourceClass()
    {
        return ContextVariable::class;
    }

    /**
     * Get entity short name
     *
     * @return string
     */
    public function getResourceName()
    {
        return 'ContextVariable';
    }

    //------------------------------------------

    /**
     * @var DynamicVariableManager
     */
    protected $dynamic_configuration_manager;

    /**
     * @param DynamicVariableManager $dynamic_configuration_manager
     * @return $this
     */
    public function setDynamicVariableManager(DynamicVariableManager $dynamic_configuration_manager)
    {
        $this->dynamic_configuration_manager = $dynamic_configuration_manager;
        return $this;
    }

    /**
     * @return DynamicVariableManager
     */
    public function getDynamicVariableManager()
    {
        return $this->dynamic_configuration_manager;
    }

    //-----------------------------------------------------------------------


    /**
     * @var UtilDynamicConfigurationManager
     */
    protected $util_dynamic_configuration_manager;

    /**
     * @return mixed
     */
    public function getUtilDynamicConfigurationManager()
    {
        return $this->util_dynamic_configuration_manager;
    }

    /**
     * @param mixed $util_dynamic_configuration_manager
     * @return DynamicVariableManager
     */
    public function setUtilDynamicConfigurationManager($util_dynamic_configuration_manager)
    {
        $this->util_dynamic_configuration_manager = $util_dynamic_configuration_manager;
        return $this;
    }


    //--------------------------------------------------------------------------------

    public function synchronizeContextVariablesFromAuthenticatedUser()
    {

        $variables = $this->getDynamicVariableManager()->findBy(array('contextType' => 'SHARED'));

        foreach ($variables as $cfg) {

            $queryBuilder = $this->createQueryBuilder();

            $queryBuilder
                ->andWhere($queryBuilder->getRootAliases()[0] . '.variable = :variable')
                ->setParameter('variable', $cfg->getId());

            $cov = $this->getOneOrNullResultFromQueryBuilder($queryBuilder);

            if (is_null($cov)) {
                $cov = $this->createNewInstance();
                $this->getResourceContextManager()->addContextRelationToObject($cov, $this->getContextPropertyValueNamePrefix());
                $cov->setName($cov->getContext()->getName() . '_' . $cfg->getName());
                $cov->setTitle($cfg->getTitle());
                $cov->setVariable($cfg);
                $cov->setValue($cfg->getValue());
                //$cov->setMedia($cfg->getMedia());
                $this->persist($cov);
            }
        }

        return true;
    }

    //----------------------------------------------------------------------------------------

    /**
     *
     * @param string $name
     * @return ContextVariable
     */
    protected function findVariableByName($name)
    {
        if (is_null($name)) {
            throw new \Exception('Null name don´t be accepted');
        }

        $variable = $this->findOneBy(array('name' => $name));
        /* @var $variable DynamicVariable */

        if ($variable->getType() == "SYSTEM") {
            $this->throwException('Variable {' . $name . '} isn´t for shared context');
        }

        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder
            ->andWhere($queryBuilder->getRootAliases()[0] . '.variable = :variable')
            ->setParameter('variable', $variable->getId());

        $context_variable = $this->getOneOrNullResultFromQueryBuilder($queryBuilder);

        if (is_null($context_variable)) {
            return $variable;
        }

        return $context_variable;

    }

    /**
     *
     * @param string $name
     * @return any
     */
    public function getVariableValueByName($name)
    {
        $variable = $this->findVariableByName($name);

        return json_decode($variable->getValue(), true);
    }


}

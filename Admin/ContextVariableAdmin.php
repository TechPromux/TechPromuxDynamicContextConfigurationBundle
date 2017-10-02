<?php

namespace TechPromux\DynamicContextConfigurationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use TechPromux\DynamicConfigurationBundle\Entity\ContextVariable;
use TechPromux\DynamicConfigurationBundle\Type\Variable\BaseVariableType;
use TechPromux\BaseBundle\Admin\Resource\BaseResourceAdmin;
use TechPromux\DynamicContextConfigurationBundle\Manager\ContextVariableManager;

class ContextVariableAdmin extends BaseResourceAdmin
{
    /**
     *
     * @return ContextVariableManager
     */
    public function getResourceManager()
    {
        return parent::getResourceManager();
    }

    /**
     *
     * @return ContextVariable
     */
    public function getSubject()
    {
        return parent::getSubject();
    }

    public function configureRoutes(\Sonata\AdminBundle\Route\RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('show');
        $collection->remove('create');
        $collection->remove('delete');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        parent::configureDatagridFilters($datagridMapper);
        $datagridMapper
            ->add('variable.name', null, array())
            ->add('variable.title', null, array())
            ->add('variable.type', null, array(), 'choice', array(
                'choices' => $this->getResourceManager()->getUtilDynamicConfigurationManager()->getVariableTypesChoices(),
                'translation_domain' => $this->getResourceManager()->getUtilDynamicConfigurationManager()->getBundleName()
            ))
            //->add('variable.description', null, array())
            ->add('value');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $this->getResourceManager()->synchronizeContextVariablesFromAuthenticatedUser();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $listMapper->add('variable.name', null, array());
            $listMapper->add('variable.title', null, array());
        } else {
            $listMapper->add('variable.title', null, array());
        }
        $listMapper->add('variable.type', 'choice', array(
            'choices' => $this->getResourceManager()->getUtilDynamicConfigurationManager()->getVariableTypesChoices(true),
        ));

        $listMapper->add('printableValue', 'html', array(
            //'label' => 'Value',
            'width' => '65',
            'height' => '65',
            'class' => 'img-polaroid'
        ));

        parent::configureListFields($listMapper);

        $listMapper->add('_action', 'actions', array(
            //'label' => ('Actions'),
            'row_align' => 'right',
            'header_style' => 'width: 90px',
            'actions' => array(
                'show' => array(),
                'edit' => array(),
                'delete' => array(),
            )
        ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        parent::configureFormFields($formMapper);

        $object = $this->getSubject();

        $object = $this->getSubject();

        $this->getResourceManager()->getUtilDynamicConfigurationManager()->transformValueToCustom($object);

        $formMapper
            ->with('form.group.definition.label', array('class' => 'col-md-4'));

        $formMapper->add('variable.title', null, array(
            //'mapped' => false,
            'disabled' => true,
        ));
        $formMapper->add('variable.description', null, array(
            //'mapped' => false,
            'disabled' => true,
        ));
        $formMapper
            ->add('reset_value', 'sonata_type_choice_field_mask', array(
                //'label' => 'Custom Value or Reset Value to Default',
                //'empty_data' => '0',
                'mapped' => false,
                'choices' => array(
                    'No' => '0',
                    'Yes' => '1',
                ),
                'map' => array(
                    '0' => array('customValue'),
                    '1' => array(),
                ),
                //'empty_value' => 'Choose an option',
                'required' => true,
            ))
            ->end();

        $formMapper->with('form.group.value.label', array('class' => 'col-md-8'));

        $field_options_type = $this->getResourceManager()->getUtilDynamicConfigurationManager()->getVariableTypeById($object->getVariable()->getType());
        /** @var $field_options_type BaseVariableType */

        if (!$field_options_type->getHasSettings()) {
            $formMapper->add('customValue', $field_options_type->getValueType(),
                array_merge($field_options_type->getValueOptions(), array(
                    'required' => false,
                ))
            );
        } else {
            if ($object->getVariable()->getSettings()) {
                $value_choices = $this->getResourceManager()->getUtilDynamicConfigurationManager()->getSettingsOptionsChoices($object->getVariable());
                $formMapper
                    ->add('customValue', $field_options_type->getValueType(),
                        array_merge($field_options_type->getValueOptions(), array(
                            'choices' => $value_choices,
                            'required' => false,
                        )));
            }
        }
        $formMapper->end();
    }

    public function preUpdate($object)
    {
        $request = $this->getRequest();
        $formData = $request->get($request->get('uniqid'));

        if ('1' == $formData['reset_value']) {
            $object->setCustomValue($object->getVariable()->getCustomValue());
            $object->setValue($object->getVariable()->getValue());
            $object->setMedia($object->getVariable()->getMedia());
        }

        $this->getResourceManager()->getUtilDynamicConfigurationManager()->transformCustomToValue($object);

        parent::preUpdate($object);


    }

    public function toString($object)
    {
        return $object->getVariable()->getName();
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'base_list_field':
                return 'TechPromuxDynamicConfigurationBundle:Admin:CRUD/base_list_field.html.twig';
        }
        return parent::getTemplate($name);
    }

}

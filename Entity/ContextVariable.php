<?php

namespace TechPromux\DynamicContextConfigurationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TechPromux\BaseBundle\Entity\Resource\BaseResource;
use TechPromux\BaseBundle\Entity\Context\BaseResourceContext;
use TechPromux\BaseBundle\Entity\Context\HasResourceContext;
use TechPromux\DynamicConfigurationBundle\Entity\DynamicVariable;

/**
 * ContextVariable
 *
 * @ORM\Table(name="techpromux_dynamic_configuration_context_variable")
 * @ORM\Entity()
 */
class ContextVariable extends BaseResource implements HasResourceContext
{

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @var \Sonata\MediaBundle\Entity\BaseMedia
     *
     * @ORM\ManyToOne(targetEntity="Sonata\MediaBundle\Entity\BaseMedia",cascade={"all"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true)
     */
    protected $media;

    /**
     * @ORM\ManyToOne(targetEntity="TechPromux\DynamicConfigurationBundle\Entity\DynamicVariable")
     * @ORM\JoinColumn(name="variable_id", referencedColumnName="id", nullable=false)
     */
    private $variable;

    /**
     * @var BaseResourceContext
     *
     * @ORM\ManyToOne(targetEntity="TechPromux\BaseBundle\Entity\Context\BaseResourceContext")
     * @ORM\JoinColumn(name="context_id", referencedColumnName="id", nullable=true)
     */
    protected $context;

    //----------------------------------------------------------------------------------------------------------

    /**
     * Set variable
     *
     * @param DynamicVariable $variable
     *
     * @return ContextVariable
     */
    public function setVariable(DynamicVariable $variable)
    {
        $this->variable = $variable;

        return $this;
    }

    /**
     * Get variable
     *
     * @return DynamicVariable
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @return BaseResourceContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param BaseResourceContext $context
     * @return ContextVariable
     */
    public function setContext(BaseResourceContext $context = null)
    {
        $this->context = $context;
        return $this;
    }

    //----------------------------------------------------------------------------------------------

    /**
     * Get code
     *
     * @return string
     */
    public function getName()
    {
        return $this->getVariable() ? $this->getVariable()->getName() : null;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getVariable() ? $this->getVariable()->getDescription() : null;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getVariable() ? $this->getVariable()->getType() : null;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return ContextVariable
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set media
     *
     * @param \Sonata\MediaBundle\Entity\BaseMedia $media
     *
     * @return DynamicVariable
     */
    public function setMedia(\Sonata\MediaBundle\Entity\BaseMedia $media = null)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * Get media
     *
     * @return \Sonata\MediaBundle\Entity\BaseMedia
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @return any
     */
    public function getCustomValue()
    {
        return $this->customValue;
    }

    /**
     * @param any $customValue
     * @return DynamicVariable
     */
    public function setCustomValue($customValue)
    {
        $this->customValue = $customValue;
        return $this;
    }

    //public function getHashCode() {
    //    return parent::getHashCode() .'-'. ($this->getVariable()->getType() == 'image' && $this->media ? $this->media->getName() : $this->value);
    //}

    public function __toString()
    {
        return $this->getTitle() ? $this->getTitle() : '';
    }

    /**
     * Get value
     *
     * @return string
     */

    public function getPrintableValue()
    {
        if ($this->getType() == "image") {
            return $this->media ? $this->media : $this->getVariable()->getMedia();
        }
        if ($this->getType() == "boolean") {
            return $this->value;
        }

        $printable_value = json_decode($this->value, true);

        if (is_array($printable_value))
            return $this->value;
        return $printable_value;
    }

}

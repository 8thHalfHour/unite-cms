<?php
/**
 * Created by PhpStorm.
 * User: stefankamsker
 * Date: 02.11.18
 * Time: 14:36
 */

namespace UniteCMS\CoreBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniteCMSCoreFieldTypeExtension extends AbstractTypeExtension
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // set default value for field
        if (isset($options['initial_data']) && $options['initial_data']) {

            $content = $options['content'];
            $default = $options['initial_data'];

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($default, $content) {

                $data = $event->getData();

                // if new object and data is empty
                if (is_null($content->getId()) && empty($data)) {
                    $event->setData($default);
                }

            });

        }

        // add required validation dynamically
        if (isset($options['required'])
            && $options['required']
            && isset($options['is_field'])
        ) {

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

                $data = $event->getData();
                $form = $event->getForm();

                if (empty($data)) {
                    $form->addError(new FormError('', 'not_blank'));
                }

            });

        }

    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // pass description to template
        if (isset($options['description'])) {
            $view->vars['description'] = $options['description'];
        }

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('description');
        $resolver->setDefined('initial_data');
        $resolver->setDefined('content');
        $resolver->setDefined('is_field');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return FormType::class;
    }

}
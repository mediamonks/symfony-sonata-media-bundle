<?php

namespace MediaMonks\SonataMediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaFocalPointType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'media' => null,
                'template' => 'MediaMonksMediaBundle:Form/Type:media_focal_point.html.twig',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['template'] = $options['template'];
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['media'] = $options['media'];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media_focal_point';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }
}

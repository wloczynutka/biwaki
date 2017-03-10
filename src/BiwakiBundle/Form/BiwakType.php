<?php

namespace BiwakiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BiwakType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('street')
            ->add('city')
            ->add('type')
            ->add('latitude')
            ->add('longitude')
            ->add('altitude')
            ->add('submit', SubmitType::class, array(
                'label' => 'Create',
                'attr'  => array('class' => 'btn btn-default pull-right')
            ));
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BiwakiBundle\Entity\Biwak'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'biwakibundle_biwak';
    }


}

<?php

namespace RobotOyun\OyunBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array(
                'attr' => array('class' => 'form-control', 'placeHolder' => 'Oyun İsmi')
            ) )
            ->add('description', 'textarea', array(
                'attr' => array('class' => 'form-control', 'rows' => 5)
            ))
            ->add('content', 'textarea', array(
                'attr' => array('class' => 'form-control', 'rows' => 5)
            ))
            ->add('content', 'textarea', array(
                'attr' => array('class' => 'form-control', 'rows' => 5)
            ))
            ->add('image','iphp_file',
                array(
                    'required' => 'required',
                    'label' => 'Gönderi Resmi',
                    'attr' => array('class' => 'form-control')))
            ->add('category', 'entity', array(
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'class' => 'RobotOyunOyunBundle:Category',
                'property' => 'name',
                'query_builder' => function(EntityRepository $repo) {
                    return $repo->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },

            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RobotOyun\OyunBundle\Entity\Game'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'robotoyun_oyunbundle_game';
    }
}

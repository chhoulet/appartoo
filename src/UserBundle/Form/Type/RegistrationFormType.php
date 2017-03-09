<?php

namespace UserBundle\Form\Type;

use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', TextType::class,      array('label'=>'Name',
													      'attr'=>array('class'=>'form-control')))
				->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'),
											        array('label'=>'Name',
													      'attr'=>array('class'=>'form-control')))
				->add('username', TextType::class,  array('label'=>'Username',
													      'attr'=>array('class'=>'form-control')))				
		        ->add('age', NumberType::class,     array('label'=>'Age', 
													      'attr'=>array('class'=>'form-control')))
				->add('family', TextType::class,    array('label'=>'Family',
											              'attr'=>array('class'=>'form-control')))
				->add('food', TextType::class,      array('label'=>'Food',
													      'attr'=>array('class'=>'form-control')))
				->add('race', TextType::class,      array('label'=>'race',
													      'attr'=>array('class'=>'form-control')))								
           		->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), 
				           			    array('type'            => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
				                              'options'         => array('translation_domain' => 'FOSUserBundle'),
				                              'first_options'   => array('label' => 'Mot de passe','attr'=>array('class'=>'form-control')),
				                              'second_options'  => array('label' => 'Confirmez le mot de passe','attr'=>array('class'=>'form-control')),
				                              'invalid_message' => 'Ces mots de passe ne correspondent pas !'))
           		->add('Create', SubmitType::class, array('attr'=>array('class'=>'btn btn-info btn-lg')));
	}	


    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getName()
    {
        return 'user_registration';
    }
}
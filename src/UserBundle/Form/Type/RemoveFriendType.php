<?php

namespace UserBundle\Form\Type;

use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use UserBundle\Repository\UserRepository;

class RemoveFriendType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$id=$options['id'];

		$builder->add('users', EntityType::class, array('label'=>'Name', 
													    'class' => 'UserBundle:User',
													    'choice_label' => 'name',
													    'multiple'=>true,
													    'query_builder' => function(UserRepository $er) use($id) {
													        return $er->getMyFriendsInTheList($id);
													     },
													    'attr'=>array('class'=>'form-control')))
				->add('submit', SubmitType::class, array('attr'=>array('class'=>'btn btn-success',
														 'label'=>'Add')));
				
	}

	public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setRequired(['id']);
	}
}



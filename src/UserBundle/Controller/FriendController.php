<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use UserBundle\Form\Type\AddFriendType;
use UserBundle\Form\Type\RegistrationFormType;
use UserBundle\Form\Type\RemoveFriendType;


class FriendController extends Controller
{
	public function listAction(Request $request)
	{
		$em=$this->getDoctrine()->getManager();
		$session=$request->getSession();
		$user=$this->getUser();
		
		$listFriends=$user->getMyFriends();
			

		//Appel du formulaire pour ajouter un ami à sa liste
			$form=$this->createForm(AddFriendType::class, null);

			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid())
			{
				$data=$form->getData();
				$friends=$data['users'];

				foreach($friends as $friend)
				{
					$user->addMyFriend($friend);
					$em->flush();
				}

				$session->getFlashBag()->add('successCreate', 'This bonobo is added at your list !');
				return $this->redirectToRoute('user_friend_list');
			}

		
		

		return $this->render('UserBundle:Friend:list.html.twig', 
			array('friends'=>$listFriends,
				  'form'       =>$form->createView()));

	}

	public function editFriendAction(Request $request, $id)
    {
    	$em=$this->getDoctrine()->getManager();
    	$user=$this->getUser();

		// Sélection des friends de l'user connecté
		$listFriends=$user->getMyFriends();

		if($id)
		{
			//Vérification de sécurité et sélection du friend
			$listId=[];
			foreach($listFriends as $friend)
			{
				$idUser=$friend->getId();
				$listId[]=$idUser;
			}

			if(in_array($id, $listId))
			{
				$friend=$em->getRepository('UserBundle:User')->find($id);
		        /** @var $dispatcher EventDispatcherInterface */
		        $dispatcher = $this->get('event_dispatcher');

		        $event = new GetResponseUserEvent($friend, $request);
		        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

		        if (null !== $event->getResponse()) {
		            return $event->getResponse();
		        }
		       
		        // Remplacement du form de FosUserBundle par le mien
		        $form = $this->createForm(RegistrationFormType::class, $friend);		        

		        $form->handleRequest($request);

		        if ($form->isSubmitted() && $form->isValid()) {
		            /** @var $userManager UserManagerInterface */
		            $userManager = $this->get('fos_user.user_manager');

		            $event = new FormEvent($form, $request);
		            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

		            $userManager->updateUser($friend);

		            if (null === $response = $event->getResponse()) {
		                $url = $this->generateUrl('fos_user_profile_show');
		                $response = new RedirectResponse($url);
		            }

		            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($friend, $request, $response));

		            return $response;
		        }

		    }
			else
			{
				throw new AccessDeniedException('This user does not have access to this section.');
			}
        }
        else
        {
        	throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('UserBundle:Friend:editFriend.html.twig', array(
            'form' => $form->createView(),
            'friend'=>$friend
        ));
    }

    public function showAction($id)
    {
    	$em=$this->getDoctrine()->getManager();
    	$user=$this->getUser();

		// Sélection des friends de l'user connecté
		$listFriends=$user->getMyFriends();

		if($id)
		{
			//Vérification de sécurité et sélection du friend
			$listId=[];
			foreach($listFriends as $friend)
			{
				$idUser=$friend->getId();
				$listId[]=$idUser;
			}

			if(in_array($id, $listId))
			{
				$friend=$em->getRepository('UserBundle:User')->find($id);
			}
			else
			{
				throw new AccessDeniedException('This user does not have access to this section.');
			}
        }
        else
        {
        	throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('UserBundle:Friend:show.html.twig', array('friend'=>$friend));
    }

    public function deleteAction()
    {
    	$em=$this->getDoctrine()->getManager();
    	$user=$this->getUser();		
		
		//Appel du formulaire pour retirer un ami de sa liste
		$form = $this->createForm(RemoveFriendType::class, null, [
				'id' => $user->getId()
				]);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$data=$form->getData();
			$friends=$data['users'];

			foreach($friends as $friend)
			{
				$user->removeMyFriend($friend);
				$em->flush();
			}

			$session->getFlashBag()->add('successDelete', 'This bonobo is removed from your list !');
			return $this->redirectToRoute('user_friend_list');
		}			

        return $this->render('UserBundle:Friend:delete.html.twig', array('form'=>$form->createView()));		
    }
}
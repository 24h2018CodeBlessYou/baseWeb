<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $helper)
    {
        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError()
        ]);
    }


    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should neever be reached!');
    }

    /**
     * @Route("/register", name="security_register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // Par defaut l'utilisateur aura toujours le rôle ROLE_USER
            $user->setRoles(['ROLE_USER']);

            // On enregistre l'utilisateur dans la base
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("redirection", name="security_redirection")
     */
    public function redirection(AuthorizationCheckerInterface $authChecker) {
        if($authChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("admin_index");
        }
        elseif ($authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute("user_index");
        }
        else {
            return $this->redirectToRoute("index");
        }
    }
}

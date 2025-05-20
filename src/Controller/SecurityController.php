<?php
// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authUtils): Response
    {
        // s’il y a déjà un utilisateur connecté, on le redirige
        if ($this->getUser()) {
            return $this->redirectToRoute('app_appointment_index');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error'         => $authUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        // Symfony gère la déconnexion, pas besoin de code ici
        throw new \LogicException('This method can be blank – it will be intercepted by the logout key on your firewall.');
    }
}

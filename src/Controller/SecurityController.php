<?php
// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirige l’utilisateur déjà connecté vers la page d’accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_appointment_index');
        }

        // Récupère le dernier identifiant saisi et l’erreur éventuelle
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Cette méthode peut rester vide, Symfony intercepte la route pour gérer la déconnexion
        throw new \LogicException('Cette méthode peut rester vide - elle sera interceptée par le firewall.');
    }
}

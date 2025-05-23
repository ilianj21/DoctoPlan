# DoctoPlan

**DoctoPlan** est une application Symfony permettant la gestion de rendez-vous médicaux entre patients et médecins.

## Fonctionnalités

- Authentification sécurisée (inscription, connexion, déconnexion)
- Gestion des utilisateurs (admin, médecin, patient)
- Création automatique des créneaux disponibles (TimeSlot)
- Prise de rendez-vous (Appointment)
- Panneau d'administration avec contrôle des rôles

## 🧑‍⚕️ Rôles disponibles

- `ROLE_USER` : Patient (par défaut à l'inscription)
- `ROLE_DOCTOR` : Médecin
- `ROLE_ADMIN` : Administrateur

## 🔧 Technologies

- Symfony 5+
- Doctrine ORM
- SQLite (base de données locale)
- Bootstrap (via twig)
- Composer

---

## Installation

### 1. Cloner le projet

git clone https://github.com/ilianj21/DoctoPlan.git
cd DoctoPlan

-BDD
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

-Lancement du serveur
.\bin\Symfony.exe server:start

Lien : http://127.0.0.1:8000/appointment/

Pour acceder au user : 
http://127.0.0.1:8000/user/
Compte de test : 
- user -> ilianjean@gmail.com - MDP : ilianjean
- doc-> doc@gmail.com - MDP : 123456
- admin-> admin@admin.com - MDP : administrateur

Le mot de passe doit contenir minimum 6 caractères. 

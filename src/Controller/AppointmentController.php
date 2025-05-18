<?php
// src/Controller/AppointmentController.php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\TimeSlotRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/appointment')]
class AppointmentController extends AbstractController
{
    #[Route('/', name: 'app_appointment_index', methods: ['GET'])]
    public function index(AppointmentRepository $repo): Response
    {
        return $this->render('appointment/index.html.twig', [
            'appointments' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_appointment_new', methods: ['GET','POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        TimeSlotRepository $slotRepo,
        UserRepository $userRepo
    ): Response {
        $appointment = new Appointment();

        // Récupérer les données soumises, sans provoquer l'erreur "non-scalar default"
        $submitted = $request->request->get('appointment');
        if (!\is_array($submitted)) {
            $submitted = [];
        }

        // Identifier le médecin sélectionné (pour pré-charger les créneaux)
        $doctorId = $submitted['doctor'] ?? null;
        $doctor   = $doctorId ? $userRepo->find($doctorId) : null;

        $form = $this->createForm(AppointmentType::class, $appointment, [
            'doctor' => $doctor,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($appointment->getTimeSlot() && $appointment->getStatus()) {
                $em->persist($appointment);
                $em->flush();
                $this->addFlash('success', 'Rendez-vous créé avec succès.');

                return $this->redirectToRoute('app_appointment_index');
            }
        }

        return $this->render('appointment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_appointment_show', methods: ['GET'])]
    public function show(Appointment $appointment): Response
    {
        return $this->render('appointment/show.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appointment_edit', methods: ['GET','POST'])]
    public function edit(
        Request $request,
        Appointment $appointment,
        EntityManagerInterface $em,
        TimeSlotRepository $slotRepo,
        UserRepository $userRepo
    ): Response {
        // Même logique pour récupérer le doctor si on re-soumet via onchange
        $submitted = $request->request->get('appointment');
        if (!\is_array($submitted)) {
            $submitted = [];
        }

        $doctorId = $submitted['doctor'] 
            ?? $appointment->getDoctor()?->getId();
        $doctor   = $doctorId ? $userRepo->find($doctorId) : null;

        $form = $this->createForm(AppointmentType::class, $appointment, [
            'doctor' => $doctor,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($appointment->getTimeSlot() && $appointment->getStatus()) {
                $em->flush();
                $this->addFlash('success', 'Rendez-vous mis à jour.');

                return $this->redirectToRoute('app_appointment_index');
            }
        }

        return $this->render('appointment/edit.html.twig', [
            'form'        => $form->createView(),
            'appointment' => $appointment,
        ]);
    }

    #[Route('/{id}', name: 'app_appointment_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Appointment $appointment,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$appointment->getId(), $request->request->get('_token'))) {
            $em->remove($appointment);
            $em->flush();
            $this->addFlash('success', 'Rendez-vous supprimé.');
        }

        return $this->redirectToRoute('app_appointment_index');
    }
}

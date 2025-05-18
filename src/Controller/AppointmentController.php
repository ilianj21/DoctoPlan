<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
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

    #[Route('/new', name: 'app_appointment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On ne persiste que si timeSlot ET status sont renseignés
            if (null !== $appointment->getTimeSlot() && null !== $appointment->getStatus()) {
                $em->persist($appointment);
                $em->flush();

                $this->addFlash('success', 'Rendez-vous enregistré avec succès.');
                return $this->redirectToRoute('app_appointment_index');
            }
            // Sinon, on retombe dans le render pour laisser apparaître les erreurs / re-compléter le form
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

    #[Route('/{id}/edit', name: 'app_appointment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Appointment $appointment, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Même logique : on ne flush que si timeSlot ET status sont définis
            if (null !== $appointment->getTimeSlot() && null !== $appointment->getStatus()) {
                $em->flush();

                $this->addFlash('success', 'Rendez-vous mis à jour avec succès.');
                return $this->redirectToRoute('app_appointment_index');
            }
        }

        return $this->render('appointment/edit.html.twig', [
            'appointment' => $appointment,
            'form'        => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_appointment_delete', methods: ['POST'])]
    public function delete(Request $request, Appointment $appointment, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appointment->getId(), $request->request->get('_token'))) {
            $em->remove($appointment);
            $em->flush();
            $this->addFlash('success', 'Rendez-vous supprimé.');
        }

        return $this->redirectToRoute('app_appointment_index');
    }
}

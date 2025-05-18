<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\TimeSlotRepository;
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
        TimeSlotRepository $slotRepo
    ): Response {
        $appointment = new Appointment();
        // récupérer doctor pré-soumis pour recharger les créneaux
        $submitted = $request->request->get('appointment', []);
        $doctorId  = $submitted['doctor'] ?? null;
        $doctor    = $doctorId 
            ? $slotRepo->getEntityManager()->getRepository(\App\Entity\User::class)->find($doctorId)
            : null;

        $form = $this->createForm(AppointmentType::class, $appointment, [
            'doctor' => $doctor,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // on persiste dès que timeSlot et status sont remplis
            if ($appointment->getTimeSlot() && $appointment->getStatus()) {
                $em->persist($appointment);
                $em->flush();
                $this->addFlash('success','RDV créé avec succès.');
                return $this->redirectToRoute('app_appointment_index');
            }
        }

        return $this->render('appointment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appointment_edit', methods: ['GET','POST'])]
    public function edit(
        Request $request,
        Appointment $appointment,
        EntityManagerInterface $em,
        TimeSlotRepository $slotRepo
    ): Response {
        // même logique pour recharger les créneaux existants
        $doctor    = $appointment->getDoctor();
        $form = $this->createForm(AppointmentType::class, $appointment, [
            'doctor' => $doctor,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($appointment->getTimeSlot() && $appointment->getStatus()) {
                $em->flush();
                $this->addFlash('success','RDV mis à jour.');
                return $this->redirectToRoute('app_appointment_index');
            }
        }

        return $this->render('appointment/edit.html.twig', [
            'form'        => $form->createView(),
            'appointment'=> $appointment,
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
            $this->addFlash('success','RDV supprimé.');
        }
        return $this->redirectToRoute('app_appointment_index');
    }
}

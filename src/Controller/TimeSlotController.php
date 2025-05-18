<?php

namespace App\Controller;

use App\Entity\TimeSlot;
use App\Form\TimeSlotType;
use App\Repository\TimeSlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/time/slot')]
class TimeSlotController extends AbstractController
{
    #[Route(name: 'app_time_slot_index', methods: ['GET'])]
    public function index(TimeSlotRepository $timeSlotRepository): Response
    {
        return $this->render('time_slot/index.html.twig', [
            'time_slots' => $timeSlotRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_time_slot_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        TimeSlotRepository $repo
    ): Response {
        $timeSlot = new TimeSlot();
        $form = $this->createForm(TimeSlotType::class, $timeSlot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conflicts = $repo->findOverlappingSlots(
                $timeSlot->getDoctor(),
                $timeSlot->getStartAt(),
                $timeSlot->getEndAt()
            );

            if (count($conflicts) > 0) {
                // Conflit : on reste sur le form et on ajoute un message
                $form->addError(new FormError(
                    'Ce créneau chevauche un créneau existant pour ce médecin.'
                ));
                $this->addFlash('warning', 'Le créneau choisi n\'est pas disponible.');
            } else {
                // Persistance + flash + redirect (POST→REDIRECT→GET)
                $em->persist($timeSlot);
                $em->flush();
                $this->addFlash('success', 'Créneau enregistré avec succès.');
                return $this->redirectToRoute('app_time_slot_index');
            }
        }

        return $this->render('time_slot/new.html.twig', [
            'time_slot' => $timeSlot,
            'form'      => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_time_slot_show', methods: ['GET'])]
    public function show(TimeSlot $timeSlot): Response
    {
        return $this->render('time_slot/show.html.twig', [
            'time_slot' => $timeSlot,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_time_slot_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        TimeSlot $timeSlot,
        EntityManagerInterface $em,
        TimeSlotRepository $repo
    ): Response {
        $form = $this->createForm(TimeSlotType::class, $timeSlot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conflicts = $repo->findOverlappingSlots(
                $timeSlot->getDoctor(),
                $timeSlot->getStartAt(),
                $timeSlot->getEndAt()
            );
            // Exclure le créneau actuel
            $conflicts = array_filter($conflicts, fn($c) => $c->getId() !== $timeSlot->getId());

            if (count($conflicts) > 0) {
                $form->addError(new FormError(
                    'Ce créneau chevauche un créneau existant pour ce médecin.'
                ));
                $this->addFlash('warning', 'Le créneau modifié n\'est pas disponible.');
            } else {
                $em->flush();
                $this->addFlash('success', 'Créneau mis à jour avec succès.');
                return $this->redirectToRoute('app_time_slot_index');
            }
        }

        return $this->render('time_slot/edit.html.twig', [
            'time_slot' => $timeSlot,
            'form'      => $form,
        ]);
    }
    
    #[Route('/available/{doctor}', name: 'available_slots', methods: ['GET'])]
    public function available(TimeSlotRepository $repo, User $doctor): JsonResponse
    {
        $slots = $repo->findAvailableSlotsForDoctor($doctor);
        $data  = array_map(fn(TimeSlot $t) => [
            'id'    => $t->getId(),
            'label' => $t->getStartAt()->format('d/m/Y H:i') . ' – ' . $t->getEndAt()->format('H:i'),
        ], $slots);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'app_time_slot_delete', methods: ['POST'])]
    public function delete(Request $request, TimeSlot $timeSlot, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timeSlot->getId(), $request->request->get('_token'))) {
            $em->remove($timeSlot);
            $em->flush();
        }

        return $this->redirectToRoute('app_time_slot_index');
    }
}

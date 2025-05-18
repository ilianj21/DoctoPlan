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
    #[Route('/', name: 'app_time_slot_index', methods: ['GET'])]
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
            // fusion date + heure
            $d1 = $form->get('startAt')->getData();
            $t1 = $form->get('startAtTime')->getData();
            $d2 = $form->get('endAt')->getData();
            $t2 = $form->get('endAtTime')->getData();
            $timeSlot->setStartAt(new \DateTimeImmutable($d1->format('Y-m-d').' '.$t1->format('H:i')));
            $timeSlot->setEndAt(new \DateTime($d2->format('Y-m-d').' '.$t2->format('H:i')));

            // détection chevauchement
            $conflicts = $repo->findOverlappingSlots(
                $timeSlot->getDoctor(),
                $timeSlot->getStartAt(),
                $timeSlot->getEndAt()
            );

            if (count($conflicts) > 0) {
                $form->addError(new FormError('Ce créneau chevauche un autre créneau pour ce médecin.'));
                $this->addFlash('warning', 'Le créneau choisi n\'est pas disponible.');
            } else {
                $em->persist($timeSlot);
                $em->flush();
                $this->addFlash('success', 'Créneau enregistré avec succès.');

                return $this->redirectToRoute('app_time_slot_index');
            }
        }

        return $this->render('time_slot/new.html.twig', [
            'time_slot' => $timeSlot,
            'form'      => $form->createView(),
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
            // fusion date + heure
            $d1 = $form->get('startAt')->getData();
            $t1 = $form->get('startAtTime')->getData();
            $d2 = $form->get('endAt')->getData();
            $t2 = $form->get('endAtTime')->getData();
            $timeSlot->setStartAt(new \DateTimeImmutable($d1->format('Y-m-d').' '.$t1->format('H:i')));
            $timeSlot->setEndAt(new \DateTime($d2->format('Y-m-d').' '.$t2->format('H:i')));

            // détection chevauchement hors lui-même
            $conflicts = $repo->findOverlappingSlots(
                $timeSlot->getDoctor(),
                $timeSlot->getStartAt(),
                $timeSlot->getEndAt()
            );
            $conflicts = array_filter($conflicts, fn($c) => $c->getId() !== $timeSlot->getId());

            if (count($conflicts) > 0) {
                $form->addError(new FormError('Ce créneau chevauche un autre créneau pour ce médecin.'));
                $this->addFlash('warning', 'Le créneau modifié n\'est pas disponible.');
            } else {
                $em->flush();
                $this->addFlash('success', 'Créneau mis à jour avec succès.');

                return $this->redirectToRoute('app_time_slot_index');
            }
        }

        return $this->render('time_slot/edit.html.twig', [
            'time_slot' => $timeSlot,
            'form'      => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_time_slot_delete', methods: ['POST'])]
    public function delete(Request $request, TimeSlot $timeSlot, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timeSlot->getId(), $request->request->get('_token'))) {
            $em->remove($timeSlot);
            $em->flush();
            $this->addFlash('success', 'Créneau supprimé.');
        }

        return $this->redirectToRoute('app_time_slot_index');
    }
}

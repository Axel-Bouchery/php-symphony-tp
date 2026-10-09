<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RestaurantController extends AbstractController
{
    #[Route('/restaurant', name: 'app_restaurant')]
    public function index(): Response
    {
        return $this->render('restaurant/index.html.twig', [
            'controller_name' => 'RestaurantController',
        ]);
    }

    #[Route('/restaurant/new', name: 'app_restaurant_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();

        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Restaurant créé avec succès.');

            return $this->redirectToRoute('app_restaurant');
        }

        return $this->render('restaurant/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/restaurant/{id}/edit', name: 'app_restaurant_edit')]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($id);

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant non trouvé.');
        }

        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Restaurant modifié avec succès.');

            return $this->redirectToRoute('app_restaurant');
        }

        return $this->render('restaurant/edit.html.twig', [
            'form' => $form,
            'restaurant' => $restaurant,
        ]);
    }
}



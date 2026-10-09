<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\RestaurantType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VilleRepository;
use App\Entity\Commentaire;
use App\Form\CommentaireType;

final class RestaurantController extends AbstractController
{
    #[Route('/restaurant', name: 'app_restaurant')]
    public function index(
        Request $request,
        RestaurantRepository $restaurantRepository,
        VilleRepository $villeRepository
    ): Response {
        $villeNom = $request->query->get('ville');

        if ($villeNom) {
            $ville = $villeRepository->findOneBy([
                'nom' => $villeNom,
            ]);

            $restaurants = $ville
                ? $restaurantRepository->findBy(['ville' => $ville])
                : [];
        } else {
            $restaurants = $restaurantRepository->findAll();
        }

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    #[Route('/restaurant/{id}', name: 'app_restaurant_show', requirements: ['id' => '\d+'])]
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
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

            return $this->redirectToRoute('app_restaurant');
        }

        return $this->render('restaurant/add.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/restaurant/{id}/edit', name: 'app_restaurant_edit')]
    public function edit(
        Restaurant $restaurant,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant');
        }

        return $this->render('restaurant/edit.html.twig', [
            'form' => $form,
            'restaurant' => $restaurant,
        ]);
    }




    #[Route('/restaurant/{id}/delete', name: 'app_restaurant_delete')]
    public function delete(
        Restaurant $restaurant,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($restaurant);
        $entityManager->flush();

        return $this->redirectToRoute('app_restaurant');
    }






    #[Route('/restaurant/{id}/commentaire/new', name: 'app_commentaire_new')]
    public function newCommentaire(
        Restaurant $restaurant,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $commentaire = new Commentaire();
        $commentaire->setRestaurant($restaurant);
        $commentaire->setDate(new \DateTime());

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant_show', [
                'id' => $restaurant->getId(),
            ]);
        }

        return $this->render('commentaire/new.html.twig', [
            'form' => $form,
            'restaurant' => $restaurant,
        ]);
    }






    #[Route('/commentaire/{id}/delete', name: 'app_commentaire_delete')]
    public function deleteCommentaire(
        Commentaire $commentaire,
        EntityManagerInterface $entityManager
    ): Response {
        $restaurant = $commentaire->getRestaurant();

        $entityManager->remove($commentaire);
        $entityManager->flush();

        return $this->redirectToRoute('app_restaurant_show', [
            'id' => $restaurant->getId(),
        ]);
    }

}
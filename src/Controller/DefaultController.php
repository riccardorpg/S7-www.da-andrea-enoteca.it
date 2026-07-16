<?php

namespace App\Controller;

use App\Repository\WineCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(Request $request, WineCategoryRepository $wineCategoryRepository): Response
    {
        $session = $request->getSession();
        $section = $session->get('section');
        $session->remove('section');

        return $this->render('default/index.html.twig', [
            'section' => $section,
            'wineCategories' => $wineCategoryRepository->findAllOrdered(),
        ]);
    }

    #[Route('/home/{section}', name: 'homepage_with_section')]
    public function indexWithSection(Request $request, string $section): Response
    {
        $session = $request->getSession();
        $session->set('section', $section);

        return $this->redirectToRoute('homepage');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        $numbers = $this->lazyRange(1, 4000000);
        for ($i = 0; $i < 2000000; $i++) {
            $numbers->next();
        }
        return $this->render('task/index.html.twig', [
            'value' => json_encode($numbers->current())
        ]);
    }

    private function lazyRange(int $start, int $end): \Generator
    {
        for ($i = $start; $i <= $end; $i++) {
            yield $i;
        }
    }
}
<?php

declare(strict_types=1);

namespace App\UI\Http;

use App\Domain\Customer\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class CustomerController extends AbstractController
{
    public function __construct(private readonly CustomerRepository $customerRepository) {}

    #[Route('/', name: 'login', methods: ['GET'])]
    public function login(): Response
    {
        $customers = $this->customerRepository->findAll();

        return $this->render('customer/login.html.twig', [
            'customers' => $customers,
        ]);
    }

    #[Route('/', name: 'login_submit', methods: ['POST'])]
    public function loginSubmit(Request $request): Response
    {
        $customerId = $request->request->getString('customerId');

        if (empty($customerId)) {
            $this->addFlash('error', 'Please select a customer');
            return $this->redirectToRoute('login');
        }

        $request->getSession()->set('customerId', $customerId);

        return $this->redirectToRoute('customer_shop', ['customerId' => $customerId]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(Request $request): Response
    {
        $request->getSession()->remove('customerId');

        return $this->redirectToRoute('login');
    }
}


<?php

namespace App\Controller;

// TODO: Refactor this shit

use App\DTO\CalculatePriceDTO;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Entity\TaxNumber;
use App\Service\TaxService;
use App\Service\PaymentService;
use App\Type\PaymentStatus;
use App\Type\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SiteController extends AbstractController
{
    #[Route('/')]
    public function index(): JsonResponse
    {
        return $this->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/calculate-price', methods: ['POST'])]
    public function calculatePrice(Request $request, EntityManagerInterface	$em, ValidatorInterface $validator, PaymentService $paymentService): JsonResponse
    {
		$data = json_decode($request->getContent(), true);

        $copons = $em->getRepository(Coupon::class)->findBy(['status' => Status::ACTIVE]);
        $taxNumberPatterns = $em->getRepository(TaxNumber::class)->findAll();

        $paymentService->setTaxNumber($data['taxNumber'] ?? '')
            ->setProductId($data['product'] ?? 0)
            ->setCouponCode($data['couponCode'] ?? '')
            ->setCopons($copons)
            ->setTaxNumberPatterns($taxNumberPatterns);

		$errors = $validator->validate($paymentService);

        if ($errors->count() > 0) {
            return $this->renderError($errors);
		}

		$product = $em->getRepository(Product::class)->find($paymentService->getProductID());
		if (!$product) {
            throw $this->createNotFoundException(
                '1.No product found for id '.$paymentService->getProductID()
            );
		}

		$price = $paymentService->calculateTax($product->getPrice()) / 100;

        return $this->json(['error' => '', 'price' => $price], Response::HTTP_OK);
    }

    #[Route('/purchase', methods: ['POST'])]
    public function purchase(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, PaymentService $paymentService): JsonResponse
    {
		$data = json_decode($request->getContent(), true);

        $copons = $em->getRepository(Coupon::class)->findBy(['status' => Status::ACTIVE]);
        $taxNumberPatterns = $em->getRepository(TaxNumber::class)->findAll();

        $paymentService->setTaxNumber($data['taxNumber'] ?? '')
            ->setProductId($data['product'] ?? 0)
            ->setCouponCode($data['couponCode'] ?? '')
            ->setPaymentProcessor($data['paymentProcessor'] ?? '')
            ->setCopons($copons)
            ->setTaxNumberPatterns($taxNumberPatterns);

        $errors = $validator->validate($paymentService);

        if ($errors->count() > 0) {
            return $this->renderError($errors);
		}

		$product = $em->getRepository(Product::class)->find($paymentService->getProductID());

        $paymentStatus = $paymentService->pay($product->getPrice());

        if ($paymentStatus === PaymentStatus::FAIL) {
            return $this->json(['error' => $paymentService->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        if ($paymentStatus === PaymentStatus::UNKNOWN) {
            return $this->json(['error' => 'Unknown payment method ' . $data['paymentProcessor']], Response::HTTP_BAD_REQUEST);
        }

        $price = $paymentService->calculateTax($product->getPrice()) / 100;
        return $this->json(['error' => '', 'product_id' => $paymentService->getProductID(), 'price' => $price], Response::HTTP_OK);
    }

    private function renderError(ConstraintViolationList $errors): JsonResponse
    {
        $err = [];
        foreach($errors as $id => $error) {
            $err[] = ($id + 1) . '.' . $error->getMessage();
        }
        return $this->json(['error' => implode('; ', $err)], Response::HTTP_BAD_REQUEST);
    }
}

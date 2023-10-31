<?php

namespace App\Action;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Exception\EmptyCartException;
use App\Entity\Order;
use App\Entity\LineOrder;

class CheckoutAction extends AbstractController
{
    private EntityManagerInterface $em;

    private ProductRepository $productRepo;

    public function __construct(EntityManagerInterface $em, ProductRepository $productRepo)
    {
        $this->em = $em;
        $this->productRepo = $productRepo;
    }

    #[IsGranted("ROLE_USER")]
    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if(!in_array("ROLE_USER", $user->getRoles())) {
            throw new Exception("Vous n'avez pas le droit d'être la");
        }

        $content = json_decode($request->getContent(), true);
        if (empty($content)) {
            throw new EmptyCartException();
        }
        $tokenProvider = $this->container->get('security.csrf.token_manager');
        $token = $tokenProvider->getToken('stripe_token')->getValue();
        $stripe_items = [];
        $order = new Order();
        $order->setDatetime(new DateTime());
        $order->setStatus("PAYMENT_WAITING");
        $total = 0;

        foreach ($content as $key => $quantity) {
            $product = $this->productRepo->find($key);
            dump($key, $quantity);
            $line = new LineOrder();
            $line->setProduct($product);
            $line->setQuantity($quantity);
            $line->setSubtotal($quantity * $product->getPrice());
            $total += $quantity * $product->getPrice();
            $order->addLineOrder($line);
            $stripe_items[] =
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product->getName(),
                        ],
                        'unit_amount' => $product->getPrice(),
                    ],
                    'quantity' => $quantity,
                ];
        }
        $order->setTotal($total);
        $this->em->persist($order);
        $this->em->flush();

        Stripe::setApiKey($_ENV["STRIPE_API_KEY"]);
        $session = Session::create([
            'line_items' => $stripe_items,
            'mode' => 'payment',
            'success_url' => 'http://localhost:3000/checkout_success',
            'cancel_url' => 'http://localhost:3000/checkout_error'
        ]);

        return new JsonResponse([
            "order" => ["order_waiting", $order->getId()],
            "stripe" => $session,
            "token_stripe" => $token
        ]);
    }
}
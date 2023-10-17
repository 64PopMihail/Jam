<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Util\TokenUtil;

class CheckoutProcessor implements ProcessorInterface
{

    private EntityManagerInterface $entityManager;

    //private ContainerInterface $container;

    public function __construct(
        EntityManagerInterface $entityManager,
        //ContainerInterface $container
    )
    {
        $this->entityManager = $entityManager;
        //$this->container = $container;
    }

    #[IsGranted("ROLE_USER")]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        //$token = TokenUtil::getStripeToken($this->container->get('security.csrf.token_manager'));
        //dd($token,$data);
        $stripe_items = [];
        $cart = $session->get("cart", []);
        if (empty($cart)) {
            return $this->redirectToRoute("home");
        }
        $order = new Order;
        $order->setDatetime(new DateTime);
        $order->setStatus("PAYMENT_WAITING");
        $total = 0;

        foreach ($cart as $key => $quantity) {
            $product = $productRepo->find($key);
            $line = new LineOrder();
            $line->setProduct($product);
            $line->setQuantity($quantity);
            $line->setSubtotal($quantity * $product->getPrice());
            $total += $quantity * $product->getPrice();
            $order->addLineOrder($line);
            //equivalent
            // $line->setOrderAssociated($order);
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
        $manager->persist($order);
        $manager->flush();
        $session->set("order_waiting", $order->getId());

        Stripe::setApiKey($_ENV["STRIPE_API_KEY"]);
        $session = Session::create([
            'line_items' => $stripe_items,
            'mode' => 'payment',
            'success_url' => 'http://localhost:80/checkout_success/' . $token,
            'cancel_url' => 'http://localhost:80/checkout_error'
        ]);
        //dd($token,$stripe_items, $session, $session->url);
        return $this->redirect($session->url, 303);
    }
}
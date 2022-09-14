<?php

namespace App\Controller;

use App\Repository\ChatTypeRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;

class TestController extends AbstractController
{
    private LoggerInterface $logger;
    private ChatTypeRepository $repoChat;

    public function __construct(LoggerInterface $logger,ChatTypeRepository $repoChat)
    {
        $this->logger = $logger;
        $this->repoChat = $repoChat;
    }

    #[Route('/test/test', name: 'test')]
    public function getJwt(): string
    {
        return 'the-JWT';
    }


    public function publish(HubInterface $hub): Response
    {
        // http://mercury.localhost:61150/my-private-topic
        $update = new Update(
            'my-private-topic',
            json_encode([0=>['Id' => 'OutOfStock','Alias' => 'OutOfStock','Message' => 'OutOfStock','MessageTime' => '123456789']])
        );
        $hub->publish($update);

        return new Response('published!');
    }
}


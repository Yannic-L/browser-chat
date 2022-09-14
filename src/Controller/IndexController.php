<?php

namespace App\Controller;

use App\Entity\ChatType;
use App\Repository\ChatTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private EntityManagerInterface $em;
    private ChatTypeRepository $repoChat;
    private LoggerInterface $logger;
    private HubInterface $hub;


    function __construct(EntityManagerInterface $em, ChatTypeRepository $repoChat, LoggerInterface $logger, HubInterface $hub)
    {
        $this->em = $em;
        $this->repoChat = $repoChat;
        $this->logger = $logger;
        $this->hub = $hub;

    }

    //#[Route('/update/ajax')]
    //public function ajaxAction(Request $request)
    //{
    //    $CleanMessages = $this->lodeLastMesagess();
    //    return new JsonResponse($CleanMessages);
    //
    //}

    #[Route('{lastName}', name: 'app_index', defaults: ['lastName' => 'Anonym User'])]
    public function browserChat(Request $request, string $lastName = ''): Response
    {
        $form = $this->createFormBuilder(null, ['data_class' => ChatType::class])
            ->add('message', TextType::class, [
                'attr' => [
                    'autofocus' => true,
                    'placeholder' => 'Nachricht'
                ],
                'data' => "Nachricht"
            ])
            ->add('alias', (($lastName === 'Anonym User') ? TextType::class : HiddenType::class), [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Choose you name',
                ],
                'data' => $lastName,
                'empty_data' => "Anonym User"
            ])
            ->add('save', SubmitType::class, ['label' => 'Send Message'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var $chatMessage ChatType
             */
            $chatMessage = $form->getData();
            $chatMessage->setMessage(htmlspecialchars($chatMessage->getMessage())); # Disabel vor fun

            $this->logger->info($chatMessage->getMessage());
            $this->em->persist($chatMessage);
            $this->em->flush();

            $this->saveToDB($chatMessage);
            $this->sendUpdate($chatMessage);

            return $this->redirectToRoute('app_index', ['lastName' => $chatMessage->getAlias()]);
        }
        $initalMessage = $this->repoChat->findeLastMessage()["0"]->getMessageTime();
        return $this->renderForm('new.html.twig', [
            'initalMessage' => ($initalMessage),
            'form' => $form,
            'alias' => $lastName]);
    }

    public function saveToDB($chatMessage)
    {
        $chatMessage->setMessage(htmlspecialchars($chatMessage->getMessage())); # Disabel vor fun

        $this->logger->info($chatMessage->getMessage());
        $this->em->persist($chatMessage);
        $this->em->flush();
    }
    public function sendUpdate($chatMessage)
    {
        $update = new Update(
            'push-New-Messages',
            json_encode([
                'Id' => $chatMessage->getId(),
                'Alias' => $chatMessage->getAlias(),
                'Message' => $chatMessage->getMessage(),
                'MessageTime' => $chatMessage->getMessageTime()
            ])
        );
        $this->hub->publish($update);
    }

    #[Route('/update/ajax/old/{MessageTime}', name: 'oldAjaxAction')]
    public function loadOldMessages(string $MessageTime='')
    {
        foreach ($this->repoChat->fetchChatTypesBefore($MessageTime) as $val => $item) {
            $CleanMessages[$val]["Id"] = intval($item->getID());
            $CleanMessages[$val]["Alias"] = $item->getAlias();
            $CleanMessages[$val]["Message"] = $item->getMessage();
            $CleanMessages[$val]["MessageTime"] = $item->getMessageTime();
        }
        return new Response (json_encode($CleanMessages));
    }


}
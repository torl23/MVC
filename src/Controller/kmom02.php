<?php

namespace App\Controller;

use App\Controller\card;
use App\Controller\deck;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class kmom02 extends AbstractController
{
    #[Route("/session", name: "session")]
    public function showSession(
        SessionInterface $session
    ): Response {
        // shows whats in the session
        $sessionData = $session->all();

        var_dump($sessionData);

        return $this->render('session.html.twig');
    }

    #[Route("/session/delete", name: "session_delete")]
    public function clearSession(
        SessionInterface $session
    ): Response {
        // clears the session
        $session->clear();

        $this->addFlash(
            'notice',
            'The session has been cleared'
        );
        return $this->redirectToRoute('session');
    }

    #[Route("/card", name: "card")]
    public function cards(
        SessionInterface $session
    ): Response {
        // links to the diffrent routes
        return $this->render('card.html.twig');
    }

    #[Route("/card/deck", name: "card_deck")]
    public function cardDeck(SessionInterface $session): Response
    {
        // shows all the current cards in the deck. if no deck exist make one.
        if (!$session->has("deck")) {
            $deck = new Deck();
            $originalCards = $deck->getCards();
            $shuffledCards = $deck->getShuffledCards();
            $session->set("deck", $originalCards);
            $session->set("shuffled", $shuffledCards);
        } else {
            $originalCards = $session->get("deck");
            $shuffledCards = $session->get("shuffled");
        }

        return $this->render('cardDeck.html.twig', [
            'originalCards' => $originalCards
        ]);
    }

    #[Route("/card/deck/shuffle", name: "shuffle_deck")]
    public function shuflle(
        SessionInterface $session
    ): Response {
        // makes a new deck and shuffle it each time you go to this route.
        $session->clear();
        $deck = new deck();
        $originalCards = $deck->getCards();
        $shuffledCards = $deck->getShuffledCards();
        $session->set("deck", $originalCards);
        $session->set("shuffled", $shuffledCards);
        return $this->render('shuffle.html.twig', ['shuffledCards' => $shuffledCards]);
    }

    #[Route("/card/deck/draw", name: "cardDraw", methods: ['GET', 'POST'])]
    public function drawcard(
        Request $request,
        SessionInterface $session
    ): Response {
        // takes a request from an input and draws that many cards from the deck.
        // can't draw more cards then what the current deck has
        $numCard = $request->request->get('num_cards');
        if (!$session->has("deck")) {
            $deck = new deck();
            $originalCards = $deck->getCards();
            $shuffledCards = $deck->getShuffledCards();
            $session->set("deck", $originalCards);
            $session->set("shuffled", $shuffledCards);
        }
        $originalCards = $session->get("deck");
        $left = count($originalCards);
        $drawnCards = [];
        if ($numCard <= $left) {
            for ($i = 0; $i < $numCard; $i++) {
                $originalCards = $session->get("deck");
                // takes a random index from the deck and removes it.
                $drawnCardIndex = array_rand($originalCards);
                $drawnCard = $originalCards[$drawnCardIndex];
                unset($originalCards[$drawnCardIndex]);

                // if multiple cards were drawn
                $drawnCards[] = $drawnCard;
                $session->set("deck", $originalCards);
            }
        }
        $left = count($originalCards);

        return $this->render('draw.html.twig', ['drawnCards' => $drawnCards, 'left' => $left]);
    }

    #[Route("/card/deck/draw/{numCard<\d+>}", name: "cardDraws")]
    public function drawcards(
        int $numCard,
        SessionInterface $session
    ): Response {
        // takes and draws the cards from the url
        if (!$session->has("deck")) {
            $deck = new Deck();
            $originalCards = $deck->getCards();
            $shuffledCards = $deck->getShuffledCards();
            $session->set("deck", $originalCards);
            $session->set("shuffled", $shuffledCards);
        }

        $originalCards = $session->get("deck");
        $left = count($originalCards);
        $drawnCards = [];

        if ($numCard <= $left) {
            for ($i = 0; $i < $numCard; $i++) {
                $originalCards = $session->get("deck");
                $drawnCardIndex = array_rand($originalCards);
                $drawnCard = $originalCards[$drawnCardIndex];
                unset($originalCards[$drawnCardIndex]);

                $drawnCards[] = $drawnCard;
                $session->set("deck", $originalCards);
            }
        }
        $left = count($originalCards);

        return $this->render('draw.html.twig', ['drawnCards' => $drawnCards, 'left' => $left]);
    }

    #[Route("/api/deck", name: "api_deck", methods: ['GET'])]
    public function apiDeck(SessionInterface $session): JsonResponse
    {
        if (!$session->has("deck")) {
            $deck = new Deck();
            $originalCards = $deck->getCards();
            $shuffledCards = $deck->getShuffledCards();
            $session->set("deck", $originalCards);
            $session->set("shuffled", $shuffledCards);
        }
        $originalCards = $session->get("deck");
        $jsonCards = [];
        foreach ($originalCards as $card) {
            $jsonCards[] = [
                'suits' => $card->getSuits(),
                'values' => $card->getValue()
            ];
        }
        $data = [
            'deck' => $jsonCards
        ];
        return new JsonResponse($data);
    }

    #[Route("/api/deck/shuffle", name: "api_deck_shuffle", methods: ['POST'])]
    public function apiDeckShuffle(SessionInterface $session): JsonResponse
    {
        if (!$session->has("deck")) {
            $deck = new Deck();
            $originalCards = $deck->getCards();
            $shuffledCards = $deck->getShuffledCards();
            $session->set("deck", $originalCards);
            $session->set("shuffled", $shuffledCards);
        }
        $shuffledCards = $session->get("shuffled");
        $jsonCards = [];
        foreach ($shuffledCards as $card) {
            $jsonCards[] = [
                'suits' => $card->getSuits(),
                'values' => $card->getValue()
            ];
        }
        $data = [
            'deck' => $jsonCards
        ];
        return new JsonResponse($data);
    }

    #[Route("/api/deck/draw", name: "apiCardDraws")]
    public function drawcardsJson(
        Request $request,
        SessionInterface $session
    ): JsonResponse {
        // takes a request from an input and draws that many cards from the deck.
        // can't draw more cards then what the current deck has
        $numCard = $request->request->get('api_num_cards');
        if (!$session->has("deck")) {
            $deck = new deck();
            $originalCards = $deck->getCards();
            $shuffledCards = $deck->getShuffledCards();
            $session->set("deck", $originalCards);
            $session->set("shuffled", $shuffledCards);
        }
        $originalCards = $session->get("deck");
        $left = count($originalCards);
        $drawnCards = [];
        if ($numCard <= $left) {
            for ($i = 0; $i < $numCard; $i++) {
                $originalCards = $session->get("deck");
                // takes a random index from the deck and removes it.
                $drawnCardIndex = array_rand($originalCards);
                $drawnCard = $originalCards[$drawnCardIndex];
                unset($originalCards[$drawnCardIndex]);

                // if multiple cards were drawn
                $drawnCards[] = $drawnCard;
                $session->set("deck", $originalCards);
            }
        }
        $left = count($originalCards);
        $jsonCardss = [];
        foreach ($drawnCards as $card) {
            $jsonCardss[] = [
                'suits' => $card->getSuits(),
                'values' => $card->getValue()
            ];
        }
        $data = [
            'drawn' => $jsonCardss,
            'left' => $left
        ];
        return new JsonResponse($data);
    }

    #[Route("/api/deck/draw/{numCard<\d+>}", name: "apiCardDraw")]
    public function drawcardJson(
        int $numCard,
        Request $request,
        SessionInterface $session
    ): JsonResponse {
        // takes a request from an input and draws that many cards from the deck.
        // can't draw more cards then what the current deck has
        // $numCard = $request->request->get('api_num_cards');
        if (!$session->has("deck")) {
            $deck = new deck();
            $originalCards = $deck->getCards();
            $shuffledCards = $deck->getShuffledCards();
            $session->set("deck", $originalCards);
            $session->set("shuffled", $shuffledCards);
        }
        $originalCards = $session->get("deck");
        $left = count($originalCards);
        $drawnCards = [];
        if ($numCard <= $left) {
            for ($i = 0; $i < $numCard; $i++) {
                $originalCards = $session->get("deck");
                // takes a random index from the deck and removes it.
                $drawnCardIndex = array_rand($originalCards);
                $drawnCard = $originalCards[$drawnCardIndex];
                unset($originalCards[$drawnCardIndex]);

                // if multiple cards were drawn
                $drawnCards[] = $drawnCard;
                $session->set("deck", $originalCards);
            }
        }
        $left = count($originalCards);
        $jsonCardss = [];
        foreach ($drawnCards as $card) {
            $jsonCardss[] = [
                'suits' => $card->getSuits(),
                'values' => $card->getValue()
            ];
        }
        $data = [
            'drawn' => $jsonCardss,
            'left' => $left
        ];
        return new JsonResponse($data);
    }
}

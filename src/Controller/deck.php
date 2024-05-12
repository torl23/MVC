<?php

namespace App\Controller;

class deck extends card
{
    protected $cards;
    protected $shuffledcards;

    // creates the cards by looping to things over eachother
    public function __construct()
    {
        $suits = ["♠", "♥", "♦", "♣"];
        $values = ["A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K"];

        //puts the cards in an array and make a shuffled copy of deck
        $this->cards = [];
        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $this->cards[] = new card($suit, $value);
            }
        }
        $this->shuffledCards = $this->cards;
        shuffle($this->shuffledCards);
    }

    // public function drawCard() {
    //     $index = array_rand($this->cards);
    //     $randomCard = $this->cards[$index];
    //     array_splice($this->cards, $index, 1);
    //     $this->cards = array_values($this->cards);
    //     return $randomCard;
    // }

    //to get the cards
    public function getCards(): array
    {
        return $this->cards;
    }

    public function getShuffledCards(): array
    {
        return $this->shuffledCards;
    }
}

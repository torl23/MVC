<?php

namespace App\Controller;

// create the cards
class card
{
    protected $suits;
    protected $values;

    public function __construct($suits, $values)
    {
        $this->suits = $suits;
        $this->values = $values;
    }

    // make the cards a string and gets the cards.
    public function __toString()
    {
        return "{$this->values}{$this->suits}";
    }

    public function getSuits(): string
    {
        return $this->suits;
    }

    public function getValue(): string
    {
        return $this->values;
    }
}

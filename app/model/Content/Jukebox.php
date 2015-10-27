<?php
namespace app\model\Content;

use app\helpers\Configuration as Cfg;
use app\helpers\Hash;
use app\helpers\General;


class Jukebox
{
    public $quotes = array();

    function __construct($user) {
        if($user) {
            $this->quotes[] = " je za Dinamo bez Mamića!";
            $this->quotes[] = " je iz metropole, a vi ste kurac!";
            if ($user->sex === Cfg::read('db.sex.male')) {
                $this->quotes[] = " voli samo Dinamo, alkohol i žene..";
                $this->quotes[] = " je pio pelina, pušio trave i najviše voli Dinamo sa Save!";
                $this->quotes[] = " piša u govornice i nije normalan.";
            } else if ($user->sex === Cfg::read('db.sex.female')){
                $this->quotes[] = " je pila pelina, pušila trave i najviše voli Dinamo sa Save!";
                $this->quotes[] = " voli samo Dinamo!";
            }
        }

    }

    public function getNumber() {
        return sizeof($this->quotes);
    }

    public function getRandomQuote() {
        return $this->quotes[rand(0, $this->getNumber() -1 )];
    }
}
?>
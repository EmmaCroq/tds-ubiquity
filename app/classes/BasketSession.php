<?php


namespace classes;

use models\Basket;
use models\Basketdetail;
use Ubiquity\orm\DAO;

class BasketSession
{
    private $basket;

    public function __construct($basket)
    {
        $this->idBasket = $basket->getId();
        $this->basket = $basket;
    }


    public function addProduct($article, $quantity)
    {
        if(DAO::getOne(Basketdetail::class,'idProduct = ?',false,[$article->getId()])){
            $this->jslog("There already a product");
        }else{
            $this->jslog("Add".$article->getName(). "product in ". $quantity);

            $basketDetail = new Basketdetail();
            $basketDetail->setBasket($this->basket);
            echo '<pre>';
            print_r($basketDetail);
            echo '</pre>';
            $basketDetail->setProduct($article);
            $basketDetail->setQuantity($quantity);
            DAO::save($basketDetail);
        }
    }

    public function getProducts()
    {
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        return $baskets->getBasketdetails();
    }


    public function getTotalFullPrice()
    {
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        $basketDetails = $baskets->getBasketdetails();
        $somme =0;
        foreach ($basketDetails as $basketDetail){
            $somme += $basketDetail->getProduct()->getPrice() * $basketDetail->getQuantity();
        }
        return $somme;
    }

    public function getTotalDiscount()
    {
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        $basketDetails = $baskets->getBasketdetails();
        $somme =0;
        foreach ($basketDetails as $basketDetail){
            $somme += $basketDetail->getProduct()->getPromotion() * $basketDetail->getQuantity();
        }
        return $somme;
    }

    public function getQuantity()
    {
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        $basketDetails = $baskets->getBasketdetails();
        $somme =0;
        foreach ($basketDetails as $basketDetail){
            $somme += $basketDetail->getQuantity();
        }
        return $somme;
    }

    private function jslog($messageLog){
        echo "<script> console.log('$messageLog ')</script>";
    }

}
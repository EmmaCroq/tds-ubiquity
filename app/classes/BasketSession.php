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

    public function addProduct($article, $quantity, $basket)
    {
        if(DAO::getOne(Basketdetail::class,'idBasket = ? and idProduct = ?',false,[$basket->getId(), $article->getId()])){
            echo "Il y a déjà un produit de ce type dans votre panier";
        }else{
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

    public function getId()
    {
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        return $baskets->getId();
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

    public function deleteAnArticle($id)
    {
        $prod = DAO::getOne(Basketdetail::class,'idBasket = ? and idProduct = ?',false, [$this->idBasket, $id]);
        if(DAO::remove($prod)) {
            return "element deleted";
        }else{
            return -1;
        }
    }

    public function clearBasket()
    {
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        $basketDetails = $baskets->getBasketdetails();
        if($basketDetails) {
            foreach ($basketDetails as $b){
                DAO::remove($b->getProduct());
            }
            return "all clear";
        }else{
            return -1;
        }
    }

    public function finalPrice($a, $b){
        $c = $a*$b;
        return $c;
    }

    private function jslog($messageLog){
        echo "<script> console.log('$messageLog ')</script>";
    }

}
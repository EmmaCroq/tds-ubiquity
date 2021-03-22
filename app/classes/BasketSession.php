<?php


namespace classes;


abstract class BasketSession
{
    abstract function addProduct($id,$price,$quantity);
    abstract function getMontant();
    abstract function getQuantity();
}
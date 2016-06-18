<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Orders;

class RestaurantController extends Controller
{
    public function tablesAction(Request $request, $current_table)
    {
        
        $finish = $request->request->get('finish');
        $choosen_dish = $request->request->get('order');
        
        if($finish)
        {
            $em = $this->getDoctrine()->getManager();
            $em->getRepository("AppBundle\Entity\Orders")->delete_orders($current_table);
                
        } 
        else if($choosen_dish)
        {

            $order = $this->getDoctrine()
            ->getRepository('AppBundle:Orders')
            ->findOneBy(["table"=>$current_table, "dish"=>$choosen_dish]);

            if($order){
                //The order already exists so, we have to increase the quantity
                $new_quantity = $order->getQuantity();
                $new_quantity++;
                $order->setQuantity($new_quantity);
            }
            else {
                //There are no current orders for that dish so, we create one                
                $order = $this->create_order($current_table,$choosen_dish);
            }

            $em = $this->getDoctrine()->getManager();    
            $em->persist($order);
            $em->flush();
        }
        
        
        $tables = $this->getDoctrine()
        ->getRepository('AppBundle:Tables')
        ->findAll();

        $dishes = $this->getDoctrine()
        ->getRepository('AppBundle:Dishes')
        ->findAll();

        $orders = $this->getDoctrine()
        ->getRepository('AppBundle:Orders')
        ->findBy(["table"=>$current_table]);
        
        
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'tables' => $tables,
            'dishes' => $dishes,
            'orders' => $orders,
            'current_table' => $current_table
        ));
    }
    
    
    function create_order($current_table,$choosen_dish)
    {
        $table = $this->getDoctrine()
        ->getRepository('AppBundle:Tables')
        ->find($current_table);

        $dish = $this->getDoctrine()
        ->getRepository('AppBundle:Dishes')
        ->find($choosen_dish);

        $order = new Orders();
        $order->setQuantity(1);
        $order->setTable($table);
        $order->setDish($dish);
        
        return $order;
    }
}

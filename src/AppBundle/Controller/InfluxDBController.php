<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class InfluxDBController extends Controller
{
    /**
     * @Route("/list")
     * @Template()
     */
    public function listAction()
    {
        $client = $this->container->get("influxdb");

        $data = $client->query("SELECT * FROM /.*/ LIMIT 20");

        return array(
            "series" => $data["results"][0]["series"],
        );
    }
}

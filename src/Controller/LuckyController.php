<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\PathPackage;

class LuckyController extends AbstractController
{
    public function number()
    {
        $number = random_int(0, 100);
        $pathPack = new PathPackage('/assets', new EmptyVersionStrategy());

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
            'favicon' => $pathPack->getUrl('favicon.ico'),
            'style' => $pathPack->getUrl('css/app.css')
        ]);
    }
    public function favicon() {

    }
}
?>
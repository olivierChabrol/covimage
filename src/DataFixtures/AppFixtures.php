<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Params;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {   
        //PARAMS
        $rootParam = new Params();
        $rootParam->setTypeParam(0);
        $rootParam->setId(1);
        $rootParam->setValue('Root Type');
        $rootParam->setColor('#000000');
        
        $manager->persist($rootParam);
        $manager->flush();

        // Script params types
        $ScriptPathType = new Params();
        $rootParam->setId(2);
        $ScriptPathType->setTypeParam(1);
        $ScriptPathType->setValue('Script Path');
        $ScriptPathType->setColor('#00FF00');
        
        $manager->persist($ScriptPathType);
        $manager->flush();

        $ScriptOutputType = new Params();
        $rootParam->setId(3);
        $ScriptOutputType->setTypeParam(1);
        $ScriptOutputType->setValue('Script Output');
        $ScriptOutputType->setColor('#00FF00');
        
        $manager->persist($ScriptOutputType);
        $manager->flush();
        
        //Default Script Params
        $ScriptPath = new Params();
        $ScriptPath->setTypeParam(2);
        $ScriptPath->setValue('./analyse.sh');
        $ScriptPath->setColor('#00FF00');
        $ScriptPath->setLabel('COVID');
        
        $manager->persist($ScriptPath);
        $manager->flush();

        $ScriptPath = new Params();
        $ScriptPath->setTypeParam(2);
        $ScriptPath->setValue('./analyseHearth.sh');
        $ScriptPath->setColor('#00FF00');
        $ScriptPath->setLabel('HEART');
        
        $manager->persist($ScriptPath);
        $manager->flush();

        $ScriptOutput = new Params();
        $ScriptOutput->setTypeParam(3);
        $ScriptOutput->setValue('images/results/');
        $ScriptOutput->setColor('#00FF00');
        
        $manager->persist($ScriptOutput);
        $manager->flush();
    }
}

<?php
namespace AppBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\DBAL\Types\Type;
class AppBundle extends Bundle
{
    public function boot()
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        //Type::addType('datekey', 'AppBundle\MyClass\DateKeyType');
        //$platform = $entityManager->getConnection()->getDatabasePlatform();
        //$platform->registerDoctrineTypeMapping('datekey', 'datekey');
        //$platform->markDoctrineTypeCommented(\Doctrine\DBAL\Types\Type::getType('datekey'));
    }
}

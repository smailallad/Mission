<?php
namespace AppBundle\MyClass;
use Doctrine\DBAL\Platforms\AbstractPlatform;
class DateKeyType extends \Doctrine\DBAL\Types\DateType
{
    public function convertToPHPValue($value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform) {
        $value = parent::convertToPHPValue($value, $platform);
        if ($value !== NULL) {
            $value = DateKey::fromDateTime($value);
        }
        return $value;
    }
    public function getName()
    {
        return 'DateKey';
    }
}
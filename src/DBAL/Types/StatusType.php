<?php
// src/DBAL/Types/StatusType.php
namespace App\DBAL\Types;

use App\Enum\Status;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

class StatusType extends Type
{
    public const STATUS = 'status';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getDoctrineTypeMapping('string');
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return Status::from($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if (!$value instanceof Status) {
            throw new InvalidArgumentException('Invalid status');
        }

        return $value->value;
    }

    public function getName(): string
    {
        return self::STATUS;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
?>

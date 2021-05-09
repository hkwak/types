<?php

namespace HKwak\Types\Dates;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use HKwak\Types\Interfaces\ComparableInterface;
use InvalidArgumentException;

/**
 * Class DateTime
 *
 * Represents the Date ( as php does not provide such )
 */
class DateTime implements ComparableInterface
{
    const SHORT_FORMAT = 'd/m/Y';
    const LONG_FORMAT = 'dS \of F Y';

    /**
     * @var null|DateTimeImmutable
     */
    private $datetime;

    /**
     * Date constructor.
     *
     * @param DateTimeInterface|null $datetime
     *
     * @throws Exception
     */
    public function __construct(DateTimeInterface $datetime = null)
    {
        $this->datetime = new DateTimeImmutable($datetime !== null ? $datetime->format('Y-m-d H:i:s') : null);
    }

    public function getDateTime(): DateTimeInterface
    {
        return clone $this->datetime;
    }

    /**
     * @param null|string|DateTimeInterface|self $value
     *
     * @param string|null $format
     *
     * @return static|null
     * @throws InvalidArgumentException
     */
    public static function parse($value, string $format = null)
    {
        if ($value instanceof self) {
            return clone $value;
        } elseif ($value instanceof DateTimeInterface) {
            return new static($value);
        } elseif (is_string($value)) {
            if ($format) {
                // if format is specified, trying to parse the date in provided format
                $date = date_create_from_format($format, $value);
            } else {
                $date = date_create_from_format('Y-m-d H:i:s', $value);
                if ($date === false) {
                    $date = date_create_from_format('d/m/Y H:i:s', $value);
                }
            }

            if ($date === false) {
                throw new InvalidArgumentException('Parameter $value does not represent the valid date');
            }

            return new static($date);
        } elseif ($value === null) {
            return null;
        } else {
            throw new InvalidArgumentException('Parameter $value should be null, string, DateTimeInterface or Date object');
        }
    }

    public function getDay(): int
    {
        return $this->datetime->format('d');
    }

    public function getMonth(): int
    {
        return $this->datetime->format('m');
    }

    public function getYear(): int
    {
        return $this->datetime->format('Y');
    }

    public function getHour(): int
    {
        return $this->datetime->format('H');
    }

    public function getMinute(): int
    {
        return $this->datetime->format('i');
    }

    public function getSecond(): int
    {
        return $this->datetime->format('s');
    }

    public function setDay(int $day)
    {
        if ($day < 1 || $day > 31) {
            throw new InvalidArgumentException('Parameter $day should be between 1 and 31');
        }
        $this->datetime = $this->datetime->setDate($this->getYear(), $this->getMonth(), $day);
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function format(string $format): string
    {
        return $this->datetime->format($format);
    }

    /**
     * @param DateInterval $interval
     *
     * @return DateTime
     */
    public function add(DateInterval $interval): DateTime
    {
        $new = $this->datetime->add($interval);

        return new self($new);
    }

    /**
     * @param DateInterval $interval
     *
     * @return DateTime
     */
    public function sub(DateInterval $interval): DateTime
    {
        $new = $this->datetime->sub($interval);

        return new self($new);
    }

    /**
     * @param DateTimeInterface $datetime2
     * @param bool $absolute
     *
     * @return DateInterval
     */
    public function diff(DateTimeInterface $datetime2, $absolute = false): DateInterval
    {
        if ($datetime2 instanceof DateTimeInterface) {
            $datetime2 = new static($datetime2);
        } elseif (!($datetime2 instanceof static)) {
            $datetime2 = static::parse($datetime2);
        }

        return $this->datetime->diff($datetime2->getDateTime(), $absolute);
    }

    public function toIsoString(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toIsoString();
    }

    /**
     * The method compares the object to another object. The method should return -1 if object is lower that the $object parameter
     * 0 if they are the same and 1 if the object is higher than the $object parameter
     *
     * @param Date $object
     *
     * @return int
     */
    public function compareTo($object): int
    {
        return $this->toIsoString() <=> $object->toIsoString();
    }
}

<?php

namespace HKwak\Types\Dates;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use HKwak\Types\Collections\StringCollection;
use HKwak\Types\Interfaces\ComparableInterface;
use InvalidArgumentException;

/**
 * Class Date
 *
 * Represents the Date ( as php does not provide such )
 */
class Date implements ComparableInterface
{
    const SHORT_FORMAT = 'd/m/Y';
    const LONG_FORMAT = 'dS \of F Y';

    const WEEK_DAYS_SHORT = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const WEEK_DAYS_LONG = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const MONTHS_LONG = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const MONTH_LENGTHS = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

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
        $this->datetime = new DateTimeImmutable($datetime !== null ? $datetime->format('Y-m-d') : 'today');
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
                $date = date_create_from_format('Y-m-d', $value);
                if ($date === false) {
                    $date = date_create_from_format('d/m/Y', $value);
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

    /**
     * Checking if the date is in a weekend
     * @return bool
     */
    public function isWeekend(): bool
    {
        return $this->getWeekDay() >= 5;
    }

    /**
     * Getting the weekday number 0 - Monday, etc.
     * @return int
     */
    public function getWeekDay(): int
    {
        $weekDay = $this->datetime->format('w') - 1;
        if ($weekDay < 0) {
            $weekDay = 6;
        }

        return $weekDay;
    }

    /**
     * Returns the date of the month, starting from 0
     *
     * @return int
     */
    public function getDay(): int
    {
        return $this->datetime->format('d');
    }

    /**
     * Getting the number of the date. The number starts with 1 for January
     *
     * @return int 1-12 value
     */
    public function getMonth(): int
    {
        return $this->datetime->format('m');
    }

    public function getYear(): int
    {
        return $this->datetime->format('Y');
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
     * @return Date
     */
    public function add(DateInterval $interval): Date
    {
        $interval->h = $interval->i = $interval->s = 0; // keep this as a date
        $new = $this->datetime->add($interval);

        return new self($new);
    }

    public function addDays(int $days): Date
    {
        return $this->add(new DateInterval('P'.$days.'D'));
    }

    public function subDays(int $days): Date
    {
        return $this->sub(new DateInterval('P'.$days.'D'));
    }

    public function addMonths(int $months): Date
    {
        return $this->add(new DateInterval('P'.$months.'M'));
    }

    public function subMonths(int $months): Date
    {
        return $this->sub(new DateInterval('P'.$months.'M'));
    }

    /**
     * @param DateInterval $interval
     *
     * @return Date
     */
    public function sub(DateInterval $interval): Date
    {
        $interval->h = $interval->i = $interval->s = 0; // keep this as a date
        $new = $this->datetime->sub($interval);

        return new self($new);
    }

    /**
     * @param Date $date2
     * @param bool $absolute
     *
     * @return DateInterval
     */
    public function diff(Date $date2, $absolute = false): DateInterval
    {
        $date1 = $this->getDateTime();

        return $date1->diff($date2->getDateTime(), $absolute);
    }

    public function toIsoString(): string
    {
        return $this->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toIsoString();
    }

    public function getTimestamp(): int
    {
        return $this->datetime->getTimestamp();
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

    /**
     * Getting weekdays
     *
     * @param bool|null $long
     *
     * @return StringCollection
     */
    public static function getWeekDays(bool $long = null): StringCollection
    {
        return new StringCollection($long ? self::WEEK_DAYS_LONG : self::WEEK_DAYS_SHORT);
    }

    public static function getMonthsNames(bool $long = null): StringCollection
    {
        return new StringCollection($long ? self::MONTHS_SHORT : self::MONTHS_LONG);
    }

    public static function getMonthName(int $monthNumber, bool $long = null): string
    {
        if ($monthNumber < 1 || $monthNumber > 12) {
            throw new InvalidArgumentException('$monthNumber must be between 1 and 12. '.$monthNumber.' provided');
        }
        $monthNumber--;


        return $long ? self::MONTHS_LONG[$monthNumber] : self::MONTHS_SHORT[$monthNumber];
    }

    /**
     *
     * @param int $monthNumber Month number starting from 1
     * @param int $year
     *
     * @return int|mixed
     */
    public static function getMonthLength(int $monthNumber, int $year)
    {
        if ($monthNumber < 1 || $monthNumber > 12) {
            throw new InvalidArgumentException('$monthNumber must be between 1 and 12. '.$monthNumber.' provided');
        }

        $monthNumber--;
        $length = self::MONTH_LENGTHS[$monthNumber];
        if ($monthNumber === 1) {
            if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
                $length = 29;
            }
        }

        return $length;
    }

    /**
     * Converting Mysql Date to human
     *
     * @param string $mysqlDate
     *
     * @return string
     */
    public static function mysql2human(string $mysqlDate): string
    {
        $bits = explode('-', $mysqlDate);

        return $bits[2].'/'.$bits[1].'/'.$bits[0];
    }
}

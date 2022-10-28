<?php

namespace App\Helpers;

use App\Models\Currency as ModelsCurrency;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use JsonSerializable;
use OutOfBoundsException;

/**
 * Class Currency.
 */
class Currency implements Arrayable, Jsonable, JsonSerializable, Renderable
{
    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var float
     */
    protected $rate;

    /**
     * @var int
     */
    protected $precision;

    /**
     * @var int
     */
    protected $subunit;

    /**
     * @var string
     */
    protected $symbol;

    /**
     * @var bool
     */
    protected $symbolFirst;

    /**
     * @var string
     */
    protected $decimalMark;

    /**
     * @var string
     */
    protected $thousandsSeparator;

    /**
     * @var array
     */
    protected static $currencies;

    /**
     * Create a new instance.
     *
     * @param string $currency
     *
     * @throws \OutOfBoundsException
     */
    public function __construct($currency)
    {
        $currency = strtoupper(trim($currency));
        $currencies = static::getCurrencies();

        if (!array_key_exists($currency, $currencies)) {
            throw new OutOfBoundsException('Invalid currency "' . $currency . '"');
        }

        $attributes = $currencies[$currency];
        $this->currency = $currency;
        $this->name = (string) $attributes['name'];
        $this->code = (int) $attributes['code'];
        $this->rate = (float) isset($attributes['rate']) ? $attributes['rate'] : 1;
        $this->precision = (int) $attributes['precision'];
        $this->subunit = (int) $attributes['subunit'];
        $this->symbol = (string) $attributes['symbol'];
        $this->symbolFirst = (bool) $attributes['symbol_first'];
        $this->decimalMark = (string) $attributes['decimal_mark'];
        $this->thousandsSeparator = (string) $attributes['thousands_separator'];
    }

    /**
     * __callStatic.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return \Akaunting\Money\Currency
     */
    public static function __callStatic($method, array $arguments)
    {
        return new static($method, $arguments);
    }

    /**
     * setCurrencies.
     *
     * @param array $currencies
     */
    public static function setCurrencies(array $currencies)
    {
        static::$currencies = $currencies;
    }

    /**
     * getCurrencies.
     *
     * @return array
     */
    public static function getCurrencies()
    {
        if (!isset(static::$currencies)) {
            static::$currencies = ModelsCurrency::all()->keyBy('short_code')->toArray();
        }

        return (array) static::$currencies;
    }

    /**
     * equals.
     *
     * @param \Akaunting\Money\Currency $currency
     *
     * @return bool
     */
    public function equals(self $currency)
    {
        return $this->getCurrency() === $currency->getCurrency();
    }

    /**
     * getCurrency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * getName.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getCode.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * getRate.
     *
     * @return int
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * getPrecision.
     *
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * getSubunit.
     *
     * @return int
     */
    public function getSubunit()
    {
        return $this->subunit;
    }

    /**
     * getSymbol.
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * isSymbolFirst.
     *
     * @return bool
     */
    public function isSymbolFirst()
    {
        return $this->symbolFirst;
    }

    /**
     * getDecimalMark.
     *
     * @return string
     */
    public function getDecimalMark()
    {
        return $this->decimalMark;
    }

    /**
     * getThousandsSeparator.
     *
     * @return string
     */
    public function getThousandsSeparator()
    {
        return $this->thousandsSeparator;
    }

    /**
     * getPrefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        if (!$this->symbolFirst) {
            return '';
        }

        return $this->symbol;
    }

    /**
     * getSuffix.
     *
     * @return string
     */
    public function getSuffix()
    {
        if ($this->symbolFirst) {
            return '';
        }

        return ' ' . $this->symbol;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [$this->currency => [
            'name' => $this->name,
            'code' => $this->code,
            'rate' => $this->rate,
            'precision' => $this->precision,
            'subunit' => $this->subunit,
            'symbol' => $this->symbol,
            'symbol_first' => $this->symbolFirst,
            'decimal_mark' => $this->decimalMark,
            'thousands_separator' => $this->thousandsSeparator,
            'prefix' => $this->getPrefix(),
            'suffix' => $this->getSuffix(),
        ]];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * jsonSerialize.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        return $this->currency . ' (' . $this->name . ')';
    }

    /**
     * __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}

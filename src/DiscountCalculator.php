<?php

namespace App;

class DiscountCalculator
{
    /**
     * Рассчитывает цену со скидкой в процентах
     *
     * @param float $price Исходная цена
     * @param float $discountPercent Процент скидки (0-100)
     * @return float Цена со скидкой
     * @throws \InvalidArgumentException Если неверные аргументы
     */
    public function calculate(float $price, float $discountPercent): float
    {
        if ($price < 0) {
            throw new \InvalidArgumentException("Цена не может быть отрицательной");
        }
        
        if ($discountPercent < 0 || $discountPercent > 100) {
            throw new \InvalidArgumentException("Скидка должна иметь значение от 0 до 100");
        }
        
        $discountAmount = $price * ($discountPercent / 100);
        return $price - $discountAmount;
    }

    /**
     * Применяет купон к цене
     *
     * @param float $price Исходная цена
     * @param string $couponCode Код купона
     * @return float Цена с учетом купона
     * @throws \InvalidArgumentException Если неверные аргументы или неизвестный купон
     */
    public function applyCoupon(float $price, string $couponCode): float
    {
        if ($price < 0) {
            throw new \InvalidArgumentException("Цена не может быть отрицательной");
        }
        
        $couponRules = [
            'SUMMER10' => 10,    
            'WINTER15' => 15,   
            'SPRING20' => 20,    
            'BLACKFRIDAY' => 30, 
            'NEWYEAR' => 25,  
        ];
        
        if (!array_key_exists($couponCode, $couponRules)) {
            throw new \InvalidArgumentException("Unknown coupon code: $couponCode");
        }
        
        return $this->calculate($price, $couponRules[$couponCode]);
    }
}
<?php
// app/Rules/IsbnRule.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsbnRule implements Rule
{
    public function passes($attribute, $value)
    {
        $value = str_replace(['-', ' '], '', $value);

        // Check if the ISBN is either 10 or 13 digits
        if (!(strlen($value) === 10 || strlen($value) === 13)) {
            return false;
        }

        // Validate ISBN-10 using the Luhn algorithm
        if (strlen($value) === 10) {
            $sum = 0;

            for ($i = 0; $i < 9; $i++) {
                $sum += (10 - $i) * intval($value[$i]);
            }

            $checksum = (11 - ($sum % 11)) % 11;

            if ($checksum === 10) {
                $checksum = 'X';
            }

            return $checksum == $value[9];
        }

        // Validate ISBN-13 using the Luhn algorithm
        if (strlen($value) === 13) {
            $sum = 0;

            for ($i = 0; $i < 12; $i++) {
                $factor = ($i % 2 == 0) ? 1 : 3;
                $sum += $factor * intval($value[$i]);
            }

            $checksum = (10 - ($sum % 10)) % 10;

            return $checksum == $value[12];
        }

        return false;
    }

    public function message()
    {
        return 'The :attribute must be a valid ISBN.';
    }
}

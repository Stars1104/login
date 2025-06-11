<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidAreaPhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // This is a concrete example implementation for phone number validation.
        // You MUST adjust this regex and logic to match your specific valid area codes and phone number formats.
        // For example, this regex checks for numbers starting with '+1' followed by exactly 10 digits.
        // It also disallows '0000000000' explicitly.

        $cleanedPhoneNumber = preg_replace('/[^0-9+]', '', $value);

        // Regex for +1 followed by 10 digits (e.g., +12223334444)
        // You'll need to customize this regex for your specific country codes and phone number patterns.
        $regex = '/^\\+1[0-9]{10}$/';

        if (!preg_match($regex, $cleanedPhoneNumber)) {
            $fail('The :attribute is not a valid phone number format for this region (e.g., must start with +1 and be 10 digits).');
            return;
        }

        // Additional check to disallow all zeros or other common invalid patterns
        if ($cleanedPhoneNumber === '+10000000000') { // Adjust based on your regex
            $fail('The :attribute is not a valid phone number.');
            return;
        }

        // Add more specific area code checks here if needed.
        // For example, to check if the area code (e.g., first 3 digits after +1) is in a specific list.
        // $areaCode = substr($cleanedPhoneNumber, 2, 3); // Gets '222' from '+12223334444'
        // $validAreaCodes = ['201', '202', '212', '303', /* add your valid codes */ ];
        // if (!in_array($areaCode, $validAreaCodes)) {
        //     $fail('The :attribute does not belong to a valid area code.');
        // }
    }
}

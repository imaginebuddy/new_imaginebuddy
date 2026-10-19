<?php

namespace App\Services;

use App\Models\AdminSettings;
use App\Models\User;
use App\Helper;

class TaxService
{
    /**
     * Complete mapping of Indian States and Union Territories with 2-digit GST state codes.
     */
    public static array $indianStates = [
        '01' => 'Jammu and Kashmir',
        '02' => 'Himachal Pradesh',
        '03' => 'Punjab',
        '04' => 'Chandigarh',
        '05' => 'Uttarakhand',
        '06' => 'Haryana',
        '07' => 'Delhi',
        '08' => 'Rajasthan',
        '09' => 'Uttar Pradesh',
        '10' => 'Bihar',
        '11' => 'Sikkim',
        '12' => 'Arunachal Pradesh',
        '13' => 'Nagaland',
        '14' => 'Manipur',
        '15' => 'Mizoram',
        '16' => 'Tripura',
        '17' => 'Meghalaya',
        '18' => 'Assam',
        '19' => 'West Bengal',
        '20' => 'Jharkhand',
        '21' => 'Odisha',
        '22' => 'Chhattisgarh',
        '23' => 'Madhya Pradesh',
        '24' => 'Gujarat',
        '26' => 'Dadra and Nagar Haveli and Daman and Diu',
        '27' => 'Maharashtra',
        '29' => 'Karnataka',
        '30' => 'Goa',
        '31' => 'Lakshadweep',
        '32' => 'Kerala',
        '33' => 'Tamil Nadu',
        '34' => 'Puducherry',
        '35' => 'Andaman and Nicobar Islands',
        '36' => 'Telangana',
        '37' => 'Andhra Pradesh',
        '38' => 'Ladakh',
        '97' => 'Other Territory',
    ];

    /**
     * Get all Indian states for dropdowns.
     */
    public static function getIndianStates(): array
    {
        return self::$indianStates;
    }

    /**
     * Extract 2-digit state code from GSTIN.
     */
    public static function getStateCodeFromGstin(?string $gstin): ?string
    {
        if (empty($gstin)) {
            return null;
        }

        $cleanGstin = strtoupper(trim($gstin));
        if (preg_match('/^[0-9]{2}/', $cleanGstin, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Resolve state code from state name or code.
     */
    public static function resolveStateCode(?string $state): ?string
    {
        if (empty($state)) {
            return null;
        }

        $trimmed = trim($state);

        // If it's already a 2-digit code
        if (isset(self::$indianStates[$trimmed])) {
            return $trimmed;
        }

        // Search by state name (case-insensitive)
        foreach (self::$indianStates as $code => $name) {
            if (strcasecmp($name, $trimmed) === 0) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Resolve state name from code or name.
     */
    public static function resolveStateName(?string $state): ?string
    {
        if (empty($state)) {
            return null;
        }

        $code = self::resolveStateCode($state);
        if ($code && isset(self::$indianStates[$code])) {
            return self::$indianStates[$code];
        }

        return $state;
    }

    /**
     * Validate GSTIN format (15 characters: 2 digits state code, 10 char PAN, 1 entity no, 1 'Z', 1 checksum).
     */
    public static function isValidGstin(?string $gstin): bool
    {
        if (empty($gstin)) {
            return false;
        }

        return (bool) preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i', trim($gstin));
    }

    /**
     * Calculate GST breakdown and generate full invoice snapshot data.
     *
     * @param float $amount Total gross amount charged
     * @param User|null $customer The user receiving the invoice
     * @param string|null $currency Currency code ('INR', 'USD', etc.)
     * @param bool $isPriceTaxInclusive Whether $amount already includes GST
     * @param string|null $customerState Optional explicit customer state
     * @return array Calculated tax components and metadata
     */
    public static function calculateGst(
        float $amount,
        ?User $customer = null,
        ?string $currency = null,
        bool $isPriceTaxInclusive = true,
        ?string $customerState = null
    ): array {
        $settings = AdminSettings::first();
        $currency = strtoupper($currency ?: ($customer && $customer->country() && $customer->country()->country_code === 'IN' ? 'INR' : (Helper::isIndia() ? 'INR' : 'USD')));

        $businessGstin = $settings ? trim($settings->gstin ?? '') : '';
        $configuredRate = $settings && $settings->gst_percentage !== null ? (float)$settings->gst_percentage : 0.00;

        // Base fallback response when business is unregistered or GST is not applicable
        $defaultResult = [
            'taxable_amount'   => round($amount, 2),
            'gst_rate'         => 0.00,
            'cgst_rate'        => 0.00,
            'cgst_amount'      => 0.00,
            'sgst_rate'        => 0.00,
            'sgst_amount'      => 0.00,
            'igst_rate'        => 0.00,
            'igst_amount'      => 0.00,
            'total_tax_amount' => 0.00,
            'total_amount'     => round($amount, 2),
            'tax_type'         => 'unregistered',
            'tax_details'      => [
                'business_gstin' => $businessGstin ?: null,
                'sac_code'       => '998439',
                'currency'       => $currency,
            ]
        ];

        // 1. If Business GSTIN is not configured or GST % is 0, no GST is charged
        if (empty($businessGstin) || $configuredRate <= 0) {
            return $defaultResult;
        }

        // 2. Determine if customer is International (Export of Services)
        $isDomestic = false;
        if ($currency === 'INR') {
            $isDomestic = true;
        } elseif ($customer && $customer->country() && $customer->country()->country_code === 'IN') {
            $isDomestic = true;
        }

        if (! $isDomestic) {
            // QUALIFYING ZERO-RATED EXPORT OF SERVICES UNDER SECTION 16 OF IGST ACT
            return [
                'taxable_amount'   => round($amount, 2),
                'gst_rate'         => 0.00,
                'cgst_rate'        => 0.00,
                'cgst_amount'      => 0.00,
                'sgst_rate'        => 0.00,
                'sgst_amount'      => 0.00,
                'igst_rate'        => 0.00,
                'igst_amount'      => 0.00,
                'total_tax_amount' => 0.00,
                'total_amount'     => round($amount, 2),
                'tax_type'         => 'export_zero_rated',
                'tax_details'      => [
                    'business_gstin'    => $businessGstin,
                    'lut_number'        => $settings->lut_number ?: null,
                    'export_note'       => 'Supply meant for export under Letter of Undertaking (LUT) without payment of integrated tax as per Section 16 of the IGST Act, 2017.',
                    'sac_code'          => '998439',
                    'currency'          => $currency,
                    'customer_country'  => $customer && $customer->country() ? $customer->country()->country_name : 'International',
                ]
            ];
        }

        // 3. Domestic Indian Customer: Determine Supplier State and Customer State
        $supplierStateCode = self::getStateCodeFromGstin($businessGstin);
        if (!$supplierStateCode && !empty($settings->supplier_state)) {
            $supplierStateCode = self::resolveStateCode($settings->supplier_state);
        }
        // Default supplier state if still unresolved
        $supplierStateCode = $supplierStateCode ?: '27'; // Default Maharashtra if not detected
        $supplierStateName = self::resolveStateName($supplierStateCode);

        // Resolve customer state
        $resolvedCustState = $customerState ?: ($customer ? ($customer->state ?: null) : null);
        $customerStateCode = self::resolveStateCode($resolvedCustState);

        // Under Section 12(2)(b) of IGST Act: If customer address/state is not on record, Place of Supply defaults to supplier location
        $placeOfSupplyStateCode = $customerStateCode ?: $supplierStateCode;
        $placeOfSupplyStateName = self::resolveStateName($placeOfSupplyStateCode);

        // 4. Calculate Tax-Inclusive or Tax-Exclusive Values
        if ($isPriceTaxInclusive) {
            $taxableAmount = round($amount / (1 + ($configuredRate / 100)), 2);
            $totalTaxAmount = round($amount - $taxableAmount, 2);
            $totalAmount = round($amount, 2);
        } else {
            $taxableAmount = round($amount, 2);
            $totalTaxAmount = round($taxableAmount * ($configuredRate / 100), 2);
            $totalAmount = round($taxableAmount + $totalTaxAmount, 2);
        }

        // 5. Intra-State vs Inter-State Supply
        $isIntraState = ($supplierStateCode === $placeOfSupplyStateCode);

        if ($isIntraState) {
            // CGST + SGST (split equally)
            $cgstRate = round($configuredRate / 2, 2);
            $sgstRate = round($configuredRate / 2, 2);

            $cgstAmount = round($totalTaxAmount / 2, 2);
            // Ensure sum of CGST and SGST matches totalTaxAmount exactly without 1-paisa discrepancy
            $sgstAmount = round($totalTaxAmount - $cgstAmount, 2);

            return [
                'taxable_amount'   => $taxableAmount,
                'gst_rate'         => $configuredRate,
                'cgst_rate'        => $cgstRate,
                'cgst_amount'      => $cgstAmount,
                'sgst_rate'        => $sgstRate,
                'sgst_amount'      => $sgstAmount,
                'igst_rate'        => 0.00,
                'igst_amount'      => 0.00,
                'total_tax_amount' => $totalTaxAmount,
                'total_amount'     => $totalAmount,
                'tax_type'         => 'cgst_sgst',
                'tax_details'      => [
                    'business_gstin'      => $businessGstin,
                    'supplier_state'      => $supplierStateName,
                    'supplier_state_code' => $supplierStateCode,
                    'customer_state'      => $placeOfSupplyStateName,
                    'customer_state_code' => $placeOfSupplyStateCode,
                    'place_of_supply'     => $placeOfSupplyStateName . ' (' . $placeOfSupplyStateCode . ')',
                    'sac_code'            => '998439',
                    'pricing_type'        => $isPriceTaxInclusive ? 'inclusive' : 'exclusive',
                    'currency'            => 'INR',
                ]
            ];
        } else {
            // Inter-State: IGST
            return [
                'taxable_amount'   => $taxableAmount,
                'gst_rate'         => $configuredRate,
                'cgst_rate'        => 0.00,
                'cgst_amount'      => 0.00,
                'sgst_rate'        => 0.00,
                'sgst_amount'      => 0.00,
                'igst_rate'        => $configuredRate,
                'igst_amount'      => $totalTaxAmount,
                'total_tax_amount' => $totalTaxAmount,
                'total_amount'     => $totalAmount,
                'tax_type'         => 'igst',
                'tax_details'      => [
                    'business_gstin'      => $businessGstin,
                    'supplier_state'      => $supplierStateName,
                    'supplier_state_code' => $supplierStateCode,
                    'customer_state'      => $placeOfSupplyStateName,
                    'customer_state_code' => $placeOfSupplyStateCode,
                    'place_of_supply'     => $placeOfSupplyStateName . ' (' . $placeOfSupplyStateCode . ')',
                    'sac_code'            => '998439',
                    'pricing_type'        => $isPriceTaxInclusive ? 'inclusive' : 'exclusive',
                    'currency'            => 'INR',
                ]
            ];
        }
    }

    /**
     * Convert an amount into words (e.g. "Two Hundred Eighty-Four Rupees Only").
     */
    public static function numberToWords(float $amount, string $currency = 'INR'): string
    {
        $amount = round($amount, 2);
        $whole = (int) floor($amount);
        $fraction = (int) round(($amount - $whole) * 100);

        $words = self::convertWholeNumberToWords($whole);

        $currency = strtoupper($currency);
        if ($currency === 'INR') {
            $currencyName = ($whole === 1) ? 'Rupee' : 'Rupees';
            $subunitName = ($fraction === 1) ? 'Paisa' : 'Paise';
        } else {
            $currencyName = ($whole === 1) ? 'Dollar' : 'Dollars';
            $subunitName = ($fraction === 1) ? 'Cent' : 'Cents';
        }

        $result = $words . ' ' . $currencyName;

        if ($fraction > 0) {
            $fractionWords = self::convertWholeNumberToWords($fraction);
            $result .= ' and ' . $fractionWords . ' ' . $subunitName;
        }

        return $result . ' Only';
    }

    /**
     * Helper to convert integer numbers to words.
     */
    protected static function convertWholeNumberToWords(int $num): string
    {
        if ($num === 0) {
            return 'Zero';
        }

        $ones = [
            1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen'
        ];

        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
        ];

        $words = '';

        if ($num >= 10000000) { // Crore
            $crores = (int) floor($num / 10000000);
            $words .= self::convertWholeNumberToWords($crores) . ' Crore ';
            $num %= 10000000;
        }

        if ($num >= 100000) { // Lakh
            $lakhs = (int) floor($num / 100000);
            $words .= self::convertWholeNumberToWords($lakhs) . ' Lakh ';
            $num %= 100000;
        }

        if ($num >= 1000) { // Thousand
            $thousands = (int) floor($num / 1000);
            $words .= self::convertWholeNumberToWords($thousands) . ' Thousand ';
            $num %= 1000;
        }

        if ($num >= 100) { // Hundred
            $hundreds = (int) floor($num / 100);
            $words .= self::convertWholeNumberToWords($hundreds) . ' Hundred ';
            $num %= 100;
        }

        if ($num > 0) {
            if ($num < 20) {
                $words .= $ones[$num] . ' ';
            } else {
                $words .= $tens[(int) floor($num / 10)] . ' ';
                if ($num % 10 > 0) {
                    $words .= $ones[$num % 10] . ' ';
                }
            }
        }

        return trim($words);
    }
}

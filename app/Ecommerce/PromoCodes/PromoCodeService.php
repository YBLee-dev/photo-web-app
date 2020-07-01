<?php

namespace App\Ecommerce\PromoCodes;

use Carbon\Carbon;

class PromoCodeService
{
    /**
     * Find all expired promo codes and update their statuses
     *
     * @param array $promo_codes
     * @throws \Exception
     */
    public function checkAndUpdateStatuses()
    {
        $repo = new PromoCodeRepo();
        $expired_promo_codes = $repo->getExpiredCodes();
        foreach ($expired_promo_codes as $promo_code) {
            $repo->update($promo_code->id, [
                'status' => PromoCodeStatusTypesEnum::EXPIRED(),
            ]);
        }

        $active_promo_codes = $repo->getActiveCodes();
        foreach ($active_promo_codes as $promo_code) {
            $repo->update($promo_code->id, [
                'status' => PromoCodeStatusTypesEnum::ACTIVE(),
            ]);
        }
    }

    /**
     * Check if promo valid by dates, used statuses and cart value
     *
     * @param string $redeem_code
     * @param string $cart_total
     * @param string $email
     * @return bool
     */
    public function getIfValid(string $redeem_code, string $cart_total, string $email = null)
    {
        $code = PromoCode::where('redeem_code', $redeem_code)
            ->where('cart_total_to', '>=', $cart_total)
            ->where('cart_total_from', '<=',$cart_total)
            ->first();

        if (!$code) {
            return false;
        }

        if($code->expires_at && today()->greaterThan(Carbon::parse($code->expires_at))){
            return false;
        }

        if($code->active_from && today()->lessThan( Carbon::parse($code->active_from))){
            return false;
        }

        //Check if one time code valid
        if ($code->isOneTimeCode() && !$code->customers->isEmpty()) {
            return false;
        }

        ////Check if one time per person code valid
        //if ($code->isOneTimeCodePerPerson() && !$code->customers->where('email', $email)->isEmpty()) {
        //    return false;
        //}

        return $code;
    }
}

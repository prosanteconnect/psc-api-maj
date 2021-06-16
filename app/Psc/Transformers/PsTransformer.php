<?php


namespace App\Psc\Transformers;


use JetBrains\PhpStorm\Pure;

/**
 * Class PsTransformer
 * @package App\Psc\Transformers
 */
class PsTransformer extends Transformer {

    protected ProfessionTransformer $professionTransformer;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->professionTransformer = new ProfessionTransformer();
    }

    /**
     * transform Ps into a protected Ps.
     *
     * @param $ps
     * @param null $refId
     * @return mixed
     */
    public function transform($ps, $refId=null): mixed
    {
        $protectedPs = $ps->toArray();
        $protectedPs['nationalId'] = $refId ?? $ps['nationalId'];
        $protectedPs['phone'] = $this->hidePhone(isset($ps['phone']) ? $ps['phone'] : "");
        $protectedPs['email'] = $this->hideEmail(isset($ps['email']) ? $ps['email'] : "");
        if (isset($ps['professions'])) {
            $protectedPs['professions'] = $this->professionTransformer->transformCollection($ps->professions->toArray());
        }
        return $protectedPs;
    }

    /**
     * Obfuscate Email.
     *
     * @param $email
     * @return string
     */
    private function hideEmail($email): string
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            list($first, $last) = explode('@', $email);
            $first = str_replace(substr($first, '3'), str_repeat('*', strlen($first)-3>0 ? strlen($first)-3 : 2), $first);
            $last = explode('.', $last);
            $last_domain = str_replace(substr($last['0'], '1'), str_repeat('*', strlen($last['0'])-1), $last['0']);
            return $first.'@'.$last_domain.'.'.$last['1'];
        }
        return "***********";
    }

    /**
     * Obfuscate phone number.
     *
     * @param $number
     * @return string
     */
    #[Pure]
    private function hidePhone($number): string
    {
        if($number && $number[0]=='+'){
            return substr($number, 0, 4) . str_repeat('*', strlen($number)-6>0 ? strlen($number)-6 : 2) . substr($number, -2);
        }
        return substr($number, 0, 2) . str_repeat('*', strlen($number)-4>0 ? strlen($number)-4 : 2) . substr($number, -2);
    }
}

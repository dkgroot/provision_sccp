<?php
declare(strict_types=1);

namespace PROVISION\Crypto;

class Crypto {

    public static function certificateHashAsBase64(string $certificateFile, int $hashAlg)  : string  {
        return new Certificate($certificateFile)
                .hashAsBase64($hashAlg);
    }
}

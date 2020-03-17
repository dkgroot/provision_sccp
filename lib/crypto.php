<?php
declare(strict_types=1);

namespace SCCP\Crypto;

class HashAlgo // extends Enum 
{
    private const SHA1 = 0;
    private const SHA256 = 1;
}

class Certificate {
    function __construct(string $file) {
		$this->file = $file;
    }
    
    public function hashAsBase64(int $hashAlg)  : string  {
        return "";
    }
}

class Crypto {

    public static function certificateHashAsBase64(string $certificateFile, int $hashAlg)  : string  {
        return new Certificate($certificateFile)
                .hashAsBase64($hashAlg);
    }
}

class Signer {
}

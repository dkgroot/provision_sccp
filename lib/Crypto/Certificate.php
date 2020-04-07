<?php
declare(strict_types=1);

namespace PROVISION\Crypto;

class Certificate {
    function __construct(string $file) {
		$this->file = $file;
    }
    
    public function hashAsBase64(int $hashAlg)  : string  {
        return "";
    }
}
?>

<?php
/**
 * GoogleAuthenticator - Implementación TOTP pura en PHP
 * Compatible con Google Authenticator, Authy y similares
 * No requiere Composer ni librerías externas
 *
 * Basado en RFC 6238 (TOTP) y RFC 4226 (HOTP)
 */
class GoogleAuthenticator
{
    private $codeLength = 6;

    /**
     * Genera un secreto aleatorio codificado en Base32
     */
    public function generateSecret(int $length = 16): string
    {
        $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $validChars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Verifica el código ingresado por el usuario
     * Permite ventana de ±1 período (30 seg) para tolerancia de tiempo
     */
    public function checkCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $time = floor(time() / 30);
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            if ($this->getCode($secret, $time + $i) === $code) {
                return true;
            }
        }
        return false;
    }

    /**
     * Genera el código TOTP para un tiempo dado
     */
    public function getCode(string $secret, ?int $timeSlice = null): string
    {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }

        $secretKey = $this->base32Decode($secret);

        // Pack time as 8 bytes big-endian
        $time = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $timeSlice);

        // HMAC-SHA1
        $hm = hash_hmac('SHA1', $time, $secretKey, true);

        // Offset
        $offset = ord(substr($hm, -1)) & 0x0F;

        // Get 4 bytes from that offset
        $hashPart = substr($hm, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        $modulo = pow(10, $this->codeLength);

        return str_pad($value % $modulo, $this->codeLength, '0', STR_PAD_LEFT);
    }

    /**
     * Genera la URL otpauth para el QR
     */
    public function getQRCodeUrl(string $account, string $secret, string $issuer): string
    {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $account)
            . '?secret=' . $secret
            . '&issuer=' . rawurlencode($issuer)
            . '&algorithm=SHA1&digits=6&period=30';
    }

    /**
     * Decodifica Base32 a binario
     */
    private function base32Decode(string $input): string
    {
        $map = array_flip(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'));
        $input = strtoupper($input);
        $output = '';
        $buffer = 0;
        $bitsLeft = 0;

        foreach (str_split($input) as $char) {
            if (!isset($map[$char])) continue;
            $buffer = ($buffer << 5) | $map[$char];
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $output .= chr(($buffer >> ($bitsLeft - 8)) & 0xFF);
                $bitsLeft -= 8;
            }
        }

        return $output;
    }
}

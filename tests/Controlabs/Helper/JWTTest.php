<?php

namespace Controlabs\Tests\Helper;

use Controlabs\Helper\JWT;
use Firebase\JWT\ExpiredException;
use PHPUnit\Framework\TestCase;

class JWTTest extends TestCase
{
    public function testEncodeAndDecodeMethod()
    {
        $helper = $this->helper();

        $iss = 'issuer_test';
        $aud = 'audit_test';
        $sub = 'subject_test';
        $exp = '+ 1 hour';
        $claims = [
            'param_1' => 'value_1',
            'param_2' => 'value_2'
        ];

        $jwt = $helper->encode(
            $iss,
            $aud,
            $sub,
            $exp,
            $claims
        );

        $expected = [
            'param_1' => 'value_1',
            'param_2' => 'value_2',
            'iss' => 'issuer_test',
            'aud' => 'audit_test',
            'sub' => 'subject_test',
            'iat' => (new \DateTimeImmutable())->getTimestamp(),
            'exp' => (new \DateTimeImmutable())->modify('+ 1 hour')->getTimestamp(),
        ];

        $this->assertSame($expected, $helper->payload($jwt));
        $this->assertSame($expected, $helper->decode($jwt));
    }

    public function testException()
    {
        $this->expectException(ExpiredException::class);
        $this->expectExceptionMessage('Expired token');

        $helper = $this->helper();

        $iss = 'issuer_test';
        $aud = 'audit_test';
        $sub = 'subject_test';
        $exp = '- 1 hour';
        $claims = [
            'param_1' => 'value_1',
            'param_2' => 'value_2'
        ];

        $jwt = $helper->encode(
            $iss,
            $aud,
            $sub,
            $exp,
            $claims
        );

        $expectedPayload = [
            'param_1' => 'value_1',
            'param_2' => 'value_2',
            'iss' => 'issuer_test',
            'aud' => 'audit_test',
            'sub' => 'subject_test',
            'iat' => (new \DateTimeImmutable())->getTimestamp(),
            'exp' => (new \DateTimeImmutable())->modify('- 1 hour')->getTimestamp(),
        ];

        $this->assertSame($expectedPayload, $helper->payload($jwt));
        $helper->decode($jwt);
    }

    public function testIgnoreException()
    {
        $helper = $this->helper();

        $iss = 'issuer_test';
        $aud = 'audit_test';
        $sub = 'subject_test';
        $exp = '- 1 hour';
        $claims = [
            'param_1' => 'value_1',
            'param_2' => 'value_2'
        ];

        $jwt = $helper->encode(
            $iss,
            $aud,
            $sub,
            $exp,
            $claims
        );

        $expectedPayload = [
            'param_1' => 'value_1',
            'param_2' => 'value_2',
            'iss' => 'issuer_test',
            'aud' => 'audit_test',
            'sub' => 'subject_test',
            'iat' => (new \DateTimeImmutable())->getTimestamp(),
            'exp' => (new \DateTimeImmutable())->modify('- 1 hour')->getTimestamp(),
        ];

        $this->assertSame($expectedPayload, $helper->payload($jwt));
        $this->assertNull($helper->decode($jwt, true));
    }

    public function testMalformedToken()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Malformed jwt received.');

        $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ';

        $this->helper()->payload($jwt);
    }

    protected function helper()
    {
        return new JWT(
            file_get_contents(__DIR__ . '/../../private_key.pem'),
            file_get_contents(__DIR__ . '/../../public_key.pem')
        );
    }
}

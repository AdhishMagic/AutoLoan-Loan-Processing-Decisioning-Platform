<?php

namespace Tests\Unit;

use App\Services\DocumentVerification\DocumentTypeDetector;
use App\Services\DocumentVerification\Parsers;
use PHPUnit\Framework\TestCase;

class DocumentVerificationParsersTest extends TestCase
{
    public function test_extracts_pan_number(): void
    {
        $ocr = "INCOME TAX DEPARTMENT\nGOVERNMENT OF INDIA\nPAN: ABCDE1234F";
        $this->assertSame('ABCDE1234F', Parsers::extractPanNumber($ocr));
    }

    public function test_extracts_aadhaar_masked_and_validates_masking(): void
    {
        $ocr = "Government of India\nXXXX XXXX 1234\nUIDAI";
        $aadhaar = Parsers::extractAadhaar($ocr);
        $this->assertSame('XXXX-XXXX-1234', $aadhaar);
        $this->assertTrue(Parsers::isAadhaarMaskingValid($aadhaar));
    }

    public function test_detects_document_types(): void
    {
        $this->assertSame('pan', DocumentTypeDetector::detect("INCOME TAX DEPARTMENT\nABCDE1234F"));
        $this->assertSame('aadhaar', DocumentTypeDetector::detect("UIDAI\n1234 5678 9012\nGOVERNMENT OF INDIA"));
        $this->assertSame('salary_slip', DocumentTypeDetector::detect("PAY SLIP\nEARNINGS\nDEDUCTIONS\nNET PAY"));
        $this->assertSame('bank_statement', DocumentTypeDetector::detect("ACCOUNT STATEMENT\nIFSC"));
    }
}

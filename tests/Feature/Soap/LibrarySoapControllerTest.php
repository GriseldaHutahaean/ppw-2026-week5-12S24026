<?php

namespace Tests\Feature\Soap;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibrarySoapControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_wsdl_endpoint_publishes_the_clearance_operation(): void
    {
        $response = $this->get('/soap/sipus.wsdl')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/wsdl+xml; charset=utf-8')
            ->assertSee('CheckClearanceStatus');

        $document = new \DOMDocument;

        $this->assertTrue($document->loadXML($response->getContent()));
        $xpath = new \DOMXPath($document);
        $this->assertSame('CheckClearanceStatus', $xpath->evaluate('string(//*[local-name()="operation"]/@name)'));
        $this->assertSame(url('/soap/sipus'), $xpath->evaluate('string(//*[local-name()="address"]/@location)'));
    }

    public function test_clearance_check_returns_active_loan_count_and_status(): void
    {
        $buku = Buku::factory()->create();
        $this->createLoan($buku, '12S24020', 'dipinjam');
        $this->createLoan($buku, '12S24020', 'dikembalikan');

        $xml = <<<'XML'
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body><CheckClearanceStatus xmlns="urn:sipus-del"><nim_peminjam>12S24020</nim_peminjam></CheckClearanceStatus></soap:Body>
</soap:Envelope>
XML;

        $response = $this->call('POST', '/soap/sipus', [], [], [], ['CONTENT_TYPE' => 'text/xml'], $xml)
            ->assertOk()
            ->assertHeader('Content-Type', 'text/xml; charset=utf-8');

        $document = new \DOMDocument;
        $document->loadXML($response->getContent());
        $xpath = new \DOMXPath($document);

        $this->assertSame('1', $xpath->evaluate('string(//*[local-name()="active_loans"])'));
        $this->assertSame('false', $xpath->evaluate('string(//*[local-name()="is_cleared"])'));

        $clearedXml = str_replace('12S24020', '12S24021', $xml);
        $clearedResponse = $this->call('POST', '/soap/sipus', [], [], [], ['CONTENT_TYPE' => 'text/xml'], $clearedXml);
        $clearedDocument = new \DOMDocument;
        $clearedDocument->loadXML($clearedResponse->getContent());
        $clearedXpath = new \DOMXPath($clearedDocument);

        $this->assertSame('0', $clearedXpath->evaluate('string(//*[local-name()="active_loans"])'));
        $this->assertSame('true', $clearedXpath->evaluate('string(//*[local-name()="is_cleared"])'));
    }

    public function test_invalid_or_empty_nim_returns_a_soap_fault(): void
    {
        $xml = <<<'XML'
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body><CheckClearanceStatus xmlns="urn:sipus-del"><nim_peminjam> </nim_peminjam></CheckClearanceStatus></soap:Body>
</soap:Envelope>
XML;

        $this->call('POST', '/soap/sipus', [], [], [], ['CONTENT_TYPE' => 'text/xml'], $xml)
            ->assertStatus(400)
            ->assertSee('<soap:Fault>', false)
            ->assertSee('nim_peminjam element is required');

        $this->call('POST', '/soap/sipus', [], [], [], ['CONTENT_TYPE' => 'text/xml'], '<invalid')
            ->assertStatus(400)
            ->assertSee('<soap:Fault>', false);
    }

    private function createLoan(Buku $buku, string $nim, string $status): Peminjaman
    {
        return $buku->peminjamans()->create([
            'kode_pinjam' => 'PJM-TEST-'.strtoupper(substr(hash('sha256', $nim.$status), 0, 12)),
            'nim_peminjam' => $nim,
            'nama_peminjam' => 'Mahasiswa Del',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'tanggal_kembali' => $status === 'dikembalikan' ? now()->toDateString() : null,
            'status' => $status,
        ]);
    }
}

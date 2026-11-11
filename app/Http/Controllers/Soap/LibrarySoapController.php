<?php

namespace App\Http\Controllers\Soap;

use App\Http\Controllers\Controller;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LibrarySoapController extends Controller
{
    private const SOAP_NAMESPACE = 'http://schemas.xmlsoap.org/soap/envelope/';

    private const SERVICE_NAMESPACE = 'urn:sipus-del';

    public function wsdl(): Response
    {
        $wsdl = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/"
             xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
             xmlns:tns="urn:sipus-del"
             xmlns:xsd="http://www.w3.org/2001/XMLSchema"
             name="SipusLibraryService"
             targetNamespace="urn:sipus-del">
  <types>
    <xsd:schema targetNamespace="urn:sipus-del" elementFormDefault="qualified">
      <xsd:element name="CheckClearanceStatus">
        <xsd:complexType><xsd:sequence><xsd:element name="nim_peminjam" type="xsd:string"/></xsd:sequence></xsd:complexType>
      </xsd:element>
      <xsd:element name="CheckClearanceStatusResponse">
        <xsd:complexType><xsd:sequence><xsd:element name="active_loans" type="xsd:nonNegativeInteger"/><xsd:element name="is_cleared" type="xsd:boolean"/></xsd:sequence></xsd:complexType>
      </xsd:element>
    </xsd:schema>
  </types>
  <message name="CheckClearanceStatusInput"><part name="parameters" element="tns:CheckClearanceStatus"/></message>
  <message name="CheckClearanceStatusOutput"><part name="parameters" element="tns:CheckClearanceStatusResponse"/></message>
  <portType name="SipusLibraryPortType">
    <operation name="CheckClearanceStatus"><input message="tns:CheckClearanceStatusInput"/><output message="tns:CheckClearanceStatusOutput"/></operation>
  </portType>
  <binding name="SipusLibraryBinding" type="tns:SipusLibraryPortType">
    <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>
    <operation name="CheckClearanceStatus">
      <soap:operation soapAction="urn:sipus-del/CheckClearanceStatus"/>
      <input><soap:body use="literal"/></input>
      <output><soap:body use="literal"/></output>
    </operation>
  </binding>
  <service name="SipusLibraryService">
    <port name="SipusLibraryPort" binding="tns:SipusLibraryBinding">
      <soap:address location="__ENDPOINT__"/>
    </port>
  </service>
</definitions>
XML;
        $wsdl = str_replace(
            '__ENDPOINT__',
            htmlspecialchars(route('soap.sipus'), ENT_QUOTES | ENT_XML1, 'UTF-8'),
            $wsdl,
        );

        return response($wsdl, 200, ['Content-Type' => 'application/wsdl+xml; charset=utf-8']);
    }

    public function handle(Request $request): Response
    {
        $xml = $request->getContent();

        if ($xml === '' || strlen($xml) > 1_048_576) {
            return $this->fault('The SOAP request is empty or exceeds the size limit.');
        }

        $document = new DOMDocument;
        $previousErrorSetting = libxml_use_internal_errors(true);

        try {
            $isValidXml = $document->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrorSetting);
        }

        if (! $isValidXml || $document->doctype !== null) {
            return $this->fault('The SOAP request contains malformed or disallowed XML.');
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('soap', self::SOAP_NAMESPACE);
        $value = $xpath->evaluate('string((/soap:Envelope/soap:Body//*[local-name()="nim_peminjam"])[1])');
        $nim = trim((string) $value);

        if ($nim === '') {
            return $this->fault('The nim_peminjam element is required.');
        }

        $activeLoans = DB::table('peminjamans')
            ->where('nim_peminjam', $nim)
            ->where('status', 'dipinjam')
            ->count();

        $responseDocument = new DOMDocument('1.0', 'UTF-8');
        $envelope = $responseDocument->createElementNS(self::SOAP_NAMESPACE, 'soap:Envelope');
        $body = $responseDocument->createElementNS(self::SOAP_NAMESPACE, 'soap:Body');
        $result = $responseDocument->createElement('CheckClearanceStatusResponse');
        $result->setAttribute('xmlns', self::SERVICE_NAMESPACE);
        $result->appendChild($responseDocument->createElement('active_loans', (string) $activeLoans));
        $result->appendChild($responseDocument->createElement('is_cleared', $activeLoans === 0 ? 'true' : 'false'));
        $body->appendChild($result);
        $envelope->appendChild($body);
        $responseDocument->appendChild($envelope);

        return response($responseDocument->saveXML(), 200, ['Content-Type' => 'text/xml; charset=utf-8']);
    }

    private function fault(string $message): Response
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $envelope = $document->createElementNS(self::SOAP_NAMESPACE, 'soap:Envelope');
        $body = $document->createElementNS(self::SOAP_NAMESPACE, 'soap:Body');
        $fault = $document->createElementNS(self::SOAP_NAMESPACE, 'soap:Fault');
        $fault->appendChild($document->createElement('faultcode', 'soap:Client'));
        $fault->appendChild($document->createElement('faultstring', $message));
        $body->appendChild($fault);
        $envelope->appendChild($body);
        $document->appendChild($envelope);

        return response($document->saveXML(), 400, ['Content-Type' => 'text/xml; charset=utf-8']);
    }
}

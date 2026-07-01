<?php

namespace Mp\MLetter\Tests\Unit;

use Mp\MLetter\Data\ContractParty;
use Mp\MLetter\Documents\BasicDocument;
use Mp\MLetter\Documents\CivilContractDocument;
use Mp\MLetter\Documents\LetterDocument;
use Mp\MLetter\MLetterServiceProvider;
use Orchestra\Testbench\TestCase;

class DocumentsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [MLetterServiceProvider::class];
    }

    public function test_basic_document_prepares_safe_view_data(): void
    {
        $document = BasicDocument::make()
            ->headingLines([
                'Uchwała nr R/1/2026',
                'Rady Fundacji Moje Państwo',
                'z dnia 30 czerwca 2026 r.',
            ])
            ->title('Sprawozdanie Fundacji Moje Państwo <script>bad()</script>')
            ->bodyMarkdown('Kwota 3 333 744,13 zł')
            ->signatures([
                ['name' => 'Jan Kowalski', 'label' => 'Przewodniczący'],
            ]);

        $data = $document->data();

        $this->assertSame('mletter::documents.basic-document', $document->view());
        $this->assertSame('Rady Fundacji&nbsp;Moje&nbsp;Państwo', $data['headingLines'][1]);
        $this->assertStringContainsString('Fundacji&nbsp;Moje&nbsp;Państwo', $data['title']);
        $this->assertStringNotContainsString('<script>', $data['title']);
        $this->assertStringContainsString('3&nbsp;333&nbsp;744,13&nbsp;zł', $data['bodyHtml']);
    }

    public function test_basic_document_can_append_heading_lines(): void
    {
        $document = BasicDocument::make()
            ->headingLine('Dokument nr 1')
            ->headingLine(null)
            ->headingLine('Fundacja Moje Państwo');

        $data = $document->data();

        $this->assertSame([
            'Dokument nr 1',
            'Fundacja&nbsp;Moje&nbsp;Państwo',
        ], $data['headingLines']);
    }

    public function test_civil_contract_document_prepares_safe_view_data(): void
    {
        $document = CivilContractDocument::make()
            ->heading('Umowa zlecenie')
            ->number('2026/06/30/01')
            ->signedDate('Warszawa, dnia 30 czerwca 2026 r.')
            ->orderingParty(new ContractParty(
                label: 'Zamawiający:',
                name: 'Fundacja Moje Państwo',
                addressLine: 'ul. Nowogrodzka 25/37',
                postalCity: '00-511 Warszawa',
                nip: '1231216692',
                representatives: 'Jan Kowalski',
            ))
            ->contractorParty(new ContractParty(
                label: 'Wykonawca:',
                name: 'Anna Nowak',
                addressLine: 'ul. Testowa 1',
                postalCity: '00-001 Warszawa',
                pesel: '12345678901',
            ))
            ->bodyMarkdown('Wynagrodzenie 3 333,00 zł')
            ->signatureLabels('Zamawiający', 'Wykonawca');

        $data = $document->data();

        $this->assertSame('mletter::documents.civil-contract', $document->view());
        $this->assertSame('Fundacja&nbsp;Moje&nbsp;Państwo', $data['orderingParty']['name']);
        $this->assertStringContainsString('3&nbsp;333,00&nbsp;zł', $data['bodyHtml']);
    }

    public function test_letter_document_prepares_safe_view_data(): void
    {
        $document = LetterDocument::make()
            ->dateLine('Warszawa, 30 czerwca 2026 r.')
            ->recipient('Jan Kowalski <script>bad()</script>', "ul. Testowa 1\n00-001 Warszawa", 'jan@example.test')
            ->subject('odpowiedzi Fundacji Moje Państwo')
            ->bodyHtml('<p>Kwota 3 333,00 zł</p>')
            ->signatureLines(['Z poważaniem,', 'Fundacja Moje Państwo'])
            ->footer(address: 'ul. Pańska 96/83, 00-837 Warszawa', identifiers: 'KRS 0000359730, NIP 7010253245');

        $data = $document->data();

        $this->assertSame('mletter::documents.letter', $document->view());
        $this->assertStringNotContainsString('<script>', $data['recipientName']);
        $this->assertStringContainsString('Fundacji&nbsp;Moje&nbsp;Państwo', $data['subject']);
        $this->assertStringContainsString('3&nbsp;333,00&nbsp;zł', $data['bodyHtml']);
        $this->assertSame('ul. Pańska 96/83, 00-837 Warszawa', $data['organizationAddress']);
    }

    public function test_document_views_render(): void
    {
        $basicDocument = BasicDocument::make()
            ->headingLine('Dokument nr 1')
            ->title('Tytuł')
            ->bodyMarkdown('Treść');

        $contract = CivilContractDocument::make()
            ->heading('Umowa')
            ->orderingParty(new ContractParty('Zamawiający:', 'Fundacja Moje Państwo'))
            ->contractorParty(new ContractParty('Wykonawca:', 'Jan Kowalski'))
            ->bodyMarkdown('Treść');

        $letterDocument = LetterDocument::make()
            ->dateLine('Warszawa, 30 czerwca 2026 r.')
            ->recipient('Jan Kowalski')
            ->bodyHtml('<p>Treść pisma</p>');

        $this->assertStringContainsString('Dokument nr 1', view($basicDocument->view(), $basicDocument->data())->render());
        $this->assertStringContainsString('Jan Kowalski', view($contract->view(), $contract->data())->render());
        $this->assertStringContainsString('Treść pisma', view($letterDocument->view(), $letterDocument->data())->render());
    }
}

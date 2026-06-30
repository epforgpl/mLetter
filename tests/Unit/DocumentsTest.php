<?php

namespace Mp\MLetter\Tests\Unit;

use Mp\MLetter\Data\ContractParty;
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

    public function test_letter_document_prepares_safe_view_data(): void
    {
        $document = LetterDocument::make()
            ->typeLine('Uchwała nr R/1/2026')
            ->organizationLine('Rady Fundacji Moje Państwo')
            ->dateLine('z dnia 30 czerwca 2026 r.')
            ->title('Sprawozdanie Fundacji Moje Państwo <script>bad()</script>')
            ->bodyMarkdown('Kwota 3 333 744,13 zł')
            ->signatures([
                ['name' => 'Jan Kowalski', 'label' => 'Przewodniczący'],
            ]);

        $data = $document->data();

        $this->assertSame('mletter::documents.letter', $document->view());
        $this->assertSame('Rady Fundacji&nbsp;Moje&nbsp;Państwo', $data['organizationLine']);
        $this->assertStringContainsString('Fundacji&nbsp;Moje&nbsp;Państwo', $data['title']);
        $this->assertStringNotContainsString('<script>', $data['title']);
        $this->assertStringContainsString('3&nbsp;333&nbsp;744,13&nbsp;zł', $data['bodyHtml']);
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

    public function test_document_views_render(): void
    {
        $letter = LetterDocument::make()
            ->typeLine('Dokument nr 1')
            ->title('Tytuł')
            ->bodyMarkdown('Treść');

        $contract = CivilContractDocument::make()
            ->heading('Umowa')
            ->orderingParty(new ContractParty('Zamawiający:', 'Fundacja Moje Państwo'))
            ->contractorParty(new ContractParty('Wykonawca:', 'Jan Kowalski'))
            ->bodyMarkdown('Treść');

        $this->assertStringContainsString('Dokument nr 1', view($letter->view(), $letter->data())->render());
        $this->assertStringContainsString('Jan Kowalski', view($contract->view(), $contract->data())->render());
    }
}

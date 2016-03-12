<?php
/**
 * This file is part of the Pixidos (http://www.pixidos.com)
 *
 * Copyright (c) 2016 Ondra Votava Pixidos LTD  (ondra.votava@pixidos.com)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 *
 */


/**
 * Created by PhpStorm.
 * User: Ondra Votava
 * Date: 22.02.2016
 * Time: 18:12
 */

namespace Pixidos\InvoicePdfCreator;

use Kdyby\Translation\Translator;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Pixidos\Accountancy\Core\Exceptions\InvalidArgumentExcetion;
use Pixidos\Accountancy\Core\Interfaces\IInvoice;
use Pixidos\InvoicePdfCreator\Exception\InvoicePdfCreatorException;
use Pixidos\PdfDocumentCreator\PdfDocumentCreatorProcesor;

/**
 * Class PdfInvoiceProcessor
 * @package Pixidos\InvoicePdfCreator
 * @author Ondra Votava <ondra.votava@pixidos.com>
 */
class PdfInvoiceProcessor
{

    /**
     * @var PdfDocumentCreatorProcesor $documentProcesor
     */
    private $documentProcesor;

    /**
     * @var Translator $translator
     */
    private $translator;

    /**
     * @var null | IInvoice $invoice
     */
    private $invoice = NULL;
    /**
     * @var  string invoiceName
     */
    private $filename = NULL;

    /**
     * @var Template template
     */
    private $template;

    private $templateFile = NULL;
    /**
     * @var  priceFormater
     */
    private $priceFormater;


    /**
     * PdfInvoiceProcessor constructor.
     * @param PdfDocumentCreatorProcesor $procesor
     * @param Translator $translator
     * @param ILatteFactory $templateFactory
     */
    public function __construct(PdfDocumentCreatorProcesor $procesor,
                                Translator $translator,
                                ILatteFactory $templateFactory)
    {
        $this->documentProcesor = $procesor;
        $this->translator = $translator;
        $this->template = new Template($templateFactory->create());
        $this->template->setTranslator($this->translator);

    }

    public function setTemplateFile($file)
    {
        if (file_exists($file)) {
            $this->templateFile = $file;
        } else {
            throw new InvalidArgumentExcetion($file . ' not exist');
        }
    }


    /**
     * @param IPriceFormater $priceFormater
     */
    public function setPriceFormater(IPriceFormater $priceFormater)
    {
        $this->priceFormater = $priceFormater;
    }

    /**
     * @param IInvoice $invoice
     * @throws InvoicePdfCreatorException
     * @throws \Pixidos\PdfDocumentCreator\Exceptions\PdfDocumentCreatorExceptions
     */
    public function createPdf( IInvoice $invoice)
    {
        $this->invoice = $invoice;

        $this->setupTemplateData();

        /**
         * set invoice template file
         */
        $this->setupTemplateFile();

        /**
         * Add filters to template
         */
        $this->setupFilterToTemplate();

        /**
         * Covert template file to string
         */
        $html = (string)$this->template;

        if($this->filename == NULL){
            $this->filename = $invoice->getInvoiceNo() . '.pdf';
        }

        /**
         * Create Pdf Document
         */
        $this->documentProcesor->createPdfDocumentFile($html, $this->filename);

    }

    /**
     * setup data for template
     */
    private function setupTemplateData()
    {
        $this->template->customer = $this->invoice->getCustomer();
        $this->template->supplier = $this->invoice->getSupplier();
        $this->template->bnkacc = $this->invoice->getBankAccount();
        $this->template->items = $this->invoice->getInvoiceItems();

        $this->template->logoImage = $this->getBase64Image($this->invoice->getSupplier()->getLogoImagePath());
    }


    /**
     * set custom latte template file
     * @param $filename
     */
    public function setInvoiceFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Convert Image to Base64
     * @param $path
     * @return string
     */
    private function getBase64Image($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    /**
     *  Setup Latte Filter
     */
    private function setupFilterToTemplate()
    {
        $this->template->addFilter('priceFormat', function ($price, $currency, $lang) {
            return $this->priceFormater->priceFormat($price, $currency, $lang);
        });
    }

    /**
     * Setup Template file
     * @throws InvoicePdfCreatorException
     */
    private function setupTemplateFile()
    {
        if ($this->templateFile == NULL) {

            $this->template->setFile(__DIR__ . '/template/defaultInvoice.latte');
            $style = file_get_contents(__DIR__ . '/template/invoice.css');
            $this->template->style = $style;

        } else {

            if (!file_exists($this->templateFile)) {
                throw new InvoicePdfCreatorException($this->templateFile . ' not exist');
            }

            $this->template->setFile($this->templateFile);
        }
    }


}
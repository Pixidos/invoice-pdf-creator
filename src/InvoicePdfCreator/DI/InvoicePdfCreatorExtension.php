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
 * Date: 24.02.2016
 * Time: 9:46
 */

namespace Pixidos\InvoicePdfCreator\DI;
use Kdyby\Translation\DI\ITranslationProvider;
use Nette\DI\CompilerExtension;
use Nette;

/**
 * Class InvoicePdfCreatorExtension
 * @package Pixidos\InvoicePdfCreator\DI
 * @author Ondra Votava <ondra.votava@pixidos.com>
 */

class InvoicePdfCreatorExtension extends CompilerExtension implements ITranslationProvider
{

    /**
     * Return array of directories, that contain resources for translator.
     *
     * @return string[]
     */
    function getTranslationResources()
    {
        return [__DIR__ . '/../resource/lang'];
    }

    public function loadConfiguration()
    {

//        $config = $this->getConfig($this->defaults);

        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('default'))
            ->setClass('Pixidos\InvoicePdfCreator\PdfInvoiceProcessor');
    }


    public static function register(Nette\Configurator $configurator)
    {
        $configurator->onCompile[] = function ($config, Nette\DI\Compiler $compiler) {
            $compiler->addExtension('PdfDocumentCreator', new InvoicePdfCreatorExtension());
        };
    }

}
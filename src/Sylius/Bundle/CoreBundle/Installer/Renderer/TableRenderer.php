<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Installer\Renderer;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

final class TableRenderer
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @var string
     */
    private $label;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->table = new Table($output);
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @param array $row
     */
    public function addRow(array $row): void
    {
        $this->rows[] = $row;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function render(): void
    {
        if (null !== $this->label) {
            $this->output->writeln(sprintf('<comment>%s</comment>', $this->label));
        }

        $this->table
            ->setHeaders($this->headers)
            ->setRows($this->rows)
            ->render()
        ;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->rows);
    }
}

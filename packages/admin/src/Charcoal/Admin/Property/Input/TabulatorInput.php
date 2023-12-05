<?php

namespace Charcoal\Admin\Property\Input;

use Charcoal\Admin\Property\AbstractPropertyInput;
use InvalidArgumentException;

/**
 * Base Tabulator Input Property
 * Tabulator is a JS Framework that allows to create interactive tables from diverse sources of data.
 * See {@link https://github.com/olifolkerd/tabulator}
 */
class TabulatorInput extends AbstractPropertyInput
{
    /**
     * Settings for {@link https://github.com/olifolkerd/tabulator Bootstrap Datetabulator}.
     *
     * @var array
     */
    private array $tabulatorOptions = [
        'layout'    => 'fitColumns',
        'addRowPos' => 'bottom',
        'history'   => true,
    ];

    /**
     * Set the color input's options.
     *
     * This method always merges default options.
     *
     * @param array $options The color input options.
     * @return self
     */
    public function setInputOptions(array $options): self
    {
        parent::setInputOptions($options);

        $this->finalizeInputOptions();

        return $this;
    }

    /**
     * @return void
     */
    protected function finalizeInputOptions()
    {
        if (
            (isset($this->inputOptions['addRow']) && $this->inputOptions['addRow']) ||
            (isset($this->inputOptions['addColumn']) && $this->inputOptions['addColumn'])
        ) {
            $this->inputOptions['addColumnOrRow'] = true;
        }

        if (isset($this->inputOptions['addColumnLabel'])) {
            $this->inputOptions['addColumnLabel'] =
                $this->translator()->translate($this->inputOptions['addColumnLabel']);
        }

        if (isset($this->inputOptions['addRowLabel'])) {
            $this->inputOptions['addRowLabel'] = $this->translator()->translate($this->inputOptions['addRowLabel']);
        }
    }

    /**
     * Merge (replacing or adding) color input options.
     *
     * @param array $options The color input options.
     * @return self
     */
    public function mergeInputOptions(array $options): self
    {
        $this->inputOptions = array_merge($this->inputOptions, $options);

        $this->finalizeInputOptions();

        return $this;
    }

    /**
     * Add (or replace) a tabulator input option.
     *
     * @param string $key The setting to add/replace.
     * @param mixed  $val The setting's value to apply.
     * @return self
     * @throws InvalidArgumentException If the identifier is not a string.
     */
    public function addInputOption(string $key, $val): self
    {
        // Make sure default options are loaded.
        if ($this->inputOptions === null) {
            $this->getInputOptions();
        }

        $this->inputOptions[$key] = $val;

        $this->finalizeInputOptions();

        return $this;
    }

    /**
     * Retrieve the default color input options.
     *
     * @return array
     */
    public function getDefaultInputOptions(): array
    {
        $translator = $this->translator();

        return [
            'addColumn'             => false,
            'addColumnLabel'        => $translator->trans('Add Column'),
            'addRow'                => false,
            'addRowLabel'           => $translator->trans('Add Row'),
            'autoColumnStartIndex'  => 0,
            'autoColumnTemplates'   => [],
            'columnsManipulateData' => false,
            'newColumnData'         => null,
            'newRowData'            => null,
            'storableRowRange'      => null,
            'validateOn'            => [],
        ];
    }

    /**
     * Merge (replacing or adding) color tabulator options.
     *
     * @param array $options The color tabulator options.
     * @return self
     */
    public function mergeTabulatorOptions(array $options): self
    {
        $this->tabulatorOptions = array_merge(
            $this->tabulatorOptions,
            $options
        );

        $this->finalizeTabulatorOptions();

        return $this;
    }

    /**
     * @return void
     */
    protected function finalizeTabulatorOptions()
    {
        if (isset($this->tabulatorOptions['autoColumnTemplates'])) {
            foreach ($this->tabulatorOptions['autoColumnTemplates'] as &$column) {
                if (isset($column['title'])) {
                    $column['title'] = $this->translator()->translate($column['title']);
                }
            }
        }

        if (isset($this->tabulatorOptions['columns'])) {
            foreach ($this->tabulatorOptions['columns'] as &$column) {
                if (isset($column['title'])) {
                    $column['title'] = $this->translator()->translate($column['title']);
                }
            }
        }
    }

    /**
     * Add (or replace) a tabulator option.
     *
     * @param string $key The setting to add/replace.
     * @param mixed  $val The setting's value to apply.
     * @return self
     * @throws InvalidArgumentException If the identifier is not a string.
     */
    public function addTabulatorOption(string $key, $val): self
    {
        $this->tabulatorOptions[$key] = $val;

        $this->finalizeTabulatorOptions();

        return $this;
    }

    /**
     * Retrieve the color tabulator's options.
     *
     * @return array
     */
    public function getTabulatorOptions(): array
    {
        return $this->tabulatorOptions;
    }

    /**
     * Set the color tabulator's options.
     *
     * This method always merges default options.
     *
     * @param array $options The color tabulator options.
     * @return self
     */
    public function setTabulatorOptions(array $options): self
    {
        $this->mergeTabulatorOptions($options);

        return $this;
    }

    /**
     * Retrieve the control's data options for JavaScript components.
     *
     * @return array
     */
    public function controlDataForJs(): array
    {
        $inputOptions = $this->getInputOptions();
        $tabulatorOptions = $this->getTabulatorOptions();
        $tabulatorSelector = '#' . $this->inputId();

        if (isset($tabulatorOptions['history']) && !$tabulatorOptions['history']) {
            $inputOptions['undo'] = false;
            $inputOptions['redo'] = false;
        }

        if (isset($tabulatorOptions['wrap']) && $tabulatorOptions['wrap']) {
            $tabulatorSelector .= '_wrap';
        }

        return [
            'input_options'      => $inputOptions,
            'tabulator_selector' => $tabulatorSelector,
            'tabulator_options'  => $tabulatorOptions,
        ];
    }
}

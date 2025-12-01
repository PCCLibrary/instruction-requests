<?php

namespace App\PowerGridThemes;

use PowerComponents\LivewirePowerGrid\Themes\Theme;

/**
 * Custom Tailwind theme
 */
class Tailwind extends Theme
{
    /**
     * @var string
     */
    public string $name = 'tailwind';

    /**
     * @var string
     */
    public string $header_color = 'bg-cyan-600 dark:bg-cyan-900';


    /**
     * @return array[]
     */
    public function table(): array
    {
        return [
            'layout' => [
                'base'      => 'align-middle inline-block min-w-full w-full mb-8',
                'div'       => 'rounded-t-lg relative border-x border-t border-pg-primary-200 dark:bg-gray-800 dark:border-gray-700',
                'table'     => 'min-w-full dark:!bg-gray-900',
                'container' => '-my-2 overflow-x-auto sm:-mx-3 lg:-mx-8',
                'actions'   => 'flex gap-2',
            ],

            'header' => [
//                'thead'    => 'shadow-sm rounded-t-lg bg-cyan-600 dark:bg-cyan-900',
                'thead'    => 'shadow-sm rounded-t-lg '. $this->header_color,
                'tr'       => '',
                'th'       => 'font-extrabold px-3 py-3 text-left text-xs text-white tracking-wider whitespace-nowrap dark:text-white',
                'thAction' => '!font-bold',
            ],

            'body' => [
                'tbody'              => 'dark:bg-gray-900',
                'tbodyEmpty'         => 'dark:bg-gray-900',
                'tr'                 => 'border-b even:bg-gray-50 odd:bg-blue-50/50 border-pg-primary-100 dark:border-gray-700 dark:even:bg-gray-800 dark:odd:bg-gray-700 hover:bg-blue-100 dark:hover:bg-gray-700',
                'td'                 => 'px-3 py-2 whitespace-nowrap dark:text-gray-200',
                'tdEmpty'            => 'p-2 whitespace-nowrap dark:text-gray-200',
                'tdSummarize'        => 'p-2 whitespace-nowrap dark:text-gray-200 text-sm text-pg-primary-600 text-right space-y-2',
                'trSummarize'        => 'dark:bg-gray-800',
                'tdFilters'          => 'dark:bg-gray-800',
                'trFilters'          => 'dark:bg-gray-800',
                'tdActionsContainer' => 'flex gap-2',
            ],
        ];
    }

    /**
     * @return string[]
     */
    /**
     * @return string[]
     */
    public function footer(): array
    {
        return [
            'view' => $this->root() . '.footer',
            'select' => 'appearance-none !bg-none focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-gray-600 dark:text-gray-300 text-gray-600 ring-gray-300 dark:bg-gray-800 bg-white dark:placeholder-gray-400 rounded-md border-0 bg-transparent py-1.5 px-4 pr-7 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
            'footer' => 'border-x border-b rounded-b-lg border-gray-200 bg-gray-100 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300',
            'footer_with_pagination' => 'md:flex md:flex-row w-full items-center py-3 bg-gray-50 text-gray-700 overflow-y-auto px-4 relative dark:bg-gray-800 dark:text-gray-300',
        ];
    }

    /**
     * @return string[]
     */
    public function cols(): array
    {
        return [
            'div' => 'select-none flex items-center gap-1',
        ];
    }

    /**
     * @return string[]
     */
    public function editable(): array
    {
        return [
            'view'  => $this->root() . '.editable',
            'input' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-gray-600 dark:text-gray-300 text-gray-600 ring-gray-300 dark:bg-gray-800 bg-white dark:placeholder-gray-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
        ];
    }

    /**
     * @return string[]
     */
    public function toggleable(): array
    {
        return [
            'view' => $this->root() . '.toggleable',
        ];
    }

    /**
     * @return string[]
     */
    public function checkbox(): array
    {
        return [
            'th'    => 'px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider dark:text-gray-300',
            'base'  => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'form-checkbox dark:border-gray-600 border-1 dark:bg-gray-800 rounded border-gray-300 bg-white transition duration-100 ease-in-out h-4 w-4 text-primary-500 focus:ring-primary-500 dark:ring-offset-gray-900',
        ];
    }

    /**
     * @return string[]
     */
    public function radio(): array
    {
        return [
            'th'    => 'px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider dark:text-gray-300',
            'base'  => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'form-radio rounded-full transition ease-in-out duration-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300',
        ];
    }

    /**
     * @return string[]
     */
    public function filterBoolean(): array
    {
        return [
            'view'   => $this->root() . '.filters.boolean',
            'base'   => 'min-w-[5rem]',
            'select' => 'appearance-none !bg-none focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-gray-600 dark:text-gray-300 text-gray-600 ring-gray-300 dark:bg-gray-800 bg-white dark:placeholder-gray-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
        ];
    }

    /**
     * @return string[]
     */
    public function filterDatePicker(): array
    {
        return [
            'base'  => '',
            'view'  => $this->root() . '.filters.date-picker',
            'input' => 'flatpickr flatpickr-input focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-gray-600 dark:text-gray-300 text-gray-600 ring-gray-300 dark:bg-gray-800 bg-white dark:placeholder-gray-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
        ];
    }

    /**
     * @return string[]
     */
    public function filterMultiSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.multi-select',
            'base'   => 'inline-block relative w-full',
            'select' => 'mt-1',
        ];
    }

    /**
     * @return string[]
     */
    public function filterNumber(): array
    {
        return [
            'view'  => $this->root() . '.filters.number',
            'input' => 'w-full min-w-[5rem] block focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-gray-600 dark:text-gray-300 text-gray-600 ring-gray-300 dark:bg-gray-800 bg-white dark:placeholder-gray-400 rounded-md border-0 bg-transparent py-1.5 pl-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6',
        ];
    }

    /**
     * @return string[]
     */
    public function filterSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.select',
            'base'   => '',
            'select' => 'appearance-none !bg-none focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-gray-600 dark:text-gray-300 text-gray-600 ring-gray-300 dark:bg-gray-800 bg-white dark:placeholder-gray-400 rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
        ];
    }

    /**
     * @return string[]
     */
    public function filterInputText(): array
    {
        return [
            'view'   => $this->root() . '.filters.input-text',
            'base'   => 'min-w-[9.5rem]',
            'select' => 'appearance-none !bg-none focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-gray-600 dark:text-gray-300 text-gray-600 ring-gray-300 dark:bg-gray-800 bg-white dark:placeholder-gray-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
            'input'  => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-gray-600 dark:text-gray-300 text-gray-600 ring-gray-300 dark:bg-gray-800 bg-white dark:placeholder-gray-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
        ];
    }

    /**
     * @return string[]
     */
    public function searchBox(): array
    {
        return [
            'input'      => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex items-center rounded-md ring-1 transition focus-within:ring-2 dark:ring-gray-600 dark:text-gray-300 text-gray-600 ring-gray-300 dark:bg-gray-800 bg-white dark:placeholder-gray-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 pl-7 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full pl-12',
            'iconClose'  => 'text-pg-primary-400 dark:text-gray-400',
            'iconSearch' => 'text-pg-primary-300 mr-2 w-5 h-5 dark:text-gray-400',
        ];
    }
}

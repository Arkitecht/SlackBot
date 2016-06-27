<?php
namespace Arkitecht\SlackBot;


class SlackBotTableFormatter
{
    private $headers = [];
    private $data = [];
    private $lengths = [];
    private $options = [];
    private $output = '';
    private $formatting = [];


    //get the data
    //go through the columns get the longest value
    public function __construct($data, $options = [], $headers = [])
    {
        if (array_key_exists('headers', $options)) {
            $headers = collect($options['headers']);
            unset($options['headers']);
        }
        if ($headers) {
            $headers = collect($headers);
            $this->data = collect($data);
        } else {
            $headers = collect(array_shift($data));
            $this->data = collect($data);
        }
        $this->setHeadersAndAndFormatting($headers);
        $this->setOptions($options);
        $this->applyFormatting();
        $this->getLongestValues();
        $this->buildOutput();
    }

    private function setHeadersAndAndFormatting($headers)
    {
        $header_row = [];
        $headers->each(function ($col, $idx) use (&$header_row) {
            if (is_array($col)) {
                if (array_key_exists('header', $col)) {
                    $header_row[$idx] = $col['header'];
                    if (array_key_exists('formatting', $col)) {
                        $this->setColumnFormatting($idx, $col['formatting']);
                    }
                } else {
                    //we have an array but no header defined
                    //TODO - Throw an error
                }
            } else {
                $header_row[$idx] = $col;
            }
        });
        $this->headers = collect($header_row);
    }

    private function applyFormatting()
    {
        $mutate = false;
        $mutators = ['transform'];
        $formatters = collect($this->formatting)->map(function($col){
            return collect($col)->keys();
        })->flatten()->unique()->toArray();

        foreach ($mutators as $mutator)
            if (in_array($mutator, $formatters)) $mutate = true;

        if (!$mutate) return;
        $formatted_data = [];
        $this->data->each(function ($row, $rowidx) use ( $mutators,&$formatted_data ) {
            collect($row)->each(function (&$col, $idx) use ($rowidx, $mutators,&$formatted_data) {
                $formatters = collect($this->formatting[$idx]);
                $formatters->each(function($formatter,$key) use ( $mutators, &$col, $idx, $rowidx,&$formatted_data ){
                    if ( in_array($key,$mutators) ) {
                        $col = $this->formatColumn($col,$formatter,$idx,$rowidx);
                    }
                });
                $formatted_data[$rowidx][$idx] = $col;
            });
        });
        $this->data = collect($formatted_data);
    }



    private function formatColumn($col,$mutator,$col_idx,$row_idx)
    {
        if ( $mutator == 'currency' ) $col = '$'.number_format($col,2);

        return $col;
    }

    private function setColumnFormatting($idx, $formatting)
    {
        if (!$formatting) return;

        if (!array_key_exists($idx, $this->formatting))
            $this->formatting[$idx] = [];

        $this->formatting[$idx] = array_merge($this->formatting[$idx], $formatting);
    }

    public function setFormatting($formatting)
    {
        collect($formatting)->each(function ($format, $idx) {
            $this->setColumnFormatting($idx, $format);
        });
    }

    public function getColumnFormatting($idx, $key = '')
    {
        if (!array_key_exists($idx, $this->formatting))
            return null;

        $formatting = $this->formatting[$idx];

        if ($key) {
            if (array_key_exists($key, $formatting)) return $formatting[$key];

            return null;
        }

        return $formatting;
    }

    public function setOptions($options)
    {
        $default_options = [
            'spacing' => 5
        ];
        if (array_key_exists('formatting', $options)) {
            $this->setFormatting($options['formatting']);
            unset($options['formatting']);
        }
        $this->options = array_merge($default_options, $options);
    }

    public function getLongestValues()
    {
        $this->headers->each(function ($col, $idx) {
            $this->checkAndSetLogValue($idx, $col);
        });
        $this->data->each(function ($row) {
            collect($row)->each(function ($col, $idx) {
                $this->checkAndSetLogValue($idx, $col);
            });
        });
    }

    public function checkAndSetLogValue($idx, $str)
    {
        if (!array_key_exists($idx, $this->lengths) || $this->lengths[$idx] < strlen($str)) {
            $this->lengths[$idx] = strlen($str);
        }
    }

    public function rowLength()
    {
        $length = 0;
        $this->headers->each(function ($col, $idx) use (&$length) {
            $length += $this->lengths[$idx];
            if ($idx > 0) $length += $this->options['spacing'];
        });

        return $length;
    }

    public function outputSeparator()
    {
        $this->addOutputLine(str_repeat('-', $this->rowLength()));
    }

    public function outputTitle()
    {
        if (array_key_exists('title', $this->options))
            $this->addOutputLine(SlackBotFormatter::bold($this->options['title']));

    }

    public function addOutputLine($line)
    {
        $this->output .= $line . "\n";
    }

    public function outputRow($row)
    {
        $line = '';
        collect($row)->each(function ($col, $idx) use (&$line) {
            $align = ($this->getColumnFormatting($idx, 'align') == 'right') ? STR_PAD_LEFT : STR_PAD_RIGHT;
            $line .= str_pad($col, $this->lengths[$idx], ' ', $align);
            $line .= str_repeat(' ', $this->options['spacing']);
        });
        $this->addOutputLine($line);
    }

    public function outputStartPre()
    {
        $this->output .= "```
";
    }

    public function outputEndPre()
    {
        $this->output .= "```";
    }

    public function buildOutput()
    {
        $this->outputTitle();
        $this->outputStartPre();
        $this->outputRow($this->headers);
        $this->outputSeparator();
        $this->data->each(function ($row) {
            $this->outputRow($row);
        });
        $this->outputEndPre();
    }

    public function output()
    {
        return $this->output;
    }

}
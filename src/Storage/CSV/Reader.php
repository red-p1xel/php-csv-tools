<?php

namespace Storage\CSV;

use Exception;

/**
 * CSV File reader
 *
 * @package Storage\CSV
 */
class Reader implements ReaderInterface
{
    /** Line-length limitation @var int */
    const MAX_LEN = 0;

    /** Iterator file pointer @var resource */
    protected $pointer;

    /** Field delimiter character @var string */
    protected $delimiter;

    /** Current iterator value @var mixed */
    protected $value;

    /** Current iterator key @var int */
    protected $offset;

    /** Table heading indexes fields @var array */
    protected $headerFields; // ['customerId', 'createdAt', 'duration', 'phone', 'ip'];

    /**
     * @param string $file
     * @param string $delimiter
     * @throws Exception
     */
    public function __construct($file, $delimiter = ',')
    {
        try {
            $this->pointer = fopen($file, 'r');
            $this->delimiter = $delimiter;
            $this->headerFields = $this->header();
        } catch (\Exception $e) {
            throw new \Exception("File can't be opened. {$e->getMessage()}", 500);
        }
    }

    /**
     * Get current line number
     * @return int
     */
    public function index(): int
    {
        return $this->offset;
    }

    /**
     * Retrieves current line from file.
     * @return string|array
     */
    public function current(): string|array
    {
        $this->value = fgetcsv($this->pointer, 0, $this->delimiter);
        $this->offset++;

        return $this->value;
    }

    /**
     * Zero-number field validation
     *
     * @return bool
     */
    private function header(): bool
    {
        // TODO: Implement header row validation with resolver.

        $this->headerFields = $this->current();
        if ($this->headerFields !== null) {
            foreach ($this->headerFields as $key => $val) {
                if (is_numeric($val)) {
                    // TODO: Thoughts on implementation. A resolver method must prepend $headerFields to position 0 on the file pointer
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $data1 = [];
        if ($this->isValid()) {
            while (false !== ($rows = fgetcsv($this->pointer, self::MAX_LEN))) {
                // Ignore blank lines
                if ($rows && array(null) !== $rows) {
                    $data1 = $rows;
                }
            }
        }

        return $data1;
    }

    /**
     * Read next line from file pointer
     *
     * @return bool
     */
    public function nextRow(): bool
    {
        if (is_resource($this->pointer)) {
            return !feof($this->pointer);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (!$this->nextRow()) {
            if (is_resource($this->pointer)) {
                fclose($this->pointer);
            }

            return false;
        }

        return true;
    }

    /**
     * Seek to specified line.
     *
     * @param  int $position
     * @throws \Exception if the position is negative
     */
    public function seek($position): void
    {
        if ($position < 0) {
            throw new \Exception(sprintf('%s() can\'t seek stream to negative line %d', __METHOD__, $position));
        }

        $this->rewind();
        while ($this->index() !== $position && $this->isValid()) {
            $this->current();
            $this->nextRow();
        }

        if (0 !== $position) {
            $this->offset--;
        }

        $this->current();
    }

    /**
     * Reset file pointer
     */
    public function rewind(): void
    {
        rewind($this->pointer);
        $this->offset = 0;
        $this->value = false;
        $this->current();
    }
}

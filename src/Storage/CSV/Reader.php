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
    protected string $delimiter = ',';

    /** Current iterator value @var mixed */
    protected mixed $value;

    /** Current iterator key @var int */
    protected int $offset;

    /** Table heading indexes fields @var array */
    protected array $headerFields = []; // ['customerId', 'createdAt', 'duration', 'phone', 'ip'];

    private array $objectFields;
    private array $formatters = [];
    private $filter;
    private $each;
    private $group;

    /**
     * Reader constructor
     * @param $pointer
     */
    public function __construct($pointer)
    {
        $this->pointer = $pointer;
    }

    /**
     * Return static instance with initialized file pointer
     *
     * @param string $filePath
     * @param string $mode
     * @param null $context
     * @return Reader
     * @throws Exception
     */
    public static function getInstance(string $filePath, string $mode = 'r+', $context = null)
    {
        $args = [$filePath, $mode];
        if (null !== $context) {
            $args[] = false;
            $args[] = $context;
        }

        $resource = fopen(...$args);
        if (!is_resource($resource)) {
            throw new \Exception(sprintf('`%s`: failed to open stream: No such file or directory', $filePath));
        }

        return new self($resource);
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
        $this->value = fgetcsv($this->pointer, self::MAX_LEN, $this->delimiter);
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
        $this->offset = 0;
        $this->value = $this->current();

        if ($this->value !== null) {
            foreach ($this->value as $key => $val) {
                if (is_numeric($val)) {
                    $this->offset--;
                    // TODO: Thoughts on implementation. A resolver method must prepend $headerFields to position 0 on the file pointer
                    return false;
                }
            }
        }

        $this->headerFields = $this->value;

        return true;
    }

    /**
     * Return CSV document header
     *
     * @return array
     */
    public function getHeader(): array
    {
        return ($this->header() == true) ? $this->headerFields : [];
    }

    /**
     * Return file as array of objects
     *
     * @param array $objectFields
     * @return Reader
     * @throws Exception
     */
    public function toObject(array $objectFields = [])
    {
        $headerFields = $this->headerFields;

        if (empty($this->headerFields) && empty($objectFields)) {
            throw new Exception('Undefined property `headerFields`. You must define custom fields for object.');
        }
        $this->objectFields = (!empty($headerFields) && !array_diff($objectFields, $headerFields))
            ? $this->headerFields
            : $objectFields;

        return $this;
    }

    /**
     * Format given line by key using a callable
     *
     * @param $key
     * @param callable $callable
     * @return $this
     */
    public function format($key, callable $callable)
    {
        foreach ((array)$key as $k) {
            $this->formatters[$k][] = $callable;
        }

        return $this;
    }

    /**
     * Set callable to be called on each line to filter lines to retrieve
     *
     * @param callable $callable
     * @return $this
     */
    public function filter(callable $callable)
    {
        $this->filter = $callable;

        return $this;
    }

    /**
     * Set callable to be called on each line
     *
     * @param callable $callable
     * @return $this
     */
    public function each(callable $callable)
    {
        $this->each = $callable;

        return $this;
    }

    /**
     * Set grouping rules to return file contents grouped in array
     *
     * @param callable $callable
     * @return $this
     */
    public function groupBy(callable $callable)
    {
        $this->group = $callable;

        return $this;
    }

    /**
     * Parse file and return content
     *
     * @return array
     */
    public function parse(): array
    {
        $lines = [];

        if ($this->isValid()) {
            while (false !== ($this->value = fgetcsv($this->pointer, self::MAX_LEN))) {
                $line = $this->value;

                // Ignore blank lines
                if ($line && array(null) !== $line) {
                    $this->offset++;
                    // Transform array of lines to object?
                    if ($this->objectFields !== null) {
                        $line = (object) array_combine($this->objectFields, $line);
                    }
                    // Execute callable to filter line
                    if (is_callable($this->filter)) {
                        $func = $this->filter;
                        if (!(boolean)$func($line, $this->offset)) {
                            continue;
                        }
                    }
                    // Execute callable for each line
                    if (is_callable($this->each)) {
                        $func = $this->each;
                        $line = $func($line, $this->offset);
                    }

                    foreach ($this->formatters as $key => $callables) {
                        foreach ($callables as $callable) {
                            if (is_object($line)) {
                                $line->{$key} = $callable($line->{$key});
                            } else {
                                $line[$key] = $callable($line[$key]);
                            }
                        }
                    }

                    if (is_callable($this->group)) {
                        $func = $this->group;
                        $lines[$func($line)][] = $line;
                    } else {
                        $lines[] = $line;
                    }
                }
            }
        }

        return $lines;
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

<?php

namespace Siyahmadde;


/**
 * Provides iterator to go recursively through all files inside given dir
 *
 * @package Siyahmadde
 */
class Iterator implements \Iterator
{
    const RETURN_FOLDERS = 'return_folders';
    const RECURSIVE = 'recursive';
    /**
     * @var Disk
     */
    protected $_disk;

    protected $_path;

    protected $_result;

    protected $_current;

    protected $_key = 0;

    protected $_currentSet;

    protected $_currentIndex = 0;

    protected $_offset;

    protected $_options;

    /**
     * @var Iterator
     */
    protected $_child;


    /**
     * Database constructor.
     *
     * @param $disk
     * @param $path
     * @param array $options
     * @internal param $sql
     */
    public function __construct(Disk $disk, $path, $options = [])
    {
        $this->_disk = $disk;
        $this->_path = $path;
        $this->_options = $options;
    }

    /**
     *
     */
    public function current()
    {
        if ($this->_child) {
            // If child exists means we are going through contents of curent folder
            return $this->_child->current();
        }
        if ($this->_options[self::RECURSIVE] AND @$this->_currentSet->_embedded->items[$this->_currentIndex]->type == 'dir') {
            // Current item is folder and we need to return it's contents recursively
            $this->_child = new self(
                $this->_disk, $this->_currentSet->_embedded->items[$this->_currentIndex]->path,
                $this->_options);
            $this->_child->rewind();
            if ($this->_options[self::RETURN_FOLDERS]) {
                // returning current folder if option is set
                return $this->_currentSet->_embedded->items[$this->_currentIndex];
            } else {
                // returning subfolder items
                return $this->_child->current();
            }
        } else {
            return $this->_currentSet->_embedded->items[$this->_currentIndex] ?? false;
        }
    }

    /**
     *
     */
    public function key()
    {
        return $this->_key;
    }

    public function next()
    {
        if ($this->_child) {
            // If child exists means we are going through contents of curent folder
            $this->_child->next();
            if (!$this->_child->valid()) {
                $this->_child = false;
                $this->_advance();
            }
        } else {
            $this->_advance();
        }
        $this->_key++;
    }

    protected function _advance()
    {
        $this->_currentIndex++;
        if (!array_key_exists($this->_currentIndex, $this->_currentSet->_embedded->items)) {
            $this->_getNewSet();
        }
    }

    protected function _getNewSet()
    {
        $this->_offset += $this->_currentIndex;
        if (isset($this->_currentSet->_embedded->total) AND $this->_offset >= $this->_currentSet->_embedded->total) {
            // We reached the end
            return;
        }
        $this->_currentSet = $this->_disk->getResource($this->_path, [
            'offset' => $this->_offset,
            'limit'  => $this->_options['limit'] ?? 10,
            'sort'   => $this->_options['sort'] ?? 'name'
        ]);
        $this->_currentIndex = 0;
    }

    public function rewind()
    {
        $this->_offset = 0;
        $this->_currentIndex = 0;
        $this->_getNewSet();
    }

    /**
     *
     */
    public function valid()
    {
        return $this->current() !== false;
    }
}
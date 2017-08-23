<?php

class Paginator extends LimitIterator
{

    protected $iterator;
    protected $currentPage;
    protected $limit;
    protected $count;
    protected $totalPages;

    /**
     * 
     * @param ArrayIterator $iterator
     * @param integer $page
     * @param integer $limit
     */
    public function __construct(ArrayIterator $iterator, $page = 1, $limit = 10)
    {
        $this->iterator = $iterator;
        $this->count = $iterator->count();
        $this->setCurrentPage($page);
        $this->setItemsPerPage($limit);
    }

    /**
     * 
     * @param integer $count
     */
    public function setItemsPerPage($count = 10)
    {
        $this->itemsPerPage = (int) $count;
        $this->totalPages = ($this->count > $this->itemsPerPage) ? ceil($this->count / $this->itemsPerPage) : 1;
    }

    /**
     * 
     * @param type $page
     */
    public function setCurrentPage($page = 1)
    {
        $this->currentPage = (int) $page;
    }

    /**
     * 
     * @return type
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * 
     * @return type
     */
    public function hasNextPage()
    {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * 
     * @return type
     */
    public function hasPreviousPage()
    {
        return $this->currentPage > 1;
    }

    /**
     * 
     * @param int $page
     * @param type $limit
     * @return \LimitIterator
     */
    public function render($page = NULL, $limit = NULL)
    {
        if (!empty($page)) {
            $this->setCurrentPage($page);
        }

        if (!empty($limit)) {
            $this->setItemsPerPage($limit);
        }

        // quickly calculate the offset based on the page
        if ($page > 0)
            $page -= 1;
        $offset = $page * $this->itemsPerPage;

        // return the limit iterator
        return new \LimitIterator($this->iterator, $offset, $this->itemsPerPage);
    }

}

<?php

namespace Application\Concrete\Page\Controller;

class PageResponse {
    private $pages;
    private $loadMore;
    private $total;
    private $totalPages;
    private $htmlData;

    public function __construct($pages, $loadMore, $total, $totalPages) {
        $this->pages = $pages;
        $this->loadMore = $loadMore;
        $this->total = $total;
        $this->totalPages = $totalPages;
    }

    public function getPages() {
        return $this->pages;
    }

    public function getLoadMore() {
        return $this->loadMore;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getTotalPages() {
        return $this->totalPages;
    }

    public function getHtmlData()
    {
        return $this->htmlData;
    }

    public function setHtmlData($htmlData)
    {
        $this->htmlData = $htmlData;
    }

    public function getResultsText($text)
    {
        return $this->total == 1 ? $text:  $text.'s';
    }

    public function getCurrentPageResultsCount() {
        return count($this->pages);
    }
}
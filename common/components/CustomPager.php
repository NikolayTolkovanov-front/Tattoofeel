<?php
namespace common\components;

use Yii;
use yii\helpers\Html;

class CustomPager  extends \yii\widgets\LinkPager
{
    public $separator = '...';

    public $activePageAsLink = false;

    public $separatorPageCssClass = 'separator';

    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = ['class' => $class === '' ? null : $class];
        if ($label === $this->separator) {
            return  Html::tag('span', $label, ['class' => $this->separatorPageCssClass]);
        }
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
            return  Html::tag('span', $label, ['class' => 'pager-current-page', 'data-current_page' => $label]);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            return  Html::tag('span', $label);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;
        $linkOptions['data-pjax'] = 0;

        $request = Yii::$app->getRequest();
        $url = '/'.$request->pathInfo;
        $params = $request->getQueryParams();
        $url = preg_replace("/\/page-(\d+)\//", '/', $url);

        if ($page != 0) {
            $url .= 'page-'.($page + 1).'/';
        }

        if (isset($params['q'])) {
            $url .= '?q='.$params['q'];
        }

        if ($active && !$this->activePageAsLink) {
            return Html::tag('span', $label, $linkOptions);
        }

        return Html::a($label, $url, $linkOptions);
    }

    protected function renderPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $buttons = [];
        $currentPage = $this->pagination->getPage();

        // first page
        if ($this->firstPageLabel !== false) {
            $buttons[] = $this->renderPageButton($this->firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0,
                false);
        }

        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }

            if ($currentPage > 0) {
                $buttons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass,
                    $currentPage <= 0, false);
            }
        }

        // page calculations
        list($beginPage, $endPage) = $this->getPageRange();
        $startSeparator = false;
        $endSeparator = false;
        $beginPage++;
        $endPage--;
        if ($beginPage != 1) {
            $startSeparator = true;
            $beginPage++;
        }
        if ($endPage + 1 != $pageCount - 1) {
            $endSeparator = true;
            $endPage--;
        }

        // smallest page
        $buttons[] = $this->renderPageButton(1, 0, null, false, 0 == $currentPage);

        // separator after smallest page
        if ($startSeparator) {
            $buttons[] = $this->renderPageButton($this->separator, null, $this->separatorPageCssClass, true, false);
        }
        // internal pages
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            if ($i != 0 && $i != $pageCount - 1) {
                $buttons[] = $this->renderPageButton($i + 1, $i, null, false, $i == $currentPage);
            }
        }
        // separator before largest page
        if ($endSeparator) {
            $buttons[] = $this->renderPageButton($this->separator, null, $this->separatorPageCssClass, true, false);
        }
        // largest page
        $buttons[] = $this->renderPageButton($pageCount, $pageCount - 1, null, false,
            $pageCount - 1 == $currentPage);

        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }

            if ($currentPage < $pageCount - 1) {
                $buttons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass,
                    $currentPage >= $pageCount - 1, false);
            }
        }

        // last page
        if ($this->lastPageLabel !== false) {
            $buttons[] = $this->renderPageButton($this->lastPageLabel, $pageCount - 1, $this->lastPageCssClass,
                $currentPage >= $pageCount - 1, false);
        }

        return Html::tag('div', implode("\n", $buttons), $this->options);
    }
}